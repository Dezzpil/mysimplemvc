<?php

namespace msmvc\core;

/**
 * Контроллер ядра MVC
 * от него должны наследовать рабочий контроллер
 * @author Dezzpil
 */
abstract class controller {

    protected $view;

    /**
     * @return view_interface
     */
    protected function getView() {
        return $this->view;
    }

    /**
     * @param view_interface $view
     */
    final function __construct(view_interface $view) {
        $this->view = $view;
    }

    abstract public function before();
    abstract public function after();
}
?>