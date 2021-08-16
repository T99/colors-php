<?php

include_once "./util/Point.php";
include_once "./util/density.php";
include_once "./ColorCollection.php";

class ColorImageProcessor {
	
	/**
	 * @var string
	 */
	protected $imagePath;
	
	/**
	 * @var Imagick
	 */
	protected $image;
	
	protected function __construct(string $imagePath) {
	
		$this->imagePath = $imagePath;
	
	}
	
	public static function fromURI(string $uri): ColorImageProcessor {
		
		if (substr($uri, 0, 4) === "http") return ColorImageProcessor::fromRemoteFile($uri);
		else return ColorImageProcessor::fromLocalFilePath($uri);
		
	}
	
	public static function fromLocalFilePath(string $imagePath): ColorImageProcessor {
		
		$result = new ColorImageProcessor($imagePath);
		
		if (file_exists($imagePath)) $result->imagePath = $imagePath;
		else throw new Error("Could not find the image file located at: '$imagePath'...");
		
		$result->image = new \Imagick(realpath($result->imagePath));
		
		return $result;
		
	}
	
	public static function fromRemoteFile(string $imagePath): ColorImageProcessor {
		
		$tempFile = tmpfile();
		$tempFileMetaData = stream_get_meta_data($tempFile);
		$tempFileURI = $tempFileMetaData["uri"];
		
		$remoteImage = file_get_contents($imagePath);
		file_put_contents($tempFileURI, $remoteImage);
		
		return ColorImageProcessor::fromLocalFilePath($tempFileURI);
		
	}
	
	public function getImageSize(): Point {
		
		return new Point($this->image->getImageWidth(), $this->image->getImageHeight());
		
	}
	
	/**
	 * Returns a ColorCollection object containing all of the strictly distinct colors found in the input image at the
	 * given absolute density.
	 *
	 * @param float $absoluteDensity The 'absolute density' at which pixels will be extracted from the image. An
	 * absolute density of 'n' means that a pixel will be extracted from the intersection of every n columns and n rows.
	 * @return ColorCollection A ColorCollection object containing all of the strictly distinct colors found in the
	 * input image at the specified density.
	 */
	public function getDistinctColorsByAbsoluteDensity(float $absoluteDensity = 1): ColorCollection {
		
		$imageSize = $this->getImageSize();
		$result = new ColorCollection();
		
		for ($xCoordinate = 0; $xCoordinate < $imageSize->x; $xCoordinate += $absoluteDensity) {
			
			$realXCoordinate = round($xCoordinate);
			
			for ($yCoordinate = 0; $yCoordinate < $imageSize->y; $yCoordinate += $absoluteDensity) {
				
				$realYCoordinate = round($yCoordinate);
				
				$result->addColors(
					Color::fromRGB(
						$this->image->getImagePixelColor($realXCoordinate, $realYCoordinate)->getColorAsString()
					)
				);
				
			}
			
		}
		
		return $result;
	
	}
	
	/**
	 * Returns a ColorCollection object containing all of the strictly distinct colors found in the input image at the
	 * given relative density.
	 *
	 * @param float $relativeDensity The 'relative density' at which pixels will be extracted from the image. For
	 * example, a relative density of '0.65' means that 65% of the source image's pixels will be extracted as colors.
	 * @return ColorCollection A ColorCollection object containing all of the strictly distinct colors found in the
	 * input image at the specified density.
	 */
	public function getDistinctColorsByRelativeDensity(float $relativeDensity): ColorCollection {
		
		return $this->getDistinctColorsByAbsoluteDensity(
			relativeDensityToAbsoluteDensity($relativeDensity)
		);
		
	}
	
	/**
	 * Returns a ColorCollection object containing all of the strictly distinct colors found in the input image, given
	 * the maximum sample size.
	 *
	 * Internally, the maximum sample size is converted to a relative density, which is then converted to an absolute
	 * density. Due to the fact that this conversion process is not entirely lossless, the final number of sampled
	 * pixels will not necessarily match the maximum sample size.
	 *
	 * @param int $maxSampleSize The maximum number of pixels that should be sampled from the image.
	 * @return ColorCollection A ColorCollection object containing all of the strictly distinct colors found in the
	 * input image at the specified density.
	 */
	public function getDistinctColorsByMaximumSampleSize(int $maxSampleSize): ColorCollection {
		
		$imageSize = $this->getImageSize();
		$totalPixelCount = $imageSize->x * $imageSize->y;
		
		return $this->getDistinctColorsByRelativeDensity($maxSampleSize / $totalPixelCount);
		
	}
	
	public function getPrimaryImageColors(): ColorCollection {
		
		// Get up to 50,000 color samples from the image.
		$result = $this->getDistinctColorsByMaximumSampleSize(50000);
		
		// Attempt to remove colors that originated from the background.
		$result = $result->getFilteredColorCollection(
			ColorCollectionFilterFunctions::createEquivalencyFilter(
				$this->guessBackgroundColor(),
				ColorEquivalencyFunctions::createCIE94DeltaEEquivalencyFunction(10)
			),
			true
		);
		
		// Sort the results so that the upcoming merge operation
		$result->sortByIncidence();
		
		// Use a weighted merge to merge colors that are found to have a CIE94 delta-E of 30 or less.
		$result = $result->getMergedColorCollection(
			ColorEquivalencyFunctions::createCIE94DeltaEEquivalencyFunction(30),
			ColorMergerFunctions::createWeightedAverageMergerFunction()
		);
		
		// Limit the results to only colors that comprise at least %5 of the colorspace.
		$result = $result->getFilteredColorCollection(
			ColorCollectionFilterFunctions::createRelativeOccurrenceFilter(0.05)
		);
		
		// Sort the result set by incidence in descending order.
		$result->sortByIncidence();
		
		return $result;
		
	}
	
	public function guessBackgroundColor(): Color {
		
		$colorCollection = new ColorCollection();
		
		$imageWidth = $this->image->getImageWidth();
		$imageHeight = $this->image->getImageHeight();
		
		$pixels = [
			$this->image->getImagePixelColor(0, 0),
			$this->image->getImagePixelColor($imageWidth, 0),
			$this->image->getImagePixelColor(0, $imageHeight),
			$this->image->getImagePixelColor($imageWidth, $imageHeight)
		];
		
		$colorCollection->addColors(...array_map(function(ImagickPixel $pixel): Color {
			
			return Color::fromRGB($pixel->getColorAsString());
			
		}, $pixels));
		
		return $colorCollection->mergeToColor(
			ColorMergerFunctions::createWeightedAverageMergerFunction()
		);
		
	}
	
}