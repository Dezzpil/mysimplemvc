<?php
namespace core;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of assets
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class asset {
    //put your code here
    
    public $js_array = array();
    function add_js($js_link)
    {
        $this->js_array[] = $js_link;
        return $this;
    }
    
    public $css_array = array();
    function add_css($css_link)
    {
        $this->css_array[] = $css_link;
        return $this;
    }
    
    function string_css() {
        $css_str = '';
        foreach ($this->css_array as $css) {
            $css_str .= '<link rel="stylesheet" type="text/css" href="'.$css.'" />'.PHP_EOL;
        }
        
        return $css_str;
    }
    
    function string_js() {
        $js_str = '';
        foreach ($this->js_array as $js) {
            $js_str .= '<script type="text/javascript" src="'.$js.'"></script>'.PHP_EOL;
        }
        
        return $js_str;
    }
}

?>
