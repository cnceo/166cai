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
                   <a href="javascript:;" <?php if($redirectPage == 'order'):?>onclick="window.location.href='<?php echo $payView; ?>'"<?php else: ?>onclick="window.webkit.messageHandlers.goUser.postMessage({});"<?php endif; ?> class="btn btn-confirm">完成</a> 
                </div>
            </div>    
        </div>
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>