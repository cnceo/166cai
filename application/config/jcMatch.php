<?php
if(!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

// 接口地址
$config['apiUrl'] = 'http://m.api.iuliao.com/';

// 缓存类型
$config['redisList'] = array(
	'LEAGUE_SEASON' => '_league_season:',
	'LEAGUE_SCHEDULE' => '_league_schedule:',
	'LEAGUE_SCORERANK' => '_league_scorerank:',
	'LEAGUE_SHOTRANK' => '_league_shotrank:',
	'JC_MATCHHISTORY' => 'jc_matchhistory:',
	'JC_MATCHDETAIL' => 'jc_matchdetail:',
	'JC_MATCHPLAYER' => 'jc_matchplayer:',
	'JC_MATCHSCORE' => 'jc_matchscore:',
	'JC_MATCHMSGID' => 'jc_matchmsgid:',
	'JC_LIVENEW' => 'jc_livenew:',
	'JC_LIVEDETAIL' => 'jc_livedetail:',
	'ODD_LIST' => 'odd_list:',
	'ODD_DETAIL' => 'odd_detail:',
);

