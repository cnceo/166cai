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
	<div class="list-txt">
    <h2>第<?php echo $value['seExpect']; ?>期 <?php echo date('Y-m-d H:i', $value['awardTime'] / 1000); ?></h2>
    <div class="<?php echo $class;?>">
	<?php
		$redBalls = explode(',', $value['awardNumber']);
	?>
	<?php foreach ($redBalls as $val): ?>
		<span><?php echo $val;?></span>
	<?php endforeach; ?>
	    </div>
	</div>
    </li>
    <?php endforeach;?>