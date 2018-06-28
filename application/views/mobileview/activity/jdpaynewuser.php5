<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width,user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="166彩">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <meta name="screen-orientation" content="portrait">
    <meta name="x5-orientation" content="portrait">
    <meta name="full-screen" content="yes">
    <meta name="x5-fullscreen" content="true">
    <meta name="browsermode" content="application">
    <meta name="x5-page-mode" content="app">
    <title>京东支付</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/jdzf.min.css');?>">
</head>

<body ontouchstart>
    <div class="wrapper">
        <!--<header class="m-header">
            <h1>京东支付</h1>
            <a href="8.166cai.cn" class="hd-lnk-l">首页</a>
        </header>-->
        <div class="jdzf">
            <div class="hd"></div>
            <div class="main">
                <ul>
                    <li>
                        <a href="<?php echo $url?>">
                            <div><p>1.单单必减，最高可减<span><b>188</b>元</span></p></div>
                            <div class="btn"><em>立即充值</em></div>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $url?>">
                            <div><p>2.<b>新用户</b>首次使用京东支付<span><b>5</b>折优惠<mark>最高立减3元</mark></span></p></div>
                            <div class="btn"><em>立即充值</em></div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="ft">
                <p><em>新用户</em><span>特指任何平台均未使用过京东支付的用户</span></p>
            </div>
        </div>
    </div>
    <script>
    	var ua = navigator.userAgent.toLowerCase();
    	
        function getClasName () {
        	return 'isNative'
        }
        window.onload = function () {
            document.querySelector('.wrapper').className += ' ' + getClasName()

            var main = document.querySelector('.main')
            main.className += ' onload'
            setTimeout(function () {
                main.className += ' bounce'
            }, 400)
        }

        function login() {
    		if (ua.indexOf('android') >= 0 && ua.indexOf('2345caipiao')) android.relogin(window.location.href);
    		else window.webkit.messageHandlers.relogin.postMessage({url:window.location.href});
        }
        
        function bindcard () {
        	if (ua.indexOf('android') >= 0 && ua.indexOf('2345caipiao')) android.auth(window.location.href);
    		else window.webkit.messageHandlers.goBind.postMessage({url:window.location.href});
        }
    </script>
    <script>
        !function(e,t,n,g,i){e[i]=e[i]||function(){(e[i].q=e[i].q||[]).push(arguments)},n=t.createElement("script"),tag=t.getElementsByTagName("script")[0],n.async=1,n.src=('https:'==document.location.protocol?'https://':'http://')+g,tag.parentNode.insertBefore(n,tag)}(window,document,"script","assets.growingio.com/2.1/gio.js","gio");
        gio('init','8d4b2106242d6858', {});
        //custom page code begin here
        //custom page code end here
        gio('send');
    </script>
</body>

</html>