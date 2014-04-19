<?php

namespace msmvc;

/**
 * Общий наследумый класс исключений для моделей
 * модуля оформления контрактов
 * 
 * @package contract module
 * @todo make often http errors and add headers to response
 * @author Nikita Dezzpil Orlov
 */
class exception_mvc extends \Exception {

    protected $message;
    protected $homepage;
    protected $vars = '';

    const ERROR_404 = 404;
    const ERROR_403 = 403;

    public function __construct($message, array $variables = NULL) {

        switch ($message) {

            case self::ERROR_404 :
                include(ABS_ERROR_PATH.'404.html'); die;
            case self::ERROR_403 :
                include(ABS_ERROR_PATH.'403.html'); die;

            default :
                $this->homepage = ROOT_PATH;
                $this->message = $message;
                $this->vars = $variables;
                die($this);
        }
    }

    public function __toString() {
        $html = "<b>MSMVC Error</b>";
        ob_start();
        var_dump($this);
        $html .= ob_get_clean();
        return $html;
    }
}
?>
