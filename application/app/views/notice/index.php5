<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>公告</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/msg.min.css');?>">
</head>
<body>
    <div class="wrapper msg-list">
        <!--
        <ul class="ui-tab-nav">
            <li class="current" data-type="1">推送活动</li>
            <li class="" data-type="0">网站公告</li>
        </ul>
        -->
        <div class="">       
            <ul class="cp-list">
            <?php if(!empty($result)):?> 
            <?php foreach( $result as $items ): ?>
            <li>
                <a href="javascript:void(0);" onClick="window.location.href='<?php echo $items['url'];?>';">
                    <h2><?php echo $items['title'];?></h2>
                    <time><?php echo date('m-d', $items['addTime']);?></time> 
                </a>
            </li>
            <?php endforeach; ?>
            <?php endif; ?>
            </ul>
            <?php if(empty($result)):?> 
            <div class="wrapper no-data">
                <i class="logo-virtual"></i>
                <p>暂无公告信息</p>
            </div>
            <?php endif;?>
        </div>
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

            //初始化分页
            var cpage = 1;
            var stop = true;

            !(function(){
                // 解决部分手机首次点击无效问题
                $(window).scrollTop(10).scrollTop(0);
                // 切换
                $('.ui-tab-nav').on('tap', 'li', function(){
                    $(this).addClass('current').siblings().removeClass('current');
                    var aLi = $(this).parents('.msg-list').find('.ui-tab-content>li');
                    // $(aLi[$(this).index()]).addClass('current').siblings().removeClass('current');
                    stop = true;
                    cpage = 1;
                    var msgType = 0;

                    // msgType 0:推送活动 1:网站消息

                    $.ajax({
                        type: 'post',
                        url: '/app/notice/ajaxNotice',
                        data: {cpage:cpage,msgType:msgType},
                        // beforeSend: $.loading(),
                        success: function (response) {
                            // showLoading.loading("hide");
                            var response = $.parseJSON(response);
                            if(response.status == '1')
                            {
                                $('.ui-tab-content').html('<ul class="cp-list">' + response.data + '</ul>');
                                stop = true;
                            }else{
                                $('.ui-tab-content').html('<div class="wrapper no-data"><i class="logo-virtual"></i><p>暂无公告信息</p></div>');
                                stop = true;
                            }
                        },
                        error: function () {
                            // showLoading.loading("hide");
                            $.tips({
                                content: '网络异常，请稍后再试',
                                stayTime: 2000
                            })
                        }
                    });
                });
            })()         
            

            $(window).scroll(function() {
                if($(this).scrollTop() + $(window).height() + 10 >= $(document).height() && $(this).scrollTop() > 10) {
                    if(stop == true)
                    {
                        var msgType = 0;
                        var showLoading = $.loading();
                        cpage = cpage + 1;
                        stop = false;
                        $.ajax({
                            type: 'post',
                            url: '/app/notice/ajaxNotice',
                            data: {cpage:cpage,msgType:msgType},
                            // beforeSend: $.loading(),
                            success: function (response) {
                                showLoading.loading("hide");
                                var response = $.parseJSON(response);
                                if(response.status == '1')
                                {
                                    $('.cp-list').append(response.data);
                                    stop = true;
                                }else{
                                    stop = false;
                                    $.tips({
                                        content: response.msg,
                                        stayTime: 2000
                                    })
                                }
                            },
                            error: function () {
                                showLoading.loading("hide");
                                $.tips({
                                    content: '网络异常，请稍后再试',
                                    stayTime: 2000
                                })
                            }
                        });
                    }
                     
                }
            });
            
            // 加载中
            function loading(display){
                if(display){
                    $(".ui-loading-wrap").show();
                }else{
                    $(".ui-loading-wrap").hide();
                }
            }
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>