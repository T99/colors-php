<?php

class ColorDebugger {
	
	public static function compareColors(ColorCollection &$colors,
										 int $size = null,
										 int $padding = 0,
										 bool $vertical = true): string {
		
		$result = "";
		$vertical = $vertical ? "flex-direction: column" : "flex-direction: row";
		
		$result .= "<div style='display: flex; padding: " . $padding . "px; $vertical'>";
		
		foreach ($colors as $colorOccurrence) {
			
			$result .= $colorOccurrence->getColor()->toRGBLabelledHTMLColorBlock($size);
		
		}
		
		$result .= "</div>";
		
		return $result;
		
	}
	
	public static function showColorCollectionWithImage(string $imagePath, ColorCollection &$colorCollection,
														string $headerContent = ""): string {
		
		$result = "";
		
		// Begin outer container...
		$outerContainerStyles  = "display: flex;";
		$outerContainerStyles .= " justify-content: center;";
		$outerContainerStyles .= " align-items: center;";
		$outerContainerStyles .= " height: 100vh;";
		
		$result .= "<div class='image-with-colors' style='$outerContainerStyles'>";
		
		// Begin image container...
		$imageContainerStyles  = "display: flex;";
		$imageContainerStyles .= " justify-content: center;";
		$imageContainerStyles .= " align-items: center;";
		$imageContainerStyles .= " flex: 2;";
		$imageContainerStyles .= " height: 100%;";
		$imageContainerStyles .= " box-shadow: inset -12px 0px 10px -10px gray;";
		$imageContainerStyles .= " background: radial-gradient(circle at center, #FBFBFB, #BDBDBD);";
		
		$result .= "<div style='$imageContainerStyles'>";
		
		$imageStyles  = "width: 100%;";
		$imageStyles .= " max-width: 90%;";
		$imageStyles .= " max-height: 90%;";
		$imageStyles .= " box-shadow: -3px 4px 5px 1px gray;";
		
		$result .= "<img src='$imagePath' style='$imageStyles'/>";
		
		$result .= "</div>";
		// End image container.
		
		// Begin right-side container...
		$rightContainerStyles  = "display: flex;";
		$rightContainerStyles .= " flex-direction: column;";
		$rightContainerStyles .= " justify-content: center;";
		$rightContainerStyles .= " align-items: stretch;";
		$rightContainerStyles .= " flex: 3;";
		$rightContainerStyles .= " height: 100%;";
		
		$result .= "<div style='$rightContainerStyles'>";
		
		// Begin header container...
		$headerContainerStyles  = "display: flex;";
		$headerContainerStyles .= " flex-direction: column;";
		$headerContainerStyles .= " justify-content: center;";
		$headerContainerStyles .= " align-items: center;";
		$headerContainerStyles .= " background: linear-gradient(to top right, #3D3D3D, #4E4E4E);";
		$headerContainerStyles .= " padding: 10px;";
		$headerContainerStyles .= " color: white;";
		$headerContainerStyles .= " font-family: sans-serif;";
		
		$result .= "<div style='$headerContainerStyles'>";
		
		$uniqueColorCount = number_format($colorCollection->getColorCount());
		$occurrenceCount = number_format($colorCollection->getOccurrenceCount());
		
		$result .= "<p style='margin: 0; text-align: center'>Showing $occurrenceCount instances of $uniqueColorCount unique colors.</p>";
		$result .= "<p style='margin: 0; margin-top: 5px; text-align: center'>$headerContent</p>";
		
		$result .= "</div>";
		// End header container.
		
		// Begin color container...
		$colorContainerStyles  = "display: flex;";
		$colorContainerStyles .= " justify-content: center;";
		$colorContainerStyles .= " align-items: flex-start;";
		$colorContainerStyles .= " flex: 1;";
		$colorContainerStyles .= " overflow-y: auto;";
		
		$result .= "<div style='$colorContainerStyles'>";
		
		$result .= $colorCollection->toHTMLColorBlockTable(null, 5);
		
		$result .= "</div>";
		// End color container.
		
		$result .= "</div>";
		// End right-side container.
		
		$result .= "</div>";
		// End outer container.
		
		return $result;
		
	}
	
	public static function showDistinctColorsForImage(string $imagePath): string {
		
		$imageProcessor = new ImageColorProcessor($imagePath);
		$distinctColors = $imageProcessor->getDistinctColors();
		
		$headerContent  = "<p>";
		$headerContent .= "</p>";
		
		return ColorDebugger::showColorCollectionWithImage(
			$imagePath,
			$distinctColors
		);
		
	}
	
	public static function showColorFunctionsTestSuiteResults(string $imagePath, ColorCollection $colorCollection,
															  int $simpleThreshold = 10, int $perceptualThreshold = 20): string {
		
		$result = "";
		
		$equivalencyFunctions = [
			[
				"name" => "simpleThreshold",
				"function" => ColorEquivalencyFunctions::createSimpleThresholdEquivalencyFunction($simpleThreshold),
				"threshold" => $simpleThreshold
			],
			[
				"name" => "humanPerceptual",
				"function" => ColorEquivalencyFunctions::createHumanPerceptualEquivalencyFunction($perceptualThreshold),
				"threshold" => $perceptualThreshold
			],
		];
		
		$mergingFunctions = [
			[
				"name" => "simpleAverage",
				"function" => ColorMergerFunctions::createSimpleAverageMergerFunction()
			],
			[
				"name" => "weightedAverage",
				"function" => ColorMergerFunctions::createWeightedAverageMergerFunction()
			],
			[
				"name" => "useFirst",
				"function" => ColorMergerFunctions::createUseFirstMergerFunction()
			],
		];
		
		foreach ($equivalencyFunctions as $equivalencyFunction) {
			
			$equivalencyFunctionName = $equivalencyFunction["name"];
			$equivalencyFunctionCallable = $equivalencyFunction["function"];
			$equivalencyFunctionThreshold = $equivalencyFunction["threshold"];
			
			foreach ($mergingFunctions as $mergingFunction) {
				
				$mergingFunctionName = $mergingFunction["name"];
				$mergingFunctionCallable = $mergingFunction["function"];
				
				$mergedCollection = $colorCollection->getMergedColorCollection(
					$equivalencyFunctionCallable,
					$mergingFunctionCallable
				);
				
				$result .= ColorDebugger::showColorCollectionWithImage(
					$imagePath,
					$mergedCollection,
					"Using <code>$equivalencyFunctionName($equivalencyFunctionThreshold)</code> for " .
					"equivalency testing and <code>$mergingFunctionName</code> to merge colors."
				);
				
				$result .= "<div style='height: 10px; background: white;'/></div>";
				
			}
			
		}
		
		return $result;
		
	}
	
}