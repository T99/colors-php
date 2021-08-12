<?php

include_once "./Color.php";
include_once "./NumericallyIndexableMap.php";

class ColorCollection implements ArrayAccess, Iterator {
	
	/**
	 * @var NumericallyIndexableMap
	 */
	protected $colorOccurrences;
	
	/**
	 * @param Color[] $colors
	 */
	public function __construct(...$colors) {
		
		$this->colorOccurrences = new NumericallyIndexableMap();
	
		$this->addColors(...$colors);
	
	}
	
	/**
	 * @param Color[] $colors
	 */
	public function addColors(...$colors): void {
		
		foreach ($colors as $color) {
			
			$key = $color->getUniqueColorID();
			
			if ($this->colorOccurrences->has($key)) $this->colorOccurrences->get($key)->increment();
			else $this->colorOccurrences->set($key, new ColorOccurrence($color));
			
		}
		
	}
	
	public function addColorOccurrences(...$colorOccurrences): void {
		
		foreach ($colorOccurrences as $colorOccurrence) {
			
			$key = $colorOccurrence->getColor()->getUniqueColorID();
			
			if ($this->colorOccurrences->has($key)) $this->colorOccurrences->get($key)->increment(count($colorOccurrence));
			else $this->colorOccurrences->set($key, $colorOccurrence);
			
		}
		
	}
	
	/**
	 * @param Color[] $colors
	 */
	public function removeColors(...$colors): void {
		
		$this->colorOccurrences->removeMany(...array_map(function (Color $color): string {

			return $color->getUniqueColorID();

		}, $colors));
	
	}
	
	public function sortByIncidence(bool $descending = true): void {
		
		$this->colorOccurrences->uasort(function (ColorOccurrence $a, ColorOccurrence $b) use ($descending): int {
		
			$comparisonValue = count($a) - count($b);
			
			if ($descending) $comparisonValue *= -1;
			
			return $comparisonValue;
		
		});
		
	}
	
	public function mergeToColor($merger): Color {
		
		return $merger(...$this->colorOccurrences->values())->getColor();
		
	}
	
	/**
	 * Attempts to merge the ColorOccurrences inside this ColorCollection based on the provided '$equivalency' and
	 * '$merger' functions. The '$equivalency' function is used to test whether two colors SHOULD be merged, while the
	 * '$merger' function actually performs the merging operation once the appropriate colors have been gathered.
	 *
	 * Note that this function greedily selects colors to merge, meaning that if a given color is found to be equivalent
	 * with some color in this ColorCollection, no other later colors will be able to merge with it (even though in most
	 * cases the later color that 'wanted' to merge with the aforementioned color would have also been found
	 * equivalent).
	 *
	 * For examples of valid equivalency functions (and a hint as to the correct signature of an equivalency function),
	 * see {@link ColorEquivalencyFunctions}.
	 *
	 * For examples of valid merger functions (and a hint as to the correct signature of a merger function), see
	 * {@link ColorMergerFunctions}.
	 *
	 * @param callable $equivalencyTester The function that will be used to test if two given colors are equivalent.
	 * @param callable $merger The function that will be used to merge a given set of 'equivalent' colors.
	 * @return ColorCollection A ColorCollection that was produced as a result of merging all of the colors of this
	 * ColorCollection that were found to be equivalent.
	 */
	public function getMergedColorCollection(callable $equivalencyTester, callable $merger): ColorCollection {
		
		$result = new ColorCollection();
		
		$colorOccurrences = iterator_to_array($this->colorOccurrences);
		$colorOccurrencesLength = count($colorOccurrences);
	
		for ($i = 0; $i < $colorOccurrencesLength; $i++) {
			
			if (!array_key_exists($i, $colorOccurrences)) continue;
			
			$colorsToMerge = [ $colorOccurrences[$i] ];
			unset($colorOccurrences[$i]);
			
			for ($j = $i + 1; $j < $colorOccurrencesLength; $j++) {
				
				if (!array_key_exists($j, $colorOccurrences)) continue;
				
				$areColorsEquivalent = $equivalencyTester(
					$colorsToMerge[0]->getColor(),
					$colorOccurrences[$j]->getColor()
				);
				
				if ($areColorsEquivalent) {
					
					$colorsToMerge[] = $colorOccurrences[$j];
					unset($colorOccurrences[$j]);
					
				}
				
			}
			
			$result->addColorOccurrences($merger(...$colorsToMerge));
			
		}
		
		return $result;
	
	}
	
	public function getFilteredColorCollection(callable $filterFunction, bool $inverse = false): ColorCollection {
		
		$result = new ColorCollection();
		$totalOccurrences = $this->getOccurrenceCount();
		
		if ($inverse) {
			
			foreach ($this->colorOccurrences as $colorOccurrence) {
				
				if (!$filterFunction($colorOccurrence, $totalOccurrences)) $result->addColorOccurrences($colorOccurrence);
				
			}
			
		} else {
			
			foreach ($this->colorOccurrences as $colorOccurrence) {
				
				if ($filterFunction($colorOccurrence, $totalOccurrences)) $result->addColorOccurrences($colorOccurrence);
				
			}
			
		}
		
		return $result;
		
	}
	
	public function head(int $amount): ColorCollection {
		
		$result = new ColorCollection();
		$resultSize = 0;
		
		foreach ($this->colorOccurrences as $colorOccurrence) {
			
			if ($resultSize >= $amount) return $result;
			else {
				
				$result->addColorOccurrences($colorOccurrence);
				$resultSize++;
				
			}
			
		}
		
		return $result;
		
	}
	
	public function toHTMLColorBlockTable(int $blockSize = null, int $padding = 0): string {
		
		$result = "";
		
		$stylings  = "display: flex;";
		$stylings .= " justify-content: center;";
		$stylings .= " align-items: flex-start;";
		$stylings .= " flex-wrap: wrap;";
		
		$result .= "<div style='$stylings'>";
		
		foreach ($this->colorOccurrences as $colorOccurrence) {
			
			$hexLabel  = "<p style='" . Color::getColorBlockContentStylings() . "'>";
			$hexLabel .= $colorOccurrence->getColor()->toHexString(true);
			$hexLabel .= "</p>";
			
			$result .= "<div style='padding: " . $padding . "px'>";
			$result .= $colorOccurrence->toRGBLabelledHTMLColorBlock($blockSize, $hexLabel);
			$result .= "</div>";
			
		}
		
		$result .= "</div>";
		
		return $result;
		
	}
	
	/**
	 * Returns the grand total number of occurrences of every color in this ColorCollection.
	 *
	 * @return int The grand total number of occurrences of every color in this ColorCollection.
	 */
	public function getOccurrenceCount(): int {
		
		return array_reduce(
			iterator_to_array($this->colorOccurrences),
			function(int $carry, ColorOccurrence $colorOccurrence): int {
				
				return $carry + count($colorOccurrence);
				
			},
			0
		);
		
	}
	
	/**
	 * Returns the number of unique colors contained in this ColorCollection.
	 *
	 * @return int The number of unique colors contained in this ColorCollection.
	 */
	public function getColorCount(): int {
		
		return count($this->colorOccurrences);
		
	}
	
	public function __toString(): string {
		
		$result = "";
		
		foreach ($this->colorOccurrences as $colorOccurrence) $result .= "$colorOccurrence\n";
		
		return $result;
		
	}
	
	// ArrayAccess Implementation Methods:
	
	public function offsetExists($offset): bool {
		
		return $this->colorOccurrences->offsetExists($offset);
		
	}
	
	public function offsetGet($offset): ?ColorOccurrence {
	
		return $this->colorOccurrences->offsetGet($offset);
	
	}
	
	/**
	 * @param null $offset
	 * @param Color $value
	 */
	public function offsetSet($offset, $value): void {
		
		if (is_null($offset)) $this->addColors($value);
		else throw new Error("Cannot set Colors in a ColorCollection by index.");
		
	}
	
	public function offsetUnset($offset): void {
		
		$this->colorOccurrences->offsetUnset($offset);
		
	}
	
	// Iterator Implementation Methods:
	
	public function current(): ColorOccurrence {
		
		return $this->colorOccurrences->current();
		
	}
	
	public function key(): int {
		
		return $this->colorOccurrences->key();
		
	}
	
	public function next(): void {
		
		$this->colorOccurrences->next();
		
	}
	
	public function rewind(): void {
		
		$this->colorOccurrences->rewind();
		
	}
	
	public function valid(): bool {
		
		return $this->colorOccurrences->valid();
		
	}
	
}