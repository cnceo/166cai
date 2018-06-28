<?php
if(!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

// 接口地址
$config['apiUrl'] = 'http://m.api.iuliao.com/';

//有料接口
$config['liaoUrl'] = 'http://liao.api.iuliao.com/';
$config['from'] = '166cai';
$config['secretKey'] = '#@!166cai';

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
	'JCLQ_MESSGEID' => 'jclq_messageid:',
	'LEAGUES' => 'league:',
	'JCLQ_ONAMES' => 'jclq_onames:',
	'JCLQ_MATCHONAME' => 'jclq_matchoname:',
	'JCLQ_LEAGUE_SCHEDULE' => 'jclq_league_schedule:',
	'JCLQ_SCORERANK' => 'jclq_scorerank:',
	'JCLQ_MATCHDETAIL' => 'jclq_matchdetail:',
	'JCLQ_MATCHSTATISTICS' => 'jclq_matchstatistics:',
	'JCLQ_MATCHPLAYER' => 'jclq_matchplayer:',
	'JCLQ_MATCHHISTORY' => 'jclq_matchhistory:',
	'JCLQ_LASTMATCH' => 'jclq_lastmatch:',
	'JCLQ_FUTUREMATCH' => 'jclq_futurematch:',
	'JC_ARTERY_COMPANIES' => 'jc_artery_companies:',
	'JCLQ_LIVELIST' => 'jclq_liveList:',
	'JCLQ_ENDLIST' => 'jclq_endlist:',
	'JCLQ_FOLLOW' => 'jclq_follow:',
	'JCZQ_MESSGEID' => 'jczq_messageid:',
	'JCLQ_LIVEMATCH' => 'jclq_livematch:',
	'JCZQ_LIVEMATCH' => 'jczq_livematch:',
);

