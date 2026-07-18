@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-bold text-ink tracking-tight">Dashboard</h1>
        <p class="text-ink-muted-48 text-xs mt-0.5">Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan hari ini.</p>
    </div>

    {{-- Kartu Statistik (Apple Configurator style - 18px radius, flat UI, border hairline) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-hairline p-5 shadow-none">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-semibold text-ink-muted-48 uppercase tracking-widest">Total Karyawan Aktif</p>
                    <p class="text-2xl font-bold text-ink mt-1">{{ $totalKaryawan }}</p>
                </div>
                <div class="w-10 h-10 bg-canvas-parchment border border-hairline rounded-sm flex items-center justify-center text-primary text-base"><i class="flaticon-025-members"></i></div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-hairline p-5 shadow-none">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-semibold text-ink-muted-48 uppercase tracking-widest">Hadir Hari Ini</p>
                    <p class="text-2xl font-bold text-primary mt-1">{{ $hadirHariIni }}</p>
                </div>
                <div class="w-10 h-10 bg-canvas-parchment border border-hairline rounded-sm flex items-center justify-center text-primary text-base"><i class="flaticon-005-checklist"></i></div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-hairline p-5 shadow-none">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-semibold text-ink-muted-48 uppercase tracking-widest">Alpha Hari Ini</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $alphaHariIni }}</p>
                </div>
                <div class="w-10 h-10 bg-canvas-parchment border border-hairline rounded-sm flex items-center justify-center text-red-600 text-base"><i class="flaticon-010-delete"></i></div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-hairline p-5 shadow-none">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-semibold text-ink-muted-48 uppercase tracking-widest">Periode Payroll</p>
                    <p class="text-sm font-bold text-ink mt-1.5">
                        @if($periodeAktif)
                            {{ $periodeAktif->label }}
                        @else
                            Belum ada
                        @endif
                    </p>
                    @if($periodeAktif)
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-pill text-[9px] font-medium border border-hairline
                            {{ $periodeAktif->status === 'final' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-yellow-50 text-yellow-700 border-yellow-100' }}">
                            {{ ucfirst($periodeAktif->status) }}
                        </span>
                    @endif
                </div>
                <div class="w-10 h-10 bg-canvas-parchment border border-hairline rounded-sm flex items-center justify-center text-primary text-base"><i class="flaticon-003-calendar"></i></div>
            </div>
        </div>
    </div>

    {{-- Section Grafik Pengeluaran (Apple style - flat UI, Action Blue line) --}}
    <div class="bg-white rounded-lg border border-hairline p-6 shadow-none mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-bold text-ink tracking-tight">Grafik Pengeluaran Gaji Bulanan</h2>
                <p class="text-[10px] text-ink-muted-48 mt-0.5">Total pengeluaran gaji bersih (Take-Home Pay) dalam 6 periode terakhir</p>
            </div>
            <div class="px-2.5 py-1 bg-canvas-parchment text-ink border border-hairline text-[10px] font-semibold rounded-pill flex items-center gap-1.5">
                <span>💰</span> Rupiah (IDR)
            </div>
        </div>
        <div class="relative w-full" style="height: 280px;">
            <canvas id="chartGaji"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('chartGaji').getContext('2d');
            
            // Create nice gradient fill using Apple primary color (Action Blue #0066cc)
            const gradient = ctx.createLinearGradient(0, 0, 0, 260);
            gradient.addColorStop(0, 'rgba(0, 102, 204, 0.2)');
            gradient.addColorStop(1, 'rgba(0, 102, 204, 0.0)');

            const labels = @json($labels);
            const dataValues = @json($dataPengeluaran);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Pengeluaran Gaji',
                        data: dataValues,
                        borderColor: '#0066cc', // Apple Action Blue
                        borderWidth: 2,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#0066cc',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1.5,
                        pointRadius: 4.5,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1d1d1f', // Apple Ink Dark
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: {
                                size: 10,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 10
                            },
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    return ' Pengeluaran: Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f5f5f7', // Apple Parchment gray
                            },
                            ticks: {
                                color: '#7a7a7a', // Apple ink-muted-48
                                font: {
                                    size: 10
                                },
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000) + ' Jt';
                                    }
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#7a7a7a',
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

    {{-- Aksi Cepat (Apple Store Utility Cards style - flat, 18px radius, no shadow) --}}
    <div class="bg-white rounded-lg border border-hairline p-6 shadow-none">
        <h2 class="text-sm font-bold text-ink tracking-tight mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.karyawan.create') }}"
                class="flex flex-col items-center gap-2.5 p-5 bg-white rounded-lg border border-hairline hover:bg-canvas-parchment transition-all active:scale-95 duration-100 text-center">
                <i class="flaticon-008-create-account text-xl text-primary"></i>
                <span class="text-xs font-semibold text-ink">Tambah Karyawan</span>
            </a>
            <a href="{{ route('admin.payroll.create') }}"
                class="flex flex-col items-center gap-2.5 p-5 bg-white rounded-lg border border-hairline hover:bg-canvas-parchment transition-all active:scale-95 duration-100 text-center">
                <i class="flaticon-032-refresh-page text-xl text-primary"></i>
                <span class="text-xs font-semibold text-ink">Buat Periode Payroll</span>
            </a>
            <a href="{{ route('admin.absensi.index') }}"
                class="flex flex-col items-center gap-2.5 p-5 bg-white rounded-lg border border-hairline hover:bg-canvas-parchment transition-all active:scale-95 duration-100 text-center">
                <i class="flaticon-012-file-explorer text-xl text-primary"></i>
                <span class="text-xs font-semibold text-ink">Lihat Absensi</span>
            </a>
            <a href="{{ route('admin.laporan.payroll') }}"
                class="flex flex-col items-center gap-2.5 p-5 bg-white rounded-lg border border-hairline hover:bg-canvas-parchment transition-all active:scale-95 duration-100 text-center">
                <i class="flaticon-040-stats text-xl text-primary"></i>
                <span class="text-xs font-semibold text-ink">Laporan Payroll</span>
            </a>
        </div>
    </div>
@endsection
