<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */
 
require_once dirname(__FILE__)."/../Driver.class.php";

/**
 * Draw your objects
 *
 * @package Artichow
 */
class awMingDriver extends awDriver {
	
	/**
	 * The Flash movie
	 *
	 * @var $movie
	 */
	var $movie;
	
	 function awMingDriver() {
		
		parent::awDriver();
		
		$this->driverString = 'ming';
		
		// Nice defaults
		ming_setScale(20.0);
		ming_useswfversion(6);

	}
	
	/**
	 * Initialize the driver for a particular awImage object
	 * 
	 * @param &$image
	 */
	 function init(&$image) {

		if($this->movie === NULL) {
			$this->setImageSize($image->width, $image->height);
			
			// Create movie
			$this->movie = new SWFMovie();
			if(!$this->movie) {
				awImage::drawError("Class Image: Unable to create a graph.");
			}
			
			$this->movie->setDimension($image->width, $image->height);
			
			$this->setAntiAliasing($image->getAntiAliasing());
			
			// Original color
			$this->filledRectangle(
				new awWhite,
				new awLine(
					new awPoint(0, 0),
					new awPoint($this->imageWidth, $this->imageHeight)
				)
			);
			
			$shadow = $image->shadow;
			if($shadow !== NULL) {
				$shadow = $shadow->getSpace();
				$p1 = new awPoint($shadow->left, $shadow->top);
				$p2 = new awPoint($this->imageWidth - $shadow->right - 1, $this->imageHeight - $shadow->bottom - 1);
				
				// Draw image background
				$this->filledRectangle($image->getBackground(), new awLine($p1, $p2));
				
				// Draw image border
				$image->border->rectangle($this, $p1, $p2);
			}
		}
	}
	
	/**
	 * Initialize the Driver for a particular FileImage object
	 * 
	 * @param &$fileImage The FileImage object to work on
	 * @param string $file Image filename
	 */
	 function initFromFile(&$fileImage, $file) {
		
	}
	
	/**
	 * Change the image size
	 *
	 * @param int $width Image width
	 * @param int $height Image height
	 */
	 function setImageSize($width, $height) {
		$this->imageWidth = $width;
		$this->imageHeight = $height;
	}
	
	/**
	 * Inform the driver of the position of your image
	 *
	 * @param float $x Position on X axis of the center of the component
	 * @param float $y Position on Y axis of the center of the component
	 */
	 function setPosition($x, $y) {
		// Calculate absolute position
		$this->x = round($x * $this->imageWidth - $this->w / 2);
		$this->y = round($y * $this->imageHeight - $this->h / 2);
	}
	
	/**
	 * Inform the driver of the position of your image
	 * This method need absolutes values
	 * 
	 * @param int $x Left-top corner X position
	 * @param int $y Left-top corner Y position
	 */
	 function setAbsPosition($x, $y) {
		$this->x = $x;
		$this->y = $y;
	}
	
	/**
	 * Move the position of the image
	 *
	 * @param int $x Add this value to X axis
	 * @param int $y Add this value to Y axis
	 */
	 function movePosition($x, $y) {
		$this->x += (int)$x;
		$this->y += (int)$y;
	}
	
	/**
	 * Inform the driver of the size of your image
	 * Height and width must be between 0 and 1.
	 *
	 * @param int $w Image width
	 * @param int $h Image height
	 * @return array Absolute width and height of the image
	 */
	 function setSize($w, $h) {
		
		// Calcul absolute size
		$this->w = round($w * $this->imageWidth);
		$this->h = round($h * $this->imageHeight);
		
		return $this->getSize();
		
	}
	
	/**
	 * Inform the driver of the size of your image
	 * You can set absolute size with this method.
	 *
	 * @param int $w Image width
	 * @param int $h Image height
	 */
	 function setAbsSize($w, $h) {
		$this->w = $w;
		$this->h = $h;
		
		return $this->getSize();
	}
	
	/**
	 * Get the size of the component handled by the driver
	 *
	 * @return array Absolute width and height of the component
	 */
	 function getSize() {
		return array($this->w, $this->h);
	}
	
	/**
	 * Turn antialiasing on or off
	 *
	 * @var bool $bool
	 */
	 function setAntiAliasing($bool) {
		if($this->movie !== NULL) {

			$actionscript = '
			_quality = "%s";
			';

			if((bool)$bool) {
				$actionscript = sprintf($actionscript, 'high');
			} else {
				$actionscript = sprintf($actionscript, 'low');
			}
			
			$this->movie->add(new SWFAction(str_replace("\r", "", $actionscript)));
		}
	}
	
	/**
	 * When passed a Color object, returns the corresponding
	 * color identifier (driver dependant).
	 *
	 * @param $color A Color object
	 * @return array $rgba A color identifier representing the color composed of the given RGB components
	 */
	 function getColor($color) {
		
		// Ming simply works with R, G, B and Alpha values.
		list($red, $green, $blue, $alpha) = $color->rgba();
		
		// However, the Ming alpha channel ranges from 255 (opaque) to 0 (transparent),
		// while the awColor alpha channel ranges from 0 (opaque) to 100 (transparent).
		// First, we convert from 0-100 to 0-255.
		$alpha = (int)($alpha * 255 / 100);
		
		// Then from 0-255 to 255-0.
		$alpha = abs($alpha - 255);
		
		return array($red, $green, $blue, $alpha);
	}
	
	/**
	 * Draw an image here
	 *
	 * @param &$image Image
	 * @param int $p1 Image top-left point
	 * @param int $p2 Image bottom-right point
	 */
	 function copyImage(&$image, $p1, $p2) {
		
	}
	
	/**
	 * Draw an image here
	 *
	 * @param &$image Image
	 * @param int $d1 Destination top-left position
	 * @param int $d2 Destination bottom-right position
	 * @param int $s1 Source top-left position
	 * @param int $s2 Source bottom-right position
	 * @param bool $resample Resample image ? (default to TRUE)
	 */
	 function copyResizeImage(&$image, $d1, $d2, $s1, $s2, $resample = TRUE) {
		
	}
	
	/**
	 * Draw a string
	 *
	 * @var &$text Text to print
	 * @param $point Draw the text at this point
	 * @param int $width Text max width
	 */
	 function string(&$text, $point, $width = NULL) {
		$font = $text->getFont();
		
		// Can we deal with that font?
		if($this->isCompatibleWithFont($font) === FALSE) {
			awImage::drawError('Class MingDriver: Incompatible font type (\''.get_class($font).'\')');
		}
		
		// Ming can only work with awFileFont objects for now
		// (i.e. awFDBFont, or awTuffy et al.)
		$fontDriver = $this->fileFontDriver;
		
		if($text->getBackground() !== NULL or $text->border->visible()) {
		
			list($left, $right, $top, $bottom) = $text->getPadding();

			$textWidth = $fontDriver->getTextWidth($text, $this);
			$textHeight = $fontDriver->getTextHeight($text, $this);
			
			$x1 = floor($point->x - $left);
			$y1 = floor($point->y - $top);
			$x2 = $x1 + $textWidth + $left + $right;
			$y2 = $y1 + $textHeight + $top + $bottom;
			
			$this->filledRectangle(
				$text->getBackground(),
				awLine::build($x1, $y1, $x2, $y2)
			);
			
			$text->border->rectangle(
				$this,
				new awPoint($x1 - 1, $y1 - 1),
				new awPoint($x2 + 1, $y2 + 1)
			);
			
		}
		
		$fontDriver->string($this, $text, $point, $width);
	}
	
	/**
	 * Draw a pixel
	 *
	 * @param $color Pixel color
	 * @param $p
	 */
	 function point($color, $p) {
		if($p->isHidden() === FALSE) {
			list($red, $green, $blue, $alpha) = $this->getColor($color);
			
			$point = new SWFShape();
			$point->setLine(1, $red, $green, $blue, $alpha);
			$point->movePenTo($this->x + round($p->x), $this->y + round($p->y));
			$point->drawLine(0.5, 0.5);
			$point->movePen(-0.5, 0);
			$point->drawLine(0.5, -0.5);
			
			$this->movie->add($point);
		}
	}
	
	/**
	 * Draw a colored line
	 *
	 * @param $color Line color
	 * @param $line
	 * @param int $thickness Line tickness
	 */
	 function line($color, $line) {
		if($line->getThickness() > 0 and $line->isHidden() === FALSE) {
	
			list($red, $green, $blue, $alpha) = $this->getColor($color);

			$mingLine = new SWFShape();
			$mingLine->setLine($line->getThickness(), $red, $green, $blue, $alpha);

			list($p1, $p2) = $line->getLocation();
			
			$mingLine->movePenTo($this->x + round($p1->x), $this->y + round($p1->y));

			switch($line->getStyle()) {
			
				case LINE_SOLID :
					$mingLine->drawLineTo($this->x + round($p2->x), $this->y + round($p2->y));
					$this->movie->add($mingLine);
					break;
					
				case LINE_DOTTED :
					$size = sqrt(pow($p2->y - $p1->y, 2) + pow($p2->x - $p1->x, 2));
					$cos = ($p2->x - $p1->x) / $size;
					$sin = ($p2->y - $p1->y) / $size;
					
					for($i = 0; $i <= $size; $i += 2) {
						$p = new awPoint(
							round($i * $cos + $p1->x),
							round($i * $sin + $p1->y)
						);
						$this->point($color, $p);
					}
					
					break;
					
				case LINE_DASHED :
					$width = $p2->x - $p1->x;
					$height = $p2->y - $p1->y;
					$size = sqrt(pow($height, 2) + pow($width, 2));
					
					if($size == 0) {
						return;
					}
					
					$cos = $width / $size;
					$sin = $height / $size;
					
					$functionX = ($width  > 0) ? 'min' : 'max';
					$functionY = ($height > 0) ? 'min' : 'max';
					
					for($i = 0; $i <= $size; $i += 6) {
						
						$t1 = new awPoint(
							round($i * $cos + $p1->x),
							round($i * $sin + $p1->y)
						);
						
						$t2 = new awPoint(
							round($functionX(($i + 3) * $cos, $width) + $p1->x),
							round($functionY(($i + 3) * $sin, $height) + $p1->y)
						);
						
						$this->line($color, new awLine($t1, $t2));
						
					}
					
					break;
			
			}
		
		}
	
	}
	
	/**
	 * Draw a color arc
	 
	 * @param $color Arc color
	 * @param $center Point center
	 * @param int $width Ellipse width
	 * @param int $height Ellipse height
	 * @param int $from Start angle
	 * @param int $to End angle
	 */
	 function arc($color, $center, $width, $height, $from, $to) {
		
	}
	
	/**
	 * Draw an arc with a background color
	 *
	 * @param $color Arc background color
	 * @param $center Point center
	 * @param int $width Ellipse width
	 * @param int $height Ellipse height
	 * @param int $from Start angle
	 * @param int $to End angle
	 */
	 function filledArc($color, $center, $width, $height, $from, $to) {
		
	}
	
	/**
	 * Draw a colored ellipse
	 *
	 * @param $color Ellipse color
	 * @param $center Ellipse center
	 * @param int $width Ellipse width
	 * @param int $height Ellipse height
	 */
	 function ellipse($color, $center, $width, $height) {
		
	}
	
	/**
	 * Draw an ellipse with a background
	 *
	 * @param mixed $background Background (can be a color or a gradient)
	 * @param $center Ellipse center
	 * @param int $width Ellipse width
	 * @param int $height Ellipse height
	 */
	 function filledEllipse($background, $center, $width, $height) {
		
	}
	
	/**
	 * Draw a colored rectangle
	 *
	 * @param $color Rectangle color
	 * @param $line Rectangle diagonale
	 * @param $p2
	 */
	 function rectangle($color, $line) {
		list($p1, $p2) = $line->getLocation();
		
		// Get Red, Green, Blue and Alpha values for the line
		list($r, $g, $b, $a) = $this->getColor($color);
		
		// Calculate the coordinates of the two other points of the rectangle
		$p3 = new Point($p1->x, $p2->y);
		$p4 = new Point($p2->x, $p1->y);
		
		
		
		$side = new Line($p1, $p2);
		
		
		// Draw the four sides of the rectangle, clockwise
		if(
			($p1->x <= $p2->x and $p1->y <= $p2->y)
			or
			($p1->x >= $p2->x and $p1->y >= $p2->y)
		) {
			$side->setLocation($p1, $p4);
			$this->line($color, $side);
			
			$side->setLocation($p4, $p2);
			$this->line($color, $side);
			
			$side->setLocation($p2, $p3);
			$this->line($color, $side);
			
			$side->setLocation($p3, $p1);
			$this->line($color, $side);
		} else {
			$side->setLocation($p1, $p3);
			$this->line($color, $side);
			
			$side->setLocation($p3, $p2);
			$this->line($color, $side);
			
			$side->setLocation($p2, $p4);
			$this->line($color, $side);
			
			$side->setLocation($p4, $p1);
			$this->line($color, $side);
		}
	}
	
	/**
	 * Draw a rectangle with a background
	 *
	 * @param mixed $background Background (can be a color or a gradient)
	 * @param $line Rectangle diagonale
	 */
	 function filledRectangle($background, $line) {
		list($p1, $p2) = $line->getLocation();
		
		// Common shape settings
		$shape = new SWFShape();
		$shape->setLine(0);
		
		if(is_a($background, 'awColor')) {
			
			// Get the Red, Green, Blue and Alpha values
			list($r, $g, $b, $a) = $this->getColor($background);
			$shape->setRightFill($r, $g, $b, $a);
			
		} else if(is_a($background, 'awGradient')) {
			
			// Get the Gradient object as an SWFGradient one
			list($flashGradient, $style) = $this->getGradient($background);
			
			$fill = $shape->addFill($flashGradient, $style);
			
			// Angles between Artichow and Ming don't match.
			// Don't use abs() or vertical gradients get inverted.
			$angle = $background->angle - 90;
			$fill->rotateTo($angle);
			
			// Move the gradient based on the position of the rectangle we're drawing
			$centerX = min($p1->x, $p2->y) + abs($p1->x - $p2->x) / 2;
			$centerY = min($p1->y, $p2->y) + abs($p1->y - $p2->y) / 2;
			$fill->moveTo($centerX, $centerY);
			
			// Ming draws its gradients on a 1600x1600 image,
			// so we have to resize it.
			if($angle === -90) {
				$ratio = abs($p1->y - $p2->y) / 1600;
			} else {
				$ratio = abs($p1->x - $p2->x) / 1600;
			}
			$fill->scaleTo($ratio);
			
			$shape->setRightFill($fill);
			
		}
		
		// Set starting position
		$shape->movePenTo($this->x + round($p1->x), $this->y + round($p1->y));
		
		// Depending on the points' relative positions,
		// we have two drawing possibilities
		if(
			($p1->x <= $p2->x and $p1->y <= $p2->y)
			or
			($p1->x >= $p2->x and $p1->y >= $p2->y)
		) {
			$shape->drawLineTo($this->x + round($p2->x), $this->y + round($p1->y));
			$shape->drawLineTo($this->x + round($p2->x), $this->y + round($p2->y));
			$shape->drawLineTo($this->x + round($p1->x), $this->y + round($p2->y));
			$shape->drawLineTo($this->x + round($p1->x), $this->y + round($p1->y));
		} else {
			$shape->drawLineTo($this->x + round($p1->x), $this->y + round($p2->y));
			$shape->drawLineTo($this->x + round($p2->x), $this->y + round($p2->y));
			$shape->drawLineTo($this->x + round($p2->x), $this->y + round($p1->y));
			$shape->drawLineTo($this->x + round($p1->x), $this->y + round($p1->y));
		}
		
		$this->movie->add($shape);
	}
	
	/**
	 * Draw a polygon
	 *
	 * @param $color Polygon color
	 * @param Polygon A polygon
	 */
	 function polygon($color, &$polygon) {
		$points = $polygon->all();
		$count = count($points);
		
		if($count > 1) {
			
			$side = new awLine;
			$side->setStyle($polygon->getStyle());
			$side->setThickness($polygon->getThickness());
			
			$prev = $points[0];
			
			for($i = 1; $i < $count; $i++) {
				$current = $points[$i];
				$side->setLocation($prev, $current);
				$this->line($color, $side);
				$prev = $current;
			}
			
			// Close the polygon
			$side->setLocation($prev, $points[0]);
			$this->line($color, $side);
		}
	}
	
	/**
	 * Draw a polygon with a background
	 *
	 * @param mixed $background Background (can be a color or a gradient)
	 * @param Polygon A polygon
	 */
	 function filledPolygon($background, &$polygon) {
		$shape = new SWFShape();
		
		if(is_a($background, 'awColor')) {
			list($red, $green, $blue, $alpha) = $this->getColor($background);
			
			$shape->setRightFill($red, $green, $blue, $alpha);
		} elseif(is_a($background, 'awGradient')) {
			list($flashGradient, $style) = $this->getGradient($background);
			
			$fill = $shape->addFill($flashGradient, $style);
			
			list($xMin, $xMax) = $polygon->getBoxXRange();
			list($yMin, $yMax) = $polygon->getBoxYRange();
			
			if($background->angle === 0) {
				$fill->scaleTo(($yMax - $yMin) / 1600);
			} else {
				$fill->scaleTo(($xMax - $xMin) / 1600);
			}
			$fill->moveTo($xMin + ($xMax - $xMin) / 2, $yMin + ($yMax - $yMin) / 2);
			
			$shape->setRightFill($fill);
		}
		
		$points = $polygon->all();
		$count = count($points);
		
		if($count > 1) {
			
			$prev = $points[0];
			
			$shape->movePenTo($prev->x, $prev->y);
			
			for($i = 1; $i < $count; $i++) {
				$current = $points[$i];
				$shape->drawLineTo($current->x, $current->y);
			}
			
			// Close the polygon
			$shape->drawLineTo($prev->x, $prev->y);
			
			$this->movie->add($shape);
			
		}
	}

	/**
	 * Sends the image, as well as the correct HTTP headers, to the browser
	 *
	 * @param &$image The Image object to send
	 */
	 function send(&$image) {
		$this->drawImage($image);
	}
	
	/**
	 * Get the image as binary data
	 *
	 * @param &$image
	 */
	 function get(&$image) {
		return $this->drawImage($image, TRUE, FALSE);
	}
	
	 function getTextWidth(&$text) {
		$font = $text->getFont();
		if($this->isCompatibleWithFont($font) === FALSE) {
			awImage::drawError('Class MingDriver: Incompatible font type (\''.get_class($font).'\')');
		}
		
		// Ming only supports FileFont
		$fontDriver = $this->fileFontDriver;
				
		return $fontDriver->getTextWidth($text, $this);
	}
	
	 function getTextHeight(&$text) {
		$font = $text->getFont();
		if($this->isCompatibleWithFont($font) === FALSE) {
			awImage::drawError('Class MingDriver: Incompatible font type (\''.get_class($font).'\')');
		}
		
		// Ming only supports FileFont
		$fontDriver = $this->fileFontDriver;
		
		return $fontDriver->getTextHeight($text, $this);
	}
	
	 function isCompatibleWithFont(&$font) {
		if(is_a($font, 'awTTFFont') or is_a($font, 'awPHPFont')) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	 function drawImage(&$image, $return = FALSE, $header = TRUE) {
		
		// Send headers to the browser
		if($header === TRUE) {
			$image->sendHeaders();
		}
		
		if($return) {
			ob_start();
		}
		
		$this->movie->output();
		
		if($return) {
			return ob_get_clean();
		}
	}

	/**
	 * Convert an awGradient object to an SWFGradient one.
	 * Returns an object as well as the style of the Flash gradient.
	 *
	 * @param $gradient The awGradient object to convert
	 * @return array
	 */
	 function getGradient($gradient) {
		$flashGradient = new SWFGradient();
		
		// Get RGBA values for the gradient boundaries
		list($r1, $g1, $b1, $a1) = $this->getColor($gradient->from);
		list($r2, $g2, $b2, $a2) = $this->getColor($gradient->to);
		
		$flashGradient->addEntry(0, $r1, $g1, $b1, $a1);
		
		if(is_a($gradient, 'awBilinearGradient')) {
			
			$flashGradient->addEntry($gradient->center, $r2, $g2, $b2, $a2);
			$flashGradient->addEntry(1, $r1, $g1, $b1, $a1);
			
			return array($flashGradient, SWFFILL_LINEAR_GRADIENT);
		} else {

			$flashGradient->addEntry(1, $r2, $g2, $b2, $a2);
			
			if(is_a($gradient, 'awLinearGradient')) {
				return array($flashGradient, SWFFILL_LINEAR_GRADIENT);
			} else {
				return array($flashGradient, SWFFILL_RADIAL_GRADIENT);
			}
		}
	}
//	abstract private 

}

registerClass('MingDriver');

/*
 * Check for ming presence
 */
if(function_exists('ming_useswfversion') === FALSE) {
	awImage::drawErrorFile('missing-ming');
}

?>