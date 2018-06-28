<!doctype html>
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>更多通知</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/help.min.css');?>">
</head>
<body>
    <div class="wrapper sMessge">
        <!-- <ul class="cp-list cp-list-senior">
            <li class="cp-list-txt">
                <h4>出票成功短信</h4>
                <label class="ui-switch" data-index="msg_send">
                    <input <?php echo ($msg_send)?'checked':''; ?> type="checkbox" name="msg_send">
                </label>
            </li>
        </ul> -->
        <ul class="cp-list cp-list-senior">
            <li class="cp-list-txt">
                <div class="cp-list-txt">
                    <h4 class="cell-l">出票成功邮件</h4>
                    <label class="cell-r ui-switch" data-index="email_send">
                        <input <?php echo ($email_send)?'checked':''; ?> type="checkbox" name="email_send">
                    </label>
                </div>    
            </li>
        </ul>
        <p class="tips">打开『出票成功邮件』将在订单成功出票后给您发送一封邮件，邮件内容包含方案信息、出票状态。</p>
        <ul class="cp-list cp-list-senior">
            <li>
                <div class="cp-list-txt">
                    <h4 class="cell-l">中奖短信通知</h4>
                    <label class="cell-r ui-switch" data-index="win_prize">
                        <input <?php echo ($win_prize)?'checked':''; ?> type="checkbox" name="win_prize">
                    </label>
                </div>
            </li>
        </ul>
        <p class="tips">关闭此通知则不会收到中奖短信。</p>
        <ul class="cp-list cp-list-senior">
            <li>
                <div class="cp-list-txt">
                    <h4 class="cell-l">追号短信通知</h4>
                    <label class="cell-r ui-switch" data-index="chase_prize">
                        <input <?php echo ($chase_prize)?'checked':''; ?> type="checkbox" name="chase_prize">
                    </label>
                </div>
            </li>
        </ul>
        <p class="tips">关闭此通知则不会收到追号短信。</p>
        <ul class="cp-list cp-list-senior">
            <li>
                <div class="cp-list-txt">
                    <h4 class="cell-l">定制跟单短信通知</h4>
                    <label class="cell-r ui-switch" data-index="gendan_prize">
                        <input <?php echo ($gendan_prize)?'checked':''; ?> type="checkbox" name="gendan_prize">
                    </label>
                </div>
            </li>
        </ul>
        <p class="tips">关闭此通知则不会收到定制跟单短信。</p>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>
        // 基础配置
        require.config({
            baseUrl: '//<?php echo DOMAIN;?>/caipiaoimg/static/js',
            paths: {
                "zepto" : "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/zepto.min",
                "frozen": "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/frozen.min",
                'basic':'//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/basic'
            }
        })
        require(['basic', 'ui/loading/src/loading', 'ui/tips/src/tips'], function(basic, loading, tips){

            !(function(){
                var stop = true;
                $('.ui-switch').on('click', 'input', function(){
                    var ctype = $(this).parent().attr('data-index');
                    if(stop){
                        stop = false;
                        var isChecked = $(this).is(':checked');
                        if(isChecked){
                            var status = '1';
                        }else{
                            var status = '0';                 
                        }
                        
                        $.ajax({
                            type: 'post',
                            url: '/app/notice/modifyMessage',
                            data: {ctype:ctype,status:status},
                            success: function (response) {
                                var response = $.parseJSON(response);
                                if(response.status == '1')
                                {
                                    $(this).prop('checked', response.data);
                                    stop = true;
                                }else{
                                    $.tips({
                                        content:response.msg,
                                        stayTime:2000
                                    }).on("tips:hide",function(){
                                        location.reload()
                                    });
                                    stop = true;
                                }
                            },
                            error: function () {
                                $.tips({
                                    content: '网络异常，请稍后再试',
                                    stayTime: 2000
                                });
                                stop = true;
                            }
                        });
                    }
                });
            })()
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>