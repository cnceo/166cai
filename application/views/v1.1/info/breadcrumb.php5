<?php
$categoryName = strpos($categoryName, '-') === FALSE
    ? $categoryName
    : substr($categoryName, strpos($categoryName, '-') + 1);
switch ($this->act) {
	case 'index':?>
	<p>您的位置：<a href="/">166彩票</a> > 资讯中心</p>
<?php break;
	case 'lists':?>
	<p>您的位置：<a href="/">166彩票</a> > <a href="/info">资讯中心</a> > <?php echo empty($categoryName) ? $cname : $categoryName ?></p>
<?php break;
default:
if ($result['category_id'] >= 9) {?>
	<p>您的位置：<a href="/">166彩票</a> > <a href="/info">资讯中心</a> > <?php echo empty($categoryName) ? $cname : $categoryName ?></p>
<?php } else {?>
	<p>您的位置：<a href="/">166彩票</a> > <a href="/info">资讯中心</a> > <a href="/info/lists/<?php echo $result['category_id']?>?cpage=1"><?php echo empty($categoryName) ? $cname : $categoryName ?></a></p>
<?php }
break;
}?>
    
