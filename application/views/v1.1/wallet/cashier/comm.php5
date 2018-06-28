<div class="cp-box-hd product-info">
<?php $datetime = strtotime($data['created']);?>
    <h2 class="tit">商品信息：<?php echo BetCnName::getCnName($data['lid']);?><?php if($orderType!=5){ ?>第<?php echo $data['issue'];?>期<?php } ?></h2>
    <p class="buy-time">购买时间：<?php echo date('Y', $datetime)."年".date('m', $datetime)."月".date('d', $datetime)."日 ".date('H:i:s', $datetime);?></p>
    <p class="order-num">订单编号：<?php echo $data['orderId'];?></p>
    <span class="total-money"id="total_money" data-totalMoney='<?php echo ParseUnit($data['money'], 1);?>' >总金额：<b><?php echo ParseUnit($data['money'], 1);?></b>元</span>
</div>