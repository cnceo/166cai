var cy = 1000, rest = tm, typeArr = ['三同号', '三不同', '三连号', '二同复', '二同单', '二不同'], playtype = 'hz', j = 0, chase = {}, lotteryId = 53, multiModifier, chaseModifier, hstyopen = 0, inteval;

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
	chaseModifier = new cx.AdderSubtractor('.multi-modifier-s.chase');
	var ballCollection = function(options) {
		this.balls = [];
		this.strings = {};
		this.lotteryId = 53;
		this.issue = options.issue || 0;
		this.multiModifier = options.multiModifier;
		this.chaseModifier = options.chaseModifier;
		this.betNum = 0;
		this._betMoney = 2;
		this.betMoney = 0;
		this.orderType = 0;
		this.chases = chase;
		this.chaseLength = chaselength;
		this.setStatus = 1;
		this.multi = 1;
		this.playType = 'hz';
		this.playTypeId = 0;
		this.init();
	}
	
	ballCollection.prototype = {
		init:function() {
			var self = this;
			$("#pd_ks_buy").click(function(){
				if ($(this).hasClass('not-login') || !$.cookie('name_ie')) {
	            	cx.PopAjax.login(1);
	                return ;
	            }

	            if ($(this).hasClass('not-bind')) {
	                return ;
	            }
				
	            var data = self.getCastOptions();
	            data.isToken = 1;
	            if (data.betTnum == 0) {
					new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>至少选择<span class='num-red'>１</span>注号码才能投注，请先选择方案"});
	                return ;
	            }
	                        
	            if (data.orderType == 1 && data.totalIssue <= 1) {
	            	cx.Alert({content: "<i class='icon-font'>&#xe611;</i>您好，追号玩法须至少选择<span class='num-red'> 2 </span>期"});
	            	return ;
	            }
	            
            	if (data.orderType == 1) {
            		for (i in data.chases.split(';')) {
            			if (parseInt(data.chases.split(';')[i].split('|')[2], 10) > 200000) {
            				new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>20万</span>元，请修改订单后重新投注"});
                            return ;
            			}
            		}
            	}else {
            		if ( data.money >200000 ) {
                        new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>20万</span>元，请修改订单后重新投注"});
                        return ;
                    }
            	}

            	cx.castCb(data, {ctype:'create', lotteryId:self.lotteryId, orderType:self.orderType, betMoney:self._betMoney * self.betNum * self.multi, chaseLength:self.chaseLength, issue:self.issue});
			});
			$(".seleFiveTit").click(function () {
				if (ballcollection.betNum > 0) {
					$(this).toggleClass("seleFiveTit2").next("div.seleFiveBox").toggle();
				}
			});
			$(".gg-type").click(function(){
				$("a[data-ordertype]").toggleClass('selected');
				$("a[data-ordertype]").each(function(){
					if ($(this).hasClass('selected')) {
						self.orderType = $(this).data('ordertype');
					}
				})
				if (self.orderType == 1) {
					$(".chase-div").show();
					$(".k3-qr").show();
				}else {
					$(".chase-div").hide();
					$(".k3-qr").hide();
				}
				if (self.orderType == 1) {
	            	$(".numbox .bet-num").html(self.betNum * self._betMoney * self.multi * self.chaseLength);
	            }else {
	            	$(".numbox .bet-num").html(self.betNum * self._betMoney * self.multi);
	            }
			}),
			$(".setStatus").click(function(){
				if ($(this).attr('checked') == 'checked') {
            		self.setStatus = 1;
            	}else {
            		self.setStatus = 0;
            	}
			}),
			$(".k3-tab-hd li").click(function(){
				$('.table-zs-hd li').removeClass('selected');
				$('.table-zs-bd .table-zs-item').hide;
				$(".ykj-info").hide();
				self.playType = $(this).data('playtype');
			}),
			self.multiModifier.setCb(function() {
                self.multi = parseInt(this.getValue(), 10);
                if (self.chaseLength > 0) {
                	j = 0;
            		for (i in chases) {
            			if (j < self.chaseLength) {
            				self.chases[i] = {};
            				self.chases[i].award_time = chases[i].award_time;
            				self.chases[i].show_end_time = chases[i].show_end_time;
            				self.chases[i].multi = self.multi;
            				self.chases[i].money = self.multi * self._betMoney * self.betNum;
                    		j++;
            			}else {
            				break;
            			}
            		}
            	}
                if (self.orderType == 1) {
                	$(".numbox .bet-num").html(self.betNum * self._betMoney * self.multi * self.chaseLength);
                }else {
                	$(".numbox .bet-num").html(self.betNum * self._betMoney * self.multi);
                }
            });
			self.chaseModifier.setCb(function() {
                self.chaseLength = parseInt(this.getValue(), 10);
                self.chases = {};
                j = 0;
            	
            	if (self.chaseLength > 0) {
            		for (i in chases) {
            			if (j < self.chaseLength) {
            				self.chases[i] = {};
            				self.chases[i].award_time = chases[i].award_time;
            				self.chases[i].show_end_time = chases[i].show_end_time;
            				self.chases[i].multi = self.multi;
            				self.chases[i].money = self.multi * self._betMoney * self.betNum;
                    		j++;
            			}else {
            				break;
            			}
            		}
            	}
            	if (self.orderType == 1) {
                	$(".numbox .bet-num").html(self.betNum * self._betMoney * self.multi * self.chaseLength);
                }else {
                	$(".numbox .bet-num").html(self.betNum * self._betMoney * self.multi);
                }
            });
		},
		add: function(box, index) {
            this.balls.push(box);
            box.setCollection(this, index);
        },
        addAll: function(balls) {
        	for (i in balls) {
        		this.autoId += 1;
        		this.strings[this.autoId] = balls[i];
                this.$castList.prepend(this.renderString(balls[i].balls, this.autoId, balls[i].betNum, false, false, balls[i].playType));
                this.betNum += balls[i].betNum;
        	}
        	this.betMoney = this.betNum * this._betMoney;
            this.renderBet();
        },
        renderBet: function() {
        	var str = '', playType = 0, pNum = 0;
            for (i in this.balls) {
            	if (this.balls[i].balls.length > 0) {
            		pNum++;
            		if (playType == 0) {
            			playType = parseInt(i)+1;
            		} 
            		this.balls[i].balls.sort(function(a, b){
            			if (a.toString().indexOf(',') > -1) {
            				a = a.replace(/,/g, '');
            			}
            			if (b.toString().indexOf(',') > -1) {
            				b = b.replace(/,/g, '');
            			}
    					a = parseInt(a, 10);
    					b = parseInt(b, 10);
    					return a > b ? 1 : ( a < b ? -1 : 0 );
    				});
            		$(".bet-type-link-item"+(parseInt(i, 10)+1)+" .pick-area-note").show();
            		$(".bet-type-link-item"+(parseInt(i, 10)+1)+" .pick-area-explain").hide();
            		str += "<tr data-index='"+this.balls[i].index+"'><td class='tal'>"+cx.Lottery.getPlayTypeName(cx.Lottery.KS, this.balls[i].index)+"</td><td class='tal'>";
            		switch (this.balls[i].options.playType) {
            			case 'hz':
            			case 'sthdx':
            			case 'sbth':
            			case 'ethfx':
            			case 'ethdx':
            				for (j in this.balls[i].balls) {
                        		str += "<span class='k3-ball'>"+this.balls[i].balls[j].toString().replace(/,/g, '')+"</span>";
                        	}
            				break;
            			case 'sthtx':
            			case 'slhtx':
            				str += "<span class='k3-ball'>"+cx.Lottery.getPlayTypeName(cx.Lottery.KS, this.balls[i].index)+"</span>";
            				break;
            			case 'ebth':
            				for (j in this.balls[i].balls) {
                        		str += "<span class='k3-ball'>"+this.balls[i].balls[j].toString().replace(/,/g, '').replace(/\*/, '')+"</span>";
                        	}
            				break;
            		}
                	str += "</td><td class='fcw'>"+(this.balls[i].balls.length * 2)+"元</td><td><span><a class='del-match' href='javascript:;'>×</a></span></td></tr>";
                	if (this.balls[i].options.playType === 'hz') {
                		var min = Number.POSITIVE_INFINITY, max = 0, val;
                		for (j in this.balls[i].balls) {
                			val = parseInt($("li[data-num="+this.balls[i].balls[j]+"] s").html(), 10);
                			if (val < min) {
                				min = val;
                			}
                			if (val > max) {
                				max = val;
                			}
                		}
                	}else if (this.balls[i].options.playType === 'ebth') {
                		var max = 8, min = 8;
                		for (j in this.balls[i].balls) {
                			for (k = parseInt(j, 10)+1; k < this.balls[i].balls.length; k++) {
            					for (l = 0; l <= 1; l++){
            						for (m = 0; m <= 1; m++) {
            							if (this.balls[i].balls[j].split(',')[l] == this.balls[i].balls[k].split(',')[m]) {
            								if (max < 16) {
            									max = 16;
            								}
            								var a = this.balls[i].balls[j].replace(this.balls[i].balls[j].split(',')[l], '').replace(/,|\*/g, '');
            								var b = this.balls[i].balls[k].replace(this.balls[i].balls[j].split(',')[l], '').replace(/,|\*/g, '');
            								if ($.inArray(a+","+b+",*", this.balls[i].balls) > -1) {
            									max = 24;
            								}
            							}
            						}
            					}
                			}
                		}
                	}else {
                		var min = parseInt($(".bet-type-link li:eq("+i+") b").html(), 10), max = min;
                	}
                	if (max == min) {
            			$(".bet-type-link-item."+this.balls[i].options.playType+" .pick-area-note span").html("如中奖，奖金"+min+"元，盈利<em>"+(min - this.balls[i].balls.length * 2)+"元</em>")
            		}else {
            			$(".bet-type-link-item."+this.balls[i].options.playType+" .pick-area-note span").html("如中奖，奖金"+min+"~"+max+"元，盈利<em>"+(min - this.balls[i].balls.length * 2)+"~"+(max - this.balls[i].balls.length * 2)+"元</em>")
            		}
            	}else {
            		$(".bet-type-link-item"+(parseInt(i, 10)+1)+" .pick-area-explain").show();
            		$(".bet-type-link-item"+(parseInt(i, 10)+1)+" .pick-area-note").hide();
            	}
            }
            $(".selected-matches tbody").html(str);
            if (pNum == 1) {
        		this.playTypeId = playType;
        	}else {
        		this.playTypeId = 0;
        	}
        	this.resetStrings();
        	if (this.betNum == 0) {
        		$('.seleFiveTit').removeClass('seleFiveTit2');
                $('.seleFiveBox').hide();
        	}
        	$(".count-matches").html(this.betNum);
        	if (this.orderType == 1) {
            	$(".numbox .bet-num").html(this.betNum * this._betMoney * this.multi * this.chaseLength);
            }else {
            	$(".numbox .bet-num").html(this.betNum * this._betMoney * this.multi);
            }
        },
        setIssue: function(issue){
        	this.issue = issue
        },
        resetStrings: function() {
        	this.strings = {}, k = 0, this.betNum = 0;
        	for (i in this.balls) {
        		if (this.balls[i].options.playType !== 'hz') {
        			for (j in this.balls[i].balls) {
        				var balls = [];
        				balls['tuo'] = [this.balls[i].balls[j]];
        				this.strings[k] = {balls:[balls], betNum:1, playType: this.balls[i].options.playType};
        				k++;
        				this.betNum += 1;
        			}
        		}else if (this.balls[i].balls.length > 0) {
        			var balls = [];
        			balls['tuo'] = this.balls[i].balls;
    				this.strings[k] = {balls:[balls], betNum:balls.length, playType:this.balls[i].options.playType};
    				k++;
    				this.betNum += this.balls[i].balls.length;
        		}
        	}
        	this.betMoney = this.betNum * this._betMoney;
        	if (this.chaseLength > 0) {
        		j = 0;
        		for (i in chases) {
        			if (j < this.chaseLength) {
        				this.chases[i] = {};
        				this.chases[i].award_time = chases[i].award_time;
        				this.chases[i].show_end_time = chases[i].show_end_time;
        				this.chases[i].multi = this.multi;
        				this.chases[i].money = this.multi * this._betMoney * this.betNum;
                		j++;
        			}else {
        				delete this.chases[i];
        				break;
        			}
        		}
        	}
        },
        getCastOptions: function() {
            var self = this;
            var castStr = cx.Lottery.toCastString(self.lotteryId, self.strings);
            if (self.orderType == 1) {
            	var endTime = '';
            	for (i in self.chases) {
                	if (endTime === ''){
                		endTime = self.chases[i].show_end_time;
                	}else {
                		break;
                	}
                }
            	var data = {
                    ctype: 'create',
                    buyPlatform: 0,
                    codes: castStr,
                    lid: self.lotteryId,
                    money: self._betMoney * self.betNum * self.chaseLength * self.multi,
                    multi: self.multi * self.chaseLength,
                    playType: self.playTypeId,
                    setStatus: self.setStatus,
                    betTnum: self.betNum,
                    isChase: 0,
                    orderType: 1,
                    totalIssue: self.chaseLength,
                    chases: cx.Lottery.toChaseString(self.chases),
                    endTime: ENDTIME
                };
            }else {
            	var data = {
                    ctype: 'create',
                    buyPlatform: 0,
                    codes: castStr,
                    lid: self.lotteryId,
                    money: self._betMoney * self.betNum * self.multi,
                    multi: self.multi,
                    issue: self.issue,
                    playType: self.playTypeId,
                    betTnum: self.betNum,
                    isChase: 0,
                    orderType: 0,
                    endTime: ENDTIME
                };
            }
            return data;
        },
        getBoxes: function() {
            return [];
        },
        setChaseByI: function(i) {
        	if (!this.chases[i]) {
        		this.chases[i] = {};
        	}
        	this.chases[i].award_time = chases[i].award_time;
    		this.chases[i].show_end_time = chases[i].show_end_time;
        }
	}

	var ballcollection = new ballCollection({
		multiModifier: multiModifier,
		chaseModifier: chaseModifier,
		issue: ISSUE
	});
	ballcollection.add(new cx.BallBox('.hz', {amount: 16, min: 3, minBall:1, playType: 'hz', lotteryId: cx.Lottery.KS}), 1);
	ballcollection.add(new cx.BallBox('.sthtx', {amount: 1, min: 0, playType: 'sthtx', lotteryId: cx.Lottery.KS}), 2);
	ballcollection.add(new cx.BallBox('.sthdx', {amount: 6, min: 1, playType: 'sthdx', lotteryId: cx.Lottery.KS}), 3);
	ballcollection.add(new cx.BallBox('.sbth', {amount: 20, min: 0, playType: 'sbth', lotteryId: cx.Lottery.KS}), 4);
	ballcollection.add(new cx.BallBox('.slhtx', {amount: 1, min: 0, playType: 'slhtx', lotteryId: cx.Lottery.KS}), 5);
	ballcollection.add(new cx.BallBox('.ethfx', {amount: 6, min: 0, playType: 'ethfx', lotteryId: cx.Lottery.KS}), 6);
	ballcollection.add(new cx.BallBox('.ethdx', {amount: 30, min: 0, playType: 'ethdx', lotteryId: cx.Lottery.KS}), 7);
	ballcollection.add(new cx.BallBox('.ebth', {amount: 15, min: 0, playType: 'ebth', lotteryId: cx.Lottery.KS}), 8);
	
	cx._basket_ = ballcollection;
	
	$(".lottery-info-time span strong").html(ISSUE.substring(8, 11));
	renderTime();
	
	
	// 底部操作条悬停
	var eleFixedBox = $('.ele-fixed-box');
    var $castPanel = eleFixedBox.find('.cast-panel');
    var castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
    function onScroll() {
        var scrollTop = $(document).scrollTop() + $(window).height();
        
        if (scrollTop >= castPanelTop) {
            $castPanel.removeClass('cast-panel-fixed');
            if(!-[1,]&&!window.XMLHttpRequest){
               $castPanel.css({'position': 'static'}); 
            }
         } else {
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
        castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
        onScroll();
    })

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
    			url: "/ajax/getOrders/ks",
    			dataType: 'json',
    			success: function(data) {
    				var str = '';
    				if (data.length > 0) {
    					for (i in data) {
    						str += '<tr><td>'+data[i].created+'</td><td>'+data[i].issue+'</td><td class="tal" title="';
    						var codes = data[i].codes.split(';'),carr = [];
    						for (j in codes) {
    							carr = codes[j].split(':');
    							str += cx.Lottery.getPlayTypeName(53, carr[1])+(($.inArray(carr[1], ['2', '5']) > -1) ? '' : carr[0].replace(/,/ig, ' ').replace(/\*/g, ''))+';';
    						}
    						carr = codes[0].split(':');
    						str += '"><div class="text-overflow">'+cx.Lottery.getPlayTypeName(53, carr[1])
    						str += ' <span class="specil-color">'+(($.inArray(carr[1], ['2', '5']) > -1) ? cx.Lottery.getPlayTypeName(53, carr[1]) : carr[0].replace(/,/ig, ' ').replace(/\*/g, ''))+'</span>';
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
    							str += '<a target="_blank" href="/ks?orderId='+data[i].orderId+'">继续预约</a></td><td></td></tr>';
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
    				url: '/ajax/getHistory/ks',
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
    			$(".canvas-mask").hide();
    		}
        }
    })
	
	$(".bet-k3-ft dt").click(function(){
		var iss, str = '';
		if (!$(this).hasClass('active')) {
			$.ajax({
				url: '/ajax/getKj/ks',
				dataType: 'json',
				success: function(data) {
					str = '';
					for (i = 1; i <= 21; i++) {
						iss = padd(i);
						str += "<tr><th>"+iss+"</th><td>";
						if (data[iss] && data[iss].award) {
							for (j in data[iss].award.split(',')) {
								str += "<span class='ball ball-red'>"+data[iss].award.split(',')[j]+"</span>";
							}
							str += "</td><td>"+data[iss].he+"</td><td>"+data[iss].type+"</td></tr>";
						}else {
							str += "</td><td></td><td></td></tr>"
						}
						$(".k3-table-kj tbody:first").html(str);
					}
					str = '';
					for (i = 22; i <= 42; i++) {
						str += "<tr><th>"+i+"</th><td>";
						if (data[i] && data[i].award) {
							for (j in data[i].award.split(',')) {
								str += "<span class='ball ball-red'>"+data[i].award.split(',')[j]+"</span>";
							}
							str += "</td><td>"+data[i].he+"</td><td>"+data[i].type+"</td></tr>";
						}else {
							str += "</td><td></td><td></td></tr>"
						}
						$(".k3-table-kj tbody:eq(1)").html(str);
					}
					str = '';
					for (i = 43; i <= 63; i++) {
						str += "<tr><th>"+i+"</th><td>";
						if (data[i] && data[i].award) {
							for (j in data[i].award.split(',')) {
								str += "<span class='ball ball-red'>"+data[i].award.split(',')[j]+"</span>"
							}
							str += "</td><td>"+data[i].he+"</td><td>"+data[i].type+"</td></tr>";
						}else {
							str += "</td><td></td><td></td></tr>"
						}
						$(".k3-table-kj tbody:eq(2)").html(str);
					}
					str = '';
					for (i = 64; i <= 84; i++) {
						if (i <= 82) {
							str += "<tr><th>"+i+"</th><td>";
							if (data[i] && data[i].award) {
								for (j in data[i].award.split(',')) {
									str += "<span class='ball ball-red'>"+data[i].award.split(',')[j]+"</span>";
								}
								str += "</td><td>"+data[i].he+"</td><td>"+data[i].type+"</td></tr>";
							}else {
								str += "</td><td></td><td></td></tr>"
							}
						}else {
							str += '<tr><th></th><td></td><td></td><td></td></tr>';
						}
						$(".k3-table-kj tbody:last").html(str);
					}
				}
			})
		}
	})
	
	$(".seleFiveBoxScroll").on('click', '.del-match', function(){
		cx._basket_.balls[$(this).parents('tr').data('index')-1].removeBalls();
		if (cx._basket_.betNum == 0) {
			$('.seleFiveTit').removeClass('seleFiveTit2');
            $('.seleFiveBox').hide();
		}
	})
	
	$(".clear-matches").click(function(){
		cx.Confirm({
    		single: '您确定要删除已选择的选项吗？',
            btns: [{type: 'confirm',txt: '确定',},{type: 'cancel',txt: '取消'}],
            confirmCb: function () {
            	for (i in cx._basket_.balls) {
        			cx._basket_.balls[i].removeBalls();
        		}
                $('.seleFiveTit').removeClass('seleFiveTit2');
                $('.seleFiveBox').hide();
            }
        });
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
		$(".kj-area ul li:first").html('和值：--');
		$(".kj-area ul li:last").html('形态：--');
		$(".kj-num").hide();
	}else if(vJson.length == 5) {
		$(".kj-ing").show().html("正在开奖中");
		$(".kj-area ul li:first").html('和值：--');
		$(".kj-area ul li:last").html('形态：--');
		$(".kj-num").hide();
	}else {
		$(".kj-num").show();
		var str = '', hz=0, xt = '三不同';
		for (i in vJson) {
			str += "<span class='kj-num-"+vJson[i]+"'>"+vJson[i]+"</span>";
			hz += parseInt(vJson[i], 10);
		}
		$(".kj-area ul li:first").html("和值：<strong>"+hz+"</strong>");
		if (vJson[0] == vJson[1] && vJson[1] == vJson[2]) {
			xt = '三同号';
		}else if (vJson[0] == vJson[1] || vJson[0] == vJson[1] || vJson[1] == vJson[2]) {
			xt = '二同号';
		}else if ((parseInt(vJson[1]) == parseInt(vJson[0]) + 1) && (parseInt(vJson[2]) == parseInt(vJson[1]) + 1)) {
			xt = '三连号';
		}
		$(".kj-area ul li:last").html('形态：'+xt);
		$(".kj-num").html(str);
		$(".kj-ing").hide();
	}
}

function render(data) {
	$(".lottery-info-time span strong").html(data.issue.substring(8, 11));
	$(".kj-periods em").html(data.prev.substring(8, 11));
	$(".arrow-tag").html("剩"+data.rest+"期<i></i>");
	if (hstyopen) {
		$(".canvas-mask").empty();
		rfshhisty(cx._basket_.playType);
	}
}

var rfshhisty = function(type) {
	var str = '', ms;
	if (type === 'hz') {
		for (i in hsty) {
			str += '<tr';
			if (hsty[i].awardNum) {
				str += " class='column-1'><td>"+hsty[i].issue.substring(4, 11)+"</td><td class='k3-ball'><span>"+hsty[i].awardNum.replace(/,/g, ' ')+"</span></td>";
				if (mall[i]) {
					ms = mall[i][0].split('|')[1].split(',');
					msxt = mall[i][0].split('|')[9].split(',');
					for (j = 3; j <= 18; j++) {
						str += "<td class='k3-red canvas-item'><span";
						if (j == hsty[i].he) {
							str += " class='selected'>"+j;
						}else {
							str += ">"+ms[j-3];
						}
						str += "</span></td>";
					}
					if (hsty[i].he <= 10) {
						str += "<td class='k3-coffee'><span><i>"+msxt[0]+"</i><em>大</em></span></td><td class='k3-coffee'><span class='selected'><i>"+msxt[1]+"</i><em>小</em></span></td>";
					}else {
						str += "<td class='k3-coffee'><span class='selected'><i>"+msxt[0]+"</i><em>大</em></span></td><td class='k3-coffee'><span><i>"+msxt[1]+"</i><em>小</em></span></td>";
					}
					if (hsty[i].he%2 > 0) {
						str += "<td class='k3-blue'><span class='selected'><i>"+msxt[2]+"</i><em>单</em></span></td><td class='k3-blue'><span><i>"+msxt[3]+"</i><em>双</em></span></td></tr>";
					}else {
						str += "<td class='k3-blue'><span><i>"+msxt[2]+"</i><em>单</em></span></td><td class='k3-blue'><span class='selected'><i>"+msxt[3]+"</i><em>双</em></span></td></tr>";
					}
				}else {
					str += "<td colspan='20'></td>";
				}
			}else if (hsty[i].prev && atm > 0) {
				str += "><td>"+hsty[i].issue.substring(4, 11)+"</td><td colspan='21'><em class='main-color atime'>"+maketstr(atm)+"</em>后开奖...</td>";
			}else if (hsty[i].prev) {
				str += "><td>"+hsty[i].issue.substring(4, 11)+"</td><td colspan='21'>正在开奖中...</td>";
			}else {
				str += "><td>"+hsty[i].issue.substring(4, 11)+"</td><td colspan='21'></td>";
			}
		}
		$("."+type+" .ykj-info table tbody").html(str);
		canvasFn($("."+type+" .jqzs"),'#ff3333');
	}else {
		for (i in hsty) {
			str += '<tr';
			var typestr = '', kdstr = '';
			if (hsty[i].awardNum) {
				
				str += " class='column-1'><td>"+hsty[i].issue.substring(4, 11)+"</td><td class='k3-ball'><span>"+hsty[i].awardNum.replace(/,/g, ' ')+"</span></td>";
				if (mall[i]) {
					ms = mall[i][0].split('|')[0].split(',');
					msxt = mall[i][0].split('|')[10].split(',');
					mskd = mall[i][0].split('|')[11].split(',');
					for (j = 1; j <= 6; j++) {
						str += "<td class='k3-red'><span";
						if ($.inArray(j.toString(), hsty[i].awardNum.split(',')) > -1) {
							str += " class='selected";
							if ($.inArray(0, hsty[i].type) > -1) {
								str += " selected3'";
							}else if ($.inArray(3, hsty[i].type) > -1) {
								var awardk = hsty[i].awardNum.split(','), n = 0;
								for (k in awardk) {
									if (j == awardk[k]) {
										n++;
									}
								}
								if (n == 2) {
									str += " selected2'";
								}else {
									str += "'";
								}
							}else {
								str += "'";
							}
							str += ">"+j;
						}else {
							str += ">"+ms[j-1];
						}
						str += "<i></i></span></td>";
						if ($.inArray(j-1, hsty[i].type) > -1) {
							typestr += "<td class='k3-blue'><span class='selected'><i>"+msxt[j-1]+"</i><em>"+typeArr[j-1]+"</em></span></td>";
						}else {
							typestr += "<td class='k3-blue'><span><i>"+msxt[j-1]+"</i><em>"+typeArr[j-1]+"</em></span></td>";
						}
						if (j-1 == hsty[i].kd) {
							kdstr += "<td class='k3-coffee canvas-item'><span class='selected'>"+(j-1)+"</span></td>";
						}else {
							kdstr += "<td class='k3-coffee canvas-item'><span>"+mskd[j-1]+"</span></td>";
						}
					}
					str += typestr+kdstr+"</tr>";
				}else {
					str += "<td colspan='20'></td>"
				}
			}else if (hsty[i].prev && atm > 0) {
				str += "><td>"+hsty[i].issue.substring(4, 11)+"</td><td colspan='21'><em class='main-color atime'>"+maketstr(atm)+"</em>后开奖...</td>";
			}else if (hsty[i].prev) {
				str += "><td>"+hsty[i].issue.substring(4, 11)+"</td><td colspan='21'>正在开奖中...</td>";
			}else {
				str += "><td>"+hsty[i].issue.substring(4, 11)+"</td><td colspan='21'></td>";
			}
		}
		$("."+type+" .ykj-info table tbody").html(str);
		canvasFn($("."+type+" .jqzs"),'#ea9149');
	}
	
}

function fmoney(s) 
{   
	s = s.toString().split("").reverse().join("").substring(0, s.toString().length);
	return s.replace(/(\d{3})/g, '$1,').split("").reverse().join("").replace(',', '');
} 

function canvasFn(ele, lineColor) {
    var betTypeItem = ele.parents('.bet-type-link-item');
    var ykjInfo = betTypeItem.find('.ykj-info');
    var canvasMask = ykjInfo.find('.canvas-mask');
    setTimeout(function() {
        if (!ykjInfo.is(':hidden')) {
            canvasMask.show();
            // 到开发那边改为ajax加载开奖结果成功后开始创建
            var i = 0;
            var itemAarry = [];
            var item = ykjInfo.find('.canvas-item').find('.selected');

            // 获取canvas父级的定位
            var canvasMaskOffset = canvasMask.offset();
            var canvasMaskTop = canvasMaskOffset.top;
            var canvasMaskLeft = canvasMaskOffset.left;
            canvasMask.attr({
                'data-left': canvasMaskLeft,
                'data-top': canvasMaskTop
            })

            // 给选中的球添加自身的定位参数
            for (var i = 0, itemLength = item.length; i < itemLength; i++) {
                $(item[i]).attr({
                    'data-top': Math.round($(item[i]).offset().top - canvasMaskTop) + 10,
                    'data-left': Math.round($(item[i]).offset().left - canvasMaskLeft) + 10
                })
            }
            // 中奖数字分组
            itemAarry = [item];

            for (var k = 0, itemAarryLength = itemAarry.length; k < itemAarryLength; k++) {
                for (var i = 0, itemAarryClength = itemAarry[k].length; i < itemAarryClength; i++) {
                    //控制创建canvas的个数
                    if (i < (itemAarryClength - 1)) {
                        // 计算两个中奖球之间矩形的宽高
                        var left1 = Math.round($(itemAarry[k][i]).attr('data-left'));
                        var top1 = Math.round($(itemAarry[k][i]).attr('data-top'));
                        var left2 = Math.round($(itemAarry[k][i + 1]).attr('data-left'));
                        var top2 = Math.round($(itemAarry[k][i + 1]).attr('data-top'));
                        var width = left2 - left1;
                        var height = top2 - top1;
                        var canvasTag = document.createElement('canvas');

                        // 插入到html中
                        canvasMask.append(canvasTag);
                        if (!$.support.leadingWhitespace) {
                            var canvas = window.G_vmlCanvasManager.initElement($('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k + i]);
                        }

                        var canvas = canvasMask.find('canvas')[(itemAarryClength - 1) * k + i].getContext('2d');
                        if (width > height) {
                            // 当连接线是斜线时
                            // width = width - 20;
                            $(canvasMask.find('canvas')[(itemAarryClength - 1) * k + i]).css({
                                'position': 'absolute',
                                'left': left1 + 'px',
                                'top': top1 + 'px'
                            }).attr({
                                'width': width,
                                'height': height
                            })
                            canvas.beginPath();
                            canvas.moveTo(6, 6 * height / width); //第一个起点
                            canvas.lineTo(width - 6, height - 6 * height / width); //第二个点
                        } else if (width < 0) {
                            // 当连接线是反向斜线时
                            width = -width
                            $(canvasMask.find('canvas')[(itemAarryClength - 1) * k + i]).css({
                                'position': 'absolute',
                                'left': (left1 - width) + 'px',
                                'top': top1 + 'px'
                            }).attr({
                                'width': width,
                                'height': height
                            })
                            canvas.beginPath();
                            canvas.moveTo(width - 6, 6 * height / width); //第一个起点
                            canvas.lineTo(6, height - 6 * height / width); //第二个点
                        } else {
                            // 当连接线是垂直线时
                            // height = height - 18;
                            $(canvasMask.find('canvas')[(itemAarryClength - 1) * k + i]).css({
                                'position': 'absolute',
                                'left': left1 + 'px',
                                'top': top1 + 'px'
                            }).attr({
                                'width': width + 2,
                                'height': height
                            })
                            canvas.beginPath();
                            canvas.moveTo(0, 6); //第一个起点
                            canvas.lineTo(0, height - 6); //第二个点
                        }

                        // 画线
                        canvas.lineWidth = 1;
                        canvas.strokeStyle = lineColor;
                        canvas.stroke();
                    }
                }
            }
        }
    }, 400)
    
    
    
}