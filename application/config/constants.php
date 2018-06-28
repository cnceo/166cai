<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/* End of file constants.php */
/* Location: ./application/config/constants.php */

define('DLT', 23529);
define('SSQ', 51);
define('JCZQ', 42);
define('JCLQ', 43);
define('SYXW', 21406);
define('JXSYXW', 21407);
define('HBSYXW', 21408);
define('FCSD', 52);
define('PLS', 33);
define('KS', 53);
define('JLKS', 56);
define('JXKS', 57);
define('KLPK', 54);
define('CQSSC', 55);
define('PLW', 35);
define('QXC', 10022);
define('QLC', 23528);
define('BJDC', 41);
define('SFC', 11);
define('RJ', 19);
define('GJ', 44);
define('GYJ', 45);
define('HBSYXW', 21408);
define('GDSYXW', 21421);

define('SERVICE_TEL', '400-096-5100');

/* sohu */
define('SOHU_PASS',  'https://api.sohu.com/oauth2/authorize?client_id=cd6c4ccd32e743259b8b3bab297bf81c&response_type=code');
define('SOHU_OAUTH', 'https://api.sohu.com/oauth2/authorize/');
define('SOHU_CLIENT', 'cd6c4ccd32e743259b8b3bab297bf81c');

/* resources version */
define('VER_BUY_CSS', '20141022');
define('VER_BUY_DETAIL_CSS', '20141022');
define('VER_COMMON_CSS', '20141022');
define('VER_DIALOG_CSS', '20141022');
define('VER_DRAWINGS_CSS', '20141022');
define('VER_FIND_PWD_CSS', '20141022');
define('VER_INDEX_CSS', '20141022');
define('VER_LOGIN_CSS', '20141022');
define('VER_LOTTERY_CSS', '20141022');
define('VER_LOTTERY_ZQ_CSS', '20141022');
define('VER_MOBILE_CSS', '20141022');
define('VER_MONEY_CSS', '20141022');
define('VER_MY_LOTTERY_CSS', '20141022');
define('VER_PAY_CSS', '20141022');
define('VER_SOHU_CSS', '20141022');
define('VER_YUN_CSS', '20141022');
define('VER_LOTTERY_LQ_CSS', '20141022');
define('VER_LOTTERY_ZQDG_CSS', '20141022');
define('VER_NEWS_CSS', '20141022');

define('VER_BASE_JS', '20141022');
define('VER_DLT_JS', '20141022');
define('VER_HOME_JS', '20141022');
define('VER_JC_DETAIL_JS', '20141022');
define('VER_JCZQ_JS', '20141022');
define('VER_LOTTERY_JS', '20141022');
define('VER_MATH_JS', '20141022');
define('VER_ORDER_JS', '20141022');
define('VER_SCROLL_JS', '20141022');
define('VER_SSQ_JS', '20141022');
define('VER_SYXW_JS', '20141022');
define('VER_VFORM_JS', '20141022');
define('VER_SFC_JS', '20141022');
define('VER_JCLQ_JS', '20141023');
define('VER_JCZQ_DG_JS', '20141022');
define('VER_LZC_JS', '20141022');