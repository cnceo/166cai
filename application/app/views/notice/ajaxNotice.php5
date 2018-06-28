<?php if(!empty($result)):?>
    <?php foreach( $result as $items ): ?>
    <li>
        <a href="javascript:void(0);" onClick="window.location.href='<?php echo $items['url'];?>';">
            <h2><?php echo $items['title'];?></h2>
            <time><?php echo date('m-d', $items['addTime']);?></time> 
        </a>
    </li>
    <?php endforeach; ?>
<?php else: ?>
	<li class="wrapper no-data">
        <i class="logo-virtual"></i>
        <p>暂无公告信息</p>
    </li>
<?php endif;?>

        