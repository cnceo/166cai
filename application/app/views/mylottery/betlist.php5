<?php $this->load->view('comm/header'); ?>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/bet-history.min.css');?>">
</head>
<body>
    <div class="wrapper bet-history">
        <div class="ui-tab">
    <ul class="ui-tab-nav ui-border-b">
        <li class="current" data="1">全部订单</li>
        <li data="2">已中奖</li>
        <li data="3">待开奖</li>
    </ul>
    <div class="ui-tab-bd">        
    <ul class="ui-tab-content" style="width: 300%">
        <li>
            <?php if($orders):?>
            <ul class="cp-list ui-list-link ui-border-b" id="type1">
            <?php foreach ($orders as $order):?>
            <?php
            	$winClass = '';
            	$status = parse_order_status($order['status'], $order['my_status']);
            	if($order['margin'] > 0 || $status == '待付款')
            	{
            		$winClass = 'bet-history-winning';
            	}
            	elseif ($status == '部分出票成功')
            	{
            		$status = '等待开奖';
            	}
            ?>
                <li class="cp-list-txt <?php echo $winClass;?>">
                    <a href="<?php echo $this->config->item('base_url') . "order/detail/{$order['orderId']}/".urlencode($strCode);?>">
                        <div>
                            <p><?php echo BetCnName::$BetCnName[$order['lid']]; ?><span><?php if($order['lid'] == BetCnName::PLS || $order['lid'] == BetCnName::FCSD){echo BetCnName::$playCnName[$order['lid']][$order['playType']];} ?></span></p>
                            <p><?php echo number_format(ParseUnit($order['money'], 1), 2);?>元</p>
                        </div>
                        <div>
                            <p><time><?php echo date('m-d H:i', strtotime($order['created']));?></time></p>
                            <p><s><?php if($order['margin'] > 0){ echo "中奖".number_format(ParseUnit($order['margin'], 1), 2)."元";}else{ echo $status;} ?></s></p>
                        </div>
                    </a>
                </li>
              <?php endforeach;?>
            </ul>
            <?php else: ?>
            <div class="wrapper no-data">
              <i class="logo-virtual"></i>
              <p>暂无交易记录</p>
            </div>
            <?php endif;?>
        </li>
        <li>
            <ul class="cp-list ui-list-link ui-border-b" id="type2">
            </ul>
            <div class="wrapper no-data" style="display:none;" id="no-data2">
              <i class="logo-virtual"></i>
              <p>暂无交易记录</p>
            </div>
        </li>
        <li>
            <ul class="cp-list ui-list-link ui-border-b"  id="type3">
            </ul>
            <div class="wrapper no-data" style="display:none;" id="no-data3">
              <i class="logo-virtual"></i>
              <p>暂无交易记录</p>
            </div>
        </li>
    </ul>
    </div>    
</div>
    </div>
    <input type="hidden" name="strCode" value="<?php echo $strCode;?>" />
    <input type="hidden" name="type" value="1" />
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
        require(['lib/zepto.min', 'lib/basic', 'ui/loading/src/loading', 'ui/scroller/src/scroll'], function($, basic, loading, Scroll){

        	var tab = new Scroll('.ui-tab', {
                role: 'tab',
            });
        	//订单列表调用
        	tab.on('scrollEnd', function(id) {
            	id = id + 1;
        		var content = $("#type"+id).find('li').html();
        		if(content == null){
        			var strCode = $("input[name='strCode']").val();
        			$.ajax({
        				type: 'post',
        				url: '/app/mylottery/ajax_betlist',
        				data: {strCode:strCode,cpage:1,type:id},
        				success: function (response) {
                  if(response)
                  {
                    $('#type'+id).append(response);
                  }
                  else
                  {
                    $('#no-data'+id).show();
                  }
        				}
        			});
        		}
        		$("input[name='type']").val(id);
            });
        	//初始化分页
        	var cpage = {
        		1 : 1,
        		2 : 1,
        		3 : 1,
        	};
        	var stop = {
        		1 : true,
        		2 : true,
        		3 : true
        	}
        	var strCode = $("input[name='strCode']").val();

            $(window).scroll(function() {
                if($(this).scrollTop() + $(window).height() + 10 >= $(document).height() && $(this).scrollTop() > 10) {
                	var id = $("input[name='type']").val();
          			 if(stop[id]==true){
          				stop[id]=false;
          				var showLoading = $.loading();  
          				cpage[id]=cpage[id]+1;//当前要加载的页码
          				$.ajax({
          					type: 'post',
          					url: '/app/mylottery/ajax_betlist',
          					data: {strCode:strCode,cpage:cpage[id],type:id},
          					success: function (response) {
              					showLoading.loading("hide");
          						if(response){
          							$('#type'+id).append(response);
          							stop[id]=true;
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