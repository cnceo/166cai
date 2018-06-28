<?php

/*
 * 资讯 模型层
 * @date:2016-09-06
 */

class Info_Model extends MY_Model
{

    // 资讯类型
    public $category = array(
        '1' =>  array(
            'name'  =>  '彩市新闻',
            'lid'   =>  ''
        ),
        '2' =>  array(
            'name'  =>  '双色球',
            'lid'   =>  '51'
        ),
        '3' =>  array(
            'name'  =>  '其他福彩',
            'lid'   =>  ''
        ),
        '4' =>  array(
            'name'  =>  '大乐透',
            'lid'   =>  '23529'
        ),
        '5' =>  array(
            'name'  =>  '其他体彩',
            'lid'   =>  ''
        ),
        '6' =>  array(
            'name'  =>  '竞彩足球',
            'lid'   =>  '42'
        ),
        '7' =>  array(
            'name'  =>  '胜负彩',
            'lid'   =>  '11'
        ),
        '8' =>  array(
            'name'  =>  '竞彩篮球',
            'lid'   =>  '43'
        ),
        '9' =>  array(
            'name'  =>  '专家推荐-足球',
            'lid'   =>  '42'
        ),
        '10' => array(
            'name'  =>  '专家推荐-篮球',
            'lid'   =>  '43'
        ),
    );

    /*
     * 查询资讯类型
     * @date:2016-09-06
     */
    public function getCategory()
    {
        return $this->category;
    }

    /*
     * 查询资讯列表
     * @date:2016-09-06
     */
    public function getInfoList($categoryId, $cpage, $psize)
    {
        $sql = "select id, title, content, is_top, weight, category_id, source_id, submitter, submitter_id, num, likeNum, comNum, is_show, show_time, created from cp_info
        where category_id = ? and platform & 4 and is_show = 1 order by is_top DESC, show_time DESC limit " . ($cpage - 1) * $psize . ", $psize";
        $info = $this->slave->query($sql, array('category_id' => $categoryId))->getAll();
        return $info;
    }

    public function getInfoDetail($id)
    {
        $sql = "select id, title, content, is_top, weight, category_id, source_id, submitter, submitter_id, num, likeNum, comNum, is_show, additions, show_time, created from cp_info
        where platform & 4 and id = ?";
        $info = $this->slave->query($sql, array('id' => $id))->getRow();
        return $info;
    }

    public function updateReadNum($id)
    {
        $sql = "update cp_info set num = num + 1,trueNum = trueNum + 1 where id = ?";
        $this->db->query($sql, array('id' => $id));
    }

}
