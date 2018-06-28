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
    <title><?php echo $category; ?></title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/msg.min.css');?>">
    <?php $this->load->view('comm/baidu'); ?>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</head>
<body>
	<div class="wrapper msg-detail txt-bg">
        <article class="news-bet">
            <header class="msg-detail-hd">
                <h1><?php echo $title; ?></h1>
                <p><time><?php echo $date; ?></time><span class="msg-source">阅读 <?php echo $num; ?></span></p>
            </header>
            <section class="msg-detail-bd">
                <?php echo htmlspecialchars_decode($content); ?>
            </section>
            <?php if($lid): ?>
            <footer>
                <a href="javascript:" class="btn btn-plain-confirm" onclick="window.webkit.messageHandlers.doBet.postMessage({lid:'<?php echo $lid;?>'});">立即投注</a>
            </footer>
            <?php endif;?>
            <?php if ($ios && $additions == 1) :?>
            <footer>
                <a href="javascript:" class="btn btn-plain-confirm" onclick="share();">分享好友</a>
            </footer>
            <?php endif;?>
        </article>
        <?php if (!$ios && $additions == 1) :?>
        <div class="fixed-bottombar">
        	<a href="javascript:;" onclick="javascript:location.href = '/app/download';">
            	<div class="title"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/app-icon.png');?>" alt="">手机客户端</div>
                <button type="button" class="btn btn-confirm">立即下载</button>
            </a>
            <span class="close"></span>
        </div>
        <div class="fixed-bottombar-hold"></div>
        <?php endif;?>
    </div>
</body>
<script>
var share = function() {
	window.webkit.messageHandlers.snsShare.postMessage({url:location.href,title:"<?php echo $title?>",content:"<?php echo $title?>…",imageUrl:"<?php echo $imgurl?>"});
}
var box = document.querySelector('.fixed-bottombar'), boxHold = document.querySelector('.fixed-bottombar-hold'), parentNode = box.parentNode, close = box.querySelector('.close');
close.addEventListener('click', function () {
	parentNode.removeChild(box);
	parentNode.removeChild(boxHold);
})
</script>
</html>