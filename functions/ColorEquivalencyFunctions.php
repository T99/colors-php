<?php

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
		
		/**
		 * Returns a human-perception-based 'distance' between two colors, indicating how similar they
		 * seem to the average human eye.
		 *
		 * See this resource for details as to the reasoning behind this function:
		 * https://www.compuphase.com/cmetric.htm
		 *
		 * @param $color1 Color The first RGB color to compare.
		 * @param $color2 Color The second RGB color to compare.
		 * @return bool true if the resultant human-perception-based 'distance' between the two given colors was at
		 * least as small as the configured threshold.
		 */
		return function(Color $color1, Color $color2) use ($threshold): bool {
			
			$redMean = ($color1->getRed() + $color2->getRed()) / 2;
			
			$redComponent = $color1->getRed() - $color2->getRed();
			$greenComponent = $color1->getGreen() - $color2->getGreen();
			$blueComponent = $color1->getBlue() - $color2->getBlue();
			
			$perceptualColorDistance = sqrt(
				(((512 + $redMean) * $redComponent * $redComponent) >> 8) +
				4 * $greenComponent * $greenComponent +
				(((767 - $redMean) * $blueComponent * $blueComponent) >> 8)
			);
			
			return ($perceptualColorDistance <= $threshold);
			
		};
		
	}
	
	
	public static function createCIE76DeltaEEquivalencyFunction(float $threshold = 2.3): callable {
		
		return function(Color $color1, Color $color2) use ($threshold): bool {
			
			$LComponent = pow($color2->getLStar() - $color1->getLStar(), 2);
			$CComponent = pow($color2->getAStar() - $color1->getAStar(), 2);
			$HComponent = pow($color2->getBStar() - $color1->getBStar(), 2);
			
			$deltaE = sqrt($LComponent + $CComponent + $HComponent);
			
			return ($deltaE <= $threshold);
			
		};
		
	}
	
	
	public static function createCIE94DeltaEEquivalencyFunction(float $threshold = 2.3): callable {
		
		return function(Color $color1, Color $color2) use ($threshold): bool {
			
			// Unity and weighting factors for graphics arts:
			$k_L = 1;
			$K_1 = 0.045;
			$K_2 = 0.015;
			
			// Unknown factors...?
			$k_C = 1;
			$k_H = 1;
			
			$deltaLStar = $color1->getLStar() - $color2->getLStar();
			$CStar_1 = sqrt(pow($color1->getAStar(), 2) + pow($color1->getBStar(), 2));
			$CStar_2 = sqrt(pow($color2->getAStar(), 2) + pow($color2->getBStar(), 2));
			$deltaCStar_ab = $CStar_1 - $CStar_2;
			$deltaAStar = $color1->getAStar() - $color2->getAStar();
			$deltaBStar = $color1->getBStar() - $color2->getBStar();
			$S_L = 1;
			$S_C = 1 + $K_1 * $CStar_1;
			$S_H = 1 + $K_2 * $CStar_1;
			$deltaHStar_ab = sqrt(pow($deltaAStar, 2) + pow($deltaBStar, 2) - pow($deltaCStar_ab, 2));
			
			$LComponent = pow($deltaLStar / ($k_L * $S_L), 2);
			$CComponent = pow($deltaCStar_ab / ($k_C * $S_C), 2);
			$HComponent = pow($deltaHStar_ab / ($k_H * $S_H), 2);
			
			$deltaE = sqrt($LComponent + $CComponent + $HComponent);
			
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