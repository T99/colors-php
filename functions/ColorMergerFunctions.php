<?php

class ColorMergerFunctions {
	
	/**
	 * Returns a callable/function that generates a {@link Color} from an array of {@link ColorOccurrence} objects based
	 * on a simple average of all of their red, green, and blue components.
	 */
	public static function createSimpleAverageMergerFunction(): callable {
		
		return function(...$colorOccurrences): ColorOccurrence {
			
			$redSum = 0;
			$greenSum = 0;
			$blueSum = 0;
			$count = count($colorOccurrences);
			
			foreach ($colorOccurrences as $colorOccurrence) {
				
				$color = $colorOccurrence->getColor();
				
				$redSum += $color->getRed();
				$greenSum += $color->getGreen();
				$blueSum += $color->getBlue();
				
			}
			
			return new ColorOccurrence(
				new Color(
					$redSum / $count,
					$greenSum / $count,
					$blueSum / $count
				),
				$count
			);
			
		};
		
	}
	
	/**
	 * Returns a callable/function that generates a {@link Color} from an array of {@link ColorOccurrence} objects based
	 * on a weighted average of all of their red, green, and blue components, weighted by number of occurrences.
	 */
	public static function createWeightedAverageMergerFunction(): callable {
		
		return function(...$colorOccurrences): ColorOccurrence {
			
			$redSum = floatval(0);
			$greenSum = floatval(0);
			$blueSum = floatval(0);
			$count = 0;
			
			foreach ($colorOccurrences as $colorOccurrence) {
				
				$color = $colorOccurrence->getColor();
				$colorCount = count($colorOccurrence);
				
				$redSum   += $colorCount * pow($color->getRed(), 2);
				$greenSum += $colorCount * pow($color->getGreen(), 2);
				$blueSum  += $colorCount * pow($color->getBlue(), 2);
				
				$count += $colorCount;
				
			}
			
			return new ColorOccurrence(
				new Color(
					sqrt($redSum / $count),
					sqrt($greenSum / $count),
					sqrt($blueSum / $count)
				),
				$count
			);
			
		};
		
	}
	
	/**
	 * Returns a callable/function that generates a {@link Color} from an array of {@link ColorOccurrence} objects based
	 * solely on the first Color of the array of ColorOccurrences.
	 */
	public static function createUseFirstMergerFunction(): callable {
		
		return function(...$colorOccurrences): ColorOccurrence {
			
			return new ColorOccurrence(
				$colorOccurrences[0]->getColor(),
				array_reduce($colorOccurrences, function (int $carry, ColorOccurrence $colorOccurrence): int {
					return $carry + count($colorOccurrence);
				}, 0)
			);
			
		};
		
	}
	
}