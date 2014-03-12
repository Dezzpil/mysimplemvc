<?php
namespace msmvc\help;

/**
 * Some common method for array
 * @author Dezzpil
 */
class arr {

    /**
     * Get value of array or false
     * @param $array
     * @param $key
     * @param $default
     * @return mixed
     */
    static function get($key, $array, $default = null) {

        if (array_key_exists($key, $array) !== false) {
            return $array[$key];
        }

        return $default;
    }
    
    /**
     * Returns array with order by given key (if it doesn't exists - returns false)
     * @param array $array
     * @param string $key
     * @return array or false
     */
    static function index_to_key($array, $key) {
        $result = array();
        
        foreach ($array as $index => $item) {
            if (array_key_exists($key, $item)) {
                $result[$item[$key]] = $item;
            } else {
                return false;
            }
        }
        
        return $result;
    }
    
    /**
	 * Convert all values in array into int
	 * @param array $array
	 * @return array or false
	 */
	static function convert_to_int($array) {
		if (empty($array) || !is_array($array))
			return false;

		array_walk($array, function(&$val) {
            $val = (int) $val;
        });

		return $array;
	}
    
    /**
	 * Gives the prefix to all keys of array
	 * @param array $array
	 * @param string $prefix : as default
	 * @return array or false
	 */
	static function add_prefix($array, $prefix = ':') {
		if (empty($array) || !is_array($array))
			return false;
		foreach ($array as $key => $val)
			$params[$prefix.$key] = $val;
		return $params;
	}

    /**
     * Convert array to the array compatible for html <select>
     * @param object $dataset
     * @param $field_id
     * @param $field_name
     * @param bool $first_empty
     * @return array($field_id => $field_name, ... )
     */
    static function to_select_list($dataset, $field_id = 'id', $field_name = 'name', $first_empty = TRUE) {
        $result = array();

        if ($first_empty)
            array_unshift($result, '');

        if (is_array($dataset)) {
            foreach ($dataset as $item) {
                $result[$item[$field_id]] = $item[$field_name];
            }
        } else {
            foreach ($dataset as $item) {
                $result[$item->$field_id] = $item->$field_name;
            }
        }

        return $result;
    }
}
?>