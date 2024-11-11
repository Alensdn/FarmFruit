<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');
require('report.php'); // Ensure this file is correctly required for database connection

// Get current date
$current_date = date('Y-m-d');

// Check if a specific month is set, otherwise use the current month
if (isset($_POST['month']) && !empty($_POST['month'])) {
    $selected_month = $_POST['month'];
} else {
    $selected_month = date('Y-m');
}

// Fetch items that have expired and their remaining quantities for the selected month
$sql_expired_items = "SELECT 
    i.Item_name,
    ih.remaining_quantity,
    ih.date_expiration,
    ih.boxname
FROM 
    item_history ih
INNER JOIN 
    item i ON ih.item_id = i.Item_ID
WHERE 
    ih.date_expiration < :current_date
    AND DATE_FORMAT(ih.date_expiration, '%Y-%m') = :selected_month";

$stmt_expired_items = $conn->prepare($sql_expired_items);
$stmt_expired_items->bindParam(':current_date', $current_date);
$stmt_expired_items->bindParam(':selected_month', $selected_month);
$stmt_expired_items->execute();
$result_expired_items = $stmt_expired_items->fetchAll(PDO::FETCH_ASSOC);

// Start output buffering to capture the HTML
ob_start();

echo "<div class='div5' id='expiredItems'>";
echo "<h2>Expired Items and Remaining Quantities</h2>";

// Month filter form
echo "<form method='post'>";
echo "<label for='month'>Select Month:</label>";
echo "<input type='month' id='month' name='month' value='".$selected_month."'>";
echo "<input type='submit' value='Filter'>";
echo "</form>";

echo "<table border='1' cellpadding='5'>";
echo "<tr>";
echo "<th>Item Name</th>";
echo "<th>Remaining Quantity</th>";
echo "<th>Date Expiration</th>";
echo "<th>Box Name</th>";
echo "</tr>";

if ($result_expired_items) {
    foreach ($result_expired_items as $row) {
        echo "<tr>";
        echo "<td>".htmlspecialchars($row['Item_name'])."</td>";
        echo "<td>".htmlspecialchars($row['remaining_quantity'])."</td>";
        echo "<td>".htmlspecialchars($row['date_expiration'])."</td>";
        echo "<td>".htmlspecialchars($row['boxname'])."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr>";
    echo "<td colspan='4'>No expired items found for the selected month</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// Get the HTML content
$html_content = ob_get_clean();

// Initialize TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Expired Items Report');
$pdf->SetSubject('Expired Items Report');
$pdf->SetKeywords('TCPDF, PDF, report');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page for the expired items report
$pdf->AddPage();
$pdf->writeHTML($html_content, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('expired_items_report.pdf', 'D');
?>

