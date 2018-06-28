  <?php 
  	$cpage = intval($this->input->get('cpage', true));
  	$cpage = $cpage <= 1 ? 1 : $cpage;
  	$tpage = $spagenum;
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
	  		if(preg_match('/cpage=-{0,1}\d+/i', $URIS[1]))
	  		{
	  			$URIS[1] = preg_replace('/cpage=-{0,1}\d+/i', "cpage=$cpage", $URIS[1]);
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
  </div>
  <script type='text/javascript'><!--
  var spagenum = <?php echo $tpage;?>;
  function sgetUri($obj)
  {
	  sjpage = parseInt($('#secondskips').val())?parseInt($('#secondskips').val()):1;
	  sjpage = (sjpage > spagenum)? spagenum : sjpage;
	  return $obj.href.replace(/cpage=\d+/i, 'cpage=' + sjpage);
  }
  <?php if(!empty($ajaxform)):?>
	$(function(){
		 $('#<?php echo $ajaxform?>_comm_page_').find('a').click(function(event) {
			 if($(this).hasClass('spage_skip'))
			 {
				 this.href = sgetUri(this);
			 }
			 var tar = target;
			 target = this.href;
			 $('.<?php echo $ajaxform;?>').find('.submit').first().trigger('click');
			 target = tar;
		   	 return false;
		 });
	})
  <?php else:?>
   $('.spage_skip').click(function(event){
		this.href = sgetUri(this);
   });
  <?php endif;?>
  --></script>