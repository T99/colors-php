<?php

function absoluteDensityToRelativeDensity(float $absoluteDensity): float {
	
	return (pow(floor((2 * $absoluteDensity - 1) / $absoluteDensity), 2) / pow($absoluteDensity, 2));
	
}

/**
 * Approximates an absolute density value that will produce the desired relative density.
 *
 * Because of the fact that the {@link absoluteDensityToRelativeDensity} function uses the floor function as a part of
 * its operation, algebraically solving in order to find the inverse function is non-trivial. Instead, we use a binary
 * search-type method here to approximate an appropriate absolute density that will yield
 *
 * @param float $relativeDensity
 * @param float $epsilon
 * @return float
 */
function relativeDensityToAbsoluteDensity(float $relativeDensity, float $epsilon = 0.001): float {
	
	if ($relativeDensity >= 1) return 1;
	else if ($relativeDensity <= 0) return 0;

	$ceiling = 10000;
	$floor = 1;
	
	$count = 0;
	
	do {
		
		$currentAbsoluteDensity = (($ceiling - $floor) / 2) + $floor;
		$currentRelativeDensity = absoluteDensityToRelativeDensity($currentAbsoluteDensity);
		
		if ($currentRelativeDensity > $relativeDensity) $floor = $currentAbsoluteDensity;
		else $ceiling = $currentAbsoluteDensity;
		
	} while (abs($currentRelativeDensity - $relativeDensity) > $epsilon && ++$count < 20);
	
	return $currentAbsoluteDensity;

}