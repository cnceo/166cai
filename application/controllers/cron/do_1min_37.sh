#!/bin/bash
function runlist()
{
#报警脚本
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php warning/cli_alert_log index
##智胜场次信息抓取
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_cfg_zhisheng_match index
#智胜赛事更新
/opt/app/php5/bin/php /opt/case/www.166cai.com/dcenter/index.php cron/cli_zhisheng matchs
##不中包赔参与状态统计
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_activity_jc index
##联盟返点赔率更新
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_rebate index
#红包发放脚本
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_pull_redpack index
#竞彩比分直播推送每一分钟
/opt/app/php5/bin/php /opt/case/www.166cai.com/dcenter/index.php cron/cli_zhisheng_live index
#敏感词过滤脚本
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php sensitive/cli_sensitive_word index
#限号报警报警（197）
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php warning/cli_limit_code index
#统计订单当日订单销量
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_cfg_collect_bonus index
#同名用户注册报警
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_checkusers checkRealname
##统计ios下载量（197）
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_aso_notice index >> /dev/null 2>&1
##移动端首页缓存（197)
#/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_refresh_app_index index >> /dev/null 2>&1
##春节活动脚本（197）
/opt/app/php5/bin/php /opt/case/www.166cai.com/index.php cron/cli_activity_xn >> /dev/null 2>&1
}

fname="$0"
cfname="/opt/case/www.166cai.com/application/logs/plock/${fname##*/}.lock"
{
	flock -xn 3
        [ $? -eq 1 ] && { echo $cfname already started; exit; }
        runlist
} 3<>$cfname

 
