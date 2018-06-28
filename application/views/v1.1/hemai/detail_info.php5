<!-- 方案内容 -->
<?php if ($awardNum || in_array($orderInfo['status'], array(1000, 2000))) {
	if ($awardNum) {
	$awardNumArr = preg_split('/\||:/', $awardNum);?>
	<div class="lottery-numbers clrearfix"><span class="sTit">本期开奖号码：</span><div class="award-nums"><?php foreach (explode(',', $awardNumArr[0]) as $award) {?><span class="ball ball-red"><?php echo in_array($orderInfo['lid'], array(SSQ, DLT, QLC)) ? str_pad($award, 2, '0', STR_PAD_LEFT) : $award?></span><?php } if ($awardNumArr[1]) {foreach (explode(',', $awardNumArr[1]) as $award) {?><span class="ball ball-blue"><?php echo in_array($orderInfo['lid'], array(SSQ, DLT, QLC)) ? str_pad($award, 2, '0', STR_PAD_LEFT) : $award?></span><?php }}?></div><a href="/kaijiang/<?php echo str_replace(array('pls', 'fcsd', 'plw'), array('pl3', 'fc3d', 'pl5'), BetCnName::getEgName($orderInfo['lid']))?>/<?php echo !in_array($orderInfo['lid'], array(JCZQ, JCLQ)) ? (in_array($orderInfo['lid'], array(DLT, QXC, PLS, PLW, SFC, RJ)) ? substr($orderInfo['issue'], 2) : $orderInfo['issue']) : ''?>" target="_blank" class="a-detail">详情</a></div>
	<?php }
	if (in_array($orderInfo['status'], array(1000, 2000))) {?>
	<p class="total-prize-money"><i class="icon-font">&#xe626;</i>税后总奖金：
	<?php if ($orderInfo['status'] == 1000) {?>
	未中奖</p>
	<?php }else {?>
        <?php if($orderInfo['otherBonus']==0){ ?>
	<em class="emNum"><?php echo number_format(ParseUnit($orderInfo['orderMargin'], 1), 2)?></em>元
        <?php }else {?>
        <em class="emNum"><?php echo number_format(ParseUnit($orderInfo['orderMargin'], 1), 2)?></em>元（奖金：<em class="emNum"><?php echo number_format(ParseUnit(($orderInfo['orderMargin']-$orderInfo['otherBonus']), 1), 2)?></em>元+ 加奖：<em class="emNum"><?php echo number_format($orderInfo['otherBonus']/100, 2);?></em>元）
        <?php } ?>
         <?php if ($orderInfo['margin']) {?>；您的奖金：<em class="emNum"><?php echo number_format(ParseUnit($orderInfo['margin'], 1), 2)?></em>元<?php }?></p>
	<?php }
		}
	}elseif (!empty($orderInfo['ForecastBonusv'])) {$forecastBonusv = explode('|', $orderInfo['ForecastBonusv']) ?>
	    <p class="total-prize-money"><i class="icon-font">&#xe626;</i>预计总奖金：<em class="emNum"><?php echo $forecastBonusv[0]?></em>&nbsp;~&nbsp;<em class="emNum"><?php echo $forecastBonusv[1]?></em>元</p>
	<?php }elseif (!in_array($orderInfo['lid'], array(JCZQ, JCLQ, SFC, RJ))) {?>
	<div class="lottery-numbers clrearfix"><span class="sTit">本期开奖号码：</span><span class="sDes">预计开奖<?php echo date('Y-m-d H:i:s', $awardTime/1000)?>（星期<?php echo $weekdayarr[date('w', $awardTime/1000)]?>）</span></div>
	<?php }
	if ($showdetail) {?>
<div class="stage-detail stage-table">	
<?php if (in_array($orderInfo['lid'], array(SSQ, DLT, FCSD, PLS, PLW, QLC, QXC))) {?>
	<div class="jc-inTable-scroll">
		<table class="jc-inTable">
			<colgroup><col width="37"><col width="4"><col width="4"><col width="9"><col width="13"><col width="19"></colgroup>
			<thead><tr><th>方案信息</th><th>注数</th><th>倍数</th><th>订单状态</th><th>命中</th><th>奖金</th></tr></thead>
		</table>
		<div class="jc-inTable-scroll-body">
			<table class="jc-inTable">
				<colgroup><col width="38"><col width="5"><col width="4"><col width="9"><col width="13"><col width="18"></colgroup>
				<tbody>
				<?php foreach ($codeArr as $code) {?>
					<tr>
						<td class="tal"><div class="number-game"><?php echo $code['code']?></div></td>
						<td><?php echo empty($code['betNum']) ? '---' : $code['betNum']?></td>
						<td><?php echo empty($code['multi']) ? '---' : $code['multi']?></td>
						<td><?php echo $this->lotterydetail->getTicketStatus((isset($code['status']) ? $code['status'] : $orderInfo['status']), true);?></td>
						<td><?php if (empty($code['mz'])) {echo ($orderInfo['status'] < 1000) ? '--' : '0'; } else{
							switch ($orderInfo['lid']) {
								case SSQ:
								case DLT:
								case QLC:
								case QXC:
									$nArr = array('', '一', '二', '三', '四', '五', '六', '七');
									$mz = each($code['mz']);
									echo $nArr[$mz['key']]."等奖".$mz['value']."注";
									unset($mz);
									break;
								case PLS:
								case FCSD:
								case PLW:
									echo $code['mz'][0] == 0 ? '0' : "<span class='main-color-s'>".$code['mz'][0]."</span>注";
									break;
							}
							if (count($code['mz']) > 1) { ?>
							<i class="jj-arrow" tiptext="<ul><?php $i = 0;foreach ($code['mz'] as $k => $mz) {$i++;echo (($i%2) ? '<li>' : '').$nArr[$k]."等奖<em class='main-color-s'>".$mz."</em>注；".(($i%2 == 0) ? '</li>' : '');}?></ul>"></i>
						<?php }
						}?></td>
						<td>
						<?php echo isset($code['status']) ? $this->lotterydetail->getTicketBonus($code['status'], $code['bonus']) : '---';?>
						</td>
					</tr>
				<?php }?>
				</tbody>
			</table>
		</div>
		<p>注数倍数： <?php echo $orderInfo['betTnum']?>注  <?php echo $orderInfo['multi']?>倍</p>
	</div>

<?php }elseif (in_array($orderInfo['lid'], array(SFC, RJ))) {?>
	<table class="jc-inTable">
		<tbody>
			<tr class="th-bg-fix"><th width="7%">场次</th><?php for ($i = 1; $i <= 14; $i++) {?><td width="6%"<?php if ($i == 14) {?> class="last"<?php }?>><?php echo $i?></td><?php }?></tr>
			<tr class="text-vertical"><th><span>主队</span></th><?php foreach ($orderDetail as $detail) {?><td><span><?php echo $detail['teamName1']?></span></td><?php }?></tr>
			<tr class="text-vertical"><th><span>客队</span></th><?php foreach ($orderDetail as $detail) {?><td><span><?php echo $detail['teamName2']?></span></td><?php }?></tr>
			<tr>
				<th>彩果</th>
				<?php if ($awardNum) {
					$awardNumArr = explode(',', $awardNum);
					foreach ($awardNumArr as $awd) {?><td><em class="main-color-s"><?php echo $awd?></em></td><?php }
				}else {
					for ($i = 0; $i < 14; $i++) {?><td>--</td><?php }
				}?>
			</tr>
			<?php $codesArr = explode(';', $orderInfo['codes']);
			foreach ($codesArr as $k => $val) {
			$val = explode(':', $val);
			?>
			<tr>
				<?php if ($k == 0) {?><th rowspan="<?php echo count($codesArr)?>" class="nopadding">投注方案</th><?php }?>
				<?php foreach (explode(',', $val[0]) as $k1 => $v) {?><td>
				<?php foreach (str_split($v) as $v1) {?>
					<em<?php if ($awardNumArr[$k1] == $v1) {?> class="main-color-s"<?php }?>><?php echo str_replace('#', '--', $v1)?></em>
				<?php }?>
				</td><?php }?>
			</tr>
			<?php }?>
		</tbody>
	</table>
	<p>注数倍数： <?php echo $orderInfo['betTnum']?>注  <?php echo $orderInfo['multi']?>倍</p>
<?php }elseif (in_array($orderInfo['lid'], array(JCZQ, JCLQ))) {?>
	<table class="jc-inTable">
	<thead>
	<tr>
	<th width="12%">场次</th>
	<th width="15%">比赛时间</th>
	<th width="23%"><?php echo ($orderInfo['lid'] == JCZQ) ? '主队 VS 客队' : '客队 VS 主队'?></th>
	<th width="14%">玩法</th>
	<th width="10%">比分</th>
	<th width="10%">彩果</th>
	<th width="18%" class="last" width="140">投注方案/出票赔率</th>
	</tr>
	</thead>
	<tbody class="match-award">
	<?php foreach ($orderDetail as $odrdtl) {
		$k = 0;
	foreach ($codeArr[$odrdtl['mid']] as $key => $val) {
		$value = each($val);?>
		<tr>
			<?php if ($k == 0) { $count = count($codeArr[$odrdtl['mid']]);?>
			<td rowspan="<?php echo $count?>"><?php echo $odrdtl['mname']?></td>
			<td rowspan="<?php echo $count?>"><?php echo date('m-d H:i', $odrdtl['dt']/1000)?></td>
			<td rowspan="<?php echo $count?>"><?php echo ($orderInfo['lid'] == JCZQ) ? $odrdtl['home']." VS ".$odrdtl['awary'] : $odrdtl['awary']." VS ".$odrdtl['home']?></td><?php }?>
			<td><?php echo $playTypes[$key].(in_array($key, array('RQSPF', 'RFSF')) ? "(".$odrdtl['let'].")" : '').($key === 'DXF' ? "(".$odrdtl['preScore'].")" : '')?></td>
			<td><p><?php echo empty($odrdtl['score']) ? '---' : $odrdtl['score']?></p></td>
			<td><?php 
			// 彩果
			$sfcArr = array('1' => '1-5', '2' => '6-10', '3' => '11-15', '4' => '16-20', '5' => '21-25', '6' => '26+');
			if (empty($odrdtl['score'])) {
				echo '---';
			}else {
				$score = explode(':', $odrdtl['score']);
				switch ($key) {
					case 'SPF':
						echo ($score[0] == $score[1]) ? '平' : ($score[0] > $score[1] ? '胜' : '负');
						break;
					case 'RQSPF':
						$rqspfRes = array('让负', '让平', '让胜');
						$res = array();
						$letArr = explode('&', $odrdtl['let']);
						foreach ($letArr as $let) 
						{
							$sg = ($score[0]+$let == $score[1]) ? '1' : ($score[0]+$let > $score[1] ? '2' : '0');
							array_push($res, $sg);
						}
						$res = array_unique($res);
						asort($res);
						foreach ($res as $re) 
						{
							echo $rqspfRes[$re] . '<br/>';
						}
						break;
					case 'BQC':
						$scoreHalf = explode(':', $odrdtl['scoreHalf']);
						echo (($scoreHalf[0]==$scoreHalf[1])?'平':($scoreHalf[0]>$scoreHalf[1]?'胜':'负'))."-".(($score[0]==$score[1])?'平':($score[0]>$score[1]?'胜':'负'));
						break;
					case 'CBF':
						echo $odrdtl['score'];
						break;
					case 'JQS':
						echo ($score[0] + $score[1]);
						break;
					case 'DXF':
						$dxfRes = array('小分', '大分');
						$res = array();
						$preScoreArr = explode('&', $odrdtl['preScore']);
						foreach ($preScoreArr as $preScore) 
						{
							$sg = ($score[0] + $score[1] > $preScore) ? '1' : '0';
							array_push($res, $sg);
						}
						$res = array_unique($res);
						asort($res);
						foreach ($res as $re) 
						{
							echo $dxfRes[$re] . '<br/>';
						}
						break;
					case 'RFSF':
						$rfsfRes = array('主胜', '客胜');
						$res = array();
						$letArr = explode('&', $odrdtl['let']);
						foreach ($letArr as $let) 
						{
							$sg = ($score[1] + $let > $score[0]) ? '0' : '1';
							array_push($res, $sg);
						}
						$res = array_unique($res);
						asort($res);
						foreach ($res as $re) 
						{
							echo $rfsfRes[$re] . '<br/>';
						}
						break;
					case 'SF':
					case 'SFC':
						echo (($score[1] > $score[0]) ? '主胜' : '客胜').($value['value'][1] == 'SFC' ? $sfcArr[ceil(abs($score[1]-$score[0])/5)] : '');
						break;
					default:
						break;
				}
			}?></td>
			<td><?php foreach ($val as $v) {
			foreach (explode('/', $v[3]) as $v1) {?>
			<p><span <?php preg_match('/(3|1|0|\d\-\d|\d:\d|[0-7]|\d+)(\{(.+)\})?\((.+)\)/', $v1, $matches);
			$isRight = FALSE;
			$rightOdds = array();
			$getRight = 0;
			if ($odrdtl['score']) {
				switch ($key) {
					case 'SPF':
						$isRight = ((($score[0] == $score[1]) ? 1 : ($score[0] > $score[1] ? 3 : 0)) == $matches[1]) ? TRUE : FALSE;
						if($isRight)
						{
							echo 'style="color: red;"';
						}
						break;
					case 'RQSPF':
						$letArr = explode('&', $odrdtl['let']);
						foreach ($letArr as $let) 
						{
							$r = ($score[0] + $let == $score[1]) ? 1 : ($score[0] + $let > $score[1] ? 3 : 0);
							$isRight = ($r === (int)$matches[1]) ? TRUE : FALSE;
							if($isRight)
							{
								if(!empty($ticketData))
								{
									$plArrs = $ticketData[$odrdtl['mid']][$key][$let][$matches[1]];
									if(!empty($plArrs))
									{
										foreach ($plArrs as $rpl) 
										{
											array_push($rightOdds, $rpl); 
										}
										$getRight = $getRight + 1;
									}
								}
							}
						}
						if($getRight)
						{
							echo 'style="color: red;"';
						}
						break;
					case 'BQC':
						$isRight = (((($scoreHalf[0]==$scoreHalf[1])?'1':($scoreHalf[0]>$scoreHalf[1]?'3':'0'))."-".(($score[0]==$score[1])?'1':($score[0]>$score[1]?'3':'0'))) == $matches[1]) ? TRUE : FALSE;
						if($isRight)
						{
							echo 'style="color: red;"';
						}
						break;
					case 'CBF':
						if (in_array($odrdtl['score'], array('1:0', '2:0', '2:1', '3:0', '3:1', '3:2', '4:0', '4:1', '4:2', '5:0', '5:1', '5:2', '0:0', '1:1', '2:2', '3:3'
						, '0:1', '0:2', '1:2', '0:3', '1:3', '2:3', '0:4', '1:4', '2:4', '0:5', '1:5', '2:5'))) {
						if ($odrdtl['score'] == $matches[1]) $isRight = TRUE;
						}elseif ($score[0] > $score[1] && $matches[1] === '0:9') {
							$isRight = TRUE;
						}elseif ($score[0] == $score[1] && $matches[1] === '9:9') {
							$isRight = TRUE;
						}elseif ($score[0] < $score[1] && $matches[1] === '9:0') {
							$isRight = TRUE;
						}
						if($isRight)
						{
							echo 'style="color: red;"';
						}
						break;
					case 'JQS':
						$isRight = ($score[0] + $score[1] == $matches[1] || ($score[0] + $score[1] >= 7 && $matches[1] == 7)) ? TRUE : FALSE;
						if($isRight)
						{
							echo 'style="color: red;"';
						}
						break;
					case 'SFC':
						$isRight = ((($score[0] > $score[1]) ? '1' : '0').ceil(abs($score[1]-$score[0])/5) == $matches[1]) ? TRUE : FALSE;
						if($isRight)
						{
							echo 'style="color: red;"';
						}
						break;
					case 'DXF':
						$preScoreArr = explode('&', $odrdtl['preScore']);
						foreach ($preScoreArr as $preScore) 
						{
							$isRight = ((($score[0] + $score[1] > $preScore) ? '3' : '0') == $matches[1]) ? TRUE : FALSE;
							if($isRight)
							{
								if(!empty($ticketData))
								{
									$plArrs = $ticketData[$odrdtl['mid']][$key][$preScore][$matches[1]];
									if(!empty($plArrs))
									{
										foreach ($plArrs as $rpl) 
										{
											array_push($rightOdds, $rpl); 
										}
										$getRight = $getRight + 1;
									}
								}
							}

						}
						if($getRight)
						{
							echo 'style="color: red;"';
						}
						break;
					case 'SF':
						$isRight = (($score[1] > $score[0] ? 3 : 0) == $matches[1]) ? TRUE : FALSE;
						if($isRight)
						{
							echo 'style="color: red;"';
						}
						break;
					case 'RFSF':
						$letArr = explode('&', $odrdtl['let']);
						foreach ($letArr as $let) 
						{
							$isRight = (($score[1] + $let) > $score[0] ? 3 : 0) == $matches[1] ? TRUE : FALSE;
							if($isRight)
							{
								if(!empty($ticketData))
								{
									$plArrs = $ticketData[$odrdtl['mid']][$key][$let][$matches[1]];
									if(!empty($plArrs))
									{
										foreach ($plArrs as $rpl) 
										{
											array_push($rightOdds, $rpl); 
										}
										$getRight = $getRight + 1;
									}
								}
							}
						}
						if($getRight)
						{
							echo 'style="color: red;"';
						}
						break;
				}
			}?>><?php if ($orderInfo['lid'] == JCZQ) {
			    switch ($key) {
			        case 'BQC':
			            $tmp = explode('-', $matches[1]);
			            echo $spfArr["o".$tmp[0]]."-".$spfArr["o".$tmp[1]];
			            break;
			        case 'CBF':
			        case 'JQS':
			            echo str_replace(array('0:9', '9:9', '9:0', '7'), array('负其他', '平其他', '胜其他', '7+'), $matches[1]);
			            break;
			        case 'RQSPF':
			            echo $spfArr["r".$matches[1]];
			            break;
		            case 'SPF':
		            default:
		                echo $spfArr[$matches[1]];
		                break;
			    }
			}else {
			    switch ($key) {
			        case 'DXF':
			            echo $matches[1] == 3 ? '大分' : '小分';
			            break;
		            case 'SFC':
		                echo $odrdtl['home'].(substr($matches[1], 0, 1) ? '负':'胜').$sfcArr[substr($matches[1], -1)];
		                break;
		            case 'RFSF':
		                echo $sfArr["r".$matches[1]];
		                break;
		            case 'SF':
		            default:
		                echo $sfArr[$matches[1]];
		                break;
			    }
			}
			?>
			</span>
			<!-- 实际出票赔率 -->
			<?php 
				$odds = '';
				if(!empty($ticketData))
				{
					$oddArr = $ticketData[$odrdtl['mid']][$key];
					if(!empty($oddArr))
					{
						$pls = array();
						foreach ($oddArr as $pk => $pkArr) 
						{
							foreach ($pkArr as $fa => $plArr) 
							{	
								if(strpos($fa, $matches[1]) > -1)
								{
									foreach ($plArr as $k => $pl) 
									{
										if(!in_array($pl, $pls))
										{
											array_push($pls, $pl);
										}
									}
								}					
							}
						}
						$odds = implode('&', $pls);
					}
				}

				if($orderInfo['status'] >= 500 && !empty($odds))
				{
					if(in_array($key, array('RQSPF', 'DXF', 'RFSF')))
					{
						$tpl = '(';
						$optionOdds = explode('&', $odds);
						foreach ($optionOdds as $k => $odd) 
						{
							if(in_array($odd, $rightOdds))
							{
								$tpl .= '<em class="main-color-s">' . $odd . '</em>';
								if($k < count($optionOdds) - 1)
								{
									$tpl .= '&';
								}
							}
							else
							{
								$tpl .= $odd;
								if($k < count($optionOdds) - 1)
								{
									$tpl .= '&';
								}
							}
						}
						$tpl .= ')';
						echo $tpl;
					}
					else
					{
						if($isRight)
						{
							echo '<em class="main-color-s">(' . $odds . ')</em>';
						}
						else
						{
							echo '(' . $odds . ')';
						}
					}
				}
			?>
			</p>
			<?php }
			}?>
			</td>
		</tr>
	<?php $k++;}
	}?>
	</tbody>
	</table>
	<p>选择场次： <?php echo count($orderDetail)?>场</p>
	<p>过关方式： <?php echo str_replace('*', '串', $orderInfo['codes2'])?></p>
	<p>注数倍数： <?php echo $orderInfo['betTnum']?>注  <?php echo ($orderInfo['playType'] != 7) ? $orderInfo['multi'] . '倍' : ''; ?></p>
<?php }?>
</div>
<?php }elseif ($orderInfo['openStatus'] == 1) {?>
              		<div class="promptTxt clearfix"><i class="i-prompt-icon"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/icon-lock.png')?>" alt="" title=""></i><span class="sTit">仅跟单者可见</span><span class="sDes">参与认购方案可查看详情</span></div>
				<?php }else {?>
					<div class="promptTxt clearfix"><i class="i-prompt-icon"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/icon-time-sand.png')?>" alt="" title=""></i><span class="sTit">截止后可见</span><span class="sDes">官方销售截止后可查看详情</span></div>
				<?php }?>

<!-- 方案内容 -->
<?php if (in_array($orderInfo['lid'], array(SSQ, DLT, FCSD, PLS, PLW, QLC, QXC, SFC, RJ))) {?>
<script>
	$('.form-info').find('.total-prize-money').wrap('<div class="total-prize-money-szc"></div>');
</script>
<?php }?>
