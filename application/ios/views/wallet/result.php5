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
    <title>充值详情</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/pay.min.css');?>">
</head>
<body>
    <div class="wrapper recharge-detail pay-result">
        <div class="recharge-detail-hd mod-result<?php if(!$status){echo '-false';}?>">
            <div class="mod-result-hd">
                <h1 class="recharge-detail-title mod-result-title"><?php if($status): ?>充值成功！<?php else: ?>充值失败！<?php endif; ?></h1>
            </div>
            <div class="mod-result-bd">
                <ul class="cp-list">
                    <li>
                        <div class="cp-form-group">
                            <div class="cp-form-item">
                                <label>充值方式</label>
                                <span><?php echo $payType;?></span>
                            </div>
                            <div class="cp-form-item">
                                <label>充值金额</label>
                                <span><?php echo $money;?>元</span>
                            </div>
                        </div>    
                    </li>
                </ul>
            </div>
            <div class="mod-result-ft">  
                <div class="btn-group">
                    <?php if(!$isChannel): ?>
                    <a href="<?php if($redirectPage == 'order'): ?><?php echo BackToLottery('2', array('url' => $payView, 'pullRefresh' => '1'));?><?php else: ?><?php echo BackToLottery('3'); ?><?php endif; ?>" class="btn btn-confirm">完成</a> 
                    <?php else: ?>
                    <!-- 马甲版 -->
                    <a href="<?php if($redirectPage == 'order'): ?><?php echo BackToLotteryByChannel($channelName, '2', array('url' => $payView, 'pullRefresh' => '1'));?><?php else: ?><?php echo BackToLotteryByChannel($channelName, '3'); ?><?php endif; ?>" class="btn btn-confirm">完成</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>