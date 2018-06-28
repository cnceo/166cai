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
	elseif($lotteryId == Lottery_Model::SYYDJ || $lotteryId == Lottery_Model::JXSYXW || $lotteryId == Lottery_Model::HBSYXW || $lotteryId == Lottery_Model::GDSYXW)
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
	<script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>
        // 基础配置
        require.config({
            baseUrl: '//<?php echo DOMAIN;?>/caipiaoimg/static/js',
            paths: {
                "zepto" : "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/zepto.min",
                "frozen": "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/frozen.min",
                'basic':'//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/basic'
            }
        })
        require(['basic', 'ui/loading/src/loading', 'ui/tips/src/tips'], function(basic, loading, tips){
        });
    </script>
</body>
</html>