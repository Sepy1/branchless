@extends('layouts.app')

@section('content')
<div style="margin-top: 5px;">
    <!-- BAR CHART SUMMARY PER KANTOR -->
    <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
        <div style="flex: 1 1 1000px; max-width: 1200px; background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
            <div style="margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                <h5 style="margin: 0;">Frekuensi & Total Transaksi Branchless per Kantor</h5>
                <div style="display: flex; gap: 10px;">
                    <select id="bulan_summary" class="form-select form-select-sm" style="width: 200px;">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i === now()->month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->locale('id')->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <button id="exportSummaryExcel" class="btn btn-sm btn-success">Export Excel</button>
                </div>
            </div>
            <div style="position: relative; width: 100%; height: 400px;">
                <canvas id="monthlyOfficeChart"></canvas>
                <div id="globalSpinner" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;">
                    <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;"></div>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 10px; text-align: center;">
        <div id="loadingMessage" style="display: none; font-weight: bold; color: #555;">
            Mendownload Data Dari Server Mohon Menunggu...
        </div>
    </div>
    <!-- BAR CHART JUMLAH PERANGKAT -->
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        <div style="flex: 1 1 1000px; max-width: 1200px; background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
            <div style="display: flex; justify-content: end; margin-bottom: 10px;">
                <button id="exportPerangkatExcel" class="btn btn-sm btn-success">Export Excel</button>
            </div>
            <div style="position: relative; width: 100%; height: 270px;">
                <canvas id="deviceBarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- BAR CHART TRANSAKSI 12 BULAN -->
    <div style="margin-top: 20px; display: flex; flex-direction: column; align-items: center; gap: 10px;">
        <form id="filterForm" style="display: flex; align-items: center; gap: 8px;">
            <label for="kode_kantor"><strong>Filter Kode Kantor:</strong></label>
            <select id="kode_kantor" name="kode_kantor" class="form-select form-select-sm" style="width: 120px;">
                @foreach ($labels as $kode)
                    <option value="{{ $kode }}" {{ $kode === '001' ? 'selected' : '' }}>{{ $kode }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
        <!-- FREKUENSI -->
        <div style="flex: 1 1 500px; max-width: 600px; background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 20px; position: relative;">
            <div style="display: flex; justify-content: end; margin-bottom: 10px;">
                <button id="exportFrekuensiExcel" class="btn btn-sm btn-success">Export Excel</button>
            </div>
            <div style="position: relative; height: 250px;">
                <canvas id="barChartBranchless"></canvas>
                <div id="frekuensiSpinner" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;">
                    <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;"></div>
                </div>
            </div>
        </div>

        <!-- NOMINAL -->
        <div style="flex: 1 1 500px; max-width: 600px; background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 20px; position: relative;">
            <div style="display: flex; justify-content: end; margin-bottom: 10px;">
                <button id="exportNominalExcel" class="btn btn-sm btn-success">Export Excel</button>
            </div>
            <div style="position: relative; height: 250px;">
                <canvas id="nominalBarChart"></canvas>
                <div id="nominalSpinner" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;">
                    <div class="spinner-border text-success" role="status" style="width: 2rem; height: 2rem;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- LIBRARY -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    const deviceLabels = {!! json_encode($data->pluck('kode_kantor')) !!};
    const deviceCounts = {!! json_encode($data->pluck('total')) !!};
    const kodeKantorList = Array.from({ length: 28 }, (_, i) => (i + 1).toString().padStart(3, '0'));
    let barChart, nominalBarChart, deviceBarChart, monthlyOfficeChart;

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
                    barThickness: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Jumlah Perangkat' }},
                    x: { title: { display: true, text: 'Kode Kantor' }}
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
        const title = kodeKantor ? `Frekuensi Transaksi Jan - Des (${kodeKantor})` : 'Frekuensi Transaksi Jan - Des';
        barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data,
                    backgroundColor: '#007bff',
                    barThickness: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true },
                    x: { title: { display: true, text: 'Bulan' }}
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
        const title = kodeKantor ? `Total Nominal Transaksi Jan - Des (${kodeKantor})` : 'Total Nominal Transaksi Jan - Des';
        nominalBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Total Nominal (Rp)',
                    data,
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
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    },
                    x: { title: { display: true, text: 'Bulan' }}
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

    function renderMonthlyOfficeChart(labels, frekuensiData, nominalData, bulanNama) {
        const ctx = document.getElementById('monthlyOfficeChart').getContext('2d');
        if (monthlyOfficeChart) monthlyOfficeChart.destroy();

        monthlyOfficeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Frekuensi Transaksi', data: frekuensiData, backgroundColor: '#007bff' },
                    { label: 'Total Nominal (Rp)', data: nominalData, backgroundColor: '#28a745', yAxisID: 'y1' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Frekuensi Transaksi' }},
                    y1: {
                        beginAtZero: true, position: 'right',
                        title: { display: true, text: 'Total Nominal (Rp)' },
                        ticks: {
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        },
                        grid: { drawOnChartArea: false }
                    },
                    x: { title: { display: true, text: 'Kode Kantor' }}
                },
                plugins: {
                    legend: { position: 'bottom' },
                    title: {
                        display: true,
                        text: `Frekuensi & Total Transaksi Branchless - Bulan ${bulanNama}`,
                        font: { size: 16, weight: 'bold' }
                    }
                }
            }
        });
    }

    function exportToExcel(headers, labels, data, filename) {
        const rows = [headers];
        labels.forEach((label, idx) => {
            if (Array.isArray(data[idx])) {
                rows.push([label, ...data[idx]]);
            } else {
                rows.push([label, data[idx]]);
            }
        });
        const worksheet = XLSX.utils.aoa_to_sheet(rows);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
        XLSX.writeFile(workbook, filename);
    }

    $('#exportPerangkatExcel').on('click', function () {
        exportToExcel(['Kode Kantor', 'Jumlah Perangkat'], deviceLabels, deviceCounts, 'jumlah-perangkat.xlsx');
    });

    $('#exportFrekuensiExcel').on('click', function () {
        if (barChart) {
            exportToExcel(['Bulan', 'Jumlah Transaksi'], barChart.data.labels, barChart.data.datasets[0].data, 'frekuensi-transaksi.xlsx');
        }
    });

    $('#exportNominalExcel').on('click', function () {
        if (nominalBarChart) {
            exportToExcel(['Bulan', 'Total Nominal'], nominalBarChart.data.labels, nominalBarChart.data.datasets[0].data, 'nominal-transaksi.xlsx');
        }
    });

    $('#exportSummaryExcel').on('click', function () {
        if (monthlyOfficeChart) {
            const rows = monthlyOfficeChart.data.labels.map((label, idx) => [label, monthlyOfficeChart.data.datasets[0].data[idx], monthlyOfficeChart.data.datasets[1].data[idx]]);
            exportToExcel(['Kode Kantor', 'Frekuensi', 'Total Nominal'], monthlyOfficeChart.data.labels, rows, 'summary-transaksi.xlsx');
        }
    });

    function fetchMonthlyOfficeChart(bulan = null) {
        $('#globalSpinner').show();
        $('#loadingMessage').show();
        const now = new Date();
        const bulanAktif = bulan || now.getMonth() + 1;
        const tahun = now.getFullYear();
        const bulanNama = new Date(0, bulanAktif - 1).toLocaleString('id-ID', { month: 'long' });
        const frekuensiData = [];
        const nominalData = [];
        let counter = 0;

        kodeKantorList.forEach((kode, idx) => {
            const url = `/api/summary-bulanan?bulan=${bulanAktif}&tahun=${tahun}&kode_kantor=${kode}`;
            fetch(url)
            .then(res => res.json())
            .then(result => {
                frekuensiData[idx] = result.data?.jumlah_transaksi ?? 0;
                nominalData[idx] = result.data?.total_pokok ?? 0;
            })
            .catch(() => {
                frekuensiData[idx] = 0;
                nominalData[idx] = 0;
            })
            .finally(() => {
                counter++;
                if (counter === kodeKantorList.length) {
                    renderMonthlyOfficeChart(kodeKantorList, frekuensiData, nominalData, bulanNama);
                    $('#globalSpinner').hide();
                    $('#loadingMessage').hide();
                }
            });
        });
    }

    function fetchChartData(kodeKantor = '001') {
        $('#frekuensiSpinner, #nominalSpinner').show();
        $('#loadingMessage').show();
        const now = new Date();
        const bulanList = Array.from({ length: 12 }, (_, i) => i + 1);
        const bulanNama = bulanList.map(b => new Date(0, b - 1).toLocaleString('id-ID', { month: 'short' }));
        const tahun = now.getFullYear();
        const data = Array(12).fill(0);
        const nominal = Array(12).fill(0);
        let counter = 0;

        bulanList.forEach((bulan, idx) => {
            if (bulan > now.getMonth() + 1) {
                counter++;
                if (counter === 12) {
                    renderBarChart(bulanNama, data, kodeKantor);
                    renderNominalChart(bulanNama, nominal, kodeKantor);
                    $('#frekuensiSpinner, #nominalSpinner').hide();
                    $('#loadingMessage').hide();
                }
                return;
            }

            const url = `/api/summary-bulanan?bulan=${bulan}&tahun=${tahun}&kode_kantor=${kodeKantor}`;
            fetch(url)
            .then(res => res.json())
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
                    renderBarChart(bulanNama, data, kodeKantor);
                    renderNominalChart(bulanNama, nominal, kodeKantor);
                    $('#frekuensiSpinner, #nominalSpinner').hide();
                    $('#loadingMessage').hide();
                }
            });
        });
    }

    $(document).ready(function () {
        renderDeviceChart();
        fetchMonthlyOfficeChart();
        fetchChartData('001');

        $('#kode_kantor').on('change', function () {
            fetchChartData($(this).val());
        });

        $('#bulan_summary').on('change', function () {
            fetchMonthlyOfficeChart($(this).val());
        });
    });
</script>
@endsection
