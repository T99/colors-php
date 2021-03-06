<?php

include_once "./util/matrices.php";

class Color {
	
	protected $red;
	
	protected $green;
	
	protected $blue;
	
	protected $x;
	
	protected $y;
	
	protected $z;
	
	protected $lStar;
	
	protected $aStar;
	
	protected $bStar;
	
	protected $hue;
	
	protected $saturation_hsb;
	
	protected $saturation_hsl;
	
	protected $brightness;
	
	protected $lightness;
	
	public function __construct(int $red, int $green, int $blue) {
	
		$this->setRed($red);
		$this->setGreen($green);
		$this->setBlue($blue);
	
	}
	
	public static function fromRGB(string $rgbString): Color {
	
		$regex = '/rgb\((\d+), ?(\d+), ?(\d+)\)/';
		preg_match_all($regex, $rgbString, $matches);
		
		if (count($matches) === 4) {
			
			$redComponent = $matches[1][0];
			$greenComponent = $matches[2][0];
			$blueComponent = $matches[3][0];
			
			return new Color(intval($redComponent), intval($greenComponent), intval($blueComponent));
			
		}
		
		throw new Error("Could not parse string '$rgbString' as an RGB string.");
	
	}
	
	public static function fromHex(string $hexString): Color {
		
		$regex = '/#?([[:xdigit:]]{2})([[:xdigit:]]{2})([[:xdigit:]]{2})/';
		preg_match_all($regex, $hexString, $matches);
		
		if (count($matches) === 4) {
			
			$redComponent = $matches[1][0];
			$greenComponent = $matches[2][0];
			$blueComponent = $matches[3][0];
			
			return new Color(hexdec($redComponent), hexdec($greenComponent), hexdec($blueComponent));
			
		}
		
		throw new Error("Could not parse string '$hexString' as a hex string.");
	
	}
	
	public static function fromHSB(float $hue, float $saturation, float $brightness): Color {
		
		$chroma = $brightness * $saturation;
		
		$huePrime = $hue / 60;
		$x = $chroma * (1 - abs(($huePrime % 2) - 1));
		
		if ($huePrime <= 1)      $color = new Color($chroma, $x, 0);
		else if ($huePrime <= 2) $color = new Color($x, $chroma, 0);
		else if ($huePrime <= 3) $color = new Color(0, $chroma, $x);
		else if ($huePrime <= 4) $color = new Color(0, $x, $chroma);
		else if ($huePrime <= 5) $color = new Color($x, 0, $chroma);
		else if ($huePrime <= 6) $color = new Color($chroma, 0, $x);
		else                     $color = new Color(0, 0, 0);
		
		$m = $brightness - $chroma;
		
		$color->setRed($color->getRed() + $m);
		$color->setGreen($color->getGreen() + $m);
		$color->setBlue($color->getBlue() + $m);
		
		$color->hue = $hue;
		$color->saturation_hsb = $saturation;
		$color->brightness = $brightness;
		
		return $color;
	
	}
	
	public static function fromHSV(float $hue, float $saturation, float $value): Color {
		
		return Color::fromHSB($hue, $saturation, $value);
		
	}
	
	public static function fromHSL(float $hue, float $saturation, float $lightness): Color {
		
		$hue = fmod($hue, 360);
		
		$chroma = $saturation * (1 - abs((2 * $lightness) - 1));
		
		$huePrime = $hue / 60;
		$x = $chroma * (1 - abs(fmod($huePrime, 2) - 1));
		
		$m = $lightness - ($chroma / 2);
		
		$chroma255 = round(255 * ($chroma + $m));
		$x255      = round(255 * ($x + $m));
		$m255      = round(255 * $m);
		
		     if ($huePrime < 1) $color = new Color($chroma255, $x255, $m255);
		else if ($huePrime < 2) $color = new Color($x255, $chroma255, $m255);
		else if ($huePrime < 3) $color = new Color($m255, $chroma255, $x255);
		else if ($huePrime < 4) $color = new Color($m255, $x255, $chroma255);
		else if ($huePrime < 5) $color = new Color($x255, $m255, $chroma255);
		else if ($huePrime < 6) $color = new Color($chroma255, $m255, $x255);
		else                    $color = new Color(0, 0, 0);
		
		$color->hue = $hue;
		$color->saturation_hsl = $saturation;
		$color->lightness = $lightness;
		
		return $color;
		
	}
	
	public static function white(): Color {
		
		return new Color(255, 255, 255);
		
	}
	
	public static function black(): Color {
		
		return new Color(0, 0, 0);
		
	}
	
	public static function red(): Color {
	
		return new Color(255, 0, 0);
	
	}
	
	public static function green(): Color {
		
		return new Color(0, 255, 0);
		
	}
	
	public static function blue(): Color {
		
		return new Color(0, 0, 255);
		
	}
	
	public function setRed(int $red): void {
		
		if ($red < 0) $this->red = 0;
		else if ($red > 255) $this->red = 255;
		else $this->red = $red;
		
	}
	
	public function getRed(): int {
		
		return $this->red;
		
	}
	
	public function setGreen(int $green): void {
		
		if ($green < 0) $this->green = 0;
		else if ($green > 255) $this->green = 255;
		else $this->green = $green;
		
	}
	
	public function getGreen(): int {
		
		return $this->green;
		
	}
	
	public function setBlue(int $blue): void {
		
		if ($blue < 0) $this->blue = 0;
		else if ($blue > 255) $this->blue = 255;
		else $this->blue = $blue;
		
	}
	
	public function getBlue(): int {
		
		return $this->blue;
		
	}
	
	/**
	 * Computes the appropriate CIEXYZ color values from the current sRGB color values.
	 */
	public function computeXYZ(): void {
		
		$components_srgb = [
			$this->getRed() / 255,
			$this->getGreen() / 255,
			$this->getBlue() / 255
		];
		
		$a = 0.055;
		
		$components_linear = [];
		
		foreach ($components_srgb as $component_srgb) {
		
			if ($component_srgb <= 0.04045) $components_linear[] = [$component_srgb / 12.92];
			else $components_linear[] = [pow((($component_srgb + $a) / (1 + $a)), 2.4)];
			
		}
		
		$correctiveMatrix = [
			[0.4124, 0.3576, 0.1805],
			[0.2126, 0.7152, 0.0722],
			[0.0193, 0.1192, 0.9505]
		];
		
		$components_xyzd65 = dotProduct($correctiveMatrix, $components_linear);
		
		
		$this->x = $components_xyzd65[0][0] * 100;
		$this->y = $components_xyzd65[1][0] * 100;
		$this->z = $components_xyzd65[2][0] * 100;
		
	}
	
	public function computeLAB(): void {
	
		if (!isset($this->x) || !isset($this->y) || !isset($this->z)) $this->computeXYZ();
		
		$delta = 6/29;
		$deltaSquared = $delta * $delta;
		$deltaCubed = $deltaSquared * $delta;
		
		$f = function($t) use ($deltaSquared, $deltaCubed): float {
		
			if ($t > $deltaCubed) return pow($t, 1/3);
			else return (($t / (3 * $deltaSquared)) + (4/29));
		
		};
		
		// Sub-n values set according to standard illuminate D65.
		// These values are effectively relative corrective settings viewing medium of the color.
		$x_n = 95.0489;
		$y_n = 100;
		$z_n = 108.8840;
		
		$this->lStar = 116 * $f($this->y / $y_n) - 16;
		$this->aStar = 500 * ($f($this->x / $x_n) - $f($this->y / $y_n));
		$this->bStar = 200 * ($f($this->y / $y_n) - $f($this->z / $z_n));
		
	}
	
	/**
	 * Computes the Hue-Saturation-Brightness values for this color.
	 */
	public function computeHSB(): void {
		
		$redPrime = $this->red / 255;
		$greenPrime = $this->green / 255;
		$bluePrime = $this->blue / 255;
		
		$cMax = max($redPrime, $greenPrime, $bluePrime);
		$cMin = min($redPrime, $greenPrime, $bluePrime);
		$cDelta = $cMax - $cMin;
		
		if ($cDelta < 0.000001) $this->hue = 0;
		else {
			
			switch ($cMax) {
				
				case $redPrime: {
					
					$this->hue = 60 * (fmod(($greenPrime - $bluePrime) / $cDelta, 6));
					break;
					
				}
				
				case $greenPrime: {
					
					$this->hue = 60 * ((($bluePrime - $redPrime) / $cDelta) + 2);
					break;
					
				}
				
				case $bluePrime: {
					
					$this->hue = 60 * ((($redPrime - $greenPrime) / $cDelta) + 4);
					break;
					
				}
				
			}
			
		}
		
		// Normalize hue to the range [0..360]
		$this->hue = fmod($this->hue + 360, 360);
		
		if ($cMax === 0) $this->saturation_hsb = 0;
		else $this->saturation_hsb = $cDelta / $cMax;
		
		$this->brightness = $cMax;
	
	}
	
	/**
	 * Alias method for `computeHSB`, as HSV is simply another name for HSB.
	 */
	public function computeHSV(): void {
		
		$this->computeHSB();
		
	}
	
	/**
	 * Computes the Hue-Saturation-Lightness values for this color.
	 */
	public function computeHSL(): void {
		
		if (!isset($this->brightness)) $this->computeHSB();
		
		$this->lightness = $this->brightness * (1 - ($this->saturation_hsb / 2));
		
		if ($this->lightness === 0 || $this->lightness === 1) $this->saturation_hsl = 0;
		else {
			
			$this->saturation_hsl = ($this->brightness - $this->lightness) / min($this->lightness, 1 - $this->lightness);
			
		}
		
	}
	
	public function getHue(): int {
		
		if (!isset($this->hue)) $this->computeHSL();
		
		return round($this->hue);
		
	}
	
	public function getHSBSaturation(): float {
		
		if (!isset($this->saturation_hsb)) $this->computeHSB();
		
		return $this->saturation_hsb;
		
	}
	
	public function getHSVSaturation(): float {
		
		if (!isset($this->saturation_hsb)) $this->computeHSB();
		
		return $this->saturation_hsb;
		
	}
	
	public function getHSLSaturation(): float {
		
		if (!isset($this->saturation_hsl)) $this->computeHSL();
		
		return $this->saturation_hsl;
		
	}
	
	public function getBrightness(): float {
		
		if (!isset($this->brightness)) $this->computeHSB();
		
		return $this->brightness;
		
	}
	
	public function getLightness(): float {
		
		if (!isset($this->lightness)) $this->computeHSL();
		
		return $this->lightness;
		
	}
	
	public function getX(): float {
		
		if (!isset($this->x)) $this->computeXYZ();
		
		return $this->x;
		
	}
	
	public function getY(): float {
		
		if (!isset($this->y)) $this->computeXYZ();
		
		return $this->y;
		
	}
	
	public function getZ(): float {
		
		if (!isset($this->z)) $this->computeXYZ();
		
		return $this->z;
		
	}
	
	public function getLStar(): float {
		
		if (!isset($this->lStar)) $this->computeLAB();
		
		return $this->lStar;
	
	}
	
	public function getAStar(): float {
	
		if (!isset($this->aStar)) $this->computeLAB();
		
		return $this->aStar;
	
	}
	
	public function getBStar(): float {
		
		if (!isset($this->bStar)) $this->computeLAB();
		
		return $this->bStar;
	
	}
	
	public function getUniqueColorID(): string {
		
		return $this->toHexString(true);
		
	}
	
	public function toRGBString(): string {
		
		$red = $this->getRed();
		$green = $this->getGreen();
		$blue = $this->getBlue();
		
		return "rgb($red, $green, $blue)";
		
	}
	
	public function toHSBString(): string {
		
		$hue =        $this->getHue();
		$saturation = number_format($this->getHSBSaturation(), 2);
		$brightness = number_format($this->getBrightness(), 2);
		
		if ($saturation === "1.00") $saturation = 1;
		if ($brightness === "1.00") $brightness = 1;
		
		return "hsb($hue, $saturation, $brightness)";
		
	}
	
	public function toHSVString(): string {
		
		$hue = $this->getHue();
		$saturation = $this->getHSBSaturation();
		$brightness = $this->getBrightness();
		
		if ($saturation === "1.00") $saturation = 1;
		if ($brightness === "1.00") $brightness = 1;
		
		return "hsv($hue, $saturation, $brightness)";
		
	}
	
	public function toHSLString(): string {
		
		$hue =        $this->getHue();
		$saturation = number_format($this->getHSLSaturation(), 2);
		$lightness =  number_format($this->getLightness(), 2);
		
		if ($saturation === "1.00") $saturation = 1;
		if ($lightness === "1.00") $lightness = 1;
		
		return "hsl($hue, $saturation, $lightness)";
		
	}
	
	public function toHexString(bool $includePoundSign = true): string {
		
		$poundSign = $includePoundSign ? "#" : "";
		$redComponent = strtoupper(str_pad(dechex($this->getRed()), 2, "0", STR_PAD_LEFT));
		$greenComponent = strtoupper(str_pad(dechex($this->getGreen()), 2, "0", STR_PAD_LEFT));
		$blueComponent = strtoupper(str_pad(dechex($this->getBlue()), 2, "0", STR_PAD_LEFT));
		
		return "$poundSign$redComponent$greenComponent$blueComponent";
		
	}
	
	public static function getColorBlockContentStylings(): string {
		
		$stylings = "";
		
		$stylings .= "background: #FFFA;";
		$stylings .= " padding: 3px;";
		$stylings .= " border-radius: 3px;";
		$stylings .= " font-size: 0.9em;";
		$stylings .= " font-family: monospace;";
		$stylings .= " margin: 2px 0;";
		
		return $stylings;
		
	}
	
	public static function getDefaultColorBlockSize(): int {
		
		return 150;
		
	}
	
	public function toHTMLColorBlock(int $size = null, string $innerContent = ""): string {
		
		if (is_null($size)) $size = Color::getDefaultColorBlockSize();
		
		$result = "";
		
		$stylings =  "height: " . $size . "px;";
		$stylings .= " width: " . $size . "px;";
		$stylings .= " background-color: " . $this->toHexString(true) . ";";
		$stylings .= " display: flex;";
		$stylings .= " flex-direction: column;";
		$stylings .= " justify-content: center;";
		$stylings .= " align-items: center;";
		
		$result .= "<div style='$stylings'>";
		$result .= $innerContent;
		$result .= "</div>";
		
		return $result;
		
	}
	
	public function toRGBLabelledHTMLColorBlock(int $size = null, string $innerContent = ""): string {
		
		$stylings = Color::getColorBlockContentStylings();
		
		$rgbLabel = "<p style='$stylings'>" . $this->toRGBString() . "</p>";
		
		$rgbLabel .= "<p style='$stylings'>" . $this->toHSLString() . "</p>";
		
		$rgbLabel .= "<p style='$stylings'>" . join(", ", ColorNaming::getNames($this)) . "</p>";
		
		$innerContent = $innerContent . $rgbLabel;
		
		return $this->toHTMLColorBlock($size, $innerContent);
		
	}
	
	public function toHexLabelledHTMLColorBlock(int $size = null, string $innerContent = ""): string {
		
		$stylings = Color::getColorBlockContentStylings();
		
		$rgbLabel = "<p style='$stylings'>" . $this->ttoHexString(true) . "</p>\n";
		
		$innerContent = $innerContent . $rgbLabel;
		
		return $this->toHTMLColorBlock($size, $innerContent);
		
	}
	
	public function __toString() {
		
		return $this->toRGBString();
		
	}
	
}