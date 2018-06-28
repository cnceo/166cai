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
    <title>提现详情</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/pay.min.css');?>">
</head>
<body>
    <div class="wrapper trading-detail pay-result">
        <div class="trading-detail-hd mod-result">
            <div class="mod-result-hd">
                <h1 class="trading-detail-title mod-result-title">提现申请已提交 ！</h1>
                <p>预计到账时间：<?php echo $applyTime; ?></p>
            </div>
            <div class="mod-result-bd">
                <ul class="cp-list">
                    <li>
                        <div class="cp-form-group">
                            <div class="cp-form-item">
                                <label>银行卡</label>
                                <span><?php echo substr($bank_id, 0, 1) . '*** **** *' . substr($bank_id, -3);?></span>
                            </div>
                            <div class="cp-form-item">
                                <label>提现金额</label>
                                <span><?php echo $money;?>元</span>
                            </div>
                        </div>    
                    </li>
                </ul>
            </div>
            <div class="mod-result-ft">  
                <div class="btn-group">
                   <a href="javascript:void(0)" onclick="window.webkit.messageHandlers.goUser.postMessage({});" class="btn btn-confirm">完成</a> 
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>