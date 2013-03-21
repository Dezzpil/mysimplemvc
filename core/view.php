<?php
/**
 * Description of view
 *
 * @author Dezzpil
 */
class view
{
    static public $template_name;
    static public $view_name;
    
    static public $template_data;
    static public $view_data;
    
    static function set_template($name, $data)
    {
        self::$template_name = $name;
        self::$template_data = $data;
    }
    
    static public $js_array = array();
    
    static function add_js($js_array)
    {
        self::$js_array = $js_array;
    }
    
    static public $css_array = array();
    
    static function add_css($css_array)
    {
        self::$css_array = $css_array;
    }
    
    static function set_view($name, $data)
    {
        self::$view_name = $name;
        self::$view_data = $data;
    }
}

?>
