<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="author" content="weblol">
  <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui">
  <meta name="format-detection" content="telephone=no, email=no">
  <meta name="apple-mobile-web-app-capable" content="yes"/>
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
  <meta name="apple-mobile-web-app-title" content="166彩票">
  <title>送166元红包</title>
  <style>
    * {
      margin: 0;
      padding: 0;
    }
    html, body {
      height: 100%;
    }
    .wrap {
      min-height: 100%;
      background: #e92718 url(<?php echo getStaticFile('/caipiaoimg/static/images/active/join818/bg.jpg');?>) 50% 0 no-repeat;
      background-size: cover;
      text-align: center;
    }
    img {
      max-width: 100%;
      vertical-align: top;
    }
    .btn {
      position: fixed;
      bottom: 25px;
      left: 50%;
      width: 225px;
      height: 50px;
      margin-left: -113px;
      background: url(<?php echo getStaticFile('/caipiaoimg/static/images/active/join818/btn.png');?>) 50% 0 no-repeat;
      background-size: 225px auto;
      text-indent: -150%;
      overflow: hidden;
    }
    .mask {
      display: none;
      position: fixed;
      top: 0;
      right: 0;
      left: 0;
      bottom: 0;
      background: rgba(0,0,0,.7) url(<?php echo getStaticFile('/caipiaoimg/static/images/active/join818n/img-tip.pg');?>) 100% 0 no-repeat;
      background-size: 100% auto;
    }
    @media all and (orientation: landscape){ 
      .mask {
        background-size: auto 100%;
      }
    } 
  </style>
</head>
<body ontouchstart="">
  <div class="wrap">
      <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/join818/img1.png');?>" alt="送166元红包">
      <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/join818/img2.png');?>" alt="出票通知，秒速到账，合买跟大神">
      <div class="btn">领取新人红包</div>
  </div>
  <div class="mask"></div>
  <script>
    !function () {
      var body = document.querySelector('body');
      var wrap = document.querySelector('.wrap');
      var mask = document.querySelector('.mask');
      function stop (e) {
        e.preventDefault();
      }
      function showTip () {
        var ua = navigator.userAgent;
        if(ua.indexOf("MicroMessenger")>-1) {
          mask.style.display = 'block';
          body.addEventListener('touchstart', stop);
        }else {
            window.location.href = '<?php echo $downHref?>'
        }
      }
      wrap.addEventListener('click', function () {
        showTip()
      })
      setTimeout(function () {
        showTip()
      }, 5000)
    }()
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
