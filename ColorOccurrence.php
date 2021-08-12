<?php

include_once "./Color.php";

class ColorOccurrence implements Countable {
	
	/**
	 * @var int The number of occurrences of the color associated with this ColorOccurrence.
	 */
	protected $count;
	
	/**
	 * @var Color The color associated with this ColorOccurrence.
	 */
	protected $color;
	
	public function __construct(Color $color, int $count = 1) {
		
		$this->count = $count;
		$this->color = $color;
		
	}
	
	public function increment(int $amount = 1): int {
		
		return ($this->count += $amount);
		
	}
	
	public function decrement(int $amount = 1): int {
		
		return ($this->count -= $amount);
		
	}
	
	public function getColor(): Color {
		
		return $this->color;
		
	}
	
	protected function getQuantityLabel(): string {
		
		$stylings = Color::getColorBlockContentStylings();
		$occurrences = number_format($this->count);
		
		return "<p style='$stylings'>($occurrences)</p>";
		
	}
	
	public function toHTMLColorBlock(int $size = null, string $innerContent = ""): string {
		
		$color = $this->getColor();
		
		return $color->toHTMLColorBlock($size, $this->getQuantityLabel() . "\n" . $innerContent);
		
	}
	
	public function toRGBLabelledHTMLColorBlock(int $size = null, string $innerContent = ""): string {
		
		$color = $this->getColor();
		
		return $color->toRGBLabelledHTMLColorBlock($size, $this->getQuantityLabel() . "\n" . $innerContent);
		
	}
	
	public function toHexLabelledHTMLColorBlock(int $size = null, string $innerContent = ""): string {
		
		$count = $this->getColor();
		
		return $count->toHexLabelledHTMLColorBlock($size, $this->getQuantityLabel() . "\n" . $innerContent);
		
	}
	
	public function __toString(): string {
		
		$rgbString = $this->getColor()->toRGBString();
		
		return "$this->count\t$rgbString";
		
	}
	
	// Countable Implementation Methods
	
	public function count(): int {
		
		return $this->count;
		
	}
	
}