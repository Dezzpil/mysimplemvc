<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dezzpil
 * Date: 1/5/14
 * Time: 2:48 PM
 * To change this template use File | Settings | File Templates.
 */

namespace msmvc\model;


class charset {

    /**
     * From win1251 to utf8
     * @param array $party_win1251
     * @return array
     */
    static public function ar_iconv($party_win1251)
    {
        array_walk_recursive($party_win1251, function (&$value) {
            $value = iconv('windows-1251', 'utf-8', $value);
        });

        return $party_win1251;
    }

    /**
     * From utf8 to win1251
     * @param array $party_utf8
     * @return array
     */
    static public function ar_iconv_ex($party_utf8)
    {
        array_walk_recursive($party_utf8, function (&$value) {
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
        if (is_array($result)) {
            array_walk_recursive($result, function (&$item) {
                $item = mb_convert_encoding($item, 'UTF-8', 'Windows-1251');
            });
            return $result;
        }

        if (is_string($result)) {
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
        if (is_array($result)) {
            array_walk_recursive($result, function (&$item) {
                $item = mb_convert_encoding($item, 'Windows-1251', 'UTF-8');
            });
            return $result;
        }

        if (is_string($result)) {
            $result = mb_convert_encoding($result, 'Windows-1251', 'UTF-8');
            return $result;
        }

        return FALSE;
    }

}