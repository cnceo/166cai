<?php 
if (!$this->input->cookie('spring-banner') && !empty($cpbanner['tzy']['path'])) {?>
<div class="spring2016-banner spring2016-banner-inner">
  <a href="<?php echo $cpbanner['tzy']['url']?>" target="_blank"><img src="/uploads/banner/<?php echo $cpbanner['tzy']['path']?>" width="1000" height="80" alt="<?php echo $cpbanner['tzy']['title']?>"></a>  
  <a href="javascript:;" target="_self" class="spring2016-banner-close">&times;</a>
</div>
<?php }?>
<script>
	$(function(){
		$('.spring2016-banner').on('click', '.spring2016-banner-close', function(){
			$(this).parents('.spring2016-banner').slideUp();
			$.cookie('spring-banner', '1', {expires:3600 * 24, path: '/'});
		})
	})
</script>