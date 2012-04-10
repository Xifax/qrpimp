QrPimp
----
_Means to prettify those hideously boring QR-codes._

Example:

![Boring QR](http://github.com/Xifax/qrpimp/raw/master/img/qr.png "Boring!")
![Not boring](http://github.com/Xifax/qrpimp/raw/master/img/pretty_qr_black.png "Not boring")

Requirements:

*ImageMagick* should be in installed, obviously. That is all.

Installation:

Copy *QrPimp.php* to your project folder (well, you should probably now better where to put it).
Include it in your project and transform those ugly ducklings of QR-codes into beautiful swans (no, it won't really happen, but one may dream).

Usage:

```php
<?php
require_once 'QrPimp.php';
// Load boring QR-code
$pimpedQR = QrPimp::magic(new Imagick('img/qr.png'));
// Display shiny one
header('Content-Type: image/' . $pimpedQR->getImageFormat());
echo $pimpedQR;
?>
```

Customization:

To be explained. For now, consult comments for method `QrPimp::style()`

Notes:

* Too much magic, and your QR won't be readable at all;
* Depending on server configuration may slow down (a little) QR-code generation.
