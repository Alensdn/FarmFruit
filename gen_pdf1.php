<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');
require ('report.php');

// Get current year
$current_year = date('Y');

// Monthly Sales by Payment Option Query
$sql_payment_option_report = "SELECT 
    DATE_FORMAT(Ordered_date_time, '%Y-%m') AS month_year,
    DATE_FORMAT(Ordered_date_time, '%M') AS month_name,
    paymentoption,
    SUM(Total_amount) AS total_sales
    FROM orders
    WHERE YEAR(Ordered_date_time) = '$current_year'
    GROUP BY DATE_FORMAT(Ordered_date_time, '%Y-%m'), paymentoption
    ORDER BY Ordered_date_time";
$stmt_payment_option = $conn->query($sql_payment_option_report);

// Monthly Sales by Payment Option HTML
$html_payment_option = '<h1 class="text-center mb-4">Monthly Sales by Payment Option Report</h1>';
$html_payment_option .= '<table class="table table-bordered">';
$html_payment_option .= '<thead class="thead-dark">';
$html_payment_option .= '<tr><th>Month</th><th>Payment Option</th><th>Total Sales</th></tr>';
$html_payment_option .= '</thead>';
$html_payment_option .= '<tbody>';
while ($row = $stmt_payment_option->fetch(PDO::FETCH_ASSOC)) {
    $html_payment_option .= '<tr>';
    $html_payment_option .= '<td>' . $row['month_name'] . '</td>';
    $html_payment_option .= '<td>' . $row['paymentoption'] . '</td>';
    $html_payment_option .= '<td>' . $row['total_sales'] . '</td>';
    $html_payment_option .= '</tr>';
}
$html_payment_option .= '</tbody>';
$html_payment_option .= '</table>';

// Initialize TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Monthly Sales Report');
$pdf->SetSubject('Monthly Sales Report');
$pdf->SetKeywords('TCPDF, PDF, report');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page for the Monthly Sales by Payment Option report
$pdf->AddPage();
$pdf->writeHTML($html_payment_option, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('monthly_sales_report.pdf', 'D');
?>
