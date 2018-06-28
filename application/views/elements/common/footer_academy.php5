<!--note-footer start-->
<style>
.foot-short { margin-top: 0;}
</style>
<!-- <div class="note-footer"> -->
<!-- 	版权所有 © 2345.com  ICP证沪B2-20120099    法务联系：fawu@2345.com<br>A股上市公司旗下网站，股票代码:002195 2345.com郑重提示：请理性购彩，热心公益。本站不向未满18周岁的青少年出售彩票 -->
<!-- </div> -->
<div class="footer footer_login foot-short">
  <div class="wrap_in">
    <div class="copyright">Copyright &copy; 2345网址导航 All Rights Reserved. <a href="http://www.2345.net/2345ICP.html" rel="nofollow">ICP证沪B2-20120099</a>  法务联系：fawu@2345.com<br>A股上市公司旗下网站，股票代码:002195  2345.com郑重提示：请理性购彩，热心公益。本站不向未满18周岁的青少年出售彩票</div>
  </div>
</div>
<div class="pop-mask hidden"></div>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/jquery-1.8.3.min.js'); ?>"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js'); ?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<script type="text/javascript" src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/comm.js');?>'></script>
<?php $this->load->view('elements/common/encrypt');?>
<?php if( empty( $this->uid ) ): ?>
<?php include dirname(__FILE__).'/../pop/login.php5'?>
<?php include dirname(__FILE__).'/../pop/register.php5'?>
<?php endif;?> 
<!--note-footer end-->
<!-- GA统计代码 -->
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/analyticstracking.js');?>"></script>
<!-- 武林榜统计代码 -->
<span style="display:none;">
<script type="text/javascript" src="http://union2.50bang.org/js/caipiao2345"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/50bang.js');?>"></script>
<!-- 百度统计代码 -->
<script>
  var _hmt = _hmt || [];
  (function() {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?73920d2a63aee9065feff02106ed5b0f";
    var s = document.getElementsByTagName("script")[0]; 
    s.parentNode.insertBefore(hm, s);
  })();
</script>
</span>
