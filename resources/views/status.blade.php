@extends('layouts.app')

@section('content')
<div style="margin-top: 60px;">
    <!-- PIE + TABEL -->
    <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
        <!-- Pie Chart -->
        <div style="flex: 1 1 500px; max-width: 600px; background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
            <h3 style="text-align: center;">Distribusi Perangkat per Kode Kantor</h3>
            <div style="position: relative; width: 100%; height: 350px;">
                <canvas id="devicePieChart"></canvas>
            </div>
        </div>

        <!-- Tabel Perangkat -->
        <div style="flex: 1 1 500px; max-width: 600px; background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
            <h3 style="text-align: center;">List Perangkat</h3>
            <p><strong>Total: {{ $data->sum('total') }} Perangkat</strong></p>
            <div style="max-height: 350px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="position: sticky; top: 0; background-color: #f2f2f2;">
                        <tr>
                            <th style="padding: 10px; border: 1px solid #000; text-align: center;">Kode Kantor</th>
                            <th style="padding: 10px; border: 1px solid #000; text-align: center;">Jumlah Perangkat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ccc; text-align: center;">{{ $item->kode_kantor }}</td>
                                <td style="padding: 10px; border: 1px solid #ccc; text-align: center;">{{ $item->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Filter Dropdown -->
   

    <!-- BAR CHART -->
    <div style="margin-top: 20px;">
    <div style="background: #ffffff; border: 1px solid #ccc; border-radius: 10px; padding: 20px;">
        
        <!-- Judul dan Filter dalam satu baris -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 20px;">
            <!-- Filter Kode Kantor -->
           <form id="filterForm" style="display: flex; align-items: center; gap: 8px; margin: 0;">
    <label for="kode_kantor" style="margin: 0;"><strong>Filter Kode Kantor:</strong></label>
    <select id="kode_kantor" name="kode_kantor" class="form-select form-select-sm" style="width: 100px;">
        <option value="">-- Semua --</option>
        @foreach ($labels as $kode)
            <option value="{{ $kode }}">{{ $kode }}</option>
        @endforeach
    </select>
</form>


            <!-- Judul -->
            <h3 id="chartTitle" style="text-align: right; margin: 0; flex: 1; text-align: center;">
                Frekuensi Transaksi 6 Bulan Terakhir
            </h3>
        </div>

        <!-- Chart Container -->
        <div style="position: relative; height: 400px;">
            <!-- Spinner Center -->
            <div id="spinner" style="
                display: none;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 10;
            ">
                <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Canvas Chart -->
            <canvas id="barChartBranchless" style="z-index: 1;"></canvas>
        </div>
    </div>
</div>
</div>

<!-- Chart.js & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<script>
    const pieLabels = {!! json_encode($labels) !!};
    const pieData = {!! json_encode($counts) !!};
    const finalPieLabels = pieLabels.map((label, index) => `${label} (${pieData[index]})`);

    let pieChart;
    function renderPieChart() {
        const ctx = document.getElementById('devicePieChart').getContext('2d');
        if (pieChart) pieChart.destroy();
        pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: finalPieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: [
                        '#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8',
                        '#6610f2', '#e83e8c', '#6f42c1', '#20c997', '#fd7e14',
                        '#6c757d', '#343a40'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    let barChart;
    function renderBarChart(labels, data, kodeKantor = '') {
        const ctx = document.getElementById('barChartBranchless').getContext('2d');
        if (barChart) barChart.destroy();
        barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: data,
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true },
                    x: { title: { display: true, text: 'Bulan' } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        const title = `Frekuensi Transaksi 6 Bulan Terakhir${kodeKantor ? ` (${kodeKantor})` : ''}`;
        document.getElementById('chartTitle').innerText = title;
    }

    function fetchChartData(kodeKantor = '') {
        $('#spinner').show();

        const bulanList = [1, 2, 3, 4, 5, 6];
        const tahun = 2025;
        const bulanNama = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];

        const label = [];
        const data = [];
        let counter = 0;

        bulanList.forEach((bulan, idx) => {
            const url = `https://branchless.bkkjateng.co.id/api/summary-bulanan?bulan=${bulan}&tahun=${tahun}${kodeKantor ? `&kode_kantor=${kodeKantor}` : ''}`;
            fetch(url)
                .then(response => response.json())
                .then(res => {
                    label[idx] = bulanNama[idx];
                    data[idx] = res.data?.jumlah_transaksi ?? 0;
                })
                .catch(() => {
                    label[idx] = bulanNama[idx];
                    data[idx] = 0;
                })
                .finally(() => {
                    counter++;
                    if (counter === bulanList.length) {
                        renderBarChart(label, data, kodeKantor);
                        $('#spinner').hide();
                    }
                });
        });
    }

    $(document).ready(function () {
        renderPieChart();
        fetchChartData('001'); // load default kantor 001
    });

    $('#kode_kantor').on('change', function () {
        const kode = $(this).val();
        fetchChartData(kode);
    });

    window.addEventListener('resize', () => {
        renderPieChart();
    });
</script>

@endsection
