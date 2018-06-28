<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>投注站详情</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
</head>
<body>
    <div class="wrapper o2o-station">
        <ul>
        	<?php if ( ! empty($stationInfo)): ?>
        		<?php foreach ($stationInfo as $detail): ?>
        		<li>
	                <p><?php echo $detail['cname']; ?>
	                <p>地&nbsp;&nbsp;址：<?php echo $detail['address']; ?></p>
	            </li>
        		<?php endforeach; ?>
        	<?php endif;?>
        </ul>
    </div>
</body>
<?php $this->load->view('mobileview/common/tongji'); ?>
</html>