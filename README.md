QrPimp
----
Means to prettify those hideously boring QR-codes.

Example:

![boring](qr.png "Boring!")
![not boring](pretty_qr.png "Not boring")

Requirements:

*ImageMagick* should be in installed, obviously. That is all.

Installation:

Copy *QrPimp.php* to your project folder (well, you should probably now better where to put it).  
Include in your project and transform those ugly ducklings of QR-codes into beautiful swans (no, it won't really happen, but one may dream).

Usage:

```php
<?php
require_once 'QrPimp.php';
$qr = new Imagick('qr.png');
$pimpedQR = self::magic($qrToPimp);
header('Content-Type: image/' . $pimpedQR->getImageFormat());
echo $pimpedQR;
?>
```

Notes:

* Too much magic - and your QR won't be readable at all;
* Depending on server configuration may slow down (a little) QR-code generation.
