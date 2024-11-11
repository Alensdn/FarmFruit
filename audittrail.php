<!DOCTYPE html>
<html>
    <head>
        <title>Audit Trail</title>
    </head>
    <body>
        <table>
            <caption>audit trail</caption>
            <tr>
                <th>Id</th>
                <th>User ID</th>
                <th>Action</th>
                <th>User Type</th>
            </tr>
            <?php 
            require "FruitSQLcon.php";
            // Modify the SQL query to exclude 'admin'
            $sql = "SELECT * FROM audit_trail WHERE userType IN ('rider', 'customer', 'employee')";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["userID"] . "</td>";
                    echo "<td>" . $row["action"] . "</td>";
                    echo "<td>" . $row["userType"] . "</td>";
                    echo "<td>" . $row["date"] . "</td>";
                    echo "</tr>";

                    // Debugging output
                    error_log("Fetched row: " . print_r($row, true));
                }
            } else {
                echo "<tr><td colspan='4'>0 results</td></tr>"; // Adjust colspan to match the number of columns
            }
            $conn->close();
            ?>
        </table>
    </body>
</html>
