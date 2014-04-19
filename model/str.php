<?php

namespace msmvc\model;


class str {

    /**
     * Closes all unclosed html tags
     * @param string $content
     * @return string
     */
    static function close_tags($content) {
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

        foreach ($open_tags as $tag => $count_not_closed) {
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
    static function cut_string($str, $len, $after = '...') {
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
    static function make_string($params, $with_escaping = TRUE, $escape_symbol = '\'') {
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
     * Convert ru to translit (рус->en)
     * @param string $str
     * @return string
     */
    static function translit($str) {
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
}