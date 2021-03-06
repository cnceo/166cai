(function() {
	
    window.cx || (window.cx = {});
    cx.closeCount = false;
    var Lottery = cx.Lottery = (function() {

        var me = {QLC: 23528, QXC: 10022, PL3: 33, PLS: 33, PL5: 35, PLW: 35, FCSD: 52, PLS:33, PLW:35};

        function _getNumberSeparator(lotteryId, playType) {
            var NUMBER_SEPARATOR = {};
            NUMBER_SEPARATOR[me.PLS] = {'default': '', 'zx': '', 'z3': ',', 'z6': ','};
            NUMBER_SEPARATOR[me.PLW] = {'default': ''};
            NUMBER_SEPARATOR[me.QLC] = {'default': ','};
            NUMBER_SEPARATOR[me.QXC] = {'default': ''};
            NUMBER_SEPARATOR[me.FCSD] = {'default': '', 'zx': '', 'z3': ',', 'z6': ','};

            var separator = ',';
            playType || (playType = 'default');
            if (lotteryId in NUMBER_SEPARATOR) {
                if (playType in NUMBER_SEPARATOR[lotteryId]) {
                    separator = NUMBER_SEPARATOR[lotteryId][playType];
                }
            }

            return separator;
        }

        function _getPlaceSeparator(lotteryId, playType) {
            var PLACE_SEPARATOR = {};
            PLACE_SEPARATOR[me.PLS] = {'default': ',', 'zx': ',', 'z3': '', 'z6': ''};
            PLACE_SEPARATOR[me.PLW] = {'default': ','};
            PLACE_SEPARATOR[me.QXC] = {'default': ','};
            PLACE_SEPARATOR[me.QLC] = {'default': ''};
            PLACE_SEPARATOR[me.FCSD] = {'default': ',', 'zx': ',', 'z3': '', 'z6': ''};

            var separator = '|';
            playType || (playType = 'default');
            if (lotteryId in PLACE_SEPARATOR) {
                if (playType in PLACE_SEPARATOR[lotteryId]) {
                    separator = PLACE_SEPARATOR[lotteryId][playType];
                }
            }

            return separator;
        }

        function _hasPaddingZero(lotteryId, playType) {
            var PADDING_ZERO = {};
            PADDING_ZERO[me.PLS] = {'default': false};
            PADDING_ZERO[me.PLW] = {'default': false};
            PADDING_ZERO[me.FCSD] = {'default': false};
            PADDING_ZERO[me.QXC] = {'default': false};
            PADDING_ZERO[me.QLC] = {'default': true};

            var hasPadding = true;
            playType || (playType = 'default');
            if (lotteryId in PADDING_ZERO) {
                hasPadding = PADDING_ZERO[lotteryId]['default'];
            }

            return hasPadding;
        }

        me.playTypes = {
            52:{'default':'1', zx:'1',  z3:'2', z6:'3'},
            33:{'default':'1', zx:'1', z3:'2', z6:'3'},
            35:{'default': 1},
            10022:{'default': 1},
            23528:{'default': 1}
        };
        
        me.jiangjin = {
    		33:{zx:'1040', z3:'346', z6:'173'},
            35:{'default':'100000'}
        }

        function _getCastPost(lotteryId, playType, betNum) {
            var CAST_POST = {};
            if (betNum > 1) {
            	CAST_POST[me.PLS] = {'default': '1', 'zx': '1', 'z3': '3', 'z6': '3'};
                CAST_POST[me.FCSD] = {'default': '1', 'zx': '1', 'z3': '3', 'z6': '3'};
            }else {
            	CAST_POST[me.PLS] = {'default': '1', 'zx': '1', 'z3': '1', 'z6': '3'};
                CAST_POST[me.FCSD] = {'default': '1', 'zx': '1', 'z3': '1', 'z6': '3'};
            }
            
            CAST_POST[me.PLW] = {'default': '1'};

            var post = '1';
            playType || (playType = 'default');
            if (lotteryId in CAST_POST) {
                if (playType in CAST_POST[lotteryId]) {
                    post = CAST_POST[lotteryId][playType];
                }
            }

            return post;
        };

        me.getPlayTypeName = function(lotteryId, playType) {
            var cnName = '';
            var playCnNames = {};
            if ($.inArray(lotteryId, [me.PLS, me.FCSD]) > -1) {
                playCnNames = {1: '直选', 2: '组三', 3: '组六'};
                cnName = playCnNames[playType];
            }else {
                cnName = '普通';
            }

            return cnName;
        };
        
        me.getMinLength = function(lotteryId, playType) {
        	var MIN_LENGTH = {};
        	MIN_LENGTH[me.PLS] = {'default': [1, 1, 1], 'zx': [1, 1, 1], 'z3': [3], 'z6': [3]};
        	MIN_LENGTH[me.PLW] = {'default': [1, 1, 1, 1, 1]};
        	MIN_LENGTH[me.QXC] = {'default': [1, 1, 1, 1, 1, 1, 1]};
        	MIN_LENGTH[me.QLC] = {'default': [7]};
        	MIN_LENGTH[me.FCSD] = {'default': [1, 1, 1], 'zx': [1, 1, 1], 'z3': [3], 'z6': [3]};
        	
        	var minlength = [];
            playType || (playType = 'default');
            if (lotteryId in MIN_LENGTH) {
                if (playType in MIN_LENGTH[lotteryId]) {
                	minlength = MIN_LENGTH[lotteryId][playType];
                }
            }
            
            return minlength;
        }
        
        me.getAmount = function(lotteryId, playType) {
        	var AMOUNT = {};
        	AMOUNT[me.QXC] = {'default': [9, 9, 9, 9, 9, 9, 9]};
        	AMOUNT[me.QLC] = {'default': [30]};
        	AMOUNT[me.PLS] = {'default': [9, 9, 9], 'zx': [9, 9, 9], 'z3': [9], 'z6': [9]};
        	AMOUNT[me.PLW] = {'default': [9, 9, 9, 9, 9]};
        	AMOUNT[me.FCSD] = {'default': [9, 9, 9], 'zx': [9, 9, 9], 'z3': [9], 'z6': [9]};
        	
        	var amount = [];
            playType || (playType = 'default');
            if (lotteryId in AMOUNT) {
                if (playType in AMOUNT[lotteryId]) {
                	amount = AMOUNT[lotteryId][playType];
                }
            }
            
            return amount;
        }
        
        me.getStartIndex = function(lotteryId, playType) {
        	var STARTINDEX = {};
        	STARTINDEX[me.QXC] = {'default': [0, 0, 0, 0, 0, 0, 0]};
        	STARTINDEX[me.QLC] = {'default': [1]};
        	STARTINDEX[me.PLS] = {'default': [0, 0, 0]};
        	STARTINDEX[me.PLW] = {'default': [0, 0, 0, 0, 0]};
        	STARTINDEX[me.FCSD] = {'default': [0, 0, 0]};
        	
        	var startindex = [];
            playType || (playType = 'default');
            if (lotteryId in STARTINDEX) {
                if (playType in STARTINDEX[lotteryId]) {
                	startindex = STARTINDEX[lotteryId][playType];
                }
            }
            
            return startindex;
        }

        me.getCnName = function(lotteryId) {
            lotteryId = parseInt(lotteryId, 10);
            return {10022: '七星彩', 23528: '七乐彩', 52: '福彩3D', 35: '排列5', 33: '排列3'}[lotteryId];
        };

        me.getRule = function(lotteryId, playType) {
            if ($.inArray(lotteryId, [Lottery.FCSD, Lottery.PLS]) > -1){
                var rule = '';
                switch(playType){
	                case 'zx':
	                    rule = '<i class="icon-font">&#xe611;</i>每位至少选择１个号码';
	                    break;
	                case 'z3':
	                    rule = '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">２</span>个号码';
	                    break;
	                case 'z6':
	                    rule = '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">３</span>个号码';
	                    break;
                    default:
                    break;
                }
                return rule;
            }else if(lotteryId == Lottery.PLW){
                return '<i class="icon-font">&#xe611;</i>每位至少选择１个号码';
            }else if(lotteryId == Lottery.QXC){
                return '<i class="icon-font">&#xe611;</i>每位至少选择<span class="num-red">１</span>个号码';
            }else if(lotteryId == Lottery.QLC){
                return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">７</span>个号码';
            }
        };

        me.parseCast = function(lotteryId, code) {
            var parts = code.split(':');
            var numbers = parts[0];
            var playType = parts[1];
            var hasDan = code.indexOf('$') > -1;
            var hasPost = code.indexOf('|') > -1;

            var preDan = [];
            var preTuo = [];
            var postDan = [];
            var postTuo = [];

            if (lotteryId === 33 || lotteryId === 35 || lotteryId === 52 || lotteryId === 10022 || lotteryId === 23528) {
                preTuo = numbers;
            } else if (lotteryId === 11 || lotteryId === 19 ) {
                preTuo = numbers;
            }	else {
                preTuo = numbers.split(',');
            }
            playType = me.getPlayTypeName(lotteryId, playType);

            return {
				playTypeCode: parts[1],
				modeCode: parts[2],
                playType: playType,
                preDan: preDan,
                preTuo: preTuo,
                postDan: postDan,
                postTuo: postTuo
            };
        };

        me.renderCast = function(lotteryId, cast, award) {
            lotteryId = parseInt(lotteryId, 10);
            var parsedCast = me.parseCast(lotteryId, cast);
            var parsedAward = me.parseAward(lotteryId, award);
            var preTpl = '';
            var postTpl = '';
            var castPre = parsedCast.preDan.concat(parsedCast.preTuo);
            var castPost = parsedCast.postDan.concat(parsedCast.postTuo);
			var playTypeCode = parsedCast.playTypeCode;
			var modeCode = parsedCast.modeCode;
			//parsedAward.preCode = ['7', '7', '2'];
			//parsedAward.hasAward = true;
            $(castPre).each(function(key, number) {
				if ( lotteryId == 33 ||  // 排列3
							lotteryId == 35 ||	// 排列5
							lotteryId == 52 ||	// 福彩3D
							lotteryId == 10022 	// 七星彩
							) {
					if ( playTypeCode == '1' ) { // 直选
						$(number.split(',')).each(function(k0, part) {
                            if( preTpl != '' ) {
                                preTpl += '<em>|</em></span><span>';
                            }
                            else{
                                preTpl += '<span>';
                            }
							$(part.split('')).each(function(k1, n1) {
								if ( ( n1 == parsedAward.preCode[k0] ) || parsedAward.hasAward === false) {
									preTpl += me.renderRedDetail(n1);
								} else {
									preTpl += me.renderGrayDetail(n1);
								}
							});
						});
					}
					// 所选号码与开奖号码相同（顺序不限），且开奖号码有任意两位相同即中奖
					else if( playTypeCode == '2' || playTypeCode == '3' ) { // 组三 组六 
						$(number.split(',')).each(function(k1, n1) {
							if ( $.inArray(n1, parsedAward.preCode) > -1 || parsedAward.hasAward === false) {
								preTpl += me.renderRedDetail(n1);
							} else {
								preTpl += me.renderGrayDetail(n1);
							}
						});
					}					
				

					/*
                    if (number.length > 1) {
                        number = number.split('');
						if( preTpl != '' ) {
							preTpl += '<em>|</em>';
						}
                        $(number).each(function(k1, n1) {
                            if ($.inArray(n1, parsedAward.preCode) > -1 || parsedAward.hasAward === false) {
                                preTpl += me.renderRedDetail(n1);
                            } else {
                                preTpl += me.renderGrayDetail(n1);
                            }
                        });
                    } else {
                        if ($.inArray(number, parsedAward.preCode) > -1 || parsedAward.hasAward === false) {
                            preTpl += me.renderRedDetail(number);
                        } else {
                            preTpl += me.renderGrayDetail(number);
                        }
                    }
					*/

                } else if (	lotteryId == 11 ||	// 胜负彩
							lotteryId == 19		// 任九
							) {
					$(number.split(',')).each(function(k0, part) {
						// 复式
						if (number.length > 1) {
							if( preTpl != '' ) {
								preTpl += '<em>|</em>';
							}
							$(part.split('')).each(function(k1, n1) {
								if ( ( n1 == parsedAward.preCode[k0] ) || parsedAward.hasAward === false) {
									preTpl += me.renderRedDetail(n1);
								} else {
									preTpl += me.renderGrayDetail(n1);
								}
							});
						}
						// 单式
						else {
							if ( part == parsedAward.preCode[k0] || parsedAward.hasAward === false ) {
								preTpl += me.renderRedDetail(part);
							} else {
								preTpl += me.renderGrayDetail(part);
							}
						}
					});
				} else if ( number.indexOf(',') > -1 ) {
					if( preTpl != '' ) {
						preTpl += '<em>|</em>';
					}
                    $(number.split(',')).each(function(k1, n1) {
                        if ( parsedAward.hasAward === true ) {
							if( $.inArray(n1, parsedAward.preCode) > -1 ){
								preTpl += me.renderRedDetail(n1);
							} else if( $.inArray(n1, parsedAward.postCode) > -1 ) {
								preTpl += me.renderBlueDetail(n1);
							} else {
								preTpl += me.renderGrayDetail(n1);
							}
                        } else {
                            preTpl += me.renderRedDetail(n1);
                        }
                    });
                } else {
                    if ($.inArray(number, parsedAward.preCode) > -1 || parsedAward.hasAward === false) {
                        preTpl += me.renderRedDetail(number);
                    } else {
                        preTpl += me.renderGrayDetail(number);
                    }
                }
            });
            $(castPost).each(function(key, number) {
                if ($.inArray(number, parsedAward.postCode) > -1 || parsedAward.hasAward === false) {
                    postTpl += me.renderBlueDetail(number);
                } else {
                    postTpl += me.renderGrayDetail(number);
                }
            });

			preTpl = '[' + parsedCast.playType + ']' + preTpl;

            return {
                preTpl: preTpl,
                postTpl: postTpl
            };
        }

        me.parseAward = function(lotteryId, code) {
            var award = {};
            var preCode = [];
            var postCode = [];
            var hasAward = false;
            if (code) {
                code += '';
                hasAward = true;
                code = code.replace(/\s+/g, ',');
                var hasPost = code.indexOf(':') > -1;
                var parts = code.split(':');
                var preCode = parts[0].split(',');
                var postCode = [];
                if (hasPost) {
                    postCode = parts[1].split(',');
                }
            }

            return {
                hasAward: hasAward,
                preCode: preCode,
                postCode: postCode
            };
        };

        me.renderAward = function(lotteryId, code) {
            var tpl = '';
            var parsedCode = me.parseAward(lotteryId, code);

            $(parsedCode.preCode).each(function(key, number) {
                tpl += Lottery.renderRed(number);
            });
            $(parsedCode.postCode).each(function(key, number) {
                tpl += Lottery.renderBlue(number);
            });
            if (tpl == '') {
                tpl = '未开奖';
            }

            return tpl;
        };

        me.renderGray = function(num) {
            return '<span class="ball ball-gray">' + num + '</span>';
        };

        me.renderBlue = function(num) {
            return '<span class="ball ball-blue">' + num + '</span>';
        };

        me.renderRed = function(num) {
            return '<span class="ball ball-red">' + num + '</span>';
        };

        me.renderGrayDetail = function(num) {
            return '<em>' + num + '</em>';
        };

        me.renderBlueDetail = function(num) {
            return '<em class="spec">' + num + '</em>';
        };

        me.renderRedDetail = function(num) {
            return '<em class="spec">' + num + '</em>';
        };

        me.toCastString = function(lotteryId, subStrings) {
            var betStr = [];
            var singleBet;
            var ballStr = [];
            var preStr = [];
            for (var k in subStrings) {
            	var playType = subStrings[k].playType || 'default';
            	var midStr = ':' + me.playTypes[lotteryId][playType];
                var postStr = ':' + _getCastPost(lotteryId, playType, subStrings[k].betNum);
                singleBet = subStrings[k].balls;
                preStr = [];
                for (var j = 0; j < singleBet.length; ++j) {
                    ballStr = [];
                    singleBet[j].sort(function(a, b){
    					a = parseInt(a, 10);
    					b = parseInt(b, 10);
    					return a > b ? 1 : ( a < b ? -1 : 0 );
    				});
                    for (var i = 0; i < singleBet[j].length; ++i) {
                        singleBet[j][i] += '';
                        if (singleBet[j][i].length < 2) {
                            if (_hasPaddingZero(lotteryId, playType)) {
                               singleBet[j][i] = '0' + singleBet[j][i];
                            }
                        }
                        ballStr.push($.trim(singleBet[j][i]));
                    }
                    ballStr = ballStr.join(_getNumberSeparator(lotteryId, playType));
                    preStr.push(ballStr);
                }
                preStr = preStr.join(_getPlaceSeparator(lotteryId, playType));
                betStr.push(preStr + midStr + postStr);
            }
            return betStr.join(';');
        };
        
        me.toChaseString = function(chases) {
        	var chaseStr = '';
        	for(i in chases) {
        		chaseStr += i+"|"+chases[i].multi+"|"+chases[i].money+"|"+chases[i].award_time+"|"+chases[i].show_end_time+";";
        	}
        	return chaseStr;
        };

        me.renderBasket = function() {
        };

        me.getPlayType = function() {
        };

        return me;
    })();

    var BoxCollection = cx.BoxCollection = function(selector, options) {
        this.$el = $(selector);
        this.boxes = [];
        this.betMoney = 2;
        this.edit = 0;
        this.lotteryId = options.lotteryId || false;;
    };

    BoxCollection.prototype = {
        add: function(box) {
            this.boxes.push(box);
            box.setCollection(this);
        },
        addBall: function(boxs, modcode) {
        	var editStrs = {
                balls: [],
                betNum: 0
            };
        	$(this.boxes).each(function(k, box) {
        		box.removeAll();
        		for (var i = 0; i < boxs[k].length; ++i){
        			if (this.getType() === 'z3') {
        				box.addBall(boxs[k][i], false);
        			}else {
        				box.addBall(boxs[k][i]);
        			}
             	}
        		editStrs.balls.push(box.balls);
            });
        	editStrs.betNum = (this.getType() === 'z3' && modcode == 1) ? 1 : this.calcBetNum();
        	editStrs.playType = this.getType();
        	return editStrs;
        },
        isValid: function() {
            for (var i = 0; i < this.boxes.length; ++i) {
                if (!this.boxes[i].isValid()) {
                    return false;
                }
            }
            return true;
        },
        removeAll: function() {
            $(this.boxes).each(function(k, box) {
                box.removeAll();
            });
        },
        renderBet: function() {
        	if(this.$el.find('.num-red').length > 0)
        	{
        		this.$el.find('.num-red').html(this.getNum(0));
        	}
        	if(this.$el.find('.num-blue').length > 0)
        	{
        		this.$el.find('.num-blue').html(this.getNum(1));
        	}
            this.$el.find('.num-multiple').html(this.calcBetNum());
            this.$el.find('.num-money').html(this.calcMoney());
            if (this.isValid()) {
            	if(this.$el.find('.sub-txt').length > 0)
            	{
            		var playType = this.getType();
            		var money = cx.Lottery.jiangjin[this.lotteryId][playType];
            		if(money > 10000)
            		{
            			money = (money/10000)+'万';
            		}
            		var yingli = (cx.Lottery.jiangjin[this.lotteryId][playType]-this.calcMoney());
            		str = '（如中奖，奖金 <em>'+money+'</em> 元，盈利 ';
            		if (yingli > 0) {
            			str += '<em>'+yingli+'</em> 元）';
            		}else {
            			str += '<em class="green-color">'+yingli+'</em> 元）';
            		}
                	$(".sub-txt").html(str);
                	$(".sub-txt").show();
            	}
                this.$el.find('.add-basket').removeClass('btn-disabled');
            } else {
            	$(".sub-txt").hide();
                this.$el.find('.add-basket').addClass('btn-disabled');
            }
        },
        rand: function() {
            var randStrs = {
                balls: [],
                betNum: 0
            };
            $(this.boxes).each(function(k, box) {
                randStrs.balls.push(box.rand());
            });
            randStrs.betNum = this.calcBetNum();
            $(this.boxes).each(function(k, box) {
                box.removeAll();
            });
            return randStrs;
        },
        rand1: function(lotteryId, playType) {
        	var randStrs = {
                balls: [],
                betNum: 0
            };
        	var startindex = cx.Lottery.getStartIndex(lotteryId);
        	var arr = cx.Lottery.getMinLength(lotteryId, playType);
        	randStrs.betNum = 1;
        	var amount = cx.Lottery.getAmount(lotteryId, playType);
        	for (i in arr)
        	{
        		randStrs.balls[i] = [];
        		while (randStrs.balls[i].length < arr[i]) {
            		j = Math.floor(Math.random() * (amount[i] - startindex[i] + 1) + startindex[i]);
            		if ($.inArray(j, randStrs.balls[i]) === -1)
            		{
            			randStrs.balls[i].push(j);
            		}
            	}
        	}
        	if (playType == 'z3') {
        		randStrs.balls[0][1] = randStrs.balls[0][0];
        	}
            randStrs.playType = playType;
            return randStrs;
        },
		edit: function() {
            var editStrs = {
                balls: [],
                betNum: 0
            };
            $(this.boxes).each(function(k, box) {
                editStrs.balls.push(box.edit());
            });
            editStrs.betNum = this.calcBetNum();
            $(this.boxes).each(function(k, box) {
                box.removeAll();
            });
            return editStrs;		
		},
		getError: function(){
			var error = [] ;
            $(this.boxes).each(function(k, box) {
                error.push(box.getError());
            });
			return error;
		},
        getAllBalls: function() {
            var allBalls = [];
            $(this.boxes).each(function(k, box) {
                allBalls.push(box.getBalls());
            });
            var betNum = this.calcBetNum();
            $(this.boxes).each(function(k, box) {
                box.removeAll();
            });
            return {
                balls: allBalls,
                betNum: betNum,
                playType: this.boxes[0].options.playType
            };
        },
        getStrings: function() {
            var strings = [];
            $(this.boxes).each(function(k, box) {
                strings.push(box.joinBalls());
            });
            return strings;
        },
        calcBetNum: function() {
            var product = 1;
            $(this.boxes).each(function(k, box) {
                product *= box.calcComb();
            });
            return product;
        },
        calcMoney: function() {
            return this.calcBetNum() * this.betMoney;
        },
        setBetMoney: function(moneyNum) {
            this.betMoney = moneyNum;
        },
        getBoxes: function() {
            return this.boxes;
        },
        getNum: function(i) {
            return this.boxes[i].getNum();
        },
        getType: function() {
        	return this.boxes[0].getType();
        }
    };

    var BallBox = cx.BallBox = function(selector, options) {
        /*
         * amount
         * min
         * mutex
         */
		this.selector = selector;
        this.$el = $(selector);
        this.options = options || {};
        this.options.mutex = this.options.mutex || false;
        this.options.playType = this.options.playType || 'default';
        this.minBall = this.options.minBall || 0;
        this.balls = [];
        this.odds = [];
        this.evens = [];
        this.bigs = [];
        this.smalls = [];
        this.all = [];
        var mid = Math.floor(this.options.amount / 2);
        for (var i = this.minBall; i <= this.options.amount; ++i) {
            this.all.push(i);
            if (i % 2 == 0) {
                this.evens.push(i);
            } else {
                this.odds.push(i);
            }
            if (i > mid) {
                this.bigs.push(i);
            } else {
                this.smalls.push(i);
            }
        }
		this.error = false;
        this.init();
    };

    cx.BallBox.prototype = {
        init: function() {
            var self = this;
            this.$balls = self.$el.find('.pick-area-ball a');
            this.$balls.click(function() {
            	var $this = $(this);
            	self.BallTriger($this);
            })
//            .hover(function() {
//                $(this).addClass('hover');
//            }, function() {
//                $(this).removeClass('hover');
//            });
            this.$el.find('.clear-balls').click(function() {
                self.removeAll();
            });
            this.$el.find('.filter-bigs').click(function() {
                self.removeAll();
                var ball;
                for (var i = 0; i < self.bigs.length; ++i) {
                    ball = self.bigs[i];
                    self.BallTriger(self.$balls.eq(ball - self.minBall));
                }
            });
            this.$el.find('.filter-smalls').click(function() {
                self.removeAll();
                var ball;
                for (var i = 0; i < self.smalls.length; ++i) {
                    ball = self.smalls[i];
                    self.BallTriger(self.$balls.eq(ball - self.minBall));
                }
            });
            this.$el.find('.rand-select').click(function() {
                var count = self.$el.find('.rand-count').val();
                self.removeAll();
                self.rand(count, function(i) {
                    self.addBall(i + '') ;
                    self.$balls.eq(i - 1).addClass('selected');
                });
                self.collection.renderBet();
            });
            this.$el.find('.rand-count').change(function() {
                var count = self.$el.find('.rand-count').val();
                self.removeAll();
                self.rand(count, function(i) {
                    self.addBall(i + '') ;
                    self.$balls.eq(i - 1).addClass('selected');
                });
                self.collection.renderBet();
            });
            this.$el.find('.filter-odds').click(function() {
                self.removeAll();
                var ball;
                for (var i = 0; i < self.odds.length; ++i) {
                    ball = self.odds[i];
                    self.BallTriger(self.$balls.eq(ball - self.minBall));
                }
            });
            this.$el.find('.filter-evens').click(function() {
                self.removeAll();
                var ball;
                for (var i = 0; i < self.evens.length; ++i) {
                    ball = self.evens[i];
                    self.BallTriger(self.$balls.eq(ball - self.minBall));
                }
            });
            this.$el.find('.filter-all').click(function() {
                self.removeAll();
                var ball;
                for (var i = 0; i < self.all.length; ++i) {
                    ball = self.all[i];
                    self.BallTriger(self.$balls.eq(ball - self.minBall));
                }
            });
        },
        setCollection: function(collection) {
            this.collection = collection;
        },
        isValid: function() {
            return this.balls.length >= this.options.min;
        },
        BallTriger: function ($el) {
            var ball = $el.html();
            if ($el.hasClass('selected')) {
            	$el.removeClass('selected');
            	this.removeBall(ball);
            } else {
            	$el.addClass('selected');
            	this.addBall(ball);
            }
            this.collection.renderBet();
        },
        rand: function(number, cb) {
            number || (number = this.options.min);
            var self = this;
            cb || (cb = function(i) {
                self.balls.push(i);
            });
            var j;
            this.balls = [];
            var mutexBoxes = this.getMutexBoxes();
            var flag = true;
            while (this.balls.length < number) {
                flag = true;
                j = Math.ceil(Math.random() * this.options.amount);
                if (j >= this.minBall && $.inArray(j, this.balls) === -1) {
                    if (this.options.mutex) {
                        for (var k = 0; k < mutexBoxes.length; ++k) {
                            if ($.inArray(j, mutexBoxes[k].balls) !== -1) {
                                flag = false;
                                break;
                            }
                        }
                        if (flag) cb(j);
                    } else {
                        cb(j);
                    }
                }
            }
			this.error = false;
            return this.balls.sort(function(a, b) {
            	a = parseInt(a, 10);
				b = parseInt(b, 10);
				return a > b ? 1 : ( a < b ? -1 : 0 );
            });
        },

		edit: function(number, cb){
            number || (number = this.options.min);
            var self = this;
            cb || (cb = function(i) {
                self.balls.push(i);
            });
			var errStr;
            var j;
            this.balls = [];
            var mutexBoxes = this.getMutexBoxes();
            for (var i = 0; i < $(self.selector).length; i++) {
                j = parseInt( $(self.selector).eq(i).val(), 10);// 取球的值
				if( isNaN(j) ){
					errStr = '请填写数字';
					j = $(self.selector).eq(i).val();
				} else if( j > this.options.amount || j < 1 ){
					errStr = '数字超过范围';
				} else if (j >= this.minBall && $.inArray(j, this.balls) == -1) {
                    if (this.options.mutex) {
                        for (var k = 0; k < mutexBoxes.length; ++k) {
                            if ($.inArray(j, mutexBoxes[k].balls) !== -1) errStr = '互斥错';
                        }
                    } 
                } else {
					errStr = '同种颜色的球数字重复';
				}
				cb(j); // 原样放入界面
            }
			if( errStr ){
				this.error = true;
				cx.Alert({content:errStr});
			}else{
				this.error = false;
			}
            return this.balls;
		},

		getError: function(){
			return this.error;
		},

        calcComb: function() {
            if (this.balls.length < this.options.min) return 0;
            var combCount = cx.Math.combine(this.balls.length, this.options.min);
            if (this.options.playType == 'z3') combCount *= 2;
            return combCount;
        },
        joinBalls: function() {
            return this.balls.join(',');
        },
        getNum: function() {
        	return this.balls.length;
        },
        getBalls: function() {
            return this.balls;
        },
        getType: function() {
        	return this.options.playType;
        },
        addBall: function(i, duplicate) {
        	i = parseInt(i, 10);
            if ($.inArray(i, this.balls) > -1 && duplicate !== false) return ;
            this.balls.push(i);
            this.change();
            var mutexBoxes = this.getMutexBoxes();
            if (this.options.mutex) {
                for (var j = 0; j < mutexBoxes.length; ++j) {
                    mutexBoxes[j].removeBall(i);
                    mutexBoxes[j].$balls.eq(i - 1).removeClass('selected');
                }
            }
        },
        removeBall: function(i) {
        	i = parseInt(i, 10);
            var index = $.inArray(i, this.balls);
            if (index == -1) return ;
            this.balls.splice(index, 1);
        },
        change: function() {
        },
        removeAll: function() {
            this.balls = [];
            this.$balls.removeClass('selected');
            this.collection.renderBet();
        },
        addAll: function() {
            for (var i = 1; i <= this.options.amount; ++i) {
                this.addBall(i);
            }
        },
        addOdds: function() {
            for (var i = 0; i < this.odds.length; ++i) {
                this.addBall(this.odds[i]);
            }
        },
        addEvens: function() {
            for (var i = 0; i < this.evens.length; ++i) {
                this.addBall(this.odds[i]);
            }
        },
        addBigs: function() {
            for (var i = 0; i < this.bigs.length; ++i) {
                this.addBall(this.odds[i]);
            }
        },
        addSmalls: function() {
            for (var i = 0; i < this.smalls.length; ++i) {
                this.addBall(this.odds[i]);
            }
        },
        getMutexBoxes: function() {
            if (this.options.mutex == null) return [];
            if (!this.mutexBoxes) {
                var boxes = this.collection.getBoxes();
                var index = $.inArray(this, boxes);
                var box;
                var i = 0
                this.mutexBoxes = [];
                for (; i < boxes.length; ++i) {
                    box = boxes[i];
                    if ((index !== i) && (box.options.mutex === this.options.mutex)) this.mutexBoxes.push(boxes[i]);
                }
            }
            return this.mutexBoxes;
        }
    };

    var CastBasket = cx.CastBasket = function(selector, options) {
        this.$el = $(selector);
        this.lotteryId = options.lotteryId;
        this.boxes = options.boxes;
        this.issue = options.issue;
        this.multiModifier = options.multiModifier;
        this.strings = {};
        this.autoId = 0;
        this.$castList = this.$el.find('.cast-list');
        this.$betNum = this.$el.find('.betNum');
        this.$betMoney = this.$el.find('.betMoney');
        this.$buyMoney = this.$el.find('.buyMoney input');
        this.betNum = 0;
        this.betMoney = 0;
        this._betMoney = 2;
        this.orderType = 0;
        this.setStatus = 0;
        this.setMoney = options.setMoney || 0;
        this.chases = options.chases || {};
        this.chaseLength = options.chaseLength || 0;
        this.chaseMulti = 0;
        this.chaseMoney = 0;
        this.playType = options.playType || 'default';
        this.getCastOptions = this[options.getCastOptions] || this.getCastOptions;
        this.openStatus = 0;
        this.commission = 0;
		this.buyMoney = 0;
		this.guarantee = 0;
		this.rgpctmin = 5;
        this.init();

    };
    CastBasket.prototype = {
        init: function() {
            var self = this;
            this.multiModifier.setCb(function() {
            	$('.Multi').html(parseInt(this.getValue(), 10));
                self.renderBetMoney();
                self.setChaseMulti(this.getValue());
            });
            if ($("#ordertype1").attr('checked')) {
            	self.orderType = 1;
            	$("#ordertype1").parents('.chase-number-tab').find('.ptips-bd').hide();
            	$("#ordertype1").parents('.chase-number').find('.chase-number-bd').show();
            	self.$el.find('.chase-number-table-hd .follow-issue').val('10');
            	self.$el.find('.chase-number-table .follow-multi').val('1');
            	self.$el.find('.chase-number-table :checkbox').attr('checked', 'checked');
            	self.$el.find(".chase-number-table-ft :checkbox:first").removeAttr('checked');
            }
            
            this.chaseMulti = this.chaseLength * this.$el.find(".chase-number-table-hd .follow-multi:first").val();
            this.chaseMoney = this.chaseMulti * this._betMoney * this.betNum;
            this.$el.find(".chase-number-table-ft .fbig em:first").html(this.chaseLength);
            this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
            this.$el.find('.add-bet').click(function() {
            	self._betMoney = 2;
            	self.playType = 'default';
            	if($(this).attr('checked') == 'checked'){
            		self._betMoney = 3;
            		self.playType = 'zj';
            	}
            	self.setBetMoney();
            	self.betMoney = self._betMoney * self.betNum * self.multiModifier.getValue();
            	self.$betMoney.html(self.betMoney);
            });
            this.$el.find('.add-basket').click(function() {
            	if (self.boxes[self.playType].calcMoney() > 20000) {
            		cx.Alert({content: "<i class='icon-font'>&#xe611;</i>单笔订单金额最高<span class='num-red'>２万</span>元"});
            	}else if (!self.boxes[self.playType].isValid()) {
            		cx.Alert({content: cx.Lottery.getRule(self.lotteryId, self.playType)});
            	}else {
            		var balls = self.boxes[self.playType].getAllBalls();
            		if (self.playType == 'z3') {
            			balls = cx.z3ballsplit(balls);
            			self.addAll(balls);
            		}else {
            			if(self.boxes[self.playType].edit === 0){
                    		self.add(balls);
                    	}else{
                    		self.edit(balls, self.boxes[self.playType].edit);
                    	}
            		}
                    self.boxes[self.playType].removeAll();
                    $('html, body').animate({scrollTop: $('.cast-basket .btn-main').offset().top + $('.cast-basket .btn-main')[0].scrollHeight - $(window).height()});
            	}
            });
            this.$el.find('.rand-cast').click(function() {
                var $this = $(this);
                var amount = parseInt($this.data('amount'), 10);
                var rand = [];
                for (var i = 0; i < amount; ++i) {
                	rand.push(self.boxes[self.playType].rand1(self.lotteryId, self.playType));
                }
                self.addAll(rand);
            });
            this.$el.on('click', '.remove-str', function() {
                var $li = $(this).closest('li');
                var index = $li.attr('data-index');
                for (i in self.boxes)
                {
                	if(index === self.boxes[i].edit)
                    {
                		if(i === self.playType) self.clearButton();
                    	self.boxes[i].edit = 0;
                    }
                }
                $li.remove();
                self.remove($li.data('index'));
            });
            this.$el.on('click', '.modify-str', function() {
            	var index = $(this).parent().data('index');
            	var startindex = cx.Lottery.getStartIndex(self.lotteryId);
            	if(self.strings[index].playType !== self.playType) self.setType(self.strings[index].playType);
            	$.each(self.strings[index].balls, function(i, e){
            		self.boxes[self.playType].boxes[i].removeAll();
  	              	$(e).each(function(k, ball)
  	              	{
  	              		ball = parseInt(ball, 10);
  	              		if(!isNaN(ball))
  	              		{
  	              			self.boxes[self.playType].boxes[i].addBall(ball + '') ;
  	              			self.boxes[self.playType].boxes[i].$balls.eq(ball - parseInt(startindex[i])).addClass('selected'); 
  	              		}
  	              	})
            	})
	            self.boxes[self.playType].renderBet();
	            self.boxes[self.playType].edit = $(this).parent().attr('data-index');
	            $(".add-basket").html('确认修改<i class="icon-font">&#xe614;</i>');
	            $('html, body').animate({scrollTop: $('.bet-main').offset().top});
            });
            this.$el.find('.clear-list').click(function() {
                self.removeAll();
                self.clearButton();
                for (i in self.boxes)
                {
                	self.boxes[i].edit = 0;
                }
            });

            this.$el.on('click', '.submit', function(e) {
                var $this = $(this);
                
                if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
                	cx.PopAjax.login(1);
                    return ;
                }

                if ($this.hasClass('not-bind')) {
                	//cx.PopAjax.bind();
                    return ;
                }
				
                var data = self.getCastOptions();
                data.isToken = 1;
                if (data.betTnum == 0) {
                    if(!$('._radio_selected ul li.cur').hasClass('dssc') && !$('._bet_tab_hd ul li.current').hasClass('dssc'))
                    {
                     new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>至少选择<span class='num-red'>１</span>注号码才能投注，请先选择方案"});
                     return ;
                    }

                }
                
                if (data.orderType == 1 && data.totalIssue <= 1) {
                	cx.Alert({content: "<i class='icon-font'>&#xe611;</i>您好，追号玩法须至少选择<span class='num-red'> 2 </span>期"});
                	return ;
                }
                
                // 最大金额前端限制
            	if (data.orderType == 1) {
            		$.each(data.chases.split(';'), function(i, e){
            			if (parseInt(e.split('|')[2], 10) > 20000) {
            				new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>２万</span>元，请修改订单后重新投注"});
                            return ;
            			}
            		})
            	}else {
            		if ( data.money >20000 ) {
                        new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>２万</span>元，请修改订单后重新投注"});
                        return ;
                    }
            	}

				
                if (self.$el.find(".ipt_checkbox#agreenment").get(0) && !self.$el.find(".ipt_checkbox#agreenment").attr("checked"))
                    return void new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请先阅读并同意《用户委托投注协议》后才能继续"});

                if (self.$el.find(".ipt_checkbox#agreenment1").get(0) && !self.$el.find(".ipt_checkbox#agreenment1").attr("checked"))
                    return void new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请先阅读并同意《用户委托投注协议》<br/>《限号投注风险须知》后才能继续"});

                if (self.$el.find(".ipt_checkbox#agreenment2").get(0) && !self.$el.find(".ipt_checkbox#agreenment2").attr("checked"))
                    return void new cx.Alert({content: "请先阅读并同意《限号投注风险须知》后才能继续"});

                cx.castCb(data, {ctype:'create', lotteryId:self.lotteryId, orderType:self.orderType, betMoney:self.betMoney, chaseLength:self.chaseLength, buyMoney:self.buyMoney, guarantee:self.guarantee, issue:self.issue});
            });
            this.$el.find('.setMoney').blur(function(){
            	var money = parseInt($(this).val(), 10);
            	if ($(this).val().match(/\D/g) !== null || !money) {
            		self.setMoney = 1;
            		$(this).val(1);
            	}else {
                	self.setMoney = money;
                }
            });
            this.$el.find('.setMoney').keyup(function(){
            	var money = parseInt($(this).val(), 10);
            	if(money >= 100000){
                	$(this).val(100000);
                	self.setMoney = 100000;
                }else {
                	self.setMoney = money;
                }
            });
            this.$el.find('.chase-number-table-hd .follow-issue').keyup(function(){
            	var num = parseInt($(this).val(), 10),multi = parseInt(self.$el.find(".chase-number-table-hd .follow-multi").val() || self.multiModifier.value, 10),max = $(this).data('max');
            	if ($(this).val().match(/\D/g) !== null) {//非法字符
            		num = 10;
            		$(this).val(10);
            	}else if(num >= max){
                	$(this).val(max);
                    num = max;
                }
            	if (!isNaN(num) && num >= 2) self.setChaseByIssue(num, multi);
            });
            this.$el.find('.chase-number-table-hd .follow-issue').blur(function(){
            	var num = parseInt($(this).val(), 10),multi = parseInt((self.$el.find(".chase-number-table-hd .follow-multi").val() || self.multiModifier.value), 10);
            	if ($(this).val() === '' || parseInt($(this).val(), 10) < 2) {
            		num = 2;
            		$(this).val(2);
            		self.setChaseByIssue(num, multi);
            	}
            });
            this.$el.find(".chase-number-table-hd .follow-multi").keyup(function(){
            	var max = $(this).data('max');
            	if ($(this).val().match(/\D/g) !== null) {//非法字符
            		$(this).val(1);
            	}else if(parseInt($(this).val()) >= max){
                	$(this).val(max);
                }else if (!$(this).val() || $(this).val() == 0){
                	$(this).val('');
                }
            	var multi = parseInt($(this).val(), 10);
            	if (!isNaN(multi) && multi >= 1) {
            		self.chaseMulti = 0;
            		self.chaseMoney = 0;
                	var issue = [];
                	for (i in self.chases) {
                		self.chases[i].multi = multi;
                		self.chases[i].money = multi * self._betMoney * self.betNum;
                		self.chaseMulti += multi;
                	}
                	
                	self.chaseMoney = self.chaseMulti * self._betMoney * self.betNum;
                	self.$el.find(".chase-number-table-bd tbody tr").each(function(){
                		issue.push($(this).attr('data-issue'));
                	})
                	self.renderChase(issue);
                	self.$el.find(".chase-number-table-ft .fbig em:last").html(self.chaseMoney);
            	}
            });
            this.$el.find(".chase-number-table-hd :checkbox").click(function(){
            	self.chases = {};
            	self.chaseMoney = 0;
            	self.chaseMulti = 0;
            	var issue = [];
            	if ($(this).attr('checked') == 'checked') {
            		var multi = parseInt(self.multiModifier.value, 10);
            		$(".chase-number-table-bd tbody tr").each(function(){
            			i = $(this).attr('data-issue');
                		self.setChaseByI(i);
                		self.chases[i].multi = multi;
                		self.chases[i].money = multi * self._betMoney * self.betNum;
                		self.chaseMulti += multi;
                		issue.push(i);
                	})
                	self.chaseLength = $(".chase-number-table-bd tbody tr").length;
            		self.chaseMoney = self.chaseMulti * self._betMoney * self.betNum;
            		self.$el.find('.chase-number-table-hd .follow-issue').val(self.chaseLength);
            		self.$el.find('.chase-number-table-hd .follow-multi').val(multi);
            		self.$el.find(".chase-number-table-ft .fbig em:first").html(self.chaseLength);
            		self.$el.find(".chase-number-table-ft .fbig em:last").html(self.chaseMoney);
            		self.renderChase(issue);
            	}else {
            		self.chaseLength = 0;
            		self.$el.find(".chase-number-table-bd tbody :checkbox").removeAttr('checked');
            		self.$el.find('.chase-number-table-hd .follow-issue').val('0');
            		self.$el.find('.follow-multi').val('');
            		self.$el.find('.follow-money').html('0');
            		self.$el.find(".chase-number-table-ft .fbig em:first").html('0');
            		self.$el.find(".chase-number-table-ft .fbig em:last").html('0');
            	}
            });
            this.$el.on('click', ".chase-number-table-bd :checkbox", function(){
            	var i = $(this).parents('tr').attr('data-issue');
            	if ($(this).attr('checked') == 'checked') {
            		if ($(".chase-number-table-bd :checkbox[checked!='checked']").length == 0) $(".chase-number-table-hd :checkbox").attr('checked', 'checked');
            		multi = parseInt((self.$el.find(".chase-number-table-hd .follow-multi").val() || self.multiModifier.value), 10);
            		self.setChaseByI(i);
        			self.chases[i].multi = multi;
            		self.chases[i].money = multi * self._betMoney * self.betNum;
        			self.chaseMulti += multi;
            		self.chaseLength++;
            		$(this).parents('tr').find(".follow-multi").val(multi);
            		$(this).parents('tr').find(".follow-money").html(self.chases[i].money);
            	}else {
        			self.chaseMulti -= self.chases[i].multi;
            		delete self.chases[i];
            		self.chaseLength--;
            		$(this).parents('tr').find(".follow-multi").val('');
            		$(this).parents('tr').find(".follow-money").html('0');
            		self.$el.find(".chase-number-table-hd :checkbox").removeAttr('checked');
            	}
            	self.chaseMoney = self.chaseMulti * self._betMoney * self.betNum;
            	self.$el.find('.chase-number-table-hd .follow-issue').val(self.chaseLength);
            	self.$el.find(".chase-number-table-ft .fbig em:first").html(self.chaseLength);
            	self.$el.find(".chase-number-table-ft .fbig em:last").html(self.chaseMoney);
            });
            this.$el.on('keyup', ".chase-number-table-bd .follow-multi", function(){
            	var max = self.$el.find(".chase-number-table-hd .follow-multi").data('max');
            	
            	if ($(this).val().match(/\D/g) !== null) {//非法字符
            		$(this).val(1);
            	}else if(parseInt($(this).val()) >= max){
                	$(this).val(max);
                }else if (!$(this).val() || $(this).val() == 0){
                	$(this).val('');
                }
            	multi = parseInt($(this).val() || 0, 10);
            	if (!isNaN(multi)){
            		var i = $(this).parents('tr').attr('data-issue');
                	self.setChaseByBodyMulti($(this), multi, i);
            	}
            	
            });
            this.$el.find(".chase-number-table-ft :checkbox:first").click(function(){
            	if ($(this).attr('checked') == 'checked') {
            		self.setStatus = 1;
            	}else {
            		self.setStatus = 0;
            	}
            });
            this.$el.find('.commission').on('click', 'li', function(){
				$('.commission li').removeClass('cur');
				$(this).addClass('cur');
				self.commission = $(this).data('val');
				self.rgpctmin = (self.commission <= 5) ? 5 : self.commission;
				var buyMoney = Math.ceil(self.betMoney * self.rgpctmin / 100);
				self.buyMoney = buyMoney < self.buyMoney ? self.buyMoney : buyMoney;
				if ($('.guaranteeAll').attr('checked') || self.guarantee > self.betMoney - self.buyMoney) {
					self.guarantee = self.betMoney - self.buyMoney;
					$('.guaranteeAll').attr('checked', 'checked');
	            	self.renderGuarantee();
	            }
				self.renderBuyMoney();
			});
			this.$buyMoney.on('blur', function(){
				buyMoney = isNaN(parseInt($(this).val(), 10)) ? 0 : parseInt($(this).val(), 10);
				buyMoneymin = Math.ceil(self.betMoney * self.rgpctmin / 100);
				self.buyMoney = (buyMoney < buyMoneymin) ? buyMoneymin : (buyMoney > self.betMoney ? self.betMoney : buyMoney);
				if ($('.guaranteeAll').attr('checked') || self.guarantee > self.betMoney - self.buyMoney) {
					self.guarantee = self.betMoney - self.buyMoney;
					$('.guaranteeAll').attr('checked', 'checked');
	            	self.renderGuarantee();
	            }
				self.renderBuyMoney();
			});
			this.$el.find('.guarantee').on('blur', 'input.form-item-ipt', function(){
				guarantee = isNaN(parseInt($(this).val(), 10)) ? 0 : parseInt($(this).val(), 10);
				$('.guaranteeAll').removeAttr('checked');
				if (guarantee >= (self.betMoney - self.buyMoney)) {
					guarantee = self.betMoney - self.buyMoney;
					$('.guaranteeAll').attr('checked', 'checked');
				}
				self.guarantee = guarantee < 0 ? 0 : guarantee;
				self.renderGuarantee();
			});
			this.$el.find('.guaranteeAll').on('click', function(){
				self.guarantee = self.betMoney - self.buyMoney;
	            self.renderGuarantee();
			});
			this.$el.find('input[name=bmsz]').click(function(){
				self.openStatus = $(this).val();
			})
        },
        getCastOptions: function() {
            var self = this;
            var castStr = Lottery.toCastString(self.lotteryId, self.strings);
            var data = {
                codes: castStr,
                buy_source: 0,
                codes: castStr,
                lid: self.lotteryId,
                money: self.betMoney,
                multi: self.multiModifier.getValue(),
                bet_tnum: self.betNum,
                issue: self.issue,
                is_chase: 0,
                order_type: 0, 
                is_upload: 0
            };
            return data;
        },
        getCastOptions1: function() {
            var self = this;
            switch (self.orderType) {
	        	case 1:
	        		var endTime = '';
	            	for (i in self.chases) {
	                	if (endTime === ''){
	                		endTime = self.chases[i].show_end_time;
	                	}else {
	                		break;
	                	}
	                }
	            	var data = {
	                    money: self.chaseMoney,
	                    multi: self.chaseMulti,
	                    setStatus: self.setStatus,
	                    setMoney: self.setStatus == 1 ? self.setMoney : '',
	                    totalIssue: self.chaseLength,
	                    chases: Lottery.toChaseString(self.chases), endTime: endTime
	                };
	        		break;
	        	case 4:
	        		var data = {
	                    money: self.betMoney,
	                    multi: self.multiModifier.getValue(),
	                    issue: self.issue,
	                    endTime: hmDate.getFullYear()+"-"+padd(hmDate.getMonth() + 1)+"-"+padd(hmDate.getDate())+" "+padd(hmDate.getHours())+":"+padd(hmDate.getMinutes())+":"+padd(hmDate.getSeconds()),
	                    buyMoney: self.buyMoney,
	                    commissionRate: self.commission,
	                    guaranteeAmount: self.guarantee,
	                    openStatus: self.openStatus,
	                    openEndtime: realendTime
	                };
	        		break;
	        	case 0:
	        	default:
	        		var data = {money: self.betMoney, multi: self.multiModifier.getValue(), issue: self.issue, endTime: ENDTIME};
	        		break;
	        }
	        data.ctype = 'create';
	        data.buyPlatform = 0;
	        data.codes = Lottery.toCastString(self.lotteryId, self.strings, self.zj);
	        data.lid = self.lotteryId;
	        data.playType = 0;
	        data.betTnum = self.betNum;
	        data.isChase = self.zj ? 1 : 0;
	        data.orderType = self.orderType;
            return data;
        },
        setType: function(type) {
        	this.playType = type;
        	$(".bet-tab-hd li").removeClass('current');
        	$(".bet-tab-hd li[data-type="+type+"]").addClass('current');
        	$(".bet-pick-area, .bet-tab-bd-inner").hide();
        	$(".bet-pick-area."+type+", .bet-tab-bd-inner."+type).show();
        },
        setChaseMoney: function() {
        	this.chaseMoney = this._betMoney * this.betNum * this.chaseMulti;
        	this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
        	for (i in this.chases) {
        		this.chases[i].money = this.chases[i].multi *　this.betNum * this._betMoney;
            	$(".chase-number-table-bd tbody tr[data-issue="+i+"] .follow-money").html(this.chases[i].money);
            }
        },
        setChaseMulti: function(multi) {
        	this.chaseMulti = 0;
        	$(".chase-number-table-hd .follow-multi").val(multi);
        	for (i in this.chases) {
        		this.chaseMulti += multi;
        		this.chases[i].multi = multi;
        		this.chases[i].money = multi *　this.betNum * this._betMoney;
        		$(".chase-number-table-bd tbody tr[data-issue="+i+"] .follow-multi").val(multi);
            	$(".chase-number-table-bd tbody tr[data-issue="+i+"] .follow-money").html(this.chases[i].money);
            }
        	this.chaseMoney = this._betMoney * this.betNum * this.chaseMulti;
        	this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
        },
        setChaseByIssue: function(num, multi) {
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
        				this.chases[i].money = multi * this._betMoney * this.betNum;
        				this.chaseMulti += multi;
                		j++;
        			}else {
        				break;
        			}
        		}
        		this.chaseMoney = this.chaseMulti * this._betMoney * this.betNum;
        		this.$el.find(".chase-number-table-bd tbody").html(tbstr);
        	}
        	this.renderChase(issue);
        	this.$el.find(".chase-number-table-hd :checkbox").attr('checked', 'checked');
        	this.$el.find(".chase-number-table-hd .follow-multi").val(multi);
        	this.$el.find(".chase-number-table-ft .fbig em:first").html(num);
        	this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
        },
        setChaseByI: function(i) {
        	if (!this.chases[i])  this.chases[i] = {};
        	this.chases[i].award_time = chases[i].award_time;
    		this.chases[i].show_end_time = chases[i].show_end_time;
        },
        setChaseByBodyMulti: function(el, multi, i) {
        	if (this.chases[i]) {
        		this.chaseMulti -= this.chases[i].multi;
        		this.chaseLength --;
        		delete this.chases[i];
        	}
        	if (multi > 0) {
        		this.setChaseByI(i);
        		this.chases[i].multi = multi;
        		this.chases[i].money = multi * this._betMoney * this.betNum;
        		this.chaseMulti += multi;
        		this.chaseLength ++;
        		el.parents('tr').find(':checkbox').attr('checked', 'checked');
        	}else {
        		el.parents('tr').find(':checkbox').removeAttr('checked', 'checked');
        	}
        	this.chaseMoney = this.chaseMulti * this._betMoney * this.betNum;
			el.parents('tr').find('.follow-money').html(multi * this._betMoney * this.betNum);
			this.$el.find('.chase-number-table-hd .follow-issue').val(this.chaseLength);
			this.$el.find(".chase-number-table-ft .fbig em:first").html(this.chaseLength);
			this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
        },
        renderChase: function(issue) {
        	var tbstr = '', j = 1, self = this;
        	$.each(issue, function(i, e){
        		multi = self.chases[e] ? self.chases[e].multi : ($(".follow-multi:first").val() || self.multiModifier.value);
				tbstr += '<tr data-issue="'+e+'"><td>'+j+'</td><td class="tal"><input type="checkbox"';
        		if (self.chases[e]) tbstr += ' checked="checked"';
        		tbstr += '>'+e+'期';
        		if (e == self.issue) tbstr += ' <span class="main-color-s">（当前期）</span>';
        		tbstr += '</td><td><input type="text"';
        		if (self.chases[e]) tbstr += ' value="'+multi+'"';
        		tbstr += ' class="ipt-txt follow-multi">倍</td><td><span class="main-color-s follow-money">';
        		if (self.chases[e]) {
        			tbstr += multi * self._betMoney * self.betNum;
        		}else {
        			tbstr += '0';
        		}
        		tbstr += '</span>元</td><td>'+chases[e].award_time.substring(0, 16)+'</td></tr>';
        		j++;
        	})
    		this.$el.find(".chase-number-table-bd tbody").html(tbstr);
    		if (this.$el.find(".chase-number-table-bd :checkbox[checked!='checked']").length == 0) $(".chase-number-table-hd :checkbox").attr('checked', 'checked');
        },
        setIssue: function(issue){
        	this.issue = issue
        },
        add: function(balls) {
            this.autoId += 1;
            this.strings[this.autoId] = balls;
            this.$castList.prepend(this.renderString(balls.balls, this.autoId, balls.betNum));
            this.betNum += balls.betNum;
            this.setChaseMoney();
            this.renderAllBet();
        },
        addAll: function(balls) {
        	var self = this;
        	$.each(balls, function(i, e){
        		self.autoId += 1;
        		self.strings[self.autoId] = e;
        		self.$castList.prepend(self.renderString(e.balls, self.autoId, e.betNum, false, false, e.playType));
        		self.betNum += e.betNum;
        	})
        	this.setChaseMoney();
            this.renderAllBet();
        },
        edit: function(balls, id) {
        	this.strings[id] = balls;
        	var betNum = parseInt(this.$castList.find("li[data-index="+id+"]").find('span.bet-money').html().replace(/[^0-9]/ig,""))/this._betMoney;
            this.$castList.find("li[data-index="+id+"]").replaceWith(this.renderString(balls.balls, id, balls.betNum, true));
            this.betNum += balls.betNum-betNum;
            this.setChaseMoney();
            this.renderAllBet();
            this.clearButton();
        },
        rand: function(amount) {
            var randStr = '';
            for (var i = 0; i < amount; ++i) {
                randStr = boxes[self.playType].rand().join(' ');
                this.add(randStr);
            }
        },
        renderAllBet: function() {
            this.$betNum.html(this.betNum);
            this.renderBetMoney();
        },
        renderBetMoney: function() {
            this.betMoney = this._betMoney * this.multiModifier.getValue() * this.betNum;
            var buyMoney = Math.ceil(this.betMoney * this.rgpctmin / 100);
            this.buyMoney = (buyMoney <= this.buyMoney && this.buyMoney <= this.betMoney) ? this.buyMoney : (buyMoney > this.buyMoney ? buyMoney : this.betMoney);
            if ($('.guaranteeAll').attr('checked') || this.guarantee > this.betMoney - this.buyMoney) {
            	this.guarantee = this.betMoney - this.buyMoney;
            }
            this.$betMoney.html(this.betMoney);
            this.renderGuarantee();
            this.renderBuyMoney();
        },
        renderBuyMoney: function() {
        	this.$buyMoney.val(this.buyMoney).parents('.buyMoney').find('span em').html(this.rgpctmin);
        	this.buyMoney > 0 ? this.$buyMoney.parents('.buyMoney').find('u').show().find('em').html(Math.floor(this.buyMoney * 100/this.betMoney)) : this.$buyMoney.parents('.buyMoney').find('u').hide();
			$('.guarantee').find('span em:first').html(this.betMoney - this.buyMoney);
			$('.buy_txt').html("<em class='main-color-s'>"+(this.buyMoney+this.guarantee)+"</em> 元 <span>（认购"+this.buyMoney+"元+保底"+this.guarantee+"元）</span></span>");
        },
        renderGuarantee: function() {
        	$('.guarantee input.form-item-ipt').val(this.guarantee).parents('.guarantee').find('span em:last').html(this.betMoney == 0 ? 0 : Math.floor(this.guarantee * 100 / this.betMoney));
        	$('.buy_txt').html("<em class='main-color-s'>"+(this.buyMoney+this.guarantee)+"</em> 元 <span>（认购"+this.buyMoney+"元+保底"+this.guarantee+"元）</span></span>");
        },
        removeAll: function() {
            this.strings = {};
            this.betNum = 0;
            this.$castList.empty();
            this.setChaseMoney();
            this.renderAllBet();
        },
        remove: function(index) {
            var selected = this.strings[index];
            this.betNum -= selected.betNum;
            delete this.strings[index];
            this.setChaseMoney();
            this.renderAllBet();
        },
        setBetMoney: function(){
        	var self = this;
        	$('.cast-list').find('li').each(function(){
        		var preballs = [];
        		var posballs = [];
        		var balls;
        		var combine = 0;
        		balls = $.trim($(this).find('span.num-red:first').html()).replace(/[^0-9]/g, ',');
        		balls = balls.split(',');
        		for(pos in balls){
        			preballs.push($.trim(balls[pos]));
        		}
        		balls  = $.trim($(this).find('span.num-blue:first').html()).replace(/[^0-9]/g, ',');
        		balls = balls.split(',');
        		for(pos in balls){
        			posballs.push($.trim(balls[pos]));
        		}
        		combine = cx.Math.combine(preballs.length, 5) * cx.Math.combine(posballs.length, 2);
        		$(this).find('.bet-money').html(combine * self._betMoney + '元');
        	});
        },
        clearButton: function(){
        	this.boxes[this.playType].edit = 0;
    		$(".add-basket").html('添加到投注区<i class="icon-font">&#xe614;</i>');
        },
        renderString: function(allBalls, index, betnum, hover) {
        	var tpl = '<li ';
        	if(hover) tpl += ' class="hover"'
        	tpl += ' data-index="'+index+'"><span class="bet-type">';
        	if ($.inArray(this.lotteryId, [Lottery.PLS, Lottery.FCSD]) > -1) tpl += cx.Lottery.getPlayTypeName(this.lotteryId, cx.Lottery.playTypes[this.lotteryId][this.playType]);
        	if (betnum > 1) {
        		tpl += '复式';
        	} else {
        		tpl += '单式';
        	}
        	tpl += '</span><div class="num-group">';
            var ballTpl = [], self = this;
            $.each(allBalls, function(pi, ele){
            	var tmpTpl = '<span class="num-red">';
            	ele.sort(function(a, b){
					a = parseInt(a, 10);
					b = parseInt(b, 10);
					return a > b ? 1 : ( a < b ? -1 : 0 );
				});
            	$.each(ele, function(bi, e){
            		if(self.lotteryId == Lottery.QLC) {
                		tmpTpl += pad(e) + ' ';
                	}else {
                		tmpTpl += e + ' ';
                	}
            		function pad(i) {
                        i = '' + i;
                        if (i.length < 2) i = '0' + i;
                        return i;
                    }
            	})
                tmpTpl += '</span>';
                ballTpl.push(tmpTpl);
            })
            ballTpl = ballTpl.join('<em>|</em>');
            tpl += ballTpl+'</div><a href="javascript:;" class="remove-str">删除</a>';
            if (this.playType !== 'z3') tpl += '<a href="javascript:;" class="modify-str">修改</a>';
            tpl += '<span class="bet-money">'+ betnum * this._betMoney +'元</span></li>';

            return tpl;
        }
    };

})();
function chgbtn () {
	if (selling == 2 && ($.inArray(cx._basket_.orderType, [0, 1]) > -1 || (hmselling == 1 && hmendTime * 1000 >= (new Date()).valueOf()))) {
		$("[id^=pd][id$=_buy]").removeClass('btn-disabled').addClass('needTigger submit').html('确认预约');
		$('body').find('#buy_tip').remove();
	}else if(selling == 1) {
		$("[id^=pd][id$=_buy]").removeClass('needTigger submit').addClass('btn-disabled').html('期次更新中');
		if ($("[id^=pd][id$=_buy]").next('#buy_tip').length == 0) $("[id^=pd][id$=_buy]").after("<p id='buy_tip' class='main-color' style='margin: 4px 0 6px'>（下一期开售时间为"+realendTime.match(/\d{2}:\d{2}/)+"）</p>")
	}else {
		$("[id^=pd][id$=_buy]").removeClass('needTigger submit').addClass('btn-disabled').html('暂停预约');
		$('body').find('#buy_tip').remove();
	}
}
$(function(){
	$('.hmendTime .form-item-txt').html(hmDate.getFullYear() + "-" + padd(hmDate.getMonth() + 1) + "-" + padd(hmDate.getDate()) + " " + padd(hmDate.getHours()) + ":" + padd(hmDate.getMinutes()) + ":" + padd(hmDate.getSeconds()));
	chgbtn();
//投注栏点击修改添加hover状态
  $('.cast-list').on('click', '.modify-str', function(){
  	$(this).parents('.cast-list').find('li').removeClass('hover');
    $(this).parent('li').addClass('hover');
  })
  
//追号切换
  $('.buy-type-hd').on('click', 'li', function(){
	  if ($(this).index() == 1) {
    	  cx._basket_.orderType = 1;
          $(this).find('.ptips-bd').hide();
          var str = '连续多期购买同一个（组）号码<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>追号：</em>选好投注号码后，对期数、期<br>号、倍数进行设置后，系统按照设置<br>进行购买。">&#xe613;</i>';
          $(".chase-number-notes").html(str);
      }else if ($(this).index() == 2){
    	  cx._basket_.orderType = 4;
    	  $(this).find('.ptips-bd').hide();
    	  var str = '多人出资购买彩票，奖金按购买比例分享<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>合买：</em>选好投注号码后，由多人出资<br>购买彩票。中奖后，奖金按购买比例<br>分享。">&#xe613;</i>';
          $(".chase-number-notes").html(str);
      }else{
    	  cx._basket_.orderType = 0;
          $('#ordertype1').parents("span").find('.ptips-bd').show();
          var str = '由购买人自行全额购买彩票，独享奖金<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>自购：</em>选好投注号码后，由自己全额<br>支付购彩款。中奖后，自己独享全部<br>税后奖金。">&#xe613;</i>';
          $(".chase-number-notes").html(str);
      }
	  chgbtn();
  })
  $('.chase-number-notes, .guarantee').on('mouseenter', '.bubble-tip', function(){
      $.bubble({
          target:this,
          position: 'b',
          align: 'l',
          content: $(this).attr('tiptext'),
          width:'auto'
      })
  }).on('mouseleave', '.bubble-tip', function(){
      $('.bubble').hide();
  });
  
  $(".bet-tab-hd ul").tabPlug({
      cntSelect: '.bet-tab-bd',
      menuChildSel: 'li',
      onStyle: 'current',
      cntChildSel: '.bet-tab-bd-inner',
      eventName: 'click'
  });
});
