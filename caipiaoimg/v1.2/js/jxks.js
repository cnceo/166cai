var cy = 1000, rest = tm, multiModifier, chaseModifier, hstyopen = 0, inteval;

(function() {
	
	window.cx || (window.cx = {});
	
	cx.Lottery = (function(){
		
		var lid = cx.Lid.JXKS, me = {}; 
		
		me.lid = lid;
		me.playTypes = {'hz' : '1', 'sthtx' : '2', 'sthdx' : '3', 'sbth' : '4', 'slhtx' : '5', 'ethfx' : '6', 'ethdx' : '7', 'ebth'  : '8'};
		
		me.getPlayTypeByMidCode = function(midCode) {
			var data = {'1' : 'hz' , '2' : 'sthtx' , '3' : 'sthdx' , '4' : 'sbth', '5' : 'slhtx', '6' : 'ethfx', '7' : 'ethdx', '8' : 'ebth'};
			return (midCode in data) ? data[midCode] : 'dz';
	    }
		
		me.typeArr = ['三同号', '三不同', '三连号', '二同复', '二同单', '二不同'];
		
		me.getNumberSeparator = function(playType) {
			var data = {'default': ',', 'hz': ',', 'sthtx': ',', 'sthdx': ',', 'sbth': ',', 'slhtx': ',', 'ethfx': ',', 'ebth': ','};
			playType = playType || 'default';
	    	return data[playType];
		}
		
		me.getPlaceSeparator = function(playType) {
			var data = {'default': ',', 'hz': ',', 'sthtx': ',', 'sthdx': ',', 'sbth': ',', 'slhtx': ',', 'ethfx': ',', 'ebth': ',' };
			playType = playType || 'default';
	    	return data[playType];
	    }
		
		me.hasPaddingZero = function(playType) {
	        return false;
	    }
		
		me.getCastPost = function( playType) {
	        return '1';
	    };
	    
	    me.getPlayTypeName = function(playType) {
	    	playCnNames = {1: '和值', 2: '三同号通选', 3: '三同号单选', 4: '三不同号', 5: '三连号通选', 6: '二同号复选', 7: '二同号单选', 8: '二不同号'};
            cnName = playCnNames[playType] ? playCnNames[playType] : '普通';
	        return cnName;
	    };
	    
	    me.getMinLength = function(playType) {
	    	return [1];
	    }
	    
	    me.getAmount = function(playType) {
	    	var data = {'default': [6], 'hz': [18], 'sthtx': [1], 'sbth': [20], 'slhtx': [1], 'ethdx': [30], 'ebth': [15]};
	    	return (playType in data) ? data[playType] : data['default'];
	    }
	    
	    me.getStartIndex = function(playType) {
	    	var data = {'default': [0], 'hz': [3], 'sthdx': [1]};
	    	return (playType in data) ? data[playType] : data['default'];
	    }
	    
	    me.getPlayTypeByCode = function(lotteryId, code) {
        	code = parseInt(code, 10)
			var codeArr = ['', 'hz', 'sthtx', 'sthdx', 'sbth', 'slhtx', 'ethfx', 'ethdx', 'ebth'];
			return codeArr[code];
        }
	    
	    return me;
		
	})();
	
})();

$(function() {
	inteval = setInterval("countdown()", cy);
	multiModifier = new cx.AdderSubtractor('.multi-modifier-s:first');
	chaseModifier = new cx.AdderSubtractor('.multi-modifier-s:last');
	var ballCollection = function(options) {
		this.boxes = [];
		this.strings = {};
		this.multiModifier = options.multiModifier;
		this.betNum = 0;
		this._betMoney = 2;
		this.betMoney = 0;
		this.orderType = 0;
		this.multi = 1;
		this.playType = 'hz';
		this.playTypeId = 0;
		this.init();
		
		var _this = this;
		
		var Chase = cx.Chase = function() {
			this.chases = chase;
			this.chaseLength = chaselength;
			this.setStatus = 1;
			this.chaseModifier = options.chaseModifier;
			this.init();
		}
		
		Chase.prototype = {
			init:function(){
				var self = this;
				$(".setStatus").click(function(){
					self.setStatus = 0;
					if ($(this).attr('checked') == 'checked') self.setStatus = 1;
				})
				self.chaseModifier.setCb(function() {
	                self.chaseLength = parseInt(this.getValue(), 10);
	                self.chases = {};
	                j = 0;
	            	
	            	if (self.chaseLength > 0) {
	            		for (i in chases) {
	            			if (j < self.chaseLength) {
	            				self.chases[i] = {'award_time':chases[i].award_time, 'show_end_time':chases[i].show_end_time, 'multi':_this.multi, 'money':_this.multi * _this._betMoney * _this.betNum};
	                    		j++;
	            			}else {
	            				break;
	            			}
	            		}
	            	}
	                $(".numbox .bet-num").html(_this.betNum * _this._betMoney * _this.multi * self.chaseLength);
	            });
			},
			setChaseByI: function(i) {
	        	if (!this.chases[i]) this.chases[i] = {};
	        	this.chases[i].award_time = chases[i].award_time;
	    		this.chases[i].show_end_time = chases[i].show_end_time;
	        }
		}
		
		this.chase = new Chase();
	}
	
	ballCollection.prototype = {
		init:function() {
			var self = this;
			$("#pd_ks_buy").click(function(){
				if ($(this).hasClass('not-login') || !$.cookie('name_ie')) {
	            	cx.PopAjax.login();
	                return ;
	            }

	            if ($(this).hasClass('not-bind')) return ;
				
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

            	cx.castCb(data, {ctype:'create', lotteryId:cx.Lottery.lid, orderType:self.orderType, betMoney:self._betMoney * self.betNum * self.multi, chaseLength:self.chase.chaseLength, issue:ISSUE});
			});
			$(".seleFiveTit").click(function () {
				if (ballcollection.betNum > 0) $(this).toggleClass("seleFiveTit2").next("div.seleFiveBox").toggle();
			});
			$(".gg-type").click(function(){
				$("a[data-ordertype]").toggleClass('selected').each(function(){
					if ($(this).hasClass('selected')) self.orderType = $(this).data('ordertype');
				})
				if (self.orderType == 1) {
					$(".chase-div, .k3-qr").show();
					$(".numbox .bet-num").html(self.betNum * self._betMoney * self.multi * self.chase.chaseLength);
				}else {
					$(".chase-div, .k3-qr").hide();
					$(".numbox .bet-num").html(self.betNum * self._betMoney * self.multi);
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
                if (self.chase.chaseLength > 0) {
                	j = 0;
            		for (i in chases) {
            			if (j < self.chase.chaseLength) {
            				self.chase.chases[i] = {'award_time':chases[i].award_time, 'show_end_time':chases[i].show_end_time, 'multi':self.multi, 'money':self.multi * self._betMoney * self.betNum};
                    		j++;
            			}else {
            				break;
            			}
            		}
            	}
                if (self.orderType == 1) {
                	$(".numbox .bet-num").html(self.betNum * self._betMoney * self.multi * self.chase.chaseLength);
                }else {
                	$(".numbox .bet-num").html(self.betNum * self._betMoney * self.multi);
                }
            });
		},
		add: function(box) {
            this.boxes.push(box);
            box.setCollection(this);
        },
        renderBet: function() {
        	var str = '', playType = 0, pNum = 0;
        	$.each(this.boxes, function(i, box){
        		if (box.balls.length > 0) {
            		pNum++;
            		playType = i+1;
            		box.balls.sort(function(a, b){
            			if (a.toString().indexOf(',') > -1) a = a.replace(/,/g, '');
            			if (b.toString().indexOf(',') > -1) b = b.replace(/,/g, '');
    					a = parseInt(a, 10);
    					b = parseInt(b, 10);
    					return a > b ? 1 : ( a < b ? -1 : 0 );
    				});
            		str += "<tr data-index='"+i+"'><td class='tal'>"+cx.Lottery.getPlayTypeName(playType)+"</td><td class='tal'>";
            		switch (box.playType) {
            			case 'hz':
            			case 'sthdx':
            			case 'sbth':
            			case 'ethfx':
            			case 'ethdx':
            				$.each(box.balls, function(j, ball){
            					str += "<span class='k3-ball'>"+ball.toString().replace(/,/g, '')+"</span>";
            				})
            				break;
            			case 'sthtx':
            			case 'slhtx':
            				str += "<span class='k3-ball'>"+cx.Lottery.getPlayTypeName(playType)+"</span>";
            				break;
            			case 'ebth':
            				$.each(box.balls, function(j, ball){
            					str += "<span class='k3-ball'>"+ball.toString().replace(/,/g, '').replace(/\*/, '')+"</span>";
            				})
            				break;
            		}
                	str += "</td><td class='fcw'>"+(box.balls.length * 2)+"元</td><td><span><a class='del-match' href='javascript:;'>×</a></span></td></tr>";
                	if (box.playType === 'hz') {
                		var min = Number.POSITIVE_INFINITY, max = 0, val;
                		$.each(box.balls, function(j, ball){
                			val = parseInt($("li[data-num="+ball+"] s").html(), 10);
                			if (val < min) min = val;
                			if (val > max) max = val;
                		})
                	}else if (box.playType === 'ebth') {
                		var max = 8, min = 8;
                		$.each(box.balls, function(j, ball){
                			for (k = j+1; k < box.balls.length; k++) {
            					for (l = 0; l <= 1; l++){
            						for (m = 0; m <= 1; m++) {
            							if (ball.split(',')[l] == box.balls[k].split(',')[m]) {
            								if (max < 16) max = 16;
            								var a = ball.replace(ball.split(',')[l], '').replace(/,|\*/g, ''), b = box.balls[k].replace(ball.split(',')[l], '').replace(/,|\*/g, '');
            								if ($.inArray(a+","+b+",*", box.balls) > -1) max = 24;
            							}
            						}
            					}
                			}
                		})
                	}else {
                		var min = parseInt($(".bet-type-link li:eq("+i+") b").html(), 10), max = min;
                	}
                	if (max == min) {
            			$(".bet-type-link-item."+box.playType+" .pick-area-note span").html("如中奖，奖金"+min+"元，盈利<em>"+(min - box.balls.length * 2)+"元</em>")
            		}else {
            			$(".bet-type-link-item."+box.playType+" .pick-area-note span").html("如中奖，奖金"+min+"~"+max+"元，盈利<em>"+(min - box.balls.length * 2)+"~"+(max - box.balls.length * 2)+"元</em>")
            		}
                	$(".bet-type-link-item."+box.playType+" .pick-area-note").show();
            		$(".bet-type-link-item."+box.playType+" .pick-area-explain").hide();
            	}else {
            		$(".bet-type-link-item."+box.playType+" .pick-area-explain").show();
            		$(".bet-type-link-item."+box.playType+" .pick-area-note").hide();
            	}
        	})
            $(".selected-matches tbody").html(str);
            self.setStatus = 0;
            if (pNum == 1) this.playTypeId = playType;
        	this.resetStrings();
        	if (this.betNum == 0) {
        		$('.seleFiveTit').removeClass('seleFiveTit2');
                $('.seleFiveBox').hide();
        	}
        	$(".count-matches").html(this.betNum);
        	if (this.orderType == 1) {
            	$(".numbox .bet-num").html(this.betNum * this._betMoney * this.multi * this.chase.chaseLength);
            }else {
            	$(".numbox .bet-num").html(this.betNum * this._betMoney * this.multi);
            }
        },
        resetStrings: function() {
        	this.strings = {}, this.betNum = 0;
        	var k = 0, self = this;
        	$.each(this.boxes, function(i, box){
        		if (box.playType !== 'hz') {
        			$.each(box.balls, function(j, ball){
        				self.strings[k] = {balls:{'tuo':[ball]}, betNum:1, playType: box.playType};
        				k++;
        				self.betNum += 1;
        			})
        		}else if (box.balls.length > 0) {
        			self.strings[k] = {balls:[{'tuo':box.balls}], betNum:box.balls.length, playType:box.playType};
    				k++;
    				self.betNum += box.balls.length;
        		}
        	})
        	this.betMoney = this.betNum * this._betMoney;
        	if (this.chase.chaseLength > 0) {
        		j = 0;
        		for (i in chases) {
        			if (j < this.chase.chaseLength) {
        				this.chase.chases[i] = {'award_time':chases[i].award_time, 'show_end_time':chases[i].show_end_time, 'multi':this.multi, 'money':this.multi * this._betMoney * this.betNum};
                		j++;
        			}else {
        				delete this.chase.chases[i];
        				break;
        			}
        		}
        	}
        },
        setType: function() {
            castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
            onScroll();
        },
        getCastOptions: function() {
            var castStr = cx.castData.toCastString(this.strings);
            if (this.orderType == 1) {
            	var endTime = '';
            	for (i in this.chase.chases) {
                	if (endTime === ''){
                		endTime = this.chase.chases[i].show_end_time;
                	}else {
                		break;
                	}
                }
            	var data = {
                    ctype: 'create',
                    buyPlatform: 0,
                    codes: castStr,
                    lid: cx.Lottery.lid,
                    money: this._betMoney * this.betNum * this.chase.chaseLength * this.multi,
                    multi: this.multi * this.chase.chaseLength,
                    playType: 0,
                    setStatus: this.chase.setStatus,
                    betTnum: this.betNum,
                    isChase: 0,
                    orderType: 1,
                    totalIssue: this.chase.chaseLength,
                    chases: cx.castData.toChaseString(this.chase.chases),
                    endTime: ENDTIME
                };
            }else {
            	var data = {
                    ctype: 'create',
                    buyPlatform: 0,
                    codes: castStr,
                    lid: cx.Lottery.lid,
                    money: this._betMoney * this.betNum * this.multi,
                    multi: this.multi,
                    issue: ISSUE,
                    playType: 0,
                    betTnum: this.betNum,
                    isChase: 0,
                    orderType: 0,
                    endTime: ENDTIME
                };
            }
            return data;
        },
        getBoxes: function() {
            return [];
        }
	}

	var ballcollection = new ballCollection({
		multiModifier: multiModifier,
		chaseModifier: chaseModifier,
	});
	
	cx.BallBox.prototype.init = function() {
		var self = this;
    	this.$balls = this.$el.find('.pick-area-ball '+this.smallel);
    	this.$balls.click(function() {
        	var $this = $(this);
        	self.BallTriger($this);
        });
        this.$el.find('.clear-balls').click(function() {
            self.removeBalls();
        });
        this.$el.find('.rand-select').click(function() {
            self.removeBalls();
            self.rand(1, function(i) {
            	if (self.playType == 'hz') {
            		self.$balls.filter("[data-num='"+i+"']").trigger('click');
            	} else {
            		self.$balls.eq(i - 1).trigger('click');
            	}
            });
            self.collection.renderBet();
        });
        this.$el.find('.filter-smalls').click(function() {
            self.removeBalls();
            for (var i = 3; i <= 10; ++i) {
                self.BallTriger(self.$balls.filter("[data-num='"+i+"']"));
            }
        });
        this.$el.find('.filter-bigs').click(function() {
            self.removeBalls();
            for (var i = 11; i <= 18; ++i) {
            	self.BallTriger(self.$balls.filter("[data-num='"+i+"']"));
            }
        });
	}
	
	cx.BallBox.prototype.BallTriger = function ($el, type) {        	
		var ball = $el.data('num');
        if ($el.hasClass('selected')) {
        	$el.removeClass('selected');
        	this.removeBall(ball, type);
        } else if (!this.ballValid(type)) {
        	cx.Alert({content: cx.Lottery.getRule(this.playType, [pad(this.collection.boxes.length, 5, this.index)])['content']});
        } else {
        	$el.addClass('selected');
        	this.addBall(ball, type);
        }
        this.collection.renderBet();
    }
	
	cx.BallBox.prototype.addBall = function(i, type) {
    	var arr = this.getBalls(type);
    	if (this.playType == 'hz') i = parseInt(i, 10);
    	if ($.inArray(i, arr) > -1) return ;
    	arr.push(i);
    }
	
	cx.BallBox.prototype.removeBalls = function() {
    	var self = this;
        this.balls = [];
        this.$balls.removeClass('selected');
        this.collection.renderBet();
    }
	
	cx.BallBox.prototype.setCollection = function(collection) {
        this.collection = collection;
        this.index = 0;
    }
	
	cx.BallBox.prototype.removeBall = function(i, type) {
    	var arr = this.getBalls(type);
    	if (this.playType !== 'hz') {
    		var index;
    		for (j in arr) {
    			if (arr[j].replace(/,/g, '') === i.toString().replace(/,/g, '')) index = j;
        	}
    	}else {
    		i = parseInt(i, 10);
    		var index = $.inArray(i, arr);
    	}
        if (index == -1) return ;
        arr.splice(index, 1);
    }
	
	$.extend(cx.castData, {'toCastString':function(subStrings){
		var betStr = [], ballStr = [];
		$.each(subStrings, function(k, subString){
			var playType = subString.playType || 'default', midStr = ':' + cx.Lottery.playTypes[playType], postStr = ':' + cx.Lottery.getCastPost(playType);
            ballStr = [];
            if (playType === 'hz') {
            	var tuo = subString.balls[0]['tuo'];
            }else {
            	var tuo = subString.balls['tuo'];
            }
            for (var i = 0; i < tuo.length; ++i) {
            	if (playType !== 'hz') tuo[i] += '';
            	ballStr.push($.trim(tuo[i]));
            }
            ballStr = ballStr.join(cx.Lottery.getNumberSeparator(playType));
            betStr.push(ballStr + midStr + postStr);
		})
        return betStr.join(';');
	}})
	
	ballcollection.add(new cx.BallBox('.hz', {playType: 'hz', smallel:'li'}));
	ballcollection.add(new cx.BallBox('.sthtx', {playType: 'sthtx', smallel:'li'}));
	ballcollection.add(new cx.BallBox('.sthdx', {playType: 'sthdx', smallel:'li'}));
	ballcollection.add(new cx.BallBox('.sbth', {playType: 'sbth', smallel:'li'}));
	ballcollection.add(new cx.BallBox('.slhtx', {playType: 'slhtx', smallel:'li'}));
	ballcollection.add(new cx.BallBox('.ethfx', {playType: 'ethfx', smallel:'li'}));
	ballcollection.add(new cx.BallBox('.ethdx', {playType: 'ethdx', smallel:'li'}));
	ballcollection.add(new cx.BallBox('.ebth', {playType: 'ebth', smallel:'li'}));
	
	cx._basket_ = ballcollection;
	
	$(".lottery-info-time span strong").html(ISSUE.substring(8, 11));
	renderTime();
	
	// 底部操作条悬停
	var eleFixedBox = $('.ele-fixed-box'), $castPanel = eleFixedBox.find('.cast-panel'), castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
    function onScroll() {
        var scrollTop = $(document).scrollTop() + $(window).height();
        
        if (scrollTop >= castPanelTop) {
            $castPanel.removeClass('cast-panel-fixed');
            if(!-[1,]&&!window.XMLHttpRequest) $castPanel.css({'position': 'static'}); 
         } else {
            $castPanel.addClass('cast-panel-fixed');
            if(!-[1,]&&!window.XMLHttpRequest) $castPanel.css({'position': 'absolute', 'bottom': 'auto', 'top': scrollTop-$castPanel.height() + 'px'}); 
        }
    }

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
	        cx.PopAjax.login();
	        return ;
		}
		
        $(this).toggleClass('selected').siblings().removeClass('selected');
        $(this).parents('.table-zs').find('.table-zs-item').eq($(this).index()).toggle().siblings().hide();
        
        if ($(this).hasClass('selected') && $(this).hasClass('myorder')) {
        	$.ajax({
    			url: "/ajax/getOrders/jxks",
    			dataType: 'json',
    			success: function(data) {
    				var str = '';
    				if (data.length > 0) {
    					for (i in data) {
    						str += '<tr><td>'+data[i].created+'</td><td>'+data[i].issue+'</td><td class="tal" title="';
    						var codes = data[i].codes.split(';'),carr = [];
    						for (j in codes) {
    							carr = codes[j].split(':');
    							str += cx.Lottery.getPlayTypeName(carr[1])+(($.inArray(carr[1], ['2', '5']) > -1) ? '' : carr[0].replace(/,/ig, ' ').replace(/\*/g, ''))+';';
    						}
    						carr = codes[0].split(':');
    						str += '"><div class="text-overflow">'+cx.Lottery.getPlayTypeName(carr[1])+' <span class="specil-color">'+(($.inArray(carr[1], ['2', '5']) > -1) ? cx.Lottery.getPlayTypeName(carr[1]) : carr[0].replace(/,/ig, ' ').replace(/\*/g, ''))+'</span>';
    						if (j > 0) str += '...';
    						str += '</div></td><td><span class="fcs">'+fmoney(parseInt(data[i].money, 10) / 100, 3)+'.00</span></td><td><span class="fcs">'+cx.Order.getStatus(data[i].status, 3)+'</span></td>';
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
    							str += '<a target="_blank" href="/jxks?orderId='+data[i].orderId+'">继续预约</a></td><td></td></tr>';
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
    				url: '/ajax/getHistory/jxks',
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
		var iss, str = '', each = 21;
		if (!$(this).hasClass('active')) {
			$.ajax({
				url: '/ajax/getKj/jxks',
				dataType: 'json',
				success: function(data) {
					for (k = 0; k < 4; k++) {
						str = '';
						for (i = k * each + 1; i <= (k+1) * each; i++) {
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
						}
						$(".k3-table-kj tbody:eq("+k+")").html(str);
					}
				}
			})
		}
	})
	
	$(".seleFiveBoxScroll").on('click', '.del-match', function(){
		cx._basket_.boxes[$(this).parents('tr').data('index')].removeBalls();
		if (cx._basket_.betNum == 0) {
			$('.seleFiveTit').removeClass('seleFiveTit2');
            $('.seleFiveBox').hide();
		}
	})
	
	$(".clear-matches").click(function(){
		cx.Confirm({
    		single: '您确定要删除已选择的选项吗？',
            btns: [{type: 'confirm',txt: '确定'},{type: 'cancel',txt: '取消'}],
            confirmCb: function () {
            	$.each(cx._basket_.boxes, function(i, box){
            		box.removeBalls();
            	})
                $('.seleFiveTit').removeClass('seleFiveTit2');
                $('.seleFiveBox').hide();
            }
        });
	})
	
	$('.pick-area-tips').on('click', 'li', function(){
        if(!$(this).hasClass('current')){
            $(this).addClass('current').siblings().removeClass('current').appendTo($(this).parents('.pick-area-tips ul'));
            $(this).parents('.pick-area').find('.pick-area-ball').removeClass('hot');
        }
        if($(this).find('a').text() == '冷热') $(this).parents('.pick-area').find('.pick-area-ball').addClass('hot');
    })

    $('.bet-k3-ft').on("click", 'dt', function() {
        $(this).toggleClass('active');
        $(this).parents('dl').find('dd').slideToggle();
    })
    
    $('.side-menu-k3').on('click', '.past-award', function(){
        var tEle = $('.bet-k3-ft').find('dl').eq(0);
        if (!$(".bet-k3-ft dd")[0].style.display) $(".bet-k3-ft dt").trigger('click')
        tEle.find('dd').slideDown();
        var calcHeight = $('.bet-k3-ft').offset().top;
        $('body, html').animate({
            scrollTop: calcHeight
        }, 400);  
    })

    $('.k3-qr .bubble-tip').mouseenter(function(){
        $.bubble({target:this, position: 't', align: 'l', content: $(this).attr('tiptext'), width:'240px'})
    }).mouseleave(function(){
        $('.bubble').hide();
    });

    $('.pick-area-explain .bubble-tip, .pick-area-tips .bubble-tip').mouseenter(function(){
        $.bubble({target:this, position: 'b', align: 'l', content: $(this).attr('tiptext'), width:'auto'})
    }).mouseleave(function(){
        $('.bubble').hide();
    });

    $('.pick-area-tips .bubble-tip').mouseenter(function(){
        $.bubble({target:this, position: 'b', align: 'l', content: $(this).attr('tiptext'), width:'auto', skin: 2})
    }).mouseleave(function(){
        $('.bubble').hide();
    });
})

function renderTime() {
	$(".time").html(maketstr(tm));
	if (atm > 0 && atm < 100000) {
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
			}else if (hsty[i].prev && atm > 0 && atm < 100000) {
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
									if (j == awardk[k]) n++;
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
							typestr += "<td class='k3-blue'><span class='selected'><i>"+msxt[j-1]+"</i><em>"+cx.Lottery.typeArr[j-1]+"</em></span></td>";
						}else {
							typestr += "<td class='k3-blue'><span><i>"+msxt[j-1]+"</i><em>"+cx.Lottery.typeArr[j-1]+"</em></span></td>";
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
			}else if (hsty[i].prev && atm > 0 && atm < 100000) {
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

function fmoney(s) {   
	s = s.toString().split("").reverse().join("").substring(0, s.toString().length);
	return s.replace(/(\d{3})/g, '$1,').split("").reverse().join("").replace(',', '');
} 

function canvasFn(ele, lineColor) {
    var betTypeItem = ele.parents('.bet-type-link-item'), ykjInfo = betTypeItem.find('.ykj-info'), canvasMask = ykjInfo.find('.canvas-mask');
    setTimeout(function() {
        if (!ykjInfo.is(':hidden')) {
            canvasMask.show();
            // 到开发那边改为ajax加载开奖结果成功后开始创建
            var i = 0, itemAarry = [], item = ykjInfo.find('.canvas-item').find('.selected'),

            // 获取canvas父级的定位
            canvasMaskOffset = canvasMask.offset(), canvasMaskTop = canvasMaskOffset.top, canvasMaskLeft = canvasMaskOffset.left;
            canvasMask.attr({'data-left': canvasMaskLeft, 'data-top': canvasMaskTop})

            // 给选中的球添加自身的定位参数
            for (var i = 0, itemLength = item.length; i < itemLength; i++) {
                $(item[i]).attr({'data-top': Math.round($(item[i]).offset().top - canvasMaskTop) + 10, 'data-left': Math.round($(item[i]).offset().left - canvasMaskLeft) + 10})
            }
            // 中奖数字分组
            itemAarry = [item];

            for (var k = 0, itemAarryLength = itemAarry.length; k < itemAarryLength; k++) {
                for (var i = 0, itemAarryClength = itemAarry[k].length; i < itemAarryClength; i++) {
                    //控制创建canvas的个数
                    if (i < (itemAarryClength - 1)) {
                        // 计算两个中奖球之间矩形的宽高
                        var left1 = Math.round($(itemAarry[k][i]).attr('data-left')), top1 = Math.round($(itemAarry[k][i]).attr('data-top')), left2 = Math.round($(itemAarry[k][i + 1]).attr('data-left')),
                        top2 = Math.round($(itemAarry[k][i + 1]).attr('data-top')) ,width = left2 - left1, height = top2 - top1, canvasTag = document.createElement('canvas');

                        // 插入到html中
                        canvasMask.append(canvasTag);
                        if (!$.support.leadingWhitespace) var canvas = window.G_vmlCanvasManager.initElement($('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k + i]);

                        var canvas = canvasMask.find('canvas')[(itemAarryClength - 1) * k + i].getContext('2d');
                        if (width > height) {
                            // 当连接线是斜线时
                            // width = width - 20;
                            $(canvasMask.find('canvas')[(itemAarryClength - 1) * k + i]).css({'position': 'absolute', 'left': left1 + 'px', 'top': top1 + 'px'}).attr({'width': width, 'height': height})
                            canvas.beginPath();
                            canvas.moveTo(6, 6 * height / width); //第一个起点
                            canvas.lineTo(width - 6, height - 6 * height / width); //第二个点
                        } else if (width < 0) {
                            // 当连接线是反向斜线时
                            width = -width
                            $(canvasMask.find('canvas')[(itemAarryClength - 1) * k + i]).css({'position': 'absolute', 'left': (left1 - width) + 'px', 'top': top1 + 'px'}).attr({'width': width, 'height': height})
                            canvas.beginPath();
                            canvas.moveTo(width - 6, 6 * height / width); //第一个起点
                            canvas.lineTo(6, height - 6 * height / width); //第二个点
                        } else {
                            // 当连接线是垂直线时
                            // height = height - 18;
                            $(canvasMask.find('canvas')[(itemAarryClength - 1) * k + i]).css({'position': 'absolute', 'left': left1 + 'px', 'top': top1 + 'px'}).attr({'width': width + 2, 'height': height})
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