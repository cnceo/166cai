<?php $CI = &get_instance();
$CI->load->view('/elements/common/header');
?>
<div class="wrap header-inner index-container">
  <div class="g-notfound">
    <div class="g-notfound-cont">
      <div class="g-notfound-btit">这个页面打不开了...</div>
      <div class="g-notfound-tips">此页不存在，点此处可<a href="/main" class="trig">返回2345首页</a><a href="javascript:;" class="trig">把2345设为主页</a></div>
    </div>
  </div>
</div>
<?php $CI->load->view('/elements/common/footer');?>