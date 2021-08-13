<?php

function matrixToString(array $matrix, int $padding, string $separator = " "): string {

	$result = "";
	
	for ($rowIndex = 0; $rowIndex < count($matrix); $rowIndex++) {
		
		if ($rowIndex !== 0) $result .= "\n";
		
		for ($columnIndex = 0; $columnIndex < count($matrix[$rowIndex]); $columnIndex++) {
			
			if ($columnIndex !== 0) $result .= $separator;
			
			$result .= str_pad($matrix[$rowIndex][$columnIndex], $padding, " ", STR_PAD_LEFT);
			
		}
	
	}
	
	return $result;

}

function dotProduct(array $matrix1, array $matrix2, bool $skipErrorChecking = false): array {
	
	$matrix1RowCount = count($matrix1);
	$matrix2RowCount = count($matrix2);
	
	if ($skipErrorChecking) {
		
		$matrix1ColumnCount = count($matrix1[0]);
		$matrix2ColumnCount = count($matrix2[0]);
		
	} else {
		
		if ($matrix1RowCount <= 0) {
			
			if ($matrix2RowCount <= 0) return [];
			else return false;
			
		}
		
		$matrix1ColumnCount = count($matrix1[0]);
		
		for ($i = 1; $i < $matrix1RowCount; $i++) {
			
			if (count($matrix1[$i]) !== $matrix1ColumnCount) {
				
				throw new Error(
					"Failed to produce the dot product where matrix1 had an inconsistent number of columns."
				);
				
			}
			
		}
		
		$matrix2ColumnCount = count($matrix2[0]);
		
		for ($i = 1; $i < $matrix2RowCount; $i++) {
			
			if (count($matrix2[$i]) !== $matrix2ColumnCount) {
				
				throw new Error(
					"Failed to produce the dot product where matrix2 had an inconsistent number of columns."
				);
				
			}
			
		}
		
		if ($matrix1ColumnCount !== $matrix2RowCount) {
			
			throw new Error("Cannot produce the dot product of matrices of unmatching sizes.");
			
		}
		
	}
	
	$result = [];
	
	for ($matrix1RowIndex = 0; $matrix1RowIndex < $matrix1RowCount; $matrix1RowIndex++) {
		
		for ($matrix2ColumnIndex = 0; $matrix2ColumnIndex < $matrix2ColumnCount; $matrix2ColumnIndex++) {
			
			$result[$matrix1RowIndex][$matrix2ColumnIndex] = 0;
			
			for ($summationIndex = 0; $summationIndex < $matrix1ColumnCount; $summationIndex++) {
				
				$result[$matrix1RowIndex][$matrix2ColumnIndex] +=
					$matrix1[$matrix1RowIndex][$summationIndex] * $matrix2[$summationIndex][$matrix2ColumnIndex];
				
			}
			
		}
		
	}
	
	return $result;
	
}
