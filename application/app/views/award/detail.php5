<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/lottery-notice.min.css');?>">
</head>
<body>
<?php
	if($lotteryId == Lottery_Model::SSQ)
	{
		$this->load->view('award/ssqDetail', array('awards' => $awards));
	}
	elseif($lotteryId == Lottery_Model::SFC)
	{
		$this->load->view('award/sfcDetail', array('awards' => $awards));
	}
	elseif($lotteryId == Lottery_Model::RJ)
	{
		$this->load->view('award/rjDetail', array('awards' => $awards));
	}
	elseif($lotteryId == Lottery_Model::DLT)
	{
		$this->load->view('award/dltDetail', array('awards' => $awards));
	}
	elseif($lotteryId == Lottery_Model::FCSD)
	{
		$this->load->view('award/fc3dDetail', array('awards' => $awards));
	}
	elseif($lotteryId == Lottery_Model::PLS)
	{
		$this->load->view('award/plsDetail', array('awards' => $awards));
	}
	elseif($lotteryId == Lottery_Model::PLW)
	{
		$this->load->view('award/plwDetail', array('awards' => $awards));
	}
	elseif($lotteryId == Lottery_Model::SYYDJ)
	{
		$this->load->view('award/syxwDetail', array('awards' => $awards));
	}
	elseif($lotteryId == Lottery_Model::QXC)
	{
		$this->load->view('award/qxcDetail', array('awards' => $awards));
	}
	elseif($lotteryId == Lottery_Model::QLC)
	{
		$this->load->view('award/qlcDetail', array('awards' => $awards));
	}
?>
<?php if($channel): ?>
	<div class="fixed-bar">
        <div class="btn-group">
            <a href="<?php echo $this->config->item('pages_url'); ?>app/download?c=<?php echo $channel; ?>" target="_blank" class="btn btn-block-special">下载APP送188元红包</a>
        </div>
    </div>
<?php endif; ?>
</body>
</html>