<?php $this->load->view('v1.1/elements/common/links')?>
<div class="fix-foot-box"></div>
</div>
<div class="footer">
  <div class="wrap_in">
    <div class="help">
      <ul class="note">
          <li><i class="icon-font">&#xe632;</i>账户安全</li>
          <li><i class="icon-font">&#xe630;</i>投注便捷</li>
          <li><i class="icon-font">&#xe631;</i>兑奖简单</li>
          <li><i class="icon-font">&#xe62f;</i>提现迅速</li>
      </ul>
      <div class="qrcode">
        <p>免费下载手机客户端</p>
        <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/qrcode.png')?>" width="94" height="94" alt="">
      </div>
      <dl>
        <dt>新手教程</dt>
        <dd>
          <a href="<?php echo $baseUrl;?>help/index/b0-s1-f1">如何注册</a>
          <a href="<?php echo $baseUrl;?>help/index/b2-s1-f1">如何购彩</a>
          <a href="<?php echo $baseUrl;?>help/index/b3-s2-f1">如何兑奖</a>
        </dd>
      </dl>     
      <dl>
        <dt>帮助中心</dt>
        <dd>
          <a href="<?php echo $baseUrl;?>help/index/b0-s1">注册登录</a>
          <a href="<?php echo $baseUrl;?>help/index/b4">常见问题</a>
          <a href="<?php echo $baseUrl;?>help/index/b5-s1">彩种介绍</a>
        </dd>
      </dl>
      <dl class="last">
        <dt>充值提现</dt>
        <dd>
          <a href="<?php echo $baseUrl;?>help/index/b1-s1-f1">如何充值</a>
          <a href="<?php echo $baseUrl;?>help/index/b3-s3-f1">如何提现</a>
          <a href="<?php echo $baseUrl;?>help/index/b1-s1-f2">支付方式</a>
        </dd>
      </dl>
      <dl>
        <dt>166彩票</dt>
        <dd>
          <a href="/about/">关于我们</a>
          <a href="/about/contact">联系方式</a>
          <a href="/partner" target="_blank">友情链接</a>
        </dd>
      </dl>
    </div>
  
  <div class="copyright">
      <p>166彩票提醒：理性购彩，热爱公益  国家禁止彩票店向未满18周岁的未成年人售彩！</p>
      版权所有 <em style="font-family: Tahoma;">&copy;</em> 上海彩咖网络科技有限公司<a target="_blank" href="http://www.miitbeian.gov.cn/" rel="nofollow">沪ICP备17023410号</a> 客服热线：400-690-6760
    </div>
    <div class="zzzz">
      <a href="//www.sgs.gov.cn/lz/licenseLink.do?method=licenceView&entyId=20170609153148983" target="_blank"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/img-foot-yyzz.png');?>" width="109" height="32" alt="营业执照"></a>
      <a href="/caipiaoimg/v1.1/img/nsrzz.png" target="_blank"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/img-foot-nsrzz.png');?>" width="104" height="32" alt="纳税人资质"></a>
      <a href="/caipiaoimg/v1.1/img/jgxxdm.png" target="_blank"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/img-foot-jgxxdm.png');?>" width="116" height="32" alt="机构信用代码"></a>
      <a href="https://www.sgs.gov.cn/notice/notice/view?uuid=9DfasM8QpxkrBIC.hd.hMnJ4EgrVT52R&tab=01" target="_blank"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/img-foot-gswj.png');?>" width="92" height="32" alt="工商网监"></a>
      <a href="https://ss.knet.cn/verifyseal.dll?sn=e16072531011564232v0gb000000&ct=df&a=1&pa=0.13280558679252863"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/img-foot-kxwz.png');?>" width="87" height="32" alt="可信网站"></a>
    </div>
  </div>
</div>

<div class="side-menu">
    <a href="/activity/newmode" target="_blank" class="link-app">
      <i class="icon-font">&#xe61c;</i>手机彩票
      <div class="qrcode">
        <h2>免费下载手机客户端</h2><p>彩店出票 领奖无忧</p>
        <img src="../caipiaoimg/v1.1/img/qrcode.png" width="94" height="94" alt="">
      </div>
    </a>
    <a href="javascript:;" class="feedBack" target="_self"><i class="icon-font">&#xe62c;</i>我要反馈</a>
</div>
<!--footer end-->
<div class="pop-mask hidden"></div>

<script type="text/javascript" src='<?php echo getStaticFile('/caipiaoimg/v1.1/js/comm.min.js');?>'></script>
<script type="text/javascript" src='<?php echo getStaticFile('/caipiaoimg/v1.1/js/vform.min.js');?>'></script>
<?php $this->load->view('v1.1/elements/common/encrypt');?>
<!--<script type="text/javascript" src="<?php // echo getStaticFile('/caipiaoimg/v1.1/js/analyticstracking.js');?>"></script>-->
<script>
  // 百度统计 
  var _hmt = _hmt || [];
  (function() {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?73920d2a63aee9065feff02106ed5b0f";
    var s = document.getElementsByTagName("script")[0]; 
    s.parentNode.insertBefore(hm, s);
  })();
</script>
</body>
</html>
