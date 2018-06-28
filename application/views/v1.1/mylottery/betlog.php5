<?php if(!$this->is_ajax):?>
<?php $this->load->view('v1.1/elements/user/menu');?>
<div class="l-frame-cnt">
<div class="uc-main">
	<!-- <div class="lnk-enter">
		邀请好友赢取千元彩金<a href="//www.166cai.com/activity/joinus/3" target="_blank" class="btn-ss btn-ss-bet">立即邀请</a>
	</div> -->
	<h2 class="tit">投注记录</h2>
	<!--表单筛选 begin-->
	<div class="filter-oper">
		<div class="lArea betlog-form">
			<input type='hidden' class='submit' >
			<dl class="simu-select select-small" data-target='submit'>
	            <dt>
	            	<span class='_scontent'>最近一个月</span><i class="arrow"></i>
	            	<input type='hidden' name='date' class="vcontent" value='1' >
	            </dt>
	            <dd class="select-opt">
	            	<div class="select-opt-in" data-name='date'>
                        <?php foreach( $dateSpan as $key => $val ): ?>
	            		<a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?></a>
                        <?php endforeach; ?>
		            </div>
	            </dd>
	        </dl>
	        <dl class="simu-select select-small" data-target='submit'>
	            <dt>
                    <span class='_scontent'>所有彩种</span><i class="arrow"></i>
	            	<input type='hidden' name='lid' class="vcontent" value='all' >
	            </dt>
	            <dd class="select-opt">
	            	<div class="select-opt-in" data-name='lid'>
                        <?php foreach( $betType as $key => $val ): ?>
                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?></a>
                        <?php endforeach; ?>
		            </div>
	            </dd>
	        </dl>
	        <dl class="simu-select select-small" data-target='submit'>
	            <dt>
                    <span class='_scontent'><?php echo $buyTypeSpan[0]?></span><i class="arrow"></i>
	            	<input type='hidden' name='buyType' class="vcontent" value='0' >
	            </dt>
	            <dd class="select-opt">
	            	<div class="select-opt-in" data-name='buyType'>
                        <?php foreach( $buyTypeSpan as $key => $val ): ?>
                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?></a>
                        <?php endforeach; ?>
		            </div>
	            </dd>
	        </dl>
	        <label for="nopay"><input type="checkbox" class="ckbox vcontent" name="nopay" id="nopay">未支付</label>
	        <label for="marginonly"><input type="checkbox" class="ckbox vcontent" name="marginonly" id="marginonly">只看中奖</label>
                <label for="kaijiang"><input type="checkbox" class="ckbox vcontent" name="kaijiang" id="kaijiang">等待开奖</label>
		</div>
	</div>
	<!--表单筛选 end-->
<div id='container_betlog-form'>
<?php endif;?>
	<!--表格 begin-->
	<table class="mod-tableA">
		<thead>
			<tr>
				<th width="10%">时间</th>
				<th width="12%">彩种</th>
				<th class="tal" width="24%">订单信息</th>
				<th class="tar" width="15%">订单金额（元）</th>
				<th width="16%">订单状态</th>
				<th width="15%">我的奖金</th>
				<th width="12%">方案详情</th>
			</tr>
		</thead>
		<tbody>
		<?php if(!empty($orders)):?>
			<?php foreach ($orders as $order):?>
			<?php 
				$ctime = strtotime($order['created']);
				$notOver += (in_array($order['status'], array('600', '1000', '2000'))? 0 : 1);
				switch ($order['lid']) {
				    case 44:
				        $url = '/gjc';
				        break;
				    case 45:
				        $url = '/gjc/gyj';
				        break;
				    default:
				        $url = "/".BetCnName::getEgName($order['lid']);
				        break;
				}
			?>
			<tr>
				<td><strong><?php echo date('m月d日', $ctime);?></strong><br /><?php echo date('H:i:s', $ctime);?></td>
				<td><a href="<?php echo $url;?>"  target = "_blank"><?php echo BetCnName::getCnName($order['lid']); ?></a></td>
				<td class="tal <?php if($order['add_money'] > 0){echo "jiajiang-tag";}?>">
				<?php if ($order['orderType'] == 42) {
					echo "发起人："?><a target="_blank" href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $order['uid'])), 'ENCODE'));?>"><?php echo $order['nick_name']?></a>
                                        <?php if($order['subOrderType']==1) { echo '<br>定制跟单';} ?>
				<?php }else {
					echo "期次 ".$order['issue'];if ($order['orderType'] == 41) {?><span class="hm-progress">进度:<b><?php echo round($order['buyTotalMoney'] * 100/$order['money'], 2)?>%</b></span><?php }
				}?>
				<br>
                                <?php if($order['subOrderType']!=1){ ?>
				<?php switch ($order['orderType']) {case 1:?>追号<?php break;case 3:?>不赚包赔<?php break;case 41:?>发起合买<?php break;case 42:?>参与合买<?php break;case 6:?>追号包赔<?php break;default:?>自购<?php break; } if($order['isChase'] && in_array($order['orderType'], array(0, 1))):?>追加<?php endif;?>
                                <?php } ?>
				</td>
				<td class="tar"><?php echo number_format(ParseUnit(($order['orderType'] == 41 && strtotime($order['endTime']) < time()) ? ($order['buyMoney'] + (int)$order['guarantee']): $order['buyMoney'], 1), 2);?>
				<?php if ($order['orderType'] == 41 && strtotime($order['endTime']) < time() && $order['guarantee'] > 0) {?><i class="hm-jjdetail bubble-tip" tiptext="<span class='coffe'>认购<?php echo number_format(ParseUnit($order['buyMoney'], 1), 2)."元+保底转认购".number_format(ParseUnit((int)$order['guarantee'], 1), 2)?>元</span>"></i><?php }?></td>
				<td><?php echo in_array($order['orderType'], array(41, 42, 43)) ? parse_hemai_status($order['status'], $order['my_status']) : parse_order_status($order['status'], $order['my_status']); ?></td>
				<?php if($order['margin'] > 0):?>
                    <td><strong class="bingo"><?php echo number_format(ParseUnit($order['margin'], 1), 2);?></strong></td>
				<?php elseif(!in_array($order['status'], array('1000', '2000'))):?>
					<td>---</td>
				<?php else:?>
					<td><?php echo number_format(ParseUnit($order['margin'], 1), 2);?></td>
				<?php endif;?>
				<td>
				<?php if (in_array($order['orderType'], array(41, 42, 43))) {?>
					<a target="_blank" href="/hemai/detail/hm<?php echo $order['orderId']; ?>" style="display:block;">查看详情</a>
				<?php if ($order['orderType'] == 41) {?>
                                        <?php if(!in_array($order['lid'], array(JCZQ, JCLQ, SFC, RJ))){ ?>
					<a target="_blank" href="/<?php echo BetCnName::getEgName($order['lid'])?>?orderId=<?php echo $order['orderId'];?>" style="display:block;">继续发起</a>
                                        <?php }else{ ?>
                                        <a target="_blank" href="/<?php echo BetCnName::getEgName($order['lid'])?>" style="display:block;">继续发起</a>
                                        <?php } ?>
				<?php }else {?>
					<a target="_blank" href="/hemai/<?php echo str_replace(array('plw', 'rj'), array('pls', 'sfc'), BetCnName::getEgName($order['lid']))?>" style="display:block;">继续参与</a>
				<?php }
				}else {?>
					<a target="_blank" href="/orders/detail/<?php echo $order['orderId']; ?>" style="display:block;">查看详情</a>
				<?php if($order['status']=='10' && (strtotime("-{$lotteryConfig[$order['lid']]['ahead']} MINUTE", strtotime($order['endTime'])) > time())):?>

					<a class='main-color-s' href="<?php if($order['singleFlag'] ==1): ?>javascript:cx.single({orderId:'<?php echo $order['orderId'];?>'}, {ctype:'paysearch', orderType:0});<?php else: ?>javascript:cx.castCb({orderId:'<?php echo $order['orderId'];?>'}, {ctype:'paysearch', orderType:0});<?php endif; ?>" class="cOrange" style="display:block;">立即支付</a>

				<?php elseif(!in_array($order['lid'], array(JCZQ, JCLQ, SFC, RJ)) && in_array($order['status'], array('1000', '2000'))):
					if (in_array($order['lid'], array(44, 45))):?>
					<a href="<?php echo $url?>" style="display:block;">继续预约</a>
					<?php else:?>
					<a href="/<?php echo BetCnName::getEgName($order['lid']);?>?orderId=<?php echo $order['orderId'];?>" style="display:block;">继续预约</a>
					<?php endif;
					endif;
				}?>
			</tr>
			<?php endforeach;?>
		<?php endif;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7" class="tar">
					<div class="fl">
					<span class="mr20">进行中方案：<strong><?php echo $notover;?></strong> 个</span><span class="mr20">认购金额：<strong><?php echo number_format(ParseUnit($money, 1), 2);?></strong> 元</span><span class="mr20">中奖金额：<strong><?php echo number_format(ParseUnit($prize, 1), 2);?></strong> 元</span>
					</div>
					<div class="fr tar table-page">
						<span class="mlr10">本页<em class="mlr5"><?php echo $cpnum;?></em>条记录</span><span>共<em class="mlr5"><?php echo $pagenum;?></em>页</span>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>
	<!--表格 end-->
	<!-- pagination -->
	<?php echo $pagestr;?>
	<!-- pagination end -->
<script>
$('.hm-jjdetail.bubble-tip').mouseenter(function(){
		$.bubble({
				target:this,
				width:'auto',
				position: 'b',
				align: 'l',
				content: $(this).attr('tiptext')
		})
}).mouseleave(function(){
		$('.bubble').hide();
});
</script>
<?php if(!$this->is_ajax):?>
</div>
</div>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/single.js');?>"></script>
<script type="text/javascript">
var target = '/mylottery/betlog';
$(function(){
	 new cx.vform('.betlog-form', {
		 	checklogin: true,
	        submit: function(data) {
	            var self = this;
	            $.ajax({
	                type: 'post',
	                url:  target,
	                data: data,
	                success: function(response) {
	            		$('#container_betlog-form').html(response);
	                }
	            });
	        }
	 });

     $('#nopay, #marginonly, #kaijiang').on('click', function(){
        $('.betlog-form .submit').trigger('click');
     });
      
});
</script>
</div>
<?php $this->load->view('v1.1/elements/user/menu_tail');?>
<?php endif;?>
