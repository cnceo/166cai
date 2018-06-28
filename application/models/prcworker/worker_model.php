<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/26
 * Time: 13:53
 */

include_once dirname(__FILE__) . '/worker_base.php';
class worker_model extends worker_base
{
    //异常错误码定义
    protected $errorCodes = [
        'HY000' => 2006,
    ];
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 执行单条sql方法
     * @param unknown $work
     * @return unknown
     */
    public function execute($work)
    {
        try{
            return $this->{$work['db']}->query($work['sql'], $work['data']);
        }catch (Exception $e){
            throw new Exception($e->getMessage(), $this->getCode($e->getCode()));
        }
    }

    /**
     * 开启事物执行多条sql方法
     * @param unknown $work
     * @return boolean
     */
    public function transction($work){
        try{
            $count = 0;
            $this->{$work['db']}->trans_start();
            foreach ($work['sql'] as $in => $sql){
                $re = $this->{$work['db']}->query($sql, $work['data'][$in]);
                if(!$re) break;
                $count++;
            }
            if($count == count($work['sql'])){
                $this->{$work['db']}->trans_complete();
                return true;
            }else{
                $this->{$work['db']}->trans_rollback();
                return false;
            }
        }catch (Exception $e){
            $this->{$work['db']}->trans_rollback();
            throw new Exception($e->getMessage(), $this->getCode($e->getCode()));
            return false;
        }
    }
    
    /**
     * 异常错误映射
     * @param unknown $errorCode
     * @return number
     */
    private function getCode($errorCode)
    {
        
        $code = isset($this->errorCodes[$errorCode]) ? $this->errorCodes[$errorCode] : 500;
        
        return $code;
    }
}