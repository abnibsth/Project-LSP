@extends('layouts.app')

@section('title', 'Proses Payroll')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Proses Payroll</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola dan jalankan proses penggajian bulanan.</p>
        </div>
        <a href="{{ route('admin.payroll.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm">
            + Buat Periode Baru
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Periode</th>
                    <th class="px-6 py-3 text-center">Jumlah Slip</th>
                    <th class="px-6 py-3 text-right">Total Gaji Bersih</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Difinalisasi</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($periodes as $periode)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $periode->label }}</td>
                        <td class="px-6 py-4 text-center">{{ $periode->payslips->count() }} karyawan</td>
                        <td class="px-6 py-4 text-right font-medium">
                            Rp {{ number_format($periode->payslips->sum('gaji_bersih'), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $periode->status === 'final' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $periode->status === 'final' ? '🔒 Final' : '✏️ Draft' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">
                            @if($periode->tanggal_finalisasi)
                                {{ $periode->tanggal_finalisasi->format('d M Y H:i') }}
                                <br>oleh {{ $periode->finalizedBy?->name }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-1.5 justify-center items-center">
                                <a href="{{ route('admin.payroll.show', $periode) }}"
                                    class="px-2.5 py-1 bg-primary/10 text-primary hover:bg-primary/20 transition-all rounded-sm text-[10px] font-bold active:scale-95">
                                    Detail
                                </a>
                                @if(!$periode->isFinal())
                                    <form method="POST" action="{{ route('admin.payroll.proses', $periode) }}"
                                        onsubmit="return confirm('Proses payroll untuk {{ $periode->label }}?')" class="m-0 p-0 flex">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-purple-50 text-purple-700 hover:bg-purple-100 transition-all rounded-sm text-[10px] font-bold active:scale-95 cursor-pointer">
                                            Proses
                                        </button>
                                    </form>
                                    @if($periode->payslips->count() > 0)
                                        <form method="POST" action="{{ route('admin.payroll.finalisasi', $periode) }}"
                                            onsubmit="return confirm('Finalisasi payroll {{ $periode->label }}? Data akan dikunci!')" class="m-0 p-0 flex">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-green-50 text-green-700 hover:bg-green-100 transition-all rounded-sm text-[10px] font-bold active:scale-95 cursor-pointer">
                                                Finalisasi
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                            Belum ada periode payroll.
                            <a href="{{ route('admin.payroll.create') }}" class="text-blue-600 ml-1">Buat sekarang?</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($periodes->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $periodes->links() }}</div>
        @endif
    </div>
@endsection
