<?php

include_once "./ColorCollection.php";

class ImageColorProcessor {
	
	protected $imagePath;
	
	public function __construct(string $imagePath) {
	
		if (file_exists($imagePath)) $this->imagePath = $imagePath;
		else throw new Error("Could not find the image file located at: '$imagePath'...");
	
	}
	
	/**
	 * Returns a ColorCollection object containing all of the strictly distinct colors found in the input image.
	 *
	 * @param int $density The 'density' at pixels will be extracted from the image. A density of 'n' means that a pixel
	 * will be extracted from the intersection of every n columns and every n rows.
	 * @return ColorCollection A ColorCollection object containing all of the strictly distinct colors found in the
	 * input image.
	 */
	public function getDistinctColors(int $density = 1): ColorCollection {
		
		$image = new \Imagick(realpath($this->imagePath));
		$pixelRowIterator = $image->getPixelIterator();
		$colors = new ColorCollection();
	
		foreach ($pixelRowIterator as $rowIndex => $pixelRow) {
	
			if ($rowIndex % $density !== 0) continue;
	
			foreach ($pixelRow as $columnIndex => $pixel) {
	
				if ($columnIndex % $density !== 0) continue;
				
				$colors->addColors(Color::fromRGB($pixel->getColorAsString()));
	
			}
	
		}
		
		$colors->sortByIncidence();
		
		return $colors;
	
	}
	
	public function guessBackgroundColor(): Color {
		
		$image = new \Imagick(realpath($this->imagePath));
		$colorCollection = new ColorCollection();
		
		$imageWidth = $image->getImageWidth();
		$imageHeight = $image->getImageHeight();
		
		$pixels = [
			$image->getImagePixelColor(0, 0),
			$image->getImagePixelColor($imageWidth, 0),
			$image->getImagePixelColor(0, $imageHeight),
			$image->getImagePixelColor($imageWidth, $imageHeight)
		];
		
		$colorCollection->addColors(...array_map(function(ImagickPixel $pixel): Color {
			
			return Color::fromRGB($pixel->getColorAsString());
			
		}, $pixels));
		
		return $colorCollection->mergeToColor(
			ColorMergerFunctions::createWeightedAverageMergerFunction()
		);
		
	}
	
}