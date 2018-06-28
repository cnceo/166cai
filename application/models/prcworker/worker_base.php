<?php
/**
 * Created by PhpStorm.
 * User: huxiaoming
 * Date: 2017/9/26
 * Time: 13:44
 */

class worker_base extends CI_Model
{
    protected $DB = null;
    protected $CF = null;
    public function __construct()
    {
        parent::__construct();
    }

    public function init($params)
    {
        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }
    }
}