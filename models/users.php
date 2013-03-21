<?php

/**
 * Model for list of users
 * @author Dezzpil
 */
class model_users
{   
    protected $tbl_name = 'users';
    
    const AUTH_KEY = 'auth_is';
    const AUTH_TIMESTAMP_KEY = 'auth_timestamp';
    const AUTH_USER_PREFIX = 'u_';
    
    /**
     * 0 - нет, 1 - да
     * @return int
     */
    static function is_login()
    {
        return (int) session::get(self::AUTH_USER_PREFIX.self::AUTH_KEY);
    }
    
    /**
     * -1 - нет пользователя с ук. логином
     * 0 - ошибка в пароле
     * 1 - успех
     * 
     * @param string $login
     * @param string $password
     * @return int
     */
    function login($login, $password)
    {
        // берем из бд
        $login = mysql_real_escape_string($login);
        
        $props = model_user::get_props();
        $props_str = join(', ', $props);
        
        $query = "select $props_str, password from $this->tbl_name where login = '$login' limit 1";
        $result = db::instance()->query($query);
        
        if (empty($result)) return -1;
		
        if ($result[0]['password'] === $this->get_password_hash($password))
        {
            // сохраняем данные в сессии
            $session = session::instance();
            foreach ($result['0'] as $key => $val)
            {
                $session->set(self::AUTH_USER_PREFIX.$key, $val);
            }
            $session->set(self::AUTH_USER_PREFIX.self::AUTH_KEY, 1);
            $session->set(self::AUTH_USER_PREFIX.self::AUTH_TIMESTAMP_KEY, time());
            
            return 1;
        }
        
        return 0;      
    }
    
    function logout()
    {
        // текущий авторизованный пользователь
        $session = session::instance();
        $session_data = $session->get();
        foreach ($session_data as $key => $value)
        {
            // данные пользователя
            if (stripos($key, model_users::AUTH_USER_PREFIX) === 0)
            {
                $session->remove($key);
            }
        }
        
        $session->set(model_users::AUTH_USER_PREFIX.self::AUTH_KEY, 0);
    }
    
    // соль для паролей пользователей
    const USER_PASS_SALT = '0_Poor_Yor1k!';
    
    function get_password_hash($password)
    {
        $password = self::USER_PASS_SALT.$password;
        return sha1(sha1($password).self::USER_PASS_SALT);
    }
    
    function add_user($login, $password)
    {
        // шифрование пароля
        $password = mysql_real_escape_string($password);
        $pass_hash = $this->get_password_hash($password);
        
        // записываем
        $query = "insert into $this->tbl_name (`login`, `password`, `date_created`) 
            values ('$login', '$pass_hash', '".date('Y-m-d')."')";
        $id = db::instance()->query($query);
        
        return $id;
    }
    
    function edit_user($name, $surname, $patronymic, $icon)
    {
        //
    }
    
    function remove_user($id)
    {
        //
    }
    
    function get_list()
    {
        $query = "select * from {$this->tbl_name}";
        $result = $this->db->query($query);
        
        foreach ($result as $user)
        {
            unset($user['password']);
            $users[$user['id']] = $user;
        }

        return $users;
    }
}

?>