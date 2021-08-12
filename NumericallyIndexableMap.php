<?php

class NumericallyIndexableMap implements Countable, ArrayAccess, Iterator {
	
	protected $map;
	
	protected $keyToIndexMap;
	
	protected $indexToKeyMap;
	
	protected $length;
	
	protected $iteratorPosition;
	
	public function __construct() {
		
		$this->map = [];
		$this->keyToIndexMap = [];
		$this->indexToKeyMap = [];
		$this->length = 0;
		$this->iteratorPosition = 0;
		
	}
	
	protected function rebuildMap(): void {
	
		$this->indexToKeyMap = array_keys($this->map);
		$this->keyToIndexMap = array_flip($this->indexToKeyMap);
		$this->length = count($this->map);
	
	}
	
	public function has($key): bool {
		
		return array_key_exists($key, $this->map);
		
	}
	
	public function get($key) {
		
		if ($this->has($key)) return $this->map[$key];
		else return null;
		
	}
	
	public function set($key, $value): void {
		
		if (!$this->has($key)) {
			
			$this->keyToIndexMap[$key] = $this->length;
			$this->indexToKeyMap[$this->length] = $key;
			$this->length++;
			
		}
		
		$this->map[$key] = $value;
		
	}
	
	public function remove($key) {
		
		if ($this->has($key)) {
			
			$value = $this->map[$key];
			unset($this->map[$key]);
			
			$keyIndex = $this->keyToIndexMap[$key];
			unset($this->keyToIndexMap[$key]);
			
			for ($i = $keyIndex + 1; $i < $this->indexToKeyMap; $i++) $this->indexToKeyMap[$i - 1] = $this->indexToKeyMap[$i];
			
			$this->length--;
			
			unset($this->indexToKeyMap[$this->length - 1]);
			
			return $value;
			
		} else return null;
		
	}
	
	public function removeMany(...$keys): void {
		
		foreach ($keys as $key) {
			
			if ($this->has($key)) {
				
				unset($this->map[$key]);
				
			}
			
			$this->rebuildMap();
			
		}
		
	}
	
	public function hasIndex(int $index): bool {
		
		return array_key_exists($index, $this->indexToKeyMap);
		
	}
	
	public function getIndexKey(int $index) {
		
		if ($this->hasIndex($index)) return $this->indexToKeyMap[$index];
		else return null;
		
	}
	
	public function getIndexValue(int $index) {
		
		if ($this->hasIndex($index)) return $this->map[$this->indexToKeyMap[$index]];
		else return null;
		
	}
	
	public function removeIndex(int $index) {
		
		$this->remove($this->getIndexKey($index));
		
	}
	
	public function keys(): array {
		
		return $this->indexToKeyMap;
		
	}
	
	public function values(): array {
		
		return array_values($this->map);
		
	}
	
	public function indices(): array {
		
		return range(0, $this->length - 1);
		
	}
	
	public function uasort($compareFunction): void {
		
		uasort($this->map, $compareFunction);
		$this->rebuildMap();
		
	}
	
	public function uksort($compareFunction): void {
		
		uksort($this->map, $compareFunction);
		$this->rebuildMap();
	
	}
	
	// Countable Implementation Methods:
	
	public function count(): int {
		
		return $this->length;
		
	}
	
	// ArrayAccess Implementation Methods:
	
	public function offsetExists($offset): bool {
		
		return $this->hasIndex($offset);
		
	}
	
	public function offsetGet($offset) {
		
		return $this->getIndexValue($offset);
		
	}
	
	public function offsetSet($offset, $value): void {
		
		throw new Error("Mutation by index is not supported for NumericallyIndexableMaps.");
		
	}
	
	public function offsetUnset($offset): void {
		
		$this->removeIndex($offset);
		
	}
	
	// Iterator Implementation Methods:
	
	public function current() {
		
		return $this->getIndexValue($this->iteratorPosition);
		
	}
	
	public function key(): int {
		
		return $this->iteratorPosition;
		
	}
	
	public function next(): void {
		
		$this->iteratorPosition++;
		
	}
	
	public function rewind(): void {
		
		$this->iteratorPosition = 0;
		
	}
	
	public function valid(): bool {
		
		return $this->hasIndex($this->iteratorPosition);
		
	}
	
}