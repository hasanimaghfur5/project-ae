@extends('layouts.app')

@section('title', 'Dashboard')

@push('head')
<style>
    @keyframes dash-in { to { stroke-dashoffset: 0; } }
    @keyframes grow-bar { from { transform: scaleX(0); } to { transform: scaleX(1); } }
    .chart-line { stroke-dasharray: 1; stroke-dashoffset: 1; animation: dash-in 1.4s cubic-bezier(.22,1,.36,1) forwards; }
    .chart-bar { transform-origin: left; animation: grow-bar .9s cubic-bezier(.22,1,.36,1) both; }
    @media (prefers-reduced-motion: reduce) {
        .chart-line, .chart-bar { animation: none; stroke-dashoffset: 0; }
    }
</style>
@endpush

@section('content')
@php
    $hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulanIndonesia = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $tanggalHariIni = $hariIndonesia[now()->dayOfWeek].', '.now()->day.' '.$bulanIndonesia[now()->month - 1].' '.now()->year;

    $formatRupiah = fn (float $nilai): string => 'Rp '.number_format($nilai, 0, ',', '.');

    $formatRingkas = function (float $nilai): string {
        if ($nilai >= 1000000) {
            return 'Rp '.number_format($nilai / 1000000, $nilai >= 10000000 ? 0 : 1, ',', '.').' jt';
        }

        if ($nilai >= 1000) {
            return 'Rp '.number_format($nilai / 1000, 0, ',', '.').' rb';
        }

        return 'Rp '.number_format($nilai, 0, ',', '.');
    };

    $gradeData = collect([
        ['label' => 'A', 'jumlah' => (int) ($perGrade['A'] ?? 0), 'warna' => '#4F957A'],
        ['label' => 'B', 'jumlah' => (int) ($perGrade['B'] ?? 0), 'warna' => '#E07642'],
        ['label' => 'Minus', 'jumlah' => (int) ($perGrade['Minus'] ?? 0), 'warna' => '#A3A3A3'],
    ])->map(function (array $grade) use ($statistik): array {
        $grade['persen'] = $statistik['total_produk'] > 0
            ? round(($grade['jumlah'] / $statistik['total_produk']) * 100, 1)
            : 0;

        return $grade;
    });
    $totalGrade = $gradeData->sum('jumlah');

    $statusData = [
        ['label' => 'Tersedia', 'jumlah' => $statistik['tersedia'], 'warna' => 'bg-brand-500'],
        ['label' => 'Dipesan', 'jumlah' => $statistik['dipesan'], 'warna' => 'bg-clay-500'],
        ['label' => 'Terjual', 'jumlah' => $statistik['terjual'], 'warna' => 'bg-stone-400'],
    ];
    $totalStatus = array_sum(array_column($statusData, 'jumlah'));

    $nilaiMaksKategori = max((float) ($nilaiKategori->max('nilai') ?? 0), 1);
    $unitMaksTerlaris = max((int) ($produkTerlaris->max('unit') ?? 0), 1);

    $chartLebar = 720;
    $chartTinggi = 260;
    $padKiri = 64;
    $padKanan = 18;
    $padAtas = 18;
    $padBawah = 42;
    $tinggiPlot = $chartTinggi - $padAtas - $padBawah;
    $nilaiMaks = max((float) $trenPendapatan->max('total'), 100000);
    $jumlahHari = $trenPendapatan->count();
    $langkahX = $jumlahHari > 1 ? ($chartLebar - $padKiri - $padKanan) / ($jumlahHari - 1) : 0;
    $garisDasar = $padAtas + $tinggiPlot;

    $titikGrafik = $trenPendapatan->values()->map(function (array $hari, int $indeks) use ($padKiri, $padAtas, $langkahX, $tinggiPlot, $nilaiMaks): array {
        $hari['x'] = $padKiri + ($indeks * $langkahX);
        $hari['y'] = $padAtas + ($tinggiPlot * (1 - ($hari['total'] / $nilaiMaks)));

        return $hari;
    });

    $garisGrafik = $titikGrafik
        ->map(fn (array $titik): string => round($titik['x'], 2).','.round($titik['y'], 2))
        ->implode(' ');
    $areaGrafik = $padKiri.','.$garisDasar.' '.$garisGrafik.' '.round($padKiri + (($jumlahHari - 1) * $langkahX), 2).','.$garisDasar;

    $sparkLebar = 190;
    $sparkTinggi = 56;
    $sparkAtas = 5;
    $sparkBawah = 5;
    $sparkLangkah = $jumlahHari > 1 ? ($sparkLebar - 8) / ($jumlahHari - 1) : 0;
    $sparkMaks = max((float) $trenPendapatan->max('total'), 1);
    $sparkTitik = $trenPendapatan->values()->map(function (array $hari, int $indeks) use ($sparkLebar, $sparkAtas, $sparkTinggi, $sparkBawah, $sparkLangkah, $sparkMaks): array {
        $hari['x'] = 4 + ($indeks * $sparkLangkah);
        $hari['y'] = $sparkAtas + (($sparkTinggi - $sparkAtas - $sparkBawah) * (1 - ($hari['total'] / $sparkMaks)));

        return $hari;
    });
    $sparkGaris = $sparkTitik->map(fn (array $titik): string => round($titik['x'], 2).','.round($titik['y'], 2))->implode(' ');
    $sparkArea = '4,'.($sparkTinggi - $sparkBawah).' '.$sparkGaris.' '.round(4 + (($jumlahHari - 1) * $sparkLangkah), 2).','.($sparkTinggi - $sparkBawah);

    $kartuKecil = [
        [
            'label' => 'Nilai Inventaris',
            'nilai' => $formatRupiah($statistik['nilai_inventaris']),
            'sub' => $statistik['tersedia'].' item tersedia',
            'ikon' => 'text-brand-700',
            'latar' => 'bg-brand-50',
            'path' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V6.75A2.25 2.25 0 0 0 18.75 4.5H5.25A2.25 2.25 0 0 0 3 6.75V12" />',
        ],
        [
            'label' => 'Produk Tersedia',
            'nilai' => number_format($statistik['tersedia'], 0, ',', '.'),
            'sub' => 'siap dijual hari ini',
            'ikon' => 'text-brand-700',
            'latar' => 'bg-brand-50',
            'path' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a2.25 2.25 0 0 0 3.182 0l4.318-4.318a2.25 2.25 0 0 0 0-3.182l-9.581-9.581A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />',
        ],
        [
            'label' => 'Terjual',
            'nilai' => number_format($statistik['terjual'], 0, ',', '.'),
            'sub' => $statistik['transaksi'].' transaksi berhasil',
            'ikon' => 'text-clay-600',
            'latar' => 'bg-clay-50',
            'path' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12A2.25 2.25 0 0 1 18.65 23H5.35a2.25 2.25 0 0 1-2.243-2.493l1.263-12A2.25 2.25 0 0 1 6.61 6.5h10.78a2.25 2.25 0 0 1 2.242 2.007Z" />',
        ],
        [
            'label' => 'Sedang Dipesan',
            'nilai' => number_format($statistik['dipesan'], 0, ',', '.'),
            'sub' => 'terkunci maks. 15 menit',
            'ikon' => 'text-stone-600',
            'latar' => 'bg-stone-100',
            'path' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
        ],
    ];
@endphp

<section class="relative overflow-hidden bg-brand-900 text-cream shadow-soft">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_12%_12%,rgba(194,65,12,0.3),transparent_36%),radial-gradient(circle_at_92%_88%,rgba(79,149,122,0.38),transparent_48%)]"></div>
    <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full border border-cream/10"></div>
    <div class="absolute -right-8 -top-8 h-44 w-44 rounded-full border border-cream/10"></div>
    <div class="relative p-6 sm:p-8 lg:p-10">
        <div class="flex flex-wrap items-start justify-between gap-8">
            <div>
                <p class="eyebrow flex items-center gap-3 !text-cream/60"><span class="h-px w-8 bg-clay-400"></span>Ringkasan Operasional</p>
                <h1 class="mt-3 text-4xl text-cream sm:text-5xl">Dashboard</h1>
                <p class="mt-3 max-w-xl text-sm leading-relaxed text-cream/65">Pantau arus stok, performa penjualan, dan kesehatan inventaris ReWear dalam satu tampilan.</p>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center gap-2 border border-cream/15 bg-white/5 px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-cream/75">
                        <svg class="h-3.5 w-3.5 text-clay-300" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                        {{ $tanggalHariIni }}
                    </span>
                    <span class="inline-flex items-center gap-2 border border-cream/15 bg-white/5 px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-cream/75">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-300 motion-reduce:animate-none animate-pulse"></span>
                        Data tersinkron
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('produk.create') }}" class="btn-clay">+ Input Produk</a>
                <a href="{{ route('pos.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-cream/25 px-6 py-3 text-[11px] font-bold uppercase tracking-[0.18em] text-cream transition duration-200 hover:bg-cream hover:text-ink">Buka POS</a>
            </div>
        </div>
    </div>
</section>

<div class="mt-5 grid gap-5 xl:grid-cols-12">
    <div class="relative overflow-hidden border border-stone-200 bg-white p-6 shadow-soft sm:p-7 xl:col-span-6">
        <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-brand-50"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Pendapatan</p>
                <p class="mt-3 font-display text-5xl leading-none text-ink">{{ $formatRupiah($pendapatanMingguIni) }}</p>
            </div>
            <span class="inline-flex items-center gap-2 border border-stone-200 bg-stone-50 px-3 py-1.5 text-[9px] font-bold uppercase tracking-[0.18em] text-stone-500">7 hari terakhir</span>
        </div>
        <div class="relative mt-4 flex flex-wrap items-end justify-between gap-5">
            <div class="flex items-center gap-2">
                @if($pertumbuhan !== null)
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.14em] {{ $pertumbuhan >= 0 ? 'bg-brand-50 text-brand-700' : 'bg-clay-50 text-clay-700' }}">
                        <svg class="h-3 w-3 {{ $pertumbuhan >= 0 ? '' : 'rotate-180' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75 12 8.25l7.5 7.5" /></svg>
                        {{ $pertumbuhan >= 0 ? '+' : '' }}{{ number_format($pertumbuhan, 1, ',', '.') }}%
                    </span>
                    <span class="text-[10px] text-stone-500">vs 7 hari sebelumnya</span>
                @else
                    <span class="inline-flex items-center gap-1 bg-stone-100 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-stone-500">Minggu ini</span>
                    <span class="text-[10px] text-stone-500">belum ada pembanding</span>
                @endif
            </div>
            <svg viewBox="0 0 {{ $sparkLebar }} {{ $sparkTinggi }}" class="h-14 w-44 shrink-0" aria-hidden="true">
                <defs>
                    <linearGradient id="sparkFill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#4F957A" stop-opacity="0.35" />
                        <stop offset="100%" stop-color="#4F957A" stop-opacity="0" />
                    </linearGradient>
                </defs>
                <polygon points="{{ $sparkArea }}" fill="url(#sparkFill)" />
                <polyline points="{{ $sparkGaris }}" fill="none" stroke="#2F7659" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <div class="relative mt-6 flex flex-wrap gap-x-6 gap-y-2 border-t border-stone-100 pt-4">
            @forelse($kanalPenjualan as $kanal)
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 {{ $kanal['kanal'] === 'pos' ? 'bg-clay-500' : 'bg-brand-500' }}"></span>
                    <span class="text-[9px] font-bold uppercase tracking-[0.16em] text-stone-500">{{ $kanal['kanal'] }}</span>
                    <span class="text-xs font-semibold text-ink">{{ $formatRupiah($kanal['omzet']) }}</span>
                </div>
            @empty
                <p class="text-xs text-stone-500">Belum ada pembayaran berhasil.</p>
            @endforelse
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 xl:col-span-6">
        @foreach($kartuKecil as $kartu)
            <div class="group relative overflow-hidden border border-stone-200 bg-white p-5 shadow-[0_10px_30px_-24px_rgba(27,26,23,0.35)] transition duration-300 hover:-translate-y-0.5 hover:shadow-soft">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full {{ $kartu['latar'] }} opacity-70 transition duration-300 group-hover:scale-110"></div>
                <div class="relative flex items-start justify-between gap-4">
                    <p class="eyebrow">{{ $kartu['label'] }}</p>
                    <span class="flex h-9 w-9 items-center justify-center border border-stone-200 bg-white {{ $kartu['ikon'] }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">{!! $kartu['path'] !!}</svg>
                    </span>
                </div>
                <p class="relative mt-5 font-display text-3xl leading-none text-ink">{{ $kartu['nilai'] }}</p>
                <p class="relative mt-1.5 text-[9px] uppercase tracking-[0.16em] text-stone-400">{{ $kartu['sub'] }}</p>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-5 grid gap-5 xl:grid-cols-[1.6fr_1fr]">
    <div class="border border-stone-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="eyebrow">Pendapatan 7 Hari</h2>
                <p class="mt-1 font-display text-2xl text-ink">Arus kas masuk harian</p>
            </div>
            <div class="text-right">
                <p class="text-[9px] font-bold uppercase tracking-[0.18em] text-stone-400">Rata-rata / hari</p>
                <p class="mt-1 text-sm font-semibold text-ink">{{ $formatRupiah($pendapatanMingguIni / max($jumlahHari, 1)) }}</p>
            </div>
        </div>
        <div class="mt-5">
            <svg viewBox="0 0 {{ $chartLebar }} {{ $chartTinggi }}" class="h-auto w-full" role="img" aria-labelledby="trenPendapatanTitle trenPendapatanDesc">
                <title id="trenPendapatanTitle">Pendapatan tujuh hari terakhir</title>
                <desc id="trenPendapatanDesc">Grafik area pendapatan harian dari {{ $trenPendapatan->first()['tanggal_label'] }} sampai {{ $trenPendapatan->last()['tanggal_label'] }}.</desc>
                <defs>
                    <linearGradient id="areaPendapatan" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#2F7659" stop-opacity="0.22" />
                        <stop offset="100%" stop-color="#2F7659" stop-opacity="0" />
                    </linearGradient>
                </defs>
                @for($i = 0; $i <= 2; $i++)
                    @php
                        $rasio = $i / 2;
                        $y = $padAtas + ($tinggiPlot * (1 - $rasio));
                        $labelSumbu = $nilaiMaks * $rasio;
                    @endphp
                    <line x1="{{ $padKiri }}" y1="{{ $y }}" x2="{{ $chartLebar - $padKanan }}" y2="{{ $y }}" stroke="#E5E5E5" stroke-dasharray="{{ $i === 0 ? '0' : '4 6' }}" />
                    <text x="{{ $padKiri - 10 }}" y="{{ $y + 4 }}" text-anchor="end" fill="#A3A3A3" font-size="9">{{ $formatRingkas($labelSumbu) }}</text>
                @endfor
                <polygon points="{{ $areaGrafik }}" fill="url(#areaPendapatan)" />
                <polyline points="{{ $garisGrafik }}" fill="none" stroke="#2F7659" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" pathLength="1" class="chart-line" />
                @foreach($titikGrafik as $titik)
                    <circle cx="{{ $titik['x'] }}" cy="{{ $titik['y'] }}" r="4" fill="#FFFFFF" stroke="#2F7659" stroke-width="2.5">
                        <title>{{ $titik['label'] }} {{ $titik['tanggal_label'] }}: {{ $formatRupiah($titik['total']) }}</title>
                    </circle>
                    <text x="{{ $titik['x'] }}" y="{{ $chartTinggi - 16 }}" text-anchor="middle" fill="#737373" font-size="10">{{ $titik['label'] }}</text>
                    <text x="{{ $titik['x'] }}" y="{{ $chartTinggi - 4 }}" text-anchor="middle" fill="#A3A3A3" font-size="8">{{ $titik['tanggal_label'] }}</text>
                @endforeach
            </svg>
        </div>
        <details class="mt-3 border-t border-stone-100 pt-3 text-xs text-stone-500">
            <summary class="cursor-pointer select-none text-[10px] font-bold uppercase tracking-[0.16em] text-stone-500 hover:text-ink">Lihat data harian</summary>
            <table class="mt-3 w-full">
                <thead>
                    <tr class="border-b border-stone-100 text-left text-[9px] font-bold uppercase tracking-[0.16em] text-stone-400">
                        <th class="py-2">Tanggal</th>
                        <th class="py-2 text-right">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trenPendapatan as $hari)
                        <tr class="border-b border-stone-50 last:border-0">
                            <td class="py-2">{{ $hari['label'] }}, {{ $hari['tanggal_label'] }}</td>
                            <td class="py-2 text-right font-semibold text-ink">{{ $formatRupiah($hari['total']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </details>
    </div>

    <div class="relative overflow-hidden border border-brand-800 bg-brand-900 p-5 text-cream shadow-soft sm:p-6">
        <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-brand-400/20"></div>
        <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-clay-500/10"></div>
        <div class="relative">
            <h2 class="eyebrow !text-cream/55">Komposisi Grade</h2>
            <p class="mt-1 font-display text-2xl text-cream">Kualitas inventaris</p>
            <div class="mt-6 flex flex-wrap items-center gap-6">
                <div class="relative h-36 w-36 shrink-0">
                    <svg viewBox="0 0 140 140" class="h-full w-full" role="img" aria-label="Komposisi grade: {{ $gradeData->map(fn ($grade) => 'Grade '.$grade['label'].' '.$grade['jumlah'].' item')->implode(', ') }}">
                        <circle cx="70" cy="70" r="54" fill="none" stroke="#FFFFFF14" stroke-width="18" />
                        @php $offsetDonat = 0; @endphp
                        @foreach($gradeData as $grade)
                            @if($grade['jumlah'] > 0)
                                <circle cx="70" cy="70" r="54" fill="none" stroke="{{ $grade['warna'] }}" stroke-width="18" pathLength="100" stroke-dasharray="{{ $grade['persen'] }} {{ 100 - $grade['persen'] }}" stroke-dashoffset="{{ -$offsetDonat }}" transform="rotate(-90 70 70)" />
                                @php $offsetDonat += $grade['persen']; @endphp
                            @endif
                        @endforeach
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="font-display text-4xl leading-none text-cream">{{ $totalGrade }}</span>
                        <span class="mt-1 text-[8px] font-bold uppercase tracking-[0.2em] text-cream/45">item</span>
                    </div>
                </div>
                <div class="min-w-[9rem] flex-1 space-y-3">
                    @foreach($gradeData as $grade)
                        <div class="flex items-center justify-between gap-4 text-xs">
                            <span class="flex items-center gap-2 text-cream/70"><span class="h-2 w-2" style="background: {{ $grade['warna'] }}"></span>Grade {{ $grade['label'] }}</span>
                            <span class="font-semibold text-cream">{{ $grade['jumlah'] }} <span class="font-normal text-cream/40">· {{ $grade['persen'] }}%</span></span>
                        </div>
                    @endforeach
                </div>
            </div>
            <p class="mt-6 border-t border-white/10 pt-4 text-[9px] uppercase leading-relaxed tracking-[0.16em] text-cream/45">Grade A kondisi terbaik · Minus perlu perhatian</p>
        </div>
    </div>
</div>

<div class="mt-5 grid gap-5 xl:grid-cols-2">
    <div class="border border-stone-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="eyebrow">Nilai Inventaris per Kategori</h2>
                <p class="mt-1 font-display text-2xl text-ink">Modal yang masih bergerak</p>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-[0.16em] text-stone-400">Stok × harga jual</span>
        </div>
        <div class="mt-6 space-y-5">
            @forelse($nilaiKategori as $index => $kategori)
                @php $persenKategori = max(0, min(100, ($kategori['nilai'] / $nilaiMaksKategori) * 100)); @endphp
                <div>
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-ink">{{ $kategori['nama'] }}</p>
                            <p class="text-[9px] uppercase tracking-[0.16em] text-stone-400">{{ $kategori['unit'] }} unit tersedia</p>
                        </div>
                        <p class="text-sm font-semibold text-ink">{{ $formatRupiah($kategori['nilai']) }}</p>
                    </div>
                    <div class="mt-2 h-2 bg-stone-100">
                        <div class="chart-bar h-2 {{ $index === 0 ? 'bg-clay-500' : 'bg-brand-600' }}" style="width: {{ $persenKategori }}%; animation-delay: {{ $index * 70 }}ms"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-stone-500">Belum ada stok tersedia.</p>
            @endforelse
        </div>
        <div class="mt-6 flex items-center justify-between border-t border-stone-100 pt-4">
            <span class="text-[9px] font-bold uppercase tracking-[0.16em] text-stone-400">Total nilai inventaris</span>
            <span class="font-display text-xl text-ink">{{ $formatRupiah($statistik['nilai_inventaris']) }}</span>
        </div>
    </div>

    <div class="border border-stone-200 bg-white p-5 shadow-soft sm:p-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="eyebrow">Produk Terlaris</h2>
                <p class="mt-1 font-display text-2xl text-ink">Paling cepat berpindah tangan</p>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-[0.16em] text-stone-400">Sepanjang data</span>
        </div>
        <div class="mt-4 divide-y divide-stone-100">
            @forelse($produkTerlaris as $index => $terlaris)
                <div class="flex items-center gap-4 py-3">
                    <span class="w-6 shrink-0 font-display text-xl italic text-stone-300">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="h-16 w-12 shrink-0 overflow-hidden bg-stone-100">
                        @if($terlaris->produk?->foto)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($terlaris->produk->foto) }}" alt="{{ $terlaris->produk->nama_produk }}" class="h-full w-full object-cover" loading="lazy">
                        @else
                            <div class="flex h-full w-full items-center justify-center font-display text-xl italic text-stone-300">R</div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-ink">{{ $terlaris->produk?->nama_produk ?? 'Produk dihapus' }}</p>
                        <p class="mt-0.5 text-[9px] uppercase tracking-[0.16em] text-stone-400">{{ $terlaris->produk?->merek ?: 'Tanpa merek' }} · {{ (int) $terlaris->unit }} unit</p>
                        <div class="mt-2 h-1 bg-stone-100">
                            <div class="h-1 bg-brand-500" style="width: {{ min(100, ((int) $terlaris->unit / $unitMaksTerlaris) * 100) }}%"></div>
                        </div>
                    </div>
                    <p class="whitespace-nowrap text-sm font-semibold text-ink">{{ $formatRupiah((float) $terlaris->omzet) }}</p>
                </div>
            @empty
                <p class="py-8 text-center text-sm text-stone-500">Belum ada produk terjual.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-5 grid gap-5 xl:grid-cols-[1.6fr_1fr]">
    <div class="overflow-hidden border border-stone-200 bg-white shadow-soft">
        <div class="flex items-center justify-between border-b border-stone-200 px-5 py-4 sm:px-6">
            <div>
                <h2 class="eyebrow">Pesanan Terbaru</h2>
                <p class="mt-1 font-display text-2xl text-ink">Lintas kanal penjualan</p>
            </div>
            <a href="{{ route('pesanan.index') }}" class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-700 underline decoration-clay-500 underline-offset-4 hover:text-clay-600">Lihat semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-stone-200 bg-stone-50">
                    <tr>
                        <th class="table-head px-5 py-3 sm:px-6">ID</th>
                        <th class="table-head px-5 py-3">Pelanggan</th>
                        <th class="table-head px-5 py-3">Kanal</th>
                        <th class="table-head px-5 py-3">Total</th>
                        <th class="table-head px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($pesananTerbaru as $order)
                        @php $badge = ['pending' => 'bg-clay-100 text-clay-700', 'dibayar' => 'bg-brand-100 text-brand-800', 'selesai' => 'bg-brand-600 text-white', 'batal' => 'bg-stone-200 text-stone-600'][$order->status_pesanan] ?? 'bg-stone-100 text-stone-600'; @endphp
                        <tr class="transition hover:bg-stone-50">
                            <td class="px-5 py-3 font-mono text-xs font-bold text-ink sm:px-6"><a href="{{ route('pesanan.show', $order) }}" class="hover:text-brand-700">#{{ str_pad((string) $order->id_pesanan, 5, '0', STR_PAD_LEFT) }}</a></td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center bg-brand-50 text-xs font-bold text-brand-700">{{ strtoupper(substr($order->pengguna->name, 0, 1)) }}</span>
                                    <div>
                                        <p class="text-sm font-medium text-ink">{{ $order->pengguna->name }}</p>
                                        <p class="text-[9px] uppercase tracking-[0.14em] text-stone-400">{{ $order->tanggal_pesanan?->format('d/m H:i') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-[0.16em] {{ $order->kanal === 'pos' ? 'text-clay-700' : 'text-brand-700' }}"><span class="h-1.5 w-1.5 {{ $order->kanal === 'pos' ? 'bg-clay-500' : 'bg-brand-500' }}"></span>{{ $order->kanal }}</span></td>
                            <td class="px-5 py-3 font-semibold text-ink">Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}</td>
                            <td class="px-5 py-3"><span class="px-2 py-1 text-[9px] font-bold uppercase tracking-[0.16em] {{ $badge }}">{{ ucfirst($order->status_pesanan) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-stone-500">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-5">
        <div class="relative overflow-hidden border {{ $aging > 0 ? 'border-clay-300 bg-clay-50' : 'border-stone-200 bg-white' }} p-5 shadow-soft">
            <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full {{ $aging > 0 ? 'bg-clay-200/60' : 'bg-stone-100' }}"></div>
            <div class="relative">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="eyebrow {{ $aging > 0 ? '!text-clay-700' : '' }}">Stok Mengendap</h2>
                        <p class="mt-1 font-display text-xl {{ $aging > 0 ? 'text-clay-900' : 'text-ink' }}">Lebih dari 30 hari</p>
                    </div>
                    <p class="font-display text-5xl leading-none {{ $aging > 0 ? 'text-clay-700' : 'text-ink' }}">{{ $aging }}</p>
                </div>
                <p class="mt-3 text-xs leading-relaxed {{ $aging > 0 ? 'text-clay-800' : 'text-stone-500' }}">Item tersedia lebih dari 30 hari. Pertimbangkan diskon cuci gudang atau bundling.</p>
                @if($aging > 0)
                    <a href="{{ route('laporan.index') }}" class="mt-4 inline-flex items-center gap-2 border-b border-clay-600 pb-1 text-[10px] font-bold uppercase tracking-[0.16em] text-clay-700 hover:text-clay-900">Lihat laporan aging <span aria-hidden="true">→</span></a>
                @endif
            </div>
        </div>

        <div class="border border-stone-200 bg-white p-5 shadow-soft">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="eyebrow">Kesehatan Inventaris</h2>
                    <p class="mt-1 font-display text-xl text-ink">Status seluruh item</p>
                </div>
                <span class="text-[9px] font-bold uppercase tracking-[0.16em] text-stone-400">{{ $totalStatus }} item</span>
            </div>
            <div class="mt-5 flex h-2 overflow-hidden bg-stone-100">
                @foreach($statusData as $status)
                    @php $persenStatus = $totalStatus > 0 ? ($status['jumlah'] / $totalStatus) * 100 : 0; @endphp
                    @if($status['jumlah'] > 0)
                        <div class="{{ $status['warna'] }}" style="width: {{ $persenStatus }}%"></div>
                    @endif
                @endforeach
            </div>
            <div class="mt-4 space-y-2.5">
                @foreach($statusData as $status)
                    @php $persenStatus = $totalStatus > 0 ? round(($status['jumlah'] / $totalStatus) * 100) : 0; @endphp
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2 text-stone-600"><span class="h-2 w-2 {{ $status['warna'] }}"></span>{{ $status['label'] }}</span>
                        <span class="font-semibold text-ink">{{ $status['jumlah'] }} <span class="font-normal text-stone-400">· {{ $persenStatus }}%</span></span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
