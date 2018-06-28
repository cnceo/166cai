<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>详情</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/account-detail.min.css');?>">
</head>
<body>
    <div class="wrapper account-detail txt-bg">
    	<div class="account-detail-bd">
	        <table class="table-info">
	            <tbody>
	                <tr>
	                    <th>交易金额</th>
	                    <td><b><?php echo $balance;?>元</b></td>
	                </tr>
	                <tr>
	                    <th>账户余额</th>
	                    <td><?php echo $residue;?>元</td>
	                </tr>
	                <tr>
	                    <th>交易时间</th>
	                    <td><time><?php echo $created;?></time></td>
	                </tr>
	                <tr>
	                    <th>交易编号</th>
	                    <td><?php echo $tradeNo;?></td>
	                </tr>
	                <tr>
	                    <th>交易类型</th>
	                    <td><?php echo $tradeType;?></td>
	                </tr>
	                <tr>
	                    <th>交易状态</th>
	                    <td><?php echo $success ? '交易未完成' : '交易成功'?></td>
	                </tr>
	                <?php if(!empty($content)):?>
	                <tr>
	                    <th>交易备注</th>
	                    <td><?php echo $content;?></td>
	                </tr>
	                <?php endif; ?>
	            </tbody>
	        </table>
        </div>
    </div>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>