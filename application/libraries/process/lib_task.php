<?php
/**
 * Created by PhpStorm.
 * User: shigaoxing
 */

class lib_task
{
    private $CI = null;
    //处理成功code
    const CODE_SUCESS = 0;
    //需要重试code
    const CODE_RETRY = 1;
    //错误code
    const CODE_ERROR = 3;
    
    private $ctype = [
        'login'=> ['growth'],
        'buyLottery' => ['growth', 'point', 'job'],
    ];
    
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model("prcworker/server_task_model");
    }
    
    public function run($data)
    {
        if(method_exists($this, $data['type']))
        {
            return $this->{$data['type']}($data);
        }
        else
        {
            return ['code' => self::CODE_ERROR, 'msg' => '类型不存在'];
        }
    }
    
    /**
     * 登录任务处理
     * @param unknown $data
     * @return unknown
     */
    private function login($data)
    {
        //检查ctype
        if(!in_array($data['ctype'], $this->ctype['login']))
        {
            return ['code' => self::CODE_ERROR, 'msg' => '业务类型不存在'];
        }
        
        $method = "login". ucfirst($data['ctype']);
        
        return $this->CI->server_task_model->{$method}($data['data']);
    }
    
    /**
     * 购彩任务处理
     * @param unknown $data
     * @return string[]|unknown
     */
    private function buyLottery($data)
    {
        //检查ctype
        if(!in_array($data['ctype'], $this->ctype['buyLottery']))
        {
            return ['code' => self::CODE_ERROR, 'msg' => '业务类型不存在'];
        }
        
        $method = "buyLottery". ucfirst($data['ctype']);
        
        return $this->CI->server_task_model->{$method}($data['data']);
    }
}
