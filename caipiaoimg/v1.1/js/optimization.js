//Knew that we ventured on such dangerous seas
//That if we wrought out life 'twas ten to one
//   ----William Shakespeare, Henry IV
$(function () {
    if (!Array.prototype.reduce) {
        Array.prototype.reduce = function (fun) {
            var len = this.length;

            if (typeof fun != "function")
                throw new TypeError();

            if (len == 0 && arguments.length == 1) {
                throw new TypeError();
            }

            var i = 0;
            if (arguments.length >= 2) {
                var rv = arguments[1];
            }
            else {
                do
                {
                    if (i in this) {
                        rv = this[i++];
                        break;
                    }

                    if (++i >= len)
                        throw new TypeError();
                }
                while (true);
            }
            for (; i < len; i++) {
                if (i in this)
                    rv = fun.call(null, rv, this[i], i, this);
            }
            return rv;
        };
    }

    if (!('map' in Array.prototype)) {
        Array.prototype.map = function (mapper, that /*opt*/) {
            var other = new Array(this.length);
            for (var i = 0, n = this.length; i < n; i++)
                if (i in this)
                    other[i] = mapper.call(that, this[i], i, this);
            return other;
        };
    }

    var MAX_MULTI = 100000,
        MIN_MULTI = 0,
        MAX_STD_PRIZE = 1000000,
        MIN_STD_PRIZE = 0,
        ONE_MONEY = 2,
        MAX_BET_MONEY = 200000,
        $schemeTable = $('#schemeTable'),
        matchAry = $('.match').map(function () {
            var $this = $(this);
            return {
                mid: $this.data('mid'),
                home: $this.data('home'),
                away: $this.data('away'),
                endTime: $this.data('endtime')
            };
        }).get(),
        disableClass = 'disabled',
        selectClass = 'selected',
        allMultiSum = 0,
        schemeAry,
        schemeCollection = (function () {
            var me = {},
                schemes = {};

            me.add = function (index, scheme) {
                schemes[index] = scheme;

                return this;
            };

            me.get = function (index) {
                return schemes[index] || {};
            };

            me.getAll = function () {
                return schemes;
            };

            me.getArray = function () {
                return $.map(schemes, function (scheme) {
                    return scheme;
                });
            };

            //maybe not harmonic, just seems like harmonic
            me.harmonicMean = function () {
                var sum = 0;
                $.each(schemes, function (i, scheme) {
                    sum += 1 / scheme.odd;
                    //sum += 1 / scheme.odd * scheme.multi;
                });
                return allMultiSum / sum;
            };

            me.avgOptimize = function () {
                var harmonicMean = me.harmonicMean(),
                    mappedAry,
                    newMultiAry,
                    orderedAry,
                    sum,
                    diff,
                    i,
                    j,
                    hasOp;

                schemeAry = schemeCollection.getArray();
                mappedAry = schemeAry.map(function (el, i) {
                    return {
                        index: i,
                        value: el.odd
                    };
                });
                mappedAry.sort(function (a, b) {
                    return a.value - b.value;
                });
                orderedAry = mappedAry.map(function (el) {
                    return schemeAry[el.index];
                });
                newMultiAry = orderedAry.map(function (scheme) {
                    return Math.max(Math.round(harmonicMean / (scheme.odd * 1)), 1);
                });
                sum = newMultiAry.reduce(function (a, b) {
                    return a + b;
                });
                diff = sum - allMultiSum;
                if (diff > 0) {
                    while (diff > 0) {
                        hasOp = false;
                        for (i = 0, j = newMultiAry.length; i < j; i++) {
                            if (newMultiAry[i] > 1) {
                                newMultiAry[i] -= 1;
                                diff--;
                                if (diff === 0) {
                                    break;
                                }
                                hasOp = true;
                            }
                        }
                        if (!hasOp) {
                            break;
                        }
                    }
                } else if (diff < 0) {
                    for (i = newMultiAry.length; i >= 0; i--) {
                        newMultiAry[i - 1] += 1;
                        diff++;
                        if (diff === 0) {
                            break;
                        }
                    }
                }
                $.each(orderedAry, function (i, tmpScheme) {
                    tmpScheme.multi = newMultiAry[i];
                });
                orderedAry.sort(function (a, b) {
                    return a.index - b.index;
                });
                schemeAry = orderedAry;
                me.alterContent();

                return this;
            };

            me.hotOptimize = function () {
                var harmonicMean = me.harmonicMean(),
                    mappedAry,
                    newMultiAry,
                    orderedAry,
                    sum,
                    diff,
                    i,
                    j,
                    hasOp;

                schemeAry = schemeCollection.getArray();
                mappedAry = schemeAry.map(function (el, i) {
                    return {
                        index: i,
                        value: el.odd
                    };
                });
                mappedAry.sort(function (a, b) {
                    return a.value - b.value;
                });
                orderedAry = mappedAry.map(function (el) {
                    return schemeAry[el.index];
                });
                newMultiAry = orderedAry.map(function (scheme) {
                    return Math.ceil(allMultiSum / scheme.odd);
                });
                sum = newMultiAry.reduce(function (a, b) {
                    return a + b;
                });

                if (sum > allMultiSum) {
                    newMultiAry = orderedAry.map(function (scheme) {
                        return Math.max(Math.round(harmonicMean / (scheme.odd * 1)), 1);
                    });
                    sum = newMultiAry.reduce(function (a, b) {
                        return a + b;
                    });
                    diff = sum - allMultiSum;
                    if (diff > 0) {
                        while (diff > 0) {
                            hasOp = false;
                            for (i = 0, j = newMultiAry.length; i < j; i++) {
                                if (newMultiAry[i] > 1) {
                                    newMultiAry[i] -= 1;
                                    diff--;
                                    if (diff === 0) {
                                        break;
                                    }
                                    hasOp = true;
                                }
                            }
                            if (!hasOp) {
                                break;
                            }
                        }
                    } else if (diff < 0) {
                        for (i = newMultiAry.length; i >= 0; i--) {
                            newMultiAry[i - 1] += 1;
                            diff++;
                            if (diff === 0) {
                                break;
                            }
                        }
                    }
                } else {
                    diff = allMultiSum - sum;
                    newMultiAry[0] += diff;
                }

                $.each(orderedAry, function (i, tmpScheme) {
                    tmpScheme.multi = newMultiAry[i];
                });
                orderedAry.sort(function (a, b) {
                    return a.index - b.index;
                });
                schemeAry = orderedAry;
                me.alterContent();

                return this;
            };

            me.coldOptimize = function () {
                var harmonicMean = me.harmonicMean(),
                    mappedAry,
                    newMultiAry,
                    orderedAry,
                    sum,
                    diff,
                    i,
                    j,
                    hasOp;

                schemeAry = schemeCollection.getArray();
                mappedAry = schemeAry.map(function (el, i) {
                    return {
                        index: i,
                        value: el.odd
                    };
                });
                mappedAry.sort(function (a, b) {
                    return a.value - b.value;
                });
                orderedAry = mappedAry.map(function (el) {
                    return schemeAry[el.index];
                });
                newMultiAry = orderedAry.map(function (scheme) {
                    return Math.ceil(allMultiSum / scheme.odd);
                });
                sum = newMultiAry.reduce(function (a, b) {
                    return a + b;
                });

                if (sum > allMultiSum) {
                    newMultiAry = orderedAry.map(function (scheme) {
                        return Math.max(Math.round(harmonicMean / (scheme.odd * 1)), 1);
                    });
                    sum = newMultiAry.reduce(function (a, b) {
                        return a + b;
                    });
                    diff = sum - allMultiSum;
                    if (diff > 0) {
                        while (diff > 0) {
                            hasOp = false;
                            for (i = 0, j = newMultiAry.length; i < j; i++) {
                                if (newMultiAry[i] > 1) {
                                    newMultiAry[i] -= 1;
                                    diff--;
                                    if (diff === 0) {
                                        break;
                                    }
                                    hasOp = true;
                                }
                            }
                            if (!hasOp) {
                                break;
                            }
                        }
                    } else if (diff < 0) {
                        for (i = newMultiAry.length; i >= 0; i--) {
                            newMultiAry[i - 1] += 1;
                            diff++;
                            if (diff === 0) {
                                break;
                            }
                        }
                    }
                } else {
                    diff = allMultiSum - sum;
                    newMultiAry[newMultiAry.length - 1] += diff;
                }

                $.each(orderedAry, function (i, tmpScheme) {
                    tmpScheme.multi = newMultiAry[i];
                });
                orderedAry.sort(function (a, b) {
                    return a.index - b.index;
                });
                schemeAry = orderedAry;
                me.alterContent();

                return this;
            };

            me.zeroOptimize = function () {
                var mappedAry,
                    orderedAry;

                schemeAry = schemeCollection.getArray();
                mappedAry = schemeAry.map(function (el, i) {
                    return {
                        index: i,
                        value: el.odd
                    };
                });
                mappedAry.sort(function (a, b) {
                    return a.value - b.value;
                });
                orderedAry = mappedAry.map(function (el) {
                    return schemeAry[el.index];
                });

                $.each(orderedAry, function (i, tmpScheme) {
                    tmpScheme.multi = 0;
                });
                orderedAry.sort(function (a, b) {
                    return a.index - b.index;
                });
                schemeAry = orderedAry;
                me.alterContent();

                return this;
            };

            me.alterContent = function () {
                $schemeTable.children().each(function () {
                    var $this = $(this),
                        index = $this.data('index'),
                        scheme = schemeCollection.get(index),
                        eachPrize = ((scheme.odd * 2).toFixed(2) * scheme.multi).toFixed(2),
                        betMoney = castData.getBetMoney();
                    $this.find('.multi').val(scheme.multi);
                    $this.find('.each-prize').text(eachPrize).toggleClass('main-color-s', eachPrize >= betMoney);
                });
                return this;
            };

            me.adjustStandard = function (stdPrize) {
                $.each(schemeCollection.getArray(), function (i, scheme) {
                    scheme.multi = Math.max(1, Math.round(stdPrize / (scheme.odd * ONE_MONEY)));
                });
                return this;
            };

            return me;
        })(),
        castData = (function () {
            var me = {},
                betMoney, buyMoney = 0, commission = 0, guarantee = 0, openStatus = 0;

            me.renderBet = function () {
                var schemeAry = schemeCollection.getArray();
                betMoney = $.map(schemeAry, function (scheme) {
                        return scheme.multi;
                    }).reduce(function (a, b) {
                        return a + b;
                    }) * ONE_MONEY;
                me.setBetMoney(betMoney);
                $('#bet-money').html(betMoney);
                $('#min-money').html(($.map(schemeAry, function (scheme) {
                    return scheme.multi * scheme.odd;
                }).reduce(function (a, b) {
                    return a < b ? a : b;
                }) * ONE_MONEY).toFixed(2));
                $('#max-money').html(($.map(schemeAry, function (scheme) {
                    return scheme.multi * scheme.odd;
                }).reduce(function (a, b) {
                    return a + b;
                }) * ONE_MONEY).toFixed(2));
            };

            me.getBetMoney = function () {
                var schemeAry = schemeCollection.getArray();
                betMoney = $.map(schemeAry, function (scheme) {
                        return scheme.multi;
                    }).reduce(function (a, b) {
                        return a + b;
                    }) * ONE_MONEY;

                return betMoney;
            };

            me.setBetMoney = function (val) {
                betMoney = val;
            };
            
            me.hemaiPop = function() {
            	var rgpctmin = 5;
            	betMoney = me.getBetMoney();
            	var showhmTime = new Date(Date.parse(endTime.replace(/-/g, "/")) - hmahead * 60000);
            	$('.pop-pay').find('.jzdt').html(showhmTime.getFullYear()+"-"+(padd(showhmTime.getMonth() + 1))+"-"+padd(showhmTime.getDate())+" "+padd(showhmTime.getHours())+":"+ padd(showhmTime.getMinutes())+":"+padd(showhmTime.getSeconds()));
            	$('.pop-pay').find('.betMoney').html(betMoney);
            	buyMoney = Math.ceil(betMoney * rgpctmin / 100);
            	renderBuyMoney();
            	renderGuarantee();
            	
            	$('.pop-pay').on('click', '.commission li', function(){
            		$(this).parents('.commission').find('li').removeClass('cur');
            		$(this).addClass('cur');
            		commission = $(this).data('val');
            		rgpctmin = (commission <= 5) ? 5 : commission;
            		buyMoneyTmp = Math.ceil(betMoney * rgpctmin / 100);
            		buyMoney = buyMoneyTmp > buyMoney ? buyMoneyTmp : buyMoney;
            		if ($('.guaranteeAll').attr('checked') || guarantee > betMoney - buyMoney) {
        				guarantee = betMoney - buyMoney;
        				$('.guaranteeAll').attr('checked', 'checked');
                    	renderGuarantee();
                    }
            		renderBuyMoney();
            	})
            	
            	$('.pop-pay').on('blur', '.buyMoney', function(){
            		var min = Math.ceil(betMoney * rgpctmin / 100);
            		buyMoney = isNaN(parseInt($(this).val(), 10)) ? 0 : parseInt($(this).val(), 10);
        			buyMoney = (buyMoney < min) ? min : (buyMoney > betMoney ? betMoney : buyMoney);
        			if ($('.guaranteeAll').attr('checked') || guarantee > betMoney - buyMoney) {
        				guarantee = betMoney - buyMoney;
        				$('.guaranteeAll').attr('checked', 'checked');
                    	renderGuarantee();
                    }
        			renderBuyMoney();
            	})
            	$('.pop-pay').on('blur', '.guarantee', function(){
            		guarantee = isNaN(parseInt($(this).val(), 10)) ? 0 : parseInt($(this).val(), 10);
        			$('.guaranteeAll').removeAttr('checked');
        			if (guarantee >= (betMoney - buyMoney)) {
        				guarantee = betMoney - buyMoney;
        				$('.guaranteeAll').attr('checked', 'checked');
        			}
        			guarantee = guarantee < 0 ? 0 : guarantee;
        			renderGuarantee();
            	})
            	$('.pop-pay').find('.guaranteeAll').on('click', function(){
        			guarantee = betMoney - buyMoney;
                    renderGuarantee();
        		});
            	$('.pop-pay').find('input[name=bmsz]').click(function(){
            		openStatus = $(this).val();
            	})
            	$('.pop-pay').find('.submit').click(function (e) {
                    var $agreement;
                    
                    $(this).addClass('needTigger');
                    
                    if (!$.cookie('name_ie')) {//登录过期
                    	cx.PopCom.hide($('.pop-pay'));
                        me.notLogin($(this), e);
                        return;
                    }
                    if ($(this).hasClass('not-bind')) {cx.PopCom.hide($('.pop-pay'));cx.PopAjax.bind();return;}
                    var data = me.getCastOptions2();
                    if(data.betTnum <= 0){
                    	return void new cx.Alert({'content': '坏啦，您竟然一注方案都没选~'});
                    }
                    data.isToken = 1;
                    var self = this;

                    cx.castCb(data, {ctype:'create', lotteryId:42, orderType:4, betMoney:data.money, buyMoney:data.buyMoney, guarantee:data.guaranteeAmount, typeCnName:typeHmName});

                });
            	function renderBuyMoney() {
                	$('.buyMoney').val(buyMoney).parents('.form-item-con').find('span em:first').html(rgpctmin)
                			.parents('.form-item-con').find('u').show().find('em').html(betMoney > 0 ? Math.floor(buyMoney * 100/betMoney) : 0);
        			$('.guarantee').parents('.form-item-con').find('span em:first').html(betMoney - buyMoney);
        			$('.buy_txt').html("<em class='main-color-s'>"+(buyMoney+guarantee)+"</em> 元 <span>（认购"+buyMoney+"元+保底"+guarantee+"元）</span></span>");
                }
            	function renderGuarantee() {
                	$('.guarantee').val(guarantee).parents('.form-item-con').find('span em:last').html(betMoney > 0 ? Math.floor(guarantee * 100 / betMoney) : 0);
                	$('.buy_txt').html("<em class='main-color-s'>"+(buyMoney+guarantee)+"</em> 元 <span>（认购"+buyMoney+"元+保底"+guarantee+"元）</span></span>");
                }
            }

            me.getCastOptions = function () {
                var schemeAry = schemeCollection.getArray();
                var betTnum = 0;
                return {
                    ctype: 'create',
                    buyPlatform: 0,
                    codes: $.map(schemeAry, function (scheme) {
                    	if(scheme.multi > 0){
                    		betTnum ++;
                    		return ['HH', scheme.str, scheme.multi, scheme.parlay].join('|');
                    	}
                    }).join(';'),
                    lid: lotteryId,
                    money: $.map(schemeAry, function (scheme) {
                        return scheme.multi;
                    }).reduce(function (a, b) {
                        return a + b;
                    }) * ONE_MONEY,
                    multi: 1,
                    issue: cx.Datetime.getToday(),
                    playType: 7,
                    betTnum: betTnum,
                    isChase: 0,
                    orderType: 0,
                    endTime: endTime,
                    codecc: $.map(matchAry, function (match) {
                        return match.mid
                    }).join(' '),
                    forecastBonus: $('#min-money').html()+"|"+$('#max-money').html(),
                    isToken: 1
                };
            };
            
            me.getCastOptions2 = function () {
            	var schemeAry = schemeCollection.getArray();
            	var betTnum = 0;
                return {
                    ctype: 'create',
                    buyPlatform: 0,
                    codes: $.map(schemeAry, function (scheme) {
                    	if(scheme.multi > 0){
                    		betTnum ++;
                    		return ['HH', scheme.str, scheme.multi, scheme.parlay].join('|');
                    	}
                    }).join(';'),
                    lid: lotteryId,
                    money: $.map(schemeAry, function (scheme) {
                        return scheme.multi;
                    }).reduce(function (a, b) {
                        return a + b;
                    }) * ONE_MONEY,
                    multi: 1,
                    issue: cx.Datetime.getToday(),
                    playType: 7,
                    betTnum: betTnum,
                    isChase: 0,
                    orderType: 4,
                    codecc: $.map(matchAry, function (match) {
                        return match.mid
                    }).join(' '),
                    endTime: hmendTime,
                    buyMoney: buyMoney,
                    commissionRate: commission,
                    guaranteeAmount: guarantee,
                    openStatus: openStatus,
                    openEndtime: openEndtime,
                    ForecastBonusv: $('#min-money').html()+"|"+$('#max-money').html()
                };
            };

            me.notLogin = function ($this, e) {
                if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
                    cx.PopAjax.login(e.target.className.match('hemai') ? '' : 1);
                    e.stopImmediatePropagation();
                }
                e.preventDefault();
            };

            return me;
        })();

    $schemeTable.children().each(function () {
        var $this = $(this),
            index = $this.data('index'),
            odd = $this.data('odd'),
            str = $this.data('str'),
            parlay = $this.data('parlay'),
            multi = $this.data('multi');

        schemeCollection.add(index, {
            index: index,
            odd: odd,
            str: str,
            parlay: parlay,
            multi: multi
        });

        allMultiSum += multi;
    });

    $('body').on('focus', '#plan', function () {
        $(this).closest('.bonus-plan-label').find('.mod-tips-t').hide();
    }).on('keyup', '#plan', function () {
        var $this = $(this),
            val = $this.val(),
            oldVal = $.map(schemeCollection.getArray(), function (scheme) {
                    return scheme.multi;
                }).reduce(function (a, b) {
                    return a + b;
                }) * ONE_MONEY;
        if (oldVal != val) {
            $('#avgOptimize').removeClass(selectClass);
            $('#hotOptimize').removeClass(selectClass);
            $('#coldOptimize').removeClass(selectClass);
            schemeCollection.zeroOptimize();
            $schemeTable.children().each(function () {
                $(this).find('.multi').val(0);
            });
            $('#bet-money').html(0);
            $('#min-money').html('0.00');
            $('#max-money').html('0.00');
        }
    }).on('blur', '#plan', function () {
        var $this = $(this),
            val = $this.val();
        if (!(/^\d+$/.test(val))) {
            val = MIN_BET_MONEY;
        }
        if (val % 2) {
            val++;
        }
        if (val < MIN_BET_MONEY) {
            val = MIN_BET_MONEY;
            $this.closest('.bonus-plan-label').find('.mod-tips-t')
                .html('为达到较好的优化效果，投注金<br>额至少为原方案金额的2倍 <b></b><s></s>')
                .show();
        }
        if (val > MAX_BET_MONEY) {
            val = MAX_BET_MONEY;
            $this.closest('.bonus-plan-label').find('.mod-tips-t')
                .html(['<i class="icon-font">&#xe600;</i>',
                    '请输入', cx.Money.format(MAX_BET_MONEY), '元以下方案金额进行优化'].join(''))
                .show();
        }
        allMultiSum = val / ONE_MONEY;
        $this.val(val);
    }).on('click', '#avgOptimize', function () {
        var $this = $(this);
        if (!$this.hasClass(disableClass)) {
            schemeCollection.avgOptimize();
            castData.renderBet();
            $this.addClass(selectClass).siblings('.selected').removeClass(selectClass);
        }
    }).on('click', '#hotOptimize', function () {
        var $this = $(this);
        if (!$this.hasClass(disableClass)) {
            schemeCollection.hotOptimize();
            castData.renderBet();
            $this.addClass(selectClass).siblings('.selected').removeClass(selectClass);
        }
    }).on('click', '#coldOptimize', function () {
        var $this = $(this);
        if (!$this.hasClass(disableClass)) {
            schemeCollection.coldOptimize();
            castData.renderBet();
            $this.addClass(selectClass).siblings('.selected').removeClass(selectClass);
        }
    }).on('click', '.stdPlus', function () {
        var $this = $(this),
            $input = $this.prev().children('.multi'),
            stdPrize = $input.val();
        stdPrize++;
        stdPrize = Math.min(stdPrize, MAX_STD_PRIZE);
        $input.val(stdPrize);
    }).on('click', '.stdMinus', function () {
        var $this = $(this),
            $input = $this.next().children('.multi'),
            stdPrize = $input.val();
        stdPrize--;
        stdPrize = Math.max(MIN_STD_PRIZE, stdPrize);
        $input.val(stdPrize);
    }).on('blur', '.stdMulti', function () {
        var $this = $(this),
            stdPrize = $this.val();
        stdPrize = parseInt(stdPrize, 10);
        if (!stdPrize) {
            stdPrize = 0;
        } else if (!(/^\d+$/.test(stdPrize))) {
            stdPrize = MIN_STD_PRIZE;
        }
        stdPrize = Math.min(MAX_STD_PRIZE, stdPrize);
        $this.val(stdPrize);
    }).on('keyup', '.stdMulti', function (e) {
        var $this = $(this),
            stdPrize = $this.val();

        stdPrize = parseInt(stdPrize, 10);
        if ((e.which >= 48 && e.which <= 57) || e.which == 8 || (e.which >= 96 && e.which <= 105)) {
            if (stdPrize != '') {
                if (!stdPrize) {
                    stdPrize = 0;
                }
                stdPrize = Math.min(MAX_STD_PRIZE, stdPrize);
                $this.val(stdPrize);
            }
        } else {
            if (!(/^\d+$/.test(stdPrize))) {
                stdPrize = MIN_STD_PRIZE;
                $this.val(stdPrize);
            }
        }
    }).on('click', '#stdConfirm', function () {
        var $this = $(this),
            $input = $this.closest('.edit-box').find('.stdMulti'),
            stdPrize = $input.val();
        schemeCollection.adjustStandard(stdPrize);
        schemeCollection.alterContent();
        castData.renderBet();
    }).on('click', '.eachPlus', function () {
        var $this = $(this),
            list = $this.closest('.bet-list-item'),
            index = list.data('index'),
            scheme = schemeCollection.get(index),
            multi = scheme.multi;

        multi++;
        multi = Math.min(multi, MAX_MULTI);
        $this.prev().children('.multi').val(multi);
        scheme.multi = multi;
        schemeCollection.alterContent();
        castData.renderBet();
    }).on('click', '.eachMinus', function () {
        var $this = $(this),
            list = $this.closest('.bet-list-item'),
            index = list.data('index'),
            scheme = schemeCollection.get(index),
            multi = scheme.multi;

        multi--;
        multi = Math.max(MIN_MULTI, multi);
        $this.prev().children('.multi').val(multi);
        scheme.multi = multi;
        schemeCollection.alterContent();
        castData.renderBet();
    }).on('blur', '.eachMulti', function () {
        var $this = $(this),
            list = $this.closest('.bet-list-item'),
            index = list.data('index'),
            scheme = schemeCollection.get(index),
            multi,
            val = $.trim($this.val());

        multi = parseInt(val, 10);
        if (!multi) {
            multi = MIN_MULTI;
        } else if (!(/^\d+$/.test(val))) {
            multi = MIN_MULTI;
        }
        multi = Math.min(MAX_MULTI, multi);
        $this.val(multi);
        scheme.multi = multi;
        schemeCollection.alterContent();
        castData.renderBet();
    }).on('keyup', '.eachMulti', function (e) {
        var $this = $(this),
            list = $this.closest('.bet-list-item'),
            index = list.data('index'),
            scheme = schemeCollection.get(index),
            multi,
            val = $.trim($this.val());

        multi = parseInt(val, 10);
        if (e.which <= 36 || e.which >= 41) {
            if ((e.which >= 48 && e.which <= 57) || e.which == 8 || (e.which >= 96 && e.which <= 105)) {
                if (val == '') {
                    multi = MIN_MULTI;
                } else {
                    if (!multi) {
                        multi = MIN_MULTI;
                    }
                    multi = Math.min(MAX_MULTI, multi);
                    $this.val(multi);
                }
            } else {
                if (!(/^\d+$/.test(val))) {
                    multi = MIN_MULTI;
                    $this.val(multi);
                }
            }
            scheme.multi = multi;
            schemeCollection.alterContent();
            castData.renderBet();
        }
    }).on('click', '.submitOpt', function (e) {
        var $this = $(this),
            data;

        if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
            castData.notLogin($this, e);
            return false;
        }

        if ($this.hasClass('not-bind')) {
            return false;
        }

        if (!$('#avgOptimize').hasClass(selectClass) && !$('#hotOptimize').hasClass(selectClass)
            && !$('#coldOptimize').hasClass(selectClass)) {
            return void new cx.Alert({'content': '计划金额变更, 请重新选择优化方案！'});
        }

        schemeAry = schemeCollection.getArray();
        schemeAry.sort(function (a, b) {
            return a.index - b.index;
        });
        data = castData.getCastOptions();
        if (data.money > MAX_BET_MONEY) {
            return void new cx.Alert({
                content: ["<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>",
                    cx.Money.format(MAX_BET_MONEY), "</span>元，请修改订单后重新投注"].join('')
            });
        }
        
        if(data.betTnum <= 0){
        	return void new cx.Alert({'content': '坏啦，您竟然一注方案都没选~'});
        }

        var nowDate = new Date(),
            dateTimeAry = endTime.split(' '),
            dateAry = dateTimeAry[0].split('-'),
            year = dateAry[0],
            month = dateAry[1] - 1,
            day = dateAry[2],
            timeAry = dateTimeAry[1].split(':'),
            hour = timeAry[0],
            minute = timeAry[1],
            second = timeAry[2],
            endDate = new Date(year, month, day, hour, minute, second);
        if (endDate <= nowDate) {
            return void new cx.Confirm({
                btns: [
                    {
                        type: 'cancel',
                        href: 'javascript:;',
                        txt: '取消'
                    },
                    {
                        type: 'confirm',
                        href: 'javascript:;',
                        txt: '重新选择方案'
                    }
                ],
                wrapper: {
                    begin: '<div class="pub-pop pop-confirm pop-new pop-w-min" style="width:380px; ">',
                    end: '</div>'
                },
                inlineBtn: true,
                content: ['', '<div class="pop-txt text-indent"><i class="icon-font">&#xe61f;</i>投注列表中【<a href="javascript:;"> ',
                    matchAry[0].home, 'VS', matchAry[0].away, '</a>】已截止或玩法暂时不可投注</div>'].join(''),
                confirmCb: function () {
                    var stakeStr = $('.match').map(function () {
                        var $this = $(this),
                            mid = $this.data('mid'),
                            $bet = $this.find('.wager');
                        return [mid, $bet.map(function () {
                            var $this = $(this),
                                option = $this.data('option'),
                                type = $this.data('type');
                            return [type, option].join('/');
                        }).get().join(',')].join('=');
                    }).get().join(';');
                    $('#stakeStr').val(stakeStr);
                    $('#stakeForm').submit();
                }
            });
        }

        cx.castCb(data, {ctype:'create', lotteryId:42, orderType:0, betMoney:data.money, typeCnName:typeCnName});
    }).on('click', '.btn-hemai', function (e){
    	data = castData.getCastOptions2();
    	if(data.betTnum <= 0){
        	return void new cx.Alert({'content': '坏啦，您竟然一注方案都没选~'});
        }
    	$(this).addClass('needTigger');
    	
    	if ($(this).hasClass('not-login') || !$.cookie('name_ie')) {
            castData.notLogin($(this), e);
            return false;
        }

        if ($(this).hasClass('not-bind')) return false;
        
        if (!$('#avgOptimize').hasClass(selectClass) && !$('#hotOptimize').hasClass(selectClass)
                && !$('#coldOptimize').hasClass(selectClass)) {
            return void new cx.Alert({'content': '计划金额变更, 请重新选择优化方案！'});
        }


		$.ajax({
	        type: 'post',
	        url: '/pop/hemai',
	        data: {'version': version},
	        success: function (response) {
	            $('body').prepend(response);
	            cx.PopCom.show('.pop-pay');
	            cx.PopCom.close('.pop-pay', {
	            	cb: function() {
	            		buyMoney = 0; 
	            		commission = 0; 
	            		guarantee = 0;
	            		openStatus = 0;
	            	}
	            });
	            castData.hemaiPop();
	            $('.pop-pay').find('.bet_info').hide();
	        }
	    });
	})

});
function padd(num) {
    return ('0' + num).slice(-2);
}