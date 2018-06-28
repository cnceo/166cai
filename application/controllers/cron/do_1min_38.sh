#!/bin/bash
function runlist()
{
#销售票商分配缓存
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_sellers_dispatch index
#增加刷新非高频彩缓存的频率
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_refresh_cache index
##自动提现
/opt/app/php7/bin/php /opt/case/www.166cai.com8080/index.php transactions/cli_transactions_withdraw index
}

fname="$0"
cfname="/opt/case/www.166cai.com/application/logs/plock/${fname##*/}.lock"
{
	flock -xn 3
        [ $? -eq 1 ] && { echo $cfname already started; exit; }
        runlist
} 3<>$cfname

 
