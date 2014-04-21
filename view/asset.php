<?php
namespace msmvc;

/**
 * Description of assets
 * @todo assets is strong typed, change it
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class view_asset {
    
    /**
     * 
     * @return view_asset
     */
    static function forge() {
        return new self;
    }
    

    protected  $jsList = array();

    function addJs($js_link)
    {
        $this->jsList[] = $js_link;
        return $this;
    }

    function stringJs() {
        $js_str = '';
        foreach ($this->jsList as $js) {
            $js_str .= '<script type="text/javascript" src="'.$js.'"></script>'.PHP_EOL;
        }

        return $js_str;
    }


    protected $cssList = array();

    function addCss($css_link)
    {
        $this->cssList[] = $css_link;
        return $this;
    }
    
    function stringCss() {
        $css_str = '';
        foreach ($this->cssList as $css) {
            $css_str .= '<link rel="stylesheet" type="text/css" href="'.$css.'" />'.PHP_EOL;
        }
        
        return $css_str;
    }
}

?>
