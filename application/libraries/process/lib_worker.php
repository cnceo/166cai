<?php
/**
 * Created by PhpStorm.
 * User: huxiaoming
 * Date: 2017/9/26
 * Time: 11:11
 */

class lib_worker
{
    private $CI = null;
    private $DB = null;
    private $CF = null;
    public function __construct($params = array())
    {
        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }
        $this->CI = & get_instance();
    }

    public function __get($name)
    {
        if(!$this->CI->{$name}){
            if(file_exists(APPPATH . "/models/prcworker/{$name}.php")){
                $this->CI->load->model("prcworker/{$name}");
                $this->CI->{$name}->init(array('DB' => $this->DB, 'CF' => $this->CF));
            }else{
                throw new Exception("Fatal error: Model {$name} is not exist!", 500);
                return false;
            }
        }
        return $this->CI->{$name};
    }
}