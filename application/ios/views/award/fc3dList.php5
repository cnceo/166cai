<?php foreach ($awards as $key => $value):?>
<?php 
	if($key == 0 && $pageNumber == 1)
	{
		$class = 'ball-group';
	}
	else
	{
		$class = 'num-group';
	}
?>
    <li>
	<a href="javascript:void(0);" onclick="window.location.href='<?php echo $this->config->item('base_url')?>awards/getAwardDetail/<?php echo $lotteryId; ?>/<?php echo $value['seExpect']; ?>';">
    <h2>第<?php echo $value['seExpect']; ?>期 <?php echo date('Y-m-d', $value['awardTime'] / 1000); ?> (<?php echo getWeekByTime($value['awardTime'] / 1000);?>)</h2>
    <div class="<?php echo $class;?>">
	<?php
		$redBalls = explode(',', $value['awardNumber']);
	?>
	<?php foreach ($redBalls as $val): ?>
		<span><?php echo $val;?></span>
	<?php endforeach; ?>
	    </div>
	</a>
    </li>
    <?php endforeach;?>

       