<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/bet-history.min.css');?>">
</head>
<body>
	<div class="wrapper bet-detail bet-detail-lsj">
        <div class="bet-detail-hd">
            <div class="lsj-info">
                <p>乐善奖在当期大乐透派奖时一并派奖<a href="<?php echo $this->config->item('pages_url'); ?>ios/info/detail/131001">查看规则&gt;</a></p>
                <p class="strong">乐善奖奖金总额<?php echo $bonusStatus; ?></p>
            </div>
        </div>
        <div class="bet-detail-bd">
        	<?php if(!empty($ticketDetail)): ?>
        	<?php foreach ($ticketDetail as $tickets): ?>
            <div class="lsj-item">
                <ul>
                    <li>
                        <div class="subtitle">订单方案</div>
                        <div class="con">
                            <ul>
                            	<?php foreach ($tickets['code'] as $code): ?>
                                <li>
                                    <?php echo $code; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <div class="subtitle">对应乐善码</div>
                        <div class="con">
                            <div class="ball-group">
                                <?php echo $tickets['awardNum']; ?>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="subtitle">乐善奖奖金</div>
                        <div class="con">
                            <?php echo $tickets['bonusStatus']; ?>
                        </div>
                    </li>
                </ul>
            </div>
            <?php endforeach; ?>
        	<?php endif; ?>
        </div>
    </div>
</body>
</html>