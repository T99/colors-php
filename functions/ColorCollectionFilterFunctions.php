<?php

class ColorCollectionFilterFunctions {
	
	public static function createMinimumOccurrenceFilterFunction(int $minimumOccurrences): callable {
		
		return function (ColorOccurrence $colorOccurrence) use ($minimumOccurrences): bool {
			
			return ($colorOccurrence->count() >= $minimumOccurrences);
			
		};
		
	}
	
	public static function createEquivalencyFilter(Color $referenceColor,
												   callable $equivalencyFunction): callable {
		
		return function (ColorOccurrence $colorOccurrence) use ($referenceColor, $equivalencyFunction): bool {
			
			return $equivalencyFunction($referenceColor, $colorOccurrence->getColor());
			
		};
		
	}
	
	public static function createGreyscaleFilter(int $threshold, bool $above = true): callable {
		
		return function (ColorOccurrence $colorOccurrence) use ($threshold, $above): bool {
			
			$color = $colorOccurrence->getColor();
			
			if ($above) {
				
				return (
					$color->getRed() >= $threshold &&
					$color->getGreen() >= $threshold &&
					$color->getBlue() >= $threshold
				);
				
			} else {
				
				return (
					$color->getRed() <= $threshold &&
					$color->getGreen() <= $threshold &&
					$color->getBlue() <= $threshold
				);
				
			}
			
		};
		
	}
	
}