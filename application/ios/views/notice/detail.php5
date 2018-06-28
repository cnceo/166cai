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
    <title>详情</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/msg.min.css');?>">
</head>
<body>
    <article class="wrapper msg-detail txt-bg">
        <header class="msg-detail-hd">
            <h1><?php echo $result['title']; ?></h1>
            <p><time><?php echo date('Y-m-d H:i', $result['addTime']); ?></time><span class="msg-source">来源：<?php $noticeType = $this->config->item('noticeType'); echo $noticeType[$result['category']]; ?></span></p>
        </header>
        <section class="msg-detail-bd">
            <?php echo htmlspecialchars_decode($result['content']); ?>
        </section>
    </article>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>