<!--note-footer start-->
<style>
.foot-short { margin-top: 0;}
</style>
<script type="text/javascript">
    var baseUrl = '<?php echo $baseUrl; ?>';
    var uri = '<?php echo str_replace(array('<', '>', 'script'), '', $_SERVER['REQUEST_URI']);?>';
    var version = 'v1.1';
    var G = {
        baseUrl: baseUrl
    }
</script>
<div class="footer footer_login foot-short">
  <div class="wrap_in">
    <div class="copyright">
      <p>166彩票提醒：理性购彩，热爱公益  国家禁止彩票店向未满18周岁的未成年人售彩！</p>
      版权所有 <em style="font-family: Tahoma;">&copy;</em> 上海彩咖网络科技有限公司 <a target="_blank" href="http://www.miitbeian.gov.cn/" rel="nofollow">沪ICP备17023410号</a> 客服热线：400-690-6760
    </div>
  </div>
</div>
<div class="pop-mask hidden"></div>
<iframe src="about:blank" class="popIframe hidden"></iframe>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js'); ?>"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/vform.js');?>"></script>
<script type="text/javascript" src='<?php echo getStaticFile('/caipiaoimg/v1.1/js/comm.min.js');?>'></script>
<?php $this->load->view('v1.1/elements/common/encrypt');?>
<!--note-footer end-->
<!-- GA统计代码 -->
<!-- <script type="text/javascript" src="<?php // echo getStaticFile('/caipiaoimg/v1.1/js/analyticstracking.js');?>"></script>-->
<!-- 百度统计代码 -->
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
</span>
