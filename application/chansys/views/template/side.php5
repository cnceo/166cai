<div class="frame-column">
	<div class="side-nav">
		<h3>
			<a href="/chansys/index/home"><i></i>合作商数据系统<s></s></a>
		</h3>
	</div>
	<script>
	window.onload = function(){
		$(".side-nav h3:not('.home')").on("click", function () {
		    $(this).next(".sub-nav").eq(0).slideToggle();
		  });  
	}
	</script>
</div>