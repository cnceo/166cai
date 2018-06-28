<?php if($orders):?>
<?php foreach ($orders as $order):?>
<?php
$winClass = '';
$status = parse_order_status($order['status'], $order['my_status']);
if($order['margin'] > 0 || $status == '待付款')
{
	$winClass = 'bet-history-winning';
}
?>
<li class="cp-list-txt <?php echo $winClass;?>">
    <a href="<?php echo $this->config->item('base_url') . "order/detail/{$order['orderId']}/".urlencode($strCode);?>">
	<div>
	    <p><?php echo BetCnName::$BetCnName[$order['lid']]; ?><span><?php if($order['lid'] == BetCnName::PLS || $order['lid'] == BetCnName::FCSD){echo BetCnName::$playCnName[$order['lid']][$order['playType']];} ?></span></p>
	    <p><?php if($order['lid'] != BetCnName::JCZQ && $order['lid'] != BetCnName::JCLQ): ?><em>普通投注</em><?php endif; ?><?php echo number_format(ParseUnit($order['money'], 1), 2);?>元</p>
	</div>
	<div>
	    <p><time><?php echo date('m-d H:i', strtotime($order['created']));?></time></p>
	    <p><s><?php if($order['margin'] > 0){ echo "中奖".number_format(ParseUnit($order['margin'], 1), 2)."元";}else{ echo $status;} ?></s></p>
	</div>
    </a>
</li>
<?php endforeach;?>
<?php endif;?>