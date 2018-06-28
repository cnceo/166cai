<div class="fix-foot-box"></div>
</div>
<?php if(!$this->is_ajax):?>
<!--footer beigin-->
<div class="footer">
  <div class="wrap_in">
    <div class="help">
      <dl class="first">
        <dt class="logo">2345彩票</dt>
        <dd>
          <p><i class="checked"></i>交易安全</p>
          <p><i class="checked"></i>投注便捷</p>
          <p><i class="checked"></i>领奖无忧</p>
        </dd>
      </dl>
      <dl>
        <dt>新手教程</dt>
        <dd>
          <p><a target='_blank' href="<?php echo $baseUrl;?>help/index/b0-s1-f1">如何注册</a></p>
          <p><a target='_blank' href="<?php echo $baseUrl;?>help/index/b2-s1-f1">如何购彩</a></p>
          <p><a target='_blank' href="<?php echo $baseUrl;?>help/index/b3-s2-f1">如何兑奖</a></p>
        </dd>
      </dl>
      <dl>
        <dt>帮助中心</dt>
        <dd>
          <p><a target='_blank' href="<?php echo $baseUrl;?>help/index/b0-s1">注册登录</a></p>
          <p><a target='_blank' href="<?php echo $baseUrl;?>help/index/b4">常见问题</a></p>
          <p><a target='_blank' href="<?php echo $baseUrl;?>help/index/b5-s1">彩种介绍</a></p>
        </dd>
      </dl>
      <dl>
        <dt>充值提款</dt>
        <dd>
          <p><a target='_blank' href="<?php echo $baseUrl;?>help/index/b1-s1-f1">如何充值</a></p>
          <p><a target='_blank' href="<?php echo $baseUrl;?>help/index/b3-s3-f1">如何提款</a></p>
          <p><a target='_blank' href="<?php echo $baseUrl;?>help/index/b1-s1-f2">支付方式</a></p>
        </dd>
      </dl>
      <dl class="last">
        <dt>2345彩票</dt>
        <dd>
          <p><a target='_blank' href="http://www.2345.com/about/about.htm">关于我们</a></p>
          <p><a target='_blank' href="/links">友情链接</a></p>
          <p><a target='_blank' href="http://bbs.2345.com/fk/index.php?cName=%u5F69%u7968">意见建议</a></p>
        </dd>
      </dl>
    </div>
      <div class="e-card">
        <span class="baidu">百度大联盟绿色认证</span>
        <span class="kxdw">可信网站示范单位</span>
        <span class="kbsj">卡巴斯基云安全认证</span>
        <a href="http://www.sgs.gov.cn/lz/licenseLink.do?method=licenceView&entyId=2013080615395371" class="dzzz">电子营业执照</a>
      </div>
    <div class="copyright">版权所有&nbsp;<em style="font-family: Tahoma;">&copy;</em>&nbsp;2345.com&nbsp;&nbsp;<a href="http://www.2345.com/icp.jpg" rel="nofollow">ICP证沪B2-20120099</a>&nbsp;&nbsp;&nbsp;&nbsp;法务联系：fawu@2345.com<br>A股上市公司旗下网站，股票代码:002195  2345.com郑重提示：请理性购彩，热心公益。本站不向未满18周岁的青少年出售彩票</div>
  </div>
</div>
<!--footer end-->
<div class="pop-mask hidden"></div>
<iframe src="about:blank" class="popIframe hidden"></iframe>
<script type="text/javascript" src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/comm.js');?>'></script>
<?php $this->load->view('elements/common/encrypt');?>
<!--[if IE 6]>
<script src="/caipiaoimg/v1.0/js/DD_belatedPNG_0.0.8a-min.js"></script>
<script>DD_belatedPNG.fix('.png_bg');</script>
<![endif]-->
<?php $this->load->view('elements/pop/login');?>
<div id="pop_bind">
<?php 
    if( $isNeedShowBindId && $showBind )
    {
        $this->load->view('elements/pop/bind_id_phone');
    }

    if( $isNeedShowBindIdNoPhone && $showBindNoPhone )
    {
        $this->load->view('elements/pop/bind_id_phone');
    }
?>
</div>

<div id="pop_register">
<?php 
    if( empty( $this->uid ) )
    {
        $this->load->view('elements/pop/register');
    }
?>
</div>

<div id="pop_welcome"></div>
<div class="backSideMenu" id="backSideMenu">
    <a href="http://wpa.qq.com/msgrd?v=3&uin=2584565084&site=qq&menu=yes" class="service" target="_blank"><i class="icon icon-service"></i><span class="txt">在线客服</span></a>
    <a href="javascript:void(0);" class="feedBack" target="_self"><i class="icon icon-feedback"></i><span class="txt">意见反馈</span></a>
</div>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/analyticstracking.js');?>"></script>
<span style="display:none;">
  <script type="text/javascript" src="http://union2.50bang.org/js/caipiao2345"></script>
  <script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/50bang.js');?>"></script>
</span>

<?php 
	/*$CI = &get_instance();
	$CI->load->library('newuserhelp');    //加载新手帮助类
	$CI->newuserhelp->get_lottery_help();*/
?>
<?php 
  //用户反馈消息
  $this->load->view('mynews/feedback');
?>
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
<?php endif;?>