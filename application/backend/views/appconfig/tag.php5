<!-- APP配置公共头部 -->
<?php 
	$tagArr = array(
		'android'	=>	array(
			'banner' 	=>	array(
				'name'	=>	'轮播图及启动页配置',
				'url'	=>	'/backend/Appconfig/banner/',
			), 
			'activity'	=>	array(
				'name'	=>	'首页活动配置',
				'url'	=>	'/backend/Appconfig/activity/',
			),
			'webBanner'	=>	array(
				'name'	=>	'支付成功页广告',
				'url'	=>	'/backend/Appconfig/webBanner/',
			),
			'version'	=>	array(
				'name'	=>	'版本基础配置',
				'url'	=>	'/backend/Appconfig/version/',
			),
			'event'		=>	array(
				'name'	=>	'活动中心配置',
				'url'	=>	'/backend/Appconfig/event/',
			),
			'gift'		=>	array(
				'name'	=>	'礼包提醒页配置',
				'url'	=>	'/backend/Appconfig/gift/',
			),
			'betBanner'	=>	array(
				'name'	=>	'投注页素材配置',
				'url'	=>	'/backend/Appconfig/betBanner/',
			),
		),
		'ios'	=>	array(
			'banner' 	=>	array(
				'name'	=>	'轮播图及启动页配置',
				'url'	=>	'/backend/Appconfig/banner/',
			), 
			'activity'	=>	array(
				'name'	=>	'首页活动配置',
				'url'	=>	'/backend/Appconfig/activity/',
			),
			'webBanner'	=>	array(
				'name'	=>	'支付成功页广告',
				'url'	=>	'/backend/Appconfig/webBanner/',
			),
			'version'	=>	array(
				'name'	=>	'版本基础配置',
				'url'	=>	'/backend/Appconfig/version/',
			),
			'event'		=>	array(
				'name'	=>	'活动中心配置',
				'url'	=>	'/backend/Appconfig/event/',
			),
			'gift'		=>	array(
				'name'	=>	'礼包提醒页配置',
				'url'	=>	'/backend/Appconfig/gift/',
			),
			'betBanner'	=>	array(
				'name'	=>	'投注页素材配置',
				'url'	=>	'/backend/Appconfig/betBanner/',
			),
		),
		'm'	=>	array(
		    'banner' 	=>	array(
		        'name'	=>	'轮播图配置',
		        'url'	=>	'/backend/Appconfig/banner/',
		    ),
			'mindex' 	=>	array(
				'name'	=>	'首页彩种位配置',
				'url'	=>	'/backend/Appconfig/mindex/',
			), 
			'pop'		=>	array(
				'name'	=>	'首页弹层配置',
				'url'	=>	'/backend/Appconfig/pop/',
			),
			'gift'		=>	array(
				'name'	=>	'礼包提醒页配置',
				'url'	=>	'/backend/Appconfig/gift/',
			),
		),
	);
?>

<?php if(!empty($tagArr[$platform])): ?>
	<?php foreach ($tagArr[$platform] as $key => $items):?>
	<li <?php if($key == $tag): ?>class="current"<?php endif; ?> ><a href="<?php echo $items['url'] . $platform; ?>"><?php echo $items['name']; ?></a></li>
	<?php endforeach;?>
<?php endif; ?>
    	
