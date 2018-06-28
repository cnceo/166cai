<?php

class Model_task extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
        $this->cfgDB = $this->load->database('cfg', TRUE);
    }
    
    // 获取所有任务
    public function getRestartTask()
    {
        return $this->slaveCfg1->query("select id, fname, mark, folder, status from cp_task_restart")->getAll();
    }
}
