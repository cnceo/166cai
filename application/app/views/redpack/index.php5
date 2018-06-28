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
    <title>红包</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
</head>
<body>
    <div class="wrapper red-packets" id="redPackets">
        <div class="ui-tab">
            <ul class="ui-tab-nav">
                <li class="current">可使用(<?php echo $total; ?>)</li>
                <li>已用完/过期</li>
            </ul>
            <div class="ui-tab-bd">
            <ul class="ui-tab-content">
                <li class="current">
                	<?php if(empty($validRedpack)):?>
                    <div class="nothing">暂无红包
                        <?php if($versionInfo['appVersionCode'] >= '40300'){ ?>
                        <a href="/app/points/mall?type=1" class="goJF">兑换红包</a>
                        <?php } ?>
                    </div>
                    <?php else :?>
                    <ul class="red-packets-ul" id="type1">
                    	<?php foreach ($validRedpack as $items):?>
                		<li>
                            <a href="<?php echo $items['a_data']['url']; ?>"<?php if($items['a_data']['class']):?> data-val="<?php echo $items['c_type'];?>"<?php endif;?> class="red-packets-item<?php echo ' ' . $items['a_data']['class'];?> <?php echo ($items['status'] == '1' && $items['valid_start'] > date('Y-m-d H:i:s'))?'isbefore':''; ?>" <?php echo $items['a_data']['onclick'];?>>
                                <div class="red-packets-num">
                                    <span>¥<b><?php echo $items['money']; ?></b><small><?php echo $items['p_name'];?></small></span>
                                </div>
                                <div class="red-packets-note">
                                    <h2><?php echo $items['use_desc']; ?></h2>
                                    <p>有效期：<?php echo date('Y/m/d',strtotime($items['valid_start'])) . '-' . date('Y/m/d',strtotime($items['valid_end'])); ?></p>
                                    <?php if($items['eventType'] == '1'): ?>
                                    <div class="action   <?php echo $items['c_type'] == '1' && $items['p_type'] == '1' ? 'ljlq ' : '';  ?> <?php echo ($items['status'] == '1' && $items['valid_start'] <= date('Y-m-d H:i:s'))?'main-color':''; ?>" data-rid = "<?php echo $items['id']; ?>" data-money="<?php echo $items['money']; ?>"><span><?php echo $items['btn']; ?></span></div>
                                    <?php endif; ?>
                                </div>
                                <?php if(!empty($items['left'])): ?>
                                <p class="term-tip"><span><?php echo $items['left']; ?></span></p>
                                <?php elseif(!empty($items['tips'])): ?>
                                <p class="term-tip"><span><?php echo $items['tips']; ?></span></p>
                                <?php endif;?>
                    		</a>
                		</li>
                		<?php endforeach;?>
                    </ul>
                    <?php endif;?>
                </li>
                <li>
                	<?php if(empty($historyRedpack)):?>
                    <div class="nothing">暂无红包
                    <?php if($versionInfo['appVersionCode'] >= '40300'){ ?>
                    <a href="/app/points/mall?type=1" class="goJF">兑换红包</a>
                    <?php } ?>
                    </div>
                    <?php else :?>
                    <ul class="red-packets-ul red-packets-history" id="type2">
                    	<?php foreach ($historyRedpack as $items):?>
                		<li>
                                    <a href="<?php echo $items['a_data']['url']; ?>" class="red-packets-item">
                                        <div class="red-packets-num">
                                            <span>¥<b><?php echo $items['money']; ?></b><small><?php echo $items['p_name'];?></small></span>
                                        </div>
                                        <div class="red-packets-note">
                                            <h2><?php echo $items['use_desc']; ?></h2>
                                            <p>有效期：<?php echo date('Y/m/d',strtotime($items['valid_start'])) . '-' . date('Y/m/d',strtotime($items['valid_end'])); ?></p>
                                        </div>
                                    <?php if(!empty($items['tips'])): ?>
                                    <p class="term-tip"><span><?php echo $items['tips']; ?></span></p>
                                    <?php endif;?>
                                    </a>
                		</li>
                		<?php endforeach;?>
                      </ul>
                    <?php endif;?>              
                </li>
            </ul>
            </div>    
        </div>
    </div>
    <input type="hidden" name="type" value="1" />
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>
        require(['//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/config.js'], function() {
            require(['Zepto', 'basic', 'ui/tips/src/tipsfix', 'ui/tab/src/tab', 'ui/loading/src/loadingfix', 'ui/scroller/src/scroll'], function($, basic, tips, tab, loading, scroll) {
                var tab = new scroll('.ui-tab', {
                    role: 'tab'
                });
                tab.on('beforeScrollStart', function() {
                    $(window).scrollTop(0);
                });
                tab.on('scrollEnd', function(id) {
                	$("input[name='type']").val(id + 1);
                });

                //初始化分页
            	var cpage = {
            		1 : 1,
            		2 : 1,
            	};
            	var stop = {
            		1 : true,
            		2 : true,
            	}
            	$(window).scroll(function() {
                    if($(this).scrollTop() + $(window).height() + 10 >= $(document).height() && $(this).scrollTop() > 10) {
                    	var id = $("input[name='type']").val();
              			 if(stop[id]==true){
              				stop[id]=false;
              				var showLoading = $.loading();  
              				cpage[id]=cpage[id]+1;//当前要加载的页码
              				$.ajax({
              					type: 'post',
              					url: '/app/redpack/ajaxRedpack',
              					data: {page:cpage[id],eventType:id},
              					success: function (response) {
                  					showLoading.loading("hide");
                  					var response = $.parseJSON(response);
                                    if(response.status == 1)
                                    {
                                    	$('#type'+id).append(response.data);
              							stop[id]=true;
                                    }else{
                                    	stop[id] = false;
                                        // $.tips({
                                        //     content: response.msg,
                                        //     stayTime: 2000
                                        // })
                                    }
              						
              					},
                                error: function () {
                                	showLoading.loading("hide");
                                }
              				});
              			 }
                    }
                });
				//打开弹窗
            	$('body').on('click', '.popOpen', function(){
                        try{
                            // 点击事件
                            android.umengStatistic('useRedpack');
                        }catch(e){
                            // ...
                        }
                	var $_this = $(this);
                        if($(this).hasClass('isbefore')){
                           return false;
                        }
                	var c_type = $_this.attr("data-val");
                	var title = $_this.find('h2').html();
                    $.ajax({
                    	type: 'post',
      					url: '/app/redpack/getLotteryPop',
      					data: {c_type:c_type},
      					success: function (response) {
          					var response = $.parseJSON(response);
                            if(response.status == 1){
                            	$('body').append(response.data);
                            	$('#popTitle').html(title + '红包<small>可投注以下彩种，请选择</small>');
                            	try {
                                	android.setFresh('0')
                                } catch (e) {
									console.log(e)
                                }	
                            }else{
                                $.tips({
                                    content: response.msg,
                                    stayTime: 2000
                                })
                            }
      						
      					},
                        error: function () {
                        	$.tips({
                                content: '操作失败，请联系客服',
                                stayTime: 2000
                            })
                        }
                    });
                });
                //关闭弹窗
            	$('body').on('click', '.popcancel', function(){
                    $('.rp-go').remove();
                    try {
                    	android.setFresh('1')
                    } catch (e) {
						console.log(e)
                    }
                });
                
            	//token提示
            	$('body').on('click', '.tipToken', function(){
                    if($(this).hasClass('isbefore')){
                        return false;
                    }
            		$.tips({
                        content: '请升级至最新版本使用红包',
                        stayTime: 2000
                    });
                });

                //ljlq
                $('body').on('click', '.ljlq', function(){
                        var rid = $(this).data('rid');
                        var money = $(this).data('money');
                        $.ajax({
                            type:'post',
                            data:{rid:rid},
                            url:'/app/redpack/redpackUse',
                            dataType:"json",
                            success: function(data)
                            {
                              if(data.code==300) 
                              {
                                // 登录
                                android.relogin(location.href);
                              }else if(data.code==500){
                                //实名
                                android.auth(location.href);
                              }else if(data.code==400){
                                 var tip = $.tips({
                                    content: data.msg,
                                    stayTime: 2000
                                });
                                //监听消失
                                tip.on("tips:hide", function () {window.location.reload();})
                              }
                              else{
                                var tip = $.tips({
                                    content: '使用成功，<span style="color:#f00;">'+money+'</span>&nbsp;元彩金已派至账户',
                                    stayTime: 2000
                                });
                                //监听消失
                                tip.on("tips:hide", function () {window.location.reload();})
                              }
                            }, 
                        });
                });
            })
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>