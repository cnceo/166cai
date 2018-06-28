$(function () {
    var ONE_MONEY = 2,
        MAX_OPTION_MULTI = 100000,
        MIN_OPTION_MULTI = 1,
        MAX_MATCH_COUNT = 15,
        MAX_BET_MONEY = 200000,   
        MAX_OPT_BET = 1000;

    function round(f) {
        if (f == 0) {
            return '0.00';
        }
        return Math.round(f * 100) / 100;
    }

    var playTypeMap = {
        hh: 0,
        spf: 1,
        rqspf: 2,
        bqc: 3,
        jqs: 4,
        cbf: 5,
        dg: 6
    };

    var playOptionNames = {
		spf: {
            3: '主胜',
            1: '平',
            0: '客胜'
        },
        rqspf: {
            3: '主胜【让】',
            1: '平【让】',
            0: '客胜【让】'
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

    var playOptionOrder = {
        spf: {
            3: 0,
            1: 1,
            0: 2
        },
        rqspf: {
            3: 0,
            1: 1,
            0: 2
        },
        bqc: {
            '33': 0,
            '31': 1,
            '30': 2,
            '13': 3,
            '11': 4,
            '10': 5,
            '03': 6,
            '01': 7,
            '00': 8
        },
        jqs: {
            0: 0,
            1: 1,
            2: 2,
            3: 3,
            4: 4,
            5: 5,
            6: 6,
            7: 7
        },
        cbf: {
            '10': 0,
            '20': 1,
            '21': 2,
            '30': 3,
            '31': 4,
            '32': 5,
            '40': 6,
            '41': 7,
            '42': 8,
            '50': 9,
            '51': 10,
            '52': 11,
            '93': 12,
            '00': 13,
            '11': 14,
            '22': 15,
            '33': 16,
            '91': 17,
            '01': 18,
            '02': 19,
            '12': 20,
            '03': 21,
            '13': 22,
            '23': 23,
            '04': 24,
            '14': 25,
            '24': 26,
            '05': 27,
            '15': 28,
            '25': 29,
            '90': 30
        }
    };

    var orderedPlayType = ['spf', 'rqspf', 'bqc', 'cbf', 'jqs'];

    var Match = cx.Match = function (options) {
        this.options = options;
        this.handicap = parseInt(this.options.handicap, 10);
        this.bfCheckMap = null;

        this.max = 0;
        this.min = Number.POSITIVE_INFINITY;
        this.maxOptions = {};
        this.minOptions = {};

        this.optionCount = 0;

        this.playOptions = {
            spf: {},
            rqspf: {},
            bqc: {},
            cbf: {},
            jqs: {}
        };
        this.playOptionsCount = {
            spf: 0,
            rqspf: 0,
            bqc: 0,
            cbf: 0,
            jqs: 0
        };
        this.domOptions = {
            spf: {},
            rqspf: {},
            bqc: {},
            cbf: {},
            jqs: {}
        };
        this.originalPlayOptions = {
            spf: {},
            rqspf: {},
            bqc: {},
            cbf: {},
            jqs: {}
        };
        this.multiOptions = {
            spf: {},
            rqspf: {},
            bqc: {},
            cbf: {},
            jqs: {}
        };
    };

    Match.prototype.addDom = function (playType, option, $el) {
        this.domOptions[playType][option] = $el;
    };

    Match.prototype.addOption = function (playType, option, odd, multi) {
        multi || (multi = 1);
        this.playOptions[playType][option] = parseFloat(odd);
        this.originalPlayOptions[playType][option] = parseFloat(odd);
        this.multiOptions[playType][option] = parseInt(multi, 10);
        this.refreshOdds();
        this.optionCount += 1;
        this.playOptionsCount[playType] += 1;
    };

    Match.prototype.changeMulti = function (playType, option, multi) {
        this.playOptions[playType][option] = this.originalPlayOptions[playType][option] * parseInt(multi, 10);
        this.multiOptions[playType][option] = parseInt(multi, 10);
        this.refreshOdds();
    };

    Match.prototype.removeDom = function (playType, option) {
        delete this.domOptions[playType][option];
    };

    Match.prototype.removeOption = function (playType, option) {
        delete this.playOptions[playType][option];
        delete this.originalPlayOptions[playType][option];
        delete this.multiOptions[playType][option];
        this.refreshOdds();
        this.optionCount -= 1;
        this.playOptionsCount[playType] -= 1;
    };

    Match.prototype.refreshOdds = function () {
        this.max = 0;
        this.min = Number.POSITIVE_INFINITY;
        this.maxOptions = {};
        this.minoptions = {};
        var bfs = PlayOptions.getAllBf();
        if (this.bfCheckMap === null) {
            this.bfCheckMap = PlayOptions.getBfCheckMap(this.handicap);
        }
        var bf;
        var validOpts;
        var sum;
        var minSum;
        var i;
        for (i = 0; i < bfs.length; ++i) {
            bf = bfs[i].name;
            validOpts = PlayOptions.validateOptByBf(this.bfCheckMap, this.playOptions, bf);
            if (!$.isEmptyObject(validOpts)) {
                sum = 0;
                minSum = 0;
                $.each(validOpts, function (playType, opts) {
                    if (playType.toUpperCase() == 'BQC') {
                        var maxBqcOpt = 0;
                        var minBqcOpt = Number.POSITIVE_INFINITY;
                        $.each(opts, function (opt, optValue) {
                            if (optValue > maxBqcOpt) {
                                maxBqcOpt = optValue;
                                validOpts[playType] = {};
                                validOpts[playType][opt] = optValue;
                            }
                            if (optValue < minBqcOpt) {
                                minBqcOpt = optValue;
                            }
                        });
                        sum += maxBqcOpt;
                        minSum += minBqcOpt;
                    } else {
                        $.each(opts, function (opt, optValue) {
                            sum += optValue;
                            minSum += optValue;
                        })
                    }
                });
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

    var MatchCollection = cx.MatchCollection = (function () {
        var me = {};
        var matches = {};

        me.add = function (mid, match) {
            if (!(mid in matches)) {
                matches[mid] = match;
            }
        };

        me.count = function () {
            var count = 0;
            $.each(matches, function (mid, match) {
                if (match.optionCount > 0) {
                    count += 1;
                }
            });

            return count;
        };

        me.has = function (mid) {
            return (mid in matches);
        };

        me.get = function (mid) {
            if (mid in matches) {
                return matches[mid];
            }
            return null;
        };

        me.getAll = function () {
            return matches;
        };

        me.getArray = function () {
            return $.map(matches, function (match) {
                if (match.optionCount > 0) {
                    return match;
                }
            });
        };

        return me;
    })();

    var PlayOptions = cx.PlayOptions = (function () {
        var me = {};
        var bfs = [];

        (function () {
            var MAX_BF = 5;
            var j = 0;

            for (var i = 0; i <= MAX_BF; ++i) {
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

        me.getBfCheckMap = function (handicap) {
            var map = {};
            var bqc = [];
            var bf;
            for (var i = 0; i < bfs.length; ++i) {
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
                if (bf.name === '93') {
                    rqspf = '3';
                } else if (bf.name === '91') {
                    rqspf = '1';
                } else if (bf.name === '90') {
                    rqspf = '0';
                } else {
                    if (home + handicap > away) {
                        rqspf = '3';
                    } else if (home + handicap == away) {
                        rqspf = '1';
                    } else {
                        rqspf = '0';
                    }
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

        me.getAllBf = function () {
            return bfs;
        };

        me.validateOptByBf = function (map, playOptions, bf) {
            var filter = map[bf];
            var validOptions = {};
            $.each(playOptions, function (playType, playValue) {
                $.each(playValue, function (option, optionValue) {
                    if ($.inArray(option, filter[playType]) > -1) {
                        if (!(playType in validOptions)) {
                            validOptions[playType] = {};
                        }
                        validOptions[playType][option] = optionValue;
                    }
                });
            });

            return validOptions;
        };

        return me;
    })();

    var GGTypes = cx.GGTypes = (function () {
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
        var types = [];

        me.addType = function (type) {
            types.push(type);
            types.sort();
        };

        me.removeType = function (type) {
            var i = $.inArray(type, types);
            if (i > -1) types.splice(i, 1);
        };

        me.getOptions = function () {
            var options = [];
            var type;
            for (var i = 0; i < types.length; ++i) {
                type = types[i];
                options = options.concat(map[type]);
            }
            return options;
        };

        me.hasType = function (type) {
            return $.inArray(type, types) > -1;
        };

        me.getTypes = function () {
            return types;
        };

        me.refreshGGType = function (count) {
            var matches = MatchCollection.getArray();
            for (var i = 0, matchLen = matches.length; i < matchLen; ++i) {
                if (matches[i].playOptionsCount.jqs > 0) count = Math.min(count, 6);
                if (matches[i].playOptionsCount.bqc > 0 || matches[i].playOptionsCount.cbf > 0) count = Math.min(count, 4);
            }
            if (count < 1) {
                types = [];
            } else {
                for (var j = 0, typeLen = types.length; j < typeLen; ++j) {
                    if (types[j].split('*')[0] > count) me.removeType(types[j]);
                }
            }

            SelectedMatches.show();
        };

        return me;
    })();

    var Combine = cx.Combine = function (matches) {
        this.matches = matches;
        this.len = matches.length;
        this.max = 1;
        this.min = 1;
        this.combineCount = 1;
        this.allOld= 1;
        for (var i = 0; i < matches.length; ++i) {
            if(matches[i].options.cancel == 1){
                var sum = 0;
                $.each(matches[i].multiOptions, function (index, value) {
                    if (!$.isEmptyObject(value)) {
                        $.each(value, function (i, v) {
                            sum += v;
                        });
                    }
                });
                matches[i].max= sum;
                matches[i].min= sum;
            }
            this.max *= matches[i].max;
            this.min *= matches[i].min;
            this.allOld = this.allOld * matches[i].options.old;
            this.combineCount *= matches[i].optionCount;
        }
    };

    var CombineCollection = (function () {
        var me = {};
        var combines = {};
        this.allOdd=1;
        this.oldcombines = {};        

        me.reset = function () {
            combines = {};
            this.oldcombines = {};
            this.allOdd=1;
        };

        me.addByLen = function (lenCombines, len, cb) {
            if (!(len in combines)) combines[len] = {};
            lenCombines.sort(function (aCombine, bCombine) {
                return aCombine.max - bCombine.max;
            });
            var maxSort = lenCombines;
            var minSort = [].concat(lenCombines);
            minSort.sort(function (aCombine, bCombine) {
                return aCombine.min - bCombine.min;
            });
            combines[len].max = maxSort;
            combines[len].min = minSort;
            var combineCount = 0;
            $(lenCombines).each(function (key, lenCombine) {
                combineCount += lenCombine.combineCount;
                me.caloldmax(lenCombine,len);
            });
            if ($.isFunction(cb)) cb(combineCount);
        };

//计算已出比分最大奖金
            me.caloldmax = function(lenCombine,len){
                this.allOdd=this.allOdd * lenCombine.allOld;
                if (lenCombine.allOld==1) {
                    if (!(len in this.oldcombines)) {
                        this.oldcombines[len] = 0;
                    }
                    this.oldcombines[len]=lenCombine.max+this.oldcombines[len];
                }
            };
            

        me.getBySlice = function (len, type, start, end) {
            var combinesByLenByType = combines[len][type];
            end || (end = combinesByLenByType.length);
            return combinesByLenByType.slice(start, end);
        };

        return me;
    })();

    var CastData = cx.CastData = (function () {
        var me = {}, betNum = 0, betMoney = 0, maxTime = '1970-01-01 00:00:00', minTime = '3000-12-31 23:59:59', 
        buyMoney = 0, commission = 0, guarantee = 0, openStatus = 0, optimization = {};
        

        me.getBetMoney = function () {
            return betMoney;
        };

        me.setBetMoney = function (val) {
            betMoney = val;
        };

        me.renderMinMaxMoney = function () {
            var matchesCount = MatchCollection.count();
            var len = GGTypes.getOptions()[0] || 0;
            var $minMoney = $('.min-money');
            var $maxMoney = $('.max-money');
            if (matchesCount < 1 || GGTypes.getOptions().length < 1) {
                $minMoney.empty().html('0.00');
                $maxMoney.empty().html('0.00');
                return;
            }
            var maxOdd = 0;
            var minOdd = 0;
            if (matchesCount in optimization) {
                $(optimization[matchesCount].max).each(function (key, combine) {
                    maxOdd += combine.max;
                });
                $(optimization[len].min).each(function (key, combine) {
                    minOdd += combine.min;
                });
                if(CombineCollection.allOdd==1){
                    $(".fcs").addClass("hidden");
                    $(".oldfcs").removeClass("hidden");
                    $(".old-max-money").html(round(maxOdd * ONE_MONEY));
                }else
                {
                    if(CombineCollection.oldcombines[len]){
                        minOdd=CombineCollection.oldcombines[len];
                    }
                    $(".fcs").removeClass("hidden");
                    $(".oldfcs").addClass("hidden");
                    $minMoney.empty().html(round(minOdd * ONE_MONEY));
                    $maxMoney.empty().html(round(maxOdd * ONE_MONEY));
                }
            }
        };
        
        me.setTime = function() {
        	var timeTmp;
        	maxTime = '1970-01-01 00:00:00'; 
        	minTime = '3000-12-31 23:59:59';
        	$.each(MatchCollection.getArray(), function(k, e) {
        		if (e.options.jzdt < minTime) minTime = e.options.jzdt;
        		if (e.options.jzdt > maxTime) maxTime = e.options.jzdt;
        	})
        }

        me.calcOptimization = function () {
            var combOptions = GGTypes.getOptions();
            var mi;
            var ci;
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

        me.renderBet = function () {
            $('.bet-num').empty().html(betNum);
            $('.bet-money').empty().html(betMoney);
        };

        me.calcCombines = function () {
            CombineCollection.reset();
            betNum = 0;
            var allCombines = cx.Math.combineList(MatchCollection.getArray(), GGTypes.getOptions(), function (matches) {
                return new Combine(matches);
            });
            $.each(allCombines, function (combOption, combines) {
                CombineCollection.addByLen(combines, combOption, function (combineCount) {
                    betNum += combineCount;
                });
            });
            betMoney = betNum * ONE_MONEY;
        };

        me.calcBetMoney = function () {
            var matches = MatchCollection.getArray();
            var match;
            var sum = 0;
            for (var i = 0, matchLen = matches.length; i < matchLen; ++i) {
                match = matches[i];
                $.each(match.multiOptions, function (index, value) {
                    if (!$.isEmptyObject(value)) {
                        $.each(value, function (i, v) {
                            sum += v;
                        })
                    }
                });
            }

            betMoney = sum * ONE_MONEY;
        };

        me.refresh = function () {
            if (MatchCollection.count() && !GGTypes.getOptions().length) {
                GGTypes.addType('1*1');
            }
            me.calcCombines();
            optimization = me.calcOptimization();
            me.calcBetMoney();
            me.renderBet();
            me.renderMinMaxMoney();
            me.setTime();
        };

        $('.bet-bar').on('click', '.submit', function(e){
            var $this = $(this),
                data,
                self;
            
            $this.addClass('needTigger');
            
            if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
                me.notLogin($this, e);
                return;
            }

            if ($this.hasClass('not-bind')) {
                return;
            }

            var agreement = $("#hasRead");
            if (agreement.get(0) && !agreement.is(":checked")) {
                return void new cx.Alert({content: "请阅读并同意《用户委托投注协议》"});
            }

            if (MatchCollection.count() < 1) {
                return void new cx.Alert({
                    content: '请至少选择一场比赛'
                });
            }

            data = me.getCastOptions();
            if (data.money > MAX_BET_MONEY) {
                return void new cx.Alert({
                    content: ["<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>",
                        cx.Money.format(MAX_BET_MONEY), "</span>元，请修改订单后重新投注"].join('')
                });
            }

            data.isToken = 1;
            self = this;
            cx.castCb(data, {ctype:'create', lotteryId:42, orderType:0, betMoney:data.money, typeCnName:typeCnName});
        }).on('click', '#optimize', function(){
            var data = me.getCastOptions2();

            if (data.money > MAX_BET_MONEY) {
                return void new cx.Alert({
                    content: ["<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>",
                        cx.Money.format(MAX_BET_MONEY), "</span>元，请修改订单后重新投注"].join('')
                });
            }

            if (data.betTnum > MAX_OPT_BET) {
                return void new cx.Alert({
                    content: ["<i class='icon-font'>&#xe611;</i>奖金优化最多支持", MAX_OPT_BET, "注"].join('')
                });
            }

            if ((new Date(minTime)) <= (new Date())) {
                return void new cx.Alert({
                    content: "<i class='icon-font'>&#xe611;</i>有场次已过投注截止时间",
                    confirmCb: function(){
                        location.reload();
                    }
                });
            }

            $('#optLotteryId').val(data.lid);
            $('#optBetNum').val(data.betTnum);
            $('#optMidStr').val(data.codecc);
            $('#optBetStr').val(data.codes);
            $('#optEndTime').val(data.endTime);
            $('#optIssue').val(data.issue);
            $('#optBetMoney').val(data.money);
            $('#optMulti').val(data.multi);
            $('#optopenEndtime').val(data.openEndtime);
            $('#optimizeForm').submit();
        });
        
        $('.bet-bar').on('click', ".btn-hemai:not([class*='btn-disabled'])", function(e){
        	
        	$(this).addClass('needTigger');
            if ($(this).hasClass('not-login') || !$.cookie('name_ie')) {
                me.notLogin($(this), e);
                return;
            }

            if ($(this).hasClass('not-bind')) return;
            var $agreement = $('.pop-pay').find(".ipt_checkbox#hmagreenment");
            if ($agreement.get(0) && !$agreement.is(":checked")) return void new cx.Alert({content: "请阅读并同意《用户委托投注协议》"});
            if (MatchCollection.count() < 1) return void new cx.Alert({content: '请至少选择一场比赛'});
            if (MatchCollection.count() == 1 && !($.inArray('1*1', GGTypes.getTypes()) > -1)) return void new cx.Alert({content: '请至少选择两场比赛'});
            if (GGTypes.getTypes().length < 1) return void new cx.Alert({content: '请至少选择一种过关方式'});
            if (betMoney > MAX_BET_MONEY) 
                return void new cx.Alert({content: ["<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>", cx.Money.format(MAX_BET_MONEY), "</span>元，请修改订单后重新投注"].join('')});
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
		            hemaiPop();
		            $('.pop-pay').find('.bet_info').hide();
		        }
		    });
		})
		
		hemaiPop = function() {
        	var rgpctmin = 5;
        	var showhmTime = new Date(Date.parse(minTime.replace(/-/g, "/"))  - hmahead * 60000);
        	$('.pop-pay').find('.jzdt').html(showhmTime.getFullYear()+"-"+(padd(showhmTime.getMonth() + 1))+"-"+padd(showhmTime.getDate())+" "+padd(showhmTime.getHours())+":"+ padd(showhmTime.getMinutes())+":"+padd(showhmTime.getSeconds()));
        	$('.betNum').html(betNum);
        	$('.Multi').html(1);
        	$('.betMoney').html(betMoney);
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
                if (!$.cookie('name_ie')) {//登录过期
                	cx.PopCom.hide($('.pop-pay'));
                    me.notLogin($(this), e);
                    return;
                }
                if ($(this).hasClass('not-bind')) {cx.PopCom.hide($('.pop-pay'));cx.PopAjax.bind();return;}
                var data = me.getCastOptions2();
                data.isToken = 1;
                var self = this;

                cx.castCb(data, {ctype:'create', lotteryId:42, orderType:4, betMoney:data.money, buyMoney:data.buyMoney, guarantee:data.guaranteeAmount, typeCnName:typeHmName});

            });
        	function renderBuyMoney() {
            	$('.buyMoney').val(buyMoney).parents('.form-item-con').find('span em:first').html(rgpctmin)
            			.parents('.form-item-con').find('u').show().find('em').html(Math.floor(buyMoney * 100/betMoney));
    			$('.guarantee').parents('.form-item-con').find('span em:first').html(betMoney - buyMoney);
    			$('.buy_txt').html("<em class='main-color-s'>"+(buyMoney+guarantee)+"</em> 元 <span>（认购"+buyMoney+"元+保底"+guarantee+"元）</span></span>");
            }
        	function renderGuarantee() {
            	$('.guarantee').val(guarantee).parents('.form-item-con').find('span em:last').html(Math.floor(guarantee * 100 / betMoney));
            	$('.buy_txt').html("<em class='main-color-s'>"+(buyMoney+guarantee)+"</em> 元 <span>（认购"+buyMoney+"元+保底"+guarantee+"元）</span></span>");
            }
        }

        function getCastStr() {
            var matches = MatchCollection.getArray(),
                ggTypes = ['1*1'],
                castStr = 'HH|',
                matchStr = '',
                match,
                playOptions = [],
                matchStrAry = [],
                tmpOption,
                midAry = [];
            for (var i = 0; i < matches.length; ++i) {
                match = matches[i];
                midAry.push(match.options.mid);
                $.each(match.playOptions, function (playType, playOption) {
                    if ($.isEmptyObject(playOption)) {
                        return true;
                    }
                    playOptions = [];
                    matchStr = playType.toUpperCase() + '>' + match.options.mid + '=';
                    $.each(playOption, function (option) {
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
                        if (playType == 'rqspf') tmp += '{' + (match.handicap > 0 ? ('+' + match.handicap) : match.handicap) + '}';
                        tmp += '(' + match.originalPlayOptions[playType][option] + ')';
                        tmp += '@' + match.multiOptions[playType][option];
                        playOptions.push(tmp);
                    });
                    matchStr += playOptions.join('/');
                    matchStrAry.push(matchStr);
                });
            }
            var singleIndex = $.inArray('1*1', ggTypes);
            if (singleIndex > -1) {
                if (ggTypes.length == 1) {
                    castStr += matchStrAry.join(',') + '|1*1';
                } else {
                    ggTypes.splice(singleIndex, 1);
                    castStr += matchStrAry.join(',') + '|' + ggTypes.join(',') + ';HH|' + matchStrAry.join(',') + '|1*1';
                }
            } else {
                castStr += matchStrAry.join(',') + '|' + ggTypes.join(',');
            }
            castStr += '^' + midAry.join(' ');

            return castStr;
        }

        me.getCastOptions = function () {
            var castStr = getCastStr().split('^');

            return {
                ctype: 'create',
                buyPlatform: 0,
                codes: castStr[0],
                lid: 42,
                money: betMoney,
                multi: 1,
                issue: cx.Datetime.getToday(),
                playType: playTypeMap[type],
                betTnum: betNum,
                isChase: 0,
                orderType: 0,
                endTime: minTime,
                codecc: castStr[1],
                forecastBonus: $('.min-money').html()+"|"+$('.max-money').html()
            };
        };
        
        me.getCastOptions2 = function () {
            var castStr = getCastStr().split('^'), openEndtime = new Date(Date.parse(maxTime.replace(/-/g, "/")) + ahead * 60000), endTime = new Date(Date.parse(minTime.replace(/-/g, "/")) - hmahead * 60000);
            openEndtime = openEndtime.getFullYear()+"-"+(padd(openEndtime.getMonth() + 1))+"-"+padd(openEndtime.getDate())+" "+padd(openEndtime.getHours())+":"+ padd(openEndtime.getMinutes())+":"+padd(openEndtime.getSeconds());
            endTime = endTime.getFullYear()+"-"+(padd(endTime.getMonth() + 1))+"-"+padd(endTime.getDate())+" "+padd(endTime.getHours())+":"+ padd(endTime.getMinutes())+":"+padd(endTime.getSeconds());
            return {
                ctype: 'create',
                buyPlatform: 0,
                codes: castStr[0],
                lid: 42,
                money: betMoney,
                multi: 1,
                issue: cx.Datetime.getToday(),
                playType: playTypeMap[type],
                betTnum: betNum,
                isChase: 0,
                orderType: 4,
                codecc: castStr[1],
                endTime: endTime,
                buyMoney: buyMoney,
                commissionRate: commission,
                guaranteeAmount: guarantee,
                openStatus: openStatus,
                openEndtime: openEndtime,
                ForecastBonusv: $('.min-money').html()+"|"+$('.max-money').html()
            };
        };

        me.notLogin = function ($this, e) {
            if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
                cx.PopAjax.login();
                e.stopImmediatePropagation();
            }
            e.preventDefault();
        };

        return me;
    })();

    $('.bet-side').on('click', '.plus', function () {
        var $this = $(this);
        var list = $this.closest('.bet-list-item');
        var index = list.data('index');
        var playType = list.data('type');
        var option = list.data('val');

        var matches = MatchCollection.getArray();
        var multi = matches[index].multiOptions[playType][option];
        multi += 1;
        multi = Math.min(multi, MAX_OPTION_MULTI);
        $this.prev().children('.number').val(multi);
        matches[index].multiOptions[playType][option] = multi;
        matches[index].changeMulti(playType, option, multi);

        $this.parent().next().find('.each-bet').empty().html(multi * ONE_MONEY);
        $this.parent().next().find('.each-reward').empty().html(
            round(ONE_MONEY * matches[index].playOptions[playType][option]));

        CastData.setBetMoney(CastData.getBetMoney() + ONE_MONEY);
        CastData.refresh();
    }).on('click', '.minus', function () {
        var $this = $(this);
        var list = $this.closest('.bet-list-item');
        var index = list.data('index');
        var playType = list.data('type');
        var option = list.data('val');

        var matches = MatchCollection.getArray();
        var multi = matches[index].multiOptions[playType][option];
        multi -= 1;
        multi = Math.max(MIN_OPTION_MULTI, multi);
        $this.next().children('.number').val(multi);
        matches[index].multiOptions[playType][option] = multi;
        matches[index].changeMulti(playType, option, multi);

        $this.parent().next().find('.each-bet').empty().html(multi * ONE_MONEY);
        $this.parent().next().find('.each-reward').empty().html(
            round(ONE_MONEY * matches[index].playOptions[playType][option]));

        CastData.setBetMoney(CastData.getBetMoney() - ONE_MONEY);
        CastData.refresh();
    }).on('blur', '.number', function () {
        var $this = $(this);
        var list = $this.closest('.bet-list-item');
        var index = list.data('index');
        var playType = list.data('type');
        var option = list.data('val');

        var matches = MatchCollection.getArray();
        var multi = matches[index].multiOptions[playType][option];

        var betMoney = CastData.getBetMoney();
        betMoney -= multi * ONE_MONEY;
        var val = $.trim($this.val());
        multi = parseInt(val, 10);
        if (!multi) {
            multi = 1;
        } else if (!(/^\d+$/.test(val))) {
            multi = MIN_OPTION_MULTI;
        }
        multi = Math.min(MAX_OPTION_MULTI, multi);
        $this.val(multi);
        matches[index].multiOptions[playType][option] = multi;
        matches[index].changeMulti(playType, option, multi);

        $this.closest('.multi-modifier').next().find('.each-bet').empty().html(multi * ONE_MONEY);
        $this.closest('.multi-modifier').next().find('.each-reward').empty()
            .html(round(ONE_MONEY * matches[index].playOptions[playType][option]));

        betMoney += multi * ONE_MONEY;
        CastData.setBetMoney(betMoney);
        CastData.refresh();
    }).on('keyup', '.number', function (e) {
        var $this = $(this),
            list = $this.closest('.bet-list-item'),
            index = list.data('index'),
            playType = list.data('type'),
            option = list.data('val'),
            matches = MatchCollection.getArray(),
            multi = matches[index].multiOptions[playType][option],
            betMoney = CastData.getBetMoney(),
            val;

        betMoney -= multi * ONE_MONEY;
        val = $.trim($this.val());
        multi = parseInt(val, 10);
        if ((e.which >= 48 && e.which <= 57) || e.which == 8 || (e.which >= 96 && e.which <= 105)) {
            if (val == '') {
                multi = 0;
            } else {
                if (!multi) {
                    multi = 1;
                }
                multi = Math.min(MAX_OPTION_MULTI, multi);
                $this.val(multi);
            }
        } else {
            if (!(/^\d+$/.test(val))) {
                multi = MIN_OPTION_MULTI;
                $this.val(multi);
            }
        }

        matches[index].multiOptions[playType][option] = multi;
        matches[index].changeMulti(playType, option, multi);

        $this.closest('.multi-modifier').next().find('.each-bet').empty().html(multi * ONE_MONEY);
        $this.closest('.multi-modifier').next().find('.each-reward').empty()
            .html(round(ONE_MONEY * matches[index].playOptions[playType][option]));

        betMoney += multi * ONE_MONEY;
        CastData.setBetMoney(betMoney);
        CastData.refresh();
    });

    $('.not-login').click(function (e) {
        CastData.notLogin($(this), e);
    });

    var selectedClass = 'selected';
    var openedClass = 'opened';
    $.each(orderedPlayType, function (index, key) {
        var option = '.' + key + '-option';
        $('.jc-box').on('click', option, function () {
            var $this = $(this),
                $matchLn = $this.closest('tr');
            if (!$matchLn.hasClass('match')) {
                $matchLn = $matchLn.prev();
            }

            var mid = $matchLn.data('mid');
            var val = $this.data('val');
            var odd = $this.data('odd');
            if (!GGTypes.hasType('1*1')) {
                GGTypes.addType('1*1');
            }

            var matchCount = MatchCollection.count();
            if ($this.hasClass(selectedClass)) {
                removeMatch($this, $matchLn, mid, key, val);
                $matchLn.data('select',$matchLn.data('select')-1);
            } else {
                $this.addClass(selectedClass);
                $matchLn.data('select',$matchLn.data('select')+1);
                var match = cx.MatchCollection.get(mid);
                if (match === null) {
                    match = new cx.Match({
                        mid: mid,
                        handicap: $matchLn.data('let'),
                        val: val,
                        odd: odd,
                        wid: $matchLn.data('wid'),
                        home: $matchLn.data('home'),
                        away: $matchLn.data('away'),
                        spfFu: $matchLn.data('spf_fu'),
                        rqspfFu: $matchLn.data('rqspf_fu'),
                        jqsFu: $matchLn.data('jqs_fu'),
                        cbfFu: $matchLn.data('cbf_fu'),
                        bqcFu: $matchLn.data('bqc_fu'),
                        jzdt: $matchLn.data('jzdt'),
                        cancel: $matchLn.data('cancel'),
                        old: $matchLn.data('old')
                    });
                    cx.MatchCollection.add(mid, match);
                }
                match.addOption(key, val, odd);
                match.addDom(key, val, $this);
                if (matchCount > MAX_MATCH_COUNT) {
                    new cx.Alert({
                        content: ['只支持', MAX_MATCH_COUNT, '场以内的投注！'].join('')
                    });
                    removeMatch($this, $matchLn, mid, key, val);
                }
            }
            var oldselected = 0;
            $('.jc-option a.selected').each(function(){
                    if($(this).parents('tr').hasClass('hasmatched')){
                        oldselected = 1;
                    }
            })
            $('.attach-options a.selected').each(function(){
                    if($(this).parents('tr').hasClass('hasmatched')){
                        oldselected = 1;
                    }
            })
            if(oldselected==1)
            {
                $("#optimize").addClass("hidden");
                $(".btn-s").addClass('hidden');
                $(".agree").addClass('hidden');
                $(".jjyc-imgs").removeClass('hidden');
            }
            else
            {
                $("#optimize").removeClass("hidden");
                $(".btn-s").removeClass('hidden');
                $(".agree").removeClass('hidden');                
                $(".jjyc-imgs").addClass('hidden');
                $(".fcs").removeClass("hidden");
                $(".oldfcs").addClass("hidden");
            }
            GGTypes.refreshGGType(matchCount);
            CastData.refresh();
        });
    });

    function removeMatch($el, $match, mid, type, val) {
        $el.removeClass(selectedClass);
        var match = cx.MatchCollection.get(mid);
        match.removeOption(type, val);
        var attachTypes = ['cbf', 'jqs', 'bqc'];
        if ($.inArray(type, attachTypes) > -1) {
            var playCount = 0;
            if (match != null) {
                $.each(attachTypes, function () {
                    playCount += match.playOptionsCount[this];
                });
            }
            if (playCount == 0) {
                $match.find('.open-attach').removeClass(selectedClass).empty()
                    .html('<div class="jc-table-action"><a href="javascript:;">更多<i class="arrow"></i></a></div>');
            } else {
                $match.find('.open-attach').empty()
                    .html(['<div class="jc-table-action selected"><a href="javascript:;" class="selected">已选', playCount, '项<i class="arrow"></i></a></div>'].join(''));
            }
        }
        match.removeDom(type, val);
    }

    $('.table-option-list').on('click', '.list-item', function () {
        var $select = $('#selectTime');
        var field = $(this).data('field');
        $('.time').children('.time-num').each(function () {
            var $this = $(this);
            var dtAry = $this.closest('.match').data(field).split(' ')[1].split(':');
            $select.empty().html(function () {
                return [(field == 'dt') ? '比赛' : '停售', '时间<i class="table-option-arrow"></i>'].join('');
            });
            $this.html([dtAry[0], dtAry[1]].join(':'));
        });
    });

    $('.jc-box').on('click', '.jc-table-action', function () {
        var $this = $(this);
        var $tr = $this.closest('.match');
        var $nextLn = $tr.next();
        if ($nextLn.html() == '') {
            $nextLn.empty().load('ajax/attachOption', {playType: type, mid: $tr.data('mid')}, function () {
                $this.closest('.match').nextUntil('.match').filter('.attach-options').addClass(openedClass).show().find('td').show();
            });
        } else {
            $this.closest('.match').nextUntil('.match').filter('.attach-options').addClass(openedClass).show().find('td').show();
        }
    });
    $('.jc-box').on('click', '.jc-table-action', function () {
        var $this = $(this);
        var $match = $this.closest('.attach-options').prev();
        var playCount = 0;
        var match = MatchCollection.get($match.data('mid'));
        if (match != null) {
            $.each(['cbf', 'jqs', 'bqc'], function (index, option) {
                playCount += match.playOptionsCount[option];
            });
        }

        $match.find('.jc-table-action').empty().html(function () {
            return (playCount > 0) ? ['<a href="javascript:;" class="selected">已选', playCount, '项<i class="arrow"></i></a>'].join('')
                : '<a href="javascript:;">更多<i class="arrow"></i></a>';
        }).parent().addClass(function () {
            return (playCount > 0) ? selectedClass : '';
        });
        $this.parents('.attach-options').removeClass(openedClass).find('td').hide();
    });
    $('body').on('click', '.jc-box-action', function () {
        var $this = $(this);
        $this.html(function () {
            return $this.closest('.jc-box').hasClass('jc-box-hide') ? '隐藏' : '显示';
        }).closest('.jc-box').toggleClass('jc-box-hide');
    });

    var SelectedMatches = (function () {
        var me = {};

        me.show = function () {
            var matches = MatchCollection.getArray(),
                matchLen = matches.length;
            $('.btn-betting').toggleClass('btn-disabled', !saleStatus);
            $('#no-matches').toggle(!matchLen);
            $('#optimize').toggle(matchLen > 0);

            var $selected = $('.bet-data');
            $selected.empty().html(function () {
                var tmpAry = [],
                    midAry = [];
                $.each(matches, function (i, match) {
                    var tplAry = ['<div data-mid="', match.options.mid, '">'];
                    tplAry.push('<div class="bet-data-hd"><a href="javascript:;" class="fr btn-delete" data-index="', i,
                        '">删除</a><span class="fr">', match.options.wid, '</span><b>', match.options.home, '</b> VS <b>',
                        match.options.away, '</b></div>', '<div class="bet-data-bd"><ul>');
                    $.each(match.originalPlayOptions, function (playType, playValue) {
                        $.each($.map(playValue, function (val, key) {
                            return key;
                        }).sort(function (a, b) {
                            return playOptionOrder[playType][a] - playOptionOrder[playType][b];
                        }), function (index, option) {
                            var originOdd = playValue[option];
                            var yj= "预计";
                            if(match.old==1){
                               yj= ""; 
                            } 
                            tplAry.push('<li class="bet-list-item" data-index="', i, '" data-type="', playType,
                                '" data-val="', option, '">', '<p class="clearfix"><span class="bet-resulte">',
                                playOptionNames[playType][option], '</span>赔率', originOdd.toFixed(2), '</p>',
                                '<div class="multi-modifier">',
                                '<a href="javascript:;" class="minus selem">-</a><label><input class="multi number" type="text"',
                                'value=" ', match.multiOptions[playType][option], '"',
                                'autocomplete="off"></label><a href="javascript:;" class="plus selem" data-max="',
                                MAX_OPTION_MULTI, '">+</a>', '</div>', '<p><span class="mr20">投<b class="each-bet">',
                                match.multiOptions[playType][option] * ONE_MONEY,
                                '</b>元</span><span>'+yj+'奖金<b class="each-reward">',
                                round(ONE_MONEY * originOdd * match.multiOptions[playType][option]),
                                '</b>元</span></p>',
                                '<a href="javascript:;" class="btn-close"><i class="icon-font">&#xe603;</i></a>',
                                '</li>');
                        });
                    });

                    tplAry.push('</ul></div>', '</div>');
                    tmpAry[match.options.mid] = tplAry.join('');
                    midAry.push(match.options.mid);
                });
                midAry.sort();
                return $.map(midAry, function (mid) {
                    return tmpAry[mid];
                }).join('');
            }).find('.btn-close').click(function () {
                var list = $(this).parent();
                matches[list.data('index')].domOptions[list.data('type')][list.data('val')].trigger('click');
            }).end().find('.btn-delete').click(function () {
                $.each(matches[$(this).data('index')].domOptions, function (playKey, playType) {
                    $.each(playType, function (key, dom) {
                        dom.trigger('click');
                    });
                });
            });
            $('.btn-clean').click(function () {
                $selected.find('.btn-close').trigger('click');
            });
        };

        return me;
    })();

    var $leagueFilter = $('.league-filter');
    $leagueFilter.mouseover(function () {
        $(this).addClass('league-filter-hover');
    }).mouseout(function () {
        $(this).removeClass('league-filter-hover');
    });

    $('.table-option').mouseover(function () {
        $(this).addClass('table-option-hover');
    }).mouseout(function () {
        $(this).removeClass('table-option-hover');
    });

    $leagueFilter.on('click', '.league', function () {
        var $this = $(this);
        var val = $this.val();
        $('.jc-box').each(function (key, match) {
            var $match = $(match);
            var league = $match.data('league');
            var $matchAttach = $match.next();
            if (league == val) {
                $('.attach-options .jc-table-action').trigger('click');
                $match.toggle().find('td').toggle();
                if (!$this.is(':checked')) {
                    $matchAttach.hide().removeClass(openedClass).find('td').hide();
                }
            }
        });
    });

    $('.select-five').click(function () {
        $('.league').each(function () {
            var $this = $(this);
            if ($.inArray(parseInt($this.val(), 10), fiveLeague) == -1) {
                if ($this.is(':checked')) {
                    $this.trigger('click');
                }
            } else {
                if (!$this.is(':checked')) {
                    $this.trigger('click');
                }
            }
        });
    });
    $('.select-anti').click(function () {
        $('.attach-options .jc-table-action').trigger('click');
        $('.league').trigger('click');
        $('.match').next().hide().find('td').hide();
    });
    $('.select-all').click(function () {
        $('.league').each(function (key, league) {
            if (!$(league).is(':checked')) {
                $(league).trigger('click');
            }
        });
    });
    $('.select-none').click(function () {
        $('.league').each(function (key, league) {
            if ($(league).is(':checked')) {
                $(league).trigger('click');
            }
        });
    });

});

function padd(num) {
    return ('0' + num).slice(-2);
}