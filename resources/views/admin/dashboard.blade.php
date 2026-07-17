@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan hari ini.</p>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Karyawan Aktif</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalKaryawan }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl">👥</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Hadir Hari Ini</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $hadirHariIni }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl">✅</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Alpha Hari Ini</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $alphaHariIni }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-2xl">❌</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Periode Payroll</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">
                        @if($periodeAktif)
                            {{ $periodeAktif->label }}
                        @else
                            Belum ada
                        @endif
                    </p>
                    @if($periodeAktif)
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $periodeAktif->status === 'final' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($periodeAktif->status) }}
                        </span>
                    @endif
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-2xl">📅</div>
            </div>
        </div>
    </div>

    {{-- Section Grafik Pengeluaran --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Grafik Pengeluaran Gaji Bulanan</h2>
                <p class="text-xs text-gray-400">Total pengeluaran gaji bersih (Take-Home Pay) dalam 6 periode terakhir</p>
            </div>
            <div class="px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-lg flex items-center gap-1.5">
                <span>💰</span> Rupiah (IDR)
            </div>
        </div>
        <div class="relative w-full" style="height: 320px;">
            <canvas id="chartGaji"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('chartGaji').getContext('2d');
            
            // Create nice gradient fill for the chart line
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)'); // blue-500 with opacity
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)'); // fading out

            const labels = @json($labels);
            const dataValues = @json($dataPengeluaran);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Pengeluaran Gaji',
                        data: dataValues,
                        borderColor: '#2563eb', // blue-600
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35, // Smooth curves
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
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
                            backgroundColor: '#1f2937', // gray-800
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 12,
                            cornerRadius: 8,
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
                                color: '#f3f4f6', // gray-100
                            },
                            ticks: {
                                color: '#9ca3af', // gray-400
                                font: {
                                    size: 11
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
                                color: '#9ca3af',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

    {{-- Aksi Cepat --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('admin.karyawan.create') }}"
                class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors text-center">
                <span class="text-2xl">➕</span>
                <span class="text-sm font-medium text-gray-700">Tambah Karyawan</span>
            </a>
            <a href="{{ route('admin.payroll.create') }}"
                class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors text-center">
                <span class="text-2xl">🔄</span>
                <span class="text-sm font-medium text-gray-700">Buat Periode Payroll</span>
            </a>
            <a href="{{ route('admin.absensi.index') }}"
                class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors text-center">
                <span class="text-2xl">📋</span>
                <span class="text-sm font-medium text-gray-700">Lihat Absensi</span>
            </a>
            <a href="{{ route('admin.laporan.payroll') }}"
                class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors text-center">
                <span class="text-2xl">📊</span>
                <span class="text-sm font-medium text-gray-700">Laporan Payroll</span>
            </a>
        </div>
    </div>
@endsection
