<!-- 出票明细 -->
<?php if ($showdetail) {?>
<div class="stage-detail stage-table">
<?php if (in_array($detail['lid'], array(JCZQ, JCLQ))) {?>
	<div class="jc-inTable-scroll">
		<table class="jc-inTable">
			<colgroup><col width="4%"><col width="31%"><col width="7%"><col width="4%"><col width="4%"><col width="7%"><col width="8%"><col width="18%"></colgroup>
			<thead><tr><th>序号</th><th>场次</th><th>过关方式</th><th>注数</th><th>倍数</th><th>投注金额</th><th>订单状态</th><th>奖金</th></tr></thead>
		</table>
		<div class="jc-inTable-scroll-body">
			<table class="jc-inTable">
				<colgroup><col width="4%"><col width="32%"><col width="8%"><col width="4%"><col width="4%"><col width="7%"><col width="7%"><col width="17%"></colgroup>
				<tbody>
				<?php
	
	$i = 0;
	$sfcArr = array('1' => '1-5', '2' => '6-10', '3' => '11-15', '4' => '16-20', '5' => '21-25', '6' => '26-30', '7' => '26+');
	foreach ( $orderDetail as $orderdetail )
	{
		$i ++;
		?>
					<tr>
						<td><?php echo $i?></td>
						<td><p class="matches">
								<?php
		$codes = explode ( '|', $orderdetail ['codes'] );
		$codes = explode ( '*', $codes [0] );
		$codesArr = array();
		foreach ($codes as $cd) {
			$codesArr[] = explode(',', $cd);
		}
		foreach ($codesArr as $k => $codestr) {
			if ($codestr[0]) echo $this->lotterydetail->ticketDetail($codestr[0], $codestr[1], $codestr[2], $orderdetail['info'][$codestr[0]], $detailres);
		}?>

							</p></td>
						<td><?php echo count($orderdetail['info']) > 1 ? $this->lotterydetail->getjjcPlayType($orderdetail['playType']) : '单关'?></td>
						<td><?php echo $orderdetail['betTnum']?></td>
						<td><?php echo $orderdetail['multi']?></td>
						<td><b><?php echo number_format(ParseUnit($orderdetail['money'], 1), 2)?></b></td>
						<td><?php echo $this->lotterydetail->getTicketStatus($orderdetail['status'], true)?></td>
						<td><?php echo $this->lotterydetail->getTicketBonus($orderdetail['status'], $orderdetail['bonus'])?></td>
					</tr>
				<?php }?>
				</tbody>
			</table>
		</div>
		<p>注数倍数：  <?php echo $detail['betTnum']?>注 <?php if ($detail['playType'] < 7) echo $detail['multi']."倍"?></p>
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
<!-- 出票明细 -->
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

        // hover效果
        $('.bubble-tip').mouseenter(function() {
            $.bubble({
                target: this,
                position: 'b',
                align: 'l',
                content: $(this).attr('tiptext'),
                width: '240px',
                autoClose: false
            })
        }).mouseleave(function() {
            $('.bubble').hide();
        });
        $('.matches .bubble-tip').mouseenter(function() {
            $.bubble({
                target: this,
                position: 'b',
                align: 'l',
                content: $(this).attr('tiptext'),
                width: 'auto',
                autoClose: false
            })
        }).mouseleave(function() {
            $('.bubble').hide();
        });
    })
</script>