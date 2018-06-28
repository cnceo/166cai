
for (i in jczq) {
	if (jzmintime === undefined) {
		jzmintime = jczq[i].jzdt;
	}else if (jzmintime > jczq[i].jzdt) {
		jzmintime = jczq[i].jzdt;
	}
	jzl++;
}
for (i in jclq) {
	if (jlmintime === undefined) {
		jlmintime = jclq[i].jzdt;
	}else if (jlmintime > jclq[i].jzdt) {
		jlmintime = jclq[i].jzdt;
	}
	jll++;
}
if (jzl === 0 && jll === 0) {
	str = "<div class='nothing-nba'><p>亲，今日暂无赛事！来注 <a href='/ssq'>双色球</a> 吧~</p>";
	str += "<img src='/caipiaoimg/v1.1/img/img-nothing-nba.png' width='386' height='66' alt=''></div>";
	$("div.jc-area-item[data-lottery=jczq], div.jc-area-item[data-lottery=jclq]").html(str);
	$(".jc-area-inner li").html('<strong>暂无赛事</strong>');
	$(".sssq-jczq .mod-bet").html('<div class="nothing-all"><p>亲，今日暂无赛事！来注 <a href="/ssq">双色球</a> 吧~</p><img src="/caipiaoimg/v1.1/img/img-nothing.png" width="380" height="89" alt=""></div>');
}else {
	if (jzl == 0){
		$(".jc-area-item[data-lottery=jczq]").attr('data-lottery', 'jclq');
		$("div.jczq, .jc-area-item[data-lottery=jclq]").each(function(){
			$(this).removeClass('jczq');
			$(this).addClass('jclq');
		})
		$(".sssq-jczq .mod-bet").html('<div class="nothing-all"><p>亲，今日暂无赛事！来注 <a href="/ssq">双色球</a> 吧~</p><img src="/caipiaoimg/v1.1/img/img-nothing.png" width="380" height="89" alt=""></div>');
	}

	if (jll == 0){
		$(".jc-area-item[data-lottery=jclq]").attr('data-lottery', 'jczq');
		$("div.jclq, .jc-area-item[data-lottery=jczq]").each(function(){
			$(this).removeClass('jclq');
			$(this).addClass('jczq');
		})
	}
}

var ssqmultiModifier = new cx.AdderSubtractor('.sssq-ssq .multi-modifier-s');
var dltmultiModifier = new cx.AdderSubtractor('.sssq-dlt .multi-modifier-s');
var syxwmultiModifier = new cx.AdderSubtractor('.sssq-syxw .multi-modifier-s');
var jczqmultiModifier = new cx.AdderSubtractor('.sssq-jczq .multi-modifier-s');
var itemfmultiModifier = new cx.AdderSubtractor('.item-first .multi-modifier-s');
var itemsmultiModifier = new cx.AdderSubtractor('.item-second .multi-modifier-s');
var itemtmultiModifier = new cx.AdderSubtractor('.item-third .multi-modifier-s');
ssqmultiModifier.setCb(function(){
	$(".sssq-ssq .mod-bet-ft em:last").html(2*parseInt(this.getValue(), 10));
})
dltmultiModifier.setCb(function(){
	$(".sssq-dlt .mod-bet-ft em:last").html(2*parseInt(this.getValue(), 10));
})
syxwmultiModifier.setCb(function(){
	$(".sssq-syxw .mod-bet-ft em:last").html(2*parseInt(this.getValue(), 10));
})
jczqmultiModifier.setCb(function(){
	$(".sssq-jczq .mod-bet-ft em:eq(-2)").html(2*parseInt(this.getValue(), 10)*$(".sssq-jczq").attr('data-count'));
	var max = $(".sssq-jczq").attr('data-max'), min = $(".sssq-jczq").attr('data-min');
	if (max == min) {
		$(".sssq-jczq").find(".mod-bet-ft em:last").html(treatodd(min) * parseInt(jczqmultiModifier.getValue(), 10) * 2 / 100);
	}else {
		$(".sssq-jczq").find(".mod-bet-ft em:last").html(treatodd(min) * parseInt(jczqmultiModifier.getValue(), 10) * 2 / 100+"~"+treatodd(max) * parseInt(jczqmultiModifier.getValue(), 10) * 2 / 100);
	}
})
itemfmultiModifier.setCb(function(){
	$(".item-first .mod-bet-ft em:eq(-2)").html(2*parseInt(this.getValue(), 10)*$(".item-first").attr('data-count'));
	var max = $(".item-first").attr('data-max'), min = $(".item-first").attr('data-min');
	if (max == min) {
		$(".item-first").find(".mod-bet-ft em:last").html(treatodd(min) * parseInt(itemfmultiModifier.getValue(), 10) * 2 / 100);
	}else {
		$(".item-first").find(".mod-bet-ft em:last").html(treatodd(min) * parseInt(itemfmultiModifier.getValue(), 10) * 2 / 100+"~"+treatodd(max) * parseInt(itemfmultiModifier.getValue(), 10) * 2 / 100);
	}
})
itemsmultiModifier.setCb(function(){
	$(".item-second .mod-bet-ft em:eq(-2)").html(2*parseInt(this.getValue(), 10)*$(".item-second").attr('data-count'));
	var max = $(".item-second").attr('data-max'), min = $(".item-second").attr('data-min');
	if (max == min) {
		$(".item-second").find(".mod-bet-ft em:last").html(treatodd(min) * parseInt(itemsmultiModifier.getValue(), 10) * 2 / 100);
	}else {
		$(".item-second").find(".mod-bet-ft em:last").html(treatodd(min) * parseInt(itemsmultiModifier.getValue(), 10) * 2 / 100+"~"+treatodd(max) * parseInt(itemsmultiModifier.getValue(), 10) * 2 / 100);
	}
})
itemtmultiModifier.setCb(function(){
	$(".item-third .mod-bet-ft em:eq(-2)").html(2*parseInt(this.getValue(), 10)*$(".item-third").attr('data-count'));
	var max = $(".item-third").attr('data-max'), min = $(".item-third").attr('data-min');
	if (max == min) {
		$(".item-third").find(".mod-bet-ft em:last").html(treatodd(min) * parseInt(itemtmultiModifier.getValue(), 10) * 2 / 100);
	}else {
		$(".item-third").find(".mod-bet-ft em:last").html(treatodd(min) * parseInt(itemtmultiModifier.getValue(), 10) * 2 / 100+"~"+treatodd(max) * parseInt(itemtmultiModifier.getValue(), 10) * 2 / 100);
	}
})

$(function(){
	$.get('/ajax/getTime', function(data){
		tm = data;
	})
	var countz = 0, countl = 0;
	if (jzl > 0) {
		randseljc(jczq[randmid('jczq', $(".sssq-jczq"))], $(".sssq-jczq"), 'jczq');
		$('.jc-area-item[data-lottery=jczq]').each(function(){
			randseljc(jczq[randmid('jczq')], $(this), 'jczq');
		})
	}
	if (jll > 0) {
		$('.jc-area-item[data-lottery=jclq]').each(function(){
			randseljc(jclq[randmid('jclq')], $(this), 'jclq');
		})
	}
	setInterval(function(){
        tm++;
        if (jzmintime/1000 < tm && countz < 5) {
        	$.ajax({
            	url:'/source/cache/jczq.html?'+Math.floor(Math.random()*10000),
            	dataType: 'json',
            	beforeSend: function () {
            		countz++;
				},
				success: function(data){
					if (data){
		        		jczq = data[0];
						hotjz = data[1];
						jzmintime = tm * 10000;
						for (i in jczq) {
							if (jzmintime > jczq[i].jzdt) {
								jzmintime = jczq[i].jzdt;
							}
							jzl++;
						}
						if (jzmintime > tm * 1000) {
							countz = 0;
						}
						hot = 1;
						midindexz = 0;
						$(".sssq-jczq").attr('data-midindex', '0');
			        	randseljc(jczq[randmid('jczq', $(".sssq-jczq"))], $('.sssq-jczq'), 'jczq');
			        	$(".jc-area-item").each(function(){
			        		if ($(this).attr('data-lottery') === 'jczq') {
			        			randseljc(jczq[randmid('jczq')], $(this), 'jczq');
			        		}
			        	})
					}
	    		}
            })
        }
        if (jlmintime/1000 < tm && countl < 5) {
        	$.ajax({
            	url:'/source/cache/jclq.html?'+Math.floor(Math.random()*10000),
            	dataType: 'json',
            	beforeSend: function () {
            		countl++;
				},
				success: function(data){
					if (data){
						jclq = data[0];
						hotjl = data[1];
						jlmintime = tm * 10000;
						for (i in jclq) {
							if (jlmintime > jclq[i].jzdt) {
								jlmintime = jclq[i].jzdt;
							}
							jll++;
						}
						if (jlmintime > tm * 1000) {
							countl = 0;
						}
						hot = 1;
						midindexl = 0;
						$(".jc-area-item").each(function(){
			        		if ($(this).attr('data-lottery') === 'jclq') {
			        			randseljc(jclq[randmid('jclq')], $(this), 'jclq');
			        		}
			        	})
					}
	    		}
            })
        }
    }, 1000)
	var ssqcount = new countdown(ssqtime, 'ssq');
	var dltcount = new countdown(dlttime, 'dlt');
	var syxwcount = new countdown(syxwtime, 'syxw');
	randsel('ssq');
	randsel('dlt');
	randsel('syxw');
	// 首页轮播图
    $(".slide").slideFocusPlugin({
      arrowBtn: true,
      leftArrowBtnClass: 'slide-btn-l',
      rightArrowBtnClass: 'slide-btn-r',
      tabClassName: 'slide-num-inner',
      selectClass: "current",
      stepNum: 300,
      animateStyle: ["fade"]
    });
    
    $(".jc-area-tab").tabPlug({
        cntSelect: '.jc-area-con',
        menuChildSel: 'li',
        onStyle: 'current',
        cntChildSel: '.jc-area-item',
        eventName: 'mouseover'
    });
    
    $(".tab-menu").tabPlug({
        cntSelect: '.rapid-bet',
        menuChildSel: 'li',
        onStyle: 'current',
        cntChildSel: '.rapid-bet-bd',
        eventName: 'mouseover'
    });
    
    $(".n-tab").tabPlug({
        cntSelect: '.tabWrap',
        menuChildSel: 'li',
        onStyle: 'active',
        cntChildSel: '.n-cont',
        eventName: 'click mouseover'
    });

    $('.slide').hover(
        function(){
            $(this).find('.slide-btn').fadeIn(400)
        },
        function(){
            $(this).find('.slide-btn').fadeOut(400)
        }
    )
    
    $(".win-news-bd").myScroll({
        speed: 40,
        rowHeight: 39
      });
})

function randmid(lottery, div){
	var hotArr, hot;
	switch (lottery) {
		case 'jczq':
			hotArr = hotjz;
			break;
		case 'jclq':
			hotArr = hotjl;
			break;
	}
	if (!div) {
		switch (lottery) {
			case 'jczq':
				if (hotArr[hotz] === undefined || hotArr[hotz][midindexz] === undefined) {
					if (hotz < 9 && hotz > 0) {
						hotz++;
						midindexz = 0;
					}else if (hotz == 9) {
						hotz = 0;
						midindexz = 0;
					}else {
						return null;
					}
					return randmid(lottery);
				}
				mid = hotArr[hotz][midindexz];
				midindexz++;
				break;
			case 'jclq':
				hotArr = hotjl;
				if (hotArr[hotl] === undefined || hotArr[hotl][midindexl] === undefined) {
					if (hotl < 9 && hotl > 0) {
						hotl++;
						midindexl = 0;
					}else if (hotl == 9) {
						hotl = 0;
						midindexl = 0;
					}else {
						return null;
					}
					return randmid(lottery);
				}
				mid = hotArr[hotl][midindexl];
				midindexl++;
				break;
		}
	}else {
		h = parseInt(div.attr('data-hot'));
		m = parseInt(div.attr('data-midindex'));
		if (hotArr[h] === undefined || hotArr[h][m] === undefined) {
			if (h < 9) {
				h++;
			} else {
				h = 0;
			}
			m = 0;
			div.attr('data-hot', h);
			div.attr('data-midindex', m);
			return randmid(lottery, div);
		}
		mid = hotArr[h][m];
		m++;
		div.attr('data-midindex', m);
	}
	return mid;
}

var amountConfig = {
	'ssq' : [33, 16],
	'dlt' : [35, 12],
	'syxw' : [11]
}

var minConfig = {
	'ssq' : [6, 1],
	'dlt' : [5, 2],
	'syxw' : [5]
}

var countdown = function(time, lottery, options){
	this.time = time;
	this.lottery = lottery;
	this.selector = $(".sssq-"+lottery);
	this.d = Math.floor(this.time / 86400);
	this.h = Math.floor((this.time % 86400) / 3600);
	this.m = Math.floor((this.time % 3600) / 60);
	this.s = Math.floor((this.time % 3600) % 60);
	this.init();
}

countdown.prototype = {
	init : function() {
		var self = this, counts = 0, cycle = 1000, url = '/source/cache/'+self.lottery+'.html';
		self.rander();
		var timefun = function(){
			if (self.time <= 0 && counts < 5) {
				$.ajax({
					url:url+'?'+Math.floor(Math.random()*10000),
					dataType: 'json',
					beforeSend: function () {
						clearInterval(timer);
						cycle *= 2;
						counts++;
					},
					success: function (data) {
						$(".sssq-"+self.lottery+" .mod-bet-hd span:first").html('第'+data.issue+'期');
						self.time = data.restTime;
						if(self.time > 0) {
							counts = 0;
							if (self.lottery == 'syxw') {
								self.randersyxw();
							} else {
								if (data.jrkj == 1) {
									self.selector.find('.mod-bet-hd span:eq(-1) s').html('<u class="arrow-tag">今日开奖</u>');
								}else {
									self.selector.find('.mod-bet-hd span:eq(-1) s').html('');
								}
								self.rander();
							}
						}
						cycle = 1000;
						timer = setInterval(timefun, cycle);
		            },
					error: function (data) {
						timer = setInterval(timefun, cycle)
					}
				})
			}else {
				self.time--;
				self.d = Math.floor(self.time / 86400);
				self.h = Math.floor((self.time % 86400) / 3600);
				self.m = Math.floor((self.time % 3600) / 60);
				self.s = Math.floor((self.time % 3600) % 60);
				if (self.lottery == 'syxw') {
					self.randersyxw();
				} else {
					self.rander();
				}
			}
		}
		var timer = setInterval(timefun, 1000);
	},
	rander : function(){
		if (this.d > 0) {
			this.selector.find('.mod-bet-hd span:eq(1)').html("截止时间：<em>"+pad(this.d)+"</em>天<em>"+pad(this.h)+"</em>小时<em>"+pad(this.m)+"</em>分");
		} else if (this.time > 0) {
			this.selector.find('.mod-bet-hd span:eq(1)').html("截止时间：<em>"+pad(this.h)+"</em>小时<em>"+pad(this.m)+"</em>分<em>"+pad(this.s)+"</em>秒");
		} else {
			this.selector.find('.mod-bet-hd span:eq(1)').html("截止时间：<em>00</em>小时<em>00</em>分<em>00</em>秒");
		}
	},
	randersyxw : function(){
		if (this.h > 0) {
			this.selector.find('.mod-bet-hd span:eq(1)').html("截止时间：<em>"+pad(this.h)+"："+pad(this.m)+"："+pad(this.s)+"</em>");
		} else if (this.time > 0) {
			this.selector.find('.mod-bet-hd span:eq(1)').html("截止时间：<em>"+pad(this.m)+"："+pad(this.s)+"</em>");
		} else {
			this.selector.find('.mod-bet-hd span:eq(1)').html("截止时间：<em>00：00</em>");
		}
	}
}

var randsel = function(lottery){
	var balls = {}
	var str = '';
	var castStr = '';
	
	var max = $(".sssq-"+lottery).data('max').split(',');
	for (i in minConfig[lottery]) {
		balls[i] = [];
		while (balls[i].length < minConfig[lottery][i]) {
    		j = Math.ceil(Math.random() * amountConfig[lottery][i]);
    		if ($.inArray(j, balls[i]) === -1){
    			balls[i].push(j);
    		}
    	}
    	balls[i].sort(function(a, b){
			a = parseInt(a, 10);
			b = parseInt(b, 10);
			return a > b ? 1 : ( a < b ? -1 : 0 );
		})
	}
	renderCast(balls, $(".sssq-"+lottery).data('playtype'), lottery, max);
}

var randseljc = function(info, div, lottery){
	if (info) {
		if (div.length > 0) {
			var time = new Date(info.jzdt);
			div.attr('data-mid', info.mid);
			if (div.hasClass('sssq-jczq')) {
				var modifider = jczqmultiModifier;
			}else if (div.hasClass('item-first')) {
				var lidiv = $(".jc-area-tab li:first");
				var modifider = itemfmultiModifier;
			}else if (div.hasClass('item-second')) {
				var lidiv = $(".jc-area-tab li:eq(1)");
				var modifider = itemsmultiModifier;
			}else if (div.hasClass('item-third')) {
				var lidiv = $(".jc-area-tab li:eq(2)");
				var modifider = itemtmultiModifier;
			}
			
			if (div.hasClass('sssq-jczq')) {
				div.find('.mod-bet-hd').html('<a href="/'+lottery+'/hh" class="more">更多比赛<i>»</i></a><span>'+info.nameSname+'</span><span>'+pad(time.getMonth()+1)+'-'+pad(time.getDate())+'  '+pad(time.getHours())+':'+pad(time.getMinutes())+'</span><span>'+info.homeSname+'vs '+info.awarySname+'</span>');
			}else {
				div.find('.mod-bet-hd').html('<a href="/'+lottery+'/hh" class="more">更多比赛<i>»</i></a><span>投注截止：</span><span>'+pad(time.getMonth()+1)+'-'+pad(time.getDate())+'  '+pad(time.getHours())+':'+pad(time.getMinutes())+'</span>');
			}
			str = '<ul class="mod-match';
			if (!div.hasClass('sssq-jczq')) {
				str += '-b';
			}
			if (lottery == 'jczq') {
				if (lidiv) {
					lidiv.html('<span class="type-tag">'+info.nameSname+'</span><strong>'+info.homeSname+' VS '+info.awarySname+'</strong>');
				}
				str += '"><li class="mod-match-zhu" data-type="3" data-odd="'+info.spfSp3+'"><a href="javascript:;" class="selected" target="_self">';
				str += '<strong>'+info.homeSname+' 胜</strong><span>'+info.spfSp3+'</span></a></li><li data-type="1" class="mod-match-ping" data-odd="'+info.spfSp1+'"><a href="javascript:;" target="_self">';
				str += '<strong>平局</strong><span>'+info.spfSp1+'</span></a></li><li data-type="0" class="mod-match-ke" data-odd="'+info.spfSp0+'"><a href="javascript:;" target="_self"><strong>'+info.awarySname+' 胜</strong>';
				str += '<span>'+info.spfSp0+'</span></a></li></ul>';
				div.find('.mod-bet-bd .mod-bet-l').html(str);
				div.find(".mod-bet-ft em:last").html(treatodd(info.spfSp3) * parseInt(modifider.getValue(), 10) * 2 / 100);
				div.attr('data-max', info.spfSp3);
				div.attr('data-min', info.spfSp3);
				var odds = {3:info.spfSp3};
			}else {
				if (lidiv) {
					lidiv.html('<span class="type-tag">'+info.nameSname+'</span><strong>'+info.awarySname+' VS '+info.homeSname+'</strong>');
				}
				str += '"><li class="mod-match-ke" data-type="0" data-odd="'+info.rfsfHf+'"><a href="javascript:;" target="_self"><strong>'+info.awarySname+' 胜</strong>';
				str += '<span>'+info.rfsfHf+'</span></a></li><li class="mod-match-ping"></li><li class="mod-match-zhu" data-type="1" data-odd="'+info.rfsfHs+'"><a target="_self" href="javascript:;" class="selected">';
				str += '<strong>'+info.homeSname+info.let+' 胜</strong><span>'+info.rfsfHs+'</span></a></li></ul>';
				div.find('.mod-bet-bd .mod-bet-l').html(str);
				div.find(".mod-bet-ft em:last").html(treatodd(info.rfsfHs) * parseInt(modifider.getValue(), 10) * 2 / 100);
				div.attr('data-max', info.rfsfHs);
				div.attr('data-min', info.rfsfHs);
				var odds = {1:info.rfsfHs};
			}
			div.find("li").click(function(){
				tretodd($(this), odds, div, modifider)
			})
		}
	}else {
		if (div.hasClass('item-first')) {
			$(".jc-area-tab li:first").html('<strong>暂无赛事</strong>');
		}else if (div.hasClass('item-second')) {
			$(".jc-area-tab li:eq(1)").html('<strong>暂无赛事</strong>');
		}else if (div.hasClass('item-third')) {
			$(".jc-area-tab li:eq(2)").html('<strong>暂无赛事</strong>');
		}
		div.find('.mod-bet').html('<div class="nothing-all"><p>亲，今日暂无赛事！来注 <a href="/ssq">双色球</a> 吧~</p><img src="/caipiaoimg/v1.1/img/img-nothing.png" width="380" height="89" alt=""></div>');
	}
}

var tretodd = function(self, odds, div, modifider){
	self.find('a').toggleClass('selected');
	if(self.find('a').hasClass('selected')) {
		odds[self.attr('data-type')] = self.attr('data-odd');
	}else {
		delete odds[self.attr('data-type')];
	}
	var max = '0', min ='0', count = 0;
	for(i in odds) {
		count++;
		if (max == '0' || treatodd(odds[i]) > treatodd(max)) {
			max = odds[i];
		}
		if (min == '0' || treatodd(odds[i]) < treatodd(min)) {
			min = odds[i];
		}
	}
	div.find(".mod-bet-ft em:eq(-2)").html(2*parseInt(modifider.getValue() * count, 10));
	div.attr('data-max', max);
	div.attr('data-min', min);
	div.attr('data-count', count);
	if (max == min) {
		div.find(".mod-bet-ft em:last").html(treatodd(min) * parseInt(modifider.getValue(), 10) * 2 / 100);
	}else {
		div.find(".mod-bet-ft em:last").html(treatodd(min) * parseInt(modifider.getValue(), 10) * 2 / 100+"~"+treatodd(max) * parseInt(modifider.getValue(), 10) * 2 / 100);
	}
}

function treatodd(odd){
	return parseInt(odd.replace(/\./, ''), 10);
}

function renderCast(balls, playtype, lottery, max){
	var castStr = '',str = '';
	$.each(balls, function(i, ele){
		$.each(ele, function(j, e){
			if ($.inArray(lottery, ['ssq', 'dlt']) > -1 && i == 1) {
				str += renderBlue(e, max[i], j);
			}else {
				str += renderRed(e, max[i], j);
			}
			castStr += balls[i][j]+","
		})
		castStr = castStr.slice(0, -1)+"|";
	})
	castStr = castStr.slice(0, -1)+":"+playtype+":1";
	$(".sssq-"+lottery).attr('data-code', castStr);
	$(".sssq-"+lottery+" .ball-group-b").html(str);
}

$(".btn-bet").click(function(){
	var $item = $(this).parents('.mod-tab-item');
	if ($item.length == 0) {
		$item = $(this).parents('.jc-area-item');
	}
	if($(this).attr('error') == 1){
		switch($item.data('lottery')){
			case 'ssq':
				cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请至少选择<span class='num-red'>６</span>个红球和<span class='num-blue'>１</span>个蓝球"});
				break;
			case 'dlt':
				cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请至少选择<span class='num-red'>５</span>个前区和<span class='num-blue'>２</span>个后区"});
				break;
			case 'syxw':
				cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请至少选择<span class='num-red'>５</span>个号码"});
				break;
		}
		return;
	}
	if ($item.data('lottery') == 'jczq') {
		var url = $item.data('lottery') +  '/hh?mid=' + $item.attr('data-mid'), spf = '';
		spf += $item.find('.mod-match-zhu a').hasClass('selected') ? '3' : '';
		spf += $item.find('.mod-match-ping a').hasClass('selected') ? '1' : '';
		spf += $item.find('.mod-match-ke a').hasClass('selected') ? '0' : '';
		url += '&spf='+spf+'&multiple=' +  $item.find(".multi-modifier-s .number").val();
		location.href = url; 
	}else if ($item.data('lottery') == 'jclq') {
		var url = $item.data('lottery') +  '/hh?midp=' + $item.attr('data-mid'), rfsf = '';
		rfsf += $item.find('.mod-match-zhu a').hasClass('selected') ? '3' : '';
		rfsf += $item.find('.mod-match-ke a').hasClass('selected') ? '0' : '';
		url += '&rfsf='+rfsf+'&multiple=' + $item.find(".multi-modifier-s .number").val();
		location.href = url; 
	}else {
		location.href = $item.data('lottery') +  '?codes=' + encodeURIComponent( $item.attr('data-code') ) 
		+ '&playType=' +  encodeURIComponent( $item.data('playtype') )
		+ '&multi=' +  encodeURIComponent( $item.find(".multi-modifier-s .number").val() );
	}
})

$(".change").click(function(){
	if ($(this).data('lottery') == 'jczq') {
		randseljc(jczq[randmid('jczq', $("."+$(this).data('div')))], $("."+$(this).data('div')), 'jczq');
	}else {
		randsel($(this).parents('.mod-tab-item').data('lottery'));
		$(this).parents('.mod-tab-item').find('.btn-bet').attr('error', '0');
	}
})

$(".mod-tab-item").on('blur', ".rotate", function(){
	var max = $(this).attr('max'), str = '';
	if ($(this).val().match(/\D/g) !== null) {//非法字符
		$(this).val('');
	}else if(parseInt($(this).val()) > max){
		cx.Alert({content: "号码球超出范围"});
    	$(this).val('');
    }else if (!$(this).val() || $(this).val() == 0){
    	$(this).val('');
    }
    if ($(this).val() !== '') {
    	$(this).val(pad($(this).val()));
    }else {
    	$(this).parents('.mod-tab-item').find('.btn-bet').attr('error', '1');
    	$(this).parents('.mod-tab-item').find(".mod-bet-ft em:last").html('0');
    	return;
    }
    	
    var code = $(this).parents('.mod-tab-item').data('code');
    var codesArr = code.split(':');
    var codeArr = codesArr[0].split('|');
    if ($(this).hasClass('blue')) {
        var arr = codeArr[1].split(',');
        if ($.inArray(parseInt($(this).val(), 10).toString(), arr) > -1 && arr[$(this).attr('index')] != parseInt($(this).val(), 10)) {
        	$(this).val('');
        	$(this).parents('.mod-tab-item').find('.btn-bet').attr('error', '1');
        	cx.Alert({content: "号码球数字重复"});
        	$(this).parents('.mod-tab-item').find(".mod-bet-ft em:last").html('0');
        	return;
        }else {
        	arr[$(this).attr('index')] = $(this).val();
        	$(this).parents('.mod-tab-item').find('.btn-bet').attr('error', '0');
            var str = codeArr[0]+"|"+arr.join(',')+":"+codesArr[1]+":"+codesArr[2];
        }
    }else {
    	var arr = codeArr[0].split(',');
    	if ($.inArray(parseInt($(this).val(), 10).toString(), arr) > -1 && arr[$(this).attr('index')] != parseInt($(this).val(), 10)) {
        	cx.Alert({content: "号码球数字重复"});
        	$(this).val('');
        	$(this).parents('.mod-tab-item').find('.btn-bet').attr('error', '1');
        	$(this).parents('.mod-tab-item').find(".mod-bet-ft em:last").html('0');
        	return;
        }else {
        	arr[$(this).attr('index')] = $(this).val();
        	$(this).parents('.mod-tab-item').find('.btn-bet').attr('error', '0');
        	var str = arr.join(',')+"|"+codeArr[1]+":"+codesArr[1]+":"+codesArr[2];
        }
    }
    $(this).parents('.mod-tab-item').attr('data-code', str);
})

function renderRed(ball, max, index){
	return "<span><input class='rotate' value='"+pad(ball)+"' max='"+max+"' index='"+index+"'></span>";
}

function renderBlue(ball, max, index){
	return "<span class='ball-blue'><input class='rotate blue' value='"+pad(ball)+"' max='"+max+"' index='"+index+"'></span>";
}

function pad(i) {
    i = '' + i;
    if (i.length < 2) {
        i = '0' + i;
    }
    return i;
}

$.fn.myScroll = function(options){
    //默认配置
    var defaults = {
      speed:40,  //滚动速度,值越大速度越慢
      rowHeight:24 //每行的高度
    };
    
    var opts = $.extend({}, defaults, options),intId = [];
    
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
      
      this.each(function(i){
        var sh = opts["rowHeight"],speed = opts["speed"],_this = $(this);
        intId[i] = setInterval(function(){
          if(_this.find("ul").height()<=_this.height()){
            clearInterval(intId[i]);
          }else{
            marquee(_this, sh);
          }
        }, speed);

        _this.hover(function(){
          clearInterval(intId[i]);
        },function(){
          intId[i] = setInterval(function(){
            if(_this.find("ul").height()<=_this.height()){
              clearInterval(intId[i]);
            }else{
              marquee(_this, sh);
            }
          }, speed);
        });
      
      });

    }