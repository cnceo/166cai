<?php if(!$this->is_ajax):?>
<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/lottery-notice.min.css');?>">
</head>
<body>
    <div class="wrapper lottery-notice lottery-list-<?php echo $enName;?>">
    <ul class="cp-list" id="cp-list-arrow">
<?php endif;?>
    <?php
    	if($lotteryId == Lottery_Model::SSQ)
    	{
    		$this->load->view('award/ssqList', array('awards' => $awards, 'lotteryId' => $lotteryId,'pageNumber' => $pageNumber));
    	}
    	elseif($lotteryId == Lottery_Model::SFC)
    	{
    		$this->load->view('award/sfcList', array('awards' => $awards, 'lotteryId' => $lotteryId,'pageNumber' => $pageNumber));
    	}
      elseif($lotteryId == Lottery_Model::RJ)
      {
        $this->load->view('award/rjList', array('awards' => $awards, 'lotteryId' => $lotteryId,'pageNumber' => $pageNumber));
      }
    	elseif($lotteryId == Lottery_Model::DLT)
    	{
    		$this->load->view('award/dltList', array('awards' => $awards, 'lotteryId' => $lotteryId,'pageNumber' => $pageNumber));
    	}
    	elseif($lotteryId == Lottery_Model::FCSD)
    	{
    		$this->load->view('award/fc3dList', array('awards' => $awards, 'lotteryId' => $lotteryId,'pageNumber' => $pageNumber));
    	}
    	elseif($lotteryId == Lottery_Model::PLS)
    	{
    		$this->load->view('award/plsList', array('awards' => $awards, 'lotteryId' => $lotteryId,'pageNumber' => $pageNumber));
    	}
    	elseif($lotteryId == Lottery_Model::PLW)
    	{
    		$this->load->view('award/plwList', array('awards' => $awards, 'lotteryId' => $lotteryId,'pageNumber' => $pageNumber));
    	}
    	elseif($lotteryId == Lottery_Model::SYYDJ || $lotteryId == Lottery_Model::JXSYXW || $lotteryId == Lottery_Model::HBSYXW || $lotteryId == Lottery_Model::GDSYXW)
    	{
    		$this->load->view('award/syxwList', array('awards' => $awards, 'lotteryId' => $lotteryId,'pageNumber' => $pageNumber));
    	}
      elseif($lotteryId == Lottery_Model::QXC)
      {
        $this->load->view('award/qxcList', array('awards' => $awards, 'lotteryId' => $lotteryId,'pageNumber' => $pageNumber));
      }
      elseif($lotteryId == Lottery_Model::QLC)
      {
        $this->load->view('award/qlcList', array('awards' => $awards, 'lotteryId' => $lotteryId,'pageNumber' => $pageNumber));
      }
      elseif($lotteryId == Lottery_Model::CQSSC)
      {
        $this->load->view('award/cqsscList', array('awards' => $awards, 'lotteryId' => $lotteryId,'pageNumber' => $pageNumber));
      }
    ?>
<?php if(!$this->is_ajax):?>
    </ul>
    </div>
    <?php if($channel): ?>
    <div class="fixed-bar">
        <div class="btn-group">
            <a href="<?php echo $this->config->item('pages_url'); ?>app/download?c=<?php echo $channel; ?>" target="_blank" class="btn btn-block-special">下载APP送188元红包</a>
        </div>
    </div>
    <?php endif; ?>
<script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
 <script>
//基础配置
 require.config({
     baseUrl: '//<?php echo DOMAIN;?>/caipiaoimg/static/js',
     paths: {
         "zepto" : "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/zepto.min",
         "frozen": "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/frozen.min",
         'basic':'//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/basic'
     }
 })
        require(['lib/zepto.min', 'lib/basic', 'ui/loading/src/loading'], function($, basic, loading){

        	//初始化分页
        	var cpage = 1;
        	var lotteryId = <?php echo $lotteryId;?>;
        	var stop = true;
        	var strCode = $("input[name='strCode']").val();
            $(window).scroll(function() {
                if($(this).scrollTop() + $(window).height() + 10 >= $(document).height() && $(this).scrollTop() > 10) {
                	var id = $("input[name='type']").val();
          			 if(stop == true){
          				stop = false;  
          				cpage =cpage+1;//当前要加载的页码
          				var showLoading = $.loading(); 
          				$.ajax({
          					type: 'get',
          					url: '<?php echo $this->config->item('pages_url')?>app/awards/number/'+lotteryId+'/'+cpage+'/',
          					success: function (response) {
          						showLoading.loading("hide");
          						if(response){
          							$('#cp-list-arrow').append(response);
          							stop =true;
          						}
          						
          					},
                            error: function () {
                            	showLoading.loading("hide");
                            }
          				});
          			 }
                }
            });
        });
</script>
</body>
</html>
<?php endif;?>