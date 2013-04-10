<?php
/**
 * Общий наследумый класс исключений для моделей
 * модуля оформления контрактов
 * 
 * @package contract module
 *
 * @author Nikita Dezzpil Orlov
 */
class mvc_exception extends Exception
{   
    protected $message;
    protected $homepage;
    
    protected $_vars = '';

    public function __construct($message, array $variables = NULL) 
    {
        $this->homepage = ROOT_PATH;
        $this->message = $message;

        $this->parse_array($variables);
        echo $this;
        die;
    }
    
    private function parse_array($variables)
    {
        // парсим массив в строку
        if ($variables)
        {
            foreach ($variables as $key => $val)
            {
                $data = "[$key] => $val\n";
                if (is_object($val)) 
                {
                    // мб это ORM ?
                    try { 
                        $val = $val->as_array(); 
                    } catch(Exception $e) {
                        //
                    }
                    
                    $val = (array) $val;
                }
                if (is_array($val))
                {
                    // а это двумерный массив!
                    foreach ($val as $subkey => $subval)
                    {
                       $data .= "    [$subkey] => $subval\n";
                    }
                }
                $this->_vars .= "\n".$data;
            }
        }
    }
    
    public function __toString()
    {
        $html = "
            <html>
                <head>
                    <style type='text/css'>
                        div.exception {
                            height: 100%;
                            padding-left: 505px;
                            padding-right: 35px;
                            background: url(".IMAGES_PATH."browsers/404.jpg) no-repeat left top;
                            padding-top: 30px;
                            font-family: Georgia, serif;
                            clear: both;
                            display: block;
                        }
                        div.exception:before {
                            content: 'возникла ошибка';
                            font-size: 100px;
                            font-family: serif;
                            display: block;
                            letter-spacing: -5px;
                            color: #233;
                            clear: both;
                            width: 100%;
                            margin-left: -10px;
                        }
                        pre { font-size:11px; }
                    </style>
                </head>
                <body>
                    <div class='exception'>
                        $this->message
                        <pre>
                            $this->_vars
                        </pre>
                    </div>
                </body>
            </html>";
        
        return $html;
    }
}
?>
