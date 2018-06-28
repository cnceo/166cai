$(function () {
    if (!Array.prototype.filter) {
        Array.prototype.filter = function (fun) {
            "use strict";

            if (this === void 0 || this === null)
                throw new TypeError();

            var t = Object(this);
            var len = t.length >>> 0;
            if (typeof fun !== "function")
                throw new TypeError();

            var res = [];
            var thisp = arguments[1];
            for (var i = 0; i < len; i++) {
                if (i in t) {
                    var val = t[i];
                    if (fun.call(thisp, val, i, t))
                        res.push(val);
                }
            }

            return res;
        };
    }

    if (!Array.prototype.reduce) {
        Array.prototype.reduce = function (fun) {
            var len = this.length;

            if (typeof fun != "function")
                throw new TypeError();

            if (len == 0 && arguments.length == 1)
                throw new TypeError();

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

    var ONE_MONEY = 2,
        MIN_MATCH_COUNT = typeEnName == 'sfc' ? 14 : 9,
        MAX_BET_MONEY = 200000,
        betNum = 0,
        betMoney = 0,
        currentBetNum = 0,
        currentBetMoney = 0,
        showMore = false,
        inModify = false,
        modifyIndex,
        buyMoney = 0,
        guarantee = 0,
        rgpctmin = 5,
        orderType = 0,
        openStatus = 0,
        commission = 0,
        baseResultHash = {
            0: '#',
            1: '0',
            2: '1',
            3: '10',
            4: '3',
            5: '30',
            6: '31',
            7: '310'
        },
        showResultHash = {
            0: '-',
            1: '0',
            2: '1',
            3: '10',
            4: '3',
            5: '30',
            6: '31',
            7: '310'
        },
        baseLengthHash = {
            0: 1,
            1: 1,
            2: 1,
            3: 2,
            4: 1,
            5: 2,
            6: 2,
            7: 3
        },
        schemeAry = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        $betNum = $('.bet-num, .betNum'),
        $betMoney = $('.bet-money, .betMoney'),
        $currentBetNum = $('.current-bet-num'),
        $currentBetMoney = $('.current-bet-money'),
        multiModifier = new cx.AdderSubtractor('.multi-modifier'),
        endTime = $('#end-time').data('time'),
        selectedClass = 'selected',
        hoverClass = 'hover',
        disableClass = 'btn-disabled',
        schemeCollection = (function () {
            var me = {},
                schemes = {};

            me.add = function (index, scheme) {
                return (schemes[index] = scheme);
            };

            me.isComplete = function (scheme) {
                return scheme.filter(function (val) {
                        return val > 0;
                    }).length >= MIN_MATCH_COUNT;
            };

            me.calcBet = function (scheme) {
                return $.map(cx.Math.slowCombineList(scheme.filter(function (val) {
                    return val > 0;
                }), MIN_MATCH_COUNT), function (scheme) {
                    return $.map(scheme, function (option) {
                        return baseLengthHash[option];
                    }).reduce(function (a, b) {
                        return a * b;
                    });
                }).reduce(function (a, b) {
                    return a + b;
                });
            };

            me.remove = function (index) {
                if (index in schemes) {
                    delete schemes[index];
                }
            };

            me.count = function () {
                var count = 0;
                $.each(schemes, function () {
                    count += 1;
                });

                return count;
            };

            me.maxIndex = function () {
                var max = 0;
                $.each(schemes, function (i) {
                    if (i > max) {
                        max = parseInt(i, 10);
                    }
                });

                return max;
            };

            me.get = function (index) {
                return schemes[index] || {};
            };

            me.getAll = function () {
                return schemes;
            };

            me.getArray = function () {
                var arr = [];
                $.each(schemes, function (i, scheme) {
                    arr.push(scheme);
                });

                return arr;
            };

            me.initCurrent = function () {
                schemeAry = $.map(schemeAry, function () {
                    return 0;
                });
            };

            me.empty = function () {
                return (schemes = {});
            };

            return me;
        })(),
        selectedSchemes = (function () {
            var me = {};
            
            me.show = function () {
                $('.cast-list').html(function () {
                    return $.map($.map(schemeCollection.getAll(), function (scheme, i) {
                        return i;
                    }).reverse(), function (i) {
                        var scheme = schemeCollection.get(i);
                        return ['<li data-index="', i, '"',
                            inModify && modifyIndex && i == modifyIndex ? ' class="hover" ' : '',
                            '><div class="num-group">',
                            $.map(scheme, function (option) {
                                return ['<span>', showResultHash[option], '</span>'].join('');
                            }).join(' '),
                            '</div><a href="javascript:;" class="delete-line">删除</a>',
                            '<a href="javascript:;" class="modify-line">修改</a><span class="bet-money">',
                            schemeCollection.calcBet(scheme) * ONE_MONEY,
                            '元</span></li>'];
                    }).join('');
                });
            };

            me.checkBox = function () {
                var $checkBox = $('.select-all');
                $checkBox.each(function () {
                    var $this = $(this),
                        $options = $this.closest('.match').find('.options'),
                        allSelect = true;
                    $options.each(function () {
                        var $this = $(this);
                        if (!$this.hasClass(selectedClass)) {
                            allSelect = false;
                            return false;
                        }
                    });
                    if (allSelect) {
                        $this.prop('checked', true);
                    } else {
                        $this.prop('checked', false);
                    }
                })
            };

            return me;
        })(),
        castData = (function () {
            var me = {};

            me.renderBet = function () {
                if (schemeCollection.count()) {
                    betNum = $.map(schemeCollection.getArray(), function (scheme) {
                        return schemeCollection.calcBet(scheme);
                    }).reduce(function (a, b) {
                        return a + b;
                    });
                } else {
                    betNum = 0;
                }
                if (!showMore) {
                    betNum = currentBetNum;
                }
                betMoney = betNum * ONE_MONEY * multiModifier.getValue();
                buyMoneymin = Math.ceil(betMoney * rgpctmin / 100);
                buyMoney = (buyMoney < buyMoneymin) ? buyMoneymin : (buyMoney > betMoney ? betMoney : buyMoney);
                $betNum.html(betNum);
                $betMoney.html(betMoney);
                if ($('.guaranteeAll').attr('checked') || guarantee > betMoney - buyMoney) {
					guarantee = betMoney - buyMoney;
					$('.guaranteeAll').attr('checked', 'checked');
	            }
                hemai.renderGuarantee();
                hemai.renderBuyMoney();
            };

            me.renderCurrentBet = function () {
                if (schemeCollection.isComplete(schemeAry)) {
                    currentBetNum = schemeCollection.calcBet(schemeAry);
                } else {
                    currentBetNum = 0;
                }
                currentBetMoney = currentBetNum * ONE_MONEY;
                $currentBetNum.html(currentBetNum);
                $currentBetMoney.html(currentBetMoney);
            };

            me.getCastOptions = function () {
                var baseAry = showMore ? schemeCollection.getArray() : [schemeAry],
                    castStr = $.map(baseAry, function (scheme) {
                        return [$.map(scheme, function (option) {
                            return baseResultHash[option];
                        }).join(','), '1', '1'].join(':');
                    }).join(';');
                if (orderType == 4) {
                	return {
                        ctype: 'create',
                        buyPlatform: 0,
                        codes: castStr,
                        lid: lotteryId,
                        money: betMoney,
                        multi: multiModifier.getValue(),
                        issue: currIssue,
                        playType: 0,
                        betTnum: betNum,
                        isChase: 0,
                        orderType: 4,
                        endTime: hmDate.getFullYear()+"-"+padd(hmDate.getMonth() + 1)+"-"+padd(hmDate.getDate())+" "+padd(hmDate.getHours())+":"+padd(hmDate.getMinutes())+":"+padd(hmDate.getSeconds()),
                        codecc: '1 2 3 4 5 6 7 8 9 10 11 12 13 14',
                        isToken: 1,
                        buyMoney: buyMoney,
                        commissionRate: commission,
                        guaranteeAmount: guarantee,
                        openStatus: openStatus,
                        openEndtime: realendTime
                    };
                }else {
                	return {
                        ctype: 'create',
                        buyPlatform: 0,
                        codes: castStr,
                        lid: lotteryId,
                        money: betMoney,
                        multi: multiModifier.getValue(),
                        issue: currIssue,
                        playType: 0,
                        betTnum: betNum,
                        isChase: 0,
                        orderType: 0,
                        endTime: endTime,
                        codecc: '1 2 3 4 5 6 7 8 9 10 11 12 13 14',
                        isToken: 1
                    };
                }
                
            };
            
            hemai = function () {
            	var self = this;
                
            	$('.commission').on('click', 'li', function(){
            		console.log(1);
    				$('.commission li').removeClass('cur');
    				$(this).addClass('cur');
    				commission = $(this).data('val');
    				rgpctmin = (commission <= 5) ? 5 : commission;
    				buyMoneyTmp = Math.ceil(betMoney * rgpctmin / 100);
    				buyMoney = buyMoneyTmp > buyMoney ? buyMoneyTmp : buyMoney;
    				if ($('.guaranteeAll').attr('checked') || guarantee > betMoney - buyMoney) {
    					guarantee = betMoney - buyMoney;
    					$('.guaranteeAll').attr('checked', 'checked');
    					self.renderGuarantee();
    	            }
    				self.renderBuyMoney();
    			});
            	$('.buyMoney').on('blur', 'input', function(){
    				buyMoney = isNaN(parseInt($(this).val(), 10)) ? 0 : parseInt($(this).val(), 10);
    				buyMoneymin = Math.ceil(betMoney * rgpctmin / 100);
    				buyMoney = (buyMoney < buyMoneymin) ? buyMoneymin : (buyMoney > betMoney ? betMoney : buyMoney);
    				if ($('.guaranteeAll').attr('checked') || guarantee > betMoney - buyMoney) {
    					guarantee = betMoney - buyMoney;
    					$('.guaranteeAll').attr('checked', 'checked');
    					self.renderGuarantee();
    	            }
    				self.renderBuyMoney();
    			});
    			$('.guarantee').on('blur', 'input.form-item-ipt', function(){
    				guarantee = isNaN(parseInt($(this).val(), 10)) ? 0 : parseInt($(this).val(), 10);
    				$('.guaranteeAll').removeAttr('checked');
    				if (guarantee >= (betMoney - buyMoney)) {
    					guarantee = betMoney - buyMoney;
    					$('.guaranteeAll').attr('checked', 'checked');
    				}
    				guarantee = guarantee < 0 ? 0 : guarantee;
    				self.renderGuarantee();
    			});
    			$('.guaranteeAll').on('click', function(){
    				guarantee = betMoney - buyMoney;
    				self.renderGuarantee();
    			});
    			$('input[name=bmsz]').click(function(){
    				openStatus = $(this).val();
    			})
            };
            hemai.prototype = {
            	renderBuyMoney:function() {
        			$('.buyMoney input').val(buyMoney).parents('.buyMoney').find('span em:first').html(rgpctmin);
        	        buyMoney > 0 ? $('.buyMoney').find('u').show().find('em').html(Math.floor(buyMoney * 100/betMoney)) : $('.buyMoney').find('u').hide();
        			$('.guarantee span em:first').html(betMoney - buyMoney);
        			$('.buy_txt').html("<em class='main-color-s'>"+(buyMoney+guarantee)+"</em> 元 <span>（认购"+buyMoney+"元+保底"+guarantee+"元）</span></span>");
        	    },
        	    renderGuarantee:function() {
        	        $('.guarantee input.form-item-ipt').val(guarantee).parents('.guarantee').find('span em:last').html(betMoney == 0 ? 0 : Math.floor(guarantee * 100 /betMoney));
        	        $('.buy_txt').html("<em class='main-color-s'>"+(buyMoney+guarantee)+"</em> 元 <span>（认购"+buyMoney+"元+保底"+guarantee+"元）</span></span>");
        	    }
            };
            var hemai = new hemai();
            return me;
        })(),
        
        
        interval;

    multiModifier.setCb(function () {
    	$('.Multi').html(this.value);
        castData.renderCurrentBet();
        castData.renderBet();
    });

    $('.table-option').on({
        mouseover: function(){
            $(this).addClass('table-option-hover')
        },
        mouseout: function(){
            $(this).removeClass('table-option-hover')
        }
    });

    $('body').on('click', '.odds-refer', function () {
        var $this = $(this),
            opName = $this.html();
        $.post('ajax/queryReferOdds', {
            lid: lotteryId,
            cid: $this.data('cid'),
            issue: currIssue
        }, function (data) {
            $this.closest('.table-option-list').find('.selected').removeClass(selectedClass)
                .end().end().addClass(selectedClass);
            $('.match').each(function () {
                var $this = $(this),
                    mid = $this.data('index') + 1;
                if (!(mid in data)) {
                    data[mid] = {
                        oh: '0.00',
                        od: '0.00',
                        oa: '0.00'
                    };
                }
                $this.find('.pjop').children('.op-oh').html(parseFloat(data[mid]['oh']).toFixed(2)).end()
                    .children('.op-od').html(parseFloat(data[mid]['od']).toFixed(2)).end()
                    .children('.op-oa').html(parseFloat(data[mid]['oa']).toFixed(2)).end();
            });
            $('#op-name').html(opName + '<i class="table-option-arrow"></i>');
        }, 'json');
    }).on('click', '.filterPeriods', function () {
        location.assign([location.origin, location.pathname, '?issue=', $(this).data('issue')].join(''));
    }).on('click', '.bet-type-more, .btn-hemai', function () {
        var $this = $(this);
        showMore = true;
        $this.closest('.bet-type-danx').hide().nextAll('.bet-type-duox,.buy-type').show();
        if (schemeCollection.isComplete(schemeAry)) {
            $this.closest('.bet-main').find('.options').removeClass(selectedClass);
            selectedSchemes.checkBox();
            schemeCollection.empty();
            schemeCollection.add(schemeCollection.maxIndex() + 1, $.map(schemeAry, function (val) {
                return val;
            }));
            schemeCollection.initCurrent();
        }
        selectedSchemes.show();
        multiModifier.render();
        castData.renderCurrentBet();
        if ($(this).hasClass('btn-hemai')) $('#ordertype1').trigger('click');
    }).on('click', '.options', function () {
        var $this = $(this),
            index = $this.closest('.match').data('index'),
            val = $this.data('val'),
            isComplete;
        if ($this.hasClass(selectedClass)) {
            schemeAry[index] -= val;
            $this.removeClass(selectedClass);
        } else {
            schemeAry[index] += val;
            $this.addClass(selectedClass);
        }
        selectedSchemes.checkBox();
        isComplete = schemeCollection.isComplete(schemeAry);
        if (showMore) {
            $('.add-basket').toggleClass(disableClass, !isComplete);
            $('.modify-confirm').toggleClass(disableClass, !isComplete);
            castData.renderCurrentBet();
        } else {
            castData.renderCurrentBet();
            castData.renderBet();
        }
    }).on('click', '.add-basket', function () {
        var $this = $(this);
        if (!schemeCollection.isComplete(schemeAry)) {
            return void new cx.Alert({
                content: '请至少选择' + MIN_MATCH_COUNT + '场比赛'
            });
        }

        schemeCollection.add(schemeCollection.maxIndex() + 1, $.map(schemeAry, function (val) {
            return val;
        }));
        schemeCollection.initCurrent();
        $this.removeClass('modify-confirm').addClass('add-basket btn-disabled')
            .closest('.bet-main').find('.match').find('.options').removeClass(selectedClass);
        selectedSchemes.checkBox();
        selectedSchemes.show();
        castData.renderCurrentBet();
        castData.renderBet();
    }).on('click', '.modify-confirm', function () {
        var $this = $(this),
            $line = $this.closest('.btn-pools').next('.bet-area').find('.cast-list').children('.hover'),
            index = $line.data('index') || modifyIndex;
        if (!schemeCollection.isComplete(schemeAry)) {
            return void new cx.Alert({
                content: '请至少选择' + MIN_MATCH_COUNT + '场比赛'
            });
        }

        inModify = false;
        schemeCollection.add(index, $.map(schemeAry, function (val) {
            return val;
        }));
        schemeCollection.initCurrent();
        $this.removeClass('modify-confirm').addClass('add-basket btn-disabled')
            .html('添加到投注区<i class="icon-font"></i>').closest('.bet-main')
            .find('.match').find('.options').removeClass(selectedClass);
        selectedSchemes.checkBox();
        selectedSchemes.show();
        castData.renderCurrentBet();
        castData.renderBet();
    }).on('click', '.rand-cast', function () {
        var $this = $(this),
            volume = $this.data('volume'),
            zeroAry,
            tmpSchemeAry,
            cmbAry;

        for (var i = 0; i < volume; i++) {
            if (typeEnName == 'sfc') {
                zeroAry = [];
            } else {
                cmbAry = cx.Math.slowCombineList([0, 1, 2, 3, 4, 5, 6, 7, 8, 10, 11, 12, 13], 5);
                zeroAry = cmbAry[Math.floor(Math.random() * cmbAry.length)];
            }
            tmpSchemeAry = $.map(schemeAry, function (val, i) {
                return $.inArray(i, zeroAry) > -1 ? 0 : [1, 2, 4][Math.floor(Math.random() * 3)];
            });
            schemeCollection.add(schemeCollection.maxIndex() + 1, tmpSchemeAry);
        }
        selectedSchemes.show();
        castData.renderCurrentBet();
        castData.renderBet();
    }).on('click', '.modify-line', function () {
        var $this = $(this),
            $line = $this.parent(),
            index = $line.data('index'),
            curScheme = $.map(schemeCollection.get(index), function (val) {
                return val;
            });
        inModify = true;
        modifyIndex = index;
        $('.match').find('.options').removeClass(selectedClass)
            .end().each(function () {
            var $matchLn = $(this),
                matchIndex = $matchLn.data('index'),
                sum = curScheme[matchIndex];
            $matchLn.find('.options').each(function () {
                var $option = $(this),
                    val = $option.data('val');
                if (sum < val) {
                    return true;
                }
                sum -= val;
                $option.addClass(selectedClass);
            });
        });
        selectedSchemes.checkBox();
        $line.addClass(hoverClass).siblings().removeClass(hoverClass)
            .end().closest('.bet-type-duox')
            .find('.btn-add2bet').removeClass('add-basket btn-disabled').addClass('modify-confirm')
            .html('确认修改<i class="icon-font"></i>');
        schemeAry = $.map(curScheme, function (val) {
            return val;
        });
        castData.renderCurrentBet();
    }).on('click', '.delete-line', function () {
        var $this = $(this),
            $line = $this.parent(),
            index = $line.data('index'),
            $addToBet = $line.closest('.bet-type-duox').find('.btn-add2bet');
        schemeCollection.remove(index);
        $line.remove();
        if (inModify) {
            if ($line.hasClass(hoverClass)) {
                $addToBet.removeClass('modify-confirm').addClass('add-basket')
                    .html('添加到投注区<i class="icon-font"></i>');
            }
        } else {
            $('.options').removeClass(selectedClass);
            schemeCollection.initCurrent();
        }
        selectedSchemes.checkBox();
        castData.renderCurrentBet();
        castData.renderBet();
    }).on('click', '.clear-list', function () {
        var $this = $(this),
            $addToBet = $this.closest('.bet-type-duox').find('.btn-add2bet');
        $('.cast-list').empty();
        if (inModify) {
            $addToBet.removeClass('modify-confirm').addClass('add-basket')
                .html('添加到投注区<i class="icon-font"></i>');
            inModify = false;
        }
        schemeCollection.empty();
        castData.renderCurrentBet();
        castData.renderBet();
    }).on('click', '.select-all', function () {
        var $this = $(this),
            $matchLn = $this.closest('.match'),
            index = $matchLn.data('index'),
            $options = $matchLn.find('.options'),
            isComplete;
        if ($this.is(':checked')) {
            $options.each(function () {
                $(this).addClass(selectedClass);
            });
            schemeAry[index] = 7;
        } else {
            $options.each(function () {
                $(this).removeClass(selectedClass);
            });
            schemeAry[index] = 0;
        }
        isComplete = schemeCollection.isComplete(schemeAry);
        if (showMore) {
            $('.add-basket').toggleClass(disableClass, !isComplete);
            $('.modify-confirm').toggleClass(disableClass, !isComplete);
            castData.renderCurrentBet();
        } else {
            castData.renderCurrentBet();
            castData.renderBet();
        }
    }).find('.bet-main').on('click', '.submit', function () {
        var $this = $(this),
            $agreement = $this.closest('.btn-group').find('.agreement'),
            data;

        if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
            cx.PopAjax.login();
            return;
        }

        if ($this.hasClass('not-bind')) {
            //cx.PopAjax.bind();
            return ;
        }

        if ($agreement.get(0) && !$agreement.is(":checked")) {
            return void new cx.Alert({content: "请阅读并同意《用户委托投注协议》"});
        }

        if ((!showMore && !schemeCollection.isComplete(schemeAry))
            || (showMore && $.isEmptyObject(schemeCollection.getAll()))) {
            return void new cx.Alert({
                content: '请至少选择' + MIN_MATCH_COUNT + '场比赛'
            });
        }

        data = castData.getCastOptions();

        if (data.money > MAX_BET_MONEY) {
            new cx.Alert({
                content: ["<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>",
                    cx.Money.format(MAX_BET_MONEY), "</span>元，请修改订单后重新投注"].join('')
            });
            return;
        }
        cx.castCb(data, {ctype:'create', lotteryId:lotteryId, orderType:orderType, betMoney:betMoney, buyMoney:buyMoney, guarantee:guarantee, typeCnName:typeCnName, issue:currIssue});
    })

    interval = setInterval(function () {
        var d,
            h,
            m,
            s,
            str,
            pad = function (num) {
                return ('0' + num).slice(-2);
            };
        if (time == 0) {
            cx.Alert({
                'content': ['<p class="jiezhi tal">您好，', realTypeCnName, '第<span class="num-red">', currIssue,
                    '</span>期已截止，当前期是第<span class="num-red">', nextIssue,
                    '</span><br/>期，投注时请确认选择的期号</p>'].join(''),
                'confirmCb': function () {
                    clearInterval(interval);
                    location.assign([location.origin, location.pathname].join(''));
                }
            });
            alertLastTime--;
            if (alertLastTime == 0) {
                clearInterval(interval);
                location.assign([location.origin, location.pathname].join(''));
            }
        } else {
            time--;
            d = Math.floor(time / 86400);
            h = Math.floor((time % 86400) / 3600);
            m = Math.floor((time % 3600) / 60);
            s = Math.floor((time % 3600) % 60);
            if (d > 0) {
                str = "本期投注剩余：<em>" + pad(d) + "</em>天<em>" + pad(h) + "</em>小时<em>" + pad(m) + "</em>分";
            } else {
                str = "本期投注剩余：<em>" + pad(h) + "</em>小时<em>" + pad(m) + "</em>分<em>" + pad(s) + "</em>秒";
            }
            $(".count-down1").html(str);
        }
    }, 1000);
    $('.hmendTime .form-item-txt').html(hmDate.getFullYear() + "-" + padd(hmDate.getMonth() + 1) + "-" + padd(hmDate.getDate()) + " " + padd(hmDate.getHours()) + ":" + padd(hmDate.getMinutes()) + ":" + padd(hmDate.getSeconds()));
    $('.buy-type-hd').on('click', 'li', function(){
  	  if ($(this).index() == 1) {
  		  orderType = 4;
  	  	  $(this).find('.ptips-bd').hide();
  	  	  var str = '多人出资购买彩票，奖金按购买比例分享<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>合买：</em>选好投注号码后，由多人出资<br>购买彩票。中奖后，奖金按购买比例<br>分享。">&#xe613;</i>';
  	        $(".chase-number-notes").html(str);
  	  } else{
  		  orderType = 0;
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
    chgbtn();
    function chgbtn () {
    	if (selling == 1 && (orderType == 0 || (hmselling == 1 && hmendTime * 1000 >= (new Date()).valueOf()))) {
    		$("[id^=pd][id$=_buy]").removeClass('btn-disabled').addClass('needTigger submit').html('确认预约');
    		$('body').find('#buy_tip').remove();
    	}else {
    		$("[id^=pd][id$=_buy]").removeClass('needTigger submit').addClass('btn-disabled').html('暂停预约');
    		$('body').find('#buy_tip').remove();
    	}
    }
});

function padd(num) {
    return ('0' + num).slice(-2);
}

