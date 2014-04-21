<?php

namespace msmvc;

/**
 * Class view_html
 * @package msmvc\core
 * @author Dezzpil
 */
class view_html extends view_interface {

    protected $zoneString = '';

    function complete() {

        $this->zoneString = $this->renderZone();
        $viewString = $this->renderTemplate();

        return $viewString;
    }

    protected function renderZone() {

        $path = trim(self::$zonePath);
        if ( ! $path)
            throw new exception_view_emptyzone();

        // рекурсивно рендерим префабы для главной области
        $data = array();
        foreach (self::$includedPrefabs as $var_name => $include_view) {
            $data[$var_name] = self::renderPrefabs($include_view);
        }

        // рендерим главную область
        $this->setViewZoneData(array_merge($data, $this->getViewZoneData()));

        //var_dump($data);die;
        $asset = $this->getAsset() ? $this->getAsset() : view_asset::forge();
        $content = $this->renderParts($path, $asset);

        // возвращается строка
        return $content;
    }

    protected function renderTemplate() {

        ${self::$zoneVarName} = $this->zoneString;

        // рендерим шаблон
        $templateName = trim(self::$templateName);
        if ( ! $templateName)
            throw new exception_view_emptytemplate('TEMPLATE DOESNT SET');

        $templateFilePath = ABS_TEMPLATE_PATH.$templateName.'.php';
        if ( ! is_readable($templateFilePath))
            throw new exception_view_notemplate('NO SUCH FILE FOR TEMPLATE: '.$templateFilePath);

        $this->setCurrentData($this->getViewTemplateDate());
        // подключаем шаблон
        ob_start();
        include_once $templateFilePath;
        $template = ob_get_clean();

        // возвращается строка
        return $template;
    }
}
?>