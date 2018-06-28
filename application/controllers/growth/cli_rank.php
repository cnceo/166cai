<?php

/**
 * 排名计算  每天0点执行一次
 * @date:2017-12-28
 */

class Cli_Rank extends MY_Controller
{
    //成长值排名键名
    private $redisKeyName = "growthRank";
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('growth_cli_model');
        $this->load->model('user_model');
        $this->load->driver('cache', array('adapter' => 'redis'));
    }

    public function index()
    {
        $this->usersToRedis();
        $this->calRank();
    }
    
    /**
     * 计算用户排名打败率
     */
    public function calRank()
    {
        $count = $this->cache->redis->zCard($this->redisKeyName);
        if($count > 0)
        {
            $REDIS = $this->config->item('REDIS');
            $pnum = 100;
            $pages = ceil($count / $pnum);
            $start = 0;
            $stop = $pnum - 1;
            for($i = 0; $i < $pages; $i++)
            {
                $members = $this->cache->redis->zRevrange($this->redisKeyName, $start, $stop);
                $bdata['s_data'] = array();
                $bdata['d_data'] = array();
                if($members)
                {
                    $uids = array_keys($members);
                    foreach ($uids as $key => $uid)
                    {
                        $rank = $start + $key + 1;
                        $rate = round(($count - $rank) / $count, 4) * 10000;
                        array_push($bdata['s_data'], "(?, ?)");
                        array_push($bdata['d_data'], $uid);
                        array_push($bdata['d_data'], $rate);
                        $ukey = "{$REDIS['USER_INFO']}$uid";
                        $this->cache->redis->hSet($ukey, "rank", $rate);
                    }
                }
                if($bdata['s_data'])
                {
                    $this->growth_cli_model->updateUserRank($bdata);
                }
                $start += $pnum;
                $stop += $pnum;
            }
        }
    }
    
    /**
     * 将昨天活动的用户刷到Redis
     */
    public function usersToRedis()
    {
        $start = date('Y-m-d', strtotime("-1 day"));
        $end   = date('Y-m-d H:i:s');
        $users = $this->growth_cli_model->getActiveUsers($start, $end);
        $gradeInfo = $this->getGradeInfo();
        foreach ($users as $uid)
        {
            $uinfo = $this->user_model->getUserInfo($uid);
            $score = $uinfo['grade_value'] + $gradeInfo[$uinfo['grade']];
            $this->cache->redis->zAdd($this->redisKeyName, $score, $uid);
        }
    }
    
    /**
     * 初始化所有用户到Redis排序序列
     */
    public function allusersToRedis()
    {
        //删除原有key值
        $this->cache->redis->delete($this->redisKeyName);
        $gradeInfo = $this->getGradeInfo();
        foreach ($gradeInfo as $grade => $gradeTotal)
        {
            $plimit = 1000;
            $start = 0;
            $users = $this->growth_cli_model->getGrowthUsers($grade, $start, $plimit);
            while ($users)
            {
                foreach ($users as $user)
                {
                    $score = $user['grade_value'] + $gradeTotal;
                    $this->cache->redis->zAdd($this->redisKeyName, $score, $user['uid']);
                }
                
                $start += $plimit;
                $users = $this->growth_cli_model->getGrowthUsers($grade, $start, $plimit);
            }
        }
        
        //计算排名
        $this->calRank();
    }
    
    
    /**
     * 返回等级累计成长值
     * @return number[]
     */
    private function getGradeInfo()
    {
        $data = array();
        $gradeInfo = $this->growth_cli_model->getGradeInfo();
        foreach ($gradeInfo as $value)
        {
            if($value['grade'] == '1')
            {
                $data[$value['grade']] = 0;
            }
            else 
            {
                $data[$value['grade']] = $data[$value['grade'] -1] + $value['value_start'];
            }
        }
        
        return $data;
    }
}