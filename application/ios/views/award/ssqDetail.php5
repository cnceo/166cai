<div class="wrapper lottery-detail lottery-detail-ssq">
<div class="cp-box">
    <div class="cp-box-hd">
	<h2 class="cp-box-title">第<?php echo $awards['base']['seExpect'];?>期 <?php echo date('Y-m-d H:i', $awards['base']['awardTime'] / 1000); ?>(<?php echo getWeekByTime($awards['base']['awardTime'] / 1000);?>)</h2>
    </div>
    <div class="cp-box-bd">
	<div class="ball-group">
	<?php if($awards['base']['awardNumber']):?>
		<?php
			$balls = explode(':', $awards['base']['awardNumber']);
			$redBalls = explode(',', $balls[0]);
		?>
		<?php foreach ($redBalls as $val): ?>
			<span><?php echo $val;?></span>
		<?php endforeach; ?>
		<span class="blue-ball"><?php echo $balls[1];?></span>
	<?php endif;?>
	</div>
    </div>
</div>
<?php if($awards['detail']['awardLevelList']):?>
<div class="lottery-money">
    <p>全国销量(元)<span class="special-color"><?php echo m_format($awards['detail']['awardMoney'] * 100, 0);?></span></p>
    <p>奖池滚存(元)<span class="special-color"><?php echo m_format($awards['detail']['awardPool'] * 100, 0);?></span></p>
</div> 
<table class="table-bet table-bet-rule">
    <colgroup>
	<col width="33%">
	<col width="33%">
	<col width="33%">
    </colgroup>
    <thead>
	<tr>
	    <th>奖项</th>
	    <th>中奖注数</th>
	    <th>单注奖金(元)</th>
	</tr>
    </thead>
    <tbody> 
    <?php foreach ($awards['detail']['awardLevelList'] as $val):?>
	<tr>
	    <td><?php echo $val['awardName'];?></td>
	    <td><?php echo $val['prizeNumber'];?></td>
	    <td><?php echo m_format($val['prize'] * 100, 0);?></td>
	</tr>
	<?php endforeach;?>	
    </tbody>
</table>
<?php else:?>
<div class="lottery-money">
    <p>全国销量(元)<span class="special-color">统计中</span></p>
    <p>奖池滚存(元)<span class="special-color">统计中</span></p>
</div>
<table class="table-bet table-bet-rule">
    <colgroup>
	<col width="33%">
	<col width="33%">
	<col width="33%">
    </colgroup>
    <thead>
	<tr>
	    <th>奖项</th>
	    <th>中奖注数</th>
	    <th>单注奖金(元)</th>
	</tr>
    </thead>
    <tbody>
    	<tr>
	    <td>一等奖</td>
	    <td>统计中</td>
	    <td>统计中</td>
	</tr>
		<tr>
	    <td>二等奖</td>
	    <td>统计中</td>
	    <td>统计中</td>
	</tr>
		<tr>
	    <td>三等奖</td>
	    <td>统计中</td>
	    <td>3000</td>
	</tr>
		<tr>
	    <td>四等奖</td>
	    <td>统计中</td>
	    <td>200</td>
	</tr>
		<tr>
	    <td>五等奖</td>
	    <td>统计中</td>
	    <td>10</td>
	</tr>
		<tr>
	    <td>六等奖</td>
	    <td>统计中</td>
	    <td>5</td>
	</tr>
    </tbody>
</table>
<?php endif;?>   
</div>