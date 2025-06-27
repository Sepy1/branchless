@extends('layouts.app')

@section('content')
<div class="container" style="padding: 20px;">
    <!-- Pie Chart dan Tabel -->
    <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
        <!-- Pie Chart -->
        <div style="flex: 1 1 500px; max-width: 600px; background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
            <h3 style="text-align: center;">Distribusi Perangkat per Kode Kantor</h3>
            <div style="position: relative; width: 100%; height: 350px;">
                <canvas id="devicePieChart"></canvas>
            </div>
        </div>

        <!-- Tabel -->
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
    <form id="filter-form" style="margin-top: 30px; text-align: center;">
        <label for="kode_kantor"><strong>Filter Kode Kantor:</strong></label>
        <select id="kode_kantor" name="kode_kantor">
            <option value="">-- Semua --</option>
            @foreach ($labels as $kode)
                <option value="{{ $kode }}">{{ $kode }}</option>
            @endforeach
        </select>
    </form>

    <!-- Bar Chart -->
    <div style="margin-top: 20px;">
        <div style="background: #ffffff; border: 1px solid #ccc; border-radius: 10px; padding: 20px;">
            <h3 id="chartTitle" style="text-align: center;">Frekuensi Transaksi 6 Bulan Terakhir</h3>
            <div style="position: relative; height: 400px;">
                <canvas id="barChartBranchless"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // === PIE CHART ===
    const labels = {!! json_encode($labels) !!};
    const data = {!! json_encode($counts) !!};
    const finalLabels = labels.map((label, index) => `${label} (${data[index]})`);

    let pieChart;
    function renderPieChart() {
        const ctx = document.getElementById('devicePieChart').getContext('2d');
        if (pieChart) pieChart.destroy();
        pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: finalLabels,
                datasets: [{
                    data: data,
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
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // === BAR CHART ===
    const bulanLabels = {!! json_encode($namaBulan) !!};
    const jumlahTransaksi = {!! json_encode($jumlahTransaksi) !!};
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
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                }
            }
        });

        const title = `Frekuensi Transaksi 6 Bulan Terakhir` + (kodeKantor ? ` (${kodeKantor})` : '');
        document.getElementById('chartTitle').innerText = title;
    }

    // === INITIAL RENDER ===
    document.addEventListener('DOMContentLoaded', () => {
        renderPieChart();
        renderBarChart(bulanLabels, jumlahTransaksi);
    });

    // === AJAX FILTERING ===
    $('#kode_kantor').on('change', function () {
        const kode = $(this).val();
        $.ajax({
            url: '{{ url("/status/chart-data") }}',
            data: { kode_kantor: kode },
            success: function (res) {
                renderBarChart(res.labels, res.data, res.kode);
            }
        });
    });

    // Resize handling
    window.addEventListener('resize', () => {
        renderPieChart();
        renderBarChart(bulanLabels, jumlahTransaksi);
    });
</script>
@endsection
