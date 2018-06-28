<?php if(!$this->is_ajax):?>
<?php $this->load->view('v1.1/elements/user/menu');?>
<div class="l-frame-cnt">
<div class="uc-main">
	<h2 class="tit">追号记录</h2>
	<!--表单筛选 begin-->
	<div class="filter-oper">
		<div class="lArea chaselog-form">
			<input type='hidden' class='submit' >
			<dl class="simu-select select-small" data-target='submit'>
	            <dt>
	            	<span class='_scontent'>最近六个月</span><i class="arrow"></i>
	            	<input type='hidden' name='date' class="vcontent" value='3' >
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
	            	<input type='hidden' name='lid' class="vcontent" value='0' >
	            </dt>
	            <dd class="select-opt">
	            	<div class="select-opt-in" data-name='lid'>
                        <?php foreach( $betType as $key => $val ): ?>
                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?></a>
                        <?php endforeach; ?>
		            </div>
	            </dd>
	        </dl>
	        <label for="is_chase"><input type="checkbox" class="ckbox vcontent" name="is_chase" id="is_chase">进行中</label>
	        <label for="has_bonus"><input type="checkbox" class="ckbox vcontent" name="has_bonus" id="has_bonus">只看中奖</label>
		</div>
	</div>
<!--表格 begin-->
<div id='container-chaselog'>
<?php endif;?>
<div>
		<table class="mod-tableA">
			<thead>
				<tr>
					<th width="10%">创建时间</th>
					<th width="12%">彩种</th>
					<th width="20%">已追期数/总期数</th>
					<th class="tar" width="15%">投注总额（元）</th>
					<th width="16%">订单状态</th>
					<th width="15%">总奖金（元）</th>
					<th width="12%">操作</th>
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
					<a href="/<?php echo BetCnName::getEgName($order['lid']);?>" target = "_blank">
						<?php echo BetCnName::getCnName($order['lid']); ?>
					</a>
					<?php if(!empty($order['chaseType'])):?>
					<img height="17" width="31" alt="包赔" class="icon-bp" src="/caipiaoimg/v1.1/img/icon-bp.png">
					<?php endif;?>
					</td>
					<td><span class="num-red"><?php echo $order['chaseIssue']?></span>/<?php echo $order['totalIssue']?></td>
					<td><?php echo number_format(ParseUnit($order['money'], 1), 2);?></td>
					<td><?php echo parse_chase_status($order['status']); ?></td>
					<td><?php echo $order['bonus'] > 0 ? "<div class='bingo'>".number_format(ParseUnit($order['bonus'], 1), 2)."</div>" : ($order['status'] == 700 ? '0.00' : '--'); ?></td>
					<td>
						<a target="_blank" href="/chases/detail/<?php echo $order['chaseId']; ?>" style="display:block;">查看详情</a>
						<?php if($order['status'] == 0 && (strtotime("-{$lotteryConfig[$order['lid']]['ahead']} MINUTE", strtotime($order['endTime'])) > time())) :?>
							<a class='main-color-s' href="<?php if($order['singleFlag'] ==1): ?>javascript:singlePay('<?php echo $order['chaseId'];?>');<?php else: ?>javascript:conPay('<?php echo $order['chaseId'];?>');<?php endif; ?>" class="cOrange" style="display:block;">立即支付</a>
						<?php elseif($order['status'] == 240 && $order['hasstop'] > 0):?>
							<a data-chase='<?php echo $order['chaseId'];?>' data-hasstop="<?php echo $order['hasstop']?>" data-ename="<?php echo BetCnName::getEgName($order['lid']);?>" data-lid='<?php echo $order['lid']?>' data-bonus="<?php $order['bonus']?>" class="stopChase" style="display:block;">停止追号</a>
						<?php elseif($order['status'] >= 240):?>
							<a target="_blank" href="/<?php echo BetCnName::getEgName($order['lid']);?>?chaseId=<?php echo $order['chaseId'];?>" style="display:block;">继续预约</a>
						<?php endif;?>
				</tr>
				<?php endforeach;?>
			<?php else :?>
				<tr>
					<td colspan="7" class="no-data">
						<p>没有记录,去 <a class="main-color-s" href="wallet/recharge">充值</a> 或 <a class="main-color-s" href="hall">购买彩票</a> ,大奖等你拿!</p>
					</td>
				</tr>
			<?php endif;?>
			</tbody>
			<?php if (!empty($orders)) {?>
			<tfoot>
				<tr>
					<td colspan="7" class="tar">
						<div class="fl">
						<span class="mr20">进行中方案：<strong><?php echo $totals['chaseing'];?></strong> 个</span><span class="mr20">认购金额：<strong><?php echo number_format(ParseUnit($totals['money'], 1), 2);?></strong> 元</span><span class="mr20">中奖金额：<strong><?php echo number_format(ParseUnit($totals['bonus'], 1), 2);?></strong> 元</span>
						</div>
						<div class="fr tar table-page">
							<span class="mlr10">本页<em class="mlr5"><?php echo count($orders);?></em>条记录</span><span>共<em class="mlr5"><?php echo $pagenum;?></em>页</span>
						</div>
					</td>
				</tr>
			</tfoot>
			<?php }?>
		</table>
		<?php if (!empty($orders)) { echo $pagestr;}?>
</div>
<?php if(!$this->is_ajax):?>
</div>
</div>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/lottery_gaoji.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/single.js');?>"></script>
<script type="text/javascript">
var target = '/mylottery/chaselog';
$(function(){
	 new cx.vform('.chaselog-form', {
		 	checklogin: true,
	        submit: function(data) {
	            var self = this;
	            $.ajax({
	                type: 'post',
	                url:  target,
	                data: data,
	                success: function(response) {
	            		$('#container-chaselog').html(response);
	                }
	            });
	        }
	 });

     $('#is_chase, #has_bonus').on('click', function(){
        $('.chaselog-form .submit').trigger('click');
     });

});
$("#container-chaselog").on('click',".stopChase",function(){
	var self = $(this);
	var chaseId = self.data('chase');
	var lid = self.data('lid');
	var bonus = self.data('bonus');
	var ename = self.data('ename');
	cx.Alert({
		content:'<i class="icon-font">&#xe611;</i>真的要停止追号吗？',
		cancel:'取消',
		addtion:1,
		confirmCb: function(){
			$.ajax({
				type: 'post',
				url:'chases/stopChase',
				data:{chaseId:chaseId, lid:lid},
				success: function(data){
					cx.Confirm({
						btns: [{type: 'confirm',href: 'javascript:;', txt: '确定'}],
						content:'<div class="fz18 pt10 yahei c333 pop-new"><div class="pop-txt text-indent" style="margin-bottom:0px"><i class="icon-font">&#xe611;</i>您好，停止追号操作成功。将退款至您的账<br>户，请注意查收。</div></div>'
					});
					if (data != 1) {
						if (bonus == 0) {
							self.parents('tr').find('td:eq(5)').html('0.00');
						}
						self.parents('tr').find('td:eq(4)').html('追号完成');
					}
					self.replaceWith('<a target="_blank" href="/'+ename+'?chaseId='+chaseId+'">继续预约</a>')
				}
			});
		},
		cancel:'取消'
	});
})
function conPay( chaseId )
{    // 根据订单号, 获取订单状态, 信息(类型, 玩法, 期次), 用户剩余金额, 订单金额
    cx.castCb({orderId:chaseId}, {ctype:'paysearch', orderType:1});
}
</script>
	<!--表格 end-->
</div>
<?php $this->load->view('v1.1/elements/user/menu_tail');?>
<?php endif;?>
