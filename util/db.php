<?php

function getImageURLsFromDatabase(mysqli $dbc, int $limit = 20, int $offset = 0): array {

	$brandName = "ekornes";
	
	$queryResult = $dbc->query("
		SELECT `media`.`fileName`
		FROM `media`
		JOIN `productsMedia`
			ON `productsMedia`.`mediaId` = `media`.`id`
		JOIN `products`
			ON `products`.`id` = `productsMedia`.`productId`
		JOIN `brands`
			ON `brands`.`id` = `products`.`brandId`
		WHERE `brands`.`title` = '$brandName'
		LIMIT $limit OFFSET $offset;
	");
	
	$arrayResults = [];
	
	while ($row = $queryResult->fetch_assoc()) {
		
		$arrayResults[] = "https://knorrcatalog.s3.amazonaws.com/$brandName/" . $row["fileName"];
		
	}
	
	return $arrayResults;

}

function getImagesToConvert(mysqli $dbc, int $limit = 20, int $offset = 0): array {
	
	$brandName = "ekornes";
	
	$queryResult = $dbc->query("
		SELECT `uri` FROM `imagesToConvert` LIMIT $limit OFFSET $offset
	");
	
	$arrayResults = [];
	
	while ($row = $queryResult->fetch_assoc()) {
		
		$path = $row["uri"];
		
		$arrayResults[] = "https://knorrcatalog.s3.amazonaws.com$path";
		
	}
	
	return $arrayResults;
	
}

function setImageColorsInDatabase(mysqli $dbc, string $url, ColorCollection &$colors): void {
	
	$totalCount = $colors->getOccurrenceCount();
	
	foreach ($colors as $color) {
		
		$colorID = $color->getColor()->toHexString(false);
		$composition = number_format(count($color) / $totalCount, 3, '.', '');
		
		$dbc->query("
			INSERT INTO `imageColors` (`uri`, `color`, `composition`)
				VALUES ('$url', '$colorID', $composition)
				ON DUPLICATE KEY UPDATE `composition` = $composition
		");
		
		$dbc->query("
			DELETE FROM `imagesToConvert` WHERE `uri` = '$colorID'
		");
		
	}

}