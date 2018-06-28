<!-- 订单详情 -->
<div class="wrap_in">
<div class="detail-container bet-num mod-box">
    <!--彩票信息-->
    <?php $this->load->view('v1.1/elements/lottery/detail_info_panel', array('noIssue' => false)); ?>
    <!--彩票信息end-->
    
	<div class="stage-detail mod-box-box">
		<div class="hd clearfix">
			<div class="plan-state"><em class="t">方案状态<i></i></em><span class="state"><?php echo parse_order_status($order['status'], $order['my_status']); ?></span></div>
			<span class="stage">第 <b class="spec"><?php echo (in_array($order['lid'], array(KS, JLKS))) ? substr($order['issue'], 2) : $order['issue']; ?></b> 期<em class="bet-type"><?php if (in_array($order['orderType'], array(1, 6))) {?>追号<?php }else {?>自购 <?php if($order['isChase']):?>追加<?php endif; }?></em></span>
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
                        <?php $this->load->view('v1.1/elements/order/precess', array('status' => $order['status'], 'shopId' => $order['shopId'], 'failMoney' => $order['failMoney'], 'created' => $order['created'], 'pay_time' => $order['pay_time'], 'ticket_time' => $order['ticket_time'])); ?>
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
                        <table style="width: auto;">
                            <tr>
                                <td>
                                    <div class="award-nums startup"><?php echo $award;?></div>
                                </td>
                                <?php if($order['status'] >= '500' && isset($order['lsDetail']) && !empty($order['lsDetail']['detail'])): ?>
                                <td>
                                    <span class="lsj-link-group">
                                        <a href="/orders/lsDetail/<?php echo $orderId;?>" class="link-color" target="_blank">查看订单乐善码<?php if($order['lsDetail']['totalMargin'] > 0):?>（中奖）<?php endif;?> &gt;</a>
                                        <a href="/info/csxw/132122" class="only-icon" title="什么是乐善码" target="_blank"><i class="icon-font"></i></a>
                                    </span>
                                </td>
                                <?php endif;?> 
                            </tr>
                        </table>
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
						</span>
						<?php if($order['status'] == 2000 && $otherBonus > 0):?>
							<span class="jiajiang-tips">奖金：<em><?php echo number_format(($order['margin'] - $otherBonus)/100, 2);?></em>元 + 加奖：<em><?php echo number_format($otherBonus/100, 2);?></em>元</span>
						<?php endif;?>
						<?php if($order['status']=='10' && (strtotime("-{$lotteryConfig[$order['lid']]['ahead']} MINUTE", strtotime($order['endTime'])) > time())):?>
							<a  href="javascript:<?php if($order['singleFlag'] ==1): ?>cx.single<?php else: ?>cx.castCb<?php endif; ?>({orderId:'<?php echo $order['orderId'];?>'}, {ctype:'paysearch', orderType:0});" class="btn-ss btn-main">立即支付</a>
						<?php endif;?>
					</td>
				</tr>      
                <?php if(!empty($ticketDetail)):?>
                <tr>
                    <th class="s-th">方案内容</th>
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
                                                <td class="tal"><div class="number-game"><?php echo $ticket['code']; ?></div></td>
                                                <td><?php echo $ticket['betNum']; ?></td>
                                                <td><?php echo $ticket['multi']; ?></td>
                                                <td><?php echo $ticket['ticketStatus']; ?></td>
                                                <td><?php echo $ticket['bonusStatus']; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody> 
                                    </table>
                                </div>
                            </div>
                            <?php if(!in_array($lotteryId, array(RJ, SFC))):?>
                                <?php if(in_array($lotteryId, array(SYXW, JXSYXW, HBSYXW))):?>
                                <a href="/<?php echo BetCnName::getEgName($lotteryId);?>?orderId=<?php echo $orderId;?>" class="goon-lnk">继续购买此方案</a>
                                <?php else:?>
                                <a href="/<?php echo BetCnName::getEgName($lotteryId);?>?orderId=<?php echo $orderId;?>" class="goon-lnk">继续购买此方案</a>
                                <?php endif;?>
                            <?php endif;?>
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
            <?php $this->load->view('v1.1/elements/lottery/tips'); ?>
        </div>
	</div>
</div>
</div>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/single.js');?>"></script>
<!-- 订单详情end -->