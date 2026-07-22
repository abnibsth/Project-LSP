@extends('layouts.app')

@section('title', 'Proses Payroll')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 page-header">
        <div>
            <h1 class="page-title">Proses Payroll</h1>
            <p class="page-subtitle">Kelola dan jalankan proses penggajian bulanan.</p>
        </div>
        <a href="{{ route('admin.payroll.create') }}" class="btn btn-primary self-start">+ Buat Periode Baru</a>
    </div>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th class="!text-center">Jumlah Slip</th>
                    <th class="!text-right">Total Gaji Bersih</th>
                    <th>Status</th>
                    <th>Difinalisasi</th>
                    <th class="!text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periodes as $periode)
                    <tr>
                        <td class="font-semibold text-ink">{{ $periode->label }}</td>
                        <td class="text-center">{{ $periode->payslips->count() }} karyawan</td>
                        <td class="text-right font-medium">
                            Rp {{ number_format($periode->payslips->sum('gaji_bersih'), 0, ',', '.') }}
                        </td>
                        <td>
                            <span class="badge {{ $periode->status === 'final' ? 'badge-success' : 'badge-warning' }}">
                                {{ $periode->status === 'final' ? 'Final' : 'Draft' }}
                            </span>
                        </td>
                        <td class="text-ink-muted-48 text-xs">
                            @if($periode->tanggal_finalisasi)
                                {{ $periode->tanggal_finalisasi->format('d M Y H:i') }}
                                <br>oleh {{ $periode->finalizedBy?->name }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.payroll.show', $periode) }}" class="btn btn-action-soft">Detail</a>
                                @if(!$periode->isFinal())
                                    <form method="POST" action="{{ route('admin.payroll.proses', $periode) }}"
                                        onsubmit="return confirm('Proses payroll untuk {{ $periode->label }}?')" class="m-0 p-0 flex">
                                        @csrf
                                        <button type="submit" class="btn btn-violet-soft">Proses</button>
                                    </form>
                                    @if($periode->payslips->count() > 0)
                                        <form method="POST" action="{{ route('admin.payroll.finalisasi', $periode) }}"
                                            onsubmit="return confirm('Finalisasi payroll {{ $periode->label }}? Data akan dikunci!')" class="m-0 p-0 flex">
                                            @csrf
                                            <button type="submit" class="btn btn-success-soft">Finalisasi</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="!text-center !py-10 text-ink-muted-48">
                            Belum ada periode payroll.
                            <a href="{{ route('admin.payroll.create') }}" class="text-primary ml-1">Buat sekarang?</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($periodes->hasPages())
            <div class="px-6 py-4 border-t border-divider-soft">{{ $periodes->links() }}</div>
        @endif
    </div>
@endsection
