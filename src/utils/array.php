<?php

if(!function_exists('array_has')){
	
	function array_has(&$array = array(), $key) {
		if (empty($array) || is_null($key)) return false;

        if (array_key_exists($key, $array)) {
			
			return (is_numeric($array[$key]) || !empty($array[$key]));
		}
		
        foreach (explode('.', $key) as $segment) {
            if(!is_array($array) || ! array_key_exists($segment, $array)) {
                return false;
            }
            $array = &$array[$segment];
        }
		
        return (is_numeric($array) || !empty($array));
	}
}

if(!function_exists('array_set')){
	
	function array_set(&$array = array(), $key, $val) {
		
		if (empty($key)) return;
			
		$current = &$array;
		
		foreach(explode('.', $key) as $segment) {
            if (!array_key_exists($segment, $current)) {
                $current[$segment] = array();
				
            }
			$current = &$current[$segment];	
        }
		
		$current = $val;
	}
}

if(!function_exists('array_get')){
	
	function array_get(&$array = array(), $key, $default = null) {
		if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || ! array_key_exists($segment, $array)) {
                return $default;
            }

            $array = &$array[$segment];
        }

        return $array;
	}
}

