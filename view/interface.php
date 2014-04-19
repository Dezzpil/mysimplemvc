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
        return @self::$templateData[$key];
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
        return @self::$zoneData[$key];
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
     * @param $zoneFileName
     * @param array $data
     * @param view_asset $asset
     * @return string
     * @throws exception_view_nozone
     */
    protected function renderParts($zoneFileName, array $data = array(), view_asset $asset = null) {

        $zoneFilePath = ABS_ZONE_PATH.$zoneFileName.'.php';
        if ( ! is_readable($zoneFilePath)) {
            throw new exception_view_nozone();
        }

        $this->setCurrentData($data);

        // буферизируем, чтобы не вывалилось раньше времени
        ob_start();

        // подключаем вид
        if ($asset) {
            echo $asset->stringCss();
            include $zoneFilePath;
            echo $asset->stringJs();
        } else {
            include $zoneFilePath;
        }

        // создаем переменную, в которую записываем рендер всего представления
        return ob_get_clean();
    }

    /**
     * @param $prefab
     * @return string
     */
    protected function renderPrefabs($prefab) {

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

        if ( ! empty($includedPrefabs)) {

            // идем глубже
            foreach ($includedPrefabs as $var_name => $included_view) {
                $data[$var_name] = $this->renderPrefabs($included_view);
            }

            // возвращаем скомпилированное содержание
            $result = $this->renderParts($prefab->getName(), $data, $prefab->getAsset());
            //print_r($result);
            return $result;

        } else {

            // возвращаем скомпилированное содержание
            return $this->renderParts($prefab->getName(), $data, $prefab->getAsset());
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