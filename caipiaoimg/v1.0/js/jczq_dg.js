$(function() {

    var ONE_MONEY = 2;

    function round(f) {
        if (f == 0) {
            return '0.00';
        }
        return Math.round(f * 100) / 100;
    }

    var playOptionNames = {
        spf: {
            3: '胜',
            1: '平',
            0: '负'
        },
        rqspf: {
            3: '让胜',
            1: '让平',
            0: '让负'
        },
        bqc: {
            '33': '胜-胜',
            '31': '胜-平',
            '30': '胜-负',
            '13': '平-胜',
            '11': '平-平',
            '10': '平-负',
            '03': '负-胜',
            '01': '负-平',
            '00': '负-负'
        },
        jqs: {
            0: '0球',
            1: '1球',
            2: '2球',
            3: '3球',
            4: '4球',
            5: '5球',
            6: '6球',
            7: '7+球'
        },
        cbf: {
            '10': '1:0',
            '20': '2:0',
            '21': '2:1',
            '30': '3:0',
            '31': '3:1',
            '32': '3:2',
            '40': '4:0',
            '41': '4:1',
            '42': '4:2',
            '50': '5:0',
            '51': '5:1',
            '52': '5:2',
            '93': '胜其他',
            '00': '0:0',
            '11': '1:1',
            '22': '2:2',
            '33': '3:3',
            '91': '平其他',
            '01': '0:1',
            '02': '0:2',
            '12': '1:2',
            '03': '0:3',
            '13': '1:3',
            '23': '2:3',
            '04': '0:4',
            '14': '1:4',
            '24': '2:4',
            '05': '0:5',
            '15': '1:5',
            '25': '2:5',
            '90': '负其他'
        }
    };

    var BetChip = cx.BetChip = function(selector, options) {
        this.$el = $(selector);
        this.$yuans = this.$el.find('.yuan');

        this.chip = 1;

        this.init();
    };

    BetChip.prototype.init = function() {
        var self = this;
        this.$el.find('.yuan').click(function() {
            var $this = $(this);
            var chip = $this.data('chip');
            $this.addClass('selected').siblings().removeClass('selected');
            self.chip = chip;
        });
    };

    BetChip.prototype.getChip = function() {
        return this.chip;
    }

    var betChip = new cx.BetChip('.bet-chip');

    var Match = cx.Match = function(options) {
        /*
         * mid
         * let
         */
        this.options = options;
        this.let = parseInt(this.options.let, 10);
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
            spf: {},
            rqspf: {},
            jqs: {},
            bqc: {},
            cbf: {}
        };
        this.playOptionsDetail = {
            spf: {},
            rqspf: {}
        };
        this.playOptionsCount = {
            spf: 0,
            rqspf: 0,
            jqs: 0,
            bqc: 0,
            cbf: 0
        };
        this.domOptions = {
            spf: {},
            rqspf: {},
            jqs: {},
            bqc: {},
            cbf: {}
        };
    };

    Match.prototype.addDom = function(playType, option, $el) {
        this.domOptions[playType][option] = $el;
        $el.find('.money').html(this.playOptionsDetail[playType][option] * ONE_MONEY);
    }

    Match.prototype.addOption = function(playType, option, odd) {
        this.playOptions[playType][option] = parseFloat(odd);

        if (option in this.playOptionsDetail[playType]) {
            this.playOptionsDetail[playType][option] += betChip.getChip();
        } else {
            this.playOptionsDetail[playType][option] = betChip.getChip();
        }

        this.refreshOdds();
        this.optionCount += betChip.getChip();
        this.playOptionsCount[playType] += 1;
    };

    Match.prototype.removeDom = function(playType, option) {
        delete this.domOptions[playType][option];
    }

    Match.prototype.removeOption = function(playType, option) {
        delete this.playOptions[playType][option];
        this.refreshOdds();
        var detail = this.playOptionsDetail[playType][option];
        this.optionCount -= detail;
        this.playOptionsCount[playType] -= 1;
        this.playOptionsDetail[playType][option] = 0;
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
					if (playType.toUpperCase() == 'BQC') {
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
						minSum += minBqcOpt;
					} else {
						for (opt in opts) {
							sum += opts[opt];
							minSum += opts[opt];
						}
					}
				}
				if (sum > this.max) {
					this.max = sum;
				}
				if (minSum < this.min) {
					this.min = minSum;
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
        var bfs = [];

        (function() {
            var MAX_BF = 5;
            var i = 0;
            var j = 0;
            var bf =null;

            for (i = 0; i <= MAX_BF; ++i) {
                for (j = 0; j <= MAX_BF; ++j) {
                    if (i == 3 && j > 3 || i > 3 && j > 2) {
                        continue;
                    }
                    bfs.push({
                        name: i + '' + j,
                        jqs: (i + j) + '',
                        diff: Math.abs(i - j),
                        spf: i > j ? '3' : (i < j ? '0' : '1'),
                        home: i,
                        away: j
                    });
                }
            }
            bfs.push({
                name: '93',
                jqs: '7',
                spf: '3',
                home: 9,
                away: 0
            }, {
                name: '91',
                jqs: '7',
                spf: '1',
                home: 9,
                away: 9
            }, {
                name: '90',
                jqs: '7',
                spf: '0',
                home: 0,
                away: 9
            });
        })();

        me.getBfCheckMap = function(let) {
            var map = {};
            var bqc = [];
            for (i = 0; i < bfs.length; ++i) {
                bqc = [];
                bf = bfs[i];
                if (bf.spf === '3') {
                    bqc.push('13');
                    bqc.push('33');
                    if (bf.jqs > 2 && bf.away > 0) {
                        bqc.push('03');
                    }
                } else if (bf.spf === '1') {
                    if (bf.jqs > 1) {
                        bqc.push('01');
                        bqc.push('31');
                    }
                    bqc.push('11');
                } else if (bf.spf === '0') {
                    bqc.push('00');
                    bqc.push('10');
                    if (bf.jqs > 2 && bf.home > 0) {
                        bqc.push('30');
                    }
                }
                var home = parseInt(bf.home, 10);
                var away = parseInt(bf.away, 10);
                var rqspf;
                if (home + let > away) {
                    rqspf = '3';
                } else if (home + let == away) {
                    rqspf = '1';
                } else {
                    rqspf = '0';
                }
                map[bf.name] = {
                    spf: [bf.spf],
                    rqspf: [rqspf],
                    cbf: [bf.name],
                    jqs: [bf.jqs],
                    bqc: bqc
                };
            }
            return map;
        };


        me.getAllBf = function() {
            return bfs;
        };

        me.validateOptByBf = function(map, playOptions, bf) {
            var filter = map[bf];
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
            return [1];
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

        me.getTypes = function() {
            return ['1*1'];
            return types;
        };

        me.refreshGGType = function(count) {
            var matches = MatchCollection.getArray();
            for (var i = 0; i < matches.length; ++i) {
                if (matches[i].playOptionsCount.jqs > 0) {
                    count = Math.min(count, 6);
                }
                if (matches[i].playOptionsCount.bqc > 0 || matches[i].playOptionsCount.cbf > 0) {
                    count = Math.min(count, 4);
                }
            }
            $ggTypes.slice(0, Math.max(0, count - 1)).css('visibility', 'visible');
            $ggTypes.slice(count-1).css('visibility', 'hidden').removeClass('selected');
            if (count < 2) {
                types = [];
            } else {
                for (var j = 0; j < types.length; ++j) {
                    if (types[j].split('*')[0] > count) {
                        me.removeType(types[j]);
                    }
                }
            }
          if (matches.length > 1 && types.length == 0) {
                $ggTypes.eq(0).trigger('click');
            }
            SelectedMatches.show();
        };

        $ggTypes.click(function() {
            var $this = $(this);
            var type = $this.data('type');
            if ($this.hasClass('selected')) {
                $this.removeClass('selected');
                me.removeType(type);
            } else {
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
        if (a.length < 1) {
            return [];
        }
        if (a.length < 2) {
            for (var i = 0; i < a[0].length; ++i) {
                a[0][i] = [a[0][i]];
            }
            return a[0];
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
                            option: option,
                            playOptionChip: match.playOptionsDetail[playType][option]
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
                    multi = associates[ai][bi].playOptionChip;
                    casts.push(associates[ai][bi].cast);
                    odds *= associates[ai][bi].odd;
                    odds = Math.round(odds * 100) / 100;
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
                return aCombine.max > bCombine.max;
            });
            var maxSort = lenCombines;
            var minSort = [].concat(lenCombines);
            minSort.sort(function(aCombine, bCombine) {
                return aCombine.min > bCombine.min;
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
            if (matchesCount <= 0) {
                $minMoney.html('0.00');
                $maxMoney.html('0.00');
            }
            var ggCount = GGTypes.getOptions().length;
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
                        continue;
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
            if (finalCastStr == '') {
                $('.submit').trigger('click');
                return ;
            }
            var url = cx.url.getBusiUrl('ticket/order/create');
            var data = me.getCastOptions();
            data.codes = finalCastStr;
            data.isToken = 1;
            data.multi = -1;
            var self = this;
            new cx.Confirm({
                title: '确认购彩',
                content: '共' + data.money + '元，确认投注？',
                confirmCb: function() {
                    cx.ajax.post({
                        url: url,
                        data: data,
                        success: function(response) {
                            cx.castCb(response, self);
                        }
                    });
                }
            });
        });

        $('.start-detail').click(function() {
            var $detailPanel = $('.detail-panel');
            var className = '';
            var len;
            var matchesCount = MatchCollection.count();
            if (matchesCount < 1) {
                new cx.Alert({
                    content: '请至少选择一场比赛'
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
                console.log(combine);
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
                        odd = Math.round(odd * 100) / 100;
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
        $('.submit').click(function(e) {
            var $this = $(this);
            if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
            	me.notLogin($this, e);
                return ;
            }
            if (MatchCollection.count() < 1) {
                new cx.Alert({
                    content: '请至少选择一场比赛'
                });
                return ;
            }
            var url = cx.url.getBusiUrl('ticket/order/create');
            var data = me.getCastOptions();
            data.isToken = 1;
            var self = this;
            new cx.Confirm({
                title: '确认购彩',
                content: '共' + data.money + '元，确认投注？',
                confirmCb: function() {
                    cx.ajax.post({
                        url: url,
                        data: data,
                        success: function(response) {
                            cx.castCb(response, self);
                        }
                    });
                }
            });
        });

        function getCastStr() {
            var matches = MatchCollection.getArray();
            var ggTypes = GGTypes.getTypes();
            var castStr = '';
            ggTypes = ggTypes.join(',');
            var match;
            var playType;
            var option;
            var playOptions = [];
            var matchStrs = [];
            var tmpOption;
            for (var i = 0; i < matches.length; ++i) {
                match = matches[i];
                for (playType in match.playOptions) {
                    if ($.isEmptyObject(match.playOptions[playType])) {
                        continue;
                    }
                    for (option in match.playOptions[playType]) {
                        var matchStr = 'HH|' + playType.toUpperCase() + '>' + match.options.mid + '=';
                        var tmp = '';
                        if (playType == 'cbf') {
                            if (option == '93') {
                                tmpOption = '90';
                            } else if (option == '91') {
                                tmpOption = '99';
                            } else if (option == '90') {
                                tmpOption = '09';
                            } else {
                                tmpOption = option;
                            }
                            tmp += tmpOption.split('').join(':');
                        } else if (playType == 'bqc') {
                            tmp += option.split('').join('-');
                        } else {
                            tmp = '' + option;
                        }
                        if (playType == 'rqspf') {
                            tmp += '{' + match.let + '}'
                        }
                        tmp += '(' + match.playOptions[playType][option] + ')';
                        playOptions.push(tmp);
                        matchStr += tmp + '|' + ggTypes + '_' + match.playOptionsDetail[playType][option];
                        matchStrs.push(matchStr);
                    }
                }
            }
            castStr += matchStrs.join(';');
            return castStr;
        }

        me.getCastOptions = function() {
            var self = this;
            var castStr = getCastStr();
            var realBetNum = 0;
            var matches = MatchCollection.getArray();
            for (var i = 0; i < matches.length; ++i) {
                var match = matches[i];
                for (var playType in match.playOptions) {
                    for (var playOption in match.playOptions[playType]) {
                        realBetNum += 1;
                    }
                }
            }
            var data = {
                codes: castStr,
                buy_source: 0,
                lid: 42,
                money: betMoney,
                multi: -1,
                bet_tnum: realBetNum,
                issue: cx.Datetime.getToday(),
                is_chase: 0,
                order_type: 0,
                is_upload: 0,
                isToken: 1
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

    $('.matches').on('click', '.clear-all', function() {
        var $this = $(this);
        var $match = $this.closest('.matches');
        var mid = $match.data('mid');
        var match = MatchCollection.get(mid);
        for (var playType in match.domOptions) {
            for (var playOption in match.domOptions[playType]) {
                match.domOptions[playType][playOption].find('.clear').trigger('click');
            }
        }
    });

    $('.matches').on('click', '.spf-option', function() {
        var $this = $(this);
        var $match = $this.closest('.matches');
        onClick($this, $match, 'spf');
    });
    $('.matches').on('click', '.spf-option .clear', function(e) {
        e.stopPropagation();
        var $this = $(this);
        var $match = $this.closest('.matches');
        onClick($this.parent(), $match, 'spf', true);
    });
    $('.matches').on('click', '.rqspf-option', function() {
        var $this = $(this);
        var $match = $this.closest('.matches');
        onClick($this, $match, 'rqspf');
    });
    $('.matches').on('click', '.rqspf-option .clear', function(e) {
        e.stopPropagation();
        var $this = $(this);
        var $match = $this.closest('.matches');
        onClick($this.parent(), $match, 'rqspf', true);
    });
    $('.matches').on('click', '.bqc-option', function() {
        var $this = $(this);
        var $match = $this.closest('tr');
        if (!$match.hasClass('match')) {
            $match = $match.prevUntil('.match').last().prev();
        }
        onClick($this, $match, 'bqc');
    });
    $('.matches').on('click', '.jqs-option', function() {
        var $this = $(this);
        var $match = $this.closest('tr');
        if (!$match.hasClass('match')) {
            $match = $match.prevUntil('.match').last().prev();
        }
        onClick($this, $match, 'jqs');
    });
    $('.matches').on('click', '.cbf-option', function() {
        var $this = $(this);
        var $match = $this.closest('tr');
        if (!$match.hasClass('match')) {
            $match = $match.prevUntil('.match').last().prev();
        }
        onClick($this, $match, 'cbf');
    });

    var selectedClass = 'selected';
    function onClick($el, $match, type, isClear) {
        isClear || (isClear = false);
        var mid = $match.data('mid');
        var let = $match.data('let');
        var val = $el.attr('data-val');
        var odd = $el.data('odd');
        var wid = $match.data('wid');
        var home = $match.data('home');
        var away = $match.data('away');

        if (isClear) {
            $el.removeClass(selectedClass);
            cx.MatchCollection.get(mid).removeOption(type, val);
            cx.MatchCollection.get(mid).removeDom(type, val);
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
                       wid: wid
                    })
                );
            }
            cx.MatchCollection.get(mid).addOption(type, val, odd);
            cx.MatchCollection.get(mid).addDom(type, val, $el);
        }

        //GGTypes.refreshGGType(MatchCollection.count());
        CastData.refresh();
    }

    $('.open-cbf').click(function() {
        var $this = $(this);
        onPlayClick($this, 'cbf');
    });
    $('.open-jqs').click(function() {
        var $this = $(this);
        onPlayClick($this, 'jqs');
    });
    $('.open-bqc').click(function() {
        var $this = $(this);
        onPlayClick($this, 'bqc');
    });
    var playTypes = ['cbf', 'jqs', 'bqc'];
    function onPlayClick($el, type, has) {
        has || (has = 0);
        var $parent = $el.parent();
        var $match = $el.closest('.match');
        var $content = $match.nextUntil('.match').filter('.' + type + '-options');
        var mid = $match.data('mid');
        var match = MatchCollection.get(mid);
        var helpTxt = '';
        if ($content.hasClass('hidden')) {
            $el.html('收起').addClass('opened');
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
            $el.html('选择' + helpTxt).removeClass('opened');
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
            var vs = match.options.home + ' VS ' + match.options.away;
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
        if ($(this).hasClass('not-login')) {
            return ;
        }
        if (cx.CastData.getBetMoney() > 0) {
            new cx.CrowdBuy('.crowd-buy', cx.CastData.getCastOptions());
        }
    });

});
