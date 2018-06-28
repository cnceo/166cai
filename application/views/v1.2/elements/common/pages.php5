  <?php 
  	$cpage = $this->input->get('cpage', true);
  	$cpage = empty($cpage) ? 1 : intval($cpage);
  	$tpage = $pagenum;
  	if($cpage>$tpage)
  	{
  		$cpage = $tpage;
  	}
  	$pos = $cpage%6;
  	function get_uri($cpage)
  	{
  		$URIS = explode('?', preg_replace('/[<>\']/', '', $_SERVER['REQUEST_URI']));
  		if(!empty($URIS[1]))
	  	{
	  		if(preg_match('/cpage=\d+/i', $URIS[1]))
	  		{
	  			$URIS[1] = preg_replace('/cpage=\d+/i', "cpage=$cpage", $URIS[1]);
	  		}
	  		else 
	  		{
	  			$URIS[1] .= "&cpage=$cpage";
	  		}
	  	}
	  	else 
	  	{
	  		$URIS[1] = "cpage=$cpage";
	  	}
	  	$RRI = implode('?', $URIS);
	  	return $RRI;
  	}
  ?>
  <div class="page" id="<?php echo $ajaxform?>_comm_page_">
	  <a href="<?php echo get_uri($cpage-1);?>" class="a_turn a_turn_left" <?php if($cpage<=1):?>style='display:none;'<?php endif;?> >上一页<i class="arrow arrow_left"></i></a>
	  <span class="num">
		  <a href="<?php echo get_uri(1);?>" <?php if($cpage-$pos < 1):?>style='display:none;'<?php endif;?>>1</a>
		  <a href="<?php echo get_uri($cpage-$pos-1);?>" <?php if($cpage-$pos < 1):?>style='display:none;'<?php endif;?> <?php if($pos==0):?>class="cur" <?php endif;?>>...</a>
		  
		  <a href="<?php echo get_uri($cpage-($pos-1));?>" <?php if($cpage-($pos-1) > $tpage):?>style='display:none;'<?php endif;?> <?php if($pos==1):?>class="cur" <?php endif;?>><?php echo $cpage-($pos-1)?></a>
		  <a href="<?php echo get_uri($cpage-($pos-2));?>" <?php if($cpage-($pos-2) > $tpage):?>style='display:none;'<?php endif;?> <?php if($pos==2):?>class="cur" <?php endif;?>><?php echo $cpage-($pos-2)?></a>
		  <a href="<?php echo get_uri($cpage-($pos-3));?>" <?php if($cpage-($pos-3) > $tpage):?>style='display:none;'<?php endif;?> <?php if($pos==3):?>class="cur" <?php endif;?>><?php echo $cpage-($pos-3)?></a>
		  <a href="<?php echo get_uri($cpage-($pos-4));?>" <?php if($cpage-($pos-4) > $tpage):?>style='display:none;'<?php endif;?> <?php if($pos==4):?>class="cur" <?php endif;?>><?php echo $cpage-($pos-4)?></a>
		  <a href="<?php echo get_uri($cpage-($pos-5));?>" <?php if($cpage-($pos-5) > $tpage):?>style='display:none;'<?php endif;?> <?php if($pos==5):?>class="cur" <?php endif;?>><?php echo $cpage-($pos-5)?></a>
		  <a href="<?php echo get_uri($cpage-($pos-6));?>" <?php if($cpage-($pos-6) > $tpage):?>style='display:none;'<?php endif;?> <?php if($pos==6):?>class="cur" <?php endif;?>><?php echo $cpage-($pos-6)?></a>
		  
		  <a href="<?php echo get_uri($cpage-($pos-7));?>" <?php if($cpage-($pos-7) >= $tpage):?>style='display:none;'<?php endif;?>>...</a>
		  <a href="<?php echo get_uri($tpage);?>" <?php if($cpage-($pos-6)>=$tpage):?>style='display:none;'<?php endif;?>><?php echo $tpage?></a>
	  </span>
	  <a href="<?php echo get_uri($cpage+1);?>" class="a_turn a_turn_right" <?php if($cpage>=$tpage):?>style='display:none;'<?php endif;?>>下一页<i class="arrow arrow_right"></i></a>
	  <span class="wirte">跳转到第<input type="text" class="text skips" value='<?php //echo $cpage;?>' />页</span>
      <a href="<?php echo get_uri($cpage);?>" class="a_btn page_skip">跳转</a>
  </div>
  <script type='text/javascript'><!--
  var pagenum = <?php echo $tpage;?>;
  function getUri($obj)
  {
	  jpage = $('#<?php echo $ajaxform?>_comm_page_').find('.skips').val();
	  jpage = (isNaN(jpage) || !jpage) ? 1 : jpage;
	  jpage = (jpage > pagenum)? pagenum : jpage;
	  return $obj.href.replace(/cpage=\d+/i, 'cpage=' + jpage);
  }
  <?php if(!empty($ajaxform)):?>
	$(function(){
		 $('#<?php echo $ajaxform?>_comm_page_').find('a').click(function(event) {
			 if($(this).hasClass('page_skip'))
			 {
				 this.href = getUri(this);
			 }
			 var tar = target;
			 target = this.href;
			 $('.<?php echo $ajaxform;?>').find('.submit').first().trigger('click');
			 target = tar;
		   	 return false;
		 });
	})
  <?php else:?>
   $('.page_skip').click(function(event){
		this.href = getUri(this);
   })
  <?php endif;?>
  --></script>