<?php

include_once "./functions/ColorDifferenceFunctions.php";

class ColorEquivalencyFunctions {
	
	public static function createImpliedEquivalencyFunction(): callable {
		
		return function(Color $color1, Color $color2): bool {
			
			return true;
			
		};
		
	}
	
	public static function createSimpleThresholdEquivalencyFunction(int $threshold): callable {
		
		return function(Color $color1, Color $color2) use ($threshold): bool {
			
			return (
				(abs($color1->getRed()   - $color2->getRed()  ) <= $threshold) &&
				(abs($color1->getGreen() - $color2->getGreen()) <= $threshold) &&
				(abs($color1->getBlue()  - $color2->getBlue() ) <= $threshold)
			);
			
		};
		
	}
	
	
	public static function createHumanPerceptualEquivalencyFunction(int $threshold): callable {
		
		return function(Color $color1, Color $color2) use ($threshold): bool {
			
			$difference = ColorDifferenceFunctions::createHumanPerceptualDifferenceFunction()($color1, $color2);
			
			return ($difference <= $threshold);
			
		};
		
	}
	
	
	public static function createCIE76DeltaEEquivalencyFunction(float $threshold = 2.3): callable {
		
		return function(Color $color1, Color $color2) use ($threshold): bool {
			
			$deltaE = ColorDifferenceFunctions::createCIE76DeltaEDifferenceFunction()($color1, $color2);
			
			return ($deltaE <= $threshold);
			
		};
		
	}
	
	
	public static function createCIE94DeltaEEquivalencyFunction(float $threshold = 2.3): callable {
		
		return function(Color $color1, Color $color2) use ($threshold): bool {
			
			$deltaE = ColorDifferenceFunctions::createCIE94DeltaEDifferenceFunction()($color1, $color2);
			
			return ($deltaE <= $threshold);
			
		};
		
	}
	
	
	public static function createCIEDE2000DeltaEEquivalencyFunction(float $threshold = 2.3): callable {
		
		return function(Color $color1, Color $color2) use ($threshold): bool {
			
			
			
			// not implemented
			return false;
			
		};
		
	}
	
}