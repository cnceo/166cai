(function() {
	
    window.cx || (window.cx = {});
    cx.closeCount = false;
    var Lottery = cx.Lottery = (function() {

        var me = {
            DLT: 23529,
            QLC: 23528,
            QXC: 10022,
            SYXW: 21406,
            SSQ: 51,
            PL3: 33,
            PLS: 33,
            PL5: 35,
            PLW: 35,
            JCZQ: 42,
            JCLQ: 43,
            FCSD: 52,
            PLS:33,
            PLW:35
        };

        function _getNumberSeparator(lotteryId, playType) {
            var NUMBER_SEPARATOR = {};
            NUMBER_SEPARATOR[me.DLT] = {
                'default': ','
            };
            NUMBER_SEPARATOR[me.SSQ] = {
                'default': ','
            };
            NUMBER_SEPARATOR[me.SYXW] = {
                'default': ','
            };
            NUMBER_SEPARATOR[me.PLS] = {
                'default': '',
                'zx': '',
                'z3': ',',
                'z6': ','
            };
            NUMBER_SEPARATOR[me.PLW] = {
                'default': ''
            };
            NUMBER_SEPARATOR[me.QLC] = {
                'default': ','
            };
            NUMBER_SEPARATOR[me.QXC] = {
                'default': ''
            };
            NUMBER_SEPARATOR[me.FCSD] = {
                'default': '',
                'zx': '',
                'z3': ',',
                'z6': ','
            };

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
            PLACE_SEPARATOR[me.DLT] = {
                'default': '|'
            };
            PLACE_SEPARATOR[me.SSQ] = {
                'default': '|'
            };
            PLACE_SEPARATOR[me.PLS] = {
                'default': ',',
                'zx': ',',
                'z3': '',
                'z6': ''
            };
            PLACE_SEPARATOR[me.PLW] = {
                'default': ','
            };
            PLACE_SEPARATOR[me.QXC] = {
                'default': ','
            };
            PLACE_SEPARATOR[me.QLC] = {
                'default': ''
            };
            PLACE_SEPARATOR[me.FCSD] = {
                'default': ',',
                'zx': ',',
                'z3': '',
                'z6': ''
            };

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
            PADDING_ZERO[me.DLT] = {
                'default': true
            };
            PADDING_ZERO[me.SSQ] = {
                'default': true
            };
            PADDING_ZERO[me.SYXW] = {
                'default': true
            };
            PADDING_ZERO[me.PLS] = {
                'default': false
            };
            PADDING_ZERO[me.PLW] = {
                'default': false
            };
            PADDING_ZERO[me.FCSD] = {
                'default': false
            };
            PADDING_ZERO[me.QXC] = {
                'default': false
            };
            PADDING_ZERO[me.QLC] = {
                'default': true
            };

            var hasPadding = true;
            playType || (playType = 'default');
            if (lotteryId in PADDING_ZERO) {
                hasPadding = PADDING_ZERO[lotteryId]['default'];
            }

            return hasPadding;
        }

        me.playTypes = {
            23529: {
                'default': 1,
                zj: 2 
            },
            51: {
                'default': 1
            },
            21406: {
                'default': '05',
                q1: '01',
                rx2: '02',
                rx3: '03',
                rx4: '04',
                rx5: '05',
                rx6: '06',
                rx7: '07',
                rx8: '08',
                qzhi2: '09',
                qzhi3: '10',
                qzu2: '11',
                qzu3: '12'
            },
            52:{
                'default':'1',
                zx:'1',
                z3:'2',
                z6:'3'
            },
            33:{
                'default':'1',
                zx:'1',
                z3:'2',
                z6:'3'
            },
            35:{
                'default': 1
            },
            10022:{
                'default': 1
            },
            23528:{
                'default': 1
            }
        };

        function _getCastPost(lotteryId, playType) {
            var CAST_POST = {};
            CAST_POST[me.DLT] = {
                'default': '1'
            };
            CAST_POST[me.SSQ] = {
                'default': '1'
            };
            CAST_POST[me.SYXW] = {
                'default': '01'
            };
            CAST_POST[me.PLS] = {
                'default': '1',
                'zx': '1',
                'z3': '3',
                'z6': '3'
            };
            CAST_POST[me.FCSD] = {
                'default': '1',
                'zx': '1',
                'z3': '3',
                'z6': '3'
            };
            CAST_POST[me.PLW] = {
                'default': '1'
            };

            var post = '1';
            playType || (playType = 'default');
            if (lotteryId in CAST_POST) {
                if (playType in CAST_POST[lotteryId]) {
                    post = CAST_POST[lotteryId][playType];
                }
            }

            return post;
        }

        me.getPlayTypeName = function(lotteryId, playType) {
            var cnName = '';
            var playCnNames = {};
            if (lotteryId === me.SYXW) {
                playCnNames = {
                    '01': '前1',
                    '02': '任选二',
                    '03': '任选三',
                    '04': '任选四',
                    '05': '任选五',
                    '06': '任选六',
                    '07': '任选七',
                    '08': '任选八',
                    '09': '前二直选',
                    '10': '前三直选',
                    '11': '前二组选',
                    '12': '前三组选'
                };
                cnName = playCnNames[playType];
            } else if (lotteryId === me.PLS) {
                playCnNames = {
                    1: '直选',
                    2: '组三',
                    3: '组六'
                };
                cnName = playCnNames[playType];
            } else if (lotteryId === me.FCSD) {
                playCnNames = {
                    1: '直选',
                    2: '组三',
                    3: '组六'
                };
                cnName = playCnNames[playType];
            } else if(lotteryId === me.DLT){
            	playCnNames = {
            		0: '普通',
            		1: '普通',
                    2: '普通 追加'
                };
            	cnName = playCnNames[playType];
            }
            else {
                cnName = '普通';
            }

            return cnName;
        }

        me.getCnName = function(lotteryId) {
            lotteryId = parseInt(lotteryId, 10);
            return {
                23529: '大乐透',
                10022: '七星彩',
                23528: '七乐彩',
                51: '双色球',
                52: '福彩3D',
                54: '11选5',
                21406: '11运夺金',
                42: '竞彩足球',
                43: '竞彩篮球',
                35: '排列5',
                33: '排列3',
                41:'北单',
                11: '胜负彩',
                19: '任选9'
            }[lotteryId];
        };

        me.getRule = function(lotteryId, playType) {
            if (lotteryId == Lottery.DLT) {
                return '请选择5-35个红球和2-12个蓝球';
            } else if (lotteryId == Lottery.SSQ) {
                return '请选择6-33个红球和1-16个蓝球';
            } else if (lotteryId == Lottery.SYXW) {
                var rule = '';
                switch (playType) {
                    case 'rx2':
                        rule = '至少选择2个球';
                        break;
                    case 'rx3':
                        rule = '至少选择3个球';
                        break;
                    case 'rx4':
                        rule = '至少选择4个球';
                        break;
                    case 'rx5':
                        rule = '至少选择5个球';
                        break;
                    case 'rx6':
                        rule = '至少选择6个球';
                        break;
                    case 'rx7':
                        rule = '至少选择7个球'
                        break;
                    case 'rx8':
                        rule = '至少选择8个球';
                        break;
                    case 'q1':
                        rule = '每位至少选择1个球';
                        break;
                    case 'qzhi2':
                        rule = '每位至少选择1个球，且相互不重复';
                        break;
                    case 'qzu2':
                        rule = '至少选择2个球';
                        break;
                    case 'qzhi3':
                        rule = '每位至少选择1个球，且相互不重复';
                        break;
                    case 'qzu3':
                        rule = '至少选择3个球';
                        break;
                    default:
                    break;
                }
                return rule;
            } else if (lotteryId == Lottery.FCSD){
                var rule = '';
                switch(playType){
                    case 'zx':
                        rule = '每位至少选择一个球';
                        break;
                    case 'z3':
                        rule = '至少选择2个球';
                        break;
                    case 'z6':
                        rule = '至少选择3个球';
                        break;
                    default:
                    break;
                }
                return rule;
            } else if (lotteryId == Lottery.PLS){
                var rule = '';
                switch(playType){
                    case 'zx':
                        rule = '每位至少选择一个球';
                        break;
                    case 'z3':
                        rule = '至少选择2个球';
                        break;
                    case 'z6':
                        rule = '至少选择3个球';
                        break;
                    default:
                    break;
                }
                return rule;
            }else if(lotteryId == Lottery.PLW){
                return '每位至少选择一个球';
            }else if(lotteryId == Lottery.QXC){
                return '每位至少选择一个球';
            }else if(lotteryId == Lottery.QLC){
                return '至少选择7个球';
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

            if (lotteryId === me.DLT || lotteryId === me.SSQ) {
                var pre = numbers.split('|')[0];
                var post = numbers.split('|')[1];
                if (hasDan) {
                    preDan = pre.split('$')[0].split(',');
                    preTuo = pre.split('$')[1].split(',');
                    var postSplit = post.split('$');
                    if (postSplit.length === 2) {
                        postDan = postSplit[0].split(',');
                        postTuo = postSplit[1].split(',');
                    } else {
                        postDan = [];
                        postTuo = postSplit[0].split(',');
                    }
                } else {
                    preTuo = pre.split(',');
                    postTuo = post.split(',');
                }
            } else if (lotteryId === 33 || lotteryId === 35 || lotteryId === 52 || lotteryId === 10022 || lotteryId === 23528) {
                preTuo = numbers;
            } else if (lotteryId === 11 || lotteryId === 19 ) {
                preTuo = numbers;
            } else if (lotteryId === 21406) {
                if (playType == '09' || playType == '10') {
                    preTuo = numbers.split('|');
                } else {
                    //preTuo = numbers.split(',');
                    preTuo = numbers;
                }
            } else {
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
				if( lotteryId == 21406 ){
					// 获取玩法, 如果是 直选的要 找出对应的奖号, 显示加红TODO
					if( playTypeCode == '01' ){ // 前一
						$(number.split(',')).each(function(k1, n1) {
							if ( ( $.inArray(n1, parsedAward.preCode.slice(0, 1)) > -1 && key < 1 ) || parsedAward.hasAward === false) {
								preTpl += me.renderRedDetail(n1);
							} else {
								preTpl += me.renderGrayDetail(n1);
							}
						});
					} else if ( playTypeCode == '09' ){ // 前二直选
						// 每位各选1或多个号码，选号与开奖号码前两位号码相同（且顺序一致）
						if( preTpl != '' ) {
							preTpl += '<em>|</em>';
						}
						$(number.split(',')).each(function(k1, n1) {
							if ( ( n1 == parsedAward.preCode[key] && key < 2 ) || parsedAward.hasAward === false) {
								preTpl += me.renderRedDetail(n1);
							} else {
								preTpl += me.renderGrayDetail(n1);
							}
						});
					} else if(playTypeCode == '10' ){ // 前三直选
						if( preTpl != '' ) {
							preTpl += '<em>|</em>';
						}
						$(number.split(',')).each(function(k1, n1) {
							if ( ( n1 == parsedAward.preCode[key] && key < 3 ) || parsedAward.hasAward === false) {
								preTpl += me.renderRedDetail(n1);
							} else {
								preTpl += me.renderGrayDetail(n1);
							}
						});
					} else if(playTypeCode == '11' ){ // 前二组选
						// 从1～11中任选3或多个号码，选号与开奖号码前三位相同（顺序不限）
						$(number.split(',')).each(function(k1, n1) {
							if ( ( $.inArray(n1, parsedAward.preCode.slice(0, 2)) > -1 && key < 2 ) || parsedAward.hasAward === false) {
								preTpl += me.renderRedDetail(n1);
							} else {
								preTpl += me.renderGrayDetail(n1);
							}
						});

					} else if(playTypeCode == '12' ){ // 前三组选
						$(number.split(',')).each(function(k1, n1) {
							if ( ( $.inArray(n1, parsedAward.preCode.slice(0, 3)) > -1 && key < 3 ) || parsedAward.hasAward === false ) {
								preTpl += me.renderRedDetail(n1);
							} else {
								preTpl += me.renderGrayDetail(n1);
							}
						});
					} else {
						$(number.split(',')).each(function(k1, n1) {
							if ($.inArray(n1, parsedAward.preCode) > -1 || parsedAward.hasAward === false) {
								preTpl += me.renderRedDetail(n1);
							} else {
								preTpl += me.renderGrayDetail(n1);
							}
						});
					}

                } else if ( lotteryId == 33 ||  // 排列3
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

        me.toCastString = function(lotteryId, subStrings, playType) {
            playType || (playType = 'default');
            var midStr = ':' + me.playTypes[lotteryId][playType];
            var postStr = ':' + _getCastPost(lotteryId, playType);

            var betStr = [];
            var singleBet;
            var ballStr = [];
            var preStr = [];
            for (var k in subStrings) {
                singleBet = subStrings[k].balls;
                preStr = [];
                for (var j = 0; j < singleBet.length; ++j) {
                    ballStr = [];
					singleBet[j].sort(function(x, y) {
						return parseInt(x, 10) > parseInt(y, 10);
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

        me.renderBasket = function() {
        };

        me.getPlayType = function() {
        };

        return me;
    })();

    var BoxCollection = cx.BoxCollection = function(selector) {
        this.$el = $(selector);
        this.boxes = [];
        this.betMoney = 2;
    };

    BoxCollection.prototype = {
        add: function(box) {
            this.boxes.push(box);
            box.setCollection(this);
        },
        addBall: function(boxs) {
        	var editStrs = {
                balls: [],
                betNum: 0
            };
        	$(this.boxes).each(function(k, box) {
        		box.removeAll();
        		for (var i = 0; i < boxs[k].length; ++i)
             	{
        			box.addBall(boxs[k][i]);
             	}
        		editStrs.balls.push(box.balls);
            });
        	editStrs.betNum = this.calcBetNum();
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
            this.$el.find('.bet-num').html(this.calcBetNum());
            this.$el.find('.bet-money').html(this.calcMoney());
            if (this.isValid()) {
                this.$el.find('.add-basket').removeClass('disabled');
            } else {
                this.$el.find('.add-basket').addClass('disabled');
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
                betNum: betNum
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
        var mid = Math.ceil(this.options.amount / 2);
        for (var i = this.minBall; i <= this.options.amount; ++i) {
            this.all.push(i);
            if (i % 2 == 0) {
                this.evens.push(i);
            } else {
                this.odds.push(i);
            }
            if (i >= mid) {
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
            this.$balls = self.$el.find('.balls li');
            this.$balls.click(function() {
                var $this = $(this);
                var ball = $this.html();
                if ($this.hasClass('selected')) {
                    $this.removeClass('selected');
                    self.removeBall(ball);
                } else {
                    $this.addClass('selected');
                    self.addBall(ball);
                }
                self.collection.renderBet();
            }).hover(function() {
                $(this).addClass('hover');
            }, function() {
                $(this).removeClass('hover');
            });
            this.$el.find('.clear-balls').click(function() {
                self.removeAll();
            });
            this.$el.find('.filter-bigs').click(function() {
                self.removeAll();
                var ball;
                for (var i = 0; i < self.bigs.length; ++i) {
                    ball = self.bigs[i];
                    self.$balls.eq(ball - self.minBall).trigger('click');
                }
            });
            this.$el.find('.filter-smalls').click(function() {
                self.removeAll();
                var ball;
                for (var i = 0; i < self.smalls.length; ++i) {
                    ball = self.smalls[i];
                    self.$balls.eq(ball - self.minBall).trigger('click');
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
            this.$el.find('.filter-odds').click(function() {
                self.removeAll();
                var ball;
                for (var i = 0; i < self.odds.length; ++i) {
                    ball = self.odds[i];
                    self.$balls.eq(ball - self.minBall).trigger('click');
                }
            });
            this.$el.find('.filter-evens').click(function() {
                self.removeAll();
                var ball;
                for (var i = 0; i < self.evens.length; ++i) {
                    ball = self.evens[i];
                    self.$balls.eq(ball - self.minBall).trigger('click');
                }
            });
            this.$el.find('.filter-all').click(function() {
                self.removeAll();
                var ball;
                for (var i = 0; i < self.all.length; ++i) {
                    ball = self.all[i];
                    self.$balls.eq(ball - self.minBall).trigger('click');
                }
            });
        },
        setCollection: function(collection) {
            this.collection = collection;
        },
        isValid: function() {
            return this.balls.length >= this.options.min;
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
                        if (flag) {
                            cb(j);
                        }
                    } else {
                        cb(j);
                    }
                }
            }
			this.error = false;
            return this.balls.sort(function(x, y) {
                return parseInt(x, 10) > parseInt(y, 10);
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
                            if ($.inArray(j, mutexBoxes[k].balls) !== -1) {
                                errStr = '互斥错';
                            }
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
            if (this.balls.length < this.options.min) {
                return 0;
            }
            var combCount = cx.Math.combine(this.balls.length, this.options.min);
            if (this.options.playType == 'z3') {
                combCount *= 2;
            }
            return combCount;
        },
        joinBalls: function() {
            return this.balls.join(',');
        },
        getBalls: function() {
            return this.balls;
        },
        addBall: function(i) {
        	i = parseInt(i, 10);
            if ($.inArray(i, this.balls) > -1) {
                return ;
            }
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
            if (index == -1) {
                return ;
            }
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
            if (this.options.mutex == null) {
                return [];
            }
            if (!this.mutexBoxes) {
                var boxes = this.collection.getBoxes();
                var index = $.inArray(this, boxes);
                var box;
                var i = 0
                this.mutexBoxes = [];
                for (; i < boxes.length; ++i) {
                    box = boxes[i];
                    if ((index !== i) && (box.options.mutex === this.options.mutex)) {
                        this.mutexBoxes.push(boxes[i]);
                    }
                }
            }
            return this.mutexBoxes;
        }
    };

    var BallBoxView = cx.BallBoxView = function(selector, options) {
        this.$self = $(selector);
        this.model = options.model;
        this.splitEvent = /^(\S+)\s*(.*)$/;
        this.events = options.events || {};

        this.init();
    };

    BallBoxView.prototype = {
        init: function() {
            this.model.change = $.proxy(this.render, this);
            this.deleteEvents();
        },
        render: function() {
        },
        deleteEvents: function() {
            var match, eventName, selector, method;
            for (var key in this.events) {
                method = events[key];
                match = key.match(this.splitEvent);
                eventName = match[1];
                selector = match[2];
                this.$self.on(eventName, selector, method);
            }
            return this;
        }
    };

    var CastBasket = cx.CastBasket = function(selector, options) {
        this.$el = $(selector);
        this.lotteryId = options.lotteryId;
        this.boxes = options.boxes;
        this.issue = options.issue;
        this.multiModifier = options.multiModifier;
        this.zhModifier = options.zhModifier;
        this.strings = {};
        this.autoId = 0;
        this.$castList = this.$el.find('.cast-list');
        this.$betNum = this.$el.find('.total-bet-num');
        this.$betMoney = this.$el.find('.total-bet-money');
        this.betNum = 0;
        this.betMoney = 0;
        this._betMoney = 2;
        this.playType = options.playType || 'default';
        this.getCastOptions = this[options.getCastOptions] || this.getCastOptions;
        this.init();
    };

    CastBasket.prototype = {
        init: function() {
            var self = this;
            this.multiModifier.setCb(function() {
                self.renderBetMoney();
            });
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
                if (self.boxes.isValid()) {
                    var balls = self.boxes.getAllBalls();
                    self.add(balls);
                    self.boxes.removeAll();
                    $('html body').animate({scrollTop: $('.userLottery').offset().top});
                } else {
                    cx.Alert({
                        content: cx.Lottery.getRule(self.lotteryId, self.playType)
                    });
                }
            });
            this.$el.find('.rand-cast').click(function() {
                var $this = $(this);
                var amount = parseInt($this.data('amount'), 10);
                var rand;
                for (var i = 0; i < amount; ++i) {
                    rand = self.boxes.rand();
                    self.add(rand);
                }
            });
            this.$el.on('click', '.remove-str', function() {
                var $li = $(this).closest('li');
                $li.remove();
                self.remove($li.data('index'));
            });
            this.$el.on('click', '.modify-str', function() {
	              var preballs = $(this).parent().find('strong').html();
	              self.boxes.boxes[0].removeAll();
	              $(preballs.split(' ')).each(function(k, ball){
	            	  ball = parseInt(ball, 10);
	            	  if(!isNaN(ball))
	            	  {
	            		  self.boxes.boxes[0].addBall(ball + '') ;
	                      self.boxes.boxes[0].$balls.eq(ball - 1).addClass('selected'); 
	            	  }
	              })
	              var postballs = $(this).parent().find('span').html();
	              self.boxes.boxes[1].removeAll();
	              $(postballs.split(' ')).each(function(k, ball){
	           	      ball = parseInt(ball, 10);
		           	  if(!isNaN(ball))
		           	  {
		           		  self.boxes.boxes[1].addBall(ball + '') ;
		                  self.boxes.boxes[1].$balls.eq(ball - 1).addClass('selected'); 
		           	  }
		          })
	              self.boxes.renderBet();
            });
            this.$el.find('.clear-list').click(function() {
                self.removeAll();
            });

            this.$el.find('.submit').click(function(e) {
                var $this = $(this);
                
                if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
                    cx.PopLogin.show();
                    return ;
                }

                if ($this.hasClass('not-bind')) {
                    cx.PopBind.show();
                    return ;
                }
				
                var data = self.getCastOptions();
                data.isToken = 1;
                if (data.money == 0) {
					new cx.Alert({content: "请先选择你要购买的号码"});
                    return ;
                }
                // 最大金额前端限制
                var maxMoneyLid_2w = [35,51,10022,23528,23529];
                if ($.inArray(data.lid,maxMoneyLid_2w) >= 0){
                    if ( data.money >20000 ) {
                        new cx.Alert({content: "订单金额需小于2万，请修改订单后重新投注"});
                        return ;
                    }
                }else{
                    if ( data.money >200000 ) {
                        new cx.Alert({content: "订单金额需小于20万，请修改订单后重新投注"});
                        return ;
                    }
                }
				
                if (self.$el.find(".ipt_checkbox#agreenment").get(0) && !self.$el.find(".ipt_checkbox#agreenment").attr("checked"))
                    return void new cx.Alert({content: "请先阅读并同意《委托投注规则》后才能继续"});

                if (self.$el.find(".ipt_checkbox#agreenment1").get(0) && !self.$el.find(".ipt_checkbox#agreenment1").attr("checked"))
                    return void new cx.Alert({content: "请先阅读并同意《委托投注规则》后才能继续"});

                if (self.$el.find(".ipt_checkbox#agreenment2").get(0) && !self.$el.find(".ipt_checkbox#agreenment2").attr("checked"))
                    return void new cx.Alert({content: "请先阅读并同意《限号投注风险须知》后才能继续"});

                cx.ajax.post({
                    url: 'order/create',
                    data: data,
                    success: function(response) {
                        if(response.code == 0)
                        { 
                            var datas = {
                                ctype: 'pay',
                                orderId: response.data.orderId,
                                money: response.data.money
                            };
                            new cx.Confirm({
                                title: '确认购彩',
								content: betInfo.number( 
									cx.Lottery.getCnName(self.lotteryId), 
									cx.Lottery.getPlayTypeName( self.lotteryId, cx.Lottery.playTypes[self.lotteryId][self.playType] ),
									self.issue.getIssue(),
									response.data.money,
									response.data.remain_money
								),
                                input: 1,
                                confirmCb: function() {
                            		if(this.input){
                            			datas.pay_pwd = $('#pay_pwd').val();
                                        if($('#pay_pwd').data('encrypt') == '1'){
                                        	datas.pay_pwd = cx.rsa_encrypt( datas.pay_pwd );
            	           	           		 if(!datas.encrypt){
            	           	           			datas.encrypt = '';
            	           	           		 }
            	           	           		 if(datas.encrypt.indexOf('pay_pwd') == -1)
            	           	           			datas.encrypt += 'pay_pwd' + '|';
                                        }
                            		}
                                    cx.ajax.post({
                                        url: 'order/pay',
                                        data: datas,
                                        success: function(response) {
                                            cx.castCb(response, self, '.pwderror');
                                        }
                                    });
                                }
                            });
                        }else{
                            cx.castCb(response, self);
                        }
                    }
                });
            });
            this.$el.find('.has-chase').change(function() {
                var $this = $(this);
                var $zhModifier = self.$el.find('.zh-modifier');
                if ($this.attr('checked')) {
                    $zhModifier.show();
                } else {
                    $zhModifier.hide();
                }
            });
            this.$el.find('.start-crowd').click(function() {
                if ($(this).hasClass('not-login') || !$.cookie('name_ie')) {
                    return ;
                }
                if (self.betMoney > 0) {
                    var crowdBuy = new cx.CrowdBuy('.crowd-buy', self.getCastOptions());
                }
            });
        },
        getCastOptions: function() {
            var self = this;
            var castStr = Lottery.toCastString(self.lotteryId, self.strings, self.playType);
            var data = {
                codes: castStr,
                buy_source: 0,
                codes: castStr,
                lid: self.lotteryId,
                money: self.betMoney,
                multi: self.multiModifier.getValue(),
                bet_tnum: self.betNum,
                issue: self.issue.getIssue(),
                is_chase: 0,
                order_type: 0, 
                is_upload: 0
            };
            return data;
        },
        getCastOptions1: function() {
            var self = this;
            var castStr = Lottery.toCastString(self.lotteryId, self.strings, self.playType);
            var data = {
                ctype: 'create',
                buyPlatform: 0,
                codes: castStr,
                lid: self.lotteryId,
                money: self.betMoney,
                multi: self.multiModifier.getValue(),
                issue: self.issue.getIssue(),
                playType: parseInt(cx.Lottery.playTypes[self.lotteryId][self.playType], 10) || 0,
                betTnum: self.betNum,
                isChase: (self.playType == 'zj' ? 1 : 0),
                orderType: 0,
                endTime: self.issue.endTime
            };
            return data;
        },
        add: function(balls) {
            this.autoId += 1;
            this.strings[this.autoId] = balls;
            this.$castList.prepend(this.renderString(balls.balls, this.autoId, this._betMoney * balls.betNum));
            this.betNum += balls.betNum;
            this.betMoney = this._betMoney * this.betNum;
            this.renderAllBet();
        },
        rand: function(amount) {
            var randStr = '';
            for (var i = 0; i < amount; ++i) {
                randStr = boxes.rand().join(' ');
                this.add(randStr);
            }
        },
        renderAllBet: function() {
            this.$betNum.html(this.betNum);
            this.renderBetMoney();
        },
        renderBetMoney: function() {
            this.betMoney = this._betMoney * this.multiModifier.getValue() * this.betNum;
            this.$betMoney.html(this.betMoney);
        },
        removeAll: function() {
            this.strings = {};
            this.betNum = 0;
            this.betMoney = 0;
            this.$castList.empty();
            this.renderAllBet();
        },
        remove: function(index) {
            var selected = this.strings[index];
            this.betNum -= selected.betNum;
            this.betMoney = this._betMoney * this.betNum;
            delete this.strings[index];
            this.renderAllBet();
        },
        calcMoney: function() {
        },
        setBetMoney: function(){
        	var self = this;
        	$('.cast-list').find('li').each(function(){
        		var preballs = [];
        		var posballs = [];
        		var balls;
        		var combine = 0;
        		balls = $.trim($(this).find('strong').html()).replace(/[^0-9]/g, ',');
        		balls = balls.split(',');
        		for(pos in balls){
        			preballs.push($.trim(balls[pos]));
        		}
        		balls  = $.trim($(this).find('span').html()).replace(/[^0-9]/g, ',');
        		balls = balls.split(',');
        		for(pos in balls){
        			posballs.push($.trim(balls[pos]));
        		}
        		combine = cx.Math.combine(preballs.length, 5) * cx.Math.combine(posballs.length, 2);
        		$(this).find('.bet-money').html(combine * self._betMoney + '元');
        	});
        },
        renderString: function(allBalls, index, money) {
            var tpl = '<li data-index="' + index + '">';
            function pad(i) {
                i = '' + i;
                if (i.length < 2) {
                    i = '0' + i;
                }
                return i;
            }
            var ballTpl = [];
            for (var pi = 0; pi < allBalls.length; ++pi) {
                if (pi > 0 && (this.lotteryId == Lottery.DLT || this.lotteryId == Lottery.SSQ)) {
                    var tmpTpl = '<span>';
                } else {
                    var tmpTpl = '<strong>';
                }
                allBalls[pi].sort(function(a, b){
					a = parseInt(a, 10);
					b = parseInt(b, 10);
					return a > b ? 1 : ( a < b ? -1 : 0 );
				});
                for (var bi = 0; bi < allBalls[pi].length; ++bi) {
                    tmpTpl += pad(allBalls[pi][bi]) + ' ';
                }
                if (pi > 0 && (this.lotteryId == Lottery.DLT || this.lotteryId == Lottery.SSQ)) {
                    tmpTpl += '</span>';
                } else {
                    tmpTpl += '</strong>';
                }
                ballTpl.push(tmpTpl);
            }
            ballTpl = ballTpl.join('<em>|</em>');
            tpl += ballTpl;
            /*if(this.issue.lotteryId == cx.Lottery.DLT){
            	tpl += '<a class="modify-str">修改</a>';
            }*/
            tpl += '<a class="remove-str">删除</a><span class="bet-money">'+ money +'元</span>';
            tpl += '</li>';

            return tpl;
        }
    };

    var Issue = cx.Issue = function(selector, options) {
        this.$el = $(selector);
        this.issue = '';
        this.lotteryId = options.lotteryId;
        this.render = options.render;
        this.$currIssue = this.$el.find('.curr-issue');
        this.$countDown = this.$el.find('.count-down');
        this.$endTime = this.$el.find('.end-time');
        this.$weekDay = this.$el.find('.week-day');
        this.endTime = '';
        
        this.init();
    };

    Issue.prototype = {
        init: function() {
            this.requestIssue();
        },
        getIssue: function() {
            return this.issue;
        },
        requestIssue: function() {
            var self = this;
            cx.ajax.get({
                url: cx.url.getBaseUrl('api/data/getNumCurrent'),
                data: {
                    lid: self.lotteryId,
                    state: 100,
                    ci: 1,
                    t: (new Date()).valueOf(),
                    cache_2345caipiao: 'lid-' + self.lotteryId + '_state-100_ci-1:1:1'
                },
                success: function(response) {
                    if (response.code != 0) {
                        return ;
                    }
                    var data = response.data;
                    self.issue = response.data.seExpect;
                    self.$currIssue.html(response.data.seExpect);
                    self.$endTime.html(cx.Datetime.format(response.data.seFsendtime, {
                        year: false,
                        seconds: false
                    }));
                    self.endTime = cx.Datetime.format(response.data.seFsendtime, {
                        year: true,
                        seconds: true
                    });
                    self.$weekDay.html(cx.Datetime.getWeekDay(response.data.seFsendtime));
                    if (self.$countDown.length > 0) {
                        var counter = new cx.Counter({
                            start: data.seFsendtime - data.nowTime - 1000
                        });
                        if (!self.render) {
                            self.render = function(tick) {
                                var time = cx.Datetime.formatTime(tick);
                                var tpl = '';
                                if ('day' in time) {
                                    tpl += time.day + '天';
                                }
                                if ('hour' in time) {
                                    tpl += time.hour + '时';
                                }
                                if ('min' in time) {
                                    tpl += time.min + '分';
                                }
                                if ('second' in time) {
                                    tpl += time.second + '秒';
                                }
                                tpl += '后截止';
                                self.$countDown.html(tpl);
                            };
                        }
                        counter.countDown(function(tick) {
                            self.render.call(self, tick);
                        }, function() {
                            self.$countDown.html('期号更新中');
                            self.requestIssue();
                        });
                    }
                }
            });
        }
    };

    var LastAward = cx.LastAward = function(selector, options) {
        this.$el = $(selector);
        this.lotteryId = options.lotteryId;
        this.$awardNums = this.$el.find('.award-nums');
        this.$lastIssue = this.$el.find('.last-issue');
		this.$lastAwardDatetime = this.$el.find('.last-award-datetime');

        this.init();
    };

    LastAward.prototype = {
        init: function() {
            var self = this;
            cx.ajax.get({
                url: cx.url.getBaseUrl('api/data/getLastAward'),
                data: {
                    lid: self.lotteryId,
                    state: 201,
                    ps: 1,
                    t: (new Date()).valueOf(),
                    cache_2345caipiao: 'lid-' + self.lotteryId + '_state-201_ps-1:6:1'
                },
                success: function(response) {
                    var data = response.data.items[0];
                    var award = cx.Lottery.renderAward(self.lotteryId, data.awardNumber);
                    self.$awardNums.html(award);
                    self.$lastIssue.html(data.seExpect);
                }
            });
        }
    };

    var ValidateForm = function(selector, options) {
        this.$form = $(selector);
        this.$eles = this.$form.find('.input-content');
        this.data = {};
        this.init();
    };


    ValidateForm.rules = [
        'required', 'pattern', 'anti_pattern', 'max', 'min', 'depend'
    ];

    ValidateForm.validate = function(ele) {
        var val = $(ele).val();
        var eleRules = $(ele).data();
        var ruleVal;
        if ('min' in eleRules) {
            if (val < eleRules.min) {
                return false;
            }
        }
        if ('max' in eleRules) {
            if (val < eleRules.max) {
                return false;
            }
        }
        if ('pattern' in eleRules) {
            if (!eleRules.pattern.test(val)) {
                return false;
            }
        }
        if ('anti_pattern' in eleRules) {
            if (eleRules.anti_pattern.test(val)) {
                return false;
            }
        }

        return true;
    };

    ValidateForm.prototype = {
        init: function() {
            this.$eles.change(function() {
            });
        },
        validate: function() {
            $eles.each(function() {
            });
        }
    };
    var CrowdBuy = cx.CrowdBuy = function(selector, castOptions) {
        this.$el = $(selector).clone();
        $('body').append(this.$el);
        this.castOptions = castOptions || {};

        this.$close = this.$el.find('.close');
        this.$commision = this.$el.find('.commision-per');
        this.$myMoney = this.$el.find('.my-money');
        this.$myPer = this.$el.find('.my-per');
        this.$leastPer = this.$el.find('.least-per');
        this.$guaranteePer = this.$el.find('.guarantee-per');
        this.$guaranteeMoney = this.$el.find('.guarantee-money');
        this.$guaranteeAll = this.$el.find('.guarantee-all');
        this.$guaranteeNone = this.$el.find('.guarantee-none');
        this.$hasPrivate = this.$el.find('.has-private');

        this.maxMoney = this.castOptions.money;
        this.commision = 0;
        this.myPer = 5;
        this.guaranteePer = 0;
        this.guaranteeMoney = 0;
        this.guaranteeAll = false;
        this.hasPrivate = 1;
        this.leastPer = 5;
        this.leastMoney = Math.ceil(this.maxMoney * this.leastPer / 100);
        if (this.leastMoney < 1) {
            this.leastPer = Math.round(this.leastMoney / this.maxMoney * 100 * 100) / 100;
        }

        this.init();
    };

    CrowdBuy.prototype = {
        renderByMoney: function(money){
            this.myMoney = money;
            this.myPer = Math.round(money / this.maxMoney * 100 * 100) / 100;
            this.$myMoney.html(this.myMoney);
            this.$myPer.val(this.myPer);
        },
        renderByPer: function(per) {
            this.myMoney = this.maxMoney * per / 100;
            this.myPer = per;
            if (this.myMoney < 1) {
                this.myMoney = 1;
                this.myPer = Math.round(this.myMoney / this.maxMoney * 100 * 100) / 100;
            }
            this.$myMoney.html(this.myMoney);
            this.$myPer.val(this.myPer);
        },
        renderGuarantee: function(per) {
            this.guaranteePer = per;
            this.guaranteeMoney = per / 100 * this.maxMoney;
            this.guaranteeMoney = Math.min(this.guaranteeMoney, this.maxMoney - this.myMoney);
            this.guaranteeMoney = Math.round(this.guaranteeMoney * 100) / 100;
            this.$guaranteePer.val(this.guaranteePer);
            this.$guaranteeMoney.html(this.guaranteeMoney);
        },
        init: function() {
            var self = this;
            cx.Mask.show();
            this.renderByPer(self.leastPer);
            this.$el.show();
            this.$el.find('.crowd-tip-gua').hover(function() {
                self.$el.find('.crowd-tip-gua-content').show();
            }, function() {
                self.$el.find('.crowd-tip-gua-content').hide();
            });
            this.$commision.change(function() {
                var val = parseInt($(this).val(), 10);
                self.commision = val;
                if (val > 5) {
                    if (val > self.leastPer) {
                        self.leastPer = val;
                        self.leastMoney = Math.round(self.leastPer / 100 * self.maxMoney * 100) / 100;
                        if (val > self.myPer) {
                            self.renderByPer(val);
                        }
                    }
                } else {
                    self.leastPer = 5;
                    self.leastMoney = Math.round(self.leastPer / 100 * self.maxMoney * 100) / 100;
                }
            });
            this.$myPer.blur(function() {
                var $this = $(this);
                var val = $this.val();
                if (val == '') {
                    $this.val(self.leastPer);
                    self.renderByPer(self.leastPer);
                    self.renderGuarantee(self.guaranteePer);
                    return ;
                }
                val = parseInt(val, 10);
                if (val < self.leastPer) {
                    val = self.leastPer;
                }
                if (val > 100) {
                    val = 100;
                }
                val = parseInt(val, 10);
                self.renderByPer(val);
                self.renderGuarantee(self.guaranteePer);
            });
            this.$guaranteePer.blur(function() {
                var val = $(this).val();
                if (val == '') {
                    self.renderGuarantee(0);
                }
            });
            this.$guaranteePer.keyup(function() {
                var val = $(this).val();
                var $this = $(this);
                if (val == '') {
                    $this.val(0);
                    self.renderGuarantee(0);
                    return ;
                }
                if (!/^\d*$/.test(val)) {
                    $this.val(val.slice(0, -1));
                    return ;
                }
                val = parseInt(val, 10);
                if (val > 100) {
                    val = 100;
                }
                self.renderGuarantee(val);
            });
            this.$guaranteeAll.click(function() {
                self.guaranteePer = 100;
                self.guaranteeMoney = self.maxMoney;
                self.renderGuarantee(100);
            });
            this.$guaranteeNone.click(function() {
                self.guaranteePer = 0;
                self.guaranteeMoney = self.maxMoney;
                self.renderGuarantee(0);
            });
            this.$hasPrivate.click(function() {
                var $this = $(this);
                if ($this.hasClass('selected')) {
                    return ;
                }
                self.$hasPrivate.removeClass('selected');
                $this.addClass('selected');
                self.hasPrivate = $this.data('val');
            });
            this.$el.find('.close').click(function() {
                self.$el.remove();
                cx.Mask.hide();
            });
            this.$el.find('.btn-cancel').click(function() {
                self.$el.remove();
                cx.Mask.hide();
            });
            this.$el.find('.submit-crowd').click(function() {
                var data = self.getCrowdCastOptions();
                data.isToken = 1;
                var fields = ['commision', 'totalFraction', 'myFraction', 'hasPrivate', 'myGuarantee'];
                for (var i in fields) {
                    if (self[fields[i]] === null) {
                        return ;
                    }
                }
                var url = cx.url.getBusiUrl('ticket/order/create');
                cx.ajax.post({
                    url: url,
                    data: data,
                    success: function(response) {
                        self.$el.hide();
                        cx.castCb(response, self);
                    }
                });
            });
        },
        setCastOptions: function(options) {
            this.castOptions = options;
            this.init();
        },
        setMoney: function(money) {
            this.money = money;
        },
        getCrowdCastOptions: function() {
            this.castOptions.tnum = this.maxMoney * 10000 / 100;
            this.castOptions.wrate = this.commision;
            this.castOptions.oflag = this.hasPrivate;
            this.castOptions.bnum = this.myMoney * 10000 / 100;
            this.castOptions.pnum = this.guaranteeMoney * 10000 / 100;
            this.castOptions.name = 'test';
            this.castOptions.desc = 'test';
            this.castOptions.order_type = 1;
            return this.castOptions;
        }
    };

    $(function() {
        $('.join-crowd').click(function() {
            var $this = $(this);
            var $detail = $this.closest('.crowd-detail');
            var $myFraction = $detail.find('.my-fraction');
            var orderId = $detail.data('id');
            var oneMoney = parseFloat($detail.data('one'), 10);
            var maxFraction = $detail.data('max');
            var my = $myFraction.val();
            if (my == '' || isNaN(parseFloat(my, 10)) || $myFraction.hasClass('gray')) {
                new cx.Alert({
                    content: '请填写您需要购买的金额'
                });
                return ;
            }
            my = parseFloat(my, 10);
            if (my > maxFraction * oneMoney) {
                new cx.Alert({
                    content: '最多能购买' + maxFraction * oneMoney + '元'
                });
                return ;
            }
            new cx.Confirm({
                title: '确认购买',
                content: '立即购买' + cx.Money.round(my) + '元',
                confirmCb: function() {
                    cx.ajax.post({
                        url: cx.url.getBusiUrl('ticket/order/gendan'),
                        data: {
                            orderId: orderId,
                            money: cx.Money.round(my),
                            bnum: Math.round(cx.Money.round(my) * 10000 / 100),
                            buy_source: 0,
                            isToken: 1
                        },
                        success: function(response) {
                            cx.castCb(response, self);
                        }
                    });
                }
            });
        });
    });

    cx.castCb = function(response, self, aptype) {
        if (response.code == 0) {
            if ('random' in self) {
                self.random();
            }
            if(aptype){
            	$('.pop-confirm').remove();
            	cx.Mask.hide();
            }
            new cx.Confirm({
                single: '恭喜您，付款成功等待出票',
                btns: [
                    {
                        type: 'cancel',
                        txt: '继续购彩'
                    },
                    {
                        type: 'confirm',
                        txt: '查看详情',
                        href: baseUrl + 'orders/detail/' + response.data.orderId
                    }
                ],
                cancelCb: function() {
                    var str = location.href.split("?");
                   // console.log(str);
                    location.href = str[0];
                    //location.href =  location.pathname

                }
            });
        } else {
            if (response.code == 12) {
                new cx.Confirm({
					content: betInfo.number( 
						cx.Lottery.getCnName(self.lotteryId),
						cx.Lottery.getPlayTypeName( self.lotteryId, cx.Lottery.playTypes[self.lotteryId][self.playType] ),
						self.issue.getIssue(),
						response.data.money,
						response.data.remain_money
					),
                    btns: [
                        {
                            type: 'confirm',
                            txt: '去支付',
                            href: baseUrl + 'wallet/directPay?orderId=' + response.data.orderId
                        }
                    ]
                });
            } else {
            	if(aptype){
            		$(aptype).html(response.msg);
            	}
            	else{
            		new cx.Alert({
                        content: response.msg
                    });
            	}
            }
        }
    };

})();
$(function(){
  function lotteryTableCPTrHover(){
    //表格一行hover黄色效果
    $(".lotteryTableCP tr.match").hover(function(e){
      $(this).addClass("hover");
      $(this).next().not(".match").addClass("hover");
    }, function(){
      $(this).removeClass("hover");
      $(this).next().not(".match").removeClass("hover");
    });
    $(".lotteryTableCP tr.match").next().hover(function(e){
      if ( $(this).hasClass("match") ) return;
      $(this).addClass("hover");
      $(this).prev().addClass("hover");
    }, function(){
      if ( $(this).hasClass("match") ) return;
      $(this).removeClass("hover");
      $(this).prev().removeClass("hover");
    });
  }
  lotteryTableCPTrHover();
});
