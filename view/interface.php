<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 1/14/14
 * Time: 1:49 AM
 * To change this template use File | Settings | File Templates.
 */

namespace msmvc;
use msmvc\model\arr;

/**
 * Class view_interface
 * @todo написать инструкцию
 * @package msmvc\core
 */
abstract class view_interface {

    static public $templateName;
    static public $templateData = array();
    static public $zoneVarName;

    /**
     * @param $name
     * @param $zoneVarName
     * @param array $data
     * @return $this
     */
    function setViewTemplate($name, $zoneVarName, $data = array()) {
        self::$zoneVarName = $zoneVarName;
        self::$templateName = $name;
        if ( ! empty($data))
            self::$templateData = $data;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    function setViewTemplateData($data) {
        self::$templateData = $data;
        return $this;
    }

    /**
     * @return array
     */
    function getViewTemplateDate() {
        return self::$templateData;
    }

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    function setViewTemplateParam($key, $val) {
        self::$templateData[$key] = $val;
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    function getViewTemplateParam($key) {
        if (array_key_exists($key, self::$templateData) !== false) {
            return self::$templateData[$key];
        } else {
            return null;
        }
    }


    static public $zonePath;
    static public $zoneData = array();

    /**
     * @param $path
     * @param array $data
     * @return $this
     */
    function setViewZone($path, $data = array()) {
        self::$zonePath = $path;
        if ( ! empty($data))
            self::$zoneData = $data;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    function setViewZoneData($data) {
        self::$zoneData = $data;
        return $this;
    }

    /**
     * @return array
     */
    function getViewZoneData() {
        return self::$zoneData;
    }

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    function setViewZoneParam($key, $val) {
        self::$zoneData[$key] = $val;
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    function getViewZoneParam($key) {
        if (array_key_exists($key, self::$zoneData) !== false) {
            return self::$zoneData[$key];
        } else {
            return null;
        }
    }


    static protected $asset = null;

    /**
     * @param view_asset $asset
     * @return $this
     */
    function setAsset(view_asset $asset) {
        self::$asset = $asset;
        return $this;
    }

    /**
     * @return view_asset|null
     */
    function getAsset() {
        return self::$asset;
    }


    static protected $includedPrefabs = array();

    /**
     * @param $var_name
     * @param view_prefab $prefab
     * @return view_prefab
     */
    function includePrefab($var_name, view_prefab $prefab) {

        if (array_key_exists($var_name, self::$includedPrefabs) !== false) {

            if ( ! is_array(self::$includedPrefabs[$var_name])) {

                // преобразуем core\include_view в массив
                $tmp = self::$includedPrefabs[$var_name];
                self::$includedPrefabs[$var_name] = array($tmp);
                self::$includedPrefabs[$var_name][] = $prefab;

            } else {

                self::$includedPrefabs[$var_name][] = $prefab;

            }

        } else {

            self::$includedPrefabs[$var_name] = $prefab;

        }

        return $prefab;
    }

    /**
     * Превратить область представления в строку
     * используется также и для префабов
     * @param $zoneFileName
     * @param view_asset $asset
     * @return string
     * @throws exception_view_nozone
     */
    protected function renderParts($zoneFileName, view_asset $asset) {

        $zoneFilePath = ABS_ZONE_PATH.$zoneFileName.'.php';
        if ( ! is_readable($zoneFilePath)) {
            throw new exception_view_nozone('NO SUCH FILE: '.$zoneFilePath);
        } else {
            ob_start();
                if ($asset) echo $asset->stringCss();
                require $zoneFilePath;
                if ($asset) echo $asset->stringJs();
            return ob_get_clean();
        }

        // создаем переменную, в которую записываем рендер всего представления

    }

    /**
     * @param view_prefab $prefab
     * @return string
     */
    protected function renderPrefabs(view_prefab $prefab) {

        if (is_array($prefab)) {
            // конкатенируем результат представлений
            $multi_view_content = '';
            foreach ($prefab as $multi_view) {
                $multi_view_content .= $this->renderPrefabs($multi_view);
            }

            return $multi_view_content;
        }

        $includedPrefabs = $prefab->getPrefabs();
        $data = $prefab->getData();
        $asset = $prefab->getAsset() ? $prefab->getAsset() : view_asset::forge();

        if ( ! empty($includedPrefabs)) {

            // идем глубже
            foreach ($includedPrefabs as $var_name => $included_view) {
                $data[$var_name] = $this->renderPrefabs($included_view);
            }

            // возвращаем скомпилированное содержание
            foreach ($data as $key => $val) {
                $this->setViewTemplateParam($key, $val);
            }
            $result = $this->renderParts($prefab->getName(), $asset);
            return $result;

        } else {

            // возвращаем скомпилированное содержание
            foreach ($data as $key => $val) {
                $this->setViewTemplateParam($key, $val);
            }
            return $this->renderParts($prefab->getName(), $asset);
        }
    }


    protected $data;

    /**
     * @param $data
     * @return $this
     */
    function setCurrentData($data) {
        $this->data = $data;
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    function get($key) {
        return arr::get($key, $this->data, null);
    }

    /**
     * @return mixed
     */
    function getAll() {
        return $this->data;
    }

    /**
     * @return string
     */
    abstract protected function renderZone();

    /**
     * @return string
     */
    abstract protected function renderTemplate();

    /**
     * @return mixed
     */
    abstract function complete();


}