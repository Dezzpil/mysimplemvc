<?php

namespace msmvc\core;

/**
 * Class view_cli
 * @package msmvc\core
 * @author Dezzpil
 */
class view_cli extends view_interface {

    function renderZone() {

        $this->setCurrentData($this->getViewZoneData());

        // буферизируем, чтобы не вывалилось раньше времени
        ob_start();

        // подключаем вид
        $zonePath = ABS_ZONE_PATH.self::$zonePath.'.php';
        if (is_readable($zonePath)) {

            include $zonePath;

        } else {

            var_dump($this->getViewZoneData());

        }

        // возвращается строка
        return ob_get_clean();
    }

    function renderTemplate() {
        // рендерим главное представление
        // в этой переменной храниться область представления
        ${self::$zoneVarName} = $this->renderZone();

        // рендерим шаблон
        $templateName = trim(self::$templateName);
        $templateFilePath = ABS_TEMPLATE_PATH.$templateName.'.php';
        ob_start();
        if ($templateName && is_readable($templateFilePath)) {

            $this->setCurrentData($this->getViewTemplateDate());
            include_once $templateFilePath;

        } else {

            var_dump($this->getViewTemplateDate());

        }

        $template = ob_get_clean();

        // возвращается строка
        return $template;
    }

    function complete() {

        return $this->renderTemplate();
    }
}
?>