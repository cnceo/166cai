<div class="bet-side-item ">
		<h3 class="bet-side-title"><a href="/info" target="_blank" class="more">更多<i>»</i></a><a href="/info/lists/4" target="_blank">大乐透推荐</a></h3>
		<ol class="lnk-help">
		<?php foreach ($data as $k => $if) {?>
			<li><a href="/info/dlt/<?php echo $if['id']?>" target="_blank"><?php echo ($k+1).".".(mb_strlen($if['title'], 'utf-8') > 18 ? mb_substr($if['title'], 0, 18, 'utf-8')."..." : $if['title'])?></a></li>
		<?php }?>
		</ol>
	  </div>