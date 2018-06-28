<html>
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection"/>
    <meta content="email=no" name="format-detection"/>
    <title><?php echo $title;?></title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/account-detail.min.css');?>">
</head>
<body>
<div class="wrapper account-list">
    <?php if ( ! empty($orders)): ?>
        <ul class="cp-list">
            <?php foreach ($orders as $order): ?>
                <li <?php echo "onclick=\"window.location.href='" . $order['tradeDetailUrl'] . "'\""; ?>\"">
                    <p><?php echo wallet_ctype($order['ctype'], $order['additions']); ?>
                        <b><span style="color:<?php echo $order['balance'] > 0 ? 'red' : 'green'; ?>"><?php echo $order['balance']; ?></span></b>
                    </p>

                    <p>
                        <time><?php echo date('m-d H:i', strtotime($order['created'])); ?></time>
                        <s>余额<?php echo number_format(ParseUnit($order['umoney'], 1), 2); ?></s>
                    </p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else:?>
    <div class="wrapper no-data">
        <i class="logo-virtual"></i>
        <p>暂无交易记录</p>
    </div>
    <?php endif;?>
    <input type='hidden' class='' name='token' value='<?php echo $token;?>'/>
    <input type='hidden' class='' name='ctype' value='<?php echo $ctype;?>'/>
</div>
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
        require(['zepto', 'basic', 'ui/loading/src/loading', 'ui/tips/src/tips'], function($,basic,loading,tips){
            //初始化分页
            var page = 1;
            var stop = true;
            var token = $('input[name="token"]').val();
            var ctype = $('input[name="ctype"]').val();
            // var showLoading = $.loading();
            // 加载
            $(window).scroll(function() {
                if($(this).scrollTop() + $(window).height() + 10 >= $(document).height() && $(this).scrollTop() > 10) {
                    if(stop == true)
                    {
                        var showLoading = $.loading();
                        page = page + 1;
                        stop = false;
                        $.ajax({
                            type: 'post',
                            url: '/ios/mylottery/ajax_detail',
                            data: {page:page,token:token,ctype:ctype},
                            // beforeSend: $.loading(),
                            success: function (response) {
                                showLoading.loading("hide");
                                var response = $.parseJSON(response);
                                if(response.status == 1)
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

            //加载中
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