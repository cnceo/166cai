<div class="wrapper lottery-detail lottery-detail-sfc">
<div class="cp-box">
    <div class="cp-box-hd">
	<h2 class="cp-box-title">第<?php echo $awards['base']['seExpect'];?>期 <?php echo date('Y-m-d H:i', $awards['base']['awardTime'] / 1000); ?></h2>
    </div>
    <div class="cp-box-bd">
	<table class="table-bet">
	    <colgroup>
		<col width="7%">
		<col width="7%">
		<col width="7%">
		<col width="7%">
		<col width="7%">
		<col width="7%">
		<col width="7%">
		<col width="7%">
		<col width="7%">
		<col width="7%">
		<col width="7%">
		<col width="7%">
		<col width="7%">
		<col width="7%">
	    </colgroup>
	    <thead>
		<tr>
		    <th>场次</th>
		    <th>1</th>
		    <th>2</th>
		    <th>3</th>
		    <th>4</th>
		    <th>5</th>
		    <th>6</th>
		    <th>7</th>
		    <th>8</th>
		    <th>9</th>
		    <th>10</th>
		    <th>11</th>
		    <th>12</th>
		    <th>13</th>
		    <th>14</th>
		</tr>
	    </thead>
	    <tbody>
	    <?php if($awards['matchs']):?>
		<tr>
		    <th>主队</th>
		    <?php foreach ($awards['matchs'] as $matchinfo): ?>
            	<td><?php echo $matchinfo['teamName1']; ?></td>
            <?php endforeach; ?>
		</tr>
		<tr>
		    <th>客队</th>
		    <?php foreach ($awards['matchs'] as $matchinfo): ?>
            	<td><span><?php echo $matchinfo['teamName2']; ?></span></td>
            <?php endforeach; ?>
		</tr>
		<tr>
		    <th>彩果</th>
		    <?php $awardnumber = explode(',', $awards['base']['awardNumber']);?>
		    <?php foreach ($awardnumber as $val): ?>
            	<td><span class="bingo"><?php echo $val; ?></span></td>
            <?php endforeach; ?>
		</tr>
		<?php endif;?>
	    </tbody>
	</table>
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
    </tbody>
</table>
<?php endif;?>
</div>