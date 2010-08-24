<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */
 
require_once dirname(__FILE__)."/../Graph.class.php";

/**
 * Draw your objects
 *
 * @package Artichow
 */
abstract class awDriver {
	
	/**
	 * Image width
	 *
	 * @var int
	 */
	public $imageWidth;
	
	/**
	 * Image height
	 *
	 * @var int
	 */
	public $imageHeight;
	
	/**
	 * Driver X position
	 *
	 * @var int
	 */
	public $x;
	
	/**
	 * Driver Y position
	 *
	 * @var int
	 */
	public $y;
	
	/**
	 * Use anti-aliasing ?
	 *
	 * @var bool
	 */
	protected $antiAliasing = FALSE;
	
	/**
	 * The FontDriver object that will be used to draw text
	 * with PHP fonts.
	 *
	 * @var awPHPFontDriver
	 */
	protected $phpFontDriver;
	
	/**
	 * The FontDriver object that will be used to draw text
	 * with TTF or FDB fonts.
	 *
	 * @var awFileFontDriver
	 */
	protected $fileFontDriver;
	
	/**
	 * A string representing the type of the driver
	 *
	 * @var string
	 */
	protected $driverString;

	private $w;
	private $h;
	
	public function __construct() {
		$this->phpFontDriver = new awPHPFontDriver();
		$this->fileFontDriver = new awFileFontDriver();
	}

	/**
	 * Initialize the driver for a particular awImage object
	 * 
	 * @param awImage $image
	 */
	abstract public function init(awImage $image);
	
	/**
	 * Initialize the Driver for a particular FileImage object
	 * 
	 * @param awFileImage $fileImage The FileImage object to work on
	 * @param string $file Image filename
	 */
	abstract public function initFromFile(awFileImage $fileImage, $file);
	
	/**
	 * Change the image size
	 *
	 * @param int $width Image width
	 * @param int $height Image height
	 */
	abstract public function setImageSize($width, $height);
	
	/**
	 * Inform the driver of the position of your image
	 *
	 * @param float $x Position on X axis of the center of the component
	 * @param float $y Position on Y axis of the center of the component
	 */
	abstract public function setPosition($x, $y);
	
	/**
	 * Inform the driver of the position of your image
	 * This method need absolutes values
	 * 
	 * @param int $x Left-top corner X position
	 * @param int $y Left-top corner Y position
	 */
	abstract public function setAbsPosition($x, $y);
	
	/**
	 * Move the position of the image
	 *
	 * @param int $x Add this value to X axis
	 * @param int $y Add this value to Y axis
	 */
	abstract public function movePosition($x, $y);
	
	/**
	 * Inform the driver of the size of your image
	 * Height and width must be between 0 and 1.
	 *
	 * @param int $w Image width
	 * @param int $h Image height
	 * @return array Absolute width and height of the image
	 */
	abstract public function setSize($w, $h);
	
	/**
	 * Inform the driver of the size of your image
	 * You can set absolute size with this method.
	 *
	 * @param int $w Image width
	 * @param int $h Image height
	 */
	abstract public function setAbsSize($w, $h);
	
	/**
	 * Get the size of the component handled by the driver
	 *
	 * @return array Absolute width and height of the component
	 */
	abstract public function getSize();
	
	/**
	 * Turn antialiasing on or off
	 *
	 * @var bool $bool
	 */
	abstract public function setAntiAliasing($bool);
	
	/**
	 * When passed a Color object, returns the corresponding
	 * color identifier (driver dependant).
	 *
	 * @param awColor $color A Color object
	 * @return int $rgb A color identifier representing the color composed of the given RGB components
	 */
	abstract public function getColor(awColor $color);
	
	/**
	 * Draw an image here
	 *
	 * @param awImage $image Image
	 * @param int $p1 Image top-left point
	 * @param int $p2 Image bottom-right point
	 */
	abstract public function copyImage(awImage $image, awPoint $p1, awPoint $p2);
	
	/**
	 * Draw an image here
	 *
	 * @param awImage $image Image
	 * @param int $d1 Destination top-left position
	 * @param int $d2 Destination bottom-right position
	 * @param int $s1 Source top-left position
	 * @param int $s2 Source bottom-right position
	 * @param bool $resample Resample image ? (default to TRUE)
	 */
	abstract public function copyResizeImage(awImage $image, awPoint $d1, awPoint $d2, awPoint $s1, awPoint $s2, $resample = TRUE);
	
	/**
	 * Draw a string
	 *
	 * @var awText $text Text to print
	 * @param awPoint $point Draw the text at this point
	 * @param int $width Text max width
	 */
	abstract public function string(awText $text, awPoint $point, $width = NULL);
	
	/**
	 * Draw a pixel
	 *
	 * @param awColor $color Pixel color
	 * @param awPoint $p
	 */
	abstract public function point(awColor $color, awPoint $p);
	
	/**
	 * Draw a colored line
	 *
	 * @param awColor $color Line color
	 * @param awLine $line
	 * @param int $thickness Line tickness
	 */
	abstract public function line(awColor $color, awLine $line);
	
	/**
	 * Draw a color arc
	 
	 * @param awColor $color Arc color
	 * @param awPoint $center Point center
	 * @param int $width Ellipse width
	 * @param int $height Ellipse height
	 * @param int $from Start angle
	 * @param int $to End angle
	 */
	abstract public function arc(awColor $color, awPoint $center, $width, $height, $from, $to);
	
	/**
	 * Draw an arc with a background color
	 *
	 * @param awColor $color Arc background color
	 * @param awPoint $center Point center
	 * @param int $width Ellipse width
	 * @param int $height Ellipse height
	 * @param int $from Start angle
	 * @param int $to End angle
	 */
	abstract public function filledArc(awColor $color, awPoint $center, $width, $height, $from, $to);
	
	/**
	 * Draw a colored ellipse
	 *
	 * @param awColor $color Ellipse color
	 * @param awPoint $center Ellipse center
	 * @param int $width Ellipse width
	 * @param int $height Ellipse height
	 */
	abstract public function ellipse(awColor $color, awPoint $center, $width, $height);
	
	/**
	 * Draw an ellipse with a background
	 *
	 * @param mixed $background Background (can be a color or a gradient)
	 * @param awPoint $center Ellipse center
	 * @param int $width Ellipse width
	 * @param int $height Ellipse height
	 */
	abstract public function filledEllipse($background, awPoint $center, $width, $height);
	
	/**
	 * Draw a colored rectangle
	 *
	 * @param awColor $color Rectangle color
	 * @param awLine $line Rectangle diagonale
	 * @param awPoint $p2
	 */
	abstract public function rectangle(awColor $color, awLine $line);
	
	/**
	 * Draw a rectangle with a background
	 *
	 * @param mixed $background Background (can be a color or a gradient)
	 * @param awLine $line Rectangle diagonale
	 */
	abstract public function filledRectangle($background, awLine $line);
	
	/**
	 * Draw a polygon
	 *
	 * @param awColor $color Polygon color
	 * @param Polygon A polygon
	 */
	abstract public function polygon(awColor $color, awPolygon $polygon);
	
	/**
	 * Draw a polygon with a background
	 *
	 * @param mixed $background Background (can be a color or a gradient)
	 * @param Polygon A polygon
	 */
	abstract public function filledPolygon($background, awPolygon $polygon);

	/**
	 * Sends the image, as well as the correct HTTP headers, to the browser
	 *
	 * @param awImage $image The Image object to send
	 */
	abstract public function send(awImage $image);
	
	/**
	 * Get the image as binary data
	 *
	 * @param awImage $image
	 */
	abstract public function get(awImage $image);
	
	/**
	 * Return the width of some text
	 * 
	 * @param awText $text
	 */
	abstract public function getTextWidth(awText $text);
	
	/**
	 * Return the height of some text
	 * 
	 * @param awText $text
	 */
	abstract public function getTextHeight(awText $text);
	
	/**
	 * Return the string representing the type of driver
	 * 
	 * @return string
	 */
	public function getDriverString() {
		return $this->driverString;
	}
	
	/**
	 * Returns whether or not the driver is compatible with the given font type
	 * 
	 * @param awFont $font
	 * @return bool
	 */
	abstract protected function isCompatibleWithFont(awFont $font);
	
//	abstract private function drawImage(awImage $image, $return = FALSE, $header = TRUE);
	
}

registerClass('Driver', TRUE);

/**
 * Abstract class for font drivers.
 * Those are used to do all the grunt work on fonts.
 * 
 * @package Artichow
 */

abstract class awFontDriver {
	
	public function __construct() {
		
	}
	
	/**
	 * Draw the actual text.
	 * 
	 * @param awDriver $driver The Driver object to draw upon
	 * @param awText $text The Text object
	 * @param awPoint $point Where to draw the text
	 * @param float $width The width of the area containing the text
	 */
	abstract public function string(awDriver $driver, awText $text, awPoint $point, $width = NULL);
	
	/**
	 * Calculate the width of a given Text.
	 *
	 * @param awFont $font The Font used to write the text
	 * @param awText $text The Text object
	 * @param string $driverType The kind of driver used ('gd' or 'ming')
	 */
	abstract public function getTextWidth(awText $text, awDriver $driver);

	/**
	 * Calculate the height of a given Text.
	 *
	 * @param awFont $font The Font used to write the text
	 * @param awText $text The Text object
	 * @param string $driverType The kind of driver used ('gd' or 'ming')
	 */
	abstract public function getTextHeight(awText $text, awDriver $driver);
	
}

registerClass('FontDriver', TRUE);

/**
 * Class to handle calculations on PHPFont objects
 * 
 * @package Artichow
 */
class awPHPFontDriver extends awFontDriver {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function string(awDriver $driver, awText $text, awPoint $point, $width = NULL) {

		switch ($driver->getDriverString()) {
			case 'gd':
				$this->gdString($driver, $text, $point, $width);
				break;
				
			default:
				awImage::drawError('Class PHPFontDriver: Incompatibility between driver and font - You should never see this error message: have you called awDriver::isCompatibleWithFont() properly?');
				break;
			
		}
	}
	
	/**
	 * Draw a string onto a GDDriver object
	 *
	 * @param awGDDriver $driver The GDDriver to draw the text upon
	 * @param awText $text The awText object containing the string to draw
	 * @param awPoint $point Where to draw the text
	 * @param float $width The width of the text
	 */
	private function gdString(awGDDriver $driver, awText $text, awPoint $point, $width = NULL) {
		
		$angle = $text->getAngle();
		if($angle !== 90 and $angle !== 0) {
			awImage::drawError("Class PHPFontDriver: You can only use 0° and 90° angles.");
		}

		if($angle === 90) {
			$function = 'imagestringup';
			$addAngle = $this->getGDTextHeight($text);
		} else {
			$function = 'imagestring';
			$addAngle = 0;
		}

		$color = $text->getColor();
		$rgb = $driver->getColor($color);

		$textString = $text->getText();
		$textString = str_replace("\r", "", $textString);
		
		$textHeight = $this->getGDTextHeight($text);
		
		// Split text if needed
		if($width !== NULL) {

			$characters = floor($width / ($this->getGDTextWidth($text) / strlen($textString)));

			if($characters > 0) {
				$textString = wordwrap($textString, $characters, "\n", TRUE);
			}

		}
		
		$font = $text->getFont();
		$lines = explode("\n", $textString);

		foreach($lines as $i => $line) {

			// Line position handling
			if($angle === 90) {
				$addX = $i * $textHeight;
				$addY = 0;
			} else {
				$addX = 0;
				$addY = $i * $textHeight;
			}

			$function(
				$driver->resource,
				$font->font,
				$driver->x + $point->x + $addX,
				$driver->y + $point->y + $addY + $addAngle,
				$line,
				$rgb
			);

		}
	}
	
	public function getTextWidth(awText $text, awDriver $driver) {
		
		switch ($driver->getDriverString()) {
			case 'gd':		
				return $this->getGDTextWidth($text);
		
			default:
				awImage::drawError('Class PHPFontDriver: Cannot get text width - incompatibility between driver and font');
				break;
		}
		
	}
	
	public function getTextHeight(awText $text, awDriver $driver) {
		
		switch ($driver->getDriverString()) {
			case 'gd':
				return $this->getGDTextHeight($text);
				
			default:
				awImage::drawError('Class PHPFontDriver: Cannot get text height - incompatibility between driver and font');
				break;
		}
		
	}
	
	/**
	 * Return the width of a text for a GDDriver
	 *
	 * @param awText $text
	 * @return int $fontWidth
	 */
	private function getGDTextWidth(awText $text) {
		$font = $text->getFont();
		
		if($text->getAngle() === 90) {
			$text->setAngle(45);
			return $this->getGDTextHeight($text);
		} else if($text->getAngle() === 45) {
			$text->setAngle(90);
		}

		$fontWidth = imagefontwidth($font->font);

		if($fontWidth === FALSE) {
			awImage::drawError("Class PHPFontDriver: Unable to get font size.");
		}

		return (int)$fontWidth * strlen($text->getText());
	}
	
	/**
	 * Return the height of a text for a GDDriver
	 *
	 * @param awText $text
	 * @return int $fontHeight
	 */
	private function getGDTextHeight(awText $text) {
		$font = $text->getFont();
		
		if($text->getAngle() === 90) {
			$text->setAngle(45);
			return $this->getGDTextWidth($text);
		} else if($text->getAngle() === 45) {
			$text->setAngle(90);
		}

		$fontHeight = imagefontheight($font->font);

		if($fontHeight === FALSE) {
			awImage::drawError("Class PHPFontDriver: Unable to get font size.");
		}

		return (int)$fontHeight;
	}
}

registerClass('PHPFontDriver');

/**
 * Class to handle calculations on FileFont objects
 * 
 * @package Artichow
 */
class awFileFontDriver extends awFontDriver {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function string(awDriver $driver, awText $text, awPoint $point, $width = NULL) {
		
		switch ($driver->getDriverString()) {
			case 'gd':
				$this->gdString($driver, $text, $point, $width);
				break;
				
			case 'ming':
				$this->mingString($driver, $text, $point, $width);
				break;
				
			default:
				awImage::drawError('Class fileFontDriver: Incompatibility between driver and font - You should never see this error message: have you called awDriver::isCompatibleWithFont() properly?');
				break;
		}
	}
	
	/**
	 * Draw an awFileFont object on a GD ressource
	 *
	 * @param awGDDriver $driver The awGDDriver object containing the ressource to draw upon
	 * @param awText $text The awText object containing the string to draw
	 * @param awPoint $point Where to draw the string from
	 * @param float $width The width of the area containing the text
	 */
	private function gdString(awGDDriver $driver, awText $text, awPoint $point, $width = NULL) {
		// Make easier font positionment
		$text->setText($text->getText()." ");

		$font = $text->getFont();
		if($font instanceof awTTFFont === FALSE and $font->getExtension() === NULL) {
			$font->setExtension('ttf');
		}

		$box = imagettfbbox($font->size, $text->getAngle(), $font->name.'.'.$font->getExtension(), $text->getText());
		$textHeight = - $box[5];

		$box = imagettfbbox($font->size, 90, $font->name.'.'.$font->getExtension(), $text->getText());
		$textWidth = abs($box[6] - $box[2]);

		// Restore old text
		$text->setText(substr($text->getText(), 0, strlen($text->getText()) - 1));

		$textString = $text->getText();

		// Split text if needed
		if($width !== NULL) {

			$characters = floor($width / $this->getGDAverageWidth($font));
			$textString = wordwrap($textString, $characters, "\n", TRUE);

		}
		
		$color = $text->getColor();
		$rgb = $driver->getColor($color);
		
		imagettftext(
			$driver->resource,
			$font->size,
			$text->getAngle(),
			$driver->x + $point->x + $textWidth * sin($text->getAngle() / 180 * M_PI),
			$driver->y + $point->y + $textHeight,
			$rgb,
			$font->name.'.'.$font->getExtension(),
			$textString
		);
	}
	
	/**
	 * Draw an awFileFont object on a Flash movie
	 *
	 * @param awGDDriver $driver The awMingDriver object containing the movie to draw upon
	 * @param awText $text The awText object containing the string to draw
	 * @param awPoint $point Where to draw the string from
	 * @param float $width The width of the area containing the text
	 */
	private function mingString(awMingDriver $driver, awText $text, awPoint $point, $width = NULL) {

		$font = $text->getFont();
		if($font instanceof awFDBFont === FALSE and $font->getExtension() === NULL) {
			$font->setExtension('fdb');
		}
		
		list($red, $green, $blue, $alpha) = $driver->getColor($text->getColor());
		
		$fontPath = ARTICHOW_FONT.'/'.$font->name.'.'.$font->getExtension();
		$flashFont = new SWFFont($fontPath);

		$flashText = new SWFText();
		$flashText->setFont($flashFont);
		$flashText->setColor($red, $green, $blue, $alpha);
		
		// Multiply the font size by 1.33 to get the same size on screen as in GD (roughly)
		$flashText->setHeight($font->size * 1.33);
		$flashText->addString($text->getText());
		
		$item = $driver->movie->add($flashText);
		$item->rotateTo($text->getAngle());
		
		$box = awFileFontDriver::mingfdbbox(
			$font->size,
			$text->getAngle(),
			$text->getText(),
			$flashText
		);
		$textHeight = - $box[5];
		
		$box = awFileFontDriver::mingfdbbox(
			$font->size,
			90,
			$text->getText(),
			$flashText
		);
		$textWidth = abs($box[6] - $box[2]);
		
		// Reposition the text so it's in the same position than with the GD driver.
		$item->moveTo(
			$driver->x + $point->x + $textWidth * sin($text->getAngle() / 180 * M_PI),
			$driver->y + $point->y + $textHeight
		);		
	}
	
	/**
	 * Emulates the behaviour of imagettfbbox() to get the same text positionment as with
	 * the GD driver. The name should actually be mingfdbbbox(), but that's too many b's.
	 *
	 * @param float $size The font size in pixels
	 * @param float $angle Angle in degrees in which text will be measured
	 * @param string $text The string to be measured
	 * @param SWFText $flashText The Ming text object to measure
	 * @return array $box
	 */
	private static function mingfdbbox($size, $angle, $text, SWFText $flashText) {
		
		$width = $flashText->getWidth((string)$text);
		
		// The lower left corner is located at (-1, 1) with GD,
		// so it should be close enough if we use (0, 0) while avoiding a few calculations.
		$box = array(0, 0);
		
		$box[2] = cos(deg2rad($angle)) * $width;
		$box[3] = -sin(deg2rad($angle)) * $width;
		
		$box[6] = cos(deg2rad($angle + 90)) * $size;
		$box[7] = -sin(deg2rad($angle + 90)) * $size;
		
		// Angle of the line going from (0, 0) to the upper right corner
		$hyp = sqrt($width * $width + $size * $size);
		$angleOffset = acos($width / $hyp);
		
		$box[4] = cos($angle * M_PI / 180 + $angleOffset) * $hyp;
		$box[5] = -sin($angle * M_PI / 180 + $angleOffset) * $hyp;
		
		return $box;
		
	}
		
	public function getTextWidth(awText $text, awDriver $driver) {
		switch ($driver->getDriverString()) {
			case 'gd':
				return $this->getGDTextWidth($text);
				
			case 'ming':
				return $this->getMingTextWidth($text);
				
			default:
				awImage::drawError('Class FileFontDriver: Cannot get text width - incompatibility between driver and font');
				break;
		}
	}
	
	public function getTextHeight(awText $text, awDriver $driver) {
		switch ($driver->getDriverString()) {
			case 'gd':
				return $this->getGDTextHeight($text);
				
			case 'ming':
				return $this->getMingTextHeight($text);
				
			default:
				awImage::drawError('Class FileFontDriver: Cannot get text height - incompatibility between driver and font');
				break;
		}
	}
	
	private function getGDTextWidth(awText $text) {
		$font = $text->getFont();
		if($font->getExtension() === NULL) {
			$font->setExtension('ttf');
		}
		
		$box = imagettfbbox($font->size, $text->getAngle(), $font->name.'.'.$font->getExtension(), $text->getText());

		if($box === FALSE) {
			awImage::drawError("Class FileFontDriver: Unable to get font width (GD).");
		}

		list(, , $x2, , , , $x1, ) = $box;

		return abs($x2 - $x1);
	}
	
	private function getGDTextHeight(awText $text) {
		$font = $text->getFont();
		if($font->getExtension() === NULL) {
			$font->setExtension('ttf');
		}
		
		$box = imagettfbbox($font->size, $text->getAngle(), $font->name.'.'.$font->getExtension(), $text->getText());

		if($box === FALSE) {
			awImage::drawError("Class FileFontDriver: Unable to get font height (GD).");
		}

		list(, , , $y2, , , , $y1) = $box;

		return abs($y2 - $y1);
	}
	
	private function getGDAverageWidth(awFileFont $font) {

		$text = "azertyuiopqsdfghjklmmmmmmmwxcvbbbn,;:!?.";

		$box = imagettfbbox($font->size, 0, $font->font.'.'.$font->getExtension(), $text);

		if($box === FALSE) {
			awImage::drawError("Class FileFontDriver: Unable to get font average width.");
		}

		list(, , $x2, $y2, , , $x1, $y1) = $box;

		return abs($x2 - $x1) / strlen($text);

	}
	
	private function getMingTextHeight(awText $text) {
		$font = $text->getFont();
		if($font->getExtension() === NULL) {
			$font->setExtension('fdb');
		}
		
		$flashFont = new SWFFont(ARTICHOW_FONT.'/'.$font->name.'.'.$font->getExtension());
		$flashText = new SWFText();
		$flashText->setFont($flashFont);
		
		return $flashText->getAscent($text->getText());
	}
	
	private function getMingTextWidth(awText $text) {
		$font = $text->getFont();
		if($font->getExtension() === NULL) {
			$font->setExtension('fdb');
		}
		
		$flashFont = new SWFFont(ARTICHOW_FONT.'/'.$font->name.'.'.$font->getExtension());
		$flashText = new SWFText();
		$flashText->setFont($flashFont);
		
		return $flashText->getWidth($text->getText());
	}
	
}

registerClass('FileFontDriver');

// Include ARTICHOW_DRIVER by default to preserve backward compatibility.
require_once dirname(__FILE__).'/drivers/'.ARTICHOW_DRIVER.'.class.php';

?>