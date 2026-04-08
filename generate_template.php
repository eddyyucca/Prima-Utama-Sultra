<?php
/**
 * Jalankan script ini untuk membuat template Excel:
 * php generate_template.php
 */
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Font, Alignment, Border};

$spreadsheet = new Spreadsheet();

// =====================
// Sheet 1: DFT GJ
// =====================
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('    DFT GJ     ');

// Company header
$sheet->mergeCells('B2:C2');
$sheet->setCellValue('B2', 'PT PRIMA UTAMA SULTRA');
$sheet->mergeCells('B3:C3');
$sheet->setCellValue('B3', 'DAFTAR GAJI TENAGA KERJA PT SCM');
$sheet->setCellValue('B4', date('Y-m-d'));

// BPJS headers row 5
$sheet->mergeCells('AJ5:AM5');
$sheet->setCellValue('AJ5', 'IURAN BPJS KETENAGAKERJAAN');

// Working days row 6
$sheet->setCellValue('B6', 'Jumlah hari kerja :');
$sheet->setCellValue('E6', 28);

// Column headers row 7
$headers = [
    'B' => 'NO.',
    'C' => 'NIK',
    'D' => 'NAMA',
    'E' => 'NIK KTP',
    'F' => 'DEPARTMENT',
    'G' => 'JABATAN',
    'H' => 'TANGGAL MASUK KONTRAK',
    'I' => 'TANGGAL AKHIR KONTRAK',
    'J' => 'NO. ACC',
    'K' => 'NAMA BANK',
    'L' => 'GAJI POKOK',
    'M' => 'GAJI KURANGI POTONGAN',
    'N' => 'RAPEL GAJI/LEMBUR',
    'O' => 'KOMPENSASI PKWT',
    'P' => 'LEMBUR',
    'Q' => 'TOTAL PENDAPATAN',
    'R' => 'BPJS KESEHATAN 1%',
    'S' => 'BPJS KETENAGAKERJAAN 3%',
    'T' => 'PPH 21',
    'U' => 'PEMBUATAN REKENING',
    'V' => 'METERAI',
    'W' => 'PINJAMAN PRIBADI',
    'X' => 'SUMBANGAN',
    'Y' => 'TOTAL POTONGAN',
    'Z' => 'DITRANSFER',
    'AA' => 'TOTAL DITRANSFER',
    'AB' => 'TANGGAL AKHIR',
    'AC' => 'POTONGAN ABSEN (RUPIAH)',
    'AD' => 'MASUK KERJA(HARI)',
    'AE' => 'LAMA LEMBUR (JAM)',
    'AF' => 'TGL LAHIR',
    'AG' => 'KPJ',
    'AH' => 'JKN',
    'AI' => 'JHT (3,7%)',
    'AJ' => 'JKM (0,3%)',
    'AK' => 'JKK (0,24%)',
    'AL' => 'PENSIUN (2%)',
    'AM' => 'TOT. IURAN',
    'AN' => 'FINGER',
    'AO' => 'Hadir',
    'AP' => 'Cuti',
    'AQ' => 'Travel',
    'AR' => 'Sakit',
    'AS' => 'Ijin',
    'AT' => 'Alpa',
    'AU' => 'Off',
    'AV' => 'TOTAL HK',
    'AW' => 'OT',
    'AX' => 'PPH 21',
];

foreach ($headers as $col => $header) {
    $sheet->setCellValue($col . '7', $header);
}

// Style header row
$headerStyle = [
    'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FFFFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF1A3A5C']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]],
];
$sheet->getStyle('B7:AX7')->applyFromArray($headerStyle);

// Sample data row 9
$sampleData = [
    'B' => 1,
    'C' => 'PUS220001',
    'D' => 'NAMA KARYAWAN',
    'E' => '7471012345670001',
    'F' => 'MINING OPERATION',
    'G' => 'Crew Survey',
    'H' => '2026-01-01',
    'I' => '2026-12-31',
    'J' => '123456789',
    'K' => 'BANK BRI',
    'L' => 3269100,
    'M' => 3269100,
    'N' => 0,
    'O' => 0,
    'P' => 1558963.8,
    'Q' => 4828063.8,
    'R' => 32691,
    'S' => 98073,
    'T' => 0,
    'U' => 0,
    'V' => 0,
    'W' => 0,
    'X' => 0,
    'Y' => 130764,
    'Z' => 'di transfer',
    'AA' => 4697299.8,
    'AB' => '2003-05-15',
    'AC' => 0,
    'AD' => 28,
    'AE' => 82,
    'AF' => '2003-05-15',
    'AG' => '18071234567',
    'AH' => '0001234567890',
    'AI' => 130764,
    'AJ' => 10441,
    'AK' => 60559,
    'AL' => 69608,
    'AM' => 373795,
    'AN' => '',
    'AO' => 14,
    'AP' => 12,
    'AQ' => 2,
    'AR' => 0,
    'AS' => 0,
    'AT' => 0,
    'AU' => 0,
    'AV' => 28,
    'AW' => 82,
    'AX' => 0,
];

foreach ($sampleData as $col => $val) {
    $sheet->setCellValue($col . '9', $val);
}

// Style data row
$dataStyle = [
    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFF0F7FF']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
    'font' => ['size' => 9],
];
$sheet->getStyle('B9:AX9')->applyFromArray($dataStyle);

// Note rows 10-11
$sheet->setCellValue('B10', 'NOTE: Isi data karyawan mulai baris 9. Hapus baris contoh ini sebelum upload.');
$sheet->getStyle('B10')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFF0000'));

// Column widths
$colWidths = ['B'=>5,'C'=>12,'D'=>25,'E'=>18,'F'=>22,'G'=>25,'H'=>14,'I'=>14,'J'=>16,'K'=>18,'L'=>14,'M'=>14,'N'=>14,'O'=>14,'P'=>14,'Q'=>14,'R'=>12,'S'=>12,'T'=>10,'U'=>12,'V'=>10,'W'=>12,'X'=>10,'Y'=>12,'Z'=>12,'AA'=>14];
foreach ($colWidths as $col => $w) {
    $sheet->getColumnDimension($col)->setWidth($w);
}

// =====================
// Sheet 2: SLIP (info)
// =====================
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('    SLIP       ');
$sheet2->setCellValue('B1', 'SLIP GAJI - Dihasilkan otomatis oleh sistem');
$sheet2->setCellValue('B2', 'Sheet ini diisi otomatis. Untuk melihat slip, login ke portal karyawan.');

// =====================
// Sheet 3: PANDUAN
// =====================
$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('PANDUAN');

$panduan = [
    ['Kolom', 'Field', 'Keterangan', 'Contoh'],
    ['C', 'NIK', 'NIK Karyawan (WAJIB)', 'PUS220003'],
    ['D', 'NAMA', 'Nama Lengkap (WAJIB)', 'BUDI SANTOSO'],
    ['E', 'NIK KTP', 'Nomor KTP 16 digit', '7471012345670001'],
    ['F', 'DEPARTMENT', 'Nama departemen', 'MINING OPERATION'],
    ['G', 'JABATAN', 'Nama jabatan', 'Crew Survey'],
    ['H', 'TGL MASUK KONTRAK', 'Format: YYYY-MM-DD', '2026-01-01'],
    ['I', 'TGL AKHIR KONTRAK', 'Format: YYYY-MM-DD', '2026-12-31'],
    ['J', 'NO. REKENING', 'Nomor rekening bank', '123456789'],
    ['K', 'NAMA BANK', 'Nama bank', 'BANK BRI'],
    ['L', 'GAJI POKOK', 'Angka tanpa titik/koma', '3269100'],
    ['M', 'GAJI KURANGI POTONGAN', 'Gaji setelah dikurangi absen', '3269100'],
    ['N', 'RAPEL GAJI/LEMBUR', 'Angka, 0 jika tidak ada', '0'],
    ['O', 'KOMPENSASI PKWT', 'Angka, 0 jika tidak ada', '0'],
    ['P', 'LEMBUR', 'Total nominal lembur', '1558963.8'],
    ['Q', 'TOTAL PENDAPATAN', 'L+M+N+O atau total kotor', '4828063.8'],
    ['R', 'BPJS KES 1%', 'Potongan BPJS Kes karyawan 1%', '32691'],
    ['S', 'BPJS TK 3%', 'Potongan BPJS TK karyawan 3%', '98073'],
    ['T', 'PPH 21', 'Potongan pajak PPh21', '0'],
    ['AA', 'TOTAL DITRANSFER', 'Gaji bersih yang ditransfer (PENTING)', '4697299.8'],
    ['AF', 'TGL LAHIR', 'Format: YYYY-MM-DD (untuk verifikasi)', '1995-05-15'],
    ['AO', 'HADIR', 'Jumlah hari hadir', '14'],
    ['AP', 'CUTI', 'Jumlah hari cuti', '12'],
    ['AQ', 'TRAVEL', 'Jumlah hari travel', '2'],
    ['AR', 'SAKIT', 'Jumlah hari sakit', '0'],
    ['AS', 'IJIN', 'Jumlah hari ijin', '0'],
    ['AT', 'ALPA', 'Jumlah hari alpa', '0'],
    ['AU', 'OFF', 'Jumlah hari off', '0'],
    ['AV', 'TOTAL HK', 'Total hari kerja', '28'],
    ['AW', 'LEMBUR JAM', 'Total jam lembur', '82'],
];

foreach ($panduan as $i => $row) {
    $sheet3->fromArray($row, null, 'A' . ($i + 1));
}

$headerStyle2 = [
    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF1A3A5C']],
];
$sheet3->getStyle('A1:D1')->applyFromArray($headerStyle2);
$sheet3->getColumnDimension('A')->setWidth(6);
$sheet3->getColumnDimension('B')->setWidth(25);
$sheet3->getColumnDimension('C')->setWidth(40);
$sheet3->getColumnDimension('D')->setWidth(20);

// Save
if (!is_dir('public/template')) mkdir('public/template', 0755, true);
$writer = new Xlsx($spreadsheet);
$writer->save('public/template/template_gaji_pus.xlsx');
echo "Template berhasil dibuat: public/template/template_gaji_pus.xlsx\n";
