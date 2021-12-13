<?php

class ColorNaming {
	
	public static function red(Color $color): array {
		
		$reds = [];
		
		if (($color->getHue() >= 330) || ($color->getHue() <= 20)) {
			
			$reds[] = "red";
			
			if ($color->getHSLSaturation() <= 0.25) $reds[] = "grey";
			
		}
		
		if ($excludeGreyscale) {
			
			$isGreyscale =
				ColorNaming::isBlack($color, 0.2) ||
				ColorNaming::isWhite($color, 0.9) ||
				ColorNaming::isGrey($color, 0.15);
			
			if ($isGreyscale) return false;
			
		}
		
		return (
			($color->getHue() >= 330) ||
			($color->getHue() <= 20)
		);
		
	}
	
	public static function orange(Color $color): array {
		
		if ($excludeGreyscale) {
			
			$isGreyscale =
				ColorNaming::isBlack($color, 0.2) ||
				ColorNaming::isWhite($color, 0.9) ||
				ColorNaming::isGrey($color, 0.6);
			
			if ($isGreyscale) return false;
			
		}
		
		return (
			($color->getHue() >= 20) &&
			($color->getHue() <= 55)
		);
		
	}
	
	public static function brown(Color $color, bool $excludeGreyscale = true): array {
		
		if ($excludeGreyscale) {
			
			$isGreyscale =
				ColorNaming::isBlack($color, 0.15) ||
				ColorNaming::isWhite($color, 0.6) ||
				ColorNaming::isGrey($color, 0.05);
			
			if ($isGreyscale) return false;
			
		}
		
		return (
			($color->getHue() >= 10) &&
			($color->getHue() <= 50)
		);
		
	}
	
	public static function yellow(Color $color, bool $excludeGreyscale = true): array {
		
		if ($excludeGreyscale) {
			
			$isGreyscale =
				ColorNaming::isBlack($color, 0.2) ||
				ColorNaming::isWhite($color, 0.9) ||
				ColorNaming::isGrey($color, 0.3);
			
			if ($isGreyscale) return false;
			
		}
		
		return (
			($color->getHue() >= 40) &&
			($color->getHue() <= 75)
		);
		
	}
	
	public static function beige(Color $color, bool $excludeGreyscale = true): array {
		
		if ($excludeGreyscale) {
			
			$isGreyscale =
				ColorNaming::isBlack($color, 0.4) ||
				ColorNaming::isWhite($color, 1) ||
				ColorNaming::isGrey($color, 0.15);
			
			if ($isGreyscale) return false;
			
		}
		
		return (
			($color->getHue() >= 10) &&
			($color->getHue() <= 60)
		);
		
	}
	
	public static function green(Color $color, bool $excludeGreyscale = true): array {
		
		if ($excludeGreyscale) {
			
			$isGreyscale =
				ColorNaming::isBlack($color, 0.25) ||
				ColorNaming::isWhite($color, 0.75) ||
				ColorNaming::isGrey($color, 0.1);
			
			if ($isGreyscale) return false;
			
		}
		
		return (
			($color->getHue() >= 65) &&
			($color->getHue() <= 165)
		);
		
	}
	
	public static function cyan(Color $color, bool $excludeGreyscale = true): array {
		
		if ($excludeGreyscale) {
			
			$isGreyscale =
				ColorNaming::isBlack($color, 0.2) ||
				ColorNaming::isWhite($color, 0.9) ||
				ColorNaming::isGrey($color, 0.2);
			
			if ($isGreyscale) return false;
			
		}
		
		return (
			($color->getHue() >= 160) &&
			($color->getHue() <= 185)
		);
		
	}
	
	public static function blue(Color $color, bool $excludeGreyscale = true): array {
		
		if ($excludeGreyscale) {
			
			$isGreyscale =
				ColorNaming::isBlack($color, 0.2) ||
				ColorNaming::isWhite($color, 0.9) ||
				ColorNaming::isGrey($color, 0.15);
			
			if ($isGreyscale) return false;
			
		}
		
		return (
			($color->getHue() >= 160) &&
			($color->getHue() <= 270)
		);
		
	}
	
	public static function purple(Color $color, bool $excludeGreyscale = true): array {
		
		if ($excludeGreyscale) {
			
			$isGreyscale =
				ColorNaming::isBlack($color, 0.2) ||
				ColorNaming::isWhite($color, 0.9) ||
				ColorNaming::isGrey($color, 0.2);
			
			if ($isGreyscale) return false;
			
		}
		
		$regularPurple = ($color->getHue() >= 260) && ($color->getHue() <= 300);
		$desaturatedBlue = ($color->getHue() >= 260) && ($color->getHue() <= 300) && ($color->getHSLSaturation() <= 75);
		
		return ($regularPurple || $desaturatedBlue);
		
	}
	
	public static function pink(Color $color, bool $excludeGreyscale = true): array {
		
		if ($excludeGreyscale) {
			
			$isGreyscale =
				ColorNaming::isBlack($color, 0.2) ||
				ColorNaming::isWhite($color, 0.9) ||
				ColorNaming::isGrey($color, 0.25);
			
			if ($isGreyscale) return false;
			
		}
		
		$regularPink = ($color->getHue() >= 295) && ($color->getHue() <= 340);
		$lightRed = (($color->getHue() <= 10) || ($color->getHue() >= 340)) && ($color->getLightness() >= 0.4);
		
		return ($regularPink || $lightRed);
		
	}
	
	public static function getNames(Color &$color): array {
		
		return [];

		return array_unique(array_merge(
			ColorNaming::red($color),
			ColorNaming::orange($color),
			ColorNaming::brown($color),
			ColorNaming::yellow($color),
			ColorNaming::beige($color),
			ColorNaming::green($color),
			ColorNaming::cyan($color),
			ColorNaming::blue($color),
			ColorNaming::purple($color),
			ColorNaming::pink($color)
		));

	}
	
//	public static function getNames(Color &$color, float $threshold = 15): array {
//
//		$colors = [
//			"white" => Color::white(),
//			"black" => Color::black(),
//			"grey" => new Color(128, 128, 128),
//			"red" => Color::red(),
//			"green" => Color::green(),
//			"blue" => Color::blue(),
//			"orange" => new Color(255, 165, 0),
//			"brown" => new Color(165, 42, 42),
//			"yellow" => new Color(255, 255, 0),
//			"beige" => new Color(245, 245, 220),
//			"cyan" => new Color(0, 255, 255),
//			"violet" => new Color(255, 0, 255),
//			"purple" => new Color(128, 0, 128),
//			"pink" => new Color(255, 100, 200)
//		];
//
//		$colors = [
//			new Color(255, 255, 255) => "white",
//			new Color(  0,   0,   0) => "black",
//			new Color(128, 128, 128) => "grey",
//			new Color(255,   0,   0) => "red",
//			new Color(  0, 255,   0) => "green",
//			new Color(  0,   0, 255) => "blue",
//			new Color(255, 165,   0) => "orange",
//			new Color(165,  42,  42) => "brown",
//			new Color(255, 255,   0) => "yellow",
//			new Color(245, 245, 220) => "beige",
//			new Color(  0, 255, 255) => "cyan",
//			new Color(255,   0, 255) => "violet",
//			new Color(128,   0, 128) => "purple",
//			new Color(255, 100, 200) => "pink",
//		];
//
//		$differenceFunction = ColorDifferenceFunctions::createCIE94DeltaEDifferenceFunction();
//
//		$colorRatings = array_map(function($testColor) use ($color, $differenceFunction): float {
//
//			return $differenceFunction($color, $testColor);
//
//		}, $colors);
//
//		$names = [];
//
//		foreach ($colorRatings as $colorName => $colorRating) {
//
//			if ($colorRating <= $threshold) $names[] = $colorName;
//
//		}
//
//		return $names;
//
//	}
	
}