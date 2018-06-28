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
    <title>积分明细</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/account-detail.min.css');?>">
</head>
<body>
    <div class="wrapper account-detail txt-bg">
        <div class="account-detail-bd">
            <table class="table-info">
                <tbody>
                    <tr>
                        <th>交易积分</th>
                        <td><b><?php echo $log['num']; ?></b></td>
                    </tr>
                    <tr>
                        <th>账户积分</th>
                        <td><?php echo $log['uvalue']; ?></td>
                    </tr>
                    <tr>
                        <th>交易时间</th>
                        <td><time><?php echo $log['created']; ?></time></td>
                    </tr>
                    <tr>
                        <th>交易编号</th>
                        <td><?php echo $log['trade_no']; ?></td>
                    </tr>
                    <tr>
                        <th>交易类型</th>
                        <td><?php echo $log['ctype']; ?></td>
                    </tr>
                    <tr>
                        <th>交易状态</th>
                        <td>交易成功</td>
                    </tr>
                    <tr>
                        <th>备注信息</th>
                        <td><?php echo $log['content']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
<?php $this->load->view('mobileview/common/tongji'); ?>
</html>