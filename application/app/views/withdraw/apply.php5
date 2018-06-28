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
    <title>选择银行卡</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/withdraw-money.min.css');?>">
</head>
<body>
    <div class="wrapper bank-list withdraw-list">
        <ul>
            <?php foreach( $bankInfo as $info ): ?>
            <li onClick="window.location.href='<?php echo $this->config->item('pages_url'); ?>app/withdraw/index/<?php echo $token;?>/<?php echo $info['id'];?>';">
                <div class="bankcard-box bankcard-name-<?php echo BanksDetail($info['bank_type'],'st');?> <?php if($info['is_default'] == 1):?>bankcard-default<?php endif;?>">
                    <h2 class="bankcard-hd"><?php echo BanksDetail($info['bank_type'],'name');?></h2>
                    <p class="bankcard-bd"><?php echo substr($info['bank_id'],0,4);?> ***** ***** <?php echo substr($info['bank_id'],-4);?></p>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>