<?php

if(!function_exists('app_encode_csv')){
	
	/**
	 * Retorna um CSV apartir de um dados
	 */
	function app_encode_csv(&$rows, $escape = '"', $tab = ';', $break = PHP_EOL) {
		
		$content = '';
		
		while($row = array_pop($rows)){
			
			$content .= $escape . implode($escape.$tab.$escape, $row).$escape . $tab . $break; 
		}
		
		return $content;
	}
}

if(!function_exists('app_decode_csv')){
	
	/**
	 * Retorna um CSV a partir de um dados
	 */
	function app_decode_csv($content = '', $columns = array(), $escape = '"', $tab = ';', $break = PHP_EOL) {
		
		$rows = array();
		
		foreach(explode($break, $content) as $line){
			
			if(empty($line)) continue;
			
			$row = array();
			
			foreach(explode($tab, $line) as $key => $val) {
				
				if(empty($val)) continue;
				
				if(array_key_exists($key, $columns)) {
					$row[$columns[$key]] = trim($val, $escape);
				}
				else {
					$row[] = trim($val, $escape);
				}
			}
			
			$rows[] = $row;
		}
		
		return $rows;
	}
}