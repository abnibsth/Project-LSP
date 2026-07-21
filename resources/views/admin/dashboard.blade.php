@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Selamat datang, {{ auth()->user()->name }}. Ringkasan operasional hari ini.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold text-ink-muted-48 uppercase tracking-widest">Karyawan Aktif</p>
                    <p class="text-3xl font-semibold text-ink mt-1 tracking-tight">{{ $totalKaryawan }}</p>
                </div>
                <div class="stat-icon"><i class="flaticon-025-members"></i></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold text-ink-muted-48 uppercase tracking-widest">Hadir Hari Ini</p>
                    <p class="text-3xl font-semibold text-primary mt-1 tracking-tight">{{ $hadirHariIni }}</p>
                </div>
                <div class="stat-icon"><i class="flaticon-005-checklist"></i></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold text-ink-muted-48 uppercase tracking-widest">Alpha Hari Ini</p>
                    <p class="text-3xl font-semibold text-red-600 mt-1 tracking-tight">{{ $alphaHariIni }}</p>
                </div>
                <div class="stat-icon !text-red-600"><i class="flaticon-010-delete"></i></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-ink-muted-48 uppercase tracking-widest">Periode Payroll</p>
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
                <div class="stat-icon"><i class="flaticon-003-calendar"></i></div>
            </div>
        </div>
    </div>

    <div class="ui-card ui-card-pad mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="typography-body-strong text-ink">Pengeluaran Gaji Bulanan</h2>
                <p class="text-xs text-ink-muted-48 mt-0.5">Total gaji bersih (take-home pay) 6 periode terakhir</p>
            </div>
            <div class="badge badge-muted self-start">Rupiah (IDR)</div>
        </div>

        @if($hasPayrollData)
            <div class="relative w-full h-[280px]">
                <canvas id="chartGaji"></canvas>
            </div>
        @else
            <div class="min-h-[280px] flex items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-gray-50/70 px-6 text-center">
                <div class="max-w-md">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-primary shadow-sm">
                        <i class="flaticon-040-stats text-xl"></i>
                    </div>
                    <h3 class="text-base font-semibold text-ink">Belum ada data pengeluaran gaji</h3>
                    <p class="mt-2 text-sm text-ink-muted-48 leading-relaxed">
                        Statistik pengeluaran akan muncul setelah periode payroll diproses dan difinalisasi.
                    </p>
                    <a href="{{ route('admin.payroll.create') }}" class="btn-primary inline-flex mt-4">
                        Buat Periode Payroll
                    </a>
                </div>
            </div>
        @endif
    </div>

    @if($hasPayrollData)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('chartGaji').getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 260);
                gradient.addColorStop(0, 'rgba(0, 102, 204, 0.18)');
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
                            pointRadius: 4,
                            pointHoverRadius: 6,
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
                                grid: { color: '#f0f0f0' },
                                ticks: {
                                    color: '#7a7a7a',
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
                                ticks: { color: '#7a7a7a', font: { size: 11 } }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

    <div class="ui-card ui-card-pad">
        <h2 class="typography-body-strong text-ink mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            <a href="{{ route('admin.karyawan.create') }}" class="quick-action">
                <i class="flaticon-008-create-account text-xl text-primary"></i>
                <span class="text-xs font-semibold text-ink">Tambah Karyawan</span>
            </a>
            <a href="{{ route('admin.payroll.create') }}" class="quick-action">
                <i class="flaticon-032-refresh-page text-xl text-primary"></i>
                <span class="text-xs font-semibold text-ink">Buat Periode Payroll</span>
            </a>
            <a href="{{ route('admin.absensi.index') }}" class="quick-action">
                <i class="flaticon-012-file-explorer text-xl text-primary"></i>
                <span class="text-xs font-semibold text-ink">Lihat Absensi</span>
            </a>
            <a href="{{ route('admin.laporan.payroll') }}" class="quick-action">
                <i class="flaticon-040-stats text-xl text-primary"></i>
                <span class="text-xs font-semibold text-ink">Laporan Payroll</span>
            </a>
        </div>
    </div>
@endsection
