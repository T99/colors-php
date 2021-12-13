<?php

class ColorNode {
	
	public $left;
	
	public $right;
	
	public $hash;
	
	public $colorName;
	
}

class ColorNameTree {
	
	/**
	 * @var ColorNode
	 */
	protected $root;
	
	public function __construct() {
		
		$requiredLevels = ceil(log(ColorNameTree::hashColor(Color::white()), 2));
		
		$this->root = new ColorNode();
		
		$this->root->left = null;
		$this->root->right = null;
		$this->root->hash = pow(2, $requiredLevels) / 2;
		
		$previousLeafNodes = [$this->root];
		$newLeafNodes = [];
		
		for ($i = 1; $i < $requiredLevels; $i++) {
		
			foreach ($previousLeafNode as $previousLeafNodes) {
			
			
			
			}
		
		}
		
	}
	
	public static function fromXKCDColorNames(string $filepath): ColorNameTree {
		
		$rawFileText = file_get_contents($filepath);
		$lines = explode("\n", $rawFileText);
		
	}
	
	protected static function determineLineBreakStyle(string $input): string {
		
		$indexOfLineFeed = input.indexOf("\n");
		
		// Line-feed character not found...
		if (indexOfLineFeed === -1) {
			
			// If we can find a carriage-return character, that must be the intended line-break style.
			if (input.indexOf("\r") !== -1) return "\r";
			
			// No line break characters found... default to "\r\n" because that is the line-break in the spec.
			else return "\r\n";
			
			// Found a line-feed character...
		} else {
	
			// We found a "\r\n" character sequence, that must be the intended line-break style.
			if (input.charAt(indexOfLineFeed - 1) === "\r") return "\r\n";
			
			// The line-feed character we found was not preceded by a carriage-return, so the intended line-break
			// style must just be a plain line-feed character.
			else return "\n";
			
		}
		
	}
	
	protected function search(Color $color, ColorNode $fromNode = null): ?ColorNode {
		
		if (is_null($fromNode)) $fromNode = $this->root;
		
		$colorHash = ColorNameTree::hashColor($color);
		
		if ($colorHash === $fromNode->hash) return $fromNode;
		else if ($colorHash < $fromNode->hash && !is_null($fromNode->left)) return $this->search($color, $fromNode->left);
		else if ($colorHash > $fromNode->hash && !is_null($fromNode->right)) return $this->search($color, $fromNode->right);
		else return null;
		
	}
	
	public function setColorName(Color $color, string $name): void {
	
		$this->search($color)->colorName = $name;
	
	}
	
	protected static function hashColor(Color $color): int {
		
		$redHex = str_pad(dechex($color->getRed()), 2, "0", STR_PAD_LEFT);
		$greenHex = str_pad(dechex($color->getGreen()), 2, "0", STR_PAD_LEFT);
		$blueHex = str_pad(dechex($color->getBlue()), 2, "0", STR_PAD_LEFT);
		
		return hexdec($redHex . $greenHex . $blueHex);
		
	}

}