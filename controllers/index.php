<?php

/**
 * Контроллер главной страницы
 * @author Dezzpil
 */
class controller_index extends controller_top
{
    protected $name = 'index';
    
    function before()
    {   
        parent::before();
    }
    
    function index()
    {
        $data = array(
			'greeteings' => 'Hello, world',
			'foo' => 'bar'
		);
        
        // ниже следует пример добавления js и less файлов для представления
        $data[parent::KEY_ARRAY_JS][] = ZONE_PATH.'/'.$this->name.'/index.js';
        $data[parent::KEY_ARRAY_LESS][] = ZONE_PATH.'/'.$this->name.'/index.css';
        
        view::set_view('index', $data);
    }
    
    function after()
    {
        parent::after();
    } 
}

?>
