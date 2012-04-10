<?php
require_once 'QrPimp.php';
// Load boring QR-code
$pimpedQR = QrPimp::magic(new Imagick('img/qr.png'));
// Display shiny one
header('Content-Type: image/' . $pimpedQR->getImageFormat());
echo $pimpedQR;
?>
