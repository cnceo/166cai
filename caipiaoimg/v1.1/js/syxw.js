var td, cy = 1000, rest = tm, inteval, chase = {}, j = 0, boxes = {},
jiangjin = {q1:'13',rx2:'6',rx2dt:'6',rx3:'19',rx3dt:'19',rx4:'78',rx4dt:'78',rx5:'540',rx5dt:'540',rx6:'90',
rx6dt:'90',rx7:'26',rx7dt:'26',rx8:'9',qzhi2:'130',qzhi3:'1170',qzu2:'65',qzu2dt:'65',qzu3:'195',qzu3dt:'195'};

for (i in chases) {
	if (j < chaselength) {
		chase[i] = {};
		chase[i].award_time = chases[i].award_time;
		chase[i].show_end_time = chases[i].show_end_time;
		chase[i].multi = chases[i].multi;
		chase[i].money = chases[i].money;
		j++;
	}else {
		break;
	}
}

$(function() {
	inteval = setInterval("countdown()", cy);
	for (var j = 2; j <= 8; j++){
		boxes['rx'+j] =  new cx.BoxCollection('.rx'+j+' .default .box-collection',{lotteryId: cx.Lottery.SYXW});
	    boxes['rx'+j].add(new cx.BallBox('.rx'+j+' .default .ball-box-1', {lotteryId: cx.Lottery.SYXW, amount: 11, min: j, minBall: 1, mutex: 1, playType: 'rx'+j}), 0);
	    
	    if (j != 8) {
	    	boxes['rx'+j+'dt'] =  new cx.BoxCollection('.rx'+j+' .dt .box-collection',{lotteryId: cx.Lottery.SYXW});
		    boxes['rx'+j+'dt'].add(new cx.BallBox('.rx'+j+' .dt .ball-box-1:last', {lotteryId: cx.Lottery.SYXW, amount: 11, min: j, minBall: 1, mutex: 1, hasdan: true, dmax: j-1, tmin: 2, dtmin: j+1, playType: 'rx'+j+'dt'},'.rx'+j+' .dt .ball-box-1:first'), 0);
	    }
	}
    
    boxes['q1'] =  new cx.BoxCollection('.q1 .default .box-collection',{lotteryId: cx.Lottery.SYXW});
    boxes['q1'].add(new cx.BallBox('.q1 .default .ball-box-1', {lotteryId: cx.Lottery.SYXW, amount: 11, min: 1, minBall: 1, mutex: 1, playType: 'q1'}), 0);
    
    for (j = 2; j <=3; j++) {
    	boxes['qzu'+j] =  new cx.BoxCollection('.qzu'+j+' .default .box-collection',{lotteryId: cx.Lottery.SYXW});
        boxes['qzu'+j].add(new cx.BallBox('.qzu'+j+' .default .ball-box-1', {lotteryId: cx.Lottery.SYXW, amount: 11, min: j, minBall: 1, mutex: 1, playType: 'qzu'+j}), 0);
        
        boxes['qzu'+j+'dt'] =  new cx.BoxCollection('.qzu'+j+' .dt .box-collection',{lotteryId: cx.Lottery.SYXW});
        boxes['qzu'+j+'dt'].add(new cx.BallBox('.qzu'+j+' .dt .ball-box-1:last', {lotteryId: cx.Lottery.SYXW, amount: 11, min: j, minBall: 1, mutex: 1, hasdan: true, dmax: j-1, tmin: 2, dtmin: j+1, playType: 'qzu'+j+'dt'},'.qzu'+j+' .dt .ball-box-1:first'), 0);
        
	    boxes['qzhi'+j] =  new cx.BoxCollection('.qzhi'+j+' .default .box-collection',{lotteryId: cx.Lottery.SYXW});
	    for (var i = 1; i <= j; ++i) {
	        boxes['qzhi'+j].add(new cx.BallBox('.qzhi'+j+' .default .ball-box-'+i, {lotteryId: cx.Lottery.SYXW, amount: 11, min: 1, minBall: 1, mutex: 1, playType: 'qzhi'+j}), i-1);
	    }
    }
    
    boxes['lexuan3'] =  new cx.BoxCollection('.lexuan .lexuan3 .box-collection',{lotteryId: cx.Lottery.SYXW, betMoney:6});
    for (var i = 1; i <= 3; ++i) {
        boxes['lexuan3'].add(new cx.BallBox('.lexuan .lexuan3 .ball-box-'+i, {lotteryId: cx.Lottery.SYXW, amount: 11, min: 1, minBall: 1, mutex: 1, playType: 'lexuan3'}), i-1);
    }
    boxes['lexuan4'] = new cx.BoxCollection('.lexuan .lexuan4 .box-collection',{lotteryId: cx.Lottery.SYXW, betMoney:10});
    boxes['lexuan4'].add(new cx.BallBox('.lexuan .lexuan4 .ball-box-1', {lotteryId: cx.Lottery.SYXW, amount: 11, min: 4, minBall: 1, mutex: 1, playType: 'lexuan4'}), 0);
    boxes['lexuan5'] = new cx.BoxCollection('.lexuan .lexuan5 .box-collection',{lotteryId: cx.Lottery.SYXW, betMoney:14});
    boxes['lexuan5'].add(new cx.BallBox('.lexuan .lexuan5 .ball-box-1', {lotteryId: cx.Lottery.SYXW, amount: 11, min: 5, minBall: 1, mutex: 1, playType: 'lexuan5'}), 0);
    
    var multiModifier = new cx.AdderSubtractor('.multi-modifier');
    	
    var basket = new cx.CastBasket('.cast-basket', {
        lotteryId: cx.Lottery.SYXW,
        boxes: boxes,
        multiModifier: multiModifier,
        chases: chase,
        chaseLength: chaselength,
        playType: 'rx8',
        issue: ISSUE,
        tab: 'bet-type-link li',
        tabClass: 'selected',
        getCastOptions:'getCastOptions1'
    });
    cx._basket_ = basket;
    
$(".rx8 .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    
    $(".tab-list-hd").on("click", "li", function(){
    	$(this).find('input').attr("checked","checked" );
    })
	
    $(".tab-list-hd li input").click(function(e){
    	e.stopPropagation();
    })
    
	$(".bet-type-link li, .tab-list-hd li").click(function(){
		var type = $(this).attr('data-type') ? $(this).attr('data-type') : $(this).find("input").attr("id");
		cx._basket_.setType(type);
		cx._basket_.boxes[type].renderBet();
    	if(getRule(21406, type, boxes[type].isValid())=== true) {
    		$('.'+type+' .add-basket').removeClass('btn-disabled');
    	}else if(!$('.'+type+' .add-basket').hasClass('btn-disabled')){
    		$('.'+type+' .add-basket').addClass('btn-disabled');
    	}
    	if(boxes[type].edit > 0) {
    		$('.cast-list').find('li').removeClass('hover');
    	    $('.cast-list').find('li[data-index="'+boxes[type].edit+'"]').addClass('hover');
		}
		rfshhisty(type);
		m = Math.floor(tm / 60);
        s = Math.floor(tm % 60);
        if (type.indexOf('lexuan') > -1) {
        	$("."+type+" .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
        }else if (type.indexOf('dt') > -1) {
        	$("."+type.replace(/dt/, '')+" .dt .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
        }else {
        	$("."+type+" .default .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
        }
	})
	
	$('.my-order-hd').on('click', function(){
        var myOrder = $(this).parents('.my-order');
        var myOrderBd = myOrder.find('.my-order-bd');
        myOrder.toggleClass('my-order-open');
        myOrderBd.slideToggle();
        return false;
    })
    
    $('.ykj-info').find('tbody tr:last-child').show();
    $('.ykj-info-action').on('click', function(){
    	var ykjInfo = $(this).parents('.ykj-info');
        // 控制是否展开
        ykjInfo.toggleClass('ykj-info-open');
        ykjInfo.find('thead').toggle();
        ykjInfo.find('tr').toggle();
        $('.ykj-info').find('tr:last-child').show();
        $('.canvas-mask').toggle();
        if ($.inArray(cx._basket_.playType, ['q1', 'qzhi2', 'qzhi3', 'lexuan3']) > -1) cavas();
        if($('.ykj-info').hasClass('ykj-info-open')){
        	$(".ykj-info-action").html("收起走势<i class='arrow'></i>");
        } else {
        	$(".ykj-info-action").html("近期走势<i class='arrow'></i>");
        }
    });
    
 // 滚动接近tab模块时，吸顶
    var ceilingBox = $('.bet-syxw .cp-box-bd');
    var thisScrollTop;
    var ceilingBoxTop;
    var thisWindow = $(window);
    var beforeScrollTop = thisWindow.scrollTop();
    thisWindow.on('scroll', function(){
        afterScrollTop = thisWindow.scrollTop();
        ceilingBoxTop = ceilingBox.offset().top;
        // 向下滚动
        if(afterScrollTop > beforeScrollTop){
            if(afterScrollTop >= ceilingBoxTop - 120 && afterScrollTop < ceilingBoxTop){
                $('html, body').scrollTop(ceilingBoxTop)
            }
        }
        beforeScrollTop = afterScrollTop;     
    })
    
    $(".my-order-hd a").click(function(){
    	if(!$(this).hasClass('not-login') && $(".my-order-bd table").length == 0) {
    		$.ajax({
    			url: "/ajax/getOrders",
    			dataType: 'json',
    			success: function(data) {
    				var str = '<table><colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup><thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal"><a target="_blank" href="mylottery/betlog">更多</a></th></tr></thead><tbody>';
    				if (data.length > 0) {
    					$.each(data, function(i, dt){
    						str += '<tr><td>'+dt.created+'</td><td>'+dt.issue+'</td><td class="tal" title="';
    						var codes = dt.codes.split(';'), carr = [], ci = 0;
    						$.each(codes, function(j, cds){
    							carr = cds.split(':');
    							str += cx.Lottery.getPlayTypeName(21406, carr[1]);
    							if ($.inArray(carr[1], ['09', '10', '11', '12']) === -1) str += (carr[2] == '05' ? '胆拖': (carr[0].split(',').length > parseInt(carr[1]) ? '复式' : '单式'));
    							str += carr[0].replace(/((.+)\$)/ig, '( $2 ) ').replace(/,/ig, ' ').replace(/\|/g, '<s> | </s>')+';';
    							ci++;
    						})
    						carr = codes[0].split(':');
    						str += '"><div class="text-overflow">'+cx.Lottery.getPlayTypeName(21406, carr[1]);
    						if ($.inArray(carr[1], ['09', '10', '11', '12']) === -1) str += (carr[2] == '05' ? '胆拖': (carr[0].split(',').length > parseInt(carr[1]) ? '复式' : '单式'));
    						str += ' <span class="specil-color">'+carr[0].replace(/((.+)\$)/ig, '( $2 ) ').replace(/,/ig, ' ').replace(/\|/g, '<s> | </s>')+'</span>';
    						if (ci > 1) str += '...';
    						str += '</div></td><td><span class="fcs">'+fmoney(parseInt(dt.money, 10) / 100, 3)+'.00</span></td><td><span class="fcs">'+cx.Order.getStatus(dt.status, 3)+'</span></td>';
    						if (dt.margin > 0) {
    							str += '<td><img src="/caipiaoimg/v1.1/img/gold.png" alt="">&nbsp;<strong class="spec arial">'+fmoney(dt.margin / 100, 3)+'.00</strong></td>';
    						}else if($.inArray(dt.status, ['1000', '2000']) === -1) {
    							str += '<td>--</td>'
    						}else {
    							str += '<td>'+fmoney(dt.margin / 100, 3)+'.00</td>';
    						}
    						str += '<td><a target="_blank" href="/orders/detail/'+dt.orderId+'">查看详情</a>';
    						if (dt.ljzf == 1) {
    							str += '<a href="javascript:cx.castCb({orderId:\''+dt.orderId+'\'}, {ctype:\'paysearch\', orderType:0});"><span class="num-red">立即支付</span></a>';
    						}else {
    							str += '<a target="_blank" href="/hbsyxw?orderId='+dt.orderId+'">继续预约</a></td><td></td></tr>';
    						}
    					})
    				} else {
    					str += '<tr><td colspan="8" style="height: 100px;">亲，您三个月内还没有订单哦！</td></tr></tbody></table>'
    				}
    				$(".my-order-bd").html(str);
    			}
    		})
    	}
    })
    
    kjNumAimation(vJson)
    setTimeout(function(){
        kjNumAimation(vJson)
    }, 2000)
});

function render(data){
	$(".lottery-info-time b").html(data.issue);//渲染页面中当前期
    $(".kj-periods b").html(data.prev);//渲染页面上一期
    
    //渲染页面倒计时剩余时间
    if (cx._basket_.playType.indexOf('dt') > -1) {
    	$("."+cx._basket_.playType.replace(/dt/, '')+" .dt .pick-area-time").html("<em><b>"+data.issue.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    }else if (cx._basket_.playType.indexOf('lexuan') > -1) {
    	$("."+cx._basket_.playType+" .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    }else {
    	$("."+cx._basket_.playType+" .default .pick-area-time").html("<em><b>"+data.issue.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    }
    $(".lottery-info-tips em").html("还剩"+data.rest+"期");//渲染页面已售、剩余
    rfshhisty(cx._basket_.playType);//刷新遗漏数据、最近时期开奖走势列表
    
    //渲染页面开奖号码
    kjNumAimation(vJson);
    setTimeout(function(){
        kjNumAimation(vJson)
    }, 2000);
}

//开奖动画
function kjNumAimation(json){
    setTimeout(function(){
        var kjNumUl = $('.kj-num-item').find('ul');
        var kjNumLiHeight = kjNumUl.find('li').height();
        $(json).each(function(index){
                $(kjNumUl[index]).animate({
                    top: -kjNumLiHeight * (parseInt(json[index], 10) ? parseInt(json[index], 10) : 0)
                }, 800)
        })
    }, 0)
    
}

function rfshhisty(type) {
	$('.canvas-mask').hide();
	$('.canvas-mask').empty();
	$(".ykj-info .column-num").remove();
	$(".ykj-info").removeClass('qezx');
	$(".ykj-info").removeClass('qezx')
	var hstr = '<th class="column-num"><div class="ball-group-s">';
	for (var n = 1; n <=11; n++) {
		hstr += '<span>'+padd(n)+'</span>';
	}
	hstr += '</div></th>';
	if(type === 'qzhi2') {
		$(".ykj-info").addClass('qezx');
		var str = "<colgroup><col width='85'><col width='304'><col width='304'><col width='124'><col width='59'><col width='59'><col width='59'></colgroup><thead";
		if ($(".ykj-info").hasClass('ykj-info-open')) {
			str += " style='display: table-header-group;'><tr style='display: table-row;'";
		}else {
			str += "><tr";
		}
		str += "><th>期次</th>"+hstr+hstr+"<th>开奖号码</th><th>和值</th><th>大小比</th><th>奇偶比</th></tr></thead>";
		$.each(hsty, function(h, ele){
			str += "<tr";
			if (h == 9 || $(".ykj-info").hasClass('ykj-info-open')) str += ' style="display: table-row;"';
			str += "><td>"+ele.issue+"</td><td class='column-num'";
			if (ele.awardNum === undefined) {
				str += " colspan='2'>正在开奖中...</td><td>--</td><td>--</td><td>--</td><td>--</td>";
			} else {
				str += "><div class='ball-group-s column-1'>";
				var awardNum = ele.awardNum.split(',');
				var hi = 0;
				if(mall[ele.issue] !== undefined) {
					$.each(mall[ele.issue][1].split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), awardNum) > -1 && hi == awardNum[0]) {
							str += "<span class='selected'>"+hi+"</span>";
						} else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td>";
					str += "<td class='column-num'><div class='ball-group-s column-2'>";
					hi = 0;
					$.each(mall[ele.issue][2].split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), awardNum) > -1 && hi == awardNum[1]) {
							str += "<span class='selected'>"+hi+"</span>";
						} else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td>";
				} else {
					str += "---</div></td><td class='column-num'><div class='ball-group-s column-2'>---</div></td>";
				}
				str += "<td><div class='num-group'>";
				$.each(awardNum, function(aw, e){
					str += '<span>'+e+'</span>';
				})
				str += "</div></td><td>"+ele.he+"</td><td>"+ele.dx+"</td><td>"+ele.jo+"</td></tr>";
			}
		})
		$(".ykj-info table").html(str);
	} else if ($.inArray(type, ['qzhi3', 'lexuan3']) > -1) {
		$(".ykj-info").addClass('qezx');
		var str = "<colgroup><col width='86'><col width='304'><col width='304'><col width='304'></colgroup><thead";
		if ($(".ykj-info").hasClass('ykj-info-open')) {
			str += " style='display: table-header-group;'><tr style='display: table-row;'";
		}else {
			str += "><tr";
		}
		str += "><th>期次</th>"+hstr+hstr+hstr+"</tr></thead>";
		$.each(hsty, function(h, ele){
			str += "<tr";
			if (h == 9 || $(".ykj-info").hasClass('ykj-info-open')) str += ' style="display: table-row;"';
			str += "><td>"+ele.issue+"</td><td class='column-num'";
			if (ele.awardNum === undefined) {
				str += " colspan='3'>正在开奖中...</td>";
			} else {
				str += "><div class='ball-group-s column-1''>";
				var awardNum = ele.awardNum.split(',');
				var hi = 0;
				if(mall[ele.issue] !== undefined) {
					$.each(mall[ele.issue][1].split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), awardNum) > -1 && hi == awardNum[0]) {
							str += "<span class='selected'>"+hi+"</span>";
						} else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td>";
					str += "<td class='column-num'><div class='ball-group-s column-2''>";
					hi = 0;
					$.each(mall[ele.issue][2].split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), awardNum) > -1 && hi == awardNum[1]) {
							str += "<span class='selected'>"+hi+"</span>";
						} else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td>";
					str += "<td class='column-num'><div class='ball-group-s column-3''>";
					hi = 0;
					$.each(mall[ele.issue][3].split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), awardNum) > -1 && hi == awardNum[2]) {
							str += "<span class='selected'>"+hi+"</span>";
						} else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td>";
				} else {
					str += "---</div></td><td class='column-num'><div class='ball-group-s column-2'>---</div></td><td class='column-num'><div class='ball-group-s column-3'>---</div></td>";
				}
			}
		})
		$(".ykj-info table").html(str);
	} else {
		var str = "<colgroup><col width='160'><col width='506'><col width='152'><col width='60'><col width='60'><col width='60'></colgroup><thead";
		if ($(".ykj-info").hasClass('ykj-info-open')) {
			str += " style='display: table-header-group;'><tr style='display: table-row;'";
		}else {
			str += "><tr";
		}
		str += "><th>期次</th>"+hstr+"<th>开奖号码</th><th>和值</th><th>大小比</th><th>奇偶比</th></tr></thead>";
		$.each(hsty, function(h, ele){
			str += "<tr";
			if (h == 9 || $(".ykj-info").hasClass('ykj-info-open')) str += ' style="display: table-row;"';
			str += "><td>"+hsty[h].issue+"</td><td class='column-num'>";
			if (hsty[h].awardNum === undefined) {
				str += "正在开奖中...</td><td><span class='specil-color'>--</span></td><td>--</td><td>--</td><td>--</td>";
			} else {
				str += "<div class='ball-group-s column-1'>";
				var awardNum = hsty[h].awardNum.split(','), award = awardNum;
				if($.inArray(type, ['qzu2', 'qzu3', 'q1']) > -1) {
					var index = parseInt(type.replace(/(\D+)/ig, ''), 10);
					award = awardNum.slice(0, index);
				}
				var mss;
				if(mall[hsty[h].issue] !== undefined) {
					var hi = 0;
					if (type == 'q1') {
						mss = mall[hsty[h].issue][1];
					} else if($.inArray(type, ['qzu2', 'qzu3']) > -1) {
						mss = mall[hsty[h].issue][index+2];
					} else {
						mss = mall[hsty[h].issue][0];
					}
					$.each(mss.split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), award) > -1) {
							str += "<span class='selected'>"+hi+"</span>";
						}else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td>"
				} else {
					str += "---</div></td>";
				}
				str += "<td><div class='num-group'>";
				$.each(awardNum, function(aw, e){
					str += '<span>'+e+'</span>';
				})
				str += "</div></td><td>"+hsty[h].he+"</td><td>"+hsty[h].dx+"</td><td>"+hsty[h].jo+"</td></tr>";
			}
		})
		$(".ykj-info table").html(str);
	}
	if ($.inArray(type, ['q1', 'qzhi2', 'qzhi3', 'lexuan3']) > -1 && $(".ykj-info").hasClass('ykj-info-open')) {
		$('.canvas-mask').show();
		cavas();
	}
}

var cavas = function () {
    // 静态页面点击创建，到开发那边改为ajax加载开奖结果成功后开始创建
    if($('.ykj-info').hasClass('ykj-info-open')){
        var i = 0;
        var itemAarry = [];
        var item = $('.ykj-info').find('.ball-group-s').find('span.selected');
        var columnNum = $('.ykj-info').find('tbody tr').eq(0).find('.ball-group-s').length;

        // 获取canvas父级的定位
        var canvasMaskOffset = $('.canvas-mask').offset();
        var canvasMaskTop = canvasMaskOffset.top; 
        var canvasMaskLeft = canvasMaskOffset.left;
        $('.canvas-mask').attr({
            'data-left': canvasMaskLeft,
            'data-top': canvasMaskTop
        })

        // 给选中的球添加自身的定位参数
        for(var i = 0, itemLength = item.length; i < itemLength; i++){
            $(item[i]).attr({
                'data-top': Math.round($(item[i]).offset().top - canvasMaskTop) + 10,
                'data-left': Math.round($(item[i]).offset().left - canvasMaskLeft) + 10
            })
        }

        // 中奖数字分组
        for(var i = 0; i < columnNum; i++){
            itemAarry.push($('.ykj-info').find('.column-' + (i + 1)).find('span.selected'))
        }

        for(var k = 0, itemAarryLength = itemAarry.length; k < itemAarryLength; k++){
            for( var i = 0, itemAarryClength = itemAarry[k].length; i < itemAarryClength; i++){
                    //控制创建canvas的个数
                if(i < (itemAarryClength - 1)){

                    // 计算两个中奖球之间矩形的宽高
                    var left1 = Math.round($(itemAarry[k][i]).attr('data-left'));
                    var top1 = Math.round($(itemAarry[k][i]).attr('data-top'));
                    var left2 = Math.round($(itemAarry[k][i+1]).attr('data-left'));
                    var top2 = Math.round($(itemAarry[k][i+1]).attr('data-top'));
                    var width = left2 - left1;
                    var height = top2 - top1;
                    var canvasTag = document.createElement('canvas');

                    // 插入到html中
                    $('.canvas-mask').append(canvasTag);
                    if(!$.support.leadingWhitespace){
                        var canvas = window.G_vmlCanvasManager.initElement($('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k + i]);
                    }
                    
                    var canvas = $('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k  + i].getContext('2d');
                    if(width > 3){
                        // 当连接线是斜线时
                        // width = width - 20;
                        $($('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k  + i]).css({
                            'position': 'absolute',
                            'left': left1 + 'px',
                            'top': top1 + 'px'
                        }).attr({
                            'width': width,
                            'height': height
                        })
                        canvas.beginPath();
                        canvas.moveTo(6,6*height/width);//第一个起点
                        canvas.lineTo(width-6,height-6*height/width);//第二个点
                    }else if(width < 0){
                        // 当连接线是反向斜线时
                        width = -width
                        $($('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k  + i]).css({
                            'position': 'absolute',
                            'left': (left1 - width) + 'px',
                            'top': top1 + 'px'
                        }).attr({
                            'width': width,
                            'height': height
                        })
                        canvas.beginPath();
                        canvas.moveTo(width - 6, 6*height/width);//第一个起点
                        canvas.lineTo(6,height-6*height/width);//第二个点
                    }else {
                        // 当连接线是垂直线时
                        // height = height - 18;
                        $($('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k  + i]).css({
                            'position': 'absolute',
                            'left': left1 + 'px',
                            'top': top1 + 'px'
                        }).attr({
                            'width': width + 2,
                            'height': height
                        })
                        canvas.beginPath();
                        canvas.moveTo(0,6);//第一个起点
                        canvas.lineTo(0,height-6);//第二个点
                    }
                    
                    // 画线
                    canvas.lineWidth = 1;
                    canvas.strokeStyle = '#e82828';
                    canvas.stroke();
                }
            }
        }
    }
}

var getRule = function(lotteryId, playType, state) {
	var index = parseInt(playType.replace(/(\D+)/ig, ''), 10);
    switch (playType) {
    	case 'q1':
        case 'rx2':
        case 'rx3':
        case 'rx4':
        case 'rx5':
        case 'rx6':
        case 'rx7':
        case 'rx8':
        case 'qzu2':
        case 'qzu3':
        	if (state[0] == '1') return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">'+index+'</span>个号码';
        	break;
        case 'rx2dt':
        case 'qzu2dt':
        	for (i in state) {
        		switch (state[i]) {
        			case 1:
            		case 2:
                	case 3:
                	case '5':
                		return '<i class="icon-font">&#xe611;</i>请选择1个胆码，2~10个拖码，胆码＋拖码<span class="num-red">≥3</span>个';
                		break;
        		}
        	}
        	break;
        case 'rx3dt':
        case 'rx4dt':
        case 'rx5dt':
        case 'rx6dt':
        case 'rx7dt':
        case 'qzu3dt':
        	for (i in state) {
        		switch (state[i]) {
        			case 1:
            		case 2:
                	case 3:
                	case '5':
                		return '<i class="icon-font">&#xe611;</i>请选择1~'+(index-1)+'个胆码，2~10个拖码，胆码＋拖码≥<span class="num-red">'+(index+1)+'</span>个';
                		break;
        		}
        	}
        	break;
        case 'qzhi2':
        	if($.inArray('01', state) > -1 || $.inArray('10', state) > -1 || $.inArray('11', state) > -1) return '<i class="icon-font">&#xe611;</i>每位至少选择1个号码，且相互不重复';
            break;
        case 'qzhi3':
        	for (i in state) {
        		switch (state[i]) {
            		case '001':
                	case '010':
                	case '011':
                	case '100':
                	case '101':
                	case '110':
                	case '111':
                		return '<i class="icon-font">&#xe611;</i>每位至少选择1个号码，且相互不重复';
                		break;
        		}
        	}
            break;
        case 'lexuan3':
        	for (i in state) {
        		switch (state[i]) {
            		case '001':
                	case '010':
                	case '011':
                	case '100':
                	case '101':
                	case '110':
                	case '111':
                		return '<i class="icon-font">&#xe611;</i>每位选择1个号码，且相互不重复';
                		break;
        		}
        	}
            break;
        case 'lexuan4':
        case 'lexuan5':
        	if (state[0] == '1') return '<i class="icon-font">&#xe611;</i>请选择<span class="num-red">'+index+'</span>个号码';
        	break;
        default:
        break;
    }
    return true;
}

function renderTime() {
	if (cx._basket_.playType.indexOf('dt') > -1) {
    	$("."+cx._basket_.playType.replace(/dt/, '')+" .dt .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    }else if (cx._basket_.playType.indexOf('lexuan') > -1) {
    	$("."+cx._basket_.playType+" .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    }else {
    	$("."+cx._basket_.playType+" .default .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    }
}

function fmoney(s) 
{   
	s = s.toString().split("").reverse().join("").substring(0, s.toString().length);
	return s.replace(/(\d{3})/g, '$1,').split("").reverse().join("").replace(',', '');
} 

cx.splitlexuanBalls = function(balls) {
	var index = parseInt(balls.playType.replace(/\D/g, ''), 10), result = {}, autoId = 0;
	if (index == 3) {
		$.each(balls.balls[0]['tuo'], function(i0, v0){
			$.each(balls.balls[1]['tuo'], function(i1, v1){
				$.each(balls.balls[2]['tuo'], function(i2, v2){
					result[autoId] = {
						balls : [{'tuo':[v0]},{'tuo':[v1]},{'tuo':[v2]}],
						betNum : 1,
						betMoney : 6,
						playType : balls.playType
					};
					autoId++;
				})
			})
		})
	}else {
		var tuo = balls.balls[0]['tuo'];
		if (index == 4) {
			for (a = 0; a < tuo.length; a++) {
				for (b = a+1; b < tuo.length; b++) {
					for (c = b+1; c < tuo.length; c++) {
						for (d = c+1; d < tuo.length; d++) {
							result[autoId] = {
								balls : [{'tuo':[tuo[a], tuo[b], tuo[c], tuo[d]]}],
								betNum : 1,
								betMoney : 10,
								playType : balls.playType
							};
							autoId++;
						}
					}
				}
			}
		}else {
			for (a = 0; a < tuo.length; a++) {
				for (b = a+1; b < tuo.length; b++) {
					for (c = b+1; c < tuo.length; c++) {
						for (d = c+1; d < tuo.length; d++) {
							for (e = d+1; e < tuo.length; e++) {
								result[autoId] = {
									balls : [{'tuo':[tuo[a], tuo[b], tuo[c], tuo[d], tuo[e]]}],
									betNum : 1,
									betMoney : 14,
									playType : balls.playType
								};
								autoId++;
							}
							
						}
					}
				}
			}
		}
	}
	return result;
}

cx.caculateBonus = function($el, playType, betMoney, balls) {
	var index = parseInt(playType.replace(/(\D+)/ig, ''), 10);
	switch (playType) {
		case 'rx2':
		case 'rx3':
		case 'rx4':
		case 'rx5':
			var money = jiangjin[playType], num = balls[0].balls.length, smalljj = cx.Math.combine((num > 6 + index ? num - 6 : index), index) * money,
			bigjj = cx.Math.combine(num > 5 ? 5 : num, index) * money, smallyl = smalljj-betMoney, bigyl = bigjj-betMoney;
			console.log(bigyl);
			break;
		case 'rx2dt':
		case 'rx3dt':
		case 'rx4dt':
		case 'rx5dt':
			var money = jiangjin[playType], num = balls[0].balls.length, dnum = balls[0].dans.length, smalljj = cx.Math.combine(num -6 > index - dnum ? num-6:index - dnum, index - dnum) * money, 
			bigjj = cx.Math.combine(5 - dnum > num ? num : 5-dnum, index-dnum) * money, smallyl = smalljj-betMoney, bigyl = bigjj-betMoney;
			break;
		case 'rx6':
		case 'rx7':
		case 'rx8':
			var money = jiangjin[playType], num = balls[0].balls.length, smalljj = bigjj = cx.Math.combine(num-5, index-5) * money, smallyl = bigyl = bigjj-betMoney;
			break;
		case 'rx6dt':
		case 'rx7dt':
			var money = jiangjin[playType], num = balls[0].balls.length, dnum = balls[0].dans.length, bigjj = dnum < 5 ? cx.Math.combine(num-5+dnum, index - 5) * money : cx.Math.combine(num, index - dnum) * money, 
			smalljj = dnum < index - 5 ? cx.Math.combine(num-5, index - dnum - 5) * money : money, smallyl = smalljj-betMoney, bigyl = bigjj-betMoney;
			break;
		case 'q1':
		case 'qzu2':
		case 'qzu3':
		case 'qzu2dt':
		case 'qzu3dt':
		case 'qzhi2':
		case 'qzhi3':
			var money = jiangjin[playType], smalljj = bigjj = money, smallyl = bigyl = money-betMoney;
			break;
		case 'lexuan3':
			var onenum = 0, moreNum = betMoney/6 - 1;
			$.each(balls, function(i, ball){
				if (ball.balls.length == 1) onenum++;
			})
			var smalljj = 19, bigjj = onenum <= 1 ? 1441 : 1384 + (moreNum > 2 ? 2 : moreNum) * 19, smallyl = smalljj-betMoney, bigyl = bigjj-betMoney;
			break;
		case 'lexuan4':
			var num = balls[0].balls.length, smalljj = (num < 10 ? (num-3) * 19 : cx.Math.combine(5, 15-num) * 154 + cx.Math.combine(num-6, 3) * 114),
			bigjj = (num == 4 ? 154 : 770 + 190 * (num - 5)), smallyl = smalljj-betMoney, bigyl = bigjj-betMoney;
			break;
		case 'lexuan5':
			var num = balls[0].balls.length, smalljj = num <= 10 ? (num-4) * 90 : (1080 + 450 * (num - 5)), bigjj = 1080 + 450 * (num - 5), smallyl = smalljj-betMoney, bigyl = bigjj-betMoney;
			break;
	}
	str = '（如中奖，奖金 <span class="main-color-s">'
	if (smalljj == bigjj) {
		str += smalljj;
	} else {
		str += smalljj+'</span>~<span class="main-color-s">'+bigjj;
	}
	str += '</span> 元，';
	if (smallyl == bigyl) {
		if (smallyl >= 0) {
			str += '盈利 <span class="main-color-s">'+smallyl;
		} else {
			str += '盈利 <span class="green-color">'+smallyl;
		}
	} else {
		if (smallyl >= 0) {
			str += '盈利 <span class="main-color-s">'+smallyl;
		} else {
			str += '盈利 <span class="green-color">'+smallyl;
		}
		if (bigyl >= 0) {
			str += '</span>~<span class="main-color-s">'+bigyl;
		} else {
			str += '</span>~<span class="green-color">'+bigyl;
		}
	}
	str += '</span> 元）';
	$el.find(".sub-txt1").html(str).show();
}