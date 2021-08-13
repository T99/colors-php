<?php

include_once "./ColorCollection.php";

class ColorImageProcessor {
	
	protected $imagePath;
	
	public function __construct(string $imagePath) {
	
		if (file_exists($imagePath)) $this->imagePath = $imagePath;
		else throw new Error("Could not find the image file located at: '$imagePath'...");
	
	}
	
	public function forEachPixel(callable $consumer): void {
		
		$image = new \Imagick(realpath($this->imagePath));
		$pixelRowIterator = $image->getPixelIterator();
		$imageSize = new Point($image->getImageWidth(), $image->getImageHeight());
		
		foreach ($pixelRowIterator as $rowIndex => $pixelRow) {
			
			foreach ($pixelRow as $columnIndex => $pixel) {
				
				$coordinates = new Point($columnIndex, $rowIndex);
				
				$consumer($imageSize, $coordinates, $image->getImagePixelColor($coordinates->x, $coordinates->y));
				
			}
			
		}
		
	}
	
	/**
	 * Returns a ColorCollection object containing all of the strictly distinct colors found in the input image.
	 *
	 * @param int $density The 'absolute density' at pixels will be extracted from the image. An absolute density of 'n'
	 * means that a pixel will be extracted from the intersection of every n columns and n rows.
	 * @return ColorCollection A ColorCollection object containing all of the strictly distinct colors found in the
	 * input image at the specified density.
	 */
	public function getDistinctColorsByAbsoluteDensity(int $density = 1): ColorCollection {
		
		$result = new ColorCollection();
		
		$this->forEachPixel(function (Point $imageSize, Point $pixelCoordinates, ImagickPixel $pixel)
		                        use ($density, &$result): void {
			
			if (($pixelCoordinates->x % $density === 0) && ($pixelCoordinates->y % $density === 0)) {
				
				$result->addColors(Color::fromRGB($pixel->getColorAsString()));
				
			}
			
		});
		
		return $result;
	
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