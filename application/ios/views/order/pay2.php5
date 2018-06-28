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
    <title>支付</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/pay.min.css');?>">
</head>
<body>
    <div class="wrapper pay ios">
        <div class="m-header">
            <header>
                <h1>支付</h1>
                <?php if(!$isChannel): ?>
                <a href='<?php echo BackToLottery('0');?>' class="hd-lnk-l">返回客户端</a>
                <?php else: ?>
                <a href='<?php echo BackToLotteryByChannel($channelName, '0');?>' class="hd-lnk-l">返回客户端</a>
                <?php endif; ?>
            </header>
        </div>    
        <form id="payConfirm" action="" class="cp-form no-top"> 
            <div class="cp-form-group box-wave mb30">
                <div class="cp-form-item">
                    <label for="">账户余额:</label>
                    <span><?php echo $account_money; ?>元</span>
                </div>
                <div class="cp-form-item <?php if ($orderType == 4) {echo 'hemai-inpay'; }?>">
				<?php if ($orderType == 4) {?>
                	<label for="">应付金额</label>
                	<div>
                		<span class="special-color"><?php echo number_format(ParseUnit($pay_money, 1), 2) ?>元</span>
                		<?php if ($ctype == 0) {?><small class="hemai-tips">(认购<?php echo number_format(ParseUnit($buyMoney, 1), 2)?>元+保底<?php echo number_format(ParseUnit($guaranteeAmount, 1), 2)?>元)</small><?php }?>
                	</div>
               	<?php }else {?>
                	<label for="">订单金额:</label><span class="special-color"><?php echo $payMoney; ?>元</span>
				<?php }?>
                </div>
                <?php if(isset($redpackMoney) && $redpackMoney > 0): ?>
                <!-- 购彩红包 -->
                <div class="cp-form-item">
                    <label for="">购彩红包:</label>
                    <span class="special-color"><?php echo ParseUnit($redpackMoney, 1); ?>元</span>
                </div>
                <div class="cp-form-item">
                    <label for="">还需支付:</label>
                    <span class="special-color"><?php echo $actual_money ? $actual_money : 0; ?>元</span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="btn-group">
                <a class="btn btn-block-confirm btn-recharge" href="javascript:void(0)" id="pay-confirm">确认支付</a>
            </div>
            <input type='hidden' class='' name='payToken' value='<?php echo $payToken; ?>'/>
        </form>
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

            // 虚拟键盘 hack
            $('#payConfirm').on('submit', function(){
                $('#pay-confirm').trigger('click');
                return false;
            })

            // 查看账户
            var betUrl = '<?php echo (!$isChannel)?BackToLottery("3"):BackToLotteryByChannel($channelName, "0");?>';
            
            var closeTag = true;
            $('#pay-confirm').on('click', function(){  
                         
                var token = $('input[name="payToken"]').val();

                if(closeTag)
                {
                    closeTag = false;

                    // 浮层
                    // var showLoading = $.loading().loading("mask");

                    $.ajax({
                        type: "post",
                        url: '/ios/wallet/pay',
                        data: {token:token},                  
                        success: function (data) {
                            // showLoading.loading("hide");
                            var data = $.parseJSON(data);
                            if(data.status == '1')
                            {
                                window.location.href = data.data;
                                closeTag = true;
                            }
                            else if(data.status == '2')
                            {
                                // 支付失败场景 跳转原生投注列表
                                $.tips({
                                    content:data.msg,
                                    stayTime:2000
                                }).on("tips:hide",function(){
                                    window.location.href = betUrl;
                                });
                                closeTag = true;
                            }
                            else
                            {
                                $.tips({
                                    content:data.msg,
                                    stayTime:2000
                                });
                                closeTag = true;
                            }
                        },
                        error: function () {
                            closeTag = true;
                            // showLoading.loading("hide");
                            $.tips({
                                content: '网络异常，请稍后再试',
                                stayTime: 2000
                            })
                        }
                    });  
                }
            })
        });
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>