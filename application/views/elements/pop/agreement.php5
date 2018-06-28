<div class="articlePopWrap pub-pop container_<?php echo $ptype;?>" style='display:none;'>
<?php if($ptype == 'lottery_pro'):?>
<!-- 委托投注协议 begin -->
	<div class="pop-in">
		<div class="pop-head">
			<h2>2345用户委托投注协议</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="pop-article">
			</div>
		</div>
	</div>
<!-- 委托投注协议 end -->
<?php elseif($ptype == 'risk_pro'):?>
<!-- 限号投注风险需知 begin -->
	<div class="pop-in">
		<div class="pop-head">
			<h2>限号投注风险需知</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="pop-article">
			</div>
		</div>
	</div>
<!-- 限号投注风险需知 end -->
<?php endif;?>
</div>

<script type="text/javascript">
$(function() {
	cx.PopAgreement_<?php echo $ptype;?> = (function() {
        var me = {};
        var $wrapper = $('.container_<?php echo $ptype;?>');

        $wrapper.find('.pop-close').click(function() {
            $wrapper.hide();
            cx.Mask.hide();
        });

        me.show = function() {
            var url;
            cx.Mask.show();
            <?php if($ptype == 'lottery_pro'):?>
            url = '/links/weituo';
            <?php elseif($ptype == 'risk_pro'):?>
            url = '/links/xianhao';
            <?php endif;?>
            $.get(url, function( response ){
                $wrapper.find('.pop-article').html( response );
                $wrapper.css({marginTop : (-$wrapper.height()/2), marginLeft : (-$wrapper.width()/2) }).show();
            });
        };

        me.hide = function() {
            $wrapper.hide();
            cx.Mask.hide();
        };

        return me;
    })();
	$('.<?php echo $ptype;?>').click(function(){
		cx.PopAgreement_<?php echo $ptype;?>.show();
	})
})
</script>
