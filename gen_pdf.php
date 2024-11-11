<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');
require ('report.php');

    // Query to retrieve data from the "sam" table
    //$sql = "SELECT iID, iName, iEmail, iRegDate FROM sam ORDER BY iRegDate";
    
        // Get current year
        $current_year = date('Y');

        // Monthly sale Report for all months in current year, including most purchased item
        $sqls = "SELECT Ordered_date_time,
        DATE_FORMAT(o.Ordered_date_time, '%Y-%m') AS month_year,
        DATE_FORMAT(o.Ordered_date_time, '%M') AS month_name,
        COUNT(*) AS total_orders,
        SUM(CASE WHEN o.Order_status IN ('delivered', 'received') THEN o.Total_amount ELSE 0 END) AS monthly_sales,
        (
            SELECT i.Item_name 
            FROM orders oi
            JOIN item i ON oi.Item_ID = i.Item_ID
            WHERE DATE_FORMAT(oi.Ordered_date_time, '%Y-%m') = month_year
            GROUP BY oi.Item_ID 
            ORDER BY SUM(oi.Quantity) DESC 
            LIMIT 1
        ) AS most_bought_item
        FROM orders o
        WHERE YEAR(o.Ordered_date_time) = '$current_year'
        AND o.Order_status IN ('delivered', 'received')
        GROUP BY DATE_FORMAT(o.Ordered_date_time, '%Y-%m')
        ORDER BY o.Ordered_date_time";
    $stmt = $conn->query($sqls);

    // Initialize TCPDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Active User and Credential Report');
    $pdf->SetSubject('Active User and Credential Report');
    $pdf->SetKeywords('TCPDF, PDF, report');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 10);

    // Include Bootstrap CSS
    $html = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">';

    // Header
    $html .= '<h1 class="text-center mb-4">Monthly sales Report</h1>';
    $html .= '<p class="text-center">Report generated on ' . date('Y-m-d') . '</p>';

    // Initialize variable to track current month
    $currentMonth = '';

    // Table
    $html .= '<table class="table table-bordered">';
    $html .= '<thead class="thead-dark">';
    $html .= '<tr><th>Month</th><th>total orders</th><th>Monthsales</th><th>Most Bought Item</th></tr>';
    $html .= '</thead>';
    $html .= '<tbody>';

    // Loop through the results
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Extract month and year from the registration date
        $month = date('Y', strtotime($row['Ordered_date_time']));


        // Add data for each record
        $html .= '<tr>';
        $html .= '<td>' . $row['month_name'] . ' ' . $month . '</td>';
        $html .= '<td>' . $row['total_orders'] . '</td>';
        $html .= '<td>' . $row['monthly_sales'] . '</td>';
        $html .= '<td>' . $row['most_bought_item'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody>';
    $html .= '</table>';

    // Output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('active_user_report.pdf', 'D');

?>
