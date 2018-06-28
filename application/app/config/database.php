<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;
if (ENVIRONMENT === 'production')
{
    $db['default']['hostname'] = 'mysql:host=172.16.0.42';

//     $db['slave']['hostname'] = 'mysql:host=172.16.0.40';
    
//     $db['slaveDc'] = array(
//     	'hostname' => 'mysql:host=172.16.0.47',
//     	'database' => 'dwh_cpiao_dc',
//         'username' => 'slavedb_ka5188'
//     );

    $db['slave1'] = array(
        'hostname' => 'mysql:host=172.16.0.47',
        'username' => 'slavedb_ka5188'
    );
    
    $db['slave2'] = array(
        'hostname' => 'mysql:host=172.16.0.48',
        'username' => 'slavedb_ka5188'
    );    
    
    $db['slaveDc1'] = array(
        'hostname' => 'mysql:host=172.16.0.47',
        'database' => 'dwh_cpiao_dc',
        'username' => 'slavedb_ka5188'
    );
    
    $db['slaveDc2'] = array(
        'hostname' => 'mysql:host=172.16.0.48',
        'database' => 'dwh_cpiao_dc',
        'username' => 'slavedb_ka5188'
    );

    $db['slaveCfg'] = array(
        'hostname' => 'mysql:host=172.16.0.41',
        'database' => 'bn_cpiao_cfg',
    );
    
    $db['slaveCfg1'] = array(
		'hostname' => 'mysql:host=172.16.0.44',
		'port'	   => '3307',
		'database' => 'bn_cpiao_cfg',
    );

    $db['dc'] = array(
        'hostname' => 'mysql:host=172.16.0.42',
        'database' => 'dwh_cpiao_dc',
    );

    $db['cfg'] = array(
        'hostname' => 'mysql:host=172.16.0.43',
        'database' => 'bn_cpiao_cfg',
    );

    $db['data'] = array(
        'hostname' => 'mysql:host=172.16.0.44',
        'database' => 'bn_cpiao_data',
        'username' => '166cpiao',
        'password' => 'Kdla5s#YNums%dE',
    );

    $db['tdb'] = array(
    	'hostname' => 'mysql:host=172.16.0.40',
    	'database' => '',
    	'username' => 'rc_status',
    	'password' => 'rc_status',
    );

    $db['tcdb'] = array(
        'hostname' => 'mysql:host=172.16.0.41',
        'database' => '',
    	'username' => 'rc_status',
    	'password' => 'rc_status',
    );
    
}
elseif (ENVIRONMENT === 'checkout')
{
    $db['default']['hostname'] = 'mysql:host=172.16.0.39';
    
//     $db['slave']['hostname'] = 'mysql:host=172.16.0.39';
    $db['slave1']['hostname'] = 'mysql:host=172.16.0.39';
    $db['slave2']['hostname'] = 'mysql:host=172.16.0.39';
    
//     $db['slaveDc'] = array(
//     	'hostname' => 'mysql:host=172.16.0.39',
//     	'database' => 'dwh_cpiao_dc',
//     );
        
    $db['slaveDc1'] = array(
		'hostname' => 'mysql:host=172.16.0.39',
		'database' => 'dwh_cpiao_dc',
    );
    
    $db['slaveDc2'] = array(
        'hostname' => 'mysql:host=172.16.0.39',
        'database' => 'dwh_cpiao_dc',
    );
    
    $db['slaveCfg'] = array(
        'hostname' => 'mysql:host=172.16.0.39',
        'database' => 'bn_cpiao_cfg',
    );
    
    $db['slaveCfg1'] = array(
		'hostname' => 'mysql:host=172.16.0.39',
		'port'	   => '3306',
		'database' => 'bn_cpiao_cfg',
    );
    
    $db['dc'] = array(
        'hostname' => 'mysql:host=172.16.0.39',
        'database' => 'dwh_cpiao_dc',
    );
    
    $db['cfg'] = array(
        'hostname' => 'mysql:host=172.16.0.39',
        'database' => 'bn_cpiao_cfg',
    );

    $db['data'] = array(
        'hostname' => 'mysql:host=172.16.0.39',
        'database' => 'bn_cpiao_data',
    );
    
    $db['tdb'] = array(
    	'hostname' => 'mysql:host=172.16.0.39',
    	'database' => '',
    	'username' => 'rc_status',
    	'password' => 'rc_status'
    );

    $db['tcdb'] = array(
        'hostname' => 'mysql:host=172.16.0.39',
        'database' => '',
    	'username' => 'rc_status',
    	'password' => 'rc_status'
    );
}
else
{
    $pwd = 'KmT7%16#LkdiwAvG';
    
    $db['default'] = array(
        'hostname'  => 'mysql:host=123.59.105.39',
        'password'  => $pwd
    );
    
//     $db['slave'] = array(
//     	'hostname' => 'mysql:host=123.59.105.39',
//         'password'  => $pwd
//     );
    
//     $db['slaveDc'] = array(
//     	'hostname' => 'mysql:host=123.59.105.39',
//     	'database' => 'dwh_cpiao_dc',
//         'password'  => $pwd
//     );
    
    $db['slave1'] = array(
		'hostname' => 'mysql:host=123.59.105.39',
        'password'  => $pwd
    );

    $db['slave2'] = array(
        'hostname' => 'mysql:host=123.59.105.39',
        'password'  => $pwd
    );
    
    $db['slaveDc1'] = array(
		'hostname' => 'mysql:host=123.59.105.39',
		'database' => 'dwh_cpiao_dc',
        'password'  => $pwd
    );
    
    $db['slaveDc2'] = array(
        'hostname' => 'mysql:host=123.59.105.39',
        'database' => 'dwh_cpiao_dc',
        'password'  => $pwd
    );
    
    $db['slaveCfg'] = array(
        'hostname' => 'mysql:host=123.59.105.39',
        'port'	   => '3306',
        'database' => 'bn_cpiao_cfg',
        'password'  => $pwd
    );
    
    $db['slaveCfg1'] = array(
		'hostname' => 'mysql:host=123.59.105.39',
		'port'	   => '3306',
		'database' => 'bn_cpiao_cfg',
        'password'  => $pwd
    );

    $db['dc'] = array(
        'hostname'  => 'mysql:host=123.59.105.39',
        'database'  => 'dwh_cpiao_dc',
        'password'  => $pwd
    );
    
    $db['cfg'] = array(
        'hostname'  => 'mysql:host=123.59.105.39',
        'database'  => 'bn_cpiao_cfg',
        'password'  => $pwd
    );

    $db['data'] = array(
        'hostname'  => 'mysql:host=123.59.105.39',
        'database'  => 'bn_cpiao_data',
        'password'  => $pwd
    );
    
    $db['tdb'] = array(
    	'hostname' => 'mysql:host=123.59.105.39',
    	'database' => '',
    	'username' => 'rc_status',
    	'password' => 'rc_status'
    );

    $db['tcdb'] = array(
        'hostname' => 'mysql:host=123.59.105.39',
        'database' => '',
    	'username' => 'rc_status',
    	'password' => 'rc_status'
    );
}

if(!empty($db))
{
	$dbcomm['username'] = '166cai';
    $dbcomm['password'] = '166cai@mysql';
    $dbcomm['database'] = 'bn_cpiao';
    $dbcomm['dbdriver'] = 'pdo';
    $dbcomm['dbprefix'] = '';
    $dbcomm['pconnect'] = TRUE;
    $dbcomm['db_debug'] = TRUE;
    $dbcomm['cache_on'] = FALSE;
    $dbcomm['cachedir'] = '';
    $dbcomm['char_set'] = 'utf8';
    $dbcomm['dbcollat'] = 'utf8_general_ci';
    $dbcomm['swap_pre'] = '';
    $dbcomm['autoinit'] = TRUE;
    $dbcomm['stricton'] = FALSE;
    foreach ($db as $cname=>$val)
    {
        foreach ($dbcomm  as $key => $val)
        {
            $db[$cname][$key] = !isset($db[$cname][$key]) ? $val : $db[$cname][$key];
        }
    }
}


/* End of file database.php */
/* Location: ./application/config/database.php */