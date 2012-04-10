<?php
require_once 'QrPimp.php';

function simple_test() {
	// Load boring QR-code
	$pimpedQR = QrPimp::magic(new Imagick('img/qr.png'));
	// Display shiny one
	header('Content-Type: image/' . $pimpedQR->getImageFormat());
	echo $pimpedQR;
}

function not_so_simple_test() {
	QrPimp::test('single');
	QrPimp::test('batch');
	QrPimp::test('palette');
}

not_so_simple_test();
?>
