<?php $this->load->view('elements/user/menu');?>
<div class="article">
    <div class="tit-b"><h2>最近投注记录</h2><a href="/mylottery/betlog" class="more">全部投注记录</a></div>

	<!--表格 begin-->
	<table class="mod-tableA">
		<thead>
			<tr>
				<th width="10%">时间</th>
				<th width="12%">彩种玩法</th>
				<th width="20%">订单信息</th>
				<th class="tar" width="15%">订单金额（元）</th>
				<th width="16%">订单状态</th>
				<th width="15%">我的奖金</th>
				<th width="12%">方案详情</th>
			</tr>
		</thead>
		<tbody>
		<?php if(!empty($orders)):?>
			<?php foreach ($orders as $order):?>
			<?php $ctime = strtotime($order['created']);?>
			<tr>
				<td><strong><?php echo date('m月d日', $ctime);?></strong><br /><?php echo date('H:i:s', $ctime);?></td>
				<td>
				<a href="/<?php echo BetCnName::getEgName($order['lid']);?>">
					<?php echo BetCnName::$BetCnName[$order['lid']]; ?>
				</a>
				<?php if(!empty($order['playType'])):?>
					<br>
					<a href="/<?php echo BetCnName::getEgName($order['lid']);?>/<?php echo BetCnName::$playTypeEgName[$order['lid']][$order['playType']]; ?>">
						<?php echo BetCnName::$playTypeCnName[$order['lid']][$order['playType']]; ?>
					</a>
				<?php endif;?>
				</td>
				<td class="tal im_pl40">期次 <?php echo $order['issue'];?><br>普通投注</td>
				<td class="tar"><?php echo number_format(ParseUnit($order['money'], 1), 2);?></td>
				<td><?php echo parse_order_status($order['status'], $order['my_status']); ?></td>
				<?php if($order['margin'] > 0):?>
                                <td><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/gold.png');?>" alt="">&nbsp;<strong class="spec arial"><?php echo number_format(ParseUnit($order['margin'], 1), 2);?></strong></td>
				<?php elseif(!in_array($order['status'], array('1000', '2000'))):?>
					<td><?php echo '---';?></td>
				<?php else:?>
					<td><?php echo number_format(ParseUnit($order['margin'], 1), 2);?></td>
				<?php endif;?>
				<td>
					<a target="_blank" href="/orders/detail/<?php echo $order['orderId']; ?>" style="display:block;">查看详情</a>
					<?php if($order['status']=='10' && ($order['endTime'] > date('Y-m-d H:i:s'))):?>
					<a href="javascript:conPay('<?php echo $order['orderId'];?>');" class="cOrange" style="display:block;">立即支付</a>
					<?php elseif(!in_array($order['lid'], array(BetCnName::JCZQ, BetCnName::JCLQ, BetCnName::SFC, BetCnName::RJ)) && in_array($order['status'], array('1000', '2000'))):?>
						<?php if(in_array($order['lid'], array(BetCnName::SYYDJ, BetCnName::FCSD, BetCnName::PLS))):?>
						<a href="/<?php echo BetCnName::getEgName($order['lid']);?>/<?php echo BetCnName::$playTypeEgName[$order['lid']][$order['playType']]; ?>?orderId=<?php echo $order['orderId'];?>" style="display:block;">继续投注</a>
						<?php else:?>
						<a href="/<?php echo BetCnName::getEgName($order['lid']);?>?orderId=<?php echo $order['orderId'];?>" style="display:block;">继续投注</a>
						<?php endif;?>
					<?php endif;?>
				</td>
			</tr>
			<?php endforeach;?>
        <?php else: ?>
            <tr class="no-log">
                <td colspan="7">
                    没有记录，去<a href="/wallet/recharge" target="_self">充值</a>或<a href="/hall" target="_self">购买彩票</a>，大奖等你拿!
                </td>
            </tr>
		<?php endif;?>
		</tbody>

	</table>
	<!--表格 end-->
</div>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>
<script type="text/javascript">
function conPay( orderId )
{
    var content;
    $.ajax({
        type: 'post',
        url: 'orders/info',
        data: { orderId: orderId },
        success: function(response) {
            console.log(response);
            var money = parseFloat( response.money.replace(/,/g, '') );
            var remain_money = parseFloat( response.remain_money.replace(/,/g, '') );

            if( response.type == 'number' ){
                content = betInfo.number( response.LotteryCnName, response.PlayTypeName, response.Issue, response.money, response.remain_money );
            } else if( response.type == 'jc' ) {
                content = betInfo.jc( response.typeCnName, response.money, response.remain_money );
            }

            if( money > remain_money ) {
                new cx.Confirm({
                    content: content,
                    btns: [
                        {
                            type: 'confirm',
                            txt: '去支付',
                            href: baseUrl + 'wallet/directPay?orderId=' + orderId
                        }
                    ]
                });    
            } else {
                new cx.Confirm({
                    title: '确认购彩',
                    content: content,
                    input: 1,
                    confirmCb: function() {
                        datas = { ctype: 'pay', orderId: orderId, money: response.money };
                        if(this.input){
                            datas.pay_pwd = $('#pay_pwd').val();
                            if($('#pay_pwd').data('encrypt') == '1'){
                            	datas.pay_pwd = cx.rsa_encrypt( datas.pay_pwd );
	           	           		 if(!datas.encrypt){
	           	           			datas.encrypt = '';
	           	           		 }
	           	           		 if(datas.encrypt.indexOf('pay_pwd') == -1)
	           	           			datas.encrypt += 'pay_pwd' + '|';
                            }
                        }
                        cx.ajax.post({
                            url: 'order/pay',
                            data: datas,
                            success: function(response) {
                            	//隐藏之前弹窗
                            	$('.pop-confirm').remove();
                            	cx.Mask.hide(); 
                                if (response.code == 12) {
                                    new cx.Confirm({
                                        content: content,
                                        btns: [
                                            {
                                                type: 'confirm',
                                                txt: '去支付',
                                                href: baseUrl + 'wallet/directPay?orderId=' + orderId
                                            }
                                        ]
                                    });    
                                } else {
                                    new cx.Alert({
                                        content: response.msg,
                                        confirmCb: function(){
                                            location.href = location.href;
                                        }
                                    });
                                }
                            }
                        });
                    }
                });
            }
        }
    });
}
</script>

<?php $this->load->view('elements/user/menu_tail');?>
