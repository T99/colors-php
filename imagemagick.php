<?php

include_once "./Color.php";
include_once "./ColorNaming.php";
include_once "./ColorDebugger.php";
include_once "./ColorOccurrence.php";
include_once "./ColorCollection.php";
include_once "./ColorImageProcessor.php";

include_once "./functions/ColorMergerFunctions.php";
include_once "./functions/ColorEquivalencyFunctions.php";
include_once "./functions/ColorCollectionFilterFunctions.php";

include_once "./util/db.php";
include_once "./util/density.php";
include_once "./util/colors-to-database.php";

echo "
	<style>
		html, body {
			margin: 0;
			padding: 0;
			scroll-snap-type: y mandatory;
		}
		
		body .image-with-colors {
			scroll-snap-align: start;
		}
	</style>
";

//$colors = new ColorCollection();
//$step = 5;
//
//for ($hue = 0; $hue <= 360; $hue += $step) {
//
//	for ($saturation = 0; $saturation <= 1.00001; $saturation += $step / 100) {
//
////		$lightness = 0.75;
//
//		for ($lightness = 0; $lightness <= 1.00001; $lightness += $step / 100) {
//
//			$color = Color::fromHSL($hue, $saturation, $lightness);
//
////			if (count(ColorNaming::getNames($color)) === 0) $colors->addColors($color);
//
////			if (ColorNaming::isBrown($color)) $colors->addColors($color);
//
//			$colors->addColors($color);
//
//		}
//
//	}
//
//}

//addColorsToDatabase();

//$colors = $colors->head(3000, 3000);

//for ($r = 0; $r <= 255; $r += $step) {
//
//	for ($g = 0; $g <= 255; $g += $step) {
//
//		for ($b = 0; $b <= 255; $b += $step) {
//
//			$color = new Color($r, $g, $b);
//
//			if (ColorNaming::isRed($color)) $colors->addColors($color);
//
//		}
//
//	}
//
//}

//echo ColorDebugger::showColorCollection($colors);

$startTime = microtime(true);
$imagePath = "examples/stressless-consul-chair-ottoman.jpg";
$imageProcessor = ColorImageProcessor::fromURI($imagePath);
$imageSize = $imageProcessor->getImageSize();
$imageColors = $imageProcessor->getPrimaryImageColors();

echo ColorDebugger::showColorCollectionWithImage(
	$imagePath,
	$imageColors,
	$imageSize->x . "x" . $imageSize->y . " image scanned in " . number_format(microtime(true) - $startTime, 2) . " seconds."
);
