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
    <div class="wrapper pay">
        <form class="form-hack cp-form" action="">        
            <ul class="cp-list">
                <li>
                    <div class="cp-form-group">
                        <div class="cp-form-item">
                            <label for="">投注彩种</label>
                            <span><?php if($orderType == '1'): ?>【追号】<?php endif; ?><?php echo $lname; ?><?php if($lid != BetCnName::JCZQ && $lid != BetCnName::JCLQ && $orderType != 5):?>第<?php echo $issue; ?>期<?php endif;?></span>
                            <?php if ($orderType == 4) {echo ($ctype == 1) ? '&nbsp;参与合买' : '&nbsp;发起合买';}?>
                            <?php if ($orderType == 5) {echo '&nbsp;定制跟单';}?>
                        </div>
                        <div class="cp-form-item <?php if ($orderType == 4) {echo 'hemai-inpay'; }?>">
                        <?php if ($orderType == 4) {?>
                        	<label for="">应付金额</label>
                        	<div>
                                <span class="special-color"><?php echo number_format(ParseUnit($pay_money, 1), 2) ?>元</span>
                                <?php if ($ctype == 0) {?><small class="hemai-tips">(认购<?php echo number_format(ParseUnit($buyMoney, 1), 2)?>元+保底<?php echo number_format(ParseUnit($guaranteeAmount, 1), 2)?>元)</small><?php }?>
                            </div>
                        <?php }elseif($orderType == 5) {?>
                               <label for="">应付金额</label>
                        	<span class="special-color"><?php echo number_format(ParseUnit($totalMoney, 1), 2); ?>元</span>
                        <?php }else{ ?>        
                        	<label for="">订单总价</label><span class="special-color"><?php echo $pay_money; ?>元</span>
						<?php }?>
                        </div>
                    </div>
                    
                </li>
            </ul>
            <?php if(isset($redpackMoney) && $redpackMoney > 0): ?>
            <ul class="cp-list">
                <li>
                    <div class="cp-form-item">
                        <label>购彩红包</label>
                         <span><?php echo number_format(ParseUnit($redpackMoney, 1), 2); ?>元</span> 
                    </div>
                </li>
            </ul>
            <?php endif; ?>
            <ul class="cp-list">
                <li>
                    <div class="cp-form-item">
                        <label>账户余额</label>
                        <span><?php echo $account_money; ?>元</span>
                    </div>
                </li>  
            </ul>
            <?php if(isset($redpackMoney) && $redpackMoney > 0): ?>
            <ul class="cp-list">
                <li>
                    <div class="cp-form-item">
                        <label>还需支付</label>
                        <span class="special-color"><?php echo $actual_money; ?>元</span>
                    </div>
                </li>
            </ul>
            <?php endif; ?>
            <div class="btn-group">
            	<a id="formSubmit" class="btn btn-block-confirm" href="javascript:;">
                    <?php if($orderType==4) { echo $ctype == 1 ? '参与合买' : '发起合买'; }
                    elseif($orderType==5){echo '立即定制'; }
                    else{ echo '付款到彩店';}?>
                </a> 
            </div>
            <?php if($orderType != 5){ ?>
            <p class="cp-tip">付款后，您的订单将会自动分配到空闲的投注站出票</p>
            <p class="tac"><a href="<?php echo $this->config->item('pages_url'); ?>app/betstation/allStation/<?php echo $lid;?>" class="view-site-lnk">查看所有投注站</a></p>
            <?php }else{ ?>
            <p class="cp-tip">发起人发起方案时，系统会按定制时间顺序去认购</p>
            <?php } ?>
            <input type='hidden' class='' name='token' value='<?php echo $token;?>'/>
        </form>
    </div>
    
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>

        
        require(['//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/config.js'], function() {

            require(['Zepto', 'basic', 'ui/loading/src/loadingfix', 'ui/tips/src/tipsfix'], function($, basic, loading, tips) {
            	$(function(){
                    window.cx || (window.cx = {});
                    cx.pub_salt = '<?php echo $this->pub_salt?>';
                    cx.rsa_encrypt = function( val ) {
                        var rsa_n = 'B31FD13CCDA7684626351A49159B9FDD';        
                        setMaxDigits(131);
                        var key = new RSAKeyPair("10001", '', rsa_n);
                        return encryptedString(key, val + '<PSALT>' + cx.pub_salt);
                    }
                });
                
                //显示隐藏密码
                $('.ipt-psw').find('a').on('click', function(){
                    var aPsw = $(this).parents('.ipt-psw').find('input');
                    if(aPsw.attr('type') == 'password'){
                        aPsw.attr('type', 'text');
                    }else if(aPsw.attr('type') == 'text'){
                        aPsw.attr('type', 'password');
                    }
                })

                // 虚拟键盘 hack
                $('.form-hack').on('submit', function(){
                    $('#formSubmit').trigger('click');
                    return false;
                })

                var closeTag = true;
                $('#formSubmit').on('click', function(){  
                             
                    var token = $('input[name="token"]').val();

                    if(closeTag)
                    {
                        closeTag = false;
                        var showLoading = $.loading().loading("mask");
                        try{
                            var versionCode = android.getAppVersionCode();
                            // 点击事件
                            android.umengStatistic('webview_pay');
                        }catch(e){
                            var versionCode = 1;
                        }

                        $.ajax({
                            type: "post",
                            url: '/app/wallet/pay',
                            data: {token:token},                   
                            success: function (data) {
                                showLoading.loading("hide");
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
                                        content:data.msg.replace('<i class="icon-font">&#xe611;</i>',''),
                                        stayTime:2000
                                    }).on("tips:hide",function(){
                                        android.goBetListActivity('tag');
                                    });
                                    closeTag = true;
                                }
                                else if(data.status == '3')
                                {
                                    $.tips({
                                        content:data.msg.replace('<i class="icon-font">&#xe611;</i>',''),
                                        stayTime:2000
                                    }).on("tips:hide",function(){
                                        android.goUniteRedUser(0);
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
                                showLoading.loading("hide");
                                $.tips({
                                    content: '网络异常，请稍后再试',
                                    stayTime: 2000
                                })
                            }
                        });  
                    }
                })

            })

        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>