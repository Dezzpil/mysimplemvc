<?
namespace help;

/**
 * Tools
 * unsorted methods, that easy hard gray life
 */
abstract class xhelp
{
	/**
	 * Copy folder with it subfolders
	 * @param string $from_path
	 * @param string $to_path
	 */
	static public function copy_dir($from_path, $to_path)
	{
		mkdir($to_path, 0777);

		if (is_dir($from_path))
		{
			chdir($from_path);
			$handle = opendir('.');
			while (($file = readdir($handle)) !== false)
			{
				if (($file != ".") && ($file != ".."))
				{
					if (is_dir($file))
					{
						rec_copy($from_path.$file."/", $to_path.$file."/");
						chdir($from_path);
					}
				}
				if (is_file($file))
				{
					copy($from_path.$file, $to_path.$file);
				}
			}
		}

		if (!empty($handle))
		{
			closedir($handle);
		}
	}

	/**
     * From win1251 to utf8
	 * @param array $party_win1251
	 * @return array
	 */
	static public function ar_iconv($party_win1251)
	{
		array_walk_recursive($party_utf8, function (&$value)
        {
            $value = iconv('windows-1251', 'utf-8', $value);
        });

		return $party_utf8;
	}

    /**
     * From utf8 to win1251
	 * @param array $party_utf8
	 * @return array
	 */
	static public function ar_iconv_ex($party_utf8)
	{
		array_walk_recursive($party_utf8, function (&$value)
        {
            $value = iconv('utf-8', 'windows-1251', $value);
        });

		return $party_utf8;
	}

    /**
     * From win1251 to utf8
	 * @param array or string $result
	 * @return array
	 */
	static public function win1251_to_utf8($result)
	{
		if (is_array($result))
		{
			array_walk_recursive($result, function (&$item)
            {
                $item = mb_convert_encoding($item, 'UTF-8', 'Windows-1251');
            });
			return $result;
		}

		if (is_string($result))
		{
			$result = mb_convert_encoding($result, 'UTF-8', 'Windows-1251');
			return $result;
		}

		return FALSE;
	}

    /**
     * From utf8 to win1251
	 * @param array or string $result
	 * @return array
	 */    
	static public function utf8_to_win1251($result)
	{
		if (is_array($result))
		{
			array_walk_recursive($result, function (&$item)
			{
				$item = mb_convert_encoding($item, 'Windows-1251', 'UTF-8');
			});
			return $result;
		}

		if (is_string($result))
		{
			$result = mb_convert_encoding($result, 'Windows-1251', 'UTF-8');
			return $result;
		}

		return FALSE;
	}

	/**
	 * Convert array to the array compatible for html <select>
	 * @param object $dataset
	 * @param $field_id
	 * @param $field_name
	 * @param bool $first_empty
	 * @return array($field_id => $field_name, ... )
	 */
	static function to_select_list($dataset, $field_id = 'id', $field_name = 'name', $first_empty = TRUE)
	{
		$result = array();

		if ($first_empty)
			array_unshift($result, '');

		if (is_array($dataset))
		{
			foreach ($dataset as $item)
			{
				$result[$item[$field_id]] = $item[$field_name];
			}
		}
		else
		{
			foreach ($dataset as $item)
			{
				$result[$item->$field_id] = $item->$field_name;
			}
		}
		return $result;
	}

	/**
	 * Closes all unclosed html tags
	 * @param string $content
	 * @return string
	 */
	static function close_tags($content)
	{
		$position = 0;
		$open_tags = array();
		$ignored_tags = array('br', 'hr', 'img');

		while (($position = strpos($content, '<', $position)) !== FALSE)
		{
			if (preg_match("|^<(/?)([a-z\d]+)\b[^>]*>|i", substr($content, $position), $match))
			{
				$tag = strtolower($match[2]);
				if (in_array($tag, $ignored_tags) == FALSE)
				{
					//тег открыт
					if (isset($match[1]) AND $match[1] == '')
					{
						if (isset($open_tags[$tag]))
							$open_tags[$tag]++;
						else
							$open_tags[$tag] = 1;
					}
					//тег закрыт
					if (isset($match[1]) AND $match[1] == '/')
					{
						if (isset($open_tags[$tag]))
							$open_tags[$tag]--;
					}
				}
				$position += strlen($match[0]);
			}
			else
				$position++;
		}
        
		foreach ($open_tags as $tag => $count_not_closed)
		{
			$content .= str_repeat("</{$tag}>", $count_not_closed);
		}

		return $content;
	}

	/**
	 * Returns cutted string
	 * @param   string    $str
	 * @param   int       $len
	 * @param   string    $after symbols that will be add to the end of cutted string
	 * @return  string 
	 */
	static function cut_string($str, $len, $after = '...')
	{
		$str = (strlen($str) > $len) ? substr($str, 0, $len - 1).$after : $str;
		return $str;
	}
    
    /**
	 * @example $params = array('Valera', 'da best', 123); 
	 * Returns string '\'Valera\', \'da best\', 123'; (if $with_escaping == TRUE)
	 * 
	 * @param array    $params         list of values
	 * @param bool     $with_escaping  [optional] add $escape_symbol to values strings?
	 * @param string   $escape_symbol  escaping symbols
	 * @return string
	 */
	static function make_string($params, $with_escaping = TRUE, $escape_symbol = '\'')
	{
		$values = '';
		foreach ($params as $param)
		{
			if ($with_escaping && is_string($param))
			{
				$values .= $escape_symbol.$param.$escape_symbol.', ';
			}
			else
			{
				$values .= $param.', ';
			}
		}

		$values = substr($values, 0, -2);

		return $values;
	}

	/**
	 * Returns pure debug in <pre></pre>
	 * @param mixed $var 
	 * @return void
	 */
	static function pre($var)
	{
		echo "<pre>";
		$debug = debug_backtrace();
		print_r($debug[0]['file'].' '.$debug[0]['line']);
		echo "<br />";
		print_r($var);
		echo "</pre>";
	}

	/**
	 * Convert ru to translit (рус->en)
	 * @param string $str
	 * @return string
	 */
	static function translit($str)
	{
		$tr = array(
			"А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
			"Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
			"Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
			"О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
			"У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
			"Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
			"Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
			"в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
			"з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
			"м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
			"с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
			"ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
			"ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
			" " => "_", "№" => "N", "#" => "N", "ё" => "e", "Ё" => "E",
			"," => "_", "=" => "_" // < *_*
		);
		return strtr($str, $tr);
	}

	/**
	 * Returns int of full years on 'now' moment. Expected date format - d.m.Y.
	 * @param string $birthday
	 * @param string $now 
	 * @return int or FALSE
	 */
	static function get_full_age($birthday, $now)
	{
		$ar_birthday = explode('.', $birthday);
		$ar_now = explode('.', $now);

		if (count($ar_birthday) < 3 || count($ar_now) < 3)
			return FALSE;

		$age = $ar_now[2] - $ar_birthday[2];
		if ($age <= 0)
			return FALSE;

		if ($ar_birthday[1] < $ar_now[1])
		{
			// in this year birthday date already happened
			return $age;
		}
		elseif ($ar_birthday[1] === $ar_now[1])
		{
			// compare days
			if ($ar_birthday[0] <= $ar_now[0])
				return $age;
			return $age - 1;
		}
		else
		{
			// in this year birthday date may not happened yet
			return $age - 1;
		}
	}
    
}
?>