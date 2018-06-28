<?php if(!$this->is_ajax):?>
<?php $this->load->view('v1.1/elements/user/menu');?>
<div class="l-frame-cnt">
<div class="uc-main">
	<!-- <div class="lnk-enter">
		邀请好友赢取千元彩金<a href="//www.166cai.com/activity/joinus/2" target="_blank" class="btn-ss btn-ss-bet">立即邀请</a>
	</div> -->
	<div class="tab-nav">
		<ul class="clearfix">
			<li class="active"><a href="javascript:;" id="noUsedCtype"><span>可使用(<em id="redpackTotal"><?php echo $totals;?></em>)</span></a></li>
			<li><a href="javascript:;" id="usedCtype"><span>已用完/过期</span></a></li>
		</ul>
	</div>
<div id="container_redpack-form">
<?php endif;?>
<?php
	$redpackType = array(
		'1' => '彩金红包',
		'2' => '充值红包'
	);
?>
	<div class="tab-content redpack-form">
	<input type='hidden' name='ctype' class="vcontent" value="<?php echo $ctype?>" />
	<input class="submit" type="hidden" />
	<input type='hidden' name='redpackTotal' value="<?php echo $totals;?>" />
		<div class="tab-item" <?php if($ctype == 1):?>style="display:block;"<?php endif;?>>
		<?php if($ctype == '1'):?>
			<?php 
				$useHref = array(
					'1' => '/safe/userInfo',
					'2' => '/wallet/recharge',
					'' => 'javascript:;',
					'3' => array(
						'101' => '/ssq',
						'102' => '/jczq',
						'103' => '/ssq',
						'104' => '/syxw',
						'105' => '/ssq',
						'106' => '/dlt',
						'107' => '/fcsd',
						'108' => '/pls',
						'109' => '/plw',
						'110' => '/qlc',
						'111' => '/qxc',
						'112' => '/jczq',
						'113' => '/jclq',
						'114' => '/sfc',
						'115' => '/rj',
						'116' => '/hbsyxw',
						'117' => '/syxw',
						'118' => '/jxsyxw',
						'119' => '/ks',
						'120' => '/klpk',
						'121' => '/cqssc',
					),
				);
			?>
			<?php if(!empty($redpacks)):?>
			<div class="hongbao">
				<div class="hongbao-filter pos-r zindex10">
					<dl class="simu-select select-small"></dl>
                	<a href="/help/index/b2-s4" class="more sub-color fr" target="_blank">红包使用说明<i>&raquo;</i></a>
                </div>
				<ul>
					<?php $now = date('Y-m-d H:i:s');?>
					<?php foreach ($redpacks as $key=> $val):?>
					<li class="hongbao-ysy">
						<div class="hongbao-l">
							<span>&yen;<b><?php echo number_format(ParseUnit($val['money'], 1), 2);?></b></span><?php if($val['p_type'] == '3'){ echo $val['p_name']; }else{ echo $redpackType[$val['p_type']]; }?>
						</div>
						<?php if($val['valid_start'] <= $now && $val['valid_end'] >= $now):?>
						<div class="hongbao-r">
							<p>截止时间：<?php if($val['valid_end'] == '0000-00-00 00:00:00'){ echo '---';}else{ echo date('Y-m-d', strtotime($val['valid_end']));}?></p>
							<p>使用条件：<?php echo $val['use_desc'];?></p>
							<?php if($val['ismobile_used']):?>
							<a href="javascript:;" class="btn-sup mobilePop">下载客户端使用</a>
							<?php else: ?>
							<a <?php if($val['p_type'] != '1'): ?> target="_blank"<?php endif ?> href="<?php if($val['p_type'] == '3'){ echo $useHref[$val['p_type']][$val['c_type']];}else if($val['p_type'] == '1'){echo 'javascript:;';}else{ echo $useHref[$val['p_type']];}?>" class="btn-sup<?php   if (!empty($this->uinfo) && $val['p_type'] == '1' && (!$this->uinfo['phone'] || !$this->uinfo['id_card']) ){echo ' not-bind';}else if($val['p_type'] == '1'){echo ' btn-ljsy';} ?>" data-money='<?php echo number_format(ParseUnit($val['money'], 1), 0);?>' data-rid="<?php echo $val['id'];?>" >立即使用</a>
							<?php endif;?>
						</div>
						<?php else :?>
						<div class="hongbao-r">
							<p>启用时间：<?php if($val['valid_start'] == '0000-00-00 00:00:00'){ echo '---';}else{ echo date('Y-m-d', strtotime($val['valid_start']));}?></p>
							<p>使用条件：<?php echo $val['use_desc'];?></p>
							<a href="javascript:;" class="btn-sup btn-disabled">暂不可用</a>
						</div>
						<?php endif;?>
						<?php if(($val['valid_end'] != '0000-00-00 00:00:00') && ($val['valid_start'] <= $now && $val['valid_end'] >= $now) && ((strtotime($val['valid_end'])-time()) < (3600 * 24 * 7))):?>
						<i class="hongbao-tag"></i>
						<?php endif;?>
						<?php if($val['ismobile_used']):?>
						<i class="app-tag"></i>
						<?php endif;?>
					</li>
					<?php endforeach;?>
				</ul>			
			</div>
			<div class="tar table-page">
				<span class="mlr10">本页<em class="mlr5"><?php echo $cpnum;?></em>条记录</span><span>共<em class="mlr5"><?php echo $pagenum;?></em>页</span>
			</div>
			<!-- pagination -->
			<?php echo $pagestr;?>
			<!-- pagination end -->
			<?php else :?>
			<div class="tac fcw">亲，你还没有可使用的红包！</div>
			<?php endif;?>
			<?php endif;?>
		</div>
		<div class="tab-item" <?php if($ctype == 2):?>style="display:block;"<?php endif;?>>
		<?php if($ctype == 2):?>
			<?php if(!empty($redpacks)):?>
			<!--表格 begin-->
			<table class="mod-tableA">
				<thead>
					<tr>
						<th width="15%">红包名称</th>
						<th width="15%">红包金额</th>
						<th width="16%">获取日期</th>
						<th width="17%">有效期</th>
						<th width="12%">状态</th>
						<th width="12%">适用范围</th>
						<th width="12%">方案详情</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($redpacks as $key=> $val):?>
					<tr>
						<td><?php if($val['p_type'] == '3'){ echo $val['p_name']; }else{ echo $redpackType[$val['p_type']]; }?></td>
						<td><?php echo number_format(ParseUnit($val['money'], 1), 2);?></td>
						<td><?php if($val['valid_start'] == '0000-00-00 00:00:00'){ echo '---';}else{ echo date('Y-m-d', strtotime($val['valid_start']));}?></td>
						<td><?php if($val['valid_end'] == '0000-00-00 00:00:00'){ echo '---';}else{ echo date('Y-m-d', strtotime($val['valid_end']));}?></td>
						<td><?php if($val['status'] == 2){ echo '已使用';}else{ echo '已过期';}?></td>
						<td><?php echo $val['use_desc'];?></td>
						<td><?php if($val['p_type'] == '3' && !empty($val['orderId'])):?><a target="_blank" href="/orders/detail/<?php echo $val['orderId'];?>">查看</a><?php else :?>---<?php endif;?></td>
					</tr>
					<?php endforeach;?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7" class="tar table-page">
							<span class="mlr10">本页<em class="mlr5"><?php echo $cpnum;?></em>条记录</span><span>共<em class="mlr5"><?php echo $pagenum;?></em>页</span>
						</td>
					</tr>
				</tfoot>
			</table>
			<!--表格 end-->
			<!-- pagination -->
			<?php echo $pagestr;?>
			<!-- pagination end -->
			<?php else :?>
			<div class="tac fcw">亲，暂无红包记录</div>
			<?php endif;?>
			<?php endif;?>
		</div>
	</div>
<script type="text/javascript">
	var target = '/mylottery/redpack';
	$(function(){
		new cx.vform('.redpack-form', {
			checklogin: true,
	        submit: function(data) {
	            var self = this;
	            $.ajax({
	                type: 'post',
	                url: target,
	                data: data,
	                success: function(response) {
	            		$('#container_redpack-form').html(response);
	            		if(data.ctype == '1'){
			                $('#redpackTotal').html($('input[name="redpackTotal"]').val());
			            }
	                }
	            });
	        }
		 });
	});
</script>
<?php if(!$this->is_ajax):?>
</div>
</div>
</div>
<div style="display: none;" class='not-bind'></div>
<div class="pub-pop pop-w-min mobilePopWrap">
        <div class="pop-in">
            <div class="pop-head">
                <h2>扫码下载手机客户端使用专享红包</h2>
                <span class="pop-close" title="关闭">&times;</span>
            </div>
            <div class="pop-body tac">
                <img src="/caipiaoimg/v1.1/img/qrcode-b.png" width="154" height="154" alt="">
            </div>
        </div>
    </div>
<script type="text/javascript">
var target = '/mylottery/redpack';
$(function(){
	$('#noUsedCtype').on('click', function(){
		$('input[name="ctype"]').val(1);
        $('.redpack-form .submit').trigger('click');
     });
	$('#usedCtype').on('click', function(){
		$('input[name="ctype"]').val(2);
        $('.redpack-form .submit').trigger('click');
     });
	$("body").on('click', '.mobilePop', function (){
		cx.mobilePopWrap.show();
	});
	//彩金红包到账
	$("body").on('click', '.btn-ljsy', function (e){
		 e.stopPropagation(); 
        if ($('.not-login').length >0 || !$.cookie('name_ie')) {cx.PopAjax.login();return ;}
        var rid = $(this).data('rid');
        var money = $(this).data('money');
	    $.ajax({
	        type:'post',
	        data:{rid:rid},
	        url:'/point/redpackUse',
	        dataType:"json",
	        success: function(data)
	        {
	          if(data.code==300) 
              {
                cx.PopAjax.login();return;
              }else if(data.code==500)
              {
              	$('.not-bind').trigger('click');
              }else if(data.code==400){
				cx.Alert({
				title: '提示' ,
				content: data.msg,
				btns:[{type: 'confirm', href: 'javascript:;', txt: '确认'}],
				cancelCb: function(){window.location.reload(true);},
				confirmCb: function(){window.location.reload(true);}
				});
              }else{
				cx.Alert({
				title: '提示' ,
				content: '<p class="pop-help" style="font-size:14px;">使用成功，<span style="color:#f00;">'+money+'</span>&nbsp;元彩金已派至账户</p>',
				btns:[{type: 'confirm', href: 'javascript:;', txt: '确认'}],
				cancelCb: function(){window.location.reload(true);},
				confirmCb: function(){window.location.reload(true);}
				});
              }
	        }, 
	    });

	});

	cx.mobilePopWrap = (function() {
        var me = {};
        var $wrapper = $('.mobilePopWrap');

        $wrapper.find('.pop-close').click(function() {
            $wrapper.hide();
            cx.Mask.hide();
        });

        me.show = function() {
            cx.Mask.show();
            $wrapper.css({marginTop : (-$wrapper.height()/2), marginLeft : (-$wrapper.width()/2) }).show();
        };

        me.hide = function() {
            $wrapper.hide();
            cx.Mask.hide();
        };

        return me;
    })();
});
</script>
<?php $this->load->view('v1.1/elements/user/menu_tail');?>
<?php endif;?>