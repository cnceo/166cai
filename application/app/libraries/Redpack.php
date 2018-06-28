<?php

/**
 * 红包解析
 * @author Liuli
 *
 */

class Redpack
{
    private $redpackStatus = array(
        'RECEIVED' => 0,
        'ACTIVE' => 1,
        'USED' => 2
    );

    // 获取有效期内可用红包
    public function getActive($redpackData)
    {
        $redpacks = array();
        if(!empty($redpackData))
        {
            foreach ($redpackData as $items) 
            {
                if( $items['status'] == $this->redpackStatus['ACTIVE'] && date('Y-m-d H:i:s') >= $items['valid_start'] && date('Y-m-d H:i:s') <= $items['valid_end'] )
                {
                    array_push($redpacks, $items);
                }
            }
        }
        return $redpacks;
    }
}