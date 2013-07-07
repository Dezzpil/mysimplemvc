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
    
    static protected $asset = null;
    static protected $include_views = array();
    
    static function set_template($name, $data = array())
    {
        self::$template_name = $name;
        self::$template_data = $data;
    }
    
    static function set_view($name, $data = array())
    {
        self::$view_name = $name;
        self::$view_data = $data;
    }
    
    static function set_asset(core\asset $asset) {
        self::$asset = $asset;
    }
    
    static function include_view($var_name, core\include_view $view) {
        if (array_key_exists($var_name, self::$include_views) !== false)
        {
            if (!is_array(self::$include_views[$var_name])) {
                
                // преобразуем core\include_view в массив
                $tmp = self::$include_views[$var_name];
                self::$include_views[$var_name] = array($tmp);
                self::$include_views[$var_name][] = $view;
                
                
            } else {
                
                self::$include_views[$var_name][] = $view;
                
            }
        }
        else
        {
            self::$include_views[$var_name] = $view;
        }
    }
    
    static function put_view_file($view_name, array $data = array(), core\asset $asset = null) {
        
        // собираем переменные для использования в представлении
        foreach ($data as $key => $val) {
            // создаем переменные из ключей массива
            $$key = $val;
        }
        
        // буферизируем, чтобы не вывалилось раньше времени
        ob_start();
        
        // подключаем вид
        if ($asset) { 
            echo $asset->string_css();
            include ABS_ZONE_PATH.$view_name.EXT;
            echo $asset->string_js();
        }
        else {
            include ABS_ZONE_PATH.$view_name.EXT;
        }
        
        // создаем переменную, в которую записываем рендер всего представления
        return ob_get_clean();
    }
    
    static function render_included($include_view) {
        
        if (is_array($include_view)) 
        {    
            // конкатенируем результат представлений
            $multi_view_content = '';
            foreach ($include_view as $multi_view) {
                $multi_view_content .= self::render_included($multi_view);
            }
            
            return $multi_view_content;
        }
        
        $included_views = $include_view->get_includes();
        
        if ( ! empty($included_views)) {
            
            // идем глубже
            $data = $include_view->get_data();

            foreach ($included_views as $var_name => $included_view) {
                $data[$var_name] = self::render_included($included_view);
            }

            // возвращаем скомпилированное содержание
            return self::put_view_file($include_view->get_name(), $data, $include_view->get_asset());
            
        } else {

            // возвращаем скомпилированное содержание
            return self::put_view_file($include_view->get_name(), $include_view->get_data(), $include_view->get_asset());
        }
    }
    
    static function render() {
        
        $view = trim(self::$view_name);
        if ( ! $view) throw new mvc_exception('use view::set_view($name) for setup view in controller');
        
        // рендерим вложенные представления
        $data = self::$view_data;
        foreach (self::$include_views as $var_name => $include_view) {
            $data[$var_name] = self::render_included($include_view);
        }
        
        // рендерим главное представление
        $content = self::put_view_file($view, $data, self::$asset);

        // рендерим шаблон
        $template = trim(self::$template_name);
        if ( ! $template) throw new mvc_exception('use view::set_template($name) for setup template in controller');
        
        foreach (self::$template_data as $key => $val)
        {
            // создаем переменные из ключей массива
            $$key = $val; 
        }
        
        // подключаем шаблон
        ob_start();
        include ABS_TEMPLATE_PATH.$template.EXT;
        $template = ob_get_clean();

        // удаляем использованные в шаблоне переменные
        foreach (self::$template_data as $key => $val) unset($$key);
        
        return $template;
    }
}
?>