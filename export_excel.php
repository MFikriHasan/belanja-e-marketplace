<?php
require 'vendor/autoload.php';
require 'koneksi.php';
include 'login_check.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$seller_id = $_SESSION['seller_id'];


$q_products = $koneksi->prepare(
    "SELECT p.name, 
                      p.price, 
                      cv.color_name, 
                      cv.product_image, 
                      c.name AS category_name,
                      SUM(td.qty) AS total_qty, 
                      SUM(td.subtotal) AS total_revenue
              FROM transaction_det td
              JOIN color_varian cv ON cv.id = td.color_varian_id 
              JOIN product p ON p.id = cv.product_id             
              JOIN category c ON c.id = p.category_id
              WHERE td.seller_id = ?
              GROUP BY cv.id                                    
              ORDER BY total_qty DESC"
);
$q_products->bind_param("i", $seller_id);
$q_products->execute();
$report_data = $q_products->get_result()->fetch_all(MYSQLI_ASSOC);


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Sales Report');


$sheet->setCellValue('A1', 'SELLER SALES REPORT');
$sheet->mergeCells('A1:F1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'Export Date: ' . date('d-m-Y H:i:s'));
$sheet->mergeCells('A2:F2');
$sheet->getStyle('A2')->getFont()->setItalic(true);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


$headers = ['No', 'Product Name', 'Category', 'Variant Color', 'Units Sold', 'Total Revenue'];
$sheet->fromArray($headers, NULL, 'A4');


$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']], // Warna Biru Dashboard
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
];
$sheet->getStyle('A4:F4')->applyFromArray($headerStyle);


$rowNum = 5;
$no = 1;
$grand_units = 0;
$grand_revenue = 0;

foreach ($report_data as $data) {
    $sheet->setCellValue('A' . $rowNum, $no++);
    $sheet->setCellValue('B' . $rowNum, $data['name']);
    $sheet->setCellValue('C' . $rowNum, $data['category_name']);
    $sheet->setCellValue('D' . $rowNum, $data['color_name']);
    $sheet->setCellValue('E' . $rowNum, $data['total_qty']);
    $sheet->setCellValue('F' . $rowNum, $data['total_revenue']);
    
    
    $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('$#,##0');
    
    $grand_units += $data['total_qty'];
    $grand_revenue += $data['total_revenue'];
    $rowNum++;
}


$sheet->setCellValue('A' . $rowNum, 'TOTAL');
$sheet->mergeCells("A{$rowNum}:D{$rowNum}");
$sheet->setCellValue('E' . $rowNum, $grand_units);
$sheet->setCellValue('F' . $rowNum, $grand_revenue);

$sheet->getStyle("A{$rowNum}:F{$rowNum}")->getFont()->setBold(true);
$sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('$#,##0');
$sheet->getStyle("A{$rowNum}:D{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);


$borderStyle = [
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]
    ]
];
$sheet->getStyle("A4:F" . $rowNum)->applyFromArray($borderStyle);

foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="SalesReport_' . date('Ymd') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;