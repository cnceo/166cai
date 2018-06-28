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
    <meta http-equiv="Pragma" content="no-cache">
    <title>支付</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/recharge.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/pay.min.css');?>">
</head>
<body>
    <div class="wrapper pay">
        <form id="payForm" action="" method="post" class="cp-form">
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
                                <?php if($ctype != 1){ ?>    
                                <span class="special-color"><?php echo number_format(ParseUnit($buyMoney+$guaranteeAmount, 1), 2) ?>元</span>
                                <small class="hemai-tips">(认购<?php echo number_format(ParseUnit($buyMoney, 1), 2)?>元+保底<?php echo number_format(ParseUnit($guaranteeAmount, 1), 2)?>元)</small>
                                <?php }else{ ?>
                                <span class="special-color"><?php echo number_format(ParseUnit($pay_money, 1), 2); ?>元</span>
                                <?php } ?>
                            </div>
                        <?php }elseif($orderType == 5) {?>
                               <label for="">应付金额</label>
                        	<span class="special-color"><?php echo number_format(ParseUnit($totalMoney, 1), 2); ?>元</span>                
                        <?php }else {?>
                        	<label for="">订单总价</label><span class="special-color"><?php echo $pay_money; ?>元</span>
						<?php }?>
                        </div>
                    </div>
                    
                </li>
            </ul>
            <?php if(isset($redpackMoney) && $redpackMoney > 0): ?>
            <ul class="cp-list">
                <li class="pay-rp has-rp has-rp-cantuse">
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
            <ul class="cp-list">
                <li>
                    <div class="cp-form-item">
                        <label>还需充值</label>
                        <span class="special-color"><?php echo $balance_money; ?>元</span>
                    </div>
                </li>  
            </ul>
            <div class="btn-group">
                <a class="btn btn-block-confirm btn-recharge" href="javascript:void(0);" onclick="window.location.href='<?php echo $rechargeUrl;?>';" >去充值</a>
            </div>
            <?php if($orderType != 5){ ?>
            <p class="cp-tip">付款后，您的订单将会自动分配到空闲的投注站出票</p>
            <p class="tac"><a href="javascript:void(0);" onclick="window.location.href='<?php echo $this->config->item('pages_url'); ?>ios/betstation/allStation/<?php echo $lid;?>';" class="view-site-lnk">查看所有投注站</a></p>
            <?php }else{ ?>
            <p class="cp-tip">发起人发起方案时，系统会按定制时间顺序去认购</p>
            <?php } ?>
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

            //...
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>
