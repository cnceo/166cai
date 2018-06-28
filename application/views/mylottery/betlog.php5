<?php if(!$this->is_ajax):?>
<?php $this->load->view('elements/user/menu');?>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
<div class="article">
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
	        <label for="nopay"><input type="checkbox" class="ckbox vcontent" name="nopay" id="nopay">未支付</label>
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
			<?php 
				$ctime = strtotime($order['created']);
				$notOver += (in_array($order['status'], array('600', '1000', '2000'))? 0 : 1);
			?>
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
<?php if(!$this->is_ajax):?>
</div>
        <script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>
<script type="text/javascript">
var target = '/mylottery/betlog';
$(function(){
	 new cx.vform('.betlog-form', {
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

     $('#nopay').on('click', function(){
        $('.betlog-form .submit').trigger('click');
     });
});

function conPay( orderId )
{
    var content;
    // 根据订单号, 获取订单状态, 信息(类型, 玩法, 期次), 用户剩余金额, 订单金额
    $.ajax({
        type: 'post',
        url: 'orders/info',
        data: { orderId: orderId },
        success: function(response) {
            //console.log(response);
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
</div>
<?php $this->load->view('elements/user/menu_tail');?>
<?php endif;?>