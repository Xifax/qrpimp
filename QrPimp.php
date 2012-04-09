<?php

/**
 * Static toolkit to make those pesky QR-codes a little less hurtfult to the eyes.
 * However, one should mind readability of the resulting semi-artistic blots. 
 */
class QrPimp {

  # Use one of those, good sir! #

  /**
   * Make QR-code look good. Simple.
   * Quick and dirty, no configuration required.
   * @param Imagick qr Plain and boring QR-code.
   * @return Imagick Your shiny QR-code.
   */
  public static boost(Imagick qr) {
  }
  
  /**
   * Prettify ImageMagick QR-code.
   * Various options are available.
   * @param Imagick qr Nasty looking generic QR-code.
   * @return Imagick Your QR-code, neatly prettified.
   */
  public static style(Imagick qr, array todo) {
  }

  # Private stuff, please don't look! #
  
  /**
   * Check, if Imagick is even in system.
   * Curse the hapless user if not (throw exception).
   * @throws Exception
   */
  private static init() {
    class_exists('Imagick') or throw new Exception('Oh noes! Do install Imagick, we implore you.');
  }

  private static smooth() {
  }

  private static rgb() {
  }

  private static colorize() {
  }

  private static filter() {
  }

  private static crc() {
  }
}

?>
