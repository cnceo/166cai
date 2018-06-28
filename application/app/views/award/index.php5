<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/lottery-notice.min.css');?>">
</head>
<body>
    <div class="wrapper lottery-notice">
        <ul class="cp-list">
            <li>
                <a href="<?php echo $this->config->item('base_url').'awards/getAwardDetail/'.Lottery_Model::SSQ.'/'.$awards[Lottery_Model::SSQ]['seExpect'].'/'.$channel;?>">
                    <h2><em>双色球</em>第<?php echo $awards[Lottery_Model::SSQ]['seExpect']; ?>期 <?php echo date('Y-m-d', $awards[Lottery_Model::SSQ]['awardTime'] / 1000); ?> (<?php echo getWeekByTime($awards[Lottery_Model::SSQ]['awardTime'] / 1000);?>)</h2>
                    <div class="ball-group">
                    <?php
						$balls = explode(':', $awards[Lottery_Model::SSQ]['awardNumber']);
						$redBalls = explode(',', $balls[0]);
						$blueBalls = explode(',', $balls[1]);
					?>
                    <?php foreach ($redBalls as $val): ?>
                        <span><?php echo $val;?></span>
                    <?php endforeach; ?>
                    <?php foreach ($blueBalls as $val): ?>
                        <span class="blue-ball"><?php echo $val;?></span>
                    <?php endforeach; ?>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->config->item('base_url').'awards/getAwardDetail/'.Lottery_Model::SFC.'/'.$awards[Lottery_Model::SFC]['seExpect'].'/'.$channel;?>">
                    <h2><em>胜负彩</em>第<?php echo $awards[Lottery_Model::SFC]['seExpect']; ?>期 <?php echo date('Y-m-d', $awards[Lottery_Model::SFC]['awardTime'] / 1000); ?></h2>
                    <div class="score-group">
                    <?php
						$redBalls = explode(',', $awards[Lottery_Model::SFC]['awardNumber']);
					?>
                        <?php foreach ($redBalls as $val): ?>
                        	<span><?php echo $val;?></span>
                    	<?php endforeach; ?>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->config->item('base_url').'awards/jczq/0/'.$channel;?>">
                    <h2><em>竞彩足球</em>不定期</h2>
                    <div class="jczq">
                        <span>主队VS客队</span><span>比赛结果</span>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->config->item('base_url').'awards/jclq/0/'.$channel;?>">
                    <h2><em>竞彩篮球</em>不定期</h2>
                    <div class="jclq">
                        <span>客队VS主队</span><span>比赛结果</span>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->config->item('base_url').'awards/getAwardDetail/'.Lottery_Model::DLT.'/'.$awards[Lottery_Model::DLT]['seExpect'].'/'.$channel;?>">
                    <h2><em>大乐透</em>第<?php echo $awards[Lottery_Model::DLT]['seExpect']; ?>期 <?php echo date('Y-m-d', $awards[Lottery_Model::DLT]['awardTime'] / 1000); ?> (<?php echo getWeekByTime($awards[Lottery_Model::DLT]['awardTime'] / 1000);?>)</h2>
                    <div class="ball-group">
                    <?php
						$balls = explode(':', $awards[Lottery_Model::DLT]['awardNumber']);
						$redBalls = explode(',', $balls[0]);
						$blueBalls = explode(',', $balls[1]);
					?>
                    <?php foreach ($redBalls as $val): ?>
                        <span><?php echo $val;?></span>
                    <?php endforeach; ?>
                    <?php foreach ($blueBalls as $val): ?>
                        <span class="blue-ball"><?php echo $val;?></span>
                    <?php endforeach; ?>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->config->item('base_url').'awards/getAwardDetail/'.Lottery_Model::FCSD.'/'.$awards[Lottery_Model::FCSD]['seExpect'].'/'.$channel;?>">
                    <h2><em>福彩3D</em>第<?php echo $awards[Lottery_Model::FCSD]['seExpect']; ?>期 <?php echo date('Y-m-d', $awards[Lottery_Model::FCSD]['awardTime'] / 1000); ?> (<?php echo getWeekByTime($awards[Lottery_Model::FCSD]['awardTime'] / 1000);?>)</h2>
                    <div class="ball-group">
                    <?php
						$redBalls = explode(',', $awards[Lottery_Model::FCSD]['awardNumber']);
					?>
                        <?php foreach ($redBalls as $val): ?>
                        	<span><?php echo $val;?></span>
                    	<?php endforeach; ?>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->config->item('base_url').'awards/getAwardDetail/'.Lottery_Model::PLS.'/'.$awards[Lottery_Model::PLS]['seExpect'].'/'.$channel;?>">
                    <h2><em>排列三</em>第<?php echo $awards[Lottery_Model::PLS]['seExpect']; ?>期 <?php echo date('Y-m-d', $awards[Lottery_Model::PLS]['awardTime'] / 1000); ?> (<?php echo getWeekByTime($awards[Lottery_Model::PLS]['awardTime'] / 1000);?>)</h2>
                    <div class="ball-group">
                    <?php
						$redBalls = explode(',', $awards[Lottery_Model::PLS]['awardNumber']);
					?>
                        <?php foreach ($redBalls as $val): ?>
                        	<span><?php echo $val;?></span>
                    	<?php endforeach; ?>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->config->item('base_url').'awards/getAwardDetail/'.Lottery_Model::PLW.'/'.$awards[Lottery_Model::PLW]['seExpect'].'/'.$channel;?>">
                    <h2><em>排列五</em>第<?php echo $awards[Lottery_Model::PLW]['seExpect']; ?>期 <?php echo date('Y-m-d', $awards[Lottery_Model::PLW]['awardTime'] / 1000); ?> (<?php echo getWeekByTime($awards[Lottery_Model::PLW]['awardTime'] / 1000);?>)</h2>
                    <div class="ball-group">
                    <?php
						$redBalls = explode(',', $awards[Lottery_Model::PLW]['awardNumber']);
					?>
                        <?php foreach ($redBalls as $val): ?>
                        	<span><?php echo $val;?></span>
                    	<?php endforeach; ?>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->config->item('base_url').'awards/number/'.Lottery_Model::SYYDJ.'/1/'.$channel;?>">
                    <h2><em>老11选5</em>第<?php echo $awards[Lottery_Model::SYYDJ]['seExpect']; ?>期 <?php echo date('Y-m-d H:i', $awards[Lottery_Model::SYYDJ]['awardTime'] / 1000); ?></h2>
                    <div class="ball-group">
                    <?php
						$redBalls = explode(',', $awards[Lottery_Model::SYYDJ]['awardNumber']);
					?>
                        <?php foreach ($redBalls as $val): ?>
                        	<span><?php echo $val;?></span>
                    	<?php endforeach; ?>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <?php if($channel): ?>
    <div class="fixed-bar">
        <div class="btn-group">
            <a href="<?php echo $this->config->item('pages_url'); ?>app/download?c=<?php echo $channel; ?>" target="_blank" class="btn btn-block-special">下载APP送188元红包</a>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>