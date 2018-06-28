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
    <title>邀请好友赢彩金</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/join-us.min.css');?>">
</head>
<body ontouchstart="">
    <h1 style="display:none;">邀请好友赢千元彩金</h1>
    <img style="display:none;" src="<?php echo getStaticFile('/caipiaoimg/static/images/active/lxShare.jpg');?>">
    <div class="wrapper join-wrap">
        <div class="join-bd ui-tab">
            <ul class="ui-tab-nav">
                <li class="current">邀请好友</li>
                <li>领取奖励</li>
            </ul>
            <div class="ui-tab-bd">
            <div class="ui-tab-content">
                <div class="join-tab-item">
                    <div class="join-hd">
                        <h1>邀请好友赢千元彩金</h1>
                        <p>每成功邀请1位好友，获得一次抽奖机会</p>
                        <p>好友还能免费领取188元红包</p>   
                    </div>
                    <div class="join-invite">
                        <div class="join-invite-email">
                            <strong>赶紧邀请好友赢取彩金吧！</strong>
                            <a href="javascript:;" class="btn-invite">我要邀请</a>
                            <p class="join-active-time">活动时间：<?php echo date('m.d', strtotime($info['start_time']))?>-<?php echo date('m.d', strtotime($info['end_time']))?></p>
                        </div>
                    </div>
                    <div class="join-rule">
                        <h2>活动规则</h2>
                        <ol>
                            <li>1.本活动所有实名认证用户均可参加，活动时间<?php echo date('m.d', strtotime($info['start_time']))?>-<?php echo date('m.d', strtotime($info['end_time']))?>。</li>
							<li>2.每成功邀请1位好友，您可以获得1次抽奖机会，100%中奖，最高5000元彩金。好友也能免费领取188元红包</li>
							<li>3.被邀请好友必须通过你分享的活动，输入手机号并验证，注册（注册时使用的手机号码，必须为领取红包时输入的手机号码）并且实名认证才视为成功。</li>
							<li>4.被邀请好友同一身份证限领一次188元红包，记一次成功邀请。</li>
							<li>5.被邀请好友超过活动截止日期仍未实名认证的，邀请人将无法获得抽奖机会。</li>
							<li>6.好友领取的188元红包价值如下：
								<ul><li>a. 3元注册红包（实名认证后可用）</li><li>b. 2元红包（充值20元及以上可用），5个</li><li>c. 5元红包（充值50元及以上可用），5个</li><li>d. 10元红包（充值100元及以上可用），5个</li><li>e. 20元红包（充值200元及以上可用），5个</li></ul>
							</li>
							<li>7.充值红包有效期为30天，逾期未使用的红包将被系统收回。</li>
							<li>8.充值时手动勾选充值红包即可使用。</li>
							<li>9.活动充值与赠送的红包不能提现，只能用于购彩，中奖奖金可提现。</li>
							<li>10.活动严禁作弊，一经发现，166彩票网有权不予赠送、冻结账户以及要求用户返还不正当得利。在法律允许范围内，166彩票网保留最终解释权。</li>
							<li>11.关于活动的任何问题，请联系在线客服或拨打电话400-690-6760。</li>
                        </ol>
                    </div>
                </div>
                <div class="join-tab-item">
                    <div class="join-lucky">
                        <div class="join-lucky-hd">
                            <p>
                            <?php if ($chj['left_num'] == 0) {?>
								您还没有抽奖机会，快去邀请好友吧～
							<?php }else {?>
								您有<em><?php echo $chj['left_num']?></em>次抽奖机会，赶紧抽奖吧
							<?php }?>
                            </p>
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
                        <h2>我的邀请记录</h2>
                        <div class="join-roster-bd join-table">
                            <div class="join-table-th">
                                邀请用户<span>成功时间</span>
                            </div>
                            <?php if(!empty($users)): ?>
                            <div class="join-table-td">
                                <ul>
                                <?php foreach ($users as $u) {?>
									<li><span><?php echo date('Y-m-d', strtotime($u['modified']))?></span><em><b><?php echo ((mb_strlen($u['uname'], 'utf8') > 3) ? mb_substr($u['uname'], 0, 3, 'utf8')."***" : $u['uname'])?></b></em></li>
								<?php }?>
                                </ul>
                            </div>
                            <?php else: ?>
                            <div style="height:4em; text-align:center; line-height:4em; color:#9060b1;">暂无邀请记录</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            </div>    
        </div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/basic.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/ui/tips/src/tips.js');?>"  type="text/javascript"></script>
    <script>
    var users = $.parseJSON('<?php echo json_encode($users)?>'), leftnum = <?php echo $chj['left_num'] ? $chj['left_num'] : 0?>, showbind = <?php echo $showBind?>;
        // 九宫格抽奖
        ;(function($){
            $.fn.myDraw = function(options){
                //默认配置
                var defaults = {
                    index: 0,   //当前转动到哪个位置，起点位置
                    speed: 20,  //初始转动速度
                    steps: 0,   //转动次数
                    cycle: 4,   //转动基本次数：即至少需要转动多少次再进入抽奖环节
                    prize: -1,  //中奖位置
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
        })(Zepto);


        $(function(){

        	var cjLook = false, selectDate = 0;
            $('.ui-tab-nav').find('li').each(function(){
                var that = $(this);
                if(that.hasClass('current')){
                    that.parents('.ui-tab').find('.join-tab-item').eq(that.index()).show()
                }
                
            })
            $('.ui-tab').on('click', '.ui-tab-nav li', function(){
                $(this).addClass('current').siblings().removeClass('current');
                $(this).parents('.ui-tab').find('.join-tab-item').eq($(this).index()).show().siblings().hide();
                var body = $("html, body");
                body.scrollTop(0);
            })

            // 九宫格抽奖
            var joinM = $('.join-m');
            var joinUs = joinM.myDraw({
            	speed: 180,
                callback: function(){
                    var that = $('.join-m .current');
                    if (leftnum > 0) {
						$('.join-lucky-hd p').html("您有<em>"+leftnum+"</em>次抽奖机会，快去邀请好友吧~");
					}else {
						$('.join-m-btn').html('立即邀请');
						$('.join-lucky-hd p').html("您还没有抽奖机会，快去邀请好友吧～")
					}
                    $('.pop-join').remove();
                    $('.pop-join-mask').removeClass('hidden');
                    $('body').append('<div class="pop-join"><div class="pop-join-inner"><div class="pop-join-hd"><a href="javascript:;" class="pop-join-close">&times;</a></div><div class="pop-join-bd"><div class="pop-join-ticket"><strong><b>' + that.find('b').html() + '</b>元</strong><p><b>恭喜您获得' + that.find('span').html() + '</b>亿万大奖等你赢</p></div></div><div class="pop-join-ft"><a href="javascript:;" class="bet-get">立即领取</a></div></div></div>');
                    cjLook = false;
                }
            });
            joinM.on('click', '.join-m-btn', function(){

            	<?php if (empty($uid)) {?>
                    $.tips({
                        content:'您尚未登录，请先登录',
                        stayTime:2000
                    });
	                return ;
				<?php }?>

	            if (showbind) {
	            	$.tips({
                        content:'您尚未实名，请先实名',
                        stayTime:2000
                    });
	                return ;
	            }
                
            	if (!cjLook) {
            		cjLook = true;
            		$.get('/activity/chj', function(data){
    					if (data.status) {
    						joinUs.go($("ul li[data-rid='"+data.data.rid+"']").index()+1);
    						leftnum = data.data.left_num;
    					} else if (data.data === '001') {
                            $.tips({
                                content:data.msg,
                                stayTime:2000
                            });
    						cjLook = false;
    					}  else {
    						$('.join-m-btn').html('立即邀请');
    						share();
    						cjLook = false;
    					}
    				}, 'json')
            	}
            }); 

            $('body').on('click', '.pop-join-close, .bet-get', function(){
                $(this).parents('.pop-join').hide();
            })

            $(".btn-invite").click(function(){
            	share();
            })

            function share() {

            	var tagDate = new Date();
                var time = tagDate.getTime() - selectDate;
                if(0 < time && time < 450)
                {
                    return false;
                }
                selectDate = tagDate.getTime();
                
            	<?php if (empty($uid)) {?>
            	$.tips({
                    content:'您尚未登录，请先登录',
                    stayTime:2000
                });
                return ;
    			<?php }?>

    	        if (showbind) {
    	        	$.tips({
                        content:'您尚未实名，请先实名',
                        stayTime:2000
                    });
    	            return ;
    	        }

	            android.shareText('邀请好友赢千元彩金', '【邀请好友赢千元彩金！】166彩票送壕礼，邀请好友赢取千元彩金，好友还能免费领取188元红包，快来参加吧！链接:<?php echo $url?>');
			}
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>