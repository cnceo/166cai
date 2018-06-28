#!/bin/bash
function runlist()
{
#页面静态换缓存
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_static_page index
}

fname="$0"
cfname="/opt/case/www.166cai.com/application/logs/plock/${fname##*/}.lock"
{
	flock -xn 3
        [ $? -eq 1 ] && { echo $cfname already started; exit; }
        runlist
} 3<>$cfname

 
