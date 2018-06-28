<div class="frame-column">
	<div class="side-nav">
		<h3>
			<a href="javascript:;"><i></i>合作商管理系统<s></s></a>
		</h3>
		<ul class="sub-nav">
			<li><a href="<?php echo $this->config->item('base_url')?>/shop">投注站管理<s></s></a></li>
		</ul>
	</div>
	<script>
	window.onload = function(){
		$(".side-nav h3:not('.home')").on("click", function () {
		    $(this).next(".sub-nav").eq(0).slideToggle();
		  });  
	}
	</script>
</div>