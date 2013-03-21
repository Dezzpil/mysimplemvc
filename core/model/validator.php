<?php

/**
 * Validator
 * 
 * Supports chaining.
 * Use get_errors() for get errors. If errors array is empty,
 * then value is correct.
 * 
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class validator
{
    protected $errors = array();
    
    protected $alphabet_eng = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,r,s,t,q,u,v,w,x,y,z';
    protected $alphabet_ru = 'а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ы,ь,э,ю,я';
    
    /**
     * Get errors array (simple array)
     * @return array
     */
    function get_errors()
    {
        return $this->errors;
    }

    protected $value;
    
    function __construct($value)
    {
        $this->value = $value;
    }
    
    function min_length($min_length)
    {
        if (mb_strlen($this->value, 'Utf-8') < $min_length)
        {
            $this->errors[] = 'Длина текста должна быть больше '.$min_length;
        }
        
        return $this;
    }
    
    function max_length($max_length)
    {
        if (mb_strlen($this->value, 'Utf-8') > $max_length)
        {
            $this->errors[] = 'Длина текста должна быть меньше '.$max_length;
        }
        
        return $this;
    }
    
    function latin_and_symbols($symbols)
    {
        for ($i = 0; $i < mb_strlen($this->value, 'Utf-8'); $i++)
        {
            $letter = $this->value[$i];
            
            if (is_numeric($letter))
            {
                // numeric is good
                continue;
            }
            else
            {
                $alphabet_eng = explode(',', $this->alphabet_eng);
                foreach ($alphabet_eng as $eng_letter)
                {
                    // it's latin
                    $letter = mb_strtolower($letter, 'Utf-8');
                    if ($letter == $eng_letter) continue 2;
                }
                
                for ($s = 0; $s < count($symbols); $s++)
                {
                    // it's letted symbol
                    $symbol = $symbols[$s];
                    if ($letter == $symbol) continue 2;
                }
                
                // oops... error
                $this->errors[] = 'Текст должен состоять из букв латинского алфавита и символов: '.join(', ', $symbols);
                break;
            }
        }
        
        return $this;
    }
    
}
?>