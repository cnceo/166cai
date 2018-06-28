var cy = 1000, rest = tm, typeArr = ['', '任选一', '任选二', '任选三', '任选四', '任选五', '任选六', '同花', '同花顺', '顺子', '豹子', '对子'], playtype = 'th', j = 0, chase = {}, lotteryId = 54, multiModifier, chaseModifier, hstyopen = 0, inteval, numArr = [];
numArr[0] = ['', 'A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
numArr['th'] = ['同花包选', '黑桃', '红桃', '梅花', '方块'];
numArr['ths'] = ['同花顺包选', '黑桃', '红桃', '梅花', '方块'];
numArr['sz'] = ['顺子包选', 'A23', '234', '345', '456', '567', '678', '789', '8910', '910J', '10JQ', 'JQK', 'QKA'];
numArr['bz'] = ['豹子包选', 'AAA', '222', '333', '444', '555', '666', '777', '888', '999', '101010', 'JJJ', 'QQQ', 'KKK'];
numArr['dz'] = ['对子包选', 'AA', '22', '33', '44', '55', '66', '77', '88', '99', '1010', 'JJ', 'QQ', 'KK'];
enArr = ['', 'rx1', 'rx2', 'rx3', 'rx4', 'rx5', 'rx6', 'th', 'ths', 'sz', 'bz', 'dz'];

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

$(function(){
	inteval = setInterval("countdown()", cy);
	multiModifier = new cx.AdderSubtractor('.multi-modifier-s.multi');
	zhModifier = new cx.AdderSubtractor('.multi-modifier-s.chase');
	var boxes = {};
	boxes['th'] = new cx.BoxCollection('.php_th .btn-group', {lotteryId: cx.Lottery.KLPK});
	boxes['th'].add(new cx.BallBox('.php_th .pre-box', {lotteryId:cx.Lottery.KLPK, amount:5, min:1, minBall:0, playType:'th'}));
	boxes['sz'] = new cx.BoxCollection('.php_sz .btn-group', {lotteryId: cx.Lottery.KLPK});
	boxes['sz'].add(new cx.BallBox('.php_sz .pre-box', {lotteryId:cx.Lottery.KLPK, amount:13, min:1, minBall:0, playType:'sz'}));
	boxes['ths'] = new cx.BoxCollection('.php_ths .btn-group', {lotteryId: cx.Lottery.KLPK});
	boxes['ths'].add(new cx.BallBox('.php_ths .pre-box', {lotteryId:cx.Lottery.KLPK, amount:5, min:1, minBall:0, playType:'ths'}));
	boxes['dz'] = new cx.BoxCollection('.php_dz .btn-group', {lotteryId: cx.Lottery.KLPK});
	boxes['dz'].add(new cx.BallBox('.php_dz .pre-box', {lotteryId:cx.Lottery.KLPK, amount:14, min:1, minBall:0, playType:'dz'}));
	boxes['bz'] = new cx.BoxCollection('.php_bz .btn-group', {lotteryId: cx.Lottery.KLPK});
	boxes['bz'].add(new cx.BallBox('.php_bz .pre-box', {lotteryId:cx.Lottery.KLPK, amount:14, min:1, minBall:0, playType:'bz'}));
	boxes['rx1'] = new cx.BoxCollection('.php_rx1 .btn-group', {lotteryId: cx.Lottery.KLPK});
	boxes['rx1'].add(new cx.BallBox('.php_rx1 .pre-box', {lotteryId:cx.Lottery.KLPK, amount:13, min:1, minBall:1, playType:'rx1'}));
	
	for (var j = 2; j <= 6; j++){
		boxes['rx'+j] = new cx.BoxCollection('.php_rx'+j+' .default .btn-group', {lotteryId: cx.Lottery.KLPK});
		boxes['rx'+j].add(new cx.BallBox('.php_rx'+j+' .default .pre-box', {lotteryId:cx.Lottery.KLPK, amount:13, min:j, minBall:1, playType:'rx'+j}));
    	boxes['rx'+j+'dt'] =  new cx.BoxCollection('.php_rx'+j+' .bet-dt-klpk .btn-group',{lotteryId: cx.Lottery.KLPK});
	    for (i = 1; i <= 1; ++i) {
	        boxes['rx'+j+'dt'].add(new cx.BallBox('.php_rx'+j+' .bet-dt-klpk .pick-area:last', {
	        	lotteryId: cx.Lottery.KLPK,
	            amount: 13,
	            min: j,
	            minBall: 1,
	            mutex: 1,
	            hasdan: true,
	            dmax: j-1,
	            tmin: 2,
	            dtmin: j+1,
	            playType: 'rx'+j+'dt'
	        },'.php_rx'+j+' .bet-dt-klpk .pick-area:first'), i-1);
	    }
	}
	cx.CastBasket.prototype.renderString = function(allBalls, index, hover, noedit){
		$(".count-matches").html(this.betNum);
		var pid = cx.Lottery.playTypes[54][allBalls.playType];
		allBalls.balls[0]['tuo'].sort(function(a, b){
			a = parseInt(a, 10);
			b = parseInt(b, 10);
			return a > b ? 1 : ( a < b ? -1 : 0 );
		});
		if (allBalls.balls[0]['dan']) {
			allBalls.balls[0]['dan'].sort(function(a, b){
				a = parseInt(a, 10);
				b = parseInt(b, 10);
				return a > b ? 1 : ( a < b ? -1 : 0 );
			});
			var typeName = cx.Lottery.getPlayTypeName(54, (pid.length == 1) ? pid+'2' : pid);
		}else {
			var typeName = typeArr[pid];
		}
		var str = "<tr data-index='"+index+"'><td class='tal'>"+typeName+"</td><td class='tal'>";
		if (allBalls.balls[0]['dan']) {
			str += "<span class='klpk-ball'>（</span>";
			for (i in allBalls.balls[0]['dan']) {
				if ($.inArray(allBalls.playType, ['th', 'ths', 'sz', 'bz', 'dz']) > -1) {
					var ball = numArr[allBalls.playType][allBalls.balls[0]['dan'][i]];
				}else {
					var ball = numArr[0][allBalls.balls[0]['dan'][i]];
				}
				str += "<span class='klpk-ball'>"+ball+"</span>";
			}
			str += "<span class='klpk-ball'>）</span>";
		}
		for (i in allBalls.balls[0]['tuo']) {
			if ($.inArray(allBalls.playType, ['th', 'ths', 'sz', 'bz', 'dz']) > -1) {
				var ball = numArr[allBalls.playType][allBalls.balls[0]['tuo'][i]];
			}else {
				var ball = numArr[0][allBalls.balls[0]['tuo'][i]];
			}
			str += "<span class='klpk-ball'>"+ball+"</span>";
		}
		str += "</td><td class='fcw'>"+(allBalls.betNum * 2)+"元</td><td><span><a class='del-match' href='javascript:;'>×</a></span></td></tr>";
		return str;
	};
	cx.CastBasket.prototype.setChaseByIssue = function(num, multi) {
    	var tbstr = '', j = 0, issue = [];
    	this.chaseMulti = 0;
    	this.chaseMoney = 0;
    	this.chaseLength = num;
    	this.chases = {};
    	
    	if (num > 0) {
    		for (i in chases) {
    			if (j < num) {
    				issue.push(i);
    				this.setChaseByI(i);
    				this.chases[i].multi = multi;
    				this.chases[i].money = multi * this.betMoney;
    				this.chaseMulti += multi;
            		j++;
    			}else {
    				break;
    			}
    		}
    		this.chaseMoney = this.chaseMulti * this.betMoney;
    	}
    },
    cx.CastBasket.prototype.renderBetMoney = function() {
        if (cx._basket_.orderType == 1) {
        	$(".numbox .bet-num").html(this.betMoney * this.multiModifier.getValue() * this.chaseLength);
        }else {
        	$(".numbox .bet-num").html(this.betMoney * this.multiModifier.getValue());
        }
    }
	$('.seleFiveBoxScroll').on('click', '.del-match', function(){
		var $tr = $(this).closest('tr');
        var index = $tr.attr('data-index');
        for (i in cx._basket_.boxes) {
        	if(index === cx._basket_.boxes[i].edit) {
        		cx._basket_.boxes[i].clearButton(i);
        		cx._basket_.boxes[i].edit = 0;
            }
        }
        $tr.remove();
        cx._basket_.remove($tr.data('index'));
        $(".count-matches").html(cx._basket_.betNum);
        if (cx._basket_.betNum == 0) {
			$('.seleFiveTit').removeClass('seleFiveTit2');
            $('.seleFiveBox').hide();
		}
	})
	$('body').on('click', '.clear-matches', function() {
		cx.Confirm({
    		single: '您确定要删除已选择的选项吗？',
            btns: [{type: 'confirm',txt: '确定',},{type: 'cancel',txt: '取消'}],
            confirmCb: function () {
            	cx._basket_.removeAll();
                for (i in self.boxes) {
                	cx._basket_.boxes[i].clearButton(i);
                }
                $(".count-matches").html('0');
                $('.seleFiveTit').removeClass('seleFiveTit2');
                $('.seleFiveBox').hide();
            }
        });
    });
	var basket = new cx.CastBasket('.cast-panel', {
        lotteryId: cx.Lottery.KLPK,
        issue: ISSUE,
        boxes: boxes,
        tab: 'bet-type-link li',
        tabClass: 'selected',
        playType: 'dz',
        chases: chase,
        setStatus: 1,
        chaseLength: chaselength,
        multiModifier: multiModifier,
        zhModifier: zhModifier,
        $castList:$(".selected-matches tbody"),
        getCastOptions:'getCastOptions1'
    });
	
    cx._basket_ = basket;
    
    zhModifier.setCb(function() {
    	cx._basket_.setChaseByIssue(parseInt(this.getValue(), 10), cx._basket_.multiModifier.value);
    	cx._basket_.renderBetMoney();
    });
    
	$(".lottery-info-time span strong").html(ISSUE.substring(6, 8));
	renderTime();
	
	// 底部操作条悬停
    var eleFixedBox = $('.ele-fixed-box');
    var $castPanel = eleFixedBox.find('.cast-panel');
    var castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
    var onScroll = function () {
        var scrollTop = $(document).scrollTop() + $(window).height();
        
        if (scrollTop >= castPanelTop) {
            $castPanel.removeClass('cast-panel-fixed');
            if(!-[1,]&&!window.XMLHttpRequest){
               $castPanel.css({'position': 'static'}); 
            }
        }else {
            $castPanel.addClass('cast-panel-fixed');
            if(!-[1,]&&!window.XMLHttpRequest){
               $castPanel.css({
                    'position': 'absolute',
                    'bottom': 'auto',
                    'top': scrollTop-$castPanel.height() + 'px'
                }); 
            }
        }
    }
    $('.bet-type-link').on('click', 'li', function(){
    	cx._basket_.setType($(this).data('type'));
        castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
        onScroll();
    })
    
    $(".seleFiveTit").click(function () {
		if (cx._basket_.betNum > 0) {
			$(this).toggleClass("seleFiveTit2").next("div.seleFiveBox").toggle();
		}
	});
    
    $(".gg-type").click(function(){
		$("a[data-ordertype]").toggleClass('selected');
		$("a[data-ordertype]").each(function(){
			if ($(this).hasClass('selected')) {
				cx._basket_.orderType = $(this).data('ordertype');
			}
		})
		if (cx._basket_.orderType == 1) {
			$(".chase-div, .klpk-qr").show();
		}else {
			$(".chase-div, .klpk-qr").hide();
		}
		cx._basket_.renderBetMoney();
	});
    
    var Throttle;
    $(window).scroll(function () {
        onScroll();
    });
    $(window).resize(function(){
        clearTimeout(Throttle);
        Throttle = setTimeout(function(){
            onScroll();
        }, 100)
    });
    onScroll();
	
	$('.table-zs-hd').on('click', 'li', function(){
		if ($(this).hasClass('myorder') && ($(this).hasClass('not-login') || !$.cookie('name_ie'))) {
	        cx.PopAjax.login(1);
	        return ;
		}
		
        $(this).toggleClass('selected').siblings().removeClass('selected');
        $(this).parents('.table-zs').find('.table-zs-item').eq($(this).index()).toggle().siblings().hide();
        
        if ($(this).hasClass('selected') && $(this).hasClass('myorder')) {
        	$.ajax({
    			url: "/ajax/getOrders/klpk",
    			dataType: 'json',
    			success: function(data) {
    				var str = '';
    				if (data.length > 0) {
    					for (i in data) {
    						str += '<tr><td>'+data[i].created+'</td><td>'+data[i].issue+'</td><td class="tal" title="';
    						var codes = data[i].codes.split(';'),carr = [], carr0 = [], nArr;
    						for (j in codes) {
    							carr = codes[j].split(':');
    							nArr = ($.inArray(carr[1], ['7', '8', '9', '10', '11']) > -1) ? numArr[enArr[carr[1]]] : numArr[0];
    							str += cx.Lottery.getPlayTypeName(54, carr[1])+':';
    							carr0 = carr[0].split('$');
    							if (carr0.length == 1) {
    								for (k in carr0[0].split(',')) {
    									str += nArr[parseInt(carr0[0].split(',')[k], 10)]+" ";
    								}
    							}else {
    								str += '（';
    								for (k in carr0[0].split(',')) {
    									str += nArr[parseInt(carr0[0].split(',')[k], 10)]+" ";
    								}
    								str = str.slice(0, -1)+'）';
    								for (k in carr0[1].split(',')) {
    									str += nArr[parseInt(carr0[1].split(',')[k], 10)]+" ";
    								}
    							}
    							str = str.slice(0, -1)+'; ';
    						}
    						
    						carr = codes[0].split(':');
    						nArr = ($.inArray(carr[1], ['7', '8', '9', '10', '11']) > -1) ? numArr[enArr[carr[1]]] : numArr[0];
    						str += '"><div class="text-overflow">'+cx.Lottery.getPlayTypeName(54, carr[1])+' <span class="specil-color">';
    						carr0 = carr[0].split('$');
							if (carr0.length == 1) {
								for (k in carr0[0].split(',')) {
									str += nArr[parseInt(carr0[0].split(',')[k], 10)]+" ";
								}
							}else {
								str += '（';
								for (k in carr0[0].split(',')) {
									str += nArr[parseInt(carr0[0].split(',')[k], 10)]+" ";
								}
								str = str.slice(0, -1)+'）';
								for (k in carr0[1].split(',')) {
									str += nArr[parseInt(carr0[1].split(',')[k], 10)]+" ";
								}
							}
    						if (j > 0) {
    							str += '...';
    						}
    						str += '</div></td><td><span class="fcs">'+fmoney(parseInt(data[i].money, 10) / 100, 3)+'.00</span></td>';
    						str += '<td><span class="fcs">'+cx.Order.getStatus(data[i].status, 3)+'</span></td>';
    						if (data[i].margin > 0) {
    							str += '<td><img src="/caipiaoimg/v1.1/img/gold.png" alt="">&nbsp;<strong class="spec arial">'+fmoney(data[i].margin / 100, 3)+'.00</strong></td>';
    						}else if($.inArray(data[i].status, ['1000', '2000']) === -1) {
    							str += '<td>--</td>'
    						}else {
    							str += '<td>'+fmoney(data[i].margin / 100, 3)+'.00</td>';
    						}
    						str += '<td><a target="_blank" href="/orders/detail/'+data[i].orderId+'">查看详情</a>';
    						if (data[i].ljzf == 1) {
    							str += '<a href="javascript:cx.castCb({orderId:\''+data[i].orderId+'\'}, {ctype:\'paysearch\', orderType:0});"><span class="num-red">立即支付</span></a>';
    						}else {
    							str += '<a target="_blank" href="/klpk?orderId='+data[i].orderId+'">继续预约</a></td><td></td></tr>';
    						}
    					}
    				} else {
    					str += '<tr><td colspan="8" style="height: 100px;">亲，您三个月内还没有订单哦！</td></tr>'
    				}
    				$(".my-order tbody").html(str);
    				castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
    	            onScroll();
    			}
    		})
        }else {
        	if ($(this).hasClass('selected')) {
    			var playType = $(this).find('a:first').data('playtype');
    			hstyopen = 1;
    			$.ajax({
    				url: '/ajax/getHistory/klpk',
    				dataType: 'json',
    				beforeSend: function() {
    					$(".ykj-info tbody").html("<tr style='height:250px'><td colspan='22'><div class='pop-loading'><img src='/caipiaoimg/v1.1/img/pop-loading.gif' width='28' height='28' alt=''></div>拼命加载中，请稍等...</td></tr>");
    				},
    				success: function(data) {
    					hsty = eval(data.hsty);//重置历史数据全局变量
    				    mall = eval(data.miss);//重置遗漏数据全局变量
    				    rfshhisty(playType);
    				    castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
        	            onScroll();
    				}
    			})
    		}else {
    			hstyopen = 0;
    		}
        }
    })
	
	$(".bet-klpk-ft dt").click(function(){
		var iss, str = '';
		if (!$(this).hasClass('active')) {
			$.ajax({
				url: '/ajax/getKj/klpk',
				dataType: 'json',
				success: function(data) {
					str = '';
					for (i = 1; i <= 22; i++) {
						iss = padd(i);
						str += "<tr><th>"+iss+"</th><td>";
						if (data[iss] && data[iss].award) {
							for (j = 0; j < 3; j++) {
								str += "<span class='klpk-num-"+data[iss].award.split('|')[1].split(',')[j].toLowerCase()+"'>"+numArr[0][parseInt(data[iss].award.split('|')[0].split(',')[j], 10)]+"</span>";
								data[iss].award.split('|')
							}
							str += "</td><td>"+data[iss].type+"</td></tr>";
						}else {
							str += "</td><td></td><td></td></tr>"
						}
						$(".klpk-table-kj tbody:first").html(str);
					}
					str = '';
					for (i = 23; i <= 44; i++) {
						str += "<tr><th>"+i+"</th><td>";
						if (data[i] && data[i].award) {
							for (j = 0; j < 3; j++) {
								str += "<span class='klpk-num-"+data[i].award.split('|')[1].split(',')[j].toLowerCase()+"'>"+numArr[0][parseInt(data[i].award.split('|')[0].split(',')[j], 10)]+"</span>";
								data[i].award.split('|')
							}
							str += "</td><td>"+data[i].type+"</td></tr>";
						}else {
							str += "</td><td></td><td></td></tr>"
						}
						$(".klpk-table-kj tbody:eq(1)").html(str);
					}
					str = '';
					for (i = 45; i <= 66; i++) {
						str += "<tr><th>"+i+"</th><td>";
						if (data[i] && data[i].award) {
							for (j = 0; j < 3; j++) {
								str += "<span class='klpk-num-"+data[i].award.split('|')[1].split(',')[j].toLowerCase()+"'>"+numArr[0][parseInt(data[i].award.split('|')[0].split(',')[j], 10)]+"</span>";
								data[i].award.split('|')
							}
							str += "</td><td>"+data[i].type+"</td></tr>";
						}else {
							str += "</td><td></td><td></td></tr>"
						}
						$(".klpk-table-kj tbody:eq(2)").html(str);
					}
					str = '';
					for (i = 67; i <= 88; i++) {
						if (i <= 88) {
							str += "<tr><th>"+i+"</th><td>";
							if (data[i] && data[i].award) {
								for (j = 0; j < 3; j++) {
									str += "<span class='klpk-num-"+data[i].award.split('|')[1].split(',')[j].toLowerCase()+"'>"+numArr[0][parseInt(data[i].award.split('|')[0].split(',')[j], 10)]+"</span>";
									data[i].award.split('|')
								}
								str += "</td><td>"+data[i].type+"</td></tr>";
							}else {
								str += "</td><td></td><td></td></tr>"
							}
						}else {
							str += '<tr><th></th><td></td><td></td><td></td></tr>';
						}
						$(".klpk-table-kj tbody:last").html(str);
					}
				}
			})
		}
	})
})

function renderTime() {
	$(".time").html(maketstr(tm));
	if (atm > 0) {
		$("body").find(".atime").html(maketstr(atm));
	}else {
		$("body").find(".atime").parents('td').html('正在开奖中...');
	}
	if (atm > 0 && atm < 100000) {
		$(".kj-ing").show().html(maketstr(atm)+"后开奖");
		$(".kj-area ul li:last").html('形态：--');
		$(".kj-num").hide();
	}else if(vJson.length == 5) {
		$(".kj-num").show().html('<span>开</span><span>奖</span><span>中</span>');
		$(".kj-ing").hide();
		$(".kj-area ul li:last").html('形态：--');
	}else {
		var str = '', xt = '散牌', awdArr = [parseInt(vJson[0], 10), parseInt(vJson[1], 10), parseInt(vJson[2], 10)].sort();
		for (var i = 0; i < 3; i++) {
			str += "<span class='kj-num-"+vJson[i+3].toLowerCase()+"' style='position: relative'>"+numArr[0][parseInt(vJson[i], 10)]+"</span>";
			if (i == 0) {
				str += '<span>开</span>';
			}else if (i == 1) {
				str += '<span>奖</span>';
			}else {
				str += '<span>中</span>';
			}
		}
		if (vJson[3] == vJson[4] && vJson[4] == vJson[5]) xt = '同花';
		if (vJson[0] == vJson[1] && vJson[1] == vJson[2]) {
			xt = '豹子';
		}else if (vJson[0] == vJson[1] || vJson[1] == vJson[2] || vJson[2] == vJson[3]) {
			xt = '对子';
		}else if ((parseInt(vJson[0], 10) + 1 == parseInt(vJson[1], 10) && parseInt(vJson[1], 10) + 1 == parseInt(vJson[2], 10))
				|| (awdArr[0] == 1 && awdArr[1] == 12 && awdArr[2] == 13)) {
			if (xt === '同花') {
				xt = '同花顺';
			}else {
				xt = '顺子';
			}
		}
		$(".kj-area ul li:last").html("形态:<strong>"+xt+"</strong>");
		$(".kj-num").show().html(str);
		$(".kj-ing").hide();
	}
}

function render(data) {
	$(".lottery-info-time span strong").html(data.issue.substring(6, 8));
	$(".kj-periods em").html(data.prev.substring(6, 8));
	$(".arrow-tag").html("剩"+data.rest+"期<i></i>");
	if (hstyopen) {
		rfshhisty(cx._basket_.playType);
	}
}

var rfshhisty = function(type) {
	var str = '', ms, k, m;
	for (i in hsty) {
		str += '<tr';
		var awardArr = [];
		if (hsty[i].awardNum) {
			str += "><td>"+hsty[i].issue+"</td><td class='klpk-ball'>";
			awardArr = hsty[i].awardNum.split('|');
			for (j in awardArr[0].split(',')) {
				str += "<span class='klpk-num-"+awardArr[1].split(',')[j].toLowerCase()+"'>"+numArr[0][parseInt(awardArr[0].split(',')[j], 10)]+"</span>"
			}
			str += "</td><td><span>"+hsty[i].type+"</span></td>";
			if (mall[i]) {
				ms = mall[i][0].split('|')[0].split(',');
				for (j in ms) {
					k = padd(parseInt(j, 10)+1);
					if ($.inArray(k, awardArr[0].split(',')) > -1) {
						m = 0;
						for (l in awardArr[0].split(',')) {
							if (k == awardArr[0].split(',')[l]) {
								m++;
							}
						}
						str += "<td class='klpk-red'><span class='selected selected"+m+"'>"+numArr[0][parseInt(j, 10)+1]+"<i></i></span></td>";
					}else {
						str += "<td class='klpk-red'><span>"+ms[j]+"<i></i></span></td>";
					}
					
				}
				str += "</tr>";
			}else {
				str += "<td colspan='20'></td>"
			}
		}else if (hsty[i].prev && atm > 0) {
			str += "><td>"+hsty[i].issue+"</td><td colspan='21'><em class='main-color atime'>"+maketstr(atm)+"</em>后开奖...</td>";
		}else if (hsty[i].prev) {
			str += "><td>"+hsty[i].issue+"</td><td colspan='21'>正在开奖中...</td>";
		}else {
			str += "><td>"+hsty[i].issue+"</td><td colspan='21'></td>";
		}
	}
	$(".php_"+type+" .ykj-info table tbody").html(str);
	
}

function fmoney(s) 
{   
	s = s.toString().split("").reverse().join("").substring(0, s.toString().length);
	return s.replace(/(\d{3})/g, '$1,').split("").reverse().join("").replace(',', '');
} 
