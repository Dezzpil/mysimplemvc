<?php
/**
 * Контроллер, наследующий от контроллера ядра MVC.
 * Является оберткой над контроллерами системы.
 *
 * Не имеет собственных действий!
 *
 * @author Dezzpil
 */
class controller_top extends core_controller
{ 
    function before()
    {   
		// установить шаблон
        view::set_template('base', $data);
		
        return parent::before();
    }
    
    function after()
    {
        parent::after();
    }
}

?>