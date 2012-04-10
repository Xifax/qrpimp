<?php

/**
 * Static toolkit to make those pesky QR-codes a little less hurtfult to the eyes.
 * However, one should mind readability of the resulting semi-artistic blots. 
 * Note, that low contrast and shading degrade QR-recognition rate beyond belief.
 * 
 * @author Artiom Basenko
 * @version 0.7
 * @copyright GPL
 *
 * TODO: fix tabulation
 * TODO: advanced customization
 * TODO: template and logo
 * TODO: play with hexdec
 */
class QrPimp {

  # Use one of those, good sir! #

  /**
   * Make QR-code look good. As simple as it gets.
   * Quick and clean, no configuration required.
   * Each new resulting QR-code will vary in appearance.
   *
   * @param Imagick $qr Plain and boring QR-code.
   * @return Imagick Your shiny QR-code.
   */
  public static function magic(Imagick $qr) {
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
   * @param Imagick $qr Nasty looking generic QR-code.
   * @param array $todo List of options. If null - will perform the highest possible level of magic.
   * @param bool $readable Try to make the result valid (semi)readable.
   * @return Imagick Your QR-code, neatly prettified.
   */
  public static function style(Imagick $qr, array $todo = null, $readable = true) {
  	  if(is_null($todo))
  	  	$todo = array('smooth',
  					  'sharpen',
					  'filter',
					  'colorize',
					  //'gradient',
				     );
	  return self::process($qr, $todo);
  }

  /**
   * Multiple at once!
   *
   * @param string $path Path to file|folder with (an awful lot of) QR-codes.
   * @param string $pretties Path to save the handsome ones (if nothing, will owerwrite the originals).
   */
  public static function batch($path, $pretties = null) {
  	  if(file_exists($path)) {
		try {
			// Process all images in directory
			if(is_dir($path)) {
				$files = glob($path . '/*.{jpg,png,gif}', GLOB_BRACE);
				foreach($files as $file) {
					$pimped = self::magic(new Imagick($file));
					if(is_null($pretties))
						$pimped->writeImage();
					else {
						if(!file_exists($pretties))
							mkdir($pretties);
						$pimped->writeImage($pretties . '/' . basename($file));
					}
					$pimped->clear();
					$pimped->destroy();
				}
			// Just this one file
			} else {
				$pimped = self::magic(new Imagick($path));
				if(is_null($pretties))
					$pimped->writeImage();
				else
					$pimped->writeImage($pretties);
				$pimped->clear();
				$pimped->destroy();
			}
		} catch(ImagickException $e) {
			throw new Exception('Could not process some of the files: ' . $e);
		}
	  } else throw new Exception('Specified path "' . $path . '" does not exist, sorry.');
  }

  /**
   * Accumulated statistics.
   * @return array Transformation times for all QR-codes processed.
   */
  public static function stats() { return self::$times; }

  # Private stuff, please look no further! #

  /**
   * Say hello to our ugly QR-code.
   */
  private static $qr = null;

  /**
   * Some measurements, just to sate curiosity.
   */
  private static $times = array();

  /**
   * Magic values and numbers.
   */
  private static $conf = array( // Smoothings (gauss blur + median)
  	  							'median' => 4, 		// smooth x1
								'gauss' => 1, 		// smooth x2
								'pass' => 2, 		// gauss only
								'sharp_r' => 10, 	// radius
								'sharp_s' => 3, 	// sigma
								// Filters (shading, pseudo-3d)
								'shade_a' => 45, 	// azimuth
								'shade_z' => 45, 	// elevation
								// Colors range (mind the brightness, the contrast!)
								'colors_l' => 30, 	// lower
								'colors_h' => 190, 	// high
								// Gradients (radial, vertical)
								'radial' => 'radial-gradient',
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
									// TODO: more!
								);

  /**
   * Work magic on those bland images.
   * @param Imagick $qr QR-code image.
   * @param array $options What to do.
   * @return Imagick Pretty image of something (actually, it's QR-code).
   */
  private static function process(Imagick $qr, array $options) {
  	self::$qr = $qr;
	self::init();
	uPro::go();
    foreach($options as $opt => $param) {
	  // In the case of mixed config.
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
	self::$times[] = uPro::fin();

	return self::$qr;
  }
  
  /**
   * Check, if Imagick is even in system.
   * Curse the hapless user if not.
   */
  private static function init() {
    class_exists('Imagick') or die('Oh noes! Do install Imagick, we implore you (honest).');
  }

  /**
   * Apply gaussian blur | median filters in multiple passes.
   */
  private static function smooth($param) {
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
   private static function sharpen($param) {
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
  private static function gradient($param) {
	$gradient = new Imagick();
	$gradient->newPseudoImage(self::$qr->width, self::$qr->height,
								'radial-gradient:white-transparent');
	self::$qr->compositeImage($gradient, Imagick::COMPOSITE_DISSOLVE, 0.0, 0.0);
  }

  /**
   * TODO: Include some fancy (probably not) logo picture.
   */
  private static function logo($param) {
  }

  /**
   * TODO: Use background template.
   */
  private static function template($param) {
  }

  /**
   * Some neat filters (no, really).
   */
  private static function filter($param) {
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
   * Beware, some especially ugly code right ahead.
   * @param string $type Test to perform: single | batch | palette .
   */
  public static function test($type = 'single') {
	ini_set('display_errors', 'On');
    set_time_limit(200);
	echo '<hr />';
    //echo 'OK, ready to rumble!<br />';
    self::init();
    try {
		switch($type) {
			// Single (lonely) QR - if it even works and looks good.
			case 'single':
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
				echo 'This is default styling with random colors (took ' . 
							$stylishTime . ' s):<br />';
				echo "<img src='data:png;base64," . 
							base64_encode($stylishQR) . "' alt='qr' />";
				break;
			// Multilple passes to grasp the idea of average perfomance.
			case 'batch':
				$iterations = 25;
				while(--$iterations > 0)
					self::magic(new Imagick('img/qr.png'));
				echo 'Transformation times: <br />' . implode('<br />', self::stats()) . '<br />';
				echo 'Total: ' . array_sum(self::stats()) . '<br />';
				echo 'Average: ' . array_sum(self::stats()) / count(self::stats());
				break;
			// Plenty of colorful QR-codes to test readability (and aesthetics).
			case 'palette':
				$rows = $columns = 5;
				echo '<table>';
				for($row=0; $row < $rows; $row++) {
					echo '<tr>';
					for($column=0; $column < $columns; $column++)
						echo "<td><img src='data:png;base64," . 
									base64_encode(self::style(new Imagick('img/qr.png'))) . "' alt='qr' /></td>";
					echo "</tr>";
				}
				echo '</table>';
				break;
			default: break;
		}
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
	private static $startTime, $endTime, $totalTime = 0;

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
//QrPimp::batch('batch', 'prettified');

?>
