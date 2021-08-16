<?php

class ColorNaming {
	
	public static function isRed(Color $color): bool {
		
		$red   = $color->getRed();
		$green = $color->getGreen();
		$blue  = $color->getBlue();
		
		return (
			($red >= 80) &&
			($red >= (3 * $green)) &&
			($red >= (3 * $blue))
		);
		
	}
	
	public static function isGreen(Color $color): bool {
		
		$red   = $color->getRed();
		$green = $color->getGreen();
		$blue  = $color->getBlue();
		
		return (
			($green >= 30) &&
			($green >= (1.25 * $red)) &&
			($green >= (1.25 * $blue))
		);
		
	}
	
	public static function isBlue(Color $color): bool {
		
		$red   = $color->getRed();
		$green = $color->getGreen();
		$blue  = $color->getBlue();
		
		return (
			($blue >= 80) &&
			($green >= (1.25 * $red)) &&
			($green >= (1.25 * $blue))
		);
		
	}
	
	public static function getNames(): array {
	
	
	
	}
	
}