<!-- 订单详情 -->
<div class="wrap_in">
<div class="detail-container cp-box sfc">
    <!--彩票信息-->
    <?php $this->load->view('v1.1/elements/lottery/detail_info_panel', array('noIssue' => false)); ?>
    <script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/single.js');?>"></script>
    <!--彩票信息end-->
    
	<div class="stage-detail">
		<div class="hd clearfix">
			<div class="plan-state"><em class="t">方案状态<i></i></em><span class="state"><?php echo parse_order_status($order['status'], $order['my_status']); ?></span></div>
			<span class="stage">第 <b class="spec"><?php echo $order['issue']; ?></b> 期<em class="bet-type">普通投注</em></span>
			<span class="order-num">订单编号：<?php echo $order['orderId']; ?></span>
		</div>
		<div class="stage-table">
			<table>
				<colgroup>
                	<col width="102">
                    <col>
                </colgroup>
				<tr>
					<th class="s-th">方案进度</th>
					<td class="process-td s-td">
						<div class="process-order" id="process">
                        <?php $this->load->view('v1.1/elements/order/precess', array('status' => $order['status'], 'shopId' => $order['shopId'], 'created' => $order['created'], 'pay_time' => $order['pay_time'], 'ticket_time' => $order['ticket_time'])); ?>
            			</div>
					</td>
				</tr>
				<tr>
					<th class="s-th">发起人</th>
                    <td class="s-td">
                    	<div class="s-td-mocks">
                        	<span class="username"><?php echo $uname; ?></span>
                            <span class="s-th">方案金额</span>
                            <div class="money">
                            	<div class="s">
                                	<b class="main-color-s"><?php echo ParseUnit( $order['money'], 1 ); ?></b>元<em>（ <?php echo $order['betTnum']; ?>注<?php echo $order['multi']; ?>倍）</em>
                                	<?php if($order['redpackId'] && $order['status'] >='40'):?>
                                    <span class="fcw"><i class="icon-font specil-color">&#xe645;</i>红包抵扣<?php echo ParseUnit( $order['redpackMoney'], 1 );?>元，实付<?php echo ParseUnit($order['money'] - $order['redpackMoney'], 1);?>元</span>
                                    <?php endif;?>
                            	</div>
                                <s></s>
                         	</div>
                        </div>
                    </td>
				</tr>
				<tr>
					<th class="s-th">本期开奖结果</th>
					<td class="s-td">
						<div class="award-nums startup" id="award-nums"><?php echo $award;?></div>
					</td>
				</tr>
				<tr>
					<th class="s-th">中奖信息</th>
					<td class="s-td">
						<span class="startup">
							<?php if ($order['status'] == 2000):?>
								<?php if($order['bonus'] != $order['margin']):?>
								税前奖金<b class="spec"> <?php echo number_format($order['bonus']/100, 2);?></b>元，税后奖金<b class="spec"> <?php echo number_format($order['margin']/100, 2);?></b>元
								<?php else: ?>
								<?php echo parse_order_status($order['status'], $order['my_status']);?>
								<b class="spec"> <?php echo number_format($order['margin']/100, 2);?></b>元
								<?php endif;?>
							<?php else:?>
								<?php echo parse_order_status($order['status'], $order['my_status']);?>
							<?php endif;?>
							<?php if($order['status']=='10' && (strtotime("-{$lotteryConfig[$order['lid']]['ahead']} MINUTE", strtotime($order['endTime'])) > time())):?>
								<a href="javascript:<?php if($order['singleFlag'] ==1): ?>cx.single<?php else: ?>cx.castCb<?php endif; ?>({orderId:'<?php echo $order['orderId'];?>'}, {ctype:'paysearch', orderType:0});" class="btn-ss btn-main">立即支付</a>
							<?php endif;?>
						</span>
					</td>
				</tr>
            <?php if($matchInfo): ?>
				<tr>
					<th class="s-th">方案内容</th>
					<td class="s-td">
                        <!-- 方案内容 strat -->
                        <table class="jc-inTable">
                            <tbody>
                                <tr class="th-bg-fix">
                                    <th width="7%">场次</th>
                                    <td width="6%">1</td>
                                    <td width="6%">2</td>
                                    <td width="6%">3</td>
                                    <td width="6%">4</td>
                                    <td width="6%">5</td>
                                    <td width="6%">6</td>
                                    <td width="6%">7</td>
                                    <td width="6%">8</td>
                                    <td width="6%">9</td>
                                    <td width="6%">10</td>
                                    <td width="6%">11</td>
                                    <td width="6%">12</td>
                                    <td width="6%">13</td>
                                    <td width="6%" class="last">14</td>
                                </tr>
                                <tr class="text-vertical">
                                    <th><span>主队</span></th>
                                    <?php foreach ($matchInfo as $matchinfo): ?>
                                        <td><span><?php echo $matchinfo['teamName1']; ?></span></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr class="text-vertical">
                                    <th><span>客队</span></th>
                                    <?php foreach ($matchInfo as $matchinfo): ?>
                                        <td><span><?php echo $matchinfo['teamName2']; ?></span></td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php $rowspan = count($betArr);?>
                                <?php foreach ($betArr as $key =>$value):?>
                                <tr>
                                	<?php if($key == '0'):?>
                                    <th rowspan="<?php echo $rowspan;?>">投注方案</th>
                                    <?php endif;?>
                                    <?php foreach ($value as $betarry): ?>
                                        <td>
                                            <?php if($betarry): ?>
                                            <?php foreach ($betarry as $betstr): ?>
                                                <b <?php if($betstr['is_win']=='1'): ?>class="num-winners"<?php endif; ?>><?php echo $betstr['bet']; ?></b>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            -
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                        <!-- 方案内容 end -->
					</td>
				</tr>
            <?php endif; ?>
            <?php if(!empty($ticketDetail)):?>
                <tr>
                    <th class="s-th">出票明细</th>
                    <td class="s-td">
                        <div>
                            <div class="jc-inTable-scroll">
                                <table class="jc-inTable">
                                    <colgroup>
                                        <col width="372">
                                        <col width="100">
                                        <col width="100">
                                        <col width="100">
                                        <col width="180">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>方案信息</th>
                                        <th>注数</th>
                                        <th>倍数</th>
                                        <th>订单状态</th>
                                        <th>奖金</th>
                                    </tr>
                                    </thead>
                                </table>
                                <div class="jc-inTable-scroll-body">
                                    <table class="jc-inTable">
                                        <colgroup>
                                            <col width="372">
                                            <col width="100">
                                            <col width="100">
                                            <col width="100">
                                            <col width="163">
                                        </colgroup>
                                        <tbody>
                                            <?php foreach ($ticketDetail as $ticket): ?>
                                            <tr>
                                                <td class="tal"><div class="number-game"><?php echo $ticket['ticketInfo']; ?></div></td>
                                                <td><?php echo $ticket['betTnum']; ?></td>
                                                <td><?php echo $ticket['multi']; ?></td>
                                                <td><?php echo $ticket['ticketStatus']; ?></td>
                                                <td><?php echo $ticket['bonusStatus']; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody> 
                                    </table>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endif;?>
                <!-- 声明 -->
                <?php $this->load->view('v1.1/elements/lottery/statement'); ?>  
			</table>
		</div>
        <div class="stage-detail-tips" style="">
            <h5 style="color: #666;">温馨提示：</h5>
            <?php $this->load->view('elements/lottery/tips'); ?>
        </div>
	</div>
</div>
</div>
<!-- 订单详情end -->
<script>
    $(function(){

        var w = 0;
        var th = $('thead tr:first', '#jcInTableHead');
        var tb = $('tbody tr:first', '#jcInTableBody');
        $('table', '.jc-inTable-scroll').css({});
        $('th, td','.jc-inTable-scroll').each(function(i){
            w = $(this).width();

            $('th:eq('+i+'), td:eq('+i+')', th).css('width',w+'px');
            $('th:eq('+i+'), td:eq('+i+')', tb).css('width',w+'px');
        });

        $('body').on('click', '.ptips-bd-close', function () {
            $(this).closest('.mod-tips-b').hide();
        });
    })
</script>