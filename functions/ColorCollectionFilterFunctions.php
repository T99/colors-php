<?php

class ColorCollectionFilterFunctions {
	
	/**
	 * Creates a filter function that matches ColorOccurrences whose color comprises at least the specified percentage
	 * occurrence rate.
	 *
	 * @param float $minimumOccurrenceRate
	 * @return callable
	 */
	public static function createRelativeOccurrenceFilter(float $minimumOccurrenceRate): callable {
		
		return function (ColorOccurrence $colorOccurrence, int $totalOccurrences) use ($minimumOccurrenceRate): bool {
			
			return ((count($colorOccurrence) / $totalOccurrences) >= $minimumOccurrenceRate);
			
		};
		
	}
	
	public static function createAbsoluteOccurrenceFilterFunction(int $minimumOccurrences): callable {
		
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