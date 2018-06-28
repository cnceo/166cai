<div class="wrapper lottery-detail lottery-detail-ssq">
<div class="cp-box">
    <div class="cp-box-hd">
	<h2 class="cp-box-title">第<?php echo $awards['base']['seExpect'];?>期 <?php echo date('Y-m-d H:i', $awards['base']['awardTime'] / 1000); ?></h2>
    </div>
    <div class="cp-box-bd">
	<div class="ball-group">
	<?php if($awards['base']['awardNumber']):?>
		<?php
			$balls = explode(',', $awards['base']['awardNumber']);
		?>
		<?php foreach ($balls as $val): ?>
			<span><?php echo $val;?></span>
		<?php endforeach; ?>
	<?php endif;?>
	</div>
    </div>
</div>
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
    <?php if($awards['detail']['awardLevelList']):?>
    <?php foreach ($awards['detail']['awardLevelList'] as $val):?>
	<tr>
	    <td><?php echo $val['awardName'];?></td>
	    <td><?php echo $val['prizeNumber'];?></td>
	    <td><?php echo m_format($val['prize'] * 100, 0);?></td>
	</tr>
	<?php endforeach;?>
	<?php endif;?>
    </tbody>
</table>      
</div>