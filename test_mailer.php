<?php
$mail = require __DIR__ . "/mailer.php";

if ($mail instanceof PHPMailer) {
    echo "Mailer setup successful.";
} else {
    echo "Mailer setup failed.";
}
?>
