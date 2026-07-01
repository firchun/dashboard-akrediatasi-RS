<?php

namespace Database\Seeders;

use App\Models\Pokja;
use App\Models\Regulasi;
use Illuminate\Database\Seeder;

class PokjaSeeder extends Seeder
{
    public function run(): void
    {
        $pokjas = [
            ['code' => 'TKRS', 'name' => 'Tata Kelola Rumah Sakit', 'group' => 'MANAJEMEN', 'ep_total' => 80],
            ['code' => 'KPS', 'name' => 'Kualifikasi & Pendidikan Staf', 'group' => 'MANAJEMEN', 'ep_total' => 62],
            ['code' => 'MFK', 'name' => 'Manajemen Fasilitas & Keselamatan', 'group' => 'MANAJEMEN', 'ep_total' => 70],
            ['code' => 'PMKP', 'name' => 'Peningkatan Mutu & Keselamatan Pasien', 'group' => 'MANAJEMEN', 'ep_total' => 60],
            ['code' => 'MRMIK', 'name' => 'Manajemen Rekam Medik & Informasi Kesehatan', 'group' => 'MANAJEMEN', 'ep_total' => 48],
            ['code' => 'PPI', 'name' => 'Pencegahan & Pengendalian Infeksi', 'group' => 'MANAJEMEN', 'ep_total' => 54],
            ['code' => 'PPK', 'name' => 'Pendidikan dalam Pelayanan Kesehatan', 'group' => 'MANAJEMEN', 'ep_total' => 30],
            ['code' => 'AKP', 'name' => 'Akses & Kesinambungan Pelayanan', 'group' => 'PASIEN', 'ep_total' => 52],
            ['code' => 'HPK', 'name' => 'Hak Pasien & Keterlibatan Keluarga', 'group' => 'PASIEN', 'ep_total' => 42],
            ['code' => 'PP', 'name' => 'Pengkajian Pasien', 'group' => 'PASIEN', 'ep_total' => 44],
            ['code' => 'PAP', 'name' => 'Pelayanan & Asuhan Pasien', 'group' => 'PASIEN', 'ep_total' => 64],
            ['code' => 'PAB', 'name' => 'Pelayanan Anestesi & Bedah', 'group' => 'PASIEN', 'ep_total' => 40],
            ['code' => 'PKPO', 'name' => 'Pelayanan Kefarmasian & Penggunaan Obat', 'group' => 'PASIEN', 'ep_total' => 62],
            ['code' => 'KE', 'name' => 'Komunikasi & Edukasi', 'group' => 'PASIEN', 'ep_total' => 28],
            ['code' => 'SKP', 'name' => 'Sasaran Keselamatan Pasien', 'group' => 'SKP', 'ep_total' => 24],
            ['code' => 'PROGNAS', 'name' => 'Program Nasional', 'group' => 'PROGNAS', 'ep_total' => 46],
        ];

        foreach ($pokjas as $data) {
            Pokja::create($data);
        }

        $regulasis = [
            'TKRS' => [
                ['SK Penetapan Struktur Organisasi & Tata Kerja (SOTK)', 'SK/Kebijakan', 'Selesai'],
                ['SK Visi, Misi, Motto & Nilai Dasar RS', 'SK/Kebijakan', 'Selesai'],
                ['Pedoman Tata Kelola RS (Hospital Bylaws)', 'Pedoman', 'Proses'],
                ['Panduan Pengelolaan Kontrak Klinis & Manajemen', 'Panduan', 'Belum'],
                ['SK Penetapan Indikator Mutu Prioritas RS (IMP-RS)', 'SK/Kebijakan', 'Proses'],
                ['Program Kerja TKRS', 'Program', 'Belum'],
            ],
            'KPS' => [
                ['Pedoman Pengelolaan SDM / Kepegawaian', 'Pedoman', 'Proses'],
                ['Panduan Rekrutmen & Seleksi Staf', 'Panduan', 'Belum'],
                ['Panduan Kredensial & Rekredensial Staf Klinis', 'Panduan', 'Belum'],
                ['SPO Orientasi Umum & Khusus Staf Baru', 'SPO', 'Proses'],
                ['SK Penetapan Pola Ketenagaan', 'SK/Kebijakan', 'Belum'],
                ['Program Diklat (Pendidikan & Pelatihan) Staf', 'Program', 'Belum'],
            ],
            'MFK' => [
                ['Pedoman Manajemen Fasilitas & Keselamatan', 'Pedoman', 'Proses'],
                ['Panduan Keselamatan & Keamanan Fasilitas', 'Panduan', 'Belum'],
                ['Panduan Pengelolaan B3 & Limbah B3', 'Panduan', 'Belum'],
                ['Panduan Penanggulangan Bencana (Disaster Plan)', 'Panduan', 'Belum'],
                ['Panduan Proteksi & Penanggulangan Kebakaran', 'Panduan', 'Belum'],
                ['Program Kerja MFK', 'Program', 'Belum'],
            ],
            'PMKP' => [
                ['Pedoman Peningkatan Mutu & Keselamatan Pasien', 'Pedoman', 'Proses'],
                ['SK Penetapan Indikator Mutu (IMP-RS & IMP-Unit)', 'SK/Kebijakan', 'Belum'],
                ['Panduan Manajemen Risiko (Risk Register)', 'Panduan', 'Belum'],
                ['Panduan Pelaporan Insiden Keselamatan Pasien (IKP)', 'Panduan', 'Proses'],
                ['SPO Audit Medis & Audit Klinis', 'SPO', 'Belum'],
                ['Program Kerja PMKP', 'Program', 'Belum'],
            ],
            'MRMIK' => [
                ['Pedoman Pengelolaan Rekam Medis', 'Pedoman', 'Proses'],
                ['Panduan Penyelenggaraan Rekam Medis Elektronik (RME)', 'Panduan', 'Proses'],
                ['Panduan Akses, Keamanan & Kerahasiaan Informasi', 'Panduan', 'Belum'],
                ['SPO Penyimpanan, Retensi & Pemusnahan Berkas RM', 'SPO', 'Belum'],
                ['SK Daftar Singkatan & Simbol Baku', 'SK/Kebijakan', 'Belum'],
                ['Program Kerja MRMIK', 'Program', 'Belum'],
            ],
            'PPI' => [
                ['Pedoman Pencegahan & Pengendalian Infeksi', 'Pedoman', 'Selesai'],
                ['Panduan Kewaspadaan Isolasi (Standar & Transmisi)', 'Panduan', 'Proses'],
                ['Panduan Surveilans HAIs', 'Panduan', 'Proses'],
                ['Panduan Kebersihan Tangan (Hand Hygiene)', 'Panduan', 'Selesai'],
                ['SPO Pengelolaan Linen, Laundry & Sterilisasi (CSSD)', 'SPO', 'Belum'],
                ['Program Kerja PPI', 'Program', 'Proses'],
            ],
            'PPK' => [
                ['SK Penetapan RS sebagai Wahana Pendidikan', 'SK/Kebijakan', 'Belum'],
                ['Pedoman Penyelenggaraan Pendidikan Klinis', 'Pedoman', 'Belum'],
                ['Panduan Supervisi Peserta Didik Klinis', 'Panduan', 'Belum'],
                ['Perjanjian Kerja Sama (PKS) Institusi Pendidikan', 'Lainnya', 'Belum'],
                ['Program Kerja PPK', 'Program', 'Belum'],
            ],
            'AKP' => [
                ['Pedoman Akses & Kesinambungan Pelayanan', 'Pedoman', 'Proses'],
                ['Panduan Skrining & Triase', 'Panduan', 'Selesai'],
                ['Panduan Transfer & Rujukan Pasien (intra/antar-RS)', 'Panduan', 'Proses'],
                ['SPO Perencanaan Pemulangan Pasien (Discharge Planning)', 'SPO', 'Belum'],
                ['Panduan Manajer Pelayanan Pasien (MPP/Case Manager)', 'Panduan', 'Belum'],
            ],
            'HPK' => [
                ['Pedoman Hak Pasien & Keterlibatan Keluarga', 'Pedoman', 'Proses'],
                ['Panduan Pemberian Informasi & Persetujuan (Informed Consent)', 'Panduan', 'Proses'],
                ['Panduan Penanganan Komplain / Keluhan', 'Panduan', 'Belum'],
                ['SPO Perlindungan Pasien & Kelompok Berisiko', 'SPO', 'Belum'],
                ['Panduan Pelayanan Kerohanian & Second Opinion', 'Panduan', 'Belum'],
            ],
            'PP' => [
                ['Pedoman Pengkajian Pasien', 'Pedoman', 'Proses'],
                ['Panduan Pengkajian Awal & Pengkajian Ulang', 'Panduan', 'Belum'],
                ['Panduan Pelayanan Laboratorium', 'Panduan', 'Belum'],
                ['Panduan Pelayanan Radiologi & Pencitraan (RIR)', 'Panduan', 'Belum'],
                ['SPO Asesmen Nyeri & Risiko Jatuh', 'SPO', 'Proses'],
            ],
            'PAP' => [
                ['Pedoman Pelayanan & Asuhan Pasien', 'Pedoman', 'Proses'],
                ['Panduan Asuhan Pasien Terintegrasi (CPPT)', 'Panduan', 'Belum'],
                ['Panduan Pelayanan Pasien Risiko Tinggi', 'Panduan', 'Belum'],
                ['Panduan Pelayanan Gizi', 'Panduan', 'Belum'],
                ['Panduan Pelayanan Tahap Terminal & Manajemen Nyeri', 'Panduan', 'Belum'],
                ['Panduan Pelayanan Kesehatan Jiwa & Risiko Bunuh Diri', 'Panduan', 'Belum'],
            ],
            'PAB' => [
                ['Pedoman Pelayanan Anestesi & Bedah', 'Pedoman', 'Proses'],
                ['Panduan Pelayanan Sedasi Moderat & Dalam', 'Panduan', 'Belum'],
                ['SPO Asesmen Pra-Anestesi & Pra-Induksi', 'SPO', 'Belum'],
                ['Panduan Penandaan Lokasi & Surgical Safety Checklist', 'Panduan', 'Belum'],
                ['SPO Laporan Operasi & Laporan Anestesi', 'SPO', 'Belum'],
            ],
            'PKPO' => [
                ['Pedoman Pelayanan Kefarmasian', 'Pedoman', 'Selesai'],
                ['SK Penetapan Formularium RS', 'SK/Kebijakan', 'Proses'],
                ['Panduan Pengelolaan Obat (seleksi s/d monitoring)', 'Panduan', 'Belum'],
                ['Panduan Obat High Alert, LASA & Narkotika', 'Panduan', 'Proses'],
                ['SPO Rekonsiliasi Obat', 'SPO', 'Belum'],
                ['Panduan Penggunaan Antibiotik (PPRA)', 'Panduan', 'Belum'],
            ],
            'KE' => [
                ['SK Penetapan Tim/Unit PKRS', 'SK/Kebijakan', 'Belum'],
                ['Pedoman Komunikasi Efektif', 'Pedoman', 'Proses'],
                ['Panduan Pemberian Edukasi Pasien & Keluarga', 'Panduan', 'Belum'],
                ['SPO Asesmen Kebutuhan & Hambatan Edukasi', 'SPO', 'Belum'],
                ['Program Kerja PKRS', 'Program', 'Belum'],
            ],
            'SKP' => [
                ['Panduan Identifikasi Pasien (SKP 1)', 'Panduan', 'Selesai'],
                ['Panduan Komunikasi Efektif SBAR/TBaK (SKP 2)', 'Panduan', 'Selesai'],
                ['Panduan Keamanan Obat yang Perlu Diwaspadai (SKP 3)', 'Panduan', 'Proses'],
                ['Panduan Tepat Lokasi-Prosedur-Pasien Operasi (SKP 4)', 'Panduan', 'Proses'],
                ['Panduan Kebersihan Tangan (SKP 5)', 'Panduan', 'Selesai'],
                ['Panduan Pencegahan Risiko Jatuh (SKP 6)', 'Panduan', 'Proses'],
            ],
            'PROGNAS' => [
                ['Program PONEK 24 Jam (Penurunan AKI–AKB)', 'Program', 'Proses'],
                ['Program Penanggulangan Tuberkulosis (TB)', 'Program', 'Proses'],
                ['Program Penanggulangan HIV/AIDS', 'Program', 'Belum'],
                ['Program Penurunan Prevalensi Stunting & Wasting', 'Program', 'Belum'],
                ['Program Pelayanan KB Rumah Sakit (PKBRS)', 'Program', 'Belum'],
                ['Program Pengendalian Resistansi Antimikroba (PPRA)', 'Program', 'Belum'],
                ['Program Pelayanan Kesehatan Jiwa', 'Program', 'Belum'],
            ],
        ];

        foreach ($regulasis as $code => $items) {
            $pokja = Pokja::where('code', $code)->first();
            if (!$pokja) continue;
            foreach ($items as $item) {
                $status = $item[2];
                $data = [
                    'pokja_id' => $pokja->id,
                    'nama' => $item[0],
                    'jenis' => $item[1],
                    'is_verified' => false,
                ];
                Regulasi::create($data);
            }
        }
    }
}
