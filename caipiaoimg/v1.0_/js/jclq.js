$(function() {

    var ONE_MONEY = 2;

    function round(f) {
        if (f == 0) {
            return '0.00';
        }
        return Math.round(f * 100) / 100;
    }

	var playTypeMap = {
        hh: 0,
		sf: 1,
        rfsf: 2,
        sfc: 3,
        dxf: 4
	};

    var playOptionNames = {
        sf: {
            3: '胜',
            0: '负'
        },
        rfsf: {
            3: '让胜',
            0: '让负'
        },
        sfc: {
            '01': '1-5',    //主胜
            '02': '6-10',
            '03': '11-15',
            '04': '16-20',
            '05': '21-25',
            '06': '26+',
            '11': '1-5',    //客胜
            '12': '6-10',
            '13': '11-15',
            '14': '16-20',
            '15': '21-25',
            '16': '26+'
        },
        dxf: {
            3: '大分',
            0: '小分'
        }
    };
    var Match = cx.Match = function(options) {
        /*
         * mid
         * let
         */
        this.options = options;
        this.let = parseFloat(this.options.let, 10);
        this.bfCheckMap = null;

        this.max = 0;
        this.min = Number.POSITIVE_INFINITY;
        this.maxOptions = [];
        this.minOptions = {};

        this.optionCount = 0;

        /*
         * option => sp
         */
        this.playOptions = {
            sf: {},
            rfsf: {},
            sfc: {},
            dxf: {}
        };
        this.playOptionsCount = {
            sf: 0,
            rfsf: 0,
            sfc: 0,
            dxf: 0
        };
        this.domOptions = {
            sf: {},
            rfsf: {},
            sfc: {},
            dxf: {}
        };
    };

    Match.prototype.addDom = function(playType, option, $el) {
        this.domOptions[playType][option] = $el;
    }

    Match.prototype.addOption = function(playType, option, odd) {
        this.playOptions[playType][option] = parseFloat(odd);

        this.refreshOdds();
        this.optionCount += 1;
        this.playOptionsCount[playType] += 1;
    };

    Match.prototype.removeDom = function(playType, option) {
        delete this.domOptions[playType][option];
    }

    Match.prototype.removeOption = function(playType, option) {
        delete this.playOptions[playType][option];
        this.refreshOdds();
        this.optionCount -= 1;
        this.playOptionsCount[playType] -= 1;
    };

    Match.prototype.refreshOdds = function () {
		this.max = 0;
		this.min = Number.POSITIVE_INFINITY;
		this.maxOptions = [];
		this.minoptions = {};
		var bfs = PlayOptions.getAllBf();
		if (this.bfCheckMap === null) {
			this.bfCheckMap = PlayOptions.getBfCheckMap(this.let);
		}
		var bf;
		var validOpts;
		var playType;
		var opts, opt;
		var sum;
		var minSum;
		var odds;
		var i;
		for (i = 0; i < bfs.length; ++i) {
			bf = bfs[i].name;
			validOpts = PlayOptions.validateOptByBf(this.bfCheckMap, this.playOptions, bf);
			if (!$.isEmptyObject(validOpts)) {
				sum = 0;
				minSum = 0;
				var minOdd = Number.POSITIVE_INFINITY;
				var maxOdd = 0;
				var minOpt = null;
				for (playType in validOpts) {
					opts = validOpts[playType];
					if (playType.toUpperCase() == 'DXF') {
						var maxBqcOpt = 0;
						var minBqcOpt = Number.POSITIVE_INFINITY;
						for (opt in opts) {
							if (opts[opt] > maxBqcOpt) {
								maxBqcOpt = opts[opt];
								validOpts[playType] = {};
								validOpts[playType][opt] = opts[opt];
							}
							if (opts[opt] < minBqcOpt) {
								minBqcOpt = opts[opt];
							}
						}
						sum += maxBqcOpt;
						if(minBqcOpt < minSum || minSum == 0){
							minSum = minBqcOpt;
						}
					} else {
						for (opt in opts) {
							sum += opts[opt];
							if(opts[opt] < minSum || minSum == 0){
								minSum = opts[opt];
							}
						}
					}
				}
				if (sum > this.max) {
					this.max = sum;
					this.maxOptions = validOpts;
				}
				if (minSum < this.min) {
					this.min = minSum;
					this.minOptions = validOpts;
				}
			}
		}
	};

    var MatchCollection = cx.MatchCollection = (function() {
        var me = {};
        var matches = {};

        me.add = function(mid, match) {
            if (!(mid in matches)) {
                matches[mid] = match;
            }
        };

        me.count = function() {
            var count = 0;
            for (var mid in matches) {
                if (matches[mid].optionCount > 0) {
                    count += 1;
                }
            }
            return count;
        };

        me.has = function(mid) {
            return (mid in matches);
        };

        me.get = function(mid) {
            if (mid in matches) {
                return matches[mid];
            }
            return null;
        };

        me.getAll = function() {
            return matches;
        };

        me.getArray = function() {
            var arr = [];
            for (var mid in matches) {
                if (matches[mid].optionCount > 0) {
                    arr.push(matches[mid]);
                }
            }
            return arr;
        }

        return me;
    })();

    var PlayOptions = cx.PlayOptions = (function() {
        var me = {};
        var sfcs = [];

        (function() {
            var dirs =  [1, -1];
            var allSfc = [
                {
                    min: 1,
                    max: 5
                }, {
                    min: 6,
                    max: 10
                }, {
                    min: 11,
                    max: 15
                }, {
                    min: 16,
                    max: 20
                }, {
                    min: 21,
                    max: 25
                }, {
                    min: 26,
                    max: Number.POSITIVE_INFINITY
                }
            ];
            var sf = 3;
            for (var di = 0; di < dirs.length; ++di) {
                if (di > 0) {
                    sf = 0;
                }
                for (var si = 0; si < allSfc.length; ++si) {
                    sfcs.push({
                        name: di + '' + (si + 1),
                        dir: dirs[di],
                        sf: sf,
                        min: allSfc[si].min * dirs[di],
                        max: allSfc[si].max * dirs[di]
                    });
                }
            }
        })();

        me.getBfCheckMap = function(letScore) {
            letScore = parseFloat(letScore, 10);
            var map = {};
            var dxf = [];
            var sfc;
            var rfsf = 0;
            for (var si = 0; si < sfcs.length; ++si) {
                dxf = ['3', '0'];
                rfsf = 0;
                sfc = sfcs[si];
                if (sfc.dir > 0) {
                    if (sfc.max > -letScore) {
                        rfsf = 3;
                    }
                } else {
                    if (-sfc.max < letScore) {
                        rfsf = 3;
                    }
                }
                /*if (sfc.dir > 0) {
                    if (sfc.max > -letScore) {
                        rfsf = 3;
                    }
                } else {
                    if (-sfc.min < letScore) {
                        rfsf = 3;
                    }
                }*/
                map[sfc.name] = {
                    sf: [sfc.sf + ''],
                    rfsf: [rfsf + ''],
                    dxf: dxf,
                    sfc: [sfc.name]
                };
            }
            return map;
        };

        me.getAllBf = function() {
            return sfcs;
        };

        me.validateOptByBf = function(map, playOptions, sfc) {
            var filter = map[sfc];
            var playType;
            var validOptions = {};
            var option;
            for (playType in playOptions) {
                for (option in playOptions[playType]) {
                    if ($.inArray(option, filter[playType]) > -1) {
                        if (!(playType in validOptions)) {
                            validOptions[playType] = {};
                        }
                        validOptions[playType][option] = playOptions[playType][option];
                    }
                }
            }
            return validOptions;
        }

        return me;
    })();

    var GGTypes = cx.GGTypes = (function() {
        var me = {};
        var map = {
            '1*1': [1],
            '2*1': [2],
            '3*1': [3],
            '4*1': [4],
            '5*1': [5],
            '6*1': [6],
            '7*1': [7],
            '8*1': [8]
        };
        var $ggTypes = $('.gg-type');
        var types = [];

        me.addType = function(type) {
            types.push(type);
            types.sort();
        };

        me.removeType = function(type) {
            var i = $.inArray(type, types);
            if (i > -1) {
                types.splice(i, 1);
            }
        };

        me.getOptions = function() {
            if (typeof fixedGGOptions !== 'undefined') {
                return fixedGGOptions;
            }
            var options = [];
            var type;
            for (var i = 0; i < types.length; ++i) {
                type = types[i];
                options = options.concat(map[type]);
            }
            return options;
        };
        
        me.hasType = function(type) {
            return $.inArray(type, types) > -1;
        };

        me.getTypes = function() {
            return types;
        };

        me.refreshGGType = function(count) {
            var matches = MatchCollection.getArray();
            for (var i = 0; i < matches.length; ++i) {
                if (matches[i].playOptionsCount.sfc > 0) {
                    count = Math.min(count, 4);
                }
            }
            $ggTypes.slice(0, Math.max(0, count)).css('visibility', 'visible');
            $ggTypes.slice(count).css('visibility', 'hidden').removeClass('selected');
            if (count < 1) {
                types = [];
            } else {
                for (var j = 0; j < types.length; ++j) {
                    if (types[j].split('*')[0] > count) {
                        me.removeType(types[j]);
                    }
                }
            }
            var isDg = true;
            for (var mi = 0; mi < matches.length; ++mi) {
                var match = matches[mi];
                for (var playType in match.playOptions) {
                    if (match.playOptionsCount[playType] > 0 && !match.options[playType + 'Fu']) {
                        isDg = false;
                        break;
                    }
                }
            }
            if (matches.length > 0 && types.length == 0) {
                if (isDg) {
                } else {
                    $ggTypes.eq(1).trigger('click');
                }
            }
          /*if (matches.length > 1 && types.length == 0) {
                $ggTypes.eq(0).trigger('click');
            }*/
            SelectedMatches.show();
        };

        $ggTypes.click(function() {
            var $this = $(this);
            var type = $this.data('type');
            if ($this.hasClass('selected')) {
                $this.removeClass('selected');
                me.removeType(type);
            } else {
            	if ($this.hasClass('single')) {
                    var matches = MatchCollection.getArray();
                    var playTypes = ['sf', 'rfsf', 'dxf', 'sfc'];
                    for (var i = 0; i < matches.length; ++i) {
                        var match = matches[i];
                        for (var j = 0; j < playTypes.length; ++j) {
                            var playType = playTypes[j];
                            if (!match.options[playType + 'Fu']) {
                                for (var playOption in match.domOptions[playType]) {
                                    match.domOptions[playType] [playOption].trigger('click');
                                }
                            }
                        }
                    }
                    $this.siblings().removeClass('selected');
                    types = [];

                    $this.css('visibility', 'visible');
                } else {
                    $this.siblings('.single').removeClass('selected');
                    me.removeType('1*1');
                }
            	
                $this.addClass('selected');
                me.addType(type);
            }
            CastData.refresh();
        });

        return me;
    })();

    var Combine = cx.Combine = function(matches) {
        this.matches = matches;
        this.len = matches.length;
        this.max = 1;
        this.min = 1;
        this.combineCount = 1;
        for (var i = 0; i < matches.length; ++i) {
            this.max *= matches[i].max;
            this.min *= matches[i].min;
            this.combineCount *= matches[i].optionCount;
        }
    };

    function multiArrs(a) {
        if (a.length < 2) {
            return [];
        }
        var r = joinArrs(a[0], a[1]);
        if (a.length == 2) {
            return r;
        } else {
            for (var i = 2; i < a.length; ++i) {
                r = joinArrs(r, a[i]);
            }
        }
        return r;
    }

    function joinArrs(a, b) {
        var i = 0;
        var j = 0;
        var c = [];
        var t;
        for (; i < a.length; ++i) {
            for (j = 0; j < b.length; ++j) {
                t = [];
                if (!$.isArray(a[i])) {
                    t.push(a[i]);
                } else {
                    t = t.concat(a[i]);
                }
                t.push(b[j]);
                c.push(t);
            }
        }
        return c;
    }

    Combine.prototype = {
        getAssociates: function(type) {
            type || (type  = 'playOptions');
            var tpl = [];
            var match;
            var wid;
            var t;
            var s;
            var ti = 0;
            var playType;
            var option;
            var associates = [];
            for (; ti < this.len; ++ti) {
                t = [];
                match = this.matches[ti];
                wid = match.options.wid;
                for (playType in match[type]) {
                    for (option in match[type][playType]) {
                        s = {
                            cast: '[' + wid + ' ' + playOptionNames[playType][option] + ']',
                            odd: match[type][playType][option],
                            options: match.options,
                            playType: playType,
                            option: option
                        };
                        t.push(s);
                    }
                }
                associates.push(t);
            }
            associates = multiArrs(associates);
            return associates;
        },
        toHtml: function(multi, type) {
            var associates = this.getAssociates(type);
            var ai = 0;
            var bi = 0;
            var liTpl = '';
            var casts;
            var odds;
            for ( ; ai < associates.length; ++ai) {
                casts = [];
                odds = 1;
                for (bi = 0; bi < associates[ai].length; ++bi) {
                    casts.push(associates[ai][bi].cast);
                    odds *= associates[ai][bi].odd;
                    odds = Math.round(odds * 1000) / 1000;
                }
                betMoney = ONE_MONEY * multi;
                liTpl += '<tr><td width="180">' + casts.join('*') + '</td>' +
                        '<td width="70">' + multi + '</td>' +
                        '<td width="70">' + betMoney + '</td>' +
                        '<td width="114">' + cx.Money.format(betMoney * odds) + '元</td></tr>';
            }
            return liTpl;
        }
    };

    var CombineCollection = (function() {
        var me = {};
        var combines = {};

        me.reset = function() {
            combines = {};
        };

        me.addByLen = function(lenCombines, len, cb) {
            if (!(len in combines)) {
                combines[len] = {};
            }
            lenCombines.sort(function(aCombine, bCombine) {
                return aCombine.max - bCombine.max;
            });
            var maxSort = lenCombines;
            var minSort = [].concat(lenCombines);
            minSort.sort(function(aCombine, bCombine) {
                return aCombine.min - bCombine.min;
            });
            combines[len].max = maxSort;
            combines[len].min = minSort;
            var combineCount = 0;
            $(lenCombines).each(function(key, lenCombine) {
                combineCount += lenCombine.combineCount;
            });
            if ($.isFunction(cb)) {
                cb(combineCount);
            }
        };

        me.getBySlice = function(len, type, start, end) {
            var combinesByLenByType = combines[len][type];
            end || (end = combinesByLenByType.length);
            return combinesByLenByType.slice(start, end);
        };

        me.getAllCombines = function() {
            var allCombines = [];
            var len;
            for (len in combines) {
                allCombines = allCombines.concat(combines[len].max);
            }
            return allCombines;
        };

        return me;
    })();

    var CastData = cx.CastData = (function() {
        var me = {};

        var betNum = 0;
        var betMoney = 0;
        var optimization = {};

        var multiModifier = new cx.AdderSubtractor('.multi-modifier');
        multiModifier.setCb(function() {
            betMoney = betNum * ONE_MONEY * this.value;
            me.renderBet();
            me.renderMinMaxMoney();
        });

        //doms
        var $count = $('.count-matches');
        var $minMoney = $('.min-money');
        var $maxMoney = $('.max-money');
        var $betNum = $('.bet-num');
        var $betMoney = $('.bet-money');

        me.getBetMoney = function() {
            return betMoney;
        };

        me.renderMinMaxMoney = function() {
            var len = 0;
            if (GGTypes.getOptions().length > 0) {
                len = GGTypes.getOptions()[0];
            }
            var matchesCount = MatchCollection.count();
            var ggCount = GGTypes.getOptions().length;
            if (matchesCount < 1 || ggCount < 1) {
                $minMoney.html('0.00');
                $maxMoney.html('0.00');
                return ;
            }
            var multi = multiModifier.getValue();
            var maxOdd = 0;
            var minOdd = 0;
            if (matchesCount in optimization) {
                $(optimization[matchesCount].max).each(function(key, combine) {
                    maxOdd += combine.max;
                });
                $(optimization[len].min).each(function(key, combine) {
                    minOdd += combine.min;
                });
                $minMoney.html(round(minOdd * ONE_MONEY * multi));
                $maxMoney.html(round(maxOdd * ONE_MONEY * multi));
            }
        };

        me.calcOptimization = function(len) {
            var combOptions = GGTypes.getOptions();
            var mi;
            var ci; 
            var type;
            var combOption;
            var matchesCount = MatchCollection.count();
            var tmpCount;
            var odds = {};
            for (mi = matchesCount; mi >= 1; --mi) {
                for (ci = 0; ci < combOptions.length; ++ci) {
                    combOption = combOptions[ci];
                    if (combOption > mi) {
                        continue ;
                    }
                    tmpCount = cx.Math.combine(mi, combOption);
                    if (!(mi in odds)) {
                        odds[mi] = {};
                    }
                    if ('max' in odds[mi]) {
                        odds[mi].max = odds[mi].max.concat(CombineCollection.getBySlice(combOption, 'max', -tmpCount));
                    } else {
                        odds[mi].max = CombineCollection.getBySlice(combOption, 'max', -tmpCount);
                    }
                    if ('min' in odds[mi]) {
                        odds[mi].min = odds[mi].min.concat(CombineCollection.getBySlice(combOption, 'min', 0, tmpCount));
                    } else {
                        odds[mi].min = CombineCollection.getBySlice(combOption, 'min', 0, tmpCount);
                    }
                }
            }
            return odds;
        };

        me.renderBet = function() {
            $betNum.html(betNum);
            $betMoney.html(betMoney);
        };

        me.calcCombines = function() {
            CombineCollection.reset();
            betNum = 0;
            betMoney = 0;
            var combOptions = GGTypes.getOptions();
            var allCombines = cx.Math.combineList(MatchCollection.getArray(), combOptions, function(matches) {
                return new Combine(matches);
            });
            var combOption;
            var combines;
            for (combOption in allCombines) {
                combines = allCombines[combOption];
                CombineCollection.addByLen(combines, combOption, function(combineCount) {
                    betNum += combineCount;
                });
            }
            betMoney = betNum * ONE_MONEY * multiModifier.getValue();
            me.renderBet();
        };

        me.refresh = function() {
            me.calcCombines();
            var matchesCount = MatchCollection.count();
            $count.html(matchesCount);
            optimization = me.calcOptimization();
            me.renderMinMaxMoney();
        };

        $('.detail-panel').find('.close').click(function() {
            $('.detail-panel').hide();
            cx.Mask.hide();
        });
        $('.detail-panel').find('.ave-optimize').click(function() {
            renderSafeOptimization();
        });
        $('.detail-panel').find('.reset').click(function() {
            finalCastStr = '';
            renderDetail();
        });
        var finalCastStr = '';
        $('.detail-panel').find('.do-cast').click(function() {

			if ($('.cast-panel').find(".ipt_checkbox#agreenment").get(0) && 
				!$('.cast-panel').find(".ipt_checkbox#agreenment").attr("checked")) {
				return void new cx.Alert({content: "请阅读并同意《用户委托投注协议》"});
			}

            if (finalCastStr == '') {
                $('.submit').trigger('click');
                return ;
            }
            var url = cx.url.getBusiUrl('ticket/order/create');
            var data = me.getCastOptions1();
            data.codes = finalCastStr;
            data.isToken = 1;
            data.multi = -1;
            var self = this;

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
							content: betInfo.jc( typeCnName, response.data.money, response.data.remain_money ),
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

        $('.start-detail').click(function() {
            var $detailPanel = $('.detail-panel');
            var className = '';
            var len;
            if (GGTypes.getOptions().length > 0) {
                len = GGTypes.getOptions()[0];
            } else {
                len = 0;
            }
            var matchesCount = MatchCollection.count();
            if (matchesCount < 2 || len == 0) {
                new cx.Alert({
                    content: '请选择场次和过关方式'
                });
                return ;
            }
            renderDetail();
            cx.Mask.show();
            $detailPanel.show();
        });

        function renderDetail() {
            var len = 0;
            if (GGTypes.getOptions().length > 0) {
                len = GGTypes.getOptions()[0];
            }
            var minOption = Math.max(len, Math.min(MatchCollection.count(), 1));
            var leftListTpl = '';
            var lenOpt;
            var maxOdd;
            var minOdd;
            var multi = multiModifier.getValue();
            for (var matchLen in optimization) {
                maxOdd = 0;
                minOdd = 0;
                if (matchLen < minOption) {
                    continue ;
                }
                lenOpt = optimization[matchLen];
                $(lenOpt.max).each(function(key, combine) {
                    maxOdd += combine.max;
                });
                $(lenOpt.min).each(function(key, combine) {
                    minOdd += combine.min;
                });
                leftListTpl = '<tr class="detail-li"><th width="25%">中' + matchLen + '场</th>' +
                               '<td width="75%"><h4>最大奖金：' + round(maxOdd * ONE_MONEY * multi) + '</h4>' +
                               '<h5>最小奖金：' + round(minOdd * ONE_MONEY * multi) + '</h5></td></tr>' + leftListTpl;
            }
            $('.prize-list').html(leftListTpl);

            var allCombines = CombineCollection.getAllCombines();
            var rightDetailTpl = '';
            var combine;
            var tpl = [];
            var result;
            var casts = [];
            var odds = 1;
            var betMoney;
            var multi = multiModifier.getValue();
            var ai = 0;
            var combine;
            for (; ai < allCombines.length; ++ai) {
                combine = allCombines[ai];
                rightDetailTpl += combine.toHtml(multi);
            }
            $('.prize-detail').html(rightDetailTpl);
        }
        function renderSafeOptimization() {
            var multi = multiModifier.getValue();
            if (multi <= 1) {
                return ;
            }
            var allCombines = CombineCollection.getAllCombines();
            var tpl = [];
            var result;
            var casts = [];
            var castOption = [];
            var odds = 1;
            var betMoney;
            var castOdds = [];
            var c = 0;

            var ci = 0;
            var ai = 0;
            var oi = 0;
            var combine;
            var associates;
            var combinesOdd = [];
            var odd = 1;
            var casts = [];
            var allAssociates = [];
            for ( ; ci < allCombines.length; ++ci) {
                combine = allCombines[ci];
                associates = combine.getAssociates();
                for (ai = 0; ai < associates.length; ++ai) {
                    allAssociates.push(associates[ai]);
                    casts = [];
                    odd = 1;
                    for (oi = 0; oi < associates[ai].length; ++oi) {
                        casts.push(associates[ai][oi].cast);
                        odd *= associates[ai][oi].odd;
                        odd = Math.round(odd * 1000) / 1000;
                    }
                    combinesOdd.push(odd);
                }
            }
            var harmonic = cx.Math.harmonic.apply(window, combinesOdd);
            var allCasts = [];
            var originalSum = combinesOdd.length * multi;
            var ceilSum = 0;
            var key = [];
            var playOptions = [];
            var playUnique;
            var casts = [];
            var castMatch;
            var multi = 1;
            
            for (ai = 0; ai < allAssociates.length; ++ai) {
                casts = [];
                odd = 1;
                playOptions = [];
                playUnique = [];
                for (oi = 0; oi < allAssociates[ai].length; ++oi) {
                    casts.push(allAssociates[ai][oi].cast);
                    odd *= allAssociates[ai][oi].odd;
                    castMatch = allAssociates[ai][oi].playType.toUpperCase() + '>' + allAssociates[ai][oi].options.mid + '=';
                    if (allAssociates[ai][oi].playType.toLowerCase() == 'cbf') {
                        if (allAssociates[ai][oi].option == '93') {
                            castMatch += '9:0';
                        } else if (allAssociates[ai][oi].option == '91') {
                            castMatch += '9:9';
                        } else if (allAssociates[ai][oi].option == '90') {
                            castMatch += '0:9';
                        } else {
                            castMatch += allAssociates[ai][oi].option.split('').join(':');
                        }
                    } else if (allAssociates[ai][oi].playType.toLowerCase() == 'bqc') {
                        castMatch += allAssociates[ai][oi].option.split('').join('-');
                    } else {
                        castMatch += allAssociates[ai][oi].option;
                    }
                    if (allAssociates[ai][oi].playType.toLowerCase() == 'rqspf') {
                        castMatch += '{' + allAssociates[ai][oi].options.let + '}';
                    }
                    castMatch += '(' + allAssociates[ai][oi].odd + ')';
                    playOptions.push(castMatch);
                    playUnique.push(allAssociates[ai][oi].options.mid + '_' + allAssociates[ai][oi].playType + '_' + allAssociates[ai][oi].option);
                }
                casts = casts.join('*');
                multi = Math.ceil(harmonic / odd * originalSum);
                ceilSum += multi;
                allCasts.push({
                    key: playUnique.join('*'),
                    casts: casts,
                    playOptions: playOptions.join(','),
                    odd: odd,
                    multi: multi,
                    gap: multi - harmonic / odd * originalSum,
                    count: playOptions.length
                });
            }
            var allGap = ceilSum - originalSum;
            allCasts.sort(function(a, b) {
                return a.gap < b.gap;
            });
            for (var i = 0; i < allCasts.length && allGap > 0; ++i) {
                if (allCasts[i].multi > 1) {
                    allCasts[i].multi -= 1;
                    allGap -= 1;
                }
            }
            var multiMoney;
            var cast;
            var finalCast = [];
            var oAllCasts = {};
            var rightDetailTpl = '';
            for (var i = 0; i < allCasts.length; ++i) {
                cast = allCasts[i];
                oAllCasts[cast.key] = cast;
                finalCast.push('HH|' + cast.playOptions + '|' + cast.count + '*1_' + cast.multi);
                multiMoney = cast.multi * ONE_MONEY;
                rightDetailTpl += '<tr><td width="180">' + cast.casts + '</td>' +
                        '<td width="50">' + cast.multi + '倍</td>' +
                        '<td width="70">' + multiMoney + '元</td>' +
                        '<td width="114">' + round(multiMoney * cast.odd) + '元</td></tr>';
            }
            finalCastStr = finalCast.join(';');
            $('.prize-detail').html(rightDetailTpl);

            var leftListTpl = '';
            for (var c = MatchCollection.count(); c >=2; --c) {
                leftListTpl += renderPrizeList(c, oAllCasts);
            }
            $('.prize-list').html(leftListTpl);
        }
        
        function renderPrizeList(len, oAllCasts) {
            var data = optimization[len];
            var maxCombs = data.max;
            var minCombs = data.min;
            var combine;
            var maxSum = 0;
            var minSum = 0;
            var tmp;
            var key;
            for (var i = 0; i < maxCombs.length; ++i) {
                combine = maxCombs[i];
                tmp = combine.getAssociates('maxOptions');
                for (var j = 0; j < tmp.length; ++j) {
                    key = tmp[j][0].options.mid + '_' + tmp[j][0].playType + '_' + tmp[j][0].option + '*' + tmp[j][1].options.mid + '_' + tmp[j][1].playType + '_' + tmp[j][1].option;
                    maxSum += oAllCasts[key].odd * oAllCasts[key].multi * ONE_MONEY;
                }
            }
            for (i = 0; i < minCombs.length; ++i) {
                combine = minCombs[i];
                tmp = combine.getAssociates('minOptions');
                for (j = 0; j < tmp.length; ++j) {
                    key = tmp[j][0].options.mid + '_' + tmp[j][0].playType + '_' + tmp[j][0].option + '*' + tmp[j][1].options.mid + '_' + tmp[j][1].playType + '_' + tmp[j][1].option;
                    minSum += oAllCasts[key].odd * oAllCasts[key].multi * ONE_MONEY;
                }
            }
            return '<tr class="detail-li"><th width="25%">中' + len + '场</th>' +
                   '<td width="75%"><h4>最大奖金：' + round(maxSum) + '</h4><h5>最小奖金：' + round(minSum) + '</h5></td></tr>';
        }
        $('.not-login').click(function(e) {
            var $this = $(this);
            me.notLogin($this, e);
            /*if ($this.hasClass('not-login')) {
                cx.PopLogin.show();
				e.stopImmediatePropagation();
			}
			e.preventDefault();*/
        });
        $('.not-bind').click(function(e) {
        	var $this = $(this);
            if ($this.hasClass('not-bind') && $.cookie('name_ie')) {
                cx.PopBind.show();
				e.stopImmediatePropagation();
			}
			e.preventDefault();
        });
        $('.cast-panel').find('.submit').click(function(e) {
            var $this = $(this);
            if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
            	me.notLogin($this, e);
                return ;
            }

			if ($('.cast-panel').find(".ipt_checkbox#agreenment").get(0) && 
				!$('.cast-panel').find(".ipt_checkbox#agreenment").attr("checked")) {
				return void new cx.Alert({content: "请阅读并同意《用户委托投注协议》"});
			}

            if ($this.hasClass('not-bind')) {
                return ;
            }
            /*if (MatchCollection.count() <= 1) {
                new cx.Alert({
                    content: '请至少选择两场比赛'
                });
                return ;
            }*/
            if (MatchCollection.count() < 1) {
                new cx.Alert({
                    content: '请至少选择一场比赛'
                });
                return ;
            }
            if(MatchCollection.count() == 1){
            	if(!($.inArray('1*1', GGTypes.getTypes()) > -1)){
            		 new cx.Alert({
                         content: '请至少选择两场比赛'
                     });
            		 return ;
            	}
            }
            if (GGTypes.getTypes().length <= 0) {
                new cx.Alert({
                    content: '请至少选择一种过关方式'
                });
                return ;
            }
            var url = cx.url.getBusiUrl('ticket/order/create');
            var data = me.getCastOptions1();
            data.isToken = 1;
            var self = this;

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
							content: betInfo.jc( typeCnName, response.data.money, response.data.remain_money ),
							input: 1,
							confirmCb: function() {
								if(this.input){
	                    			datas.pay_pwd = $('#pay_pwd').val();
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

        function getCastStr() {
            var matches = MatchCollection.getArray();
            var ggTypes = GGTypes.getTypes();
            var castStr = 'HH|';
            ggTypes = ggTypes.join(',');
            var matchStr = '';
            var match;
            var playType;
            var option;
            var playOptions = [];
            var matchStrs = [];
            var tmpOption;
            var endTime = '3000-12-31 23:59:59';
            var mids = [];
            for (var i = 0; i < matches.length; ++i) {
                match = matches[i];
                endTime = (endTime > match.options.jzdt)? match.options.jzdt : endTime;
                mids.push(match.options.mid);
                for (playType in match.playOptions) {
                    if ($.isEmptyObject(match.playOptions[playType])) {
                        continue;
                    }
                    playOptions = [];
                    matchStr = playType.toUpperCase() + '>' + match.options.mid + '=';
                    for (option in match.playOptions[playType]) {
                        var tmp = '' + option;
                        if (playType == 'rfsf') {
                            tmp += '{' + match.let + '}'
                        }
                        tmp += '(' + match.playOptions[playType][option] + ')';
                        playOptions.push(tmp);
                    }
                    matchStr += playOptions.join('/');
                    if(playType == 'dxf'){
                    	matchStr += '{' + match.options.prescore + '}'; 
                    }
                    matchStrs.push(matchStr);
                }
            }
            castStr += matchStrs.join(',') + '|' + ggTypes + '@' + endTime + '@' + mids.join(' ');
            return castStr;
        }

        me.getCastOptions = function() {
            var self = this;
            var castStr = getCastStr().split('@');
            var endTime = castStr[1];
            var codecc = castStr[2];
            castStr = castStr[0];
            var data = {
                codes: castStr,
                buy_source: 0,
                lid: 43,
                money: betMoney,
                multi: multiModifier.getValue(),
                bet_tnum: betNum,
                issue: cx.Datetime.getToday(),
                is_chase: 0,
                order_type: 0,
                is_upload: 0,
                isToken: 1,
                codecc: codecc
            };
            return data;
        };

        me.getCastOptions1 = function() {
        	var self = this;
            var castStr = getCastStr().split('@');
            var endTime = castStr[1];
            var codecc = castStr[2];
            castStr = castStr[0];
            var data = {
            	ctype: 'create',
            	buyPlatform: 0,
                codes: castStr,
                lid: 43,
                money: betMoney,
                multi: multiModifier.getValue(),
                issue: cx.Datetime.getToday(),
                playType: playTypeMap[type],
                betTnum: betNum,
                isChase: 0,
                orderType: 0,
                endTime: endTime,
                codecc: codecc
            };
            return data;
        };
        
        me.notLogin = function($this, e) {
        	if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
                cx.PopLogin.show();
				e.stopImmediatePropagation();
			}
			e.preventDefault();
        };

        return me;
    })();

    $('.matches').on('click', '.sf-option', function() {
        var $this = $(this);
        var $match = $this.closest('tr');
        onClick($this, $match, 'sf');
    });
    $('.matches').on('click', '.rfsf-option', function() {
        var $this = $(this);
        var $match = $this.closest('tr');
        if (!$match.hasClass('match')) {
            $match = $match.prev();
        }
        onClick($this, $match, 'rfsf');
    });
    $('.matches').on('click', '.dxf-option', function() {
        var $this = $(this);
        var $match = $this.closest('tr');
        if (!$match.hasClass('match')) {
            $match = $match.prevUntil('.match').last().prev();
        }
        onClick($this, $match, 'dxf');
    });
    $('.matches').on('click', '.sfc-option', function() {
        var $this = $(this);
        var $match = $this.closest('tr');
        if (!$match.hasClass('match')) {
            $match = $match.prevUntil('.match').last().prev();
        }
        onClick($this, $match, 'sfc');
    });

    var selectedClass = 'selected';
    function removeMatch($el, $match, mid, type, val){
    	$el.removeClass(selectedClass);
        cx.MatchCollection.get(mid).removeOption(type, val);
        if (cx.MatchCollection.get(mid).playOptionsCount[type] == 0) {
            $match.find('.open-' + type).removeClass('selected').html('选择投注');
        }
        cx.MatchCollection.get(mid).removeDom(type, val);
    };
    
    function onClick($el, $match, type) {
        var mid = $match.data('mid');
        var let = $match.data('let');
        var val = $el.attr('data-val');
        var odd = $el.data('odd');
        var wid = $match.data('wid');
        var home = $match.data('home');
        var away = $match.data('away');
        var jzdt = $match.data('jzdt');
        var sfFu = $match.data('sf_fu');
        var rfsfFu = $match.data('rfsf_fu');
        var sfcFu = $match.data('sfc_fu');
        var dxfFu = $match.data('dxf_fu');
        var prescore = $match.data('prescore');
        
        if (GGTypes.hasType('1*1')) {
            if (!$match.data(type + '_fu')) {
                new cx.Alert({
                    content: '该比赛不支持单关玩法'
                });
                return ;
            }
        }
        
       if ($el.hasClass(selectedClass)) {
    	   removeMatch($el, $match, mid, type, val);
       } else {
           $el.addClass(selectedClass);
           if (!cx.MatchCollection.has(mid)) {
               cx.MatchCollection.add(
                   mid,
                   new cx.Match({
                       mid: mid,
                       home: home,
                       away: away,
                       let: let,
                       wid: wid,
                       sfFu: sfFu,
                       rfsfFu: rfsfFu,
                       sfcFu: sfcFu,
                       dxfFu: dxfFu,
                       jzdt: jzdt,
                       prescore: prescore
                   })
               );
           }
           cx.MatchCollection.get(mid).addOption(type, val, odd);
           $match.find('.open-' + type).addClass('selected');
           cx.MatchCollection.get(mid).addDom(type, val, $el);
           if(MatchCollection.count() > 15) {
        	   new cx.Alert({
                   content: '只支持15场以内的投注！'
               });
        	   removeMatch($el, $match, mid, type, val);
        	   return ;
           }
       }
        GGTypes.refreshGGType(MatchCollection.count());
        CastData.refresh();
    }

    $('.open-sfc').click(function() {
        var $this = $(this);
        onPlayClick($this, 'sfc');
    });
    var playTypes = ['sfc'];
    function onPlayClick($el, type, has) {
        has || (has = 0);
        var $parent = $el.parent();
        var $match = $el.closest('.match');
        var $content = $match.nextUntil('.match').filter('.' + type + '-options');
        var mid = $match.data('mid');
        var match = MatchCollection.get(mid);
        var helpTxt = '';
        if ($content.hasClass('hidden')) {
            $el.html('收起投注').addClass('opened');
            $parent.addClass('opened');
            $content.removeClass('hidden');
            if (!has) {
                for (var i = 0; i < playTypes.length; ++i) {
                    if (type == playTypes[i]) {
                        continue;
                    }
                    var $otherEl = $match.find('.open-' + playTypes[i]);
                    if ($otherEl.hasClass('opened')) {
                       onPlayClick($otherEl, playTypes[i], 1);
                    }
                }
            }
        } else {
            if (match != null) {
                if (match.playOptionsCount[type] > 0) {
                    helpTxt = ' 已选' + match.playOptionsCount[type] + '项';
                } else {
                    helpTxt = '';
                }
            }
			
			if( !helpTxt ){
				helpTxt = '展开投注';
			}

            $el.html( helpTxt ).removeClass('opened');
            $parent.removeClass('opened');
            $content.addClass('hidden');
        }
    }

    var SelectedMatches = (function() {
        var me = {};

        me.show = function() {
            var matches = MatchCollection.getArray();
            var tpl = '';
            for (var i = 0; i < matches.length; ++i) {
                tpl += renderMatch(i, matches[i]);
            }
            $('.selected-matches').html(tpl);
            $('.selected-matches').find('.selected-option').click(function() {
                var $this = $(this);
                var index = $this.data('index');
                var type = $this.data('type');
                var val = $this.attr('data-val');
                var match = matches[index];
                var $el = match.domOptions[type][val];
                $el.trigger('click');
            });
            $('.selected-matches').find('.del-match').click(function() {
                var $this = $(this);
                var index = $this.data('index');
                var match = matches[index];
                for (var playType in match.domOptions) {
                    for (var val in match.domOptions[playType]) {
                        match.domOptions[playType][val].trigger('click');
                    }
                }
            });
            $('.clear-matches').click(function() {
                $('.selected-matches').find('.del-match').trigger('click');
            });
        }

        function renderMatch(i, match) {
            var vs = match.options.away + ' VS ' + match.options.home;
            var optionTpl = '';
            for (var playType in match.playOptions) {
                for (var option in match.playOptions[playType]) {
                    optionTpl += '<span class="selected-option" data-index="' + i + '" data-type="' + playType + '" data-val="' + option + '">' + playOptionNames[playType][option] + '</span>';
                }
            }
            var tpl = '<tr data-mid="' + match.options.mid + '"><td width="170" style="text-align:center">' + vs + '</td><td width="210">' + optionTpl + '</td><td width="50" style="text-align:center"><a class="del-match" data-index="' + i + '">删除</a></td></tr>';

            return tpl;
        }
        return me;
    })();

    var crowdBuy = null;
    $('.start-crowd').click(function() {
        if (cx.CastData.getBetMoney() > 0) {
            new cx.CrowdBuy('.crowd-buy', cx.CastData.getCastOptions());
        }
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
                    location.href = location.href;
                }
            });
        } else {
            if (response.code == 12) {
                new cx.Confirm({
					content: betInfo.jc( typeCnName, response.data.money, response.data.remain_money ),
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

});
