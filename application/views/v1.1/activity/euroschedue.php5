<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
<meta>
<title>2016法国欧洲杯赛程赛果-完整赛程表一览-166彩票官网</title>
<meta content="2016法国欧洲杯，赛程赛果，赛程表，166彩票官网" name="Keywords">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/active/euro2016-France.min.css');?>">
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js'); ?>" type="text/javascript" ></script>
</head>
<body>
	<!--top begin-->
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
	<div class="euro-wrap">
		<div class="euro-hd">
			<div class="wrap banner">
				<a class="logo" target="_blank" href="/?cpk=10069"></a>
				<h1>2016法国欧洲杯赛程赛果</h1>
			</div>
			<div class="menu">
				<div class="wrap">
					<ul class="clearfix">
						<li><a href="javascript:;" class="cur">全部赛程<i class="line"></i></a></li>
						<li data-group="A"><a href="javascript:;">A组<i class="line"></i></a></li>
						<li data-group="B"><a href="javascript:;">B组<i class="line"></i></a></li>
						<li data-group="C"><a href="javascript:;">C组<i class="line"></i></a></li>
						<li data-group="D"><a href="javascript:;">D组<i class="line"></i></a></li>
						<li data-group="E"><a href="javascript:;">E组<i class="line"></i></a></li>
						<li data-group="F"><a href="javascript:;">F组<i class="line"></i></a></li>
						<li data-group="other"><a href="javascript:;">淘汰赛<i class="line"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="euro-bd">
			<div class="fixed-box">
				<div class="fixed-wrap">
					<div class="euro-tab">
						<div class="euro-tab-bd">
							<ul class="clearfix"></ul>
						</div>
						<a href="javascript:;" class="leftBtn disabled"></a>
						<a href="javascript:;" class="rightBtn"></a>
						<i class="icon-1"></i>
						<i class="icon-2"></i>
						<i class="icon-3"></i>
						<i class="icon-4"></i>
					</div>
				</div>
			</div>


			<div class="euro-con" style="display:block;">
				<div class="wrap"></div>
			</div>
		</div>
	</div>

	<div class="fixed-adv">
		<a href="/activity/o2o?cpk=10069" target="_blank" class="a-btn btn01"></a>
		<a href="/activity/jcjj?cpk=10069" target="_blank" class="a-btn btn02"></a>
	</div>
<?php $this->load->view('v1.1/elements/common/footer_academy');?>
<script type="text/javascript">



	$(function(){
		render(null, true);

		var $tabBd=$('.euro-tab-bd'),
		    $tabUl=$('.euro-tab-bd ul'),
			$tabConBd=$('.euro-con-bd'),
			$tabLeftBtn=$(".leftBtn"),
			$tabRightBtn=$(".rightBtn"),
			isMove=false,
			arrTem=[];

		$(".euro-tab-bd").on('click', 'li', function(){
			var index=$(this).index(),_this=this;
			$(this).addClass('cur').siblings().removeClass('cur');
			if(isMove){
				return;
			}
			isMove=true;
			$(".wrap dl:eq("+index+")").show();
			$(".wrap dl:gt("+index+")").show();
			arrTem=[];
			for(var i=0;i<$('.euro-con-bd').length;i++){
				arrTem.push($('.euro-con-bd')[i].offsetTop)
			}
			$("html,body").animate({scrollTop: $('.euro-con-bd')[index].offsetTop-175}, 600,function(){
				isMove=false;
			});
		})
		
		$(".menu li").click(function(){
			var group = $(this).data('group')
			if (group) {
				$(".fixed-box").hide();
			}else {
				$(".fixed-box").show();
			}
			render(group);
			$(".menu li a").removeClass('cur');
			$(this).find('a').addClass('cur');
		})


		$tabRightBtn.on('click',function(){
			var _this=this;
			if($(this).hasClass('disabled')){
				return;
			}
			//如果滚到最右边就不滚了
			if(parseInt($tabUl.css('left'))<($('.euro-tab-bd').find('li').length-8)*$('.euro-tab-bd').find('li').width()*(-1)){
				return;
			}
			//如果点击过快只执行一次
			if($tabRightBtn.attr('data-move')==1){
				return;
			}
			$(this).attr('data-move',1);
			$tabLeftBtn.removeClass('disabled');
			$tabUl.animate({"left":'-=145px'},300,function(){
				$(_this).attr('data-move',0);
				if(parseInt($tabUl.css('left'))<($('.euro-tab-bd').find('li').length-8)*$('.euro-tab-bd').find('li').width()*(-1)){
					$tabLeftBtn.removeClass('disabled')
					$tabRightBtn.addClass('disabled')
				}
			})
		})
		$tabLeftBtn.on('click',function(){
			var _this=this;
			if($(this).hasClass('disabled')){
				return;
			}
			//如果滚到最左边就不滚了
			if(parseInt($tabUl.css('left'))==0){
				$(this).addClass('disabled');
				return;
			}
			//如果点击过快只执行一次
			if($(this).attr('data-move')==1){
				return;
			}
			$(this).attr('data-move',1);
			$tabRightBtn.removeClass('disabled');
			$tabUl.animate({"left":'+=145px'},300,function(){
				$(_this).attr('data-move',0);
				if(parseInt($tabUl.css('left'))==0){
					$tabLeftBtn.addClass('disabled')
					$tabRightBtn.removeClass('disabled')
				}
			})
		})


		showFixed();
		function showFixed(){
			if($(window).scrollTop()>=403){
				$('.fixed-adv').addClass("fixed1");
				$('.euro-hd .menu').css({"position":"fixed","top":0,"width":"100%","z-index":999});
				$('.fixed-wrap').css({"position":"fixed","top":"62px","background":"#00143b","width":"100%","z-index":"998"});
			}else{
				$('.fixed-adv').removeClass("fixed1");
				$('.euro-hd .menu,.fixed-wrap').css({"position":"relative",'top':'auto'});
			}
		}


		var timer,isScroll;
		$(window).scroll(function(){
			showFixed();
			timer=setTimeout(function(){
				if(isScroll){
					isScroll=false;
					return;
				}
				isScroll=true;
				for(var i=0;i<arrTem.length;i++){
					if($(window).scrollTop()>arrTem[i-1] && $(window).scrollTop()<arrTem[i]){
						$('.euro-tab-bd').find('li').eq(i).addClass('cur').siblings().removeClass('cur');
						if(i>$('.euro-tab-bd').find('li').length-7){
							clearTimeout(timer);
							isScroll=false;
							return;
						}
						$tabUl.stop(true).animate({"left":-145*i},300,function(){
							isScroll=false;
						});
					}
					if($(window).scrollTop()>0 && $(window).scrollTop()<=arrTem[0]){
						$('.euro-tab-bd').find('li').eq(0).addClass('cur').siblings().removeClass('cur');
						$tabUl.stop(true).animate({"left":0},300,function(){
							isScroll=false;
						});
					}
				}
			},10)

			if($(window).scrollTop()<arrTem[0]){
				$tabLeftBtn.addClass('disabled');
			}else{
				$tabLeftBtn.removeClass('disabled');
			}

			if($(window).scrollTop()>arrTem[$('.euro-tab-bd').find('li').length-7]){
				$tabRightBtn.addClass('disabled');
			}else{
				$tabRightBtn.removeClass('disabled');
			}

		});

		function render(group, first){
			var url = '/ajax/getEuroSchdule';
			var slice = 0;
			if (group) {
				slice += 74;
				url += "/"+group;
			}
			$.get(url, function(response){
				var tabstr = '', dl = '', dlstr = '', k = 0, data = response.date, astr;
				for (i in data) {
					tabstr += '<li data-date="'+i+'"><span class="date">'+i+'</span><span class="week">'+data[i][0].weekday+'</span><i class="line"></i></li>';
					dlstr = '<dl class="euro-con-bd"><dt>2016.'+i+data[i][0].weekday+'</dt><dd><ul class="item-group">';
					for (j in data[i]) {
						if (data[i][j].hid == 0) {
							iconclass = 'icon-country1';
						}else {
							iconclass = 'country-';
						}
						if (data[i][j].type == 1) {
							astr = '组';
						}else {
							astr = '决赛';
						}
						dlstr += '<li class="item"><span class="col-1">'+data[i][j].groups+astr+'</span><span class="col-2">'+data[i][j].time+'</span><span class="col-3">';
						dlstr += data[i][j].home+'&nbsp;<i class="'+iconclass+data[i][j].hid+'"></i></span><span class="col-4">';
						if (data[i][j].full_score !== null && data[i][j].full_score !== undefined) {
							dlstr += '<i class="num blue">'+data[i][j].full_score.split(':')[0]+'</i>&nbsp;&nbsp;&nbsp;&nbsp;<i class="num">'+data[i][j].full_score.split(':')[1]+'</i>';
							dlstr += '<em class="tip">半场（'+data[i][j].half_score.split(':')[0]+'-'+data[i][j].half_score.split(':')[1]+'）</em>'
						}else {
							dlstr += '<b class="vs">VS</b>';
						}
						dlstr += '</span><span class="col-5"><i class="'+iconclass+data[i][j].aid+'"></i>&nbsp;'+data[i][j].away+'</span><span class="col-6">';
						if (data[i][j].hid == 0) {
							dlstr += '敬请期待';
						}else if (data[i][j].full_score !== null && data[i][j].full_score !== undefined) {
							dlstr += '已结束';
						}else if(data[i][j].jiezhi) {
							dlstr += '投注已截止';
						}else if (data[i][j].sale_status == 0){
							dlstr += '<a href="javascript:;" class="a-btn disabled">停售</a>';
						}else {
							dlstr += '<a href="/jczq/hh?cpk=10069" target="_blank" class="a-btn">立即投注</a>';
						}
						dlstr += '</span></li>';
					}
					dlstr += '</ul></dd></dl>';
					dl += dlstr;
				}
				$(".euro-con .wrap").html(dl);
				$(".euro-tab-bd ul").html(tabstr);
				$('.euro-tab-bd ul').css('width',$('.euro-tab-bd li').length*$('.euro-tab-bd li').width());
				var idx = $(".euro-tab-bd ul li[data-date='"+response.today+"']").prevAll().length;
				if (first) {
					$(".wrap dl:lt("+idx+")").hide();
					$(".euro-tab-bd ul li[data-date='"+response.today+"']").addClass('cur');
				}else {
					isMove=true;
					var idx = $(".euro-tab-bd ul li[data-date='"+response.today+"']").prevAll().length;
					$("html,body").animate({scrollTop: $('.euro-con-bd')[idx].offsetTop-175+slice}, 600,function(){
						isMove=false;
					});
				}
				arrTem=[];
				for(var i=0;i<$('.euro-con-bd').length;i++){
					arrTem.push($('.euro-con-bd')[i].offsetTop)
				}
				
				$('.euro-tab-bd ul').css('left', idx * $('.euro-tab-bd li').width()*(-1));
				$(".leftBtn, .rightBtn").removeClass('disabled')
				if ($('.euro-tab-bd li').length < 7) {
					$(".leftBtn, .rightBtn").addClass('disabled')
				}
			}, 'json')
		}

	})

	</script>
</body>

</html>
