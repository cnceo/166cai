<?php if($cityList):?>
<?php foreach($cityList as $row): ?>
<a href="javascript:;"  target="_self" data-value='<?php echo $row['city']?>'><?php echo $row['city']?></a>
<?php endforeach; ?>
<?php endif;?>