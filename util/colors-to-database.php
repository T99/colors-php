<?php

function addColorsToDatabase(): void {
	
	echo "<pre>";
	
	$credentials = file_get_contents("credentials.json");
	$credentials = json_decode($credentials, true);
	
	$masterDBC = new mysqli(
		$credentials["c3v5"]["host"],
		$credentials["c3v5"]["user"],
		$credentials["c3v5"]["pass"],
		"catalog"
	);
	
	$localDBC = new mysqli(
		$credentials["local"]["host"],
		$credentials["local"]["user"],
		$credentials["local"]["pass"],
		"sandbox"
	);
	
	$urls = getImageURLsFromDatabase($masterDBC, 50, 0);
	
	echo "</pre>";
	
	foreach ($urls as $url) {
		
		$startTime = microtime(true);
		$imagePath = $url;
		$imageProcessor = ColorImageProcessor::fromURI($imagePath);
		$imageSize = $imageProcessor->getImageSize();
		$imageColors = $imageProcessor->getPrimaryImageColors();
		
//		setImageColorsInDatabase($localDBC, parse_url($url, PHP_URL_PATH), $imageColors);

		echo ColorDebugger::showColorCollectionWithImage(
			$imagePath,
			$imageColors,
			$imageSize->x . "x" . $imageSize->y . " image scanned in " . number_format(microtime(true) - $startTime, 2) . " seconds."
		);
	
	}
	
	echo "Done!";
	
}
