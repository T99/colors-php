<?php

include_once "./Color.php";
include_once "./matrices.php";
include_once "./ColorDebugger.php";
include_once "./ColorOccurrence.php";
include_once "./ColorCollection.php";
include_once "./ColorImageProcessor.php";
include_once "./functions/ColorMergerFunctions.php";
include_once "./functions/ColorEquivalencyFunctions.php";
include_once "./functions/ColorCollectionFilterFunctions.php";

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

echo "<pre>";

$imagePath = "examples/patterned-chair.jpg";

$imageProcessor = new ColorImageProcessor($imagePath);
$imageColors = $imageProcessor->getDistinctColors(1);

$imageColors = $imageColors->getFilteredColorCollection(
	ColorCollectionFilterFunctions::createGreyscaleFilter(239),
	true
);

//$imageColors = $imageColors->getFilteredColorCollection(
//	ColorCollectionFilterFunctions::createGreyscaleFilter(35, false),
//	true
//);

$imageColors = $imageColors->getFilteredColorCollection(
	ColorCollectionFilterFunctions::createAbsoluteOccurrenceFilterFunction(5)
);

$imageColors = $imageColors->getMergedColorCollection(
	ColorEquivalencyFunctions::createCIE94DeltaEEquivalencyFunction(20),
	ColorMergerFunctions::createWeightedAverageMergerFunction()
);

$imageColors = $imageColors->getFilteredColorCollection(
	ColorCollectionFilterFunctions::createRelativeOccurrenceFilter(0.1)
);

//$imageColors = $imageColors->getFilteredColorCollection(
//	ColorCollectionFilterFunctions::createMinimumOccurrenceFilterFunction(500)
//);

//$imageColors->sortByIncidence();

//$imageColors = $imageColors->head(3);

//$imageColors = $imageColors->getMergedColorCollection(
//	ColorEquivalencyFunctions::createImpliedEquivalencyFunction(),
//	ColorMergerFunctions::createWeightedAverageMergerFunction()
//);

$outputColors = $imageColors;

$outputColors->sortByIncidence();

echo "</pre>";

echo ColorDebugger::showColorCollectionWithImage(
	$imagePath,
	$outputColors
);


