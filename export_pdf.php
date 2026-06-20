<?php
require 'vendor/autoload.php';
require 'koneksi.php';
include 'login_check.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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


$html = '
<!DOCTYPE html>
<html>
<head>
<style>
    body { font-family: "Helvetica", Arial, sans-serif; color: #333; line-height: 1.4; }
    .header { text-align: center; margin-bottom: 30px; }
    .header h2 { margin: 0; color: #0F172A; font-size: 24px; }
    .header p { margin: 5px 0 0 0; color: #64748B; font-size: 12px; }
    
    .table-report { w-full; border-collapse: collapse; width: 100%; margin-top: 15px; }
    .table-report th { bg-color: #2563EB; background: #2563EB; color: white; font-weight: bold; padding: 10px; text-align: left; font-size: 12px; }
    .table-report td { padding: 10px; border-bottom: 1px solid #E2E8F0; font-size: 11px; }
    .table-report tr:nth-child(even) { background-color: #F8FAFC; }
    
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .total-row { font-weight: bold; background-color: #E2E8F0 !important; }
</style>
</head>
<body>

<div class="header">
    <h2>SALES REPORT ASSESSMENT</h2>
    <p>Export date: ' . date('d F Y, H:i') . '</p>
</div>

<table class="table-report">
    <thead>
        <tr>
            <th class="text-center" style="width: 5%">No</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Variant Color</th>
            <th class="text-center">Units Sold</th>
            <th class="text-right">Total Revenue</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
$grand_units = 0;
$grand_revenue = 0;

foreach ($report_data as $data) {
    $html .= '<tr>
        <td class="text-center">' . $no++ . '</td>
        <td>' . htmlspecialchars($data['name']) . '</td>
        <td>' . htmlspecialchars($data['category_name']) . '</td>
        <td>' . htmlspecialchars($data['color_name']) . '</td>
        <td class="text-center">' . $data['total_qty'] . '</td>
        <td class="text-center">$' . number_format($data['total_revenue'], 0, ',', '.') . '</td>
    </tr>';
    $grand_units += $data['total_qty'];
    $grand_revenue += $data['total_revenue'];
}

$html .= '
        <tr class="total-row">
            <td colspan="4" class="text-right">GRAND TOTAL :</td>
            <td class="text-center">' . $grand_units . '</td>
            <td class="text-center">$' . number_format($grand_revenue, 0, ',', '.') . '</td>
        </tr>
    </tbody>
</table>

</body>
</html>';


$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); 

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);


$dompdf->setPaper('A4', 'portrait');


$dompdf->render();


$dompdf->stream("SalesReport_" . date('Ymd') . ".pdf", array("Attachment" => false));
exit;