<!doctype html> 
<html> 
<head> 
<meta charset="utf-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/> 
<meta>
<title>邀请好友赢千元彩金-166彩票官网</title>
<meta content='166彩票官网最新推出“邀请好友赢千元彩金”专题活动，每成功邀请1位好友，获得1次抽奖机会，好友还能免费领取188元红包。166彩票网安全服务！' name="Keywords">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/active/join-us.min.css');?>">
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>"></script>
</head>
<body>
	<?php if (empty($this->uid)): ?>
        <div class="top_bar">
        	<?php $this->load->view('v1.1/elements/common/header_topbar_notlogin'); ?>
        </div>
    <?php else: ?>
        <div class="top_bar">
            <?php $this->load->view('v1.1/elements/common/header_topbar'); ?>
        </div>
    <?php endif; ?>
    </div>
	<div class="join-wrap">
		<div class="join-hd"><h1>邀请好友赢千元彩金</h1><p>每成功邀请1位好友，获得一次抽奖机会</p><p>好友还能免费领取188元红包</p>	</div>
		<div class="wrap join-bd">
			<div class="join-lucky">
				<div class="join-lucky-hd">
					<a href="javascript:;" class="lnk-history">我的邀请记录></a><h2>幸运抽奖</h2>
					<?php if ($chj['left_num'] == 0) {?>
						<a href="javascript:;" class="lnk-share">您还没有抽奖机会，立即邀请好友>></a>
					<?php }else {?>
						<a href="javascript:;" class="lnk-share">您有<em><?php echo $chj['left_num']?></em>次抽奖机会，邀请更多好友>></a>
					<?php }?>
				</div>
				<div class="join-m">
					<ul>
						<li class="join-m1" data-rid="8"><em><b>5000</b>元</em><span>充值红包</span></li><li class="join-m2" data-rid="3"><em><b>166</b>元</em><span>彩金红包</span></li>
						<li class="join-m3" data-rid="6"><em><b>18</b>元</em><span>充值红包</span></li><li class="join-m4" data-rid="1"><em><b>1</b>元</em><span>彩金红包</span></li>
						<li class="join-m5" data-rid="7"><em><b>500</b>元</em><span>充值红包</span></li><li class="join-m6" data-rid="4"><em><b>1000</b>元</em><span>彩金红包</span></li>
						<li class="join-m7" data-rid="5"><em><b>5</b>元</em><span>充值红包</span></li><li class="join-m8" data-rid="2"><em><b>3</b>元</em><span>彩金红包</span></li>
					</ul>
					<a href="javascript:;" class="btn-start join-m-btn"><?php if ($chj['left_num'] == 0) {?>立即邀请<?php }else {?>抽奖<?php } ?></a>
				</div>
			</div>

			<div class="join-roster">
				<h2>幸运达人</h2>
				<div class="join-roster-bd">
					<div class="join-table">
						<div class="join-table-th"><span>用户名</span><em>获得红包</em></div>
						<div class="join-table-td">
							<ul>
							<?php foreach ($list as $lt) {?>
								<li><span><?php echo ((strlen($lt['uname']) > 5) ? mb_substr($lt['uname'], 0, 7, 'utf8')."***" : $lt['uname'])?></span><em><b><?php echo $lt['money']/100?></b>元</em></li>
							<?php }?>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div class="join-rule">
				<h2>活动规则</h2>
				<ol>
					<li><span>1</span>本活动所有实名认证用户均可参加，活动时间<?php echo date('m.d', strtotime($info['start_time']))?>-<?php echo date('m.d', strtotime($info['end_time']))?>。</li>
					<li><span>2</span>每成功邀请1位好友，您可以获得1次抽奖机会，100%中奖，最高5000元彩金。好友也能免费领取188元红包。</li>
					<li><span>3</span>被邀请好友必须通过你分享的活动，输入手机号并验证，注册（注册时使用的手机号码，必须为领取红包时输入的手机号码）并且实名认证才视为成功。</li>
					<li><span>4</span>被邀请好友同一身份证限领一次188元红包，记一次成功邀请。</li>
					<li><span>5</span>被邀请好友超过活动截止日期仍未实名认证的，邀请人将无法获得抽奖机会。</li>
					<li>
						<span>6</span>好友领取的188元红包价值如下：
						<ul><li>a. 3元注册红包（实名认证后可用）</li><li>b. 2元红包（充值20元及以上可用），5个</li><li>c. 5元红包（充值50元及以上可用），5个</li><li>d. 10元红包（充值100元及以上可用），5个</li><li>e. 20元红包（充值200元及以上可用），5个</li></ul>
					</li>
					<li><span>7</span>充值红包有效期为30天，逾期未使用的红包将被系统收回。</li>
					<li><span>8</span>充值时手动勾选充值红包即可使用。</li>
					<li><span>9</span>活动充值与赠送的红包不能提现，只能用于购彩，中奖奖金可提现。</li>
					<li><span>10</span>活动严禁作弊，一经发现，166彩票网有权不予赠送、冻结账户以及要求用户返还不正当得利。在法律允许范围内，166彩票网保留最终解释权。</li>
					<li><span>11</span>关于活动的任何问题，请联系在线客服或拨打电话400-690-6760。</li>
				</ol>
			</div>
		</div>
	</div>
	<div class="pop-join-mask hidden"></div>
	<div class="pop-join pop-join-history" style="display: none;">
		<div class="pop-join-hd"><h3>我的邀请记录</h3><a href="javascript:;" class="pop-join-close">&times;</a></div>
		<div class="pop-join-bd">
			<table>
				<thead><tr><th class="tal" width="168"><span>邀请用户</span></th><th width="162">成功时间</th></tr></thead>
				<tbody><tr><td colspan="2" class="no-data">暂无邀请记录</td></tr></tbody>
			</table>
			<div class="pop-join-pag"></div>
		</div>
		<div class="pop-join-ft"><a href="javascript:;" class="btn-s btn-specail ljyq">立即邀请</a></div>
	</div>
<input type="text" id="url" style="opacity:0.0" value='<?php echo $this->config->item('pages_url')?>/activity/join/<?php echo $url ?>'>
	<?php $this->load->view('v1.1/elements/common/footer_academy');?>
	<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery.zclip.js');?>"></script>
	<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/simple-share.min.js'); ?>?>"></script>
	<script>
	var users = $.parseJSON('<?php echo json_encode($users)?>'), leftnum = <?php echo $chj['left_num'] ? $chj['left_num'] : 0?>, showbind = <?php echo $showBind?>, loginrfsh = 1, rfshbind = 1;
	var share = new SimpleShare({
        url: '<?php echo $this->config->item('pages_url')?>/activity/join/<?php echo $url ?>',
        title: "【邀请好友赢千元彩金！】166彩票送壕礼，邀请好友赢取千元彩金，好友还能免费领取188元红包，快来参加吧！",
        content: "【邀请好友赢千元彩金！】166彩票送壕礼，邀请好友赢取千元彩金，好友还能免费领取188元红包，快来参加吧！",
        pic: '<?php echo $this->config->item('pages_url')?>/caipiaoimg/v1.1/img/logo/logo-square.png'    
    });

	function renderPage(p) {
		p = parseInt(p, 10);
		var pstr = '<div class="pop-join-hd"><h3>我的邀请记录</h3><a href="javascript:;" class="pop-join-close">&times;</a></div><div class="pop-join-bd"><table><thead><tr><th class="tal" width="168"><span>邀请用户</span></th><th width="162">成功时间</th></tr></thead><tbody>', tp = <?php echo ceil(count($users)/4)?>;
		for (var i = (p-1)*4; i<p*4; i++) {
			if (users[i]) {
				pstr += '<tr><td class="tal"><span>'+users[i].uname.substring(0, 3)+(users[i].uname.length > 3 ? '***' : '')+'</span></td><td>'+users[i].modified+'</td></tr>';
			}
		}
		pstr += '</tbody></table><div class="pop-join-pag">';
		if (p == 1) {
			pstr += '<span>上一页</span>';
		}else {
			pstr += '<a href="javascript:;" onClick="goPg('+(p-1)+')">上一页</a>';
		}
		for (i = 1; i <= tp; i++) {
			pstr += '<a onClick="goPg('+(i)+')" href="javascript:;"';
			if (i == p) {
				pstr += ' class="current"';
			}
			pstr += '>'+i+'</a>';
		}
		if (p == tp) {
			pstr += '<span>下一页</span>';
		}else {
			pstr += '<a href="javascript:;" onClick="goPg('+(p+1)+')">下一页</a>';
		}
		return pstr;
	}

	var goPg = function(pg) {
		$(".pop-join-history").html(renderPage(pg)+'</div><div class="pop-join-ft"><a href="javascript:;" class="btn-s btn-specail">确定</a></div>');
	}
		// 幸运大人滚动
		;(function($){

			$.fn.myScroll = function(options){
			//默认配置
			var defaults = {
				speed: 40,  //滚动速度,值越大速度越慢
				rowHeight: 24 //每行的高度
			};
			
			var opts = $.extend({}, defaults, options), intId = [];
			function marquee(obj, step){
				obj.find("ul").animate({
					marginTop: '-=1'
				}, 0, function(){
						var s = Math.abs(parseInt($(this).css("margin-top")));
						if(s >= step){
							$(this).find("li").slice(0, 1).appendTo($(this));
							$(this).css("margin-top", 0);
						}
					});
				}

				$(this).each(function(i){
					var sh = opts['rowHeight'], speed = opts['speed'], _this = $(this);
					intId[i] = setInterval(function(){
						if(_this.find('ul').height() <= _this.height()){
							clearInterval(intId[i]);
						}else {
							marquee(_this, sh);
						}
					}, speed);

					_this.hover(function(){
						clearInterval(intId[i]);
					}, function(){
						intId[i] = setInterval(function(){
							if(_this.find('ul').height() <= _this.height()){
								clearInterval(intId[i]);
							}else{
								marquee(_this, sh);
							}
						}, speed);
					});
				
				});
			}
		})(jQuery);


		// 九宫格抽奖
		;(function($){
			$.fn.myDraw = function(options){
				//默认配置
				var defaults = {
					index: 0,	//当前转动到哪个位置，起点位置
					speed: 20,	//初始转动速度
					steps: 0,	//转动次数
					cycle: 4,	//转动基本次数：即至少需要转动多少次再进入抽奖环节
					prize: -1,	//中奖位置
					onrun: true,
					callback: function(){}
				};
				 
				var opts = $.extend({}, defaults, options);
				var that = this;
				that.each(function(){
					var _this = $(this);

					that.go = function(num){
						if(opts.onrun){
							opts.onrun = false;
							marquee(num)
						}
					}
					function marquee(num){
						var count = _this.find('li').length;
						var className = '.' + _this.attr('class');
						var steps = opts.cycle * count + num;
						var timer;
						
						function roll(){
							// 步数累加
							opts.steps += 1;

							_this.find(className + (opts.index)).removeClass('current');
							if (opts.index > count - 1) {
								opts.index = 0;
							}
							_this.find(className + (opts.index + 1)).addClass('current');
							opts.index++;

							if (opts.steps > steps && opts.prize == opts.index){
								clearTimeout(timer);
								opts.prize = -1;     
								opts.steps = 0;
								if(opts.callback){
									setTimeout(opts.callback, 400)
									setTimeout(function(){
										opts.onrun = true;
									}, 400)
						        }
							}else{
								if (opts.steps < steps) {
									opts.speed -= 10
								}else if(opts.steps == steps) {
									opts.prize = num;	
								}else{
									if (opts.steps > opts.cycle + 10 && ((opts.prize == 0 && opts.index == count) || opts.prize == opts.index)){
										opts.speed += 110
									}else {
										opts.speed += 20
									}
								}
								if (opts.speed < 40){
									opts.speed = 40
								}
								timer = setTimeout(roll, opts.speed);
							}
							return false;
						}
						timer = setTimeout(roll, opts.speed);
					}
					
				})
				return that;
			}
		})(jQuery);

		
		$(function(){
			// 九宫格抽奖
			var joinM = $('.join-m'), cjLook = false;;
			var joinUs = joinM.myDraw({
				speed: 180,
				callback: function(){
					var that = $('.join-m .current');
					$('.pop-join').remove();
					$('.pop-join-mask').removeClass('hidden');
					if (leftnum > 0) {
						$('.lnk-share').html("您有<em>"+leftnum+"</em>次抽奖机会，赶紧抽奖吧");
					}else {
						$('.join-m-btn').html('立即邀请');
						$('.lnk-share').html("您还没有抽奖机会，快去邀请好友吧～")
					}
					console.log(that.data('rid'));
					if ($.inArray(that.data('rid'), [1, 2, 3, 4]) > -1) {
						$('body').append('<div class="pop-join pop-join3"><div class="pop-join-hd"><a href="javascript:;" class="pop-join-close">&times;</a></div><div class="pop-join-bd"><div class="pop-join-ticket"><strong style="line-height: 64px;"><b style="position: relative;top: 4px;font-weight: bold">' + that.find('b').html() + '</b>元</strong><p><b>恭喜您获得' + that.find('span').html() + '</b>亿万大奖等你赢</p></div></div><div class="pop-join-ft"><a href="/mylottery/detail" target="_blank" class="btn-s btn-specail bet-join-b">查看账户明细</a></div></div>');
					}else {
						$('body').append('<div class="pop-join pop-join3"><div class="pop-join-hd"><a href="javascript:;" class="pop-join-close">&times;</a></div><div class="pop-join-bd"><div class="pop-join-ticket"><strong style="line-height: 64px;"><b style="position: relative;top: 4px;font-weight: bold">' + that.find('b').html() + '</b>元</strong><p><b>恭喜您获得' + that.find('span').html() + '</b>亿万大奖等你赢</p></div></div><div class="pop-join-ft"><a href="/mylottery/redpack" target="_blank" class="btn-s btn-specail bet-join-b">查看红包</a></div></div>');
					}
					cjLook = false;
				}
			});
			joinM.on('click', '.join-m-btn', function(){

				if (!$.cookie('name_ie')) {
	            	cx.PopAjax.login(1);
	                return ;
	            }

	            if (showbind) {
	            	cx.PopAjax.bind();
	                return ;
	            }
				
				if (!cjLook) {
					cjLook = true;
					$.get('/activity/chj', function(data){
						if (data.status) {
							joinUs.go($("ul li[data-rid='"+data.data.rid+"']").index()+1);
							leftnum = data.data.left_num;
						} else if (data.data === '001') {
							cx.Alert({content:data.msg});
							cjLook = false;
						} else {
							$('.join-m-btn').html('立即邀请');
							share();
							cjLook = false;
						}
					}, 'json')
				}
			});		


			// 幸运大人滚动
			$('.join-table-td').myScroll({
				speed: 40,
				rowHeight: 36
			});

			// 关闭弹窗
			$('body').on('click', '.pop-join-close, .btn-specail', function(){
				//$(this).parents('.pop-join').remove();
				$('.pop-join').hide();
				$('.pop-join-mask').addClass('hidden');
			})

			// 打开历史记录
			$('.lnk-history').on('click', function(){

				if (!$.cookie('name_ie')) {
	            	cx.PopAjax.login(1);
	                return ;
	            }

	            if (showbind) {
	            	cx.PopAjax.bind();
	                return ;
	            }
				
				$('.pop-join').remove();
				if (users !== null && users.length > 0) {
					var str = '<div class="pop-join pop-join-history">';
					str += renderPage(1);
					$('body').append(str+'</div><div class="pop-join-ft"><a href="javascript:;" class="btn-s btn-specail">确定</a></div></div>');
				}else {
					$('body').append('<div class="pop-join pop-join-history"><div class="pop-join-hd"><h3>我的邀请记录</h3><a href="javascript:;" class="pop-join-close">×</a></div><div class="pop-join-bd"><table><thead><tr><th class="tal" width="168"><span>邀请用户</span></th><th width="162">成功时间</th></tr></thead><tbody><tr><td colspan="2" class="no-data">暂无邀请记录</td></tr></tbody></table><div class="pop-join-pag"></div></div><div class="pop-join-ft"><a href="javascript:;" class="btn-s btn-specail ljyq">立即邀请</a></div></div>');
				}
				$('.pop-join-mask').removeClass('hidden');
			})

			
			// 复制分享
			$('body').on('click', '.lnk-share, .ljyq', function(){
				share();
			})

			function share() {
				if (!$.cookie('name_ie')) {
	            	cx.PopAjax.login(1);
	                return ;
	            }

	            if (showbind) {
	            	cx.PopAjax.bind();
	                return ;
	            }
				
				$('.pop-join').remove();
				$('.pop-join-mask').removeClass('hidden');
				$('body').append('<div class="pop-join pop-join2"><div class="pop-join-hd"><h3>邀请好友</h3><a href="javascript:;" class="pop-join-close">&times;</a></div><div class="pop-join-bd"><img src="/caipiaoimg/v1.1/img/active/join-us/img-share.png" width="570" height="191" alt=""><div class="qr-code"><img src="/mainajax/qrCode2?url=<?php echo urlencode($this->config->item('pages_url')."/activity/join/".$url);?>" alt=""></div><div class="pop-join-share">其他分享：<a href="javascript:share.weibo();" class="share-wb" title="分享到新浪微博">分享到新浪微博</a><a href="javascript:share.qzone();" class="share-qzone" title="分享到QQ空间">分享到QQ空间</a><a href="javascript:;" class="share-copy">复制链接</a><span class="copy-tips"></span></div></div></div>');
			}
			$('body').on('click', ".share-copy", function(){
				$("#url").select(); //选择对象 
		        document.execCommand("Copy"); //执行浏览器复制命令
		        $('.copy-tips').html('<i class="icon-right"></i>复制链接成功，快分享给好友吧~');
			})	
		});
	</script>
	
</body>
</html>