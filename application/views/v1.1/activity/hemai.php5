<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>合买投注技巧_合买新手指南_166彩票官网</title>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>"></script>
<style>
body { background:#f1f1f1; font:12px/1.5 microsoft yahei,Arial;}
a,a:link,a:hover,a:active { outline:none;}
.bannerwarp { width:100%; height:346px; background:#fff;}
.bannercenter { margin:0 auto; background: #fff url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/bannerbg.jpg');?>) 50% 0 no-repeat;}
.bannercenter h1 {
    height: 272px;
    font-size: 0;
    text-indent: -250%;
    overflow: hidden;
}
.bannercenter p { width:960px; margin:0 auto; padding: 18px 20px 0 20px; overflow:hidden; text-indent:2em; font-weight: bold;}
.bannercenter p em { color: #e1463d;}
.centerWarp { width:1000px; margin:0 auto;}
.listWarp { width:100%; height:50px; margin:20px 0;}
#forfix { width:100%;}
.list_four { width:1000px; height:50px; margin:0 auto; background:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/listbg.png');?>) no-repeat; overflow:hidden; zoom:1;}
.list_four a { width:250px; height:50px; float:left; text-align:center; line-height:50px; color:#fff; font-size:24px;}
.list_four a:hover,.list_four .curr { background:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/hoverlist.png');?>) no-repeat; _background-image:none;_filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled='true', sizingMethod='image', src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/hoverlist.png');?>"); text-decoration:none;}
.list_fourFix { position:fixed; top:0; left:0; z-index:88; _position:absolute; _top:expression(eval(document.documentElement.scrollTop));}
.module_warp { border:1px solid #ddd; border-top:2px solid #db352d; background:#fff; margin-bottom:30px;}
.btn-group-hemai {
    width: 160px;
    float: right;
}
.btn-hemai {
    display: inline-block;
    *display: inline;
    *zoom: 1;
    width: 154px;
    height: 40px;
    margin: 66px 0 66px;
    background: #e1463d;
    border-radius: 4px;
    text-align: center;
    line-height: 40px;
    font-size: 16px;
    color: #fff;
}
.btn-hemai:hover {
    background: #c93830;
    text-decoration: none;
    color: #fff;
}
.module_top { height:72px; background:#f8f8f8; padding-left:76px; line-height:72px; color:#db352d; font-size:26px; font-weight:bold; position:relative;}
.module_top span { width:38px; height:64px; display:block; position:absolute; left:19px; top:-2px; background-image:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/qushibg.png');?>); background-repeat:no-repeat; background-position:-224px -114px; font-family:Arial; font-size:24px; font-weight:bold; line-height:78px; padding-left:12px; color:#f9625a; overflow:hidden;}
.countent_warp { padding: 26px 48px; text-align: center;}
.goplay { width:216px; height:54px; display:block; margin:0 auto; color:#fff; font-size:26px; font-weight:bold; background-image:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/qushibg.png');?>); background-repeat:no-repeat; line-height:54px; margin-bottom:60px; overflow:hidden; padding-left:60px;}
.goplay:hover { background-position:left -57px; text-decoration:none; color:#fff;}
.gotoTop { width:55px; height:111px; position:fixed; left:50%; margin-left:530px; bottom:135px; margin-top:-27px;}

* html .gotoTop {position:absolute; _top:expression(eval(document.documentElement.scrollTop+document.documentElement.clientHeight-this.offsetHeight-135));}
.gotop {
    position: absolute; left: 0; top: 0; width:55px; height:55px; display:none; background-image:url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/qushibg.png');?>); background-repeat:no-repeat; background-position:left -114px;
}
.gotop:hover {background-position:-58px -114px; }
.hemai-go {
     position: absolute; left: 0; bottom: 0;width:55px; height:55px; display:block; background: url(<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/hemai-go.png');?>) 0 0 no-repeat; text-indent: -150px; overflow: hidden; font-size: 0;
}
.hemai-go:hover {
    background-position: 0 -56px;
}							  
/*note-footer*/
.note-footer { width:100%; padding-top:22px; padding-bottom:38px; background:#f9f9f9; border-top:1px solid #eeeeee; text-align:center; color:#999; line-height:20px;}
.pBottomTips { line-height: 20px; height: 20px; overflow: hidden; color: #ccc; font-size: 14px; margin-top: -10px; padding-bottom: 20px;}
.pBottomTips em { color: #d35049; }
.pBottomTips span { color: #888; }
.pBottomTips img { display: inline-block; vertical-align: middle; padding-right: 5px;}
.pBottomTips a.aCustomerService { padding: 0 5px; line-height: 20px; height: 20px; display: inline-block; vertical-align: middle; background: #e1463d; color: #fff; font-size: 12px; border-radius: 2px; -o-border-radius: 2px; -ms-border-radius: 2px; -moz-border-radius: 2px; -webkit-border-radius: 2px;}
.pBottomTips a:hover.aCustomerService { background: #c93830; text-decoration: none;}
</style>
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
	<!--banner start-->
	<div class="bannerwarp">
    	<div class="bannercenter">
            <h1>双色球教你怎么看走势图</h1>
            <p>毛主席说：人多力量大，“合买是指由两个或两个以上的个人共同出资购买彩票,合买的方式既能<em>用小资金共同购买大额彩票</em>，又能<em>提高参与者的中奖机率、降低个人全购高金额彩票的风险。”</em></p>
        </div>
    </div><!--bannerwarp end-->
    <!--banner end-->
    
    <!--centerWarp start-->
    <div class="listWarp">
    	<div id="forfix">
        	<div class="list_four">
            	<a class="curr" href="javascript:">新手入门</a>
                <a href="javascript:">盈利秘籍</a>
                <a href="javascript:">奖金分配</a>
                <a href="javascript:">合买优势</a>
            </div>
        </div>
    </div>
	<div class="centerWarp">
        <div class="module_warp">
        	<div class="module_top"><span>01</span>合买怎么玩：轻松几步分合买大奖</div>
            <div class="countent_warp clearfix">
            	<img class="fl" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/img-hemai1.png');?>" width="710" height="340" alt="">
                <div class="btn-group-hemai">
                    <a href="/hall" target="_blank" class="btn-hemai">马上发起合买</a>
                    <a href="/hemai" target="_blank" class="btn-hemai">现在参与合买</a>
                </div>
                
            </div><!--countent_warp end-->
        </div><!--module_warp end-->
        <div class="module_warp">
        	<div class="module_top"><span>02</span>合买发起人篇：三大要素助您走向合买巅峰</div>
            <div class="countent_warp">
            	<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/img-hemai2.png');?>" width="758" height="484" alt="">
            </div><!--countent_warp end-->
        </div><!--module_warp end-->
        <div class="module_warp">
        	<div class="module_top"><span>03</span>合买跟单人篇：跟单大神分合买大奖</div>
            <div class="countent_warp">
            	<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/img-hemai3.png');?>" width="900" height="270" alt="">
            </div><!--countent_warp end-->
        </div><!--module_warp end-->
        <div class="module_warp">
        	<div class="module_top"><span>04</span>奖金怎么分</div>
            <div class="countent_warp">
            	<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/img-hemai4.png');?>" width="900" height="248" alt="">
            </div><!--countent_warp end-->
        </div><!--module_warp end-->

        <div class="module_warp">
        	<div class="module_top"><span>05</span>合买优势有几何：众人拾柴火焰高</div>
            <div class="countent_warp">
            	<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/img-hemai5.png');?>" width="600" height="560" alt="">
            </div><!--countent_warp end-->
        </div><!--module_warp end-->
        <!--<a class="goplay" href="http://caipiao.2345.com/ssq" target="_blank">投一注试试</a>-->
        <p class="pBottomTips"><em>招贤纳士</em>&nbsp;&nbsp;|&nbsp;&nbsp;<span><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/active/hemai/hornIcon.png');?>" alt="">如果您志在成为合买名人，引领群雄，那么快联系客服小六吧。</span><a href="javascript:;" onclick="easemobim.bind({tenantId: '38338'})" class="aCustomerService">在线客服</a></p>
    </div><!--centerWarp end-->

<div class="gotoTop">
    <a href="javascript:" class="gotop"></a>
    <a href="/hemai?fn=hmzt" target="_blank" class="hemai-go">前往合买大厅</a>
</div>
<?php $this->load->view('v1.1/elements/common/footer_academy');?>
<script src='//kefu.easemob.com/webim/easemob.js'></script>
<script>
$(function() {
	//返回顶部
	var goTop = $(".gotop")
	var gotopShow = function()
	{
		var scrollTop = $(window).scrollTop();
		if(scrollTop > 200)
		{
			goTop.fadeIn(400);
		}else
		{
			goTop.fadeOut(400);
		};
	};

	$(window).on('scroll', function()
	{
		gotopShow();
	});
	goTop.on('click', function() 
	{
		$("body,html").animate({scrollTop: 0}, 700);
	});
	
	//当中菜单选择
	var listA = $(".list_four a");
	var listTop;
	var flag = true;//控制最后一个curr
	
	listA.bind("click",function()
	{
		if($(this).index() == 3)
		{
			flag = false;
		}
		listTop = $(".module_warp").eq($(this).index()).offset().top - 70;
		$("body,html").scrollTop(listTop)
		$(this).addClass("curr").siblings().removeClass("curr");
		setTimeout(function(){
			flag = true;
			},500);
	});
	
	var scrollObj = $("#forfix");

	var setting  = 
	{
		scrollTop:scrollObj.offset().top
	};

	
	var title_top_info = $(".module_warp");
	$(window).bind({scroll:function()
	{
		if($(document).scrollTop() >= setting.scrollTop)
		{
			scrollObj.addClass("list_fourFix");
		}else
		{
			scrollObj.removeClass("list_fourFix");
		};
		
		if(flag)
		{
			for (var i = title_top_info.length - 1; i > -1;  i--) 
			{
				if($(window).scrollTop() >= title_top_info.eq(i).offset().top - 70)
				{
					listA.eq(i).addClass("curr").siblings().removeClass("curr");
					break;
				};
			};	
		};
	}});
	var visitor = {userNickname:'<?php echo empty($this->uid) ? '未登录用户' : $this->uinfo['uname']?>'};
	window.easemobim = window.easemobim || {};
	easemobim.config = {visitor: visitor};
});//jq
</script>
</body>
</html>