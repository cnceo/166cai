<div class="ui-popup ui-alert rp-go">
    <div class="ui-popup-inner">
        <div class="ui-popup-hd" id="popTitle"></div>
    	<div class="ui-popup-bd">
	    	<div class="ui-scroller">
				<ul class="cp-list cp-list-senior cp-list-icon">
					<?php foreach ($datas as $value):?>
				    <li>
					<a href="javascript:;" <?php echo $value['onclick'];?>>
					    <img src="<?php echo getStaticFile('/caipiaoimg/ios/images/' . $value['imgUrl']);?>" alt="">
					    <div class="cp-list-txt"><?php echo $value['name']?></div>
					</a>
				    </li>
				    <?php endforeach;?>
				</ul>
	    	</div>
		</div>
		<div class="ui-popup-ft">
	    	<a href="javascript:;" class="popcancel">取消</a>
	    </div>
	</div>
</div>