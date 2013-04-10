<?php
/**
 * Form ajax response
 * @author Nikita Dezzpil Orlov
 */
class ajax
{
    const STATUS_SUCCESS = 'success';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';
    
    static function respond_without_die($msg, $status, $data = FALSE)
    {
        $respond['msg'] = $msg;
        $respond['status'] = $status;
        
        if ($data) $respond['data'] = $data;
        
        echo @json_encode($respond);
    }
    
    static function respond($msg, $status, $data = FALSE)
    {
        self::respond_without_die($msg, $status, $data);
        die;
    }
}
?>