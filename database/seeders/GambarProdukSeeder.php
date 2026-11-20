<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GambarProdukSeeder extends Seeder
{
    /**
     * @var array<string, array{0: string, 1: string}>
     */
    private array $palet = [
        'Atasan' => ['#1b3a2a', '#d9be93'],
        'Bawahan' => ['#26313a', '#d08a54'],
        'Outerwear' => ['#382a25', '#dda56f'],
        'Dress' => ['#332c3b', '#d8a8b4'],
        'Sepatu' => ['#2a2a2a', '#c9c3b7'],
        'Aksesoris' => ['#3b3326', '#d8b66c'],
    ];

    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('produk');
        Storage::disk('public')->makeDirectory('produk');

        $hero = database_path('seeders/hero.jpg');

        if (is_file($hero) && ($heroIsi = file_get_contents($hero)) !== false) {
            Storage::disk('public')->put('hero.jpg', $heroIsi);
        }

        Produk::query()->with('kategori')->get()->each(function (Produk $produk): void {
            $kategori = $produk->kategori?->nama_kategori ?? 'Atasan';
            $sumber = database_path('seeders/produk-foto/'.$produk->id_produk.'.jpg');
            $nama = Str::slug($produk->nama_produk).'-'.$produk->id_produk;

            if (is_file($sumber) && ($isi = file_get_contents($sumber)) !== false) {
                $path = 'produk/'.$nama.'.jpg';
                Storage::disk('public')->put($path, $isi);
            } else {
                $path = 'produk/'.$nama.'.svg';
                Storage::disk('public')->put($path, $this->svg($produk, $kategori));
            }

            $produk->update(['foto' => $path]);
        });
    }

    private function svg(Produk $produk, string $kategori): string
    {
        [$latar, $aksen] = $this->palet[$kategori] ?? $this->palet['Atasan'];
        $nama = $this->barisNama($produk->nama_produk);
        $labelKategori = $this->aman(mb_strtoupper($kategori));
        $merek = $this->aman(mb_strtoupper($produk->merek ?: 'Tanpa Merek'));
        $grade = $this->aman('Grade '.$produk->grade);
        $nomor = str_pad((string) $produk->id_produk, 3, '0', STR_PAD_LEFT);
        $ikon = $this->ikon($kategori, $aksen);
        $teksNama = '';

        foreach ($nama as $indeks => $baris) {
            $y = 604 + ($indeks * 52);
            $teksNama .= '<text x="44" y="'.$y.'" fill="#f5f2ea" font-family="Georgia, \'Times New Roman\', serif" font-size="42" font-style="italic">'.$this->aman($baris).'</text>';
        }

        $labelProduk = $this->aman($produk->nama_produk);

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 800" role="img" aria-label="{$labelProduk}">
            <rect width="600" height="800" fill="{$latar}"/>
            <circle cx="470" cy="170" r="190" fill="{$aksen}" fill-opacity="0.12"/>
            <circle cx="80" cy="520" r="120" fill="{$aksen}" fill-opacity="0.06"/>
            <path d="M44 560H556" stroke="{$aksen}" stroke-opacity="0.4" stroke-width="1"/>
            <text x="44" y="62" fill="{$aksen}" font-family="Helvetica, Arial, sans-serif" font-size="11" letter-spacing="4">REWEAR ARCHIVE</text>
            <text x="556" y="62" fill="{$aksen}" font-family="Helvetica, Arial, sans-serif" font-size="11" letter-spacing="2" text-anchor="end">NO. {$nomor}</text>
            <text x="44" y="94" fill="#f5f2ea" fill-opacity="0.7" font-family="Helvetica, Arial, sans-serif" font-size="10" letter-spacing="3">{$labelKategori}</text>
            <g transform="translate(300 340)" fill="none" stroke="{$aksen}" stroke-width="7" stroke-linecap="round" stroke-linejoin="round">{$ikon}</g>
            {$teksNama}
            <text x="44" y="742" fill="{$aksen}" font-family="Helvetica, Arial, sans-serif" font-size="10" letter-spacing="2">{$merek} · {$grade}</text>
            <text x="556" y="742" fill="#f5f2ea" fill-opacity="0.55" font-family="Helvetica, Arial, sans-serif" font-size="10" letter-spacing="2" text-anchor="end">PRELOVED · CURATED</text>
        </svg>
        SVG;
    }

    private function ikon(string $kategori, string $aksen): string
    {
        return match ($kategori) {
            'Bawahan' => '<path d="M-120 -150H120L150 170H55L0 -20L-55 170H-150Z" fill="'.$aksen.'" fill-opacity="0.1"/><path d="M-120 -150H120M-120 -108H120M-120 -150L-150 170M120 -150L150 170M-55 170L0 -20L55 170"/>',
            'Outerwear' => '<path d="M-130 -145L-55 -170L0 -120L55 -170L130 -145L185 -75L140 -45L120 -70L120 165Q0 190-120 165L-120 -70L-140 -45L-185 -75Z" fill="'.$aksen.'" fill-opacity="0.1"/><path d="M0 -120V180M-55 -170L-35 -130M55 -170L35 -130M-95 30V70M95 30V70"/>',
            'Dress' => '<path d="M-80 -160L-25 -180Q0 -150 25 -180L80 -160L100 -70L55 -55L80 30L155 170Q0 205-155 170L-80 30L-55 -55L-100 -70Z" fill="'.$aksen.'" fill-opacity="0.1"/><path d="M-80 30Q0 55 80 30M-25 -180Q0 -150 25 -180"/>',
            'Sepatu' => '<path d="M-170 90Q-160 10-90 10L-35 45Q20 80 90 88L170 105L175 165H-175Z" fill="'.$aksen.'" fill-opacity="0.1"/><path d="M-175 135L175 150M-80 20L-40 60M-50 20L-10 60M-20 20L20 60"/>',
            'Aksesoris' => '<path d="M-130 -80H130L155 170H-155Z" fill="'.$aksen.'" fill-opacity="0.1"/><path d="M-70 -80Q-70 -170 0 -170Q70 -170 70 -80M-130 -20H130"/>',
            default => '<path d="M-150 -110L-60 -150Q0 -105 60 -150L150 -110L190 -20L125 15L110 -10V160Q0 195-110 160V-10L-125 15L-190 -20Z" fill="'.$aksen.'" fill-opacity="0.1"/><path d="M-60 -150Q0 -105 60 -150M0 -105V175M50 0V160"/>',
        };
    }

    /**
     * @return array<int, string>
     */
    private function barisNama(string $nama): array
    {
        $kata = preg_split('/\s+/', trim($nama)) ?: [];
        $baris = [];
        $sekarang = '';

        foreach ($kata as $potongan) {
            $calon = $sekarang === '' ? $potongan : $sekarang.' '.$potongan;

            if (mb_strlen($calon) > 20 && $sekarang !== '') {
                $baris[] = $sekarang;
                $sekarang = $potongan;
            } else {
                $sekarang = $calon;
            }
        }

        if ($sekarang !== '') {
            $baris[] = $sekarang;
        }

        return array_slice($baris, 0, 3);
    }

    private function aman(string $teks): string
    {
        return htmlspecialchars($teks, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
