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
	        <?php if($status != '3'): ?>
	        <div class="fn-step">
	            <ol>
	                <li <?php if(in_array($status, array(2, 4))):?>class="current"<?php endif; ?>><?php if($status == '4'):?>提现失败<time><?php echo date('m-d H:i', strtotime($fail_time));?></time><?php else: ?>提现到账<?php if($status == '2'):?><time><?php echo date('m-d H:i', strtotime($succ_time));?></time><?php endif; ?><?php endif; ?></li>
	                <li <?php if(in_array($status, array(2, 4, 5))):?>class="current"<?php endif; ?>>财务打款<?php if(in_array($status, array(2, 4, 5))):?><time><?php echo date('m-d H:i', strtotime($start_check));?></time><?php endif; ?></li>
	                <li class="current">
	                    申请提现<time><?php echo date('m-d H:i', strtotime($created));?></time>
	                </li>
	            </ol>
	        </div>
	        <?php endif; ?>
	        <table class="table-info">
	            <tbody>
	                <tr>
	                    <th>提现金额</th>
	                    <td><b><?php echo $balance;?>元</b></td>
	                </tr>
	                <tr>
	                    <th>账户余额</th>
	                    <td><?php echo $residue;?>元</td>
	                </tr>
	                <tr>
	                    <th>交易编号</th>
	                    <td><?php echo $tradeNo;?></td>
	                </tr>
	                <?php if($status == '3'): ?>
	                <tr>
	                    <th>订单状态</th>
	                    <td>提款撤销</td>
	                </tr>  
	                <?php elseif($status == '4'): ?>
	                <tr>
	                    <th>失败原因</th>
	                    <td><?php echo $content;?></td>
	                </tr>    
	                <?php endif; ?>
	            </tbody>
	        </table>
        </div>
    </div>
</body>
<?php $this->load->view('mobileview/common/tongji'); ?>
</html>