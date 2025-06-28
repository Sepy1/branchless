@extends('layouts.app')

@section('content')
<div style="margin-top: 5px;">
    <!-- PIE + BAR CHART PERANGKAT -->
    <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
        <!-- Pie Chart -->
        <div style="flex: 1 1 500px; max-width: 600px; background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
            <div style="position: relative; width: 100%; height: 270px;">
                <canvas id="devicePieChart"></canvas>
            </div>
        </div>

        <!-- Bar Chart Perangkat -->
        <div style="flex: 1 1 500px; max-width: 600px; background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
            <div style="position: relative; width: 100%; height: 270px;">
                <canvas id="deviceBarChart"></canvas>
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

    <!-- BAR CHARTS TRANSAKSI -->
    <div style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
        <div style="flex: 1 1 500px; max-width: 600px; background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 20px;">
            <div style="position: relative; height: 250px;">
                <canvas id="barChartBranchless"></canvas>
            </div>
        </div>

        <div style="flex: 1 1 500px; max-width: 600px; background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 20px;">
            <div style="position: relative; height: 250px;">
                <canvas id="nominalBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- CHART.JS & JQUERY -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<script>
    const pieLabels = {!! json_encode($labels) !!};
    const pieData = {!! json_encode($counts) !!};
    const deviceLabels = {!! json_encode($data->pluck('kode_kantor')) !!};
    const deviceCounts = {!! json_encode($data->pluck('total')) !!};
    const finalPieLabels = pieLabels.map((label, index) => `${label} (${pieData[index]})`);

    let pieChart, barChart, nominalBarChart, deviceBarChart;

    function renderPieChart() {
        const ctx = document.getElementById('devicePieChart')?.getContext('2d');
        if (!ctx) return;
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
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Distribusi Perangkat Branchless',
                        font: { size: 14, weight: 'bold' }
                    }
                }
            }
        });
    }

    function renderDeviceChart() {
        const ctx = document.getElementById('deviceBarChart')?.getContext('2d');
        if (!ctx) return;
        if (deviceBarChart) deviceBarChart.destroy();
        deviceBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: deviceLabels,
                datasets: [{
                    label: 'Jumlah Perangkat',
                    data: deviceCounts,
                    backgroundColor: '#ffc107',
                    barThickness: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Jumlah Perangkat' }
                    },
                    x: { title: { display: true, text: 'Kode Kantor' } }
                },
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Jumlah Perangkat per Kode Kantor',
                        font: { size: 14, weight: 'bold' }
                    }
                }
            }
        });
    }

    function renderBarChart(labels, data, kodeKantor = '') {
        const ctx = document.getElementById('barChartBranchless').getContext('2d');
        if (barChart) barChart.destroy();
        const title = kodeKantor ? `Frekuensi Transaksi Jan - Des (${kodeKantor})` : 'Frekuensi Transaksi Jan - Des (Semua)';
        barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: data,
                    backgroundColor: '#007bff',
                    barThickness: 20
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
                    legend: { display: false },
                    title: {
                        display: true,
                        text: title,
                        font: { size: 14, weight: 'bold' }
                    }
                }
            }
        });
    }

    function renderNominalChart(labels, data, kodeKantor = '') {
        const ctx = document.getElementById('nominalBarChart').getContext('2d');
        if (nominalBarChart) nominalBarChart.destroy();
        const title = kodeKantor ? `Total Nominal Transaksi Jan - Des (${kodeKantor})` : 'Total Nominal Transaksi Jan - Des (Semua)';
        nominalBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Nominal (Rp)',
                    data: data,
                    backgroundColor: '#28a745',
                    barThickness: 20
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
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: title,
                        font: { size: 14, weight: 'bold' }
                    }
                }
            }
        });
    }

    function fetchChartData(kodeKantor = '') {
        $('#globalSpinner').show();

        const now = new Date();
        const bulanList = Array.from({ length: 12 }, (_, i) => i + 1);
        const bulanNama = bulanList.map(bulan => new Date(0, bulan - 1).toLocaleString('id-ID', { month: 'short' }));
        const tahun = now.getFullYear();

        const label = [...bulanNama];
        const data = Array(12).fill(0);
        const nominal = Array(12).fill(0);
        const API_TOKEN = "semangatpagi";
        let counter = 0;

        bulanList.forEach((bulan, idx) => {
            if (bulan > now.getMonth() + 1) {
                counter++;
                if (counter === 12) {
                    renderBarChart(label, data, kodeKantor);
                    renderNominalChart(label, nominal, kodeKantor);
                    $('#globalSpinner').hide();
                }
                return;
            }

            const url = `https://branchless.bkkjateng.co.id/api/summary-bulanan?bulan=${bulan}&tahun=${tahun}${kodeKantor ? `&kode_kantor=${kodeKantor}` : ''}`;
            fetch(url, {
                method: 'GET',
                headers: { 'Authorization': `Bearer ${API_TOKEN}` }
            })
            .then(response => response.json())
            .then(res => {
                data[idx] = res.data?.jumlah_transaksi ?? 0;
                nominal[idx] = res.data?.total_pokok ?? 0;
            })
            .catch(() => {
                data[idx] = 0;
                nominal[idx] = 0;
            })
            .finally(() => {
                counter++;
                if (counter === 12) {
                    renderBarChart(label, data, kodeKantor);
                    renderNominalChart(label, nominal, kodeKantor);
                    $('#globalSpinner').hide();
                }
            });
        });
    }

    $(document).ready(function () {
        renderPieChart();
        renderDeviceChart();
        fetchChartData('001');
    });

    $('#kode_kantor').on('change', function () {
        const kode = $(this).val();
        fetchChartData(kode);
    });

    window.addEventListener('resize', () => {
        renderPieChart();
        renderDeviceChart();
    });
</script>
@endsection
