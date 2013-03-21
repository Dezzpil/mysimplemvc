<?php
/**
 * Description of user
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class model_user
{
    static private $props = array('id', 'name', 'surname', 'patronymic', 
        'login', 'icon', 'date_created', 'date_changed', 'deleted',
        'email', 'confirmed');
    
    static function get_props()
    {
        return self::$props;
    }
    
    protected $tbl_name = 'users';
    protected $undefined_name = 'Неизвестный';
    protected $changed_props = array();
    
    static protected $current_user = null;
    static function get_current()
    {
        if (self::$current_user === null)
        {
            self::$current_user = new self;
        }
        return self::$current_user;
    }
    
    /**
     * @param mixed $key
     * @param mixed $value
     */
    function __construct($key = '', $value = '')
    {
        if ( ! $key)
        {
            $session_data = session::instance()->get();
            foreach ($session_data as $key => $value)
            {
                if (stripos($key, model_users::AUTH_USER_PREFIX) === 0)
                {
                    $key = str_replace(model_users::AUTH_USER_PREFIX, '', $key);
                    $this->$key = $value;
                }
            }
            $this->changed_props = array();
        }
        else
        {
            $key = mysql_real_escape_string($key);
            $query = "select ".join(', ', self::$props)." 
                from ".$this->tbl_name." 
                where $key = $value limit 1";
            
            $result = db::instance()->query($query);
            if ( ! empty($result))
            {
                foreach ($result[0] as $key => $value)
                {
                    $this->$key = $value;
                }
            }
            else
            {
                throw new Exception("Пользователя с $key = $value не существует");
            }
        }
    }
    
    public function __set($name, $value)
    {
        // save prop name to list of changed props
        // we will clear this list in save() method
        $this->changed_props[] = $name;
        if ( ! is_numeric($value))
        {
            $this->$name = mysql_real_escape_string($value);
        }
        else
        {
            $this->$name = $value;
        }
    }
    
    function get_full_name()
    {
        $full_name = trim($this->surname.' '.$this->name.' '.$this->patronymic);
        if (empty($full_name)) $full_name = $this->undefined_name;
        return $full_name;
    }
    
    function get_initials_name()
    {
        $fname = $this->get_full_name();
        if ($fname === $this->undefined_name) return $fname;
        
        $initials_name = $this->surname.' ';
        if ( ! empty($this->name)) $initials_name .= $this->name[0].'. ';
        if ( ! empty($this->patronymic)) $initials_name .= $this->patronymic[0].'. ';
        return $initials_name;
    }
    
    function update()
    {
        $query = "update $this->tbl_name set ";
        tools::pre($this->changed_props);
        foreach ($this->changed_props as $prop)
        {
            if ($prop === 'id') continue;

            if (is_numeric($this->$prop))
            {
                $query .= "`$prop`=".$this->$prop." ";
            }
            else
            {
                $query .= "`$prop`='".$this->$prop."' ";
            }
        }
        $query .= "where id=".$this->id;
        
        $this->changed_props = array();
        
        tools::pre($query);die;
    }
}

?>