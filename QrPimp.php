<?php

/**
 * Static toolkit to make those pesky QR-codes a little less hurtfult to the eyes.
 * However, one should mind readability of the resulting semi-artistic blots. 
 * Note, that low contrast and shading degrade QR-recognition beyond belied.
 * 
 * @author Artiom Basenko
 * @version 0.5
 * @copyright GPL
 */
class QrPimp {

  # Use one of those, good sir! #

  /**
   * Make QR-code look good. Simple.
   * Quick and clean, no configuration required.
   * Each new resulting QR-code will vary in appearance.
   *
   * @param Imagick qr Plain and boring QR-code.
   * @param bool readable Try to make the result valid (semi)readable.
   * @return Imagick Your shiny QR-code.
   */
  public static function magic(Imagick $qr, $readable = false) {
	  return self::process($qr, array('smooth',
									  'sharpen',
									  'filter',
									  'colorize' => self::$colors,
  									 )
						  );
  }
  
  /**
   * Prettify ImageMagick QR-code.
   * Various options are available (params are optional):
   * + smooth: amount
   * + colorize: array(color[s])
   * + sharpen: amount
   * + logo: path
   * + filter: name
   * + gradient: type
   * + template: path
   * + ...
   *
   * In case only option name is specified - will asume that you don't care.
   * If you don't want to use something - simply skip it.
   *
   * @param Imagick qr Nasty looking generic QR-code.
   * @param array todo List of options. If null - will perform the highest possible level of magic.
   * @return Imagick Your QR-code, neatly prettified.
   */
  public static function style(Imagick $qr, array $todo = null) {
  	  if(is_null($todo))
  	  	$todo = array('smooth',
  					  'sharpen',
					  'filter',
					  'colorize',
					  'gradient',
				     );
	  return self::process($qr, $todo);
  }

  /**
   * Multiple at once!
   *
   * @param string path Path to file|folder with (an awful lot of) QR-codes.
   * @param string pretties Path to save the handsome ones (if nothing, will owerwrite the originals).
   */
  public static function batch(string $path, string $pretties = null) {
  }

  # Private stuff, please look no further! #

  /**
   * Say hello to our ugly QR-code.
   */
  private static $qr = null;

  /**
   * Some measurements, just to sate curiosity.
   */
  private static $time = null;

  /**
   * Magic values and numbers.
   */
  private static $conf = array( // Smoothings
  	  							'median' => 4,
								'gauss' => 1,
								'pass' => 2,
								'sharp_r' => 10,
								'sharp_s' => 3,
								// Filters
								'shade_a' => 45,
								'shade_z' => 45,
								// Colors range
								'colors_l' => 50,
								'colors_h' => 255,
								// Gradient
							  );

	/**
	 * Nice colors for high-contrast QR-codes.
	 */
	private static $colors = array(
									'#2F9CC0',
									'#18393E',
									'#581936',
									'#470C1F',
									'#232D58',
									'#000000',
								);

  /**
   * Work magic on those bland images.
   * @return Imagick Pretty image of something (actually, it's QR-code).
   */
  private static function process(Imagick $qr, array $options) {
  	self::$qr = $qr;
	self::init();
    foreach($options as $opt => $param) {
	  // In case of mixed config.
	  if(is_numeric($opt)) {
	  	$opt = $param;
	  	$param = true;
	  }
      switch($opt) {
		case 'smooth': 		self::smooth($param);		break;
		case 'colorize': 	self::colorize($param); 	break;
		case 'sharpen': 	self::sharpen($param); 		break;
		case 'filter': 		self::filter($param); 		break;
		case 'logo': 		self::logo($param); 		break;
		case 'gradient': 	self::gradient($param);		break;
        default: break;
      }
	}

	return self::$qr;
  }
  
  /**
   * Check, if Imagick is even in system.
   * Curse the hapless user if not.
   */
  private static function init() {
    class_exists('Imagick') or die('Oh noes! Do install Imagick, we implore you.');
  }

  /**
   * Apply gaussian blur | median filters in multiple passes.
   */
  private static function smooth() {
  	// Suave edges
	self::$qr->medianFilterImage(self::$conf['median']);
	// Multipass smooth
	$pass = self::$conf['pass'];
	while($pass--)
		self::$qr->gaussianBlurImage(self::$conf['gauss'], 
									 self::$conf['gauss']);
  }

  /**
   * Sharpen the muddy image.
   */
   private static function sharpen() {
	// Crispness
	self::$qr->sharpenImage(self::$conf['sharp_r'], 
							self::$conf['sharp_s']);
   }

  /**
   * Add some colors!
   */
  private static function colorize($param) {
  	if(isset($param))
  		if(is_array($param) and !empty($param))
			$color = new ImagickPixel($param[array_rand($param, 1)]);

	if(!isset($color))
		$color = new ImagickPixel(self::rgb());

	self::$qr->colorizeImage($color, 0.1);
  }

  /**
   * Gradient is the way to go.
   */
  private static function gradient() {
	$gradient = new Imagick();
	$gradient->newPseudoImage(self::$qr->width, self::$qr->height,
								'radial-gradient:white-transparent');
	self::$qr->compositeImage($gradient, Imagick::COMPOSITE_DISSOLVE, 0.0, 0.0);
  }

  /**
   * TODO: Include some fancy (probably not) logo picture.
   */
  private static function logo() {
  }

  /**
   * TODO: Use background template.
   */
  private static function template() {
  }

  /**
   * Some neat filters (no, really).
   */
  private static function filter() {
	self::$qr->shadeImage(false, self::$conf['shade_a'], 
								 self::$conf['shade_z']);
  }

  # Even I forgot those were here! #
  
  /**
   * Generate random RGB color as Imagick compatible string.
   */
  private static function rgb() {
	return 'rgb(' . implode(',', array( 
						mt_rand(self::$conf['colors_l'], self::$conf['colors_h']), 
						mt_rand(self::$conf['colors_l'], self::$conf['colors_h']), 
						mt_rand(self::$conf['colors_l'], self::$conf['colors_h'])))
			. ')';
  }

  /**
   * TODO: Check, if resulting QR-code is a readable one.
   */
  private static function crc() {
  }

  # And now for the tests! #

  /**
   * Perform some check-ups|hick-ups.
   */
  public static function test() {
	ini_set('display_errors', 'On');
    echo 'OK, ready to rumble!<br />';
    self::init();
    try {
		$qrToPimp = new Imagick('img/qr.png');
		$qrToStyle = new Imagick('img/qr.png');
		echo 'Processing QR...<br />';
		uPro::go();
		$pimpedQR = self::magic($qrToPimp);
		$pimpedTime = uPro::fin();
		uPro::go();
		$stylishQR = self::style($qrToStyle);
		$stylishTime = uPro::fin();
		echo 'This is random magic (took ' . 
					$pimpedTime . ' s):<br />';
		echo "<img src='data:png;base64," . 
					base64_encode($pimpedQR) . "' alt='qr' />";
		echo '<hr />';
		echo 'This is default styling (took ' . 
					$stylishTime . ' s):<br />';
		echo "<img src='data:png;base64," . 
					base64_encode($stylishQR) . "' alt='qr' />";
	} catch(ImagickException $e) {
		echo 'Alas, something happened: ', $e;
	}
  }
}

/**
 * Microprofiler utility.
 * Measure execution time in a simply fashion.
 */
class uPro {
	private static $startTime = 0;
	private static $endTime = 0;
	private static $totalTime = 0;

	/**
	 * Start measuring execution time.
	 */
	public static function go() { self::$startTime = microtime(true);  }

	/**
	 * Finish measuring.
	 * @return string Execution time in seconds.
	 */
	public static function fin() { return microtime(true) - self::$startTime; }
}

// Uncomment for testing.
//QrPimp::test();

?>
