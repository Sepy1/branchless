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

    <!-- FILTER + SPINNER -->
    <div style="margin-top: 20px; display: flex; flex-direction: column; align-items: center; gap: 10px;">
        <form id="filterForm" style="display: flex; align-items: center; gap: 8px;">
            <label for="kode_kantor"><strong>Filter Kode Kantor:</strong></label>
            <select id="kode_kantor" name="kode_kantor" class="form-select form-select-sm" style="width: 120px;">
                <option value="">-- Semua --</option>
                @foreach ($labels as $kode)
                    <option value="{{ $kode }}">{{ $kode }}</option>
                @endforeach
            </select>
        </form>
        <div id="globalSpinner" style="display: none;">
            <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- 2 BAR CHART DALAM 1 BARIS -->
    <div style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
        <!-- Chart Frekuensi Transaksi -->
        <div style="flex: 1 1 500px; max-width: 600px; background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 20px;">
            <h3 id="chartTitle" style="text-align: center;">Frekuensi Transaksi 6 Bulan Terakhir</h3>
            <div style="position: relative; height: 250px;">
                <canvas id="barChartBranchless" style="z-index: 1;"></canvas>
            </div>
        </div>

        <!-- Chart Total Nominal Transaksi -->
        <div style="flex: 1 1 500px; max-width: 600px; background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 20px;">
            <h3 id="nominalChartTitle" style="text-align: center;">Total Nominal Transaksi per Bulan</h3>
            <div style="position: relative; height: 250px;">
                <canvas id="nominalBarChart" style="z-index: 1;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- CHART.JS & BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<script>
    const pieLabels = {!! json_encode($labels) !!};
    const pieData = {!! json_encode($counts) !!};
    const finalPieLabels = pieLabels.map((label, index) => `${label} (${pieData[index]})`);

    let pieChart, barChart, nominalBarChart;

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
                plugins: { legend: { display: false } }
            }
        });

        const title = kodeKantor ? `Frekuensi Transaksi 6 Bulan Terakhir (${kodeKantor})` : 'Frekuensi Transaksi 6 Bulan Terakhir (Semua)';
        document.getElementById('chartTitle').innerText = title;
    }

    function renderNominalChart(labels, data, kodeKantor = '') {
        const ctx = document.getElementById('nominalBarChart').getContext('2d');
        if (nominalBarChart) nominalBarChart.destroy();
        nominalBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Nominal (Rp)',
                    data: data,
                    backgroundColor: '#28a745'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: { title: { display: true, text: 'Bulan' } }
                },
                plugins: { legend: { display: false } }
            }
        });

        const title = kodeKantor ? `Total Nominal Transaksi per Bulan (${kodeKantor})` : 'Total Nominal Transaksi per Bulan (Semua)';
        document.getElementById('nominalChartTitle').innerText = title;
    }

    function fetchChartData(kodeKantor = '') {
        $('#globalSpinner').show();

        const now = new Date();
        const bulanList = [];
        const bulanNama = [];
        const tahun = now.getFullYear();

        for (let i = 5; i >= 0; i--) {
            const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
            bulanList.push(date.getMonth() + 1);
            bulanNama.push(date.toLocaleString('id-ID', { month: 'short' }));
        }

        const label = [];
        const data = [];
        const nominal = [];
        const API_TOKEN = "semangatpagi";
        let counter = 0;

        bulanList.forEach((bulan, idx) => {
            const url = `https://branchless.bkkjateng.co.id/api/summary-bulanan?bulan=${bulan}&tahun=${tahun}${kodeKantor ? `&kode_kantor=${kodeKantor}` : ''}`;
            fetch(url, {
                method: 'GET',
                headers: { 'Authorization': `Bearer ${API_TOKEN}` }
            })
            .then(response => response.json())
            .then(res => {
                label[idx] = bulanNama[idx];
                data[idx] = res.data?.jumlah_transaksi ?? 0;
                nominal[idx] = res.data?.total_pokok ?? 0;
            })
            .catch(() => {
                label[idx] = bulanNama[idx];
                data[idx] = 0;
                nominal[idx] = 0;
            })
            .finally(() => {
                counter++;
                if (counter === bulanList.length) {
                    renderBarChart(label, data, kodeKantor);
                    renderNominalChart(label, nominal, kodeKantor);
                    $('#globalSpinner').hide();
                }
            });
        });
    }

    $(document).ready(function () {
        renderPieChart();
        fetchChartData('001');
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
