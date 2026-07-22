@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Selamat datang, {{ auth()->user()->name }}. Ringkasan operasional hari ini.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3.5 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-label">Karyawan Aktif</p>
                    <p class="text-[1.75rem] font-semibold text-ink mt-1.5 tracking-tight tabular-nums">{{ $totalKaryawan }}</p>
                </div>
                <div class="stat-icon"><i data-lucide="users" class="ui-icon" aria-hidden="true"></i></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-label">Hadir Hari Ini</p>
                    <p class="text-[1.75rem] font-semibold text-primary mt-1.5 tracking-tight tabular-nums">{{ $hadirHariIni }}</p>
                </div>
                <div class="stat-icon is-success"><i data-lucide="clipboard-check" class="ui-icon" aria-hidden="true"></i></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-label">Alpha Hari Ini</p>
                    <p class="text-[1.75rem] font-semibold text-red-600 mt-1.5 tracking-tight tabular-nums">{{ $alphaHariIni }}</p>
                </div>
                <div class="stat-icon is-danger"><i data-lucide="user-x" class="ui-icon" aria-hidden="true"></i></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="section-label">Periode Payroll</p>
                    <p class="text-base font-semibold text-ink mt-2 truncate">
                        @if($periodeAktif)
                            {{ $periodeAktif->label }}
                        @else
                            Belum ada
                        @endif
                    </p>
                    @if($periodeAktif)
                        <span class="badge mt-2 {{ $periodeAktif->status === 'final' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($periodeAktif->status) }}
                        </span>
                    @endif
                </div>
                <div class="stat-icon"><i data-lucide="calendar-days" class="ui-icon" aria-hidden="true"></i></div>
            </div>
        </div>
    </div>

    <div class="ui-card ui-card-pad mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="section-title">Pengeluaran Gaji Bulanan</h2>
                <p class="text-xs text-ink-muted-48 mt-1">Total gaji bersih 6 periode terakhir</p>
            </div>
            <div class="badge badge-muted self-start">IDR</div>
        </div>

        @if($hasPayrollData)
            <div class="relative w-full h-[280px]">
                <canvas id="chartGaji"></canvas>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i data-lucide="chart-column" class="ui-icon" aria-hidden="true"></i>
                </div>
                <h3 class="empty-state-title">Belum ada data pengeluaran gaji</h3>
                <p class="empty-state-text">
                    Statistik muncul setelah periode payroll diproses dan difinalisasi.
                </p>
                <a href="{{ route('admin.payroll.create') }}" class="btn btn-primary">
                    Buat Periode Payroll
                </a>
            </div>
        @endif
    </div>

    @if($hasPayrollData)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('chartGaji').getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 260);
                gradient.addColorStop(0, 'rgba(0, 102, 204, 0.14)');
                gradient.addColorStop(1, 'rgba(0, 102, 204, 0.0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: [{
                            label: 'Total Pengeluaran Gaji',
                            data: @json($dataPengeluaran),
                            borderColor: '#0066cc',
                            borderWidth: 2,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#0066cc',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 3.5,
                            pointHoverRadius: 5.5,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1d1d1f',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                padding: 12,
                                cornerRadius: 8,
                                titleFont: { size: 12, weight: '600' },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function (context) {
                                        return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                grid: { color: '#f0f0f2' },
                                ticks: {
                                    color: '#6e6e73',
                                    font: { size: 11 },
                                    callback: function (value) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000) + ' Jt';
                                        }
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            },
                            x: {
                                border: { display: false },
                                grid: { display: false },
                                ticks: { color: '#6e6e73', font: { size: 11 } }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

    <div class="ui-card ui-card-pad">
        <h2 class="section-title mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('admin.karyawan.create') }}" class="quick-action">
                <span class="quick-action-icon"><i data-lucide="user-plus" class="ui-icon" aria-hidden="true"></i></span>
                <span class="text-xs font-semibold text-ink">Tambah Karyawan</span>
            </a>
            <a href="{{ route('admin.payroll.create') }}" class="quick-action">
                <span class="quick-action-icon"><i data-lucide="calculator" class="ui-icon" aria-hidden="true"></i></span>
                <span class="text-xs font-semibold text-ink">Buat Periode Payroll</span>
            </a>
            <a href="{{ route('admin.absensi.index') }}" class="quick-action">
                <span class="quick-action-icon"><i data-lucide="clipboard-list" class="ui-icon" aria-hidden="true"></i></span>
                <span class="text-xs font-semibold text-ink">Lihat Absensi</span>
            </a>
            <a href="{{ route('admin.laporan.payroll') }}" class="quick-action">
                <span class="quick-action-icon"><i data-lucide="chart-column" class="ui-icon" aria-hidden="true"></i></span>
                <span class="text-xs font-semibold text-ink">Laporan Payroll</span>
            </a>
        </div>
    </div>
@endsection
