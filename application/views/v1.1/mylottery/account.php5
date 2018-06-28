<?php $this->load->view('v1.1/elements/user/menu');?>
<div class="l-frame-cnt">
<div class="uc-main">
    <div class="tit-b"><h2>最近投注记录</h2><a href="/mylottery/betlog" class="more">全部投注记录</a></div>

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
			<?php $ctime = strtotime($order['created']);
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
				<td><a href="/<?php echo $url;?>"  target = "_blank"><?php echo BetCnName::getCnName($order['lid']); ?></a></td>
				<td class="tal <?php if($order['add_money'] > 0){echo "jiajiang-tag";}?>">
				<?php if ($order['orderType'] == 42) {
					echo "发起人："?><a target="_blank" href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $order['uid'])), 'ENCODE'));?>"><?php echo $order['nick_name']?></a>
				<?php }else {
					echo "期次 ".$order['issue'];if ($order['orderType'] == 41) {?><span class="hm-progress">进度:<b><?php echo round($order['buyTotalMoney'] * 100/$order['money'], 2)?>%</b></span><?php }
				}?>
				<br>
				<?php switch ($order['orderType']) {case 1:?>追号<?php break;case 3:?>不赚包赔<?php break;case 41:?>发起合买<?php break;case 42:?>参与合买<?php break;case 6:?>追号包赔<?php break;default:?>自购<?php break; } if($order['isChase'] && in_array($order['orderType'], array(0, 1))):?>追加<?php endif;?>
				</td>
				<td class="tar"><?php echo number_format(ParseUnit(($order['orderType'] == 41 && strtotime($order['endTime']) < time()) ? ($order['buyMoney'] + (int)$order['guarantee']): $order['buyMoney'], 1), 2);?>
				<?php if ($order['orderType'] == 41 && strtotime($order['endTime']) < time() && (int)$order['guarantee'] > 0) {?><i class="hm-jjdetail bubble-tip" tiptext="<span class='coffe'>认购<?php echo number_format(ParseUnit($order['buyMoney'], 1), 2)."元+保底转认购".number_format(ParseUnit((int)$order['guarantee'], 1), 2)?>元</span>"></i><?php }?></td>
				<td><?php echo in_array($order['orderType'], array(41, 42)) ? parse_hemai_status($order['status'], $order['my_status']) : parse_order_status($order['status'], $order['my_status']); ?></td>
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
					<a target="_blank" href="/<?php echo BetCnName::getEgName($order['lid'])?>" style="display:block;">继续发起</a>
				<?php }else {?>
					<a target="_blank" href="/hemai/<?php echo str_replace(array('plw', 'rj'), array('pls', 'sfc'), BetCnName::getEgName($order['lid']))?>" style="display:block;">继续参与</a>
				<?php }
				}else {?>
					<a target="_blank" href="/orders/detail/<?php echo $order['orderId']; ?>" style="display:block;">查看详情</a>
				<?php if($order['status']=='10' && (strtotime("-{$lotteryConfig[$order['lid']]['ahead']} MINUTE", strtotime($order['endTime'])) > time())):?>
					<a href="javascript:cx.castCb({orderId:'<?php echo $order['orderId'];?>'}, {ctype:'paysearch', orderType:0});" class="cOrange main-color-s" style="display:block;">立即支付</a>
				<?php elseif(!in_array($order['lid'], array(JCZQ, JCLQ, SFC, RJ)) && in_array($order['status'], array('1000', '2000'))):
					if (in_array($order['lid'], array(44, 45))):?>
					<a href="/gjc" style="display:block;">继续预约</a>
					<?php else:?>
					<a href="/<?php echo BetCnName::getEgName($order['lid']);?>?orderId=<?php echo $order['orderId'];?>" style="display:block;">继续预约</a>
					<?php endif;
					endif;
				}?>
			</tr>
			<?php endforeach;?>
        <?php else: ?>
            <tr class="no-log">
                <td colspan="7">
                    没有记录，去<a class="main-color-s" href="/wallet/recharge" target="_self">充值</a>或<a class="main-color-s" href="/hall" target="_self">购买彩票</a>，大奖等你拿!
                </td>
            </tr>
		<?php endif;?>
		</tbody>

	</table>
	<!--表格 end-->
</div>
</div>
<script type="text/javascript">
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

<?php $this->load->view('v1.1/elements/user/menu_tail');?>
