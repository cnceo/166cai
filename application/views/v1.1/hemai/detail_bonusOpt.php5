<!-- 奖金优化明细 -->
<?php if ($showdetail) {?>
<div class="stage-detail stage-table">
<?php if (in_array($detail['lid'], array(JCZQ, JCLQ)) && $bonusOpt) {?>
	<div class="jc-inTable-scroll">
		<table class="jc-inTable">
			<colgroup>
    			<col width="8%">
    			<col width="21%">
    			<col width="14%">
    			<col width="10%">
    			<col width="14%">
    			<col width="14%">
    			<col>
			</colgroup>
			<thead>
				<tr>
					<th>序号</th>
					<th>场次</th>
					<th>过关方式</th>
					<th>投注倍数</th>
					<th>单注奖金</th>
					<th>预计奖金</th>
					<th>实际奖金</th>
				</tr>
			</thead>
		</table>
		<div class="jc-inTable-scroll-body">
			<table class="jc-inTable">
				<colgroup>
        			<col width="8%">
        			<col width="21%">
        			<col width="14%">
        			<col width="10%">
        			<col width="14%">
        			<col width="14%">
        			<col>
    			</colgroup>
				<tbody>
				<?php foreach ($bonusOpt as $value): ?>
				<tr>
                    <td><?php echo $value['subCodeId']; ?></td>
                    <td><p class="matches"><?php echo $value['detail']; ?></p></td>
                    <td><?php echo $value['type']; ?></td>
                    <td><?php echo $value['multi']; ?></td>
                    <td><?php echo number_format(ParseUnit(round($value['singleMoney']), 1),
                            2); ?></td>
                    <td><b><?php echo number_format(ParseUnit(round($value['singleMoney']) * $value['multi'], 1),
                                2); ?></b></td>
                    <?php if ($value['status'] == '2000'): ?>
                        <td><span class="yzj"><?php echo number_format(ParseUnit($value['bonus'],
                                    1), 2); ?></span></td>
                    <?php elseif ($value['status'] == '1000'): ?>
                        <td><span class="wzj">未中奖</span></td>
                    <?php elseif($value['status'] == '600'): ?>
                            <td><span class="wzj">---</span></td>
                    <?php else: ?>
                        <td><span class="ddkj">等待开奖</span></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
<?php }else {?>
<div class="jc-inTable-scroll">
		<table class="jc-inTable">
			<colgroup><col width="3"><col width="37"><col width="4"><col width="4"><col width="6"><col width="15"></colgroup>
			<thead><tr><th></th><th>方案信息</th><th>注数</th><th>倍数</th><th>订单状态</th><th>奖金</th></tr></thead>
		</table>
		<div class="jc-inTable-scroll-body">
			<table class="jc-inTable">
				<colgroup><col width="3"><col width="39"><col width="4"><col width="4"><col width="6"><col width="14"></colgroup>
				<tbody>
				<?php $awardNum = isset($award) ? explode(',', $award['awardNumber']) : array(4,4,4,4,4,4,4,4,4,4,4,4,4,4);
				foreach ($orderDetail as $key => $orderdetail) {?>
					<tr>
						<td><?php echo str_pad($key+1, 2, '0', STR_PAD_LEFT)?></td>
						<td class="tal">
							<div class="number-game">
							<?php $str = '';
							foreach (explode('*', str_replace('4', '-', $orderdetail['codes'])) as $k => $code) {
								$str .= '<em>';
								foreach (explode(',', $code) as $c) {
									$str .= ($c == $awardNum[$k]) ? "<span style='color:red;'>".$c."</span>" : $c;
								}
								$str .= '</em>,';
							}
							echo substr($str, 0, -1);?>
							</div>
						</td>
						<td><?php echo $orderdetail['betTnum'] ? $orderdetail['betTnum'] : 1?></td>
						<td><?php echo $orderdetail['multi']?></td>
						<td><?php echo $this->lotterydetail->getTicketStatus($orderdetail['status'], true)?></td>
						<td><?php echo $this->lotterydetail->getTicketBonus($orderdetail['status'], $orderdetail['bonus'])?></td>
					</tr>
				<?php }?>
					
				</tbody>
			</table>
		</div>
	</div>
<?php }?>
</div>
<?php }elseif ($orderInfo['openStatus'] == 1) {?>
	<div class="promptTxt clearfix"><i class="i-prompt-icon"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/icon-lock.png')?>" alt="" title=""></i><span class="sTit">仅跟单者可见</span><span class="sDes">参与认购方案可查看详情</span></div>
<?php }else {?>
	<div class="promptTxt clearfix"><i class="i-prompt-icon"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/icon-time-sand.png')?>" alt="" title=""></i><span class="sTit">截止后可见</span><span class="sDes">官方销售截止后可查看详情</span></div>
<?php }?>
<!-- 奖金优化明细 -->