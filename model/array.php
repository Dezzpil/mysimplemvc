<?php
namespace help;

/**
 * Some common method for array
 * @author Dezzpil
 */
class marray
{
    /**
     * Get value of array or false
     * @param array $array
     * @param string or int $key
     * @return value or false
     */
    static function get($array, $key)
    {
        if (array_key_exists($key, $array))
        {
            return $array[$key];
        }
        return false;
    }
    
    /**
     * Returns array with order by given key (if it doesn't exists - returns false)
     * @param array $array
     * @param string $key
     * @return array or false
     */
    static function index_to_key($array, $key)
    {
        $result = array();
        
        foreach ($array as $index => $item)
        {
            if (array_key_exists($key, $item))
            {
                $result[$item[$key]] = $item;
            }
            else
            {
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
	static function convert_to_int($array)
	{
		if (empty($array) || !is_array($array))
			return false;

		array_walk($array, function(&$val)
        {
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
	static function add_prefix($array, $prefix = ':')
	{
		if (empty($array) || !is_array($array))
			return false;
		foreach ($array as $key => $val)
			$params[$prefix.$key] = $val;
		return $params;
	}
}
?>