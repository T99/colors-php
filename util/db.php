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