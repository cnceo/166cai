$(function () {

    var ONE_MONEY = 2,
        MAX_MATCH_COUNT = 15,
        MAX_BET_MONEY = 200000,
        MAX_OPT_BET = 1000,
        cssClass = {
            panelHide: 'panel-hide'
        };
    
    function round2(f) {
    	if (f == 0) {
            return '0.00';
        }
        return cx.Math.round2(cx.Math.multiply(f, 100)) / 100;
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
            3: '主胜',
            0: '客胜'
        },
        rfsf: {
            3: '主胜【让】',
            0: '客胜【让】'
        },
        sfc: {
            '01': '主胜1-5分',
            '02': '主胜6-10分',
            '03': '主胜11-15分',
            '04': '主胜16-20分',
            '05': '主胜21-25分',
            '06': '主胜26+分',
            '11': '客胜1-5分',
            '12': '客胜6-10分',
            '13': '客胜11-15分',
            '14': '客胜16-20分',
            '15': '客胜21-25分',
            '16': '客胜26+分'
        },
        dxf: {
            3: '大分',
            0: '小分'
        }
    };
    var orderedPlayType = ['sf', 'rfsf', 'dxf', 'sfc'];
    var Match = cx.Match = function (options) {
        this.options = options;
        this.handicap = parseFloat(this.options.handicap, 10);
        this.bfCheckMap = null;

        this.max = 0;
        this.min = Number.POSITIVE_INFINITY;
        this.maxOptions = {};
        this.minOptions = {};

        this.optionCount = 0;
        this.splitMatchs = {};

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

    Match.prototype.addDom = function (playType, option, $el) {
        this.domOptions[playType][option] = $el;
    };

    Match.prototype.addOption = function (playType, option, odd) {
        this.playOptions[playType][option] = parseFloat(odd);

        this.refreshOdds();
        this.optionCount += 1;
        this.playOptionsCount[playType] += 1;
    };

    Match.prototype.removeDom = function (playType, option) {
        delete this.domOptions[playType][option];
    };

    Match.prototype.removeOption = function (playType, option) {
        delete this.playOptions[playType][option];
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
                    if (playType.toUpperCase() == 'DXF') {
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
                        if (minBqcOpt < minSum || minSum == 0) {
                            minSum = minBqcOpt;
                        }
                    } else {
                        $.each(opts, function (opt, optValue) {
                            sum += optValue;
                            if (optValue < minSum || minSum == 0) {
                                minSum = optValue;
                            }
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
    
    Match.prototype.getsplitMatch = function() {
    	return this.splitMatchs;
    }
    
    Match.prototype.splitMatch = function() {
    	var self = this, result = [];
    	$.each(this.playOptionsCount, function(playType, count){
    		if (count > 0) {
    			var tmpMatch = new cx.Match({
                    mid: self.options.mid,
                    handicap: self.options.handicap,
                    wid: self.options.wid,
                    home: self.options.home,
                    away: self.options.away,
                    spfFu: self.options.spfFu,
                    rqspfFu: self.options.rqspfFu,
                    jqsFu: self.options.jqsFu,
                    cbfFu: self.options.cbfFu,
                    bqcFu: self.options.bqcFu,
                    jzdt: self.options.jzdt
                });
    			$.each(self.playOptions[playType], function(key, val){
    				tmpMatch.optionCount += 1;
    			})
    			if (playType in self.maxOptions) {
    				tmpMatch.maxOptions = self.maxOptions;
    				$.each(self.maxOptions[playType], function(opt, odd){
    					tmpMatch.max = odd;
    				})
    			}else {
    				tmpMatch.maxOptions = {};
    				tmpMatch.max = 0;
    			}
    			if (playType in self.minOptions) {
    				tmpMatch.minOptions = self.minOptions;
    				$.each(self.minOptions[playType], function(opt, odd){
    					tmpMatch.min = odd;
    				})
    			}else {
    				tmpMatch.minOptions = {};
    				tmpMatch.min = 0;
    			}
    			tmpMatch.playType = playType;
    			result.push(tmpMatch);
    		}
    	})
    	this.splitMatchs = result;
    };

    var MatchCollection = cx.MatchCollection = (function () {
    	var me = {}, matches = {}, multimatches = {};

        me.add = function (mid, match) {
            if (!(mid in matches)) matches[mid] = match;
        };

        me.remove = function (mid) {
            if (mid in matches) delete matches[mid];
        };
        
        me.removemulti = function(mid) {
        	$.each(multimatches, function(combinemid, multimatch){
            	if (combinemid.indexOf(mid) > -1) delete multimatches[combinemid];
            })
        }

        me.count = function () {
            var count = 0;
            $.each(matches, function (mid, match) {
                if (match.optionCount > 0) count += 1;
            });

            return count;
        };

        me.has = function (mid) {
            return (mid in matches);
        };

        me.get = function (mid) {
            if (mid in matches) return matches[mid];
            return null;
        };

        me.getAll = function () {
            return matches;
        };
        
        me.getAllMulti = function () {
            return multimatches;
        };

        me.getArray = function () {
            var newMatches = $.map(matches, function (match) {
                if (match.optionCount > 0) return match;
            });
            newMatches.sort(matchsCompare("mid"));
            return newMatches;
        };

        me.empty = function () {
            return (matches = {});
        };
        
        me.setsplitArray = function(combineArr) {
        	var i = 0, newmatches = {};
        	$.each(combineArr, function(key, mid){
            	var tmpmmatches = {}, tmpMatch = matches[mid].getsplitMatch();
            	$.each(tmpMatch, function (k, mtch){
            		if (i == 0) {
            			tmpmmatches[mid+mtch.playType] = [mtch];
            		}else {
            			$.each(newmatches, function(nk, val){
            				tmpmmatches[nk+"_"+mid+mtch.playType] = val.concat([mtch]);
                		})
            		}
            	})
            	newmatches = tmpmmatches;
            	i++;
            })
            multimatches[combineArr.join('_')] = newmatches;
        }
        
        me.getsplitArray = function(combineArr) {
        	var key = combineArr.join('_');
        	if (multimatches[key] === undefined) me.setsplitArray(combineArr);
            return multimatches[key];
        }

        return me;
    })();

    var PlayOptions = cx.PlayOptions = (function () {
        var me = {};
        var sfcs = [];

        (function () {
            var dirs = [1, -1];
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
                    max: 100000
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
                        min: cx.Math.multiply(allSfc[si].min, dirs[di]),
                        max: cx.Math.multiply(allSfc[si].max, dirs[di])
                    });
                }
            }
            
        })();

        me.getBfCheckMap = function (letScore) {
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
                map[sfc.name] = {
                    sf: [sfc.sf + ''],
                    rfsf: [rfsf + ''],
                    dxf: dxf,
                    sfc: [sfc.name]
                };
            }
            return map;
        };

        me.getAllBf = function () {
            return sfcs;
        };

        me.validateOptByBf = function (map, playOptions, sfc) {
            var filter = map[sfc];
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
    	var me = {}, $ggTypes = $('.gg-type:not(.gg-type-more)'), types = [], 
    	map = {
	    	'1*1': [1], 
	    	'2*1': [2], 
	    	'3*1': [3], '3*3': {3:[2]}, '3*4': {3:[2,3]}, 
	    	'4*1': [4], '4*4': {4:[3]}, '4*5': {4:[3,4]}, '4*6': {4:[2]}, '4*11': {4:[2,3,4]},
	    	'5*1': [5], '5*5': {5:[4]}, '5*6': {5:[4,5]}, '5*10': {5:[2]}, '5*16': {5:[3,4,5]}, '5*20': {5:[2,3]}, '5*26': {5:[2,3,4,5]},
	    	'6*1': [6], '6*6': {6:[5]}, '6*7': {6:[5,6]}, '6*15': {6:[2]}, '6*20': {6:[3]}, '6*22': {6:[4,5,6]}, '6*35': {6:[2,3]}, '6*42': {6:[3,4,5,6]}, '6*50': {6:[2,3,4]}, '6*57': {6:[2,3,4,5,6]},
	    	'7*1': [7], '7*7': {7:[6]}, '7*8': {7:[6,7]}, '7*21': {7:[5]}, '7*35': {7:[4]}, '7*120': {7:[2,3,4,5,6,7]}, 
	    	'8*1': [8], '8*8': {8:[7]}, '8*9': {8:[7,8]}, '8*28': {8:[6]}, '8*56': {8:[5]}, '8*70': {8:[4]}, '8*247': {8:[2,3,4,5,6,7,8]}
	    };

        me.addType = function (type) {
            types.push(type);
            types.sort();
        };

        me.removeType = function (type) {
            var i = $.inArray(type, types);
            if (i > -1) types.splice(i, 1);
        };

        me.getOptions = function () {
        	var options = [], optionsmore = {}, type, i, length = types.length;
            for (var i = 0; i < types.length; ++i) {
                type = types[i];
                if (parseInt(type.replace(/\d\*/, '')) > 1) {
                   $.each(map[type], function(num1, opts){
                	   if (!(num1 in optionsmore)) optionsmore[num1] = [];
                	   optionsmore[num1].push(opts);
                   })
                }else {
                   if (!(map[type] in options)) options = options.concat(map[type]);
                }
            }
            return  [options, optionsmore];
        };

        me.hasType = function (type) {
            return $.inArray(type, types) > -1;
        };

        me.getTypes = function () {
            return types;
        };

        me.refreshGGType = function (count) {
            var matches = MatchCollection.getArray();
            for (var i = 0; i < matches.length; ++i) {
                if (matches[i].playOptionsCount.sfc > 0) count = Math.min(count, 4);
            }
            $('.gg-type-more').hide();
            if (count >= 1) {
               $ggTypes.each(function(k, v){
	               var tmptypeArr = $(this).data('type').split('*');
	               if (tmptypeArr[0] > count) $(this).hide().removeClass('selected');
	               if (tmptypeArr[0] <= count) $(this).show();
	            })
	            if (count > 2) $('.gg-type-more').show();
               
               	var num = $('.more-type .more-type-pop').find('.selected').length;
				if (num == 0) {
					$('.more-type').find('.gg-type-more').removeClass('selected active').html('更多过关 ');
					$('.more-type-pop').hide();
				}else {
					$('.more-type').find('.gg-type-more').html('更多过关 ' + num);
				}
            }else {
            	$ggTypes.hide().removeClass('selected');
            }
            if (count < 1) {
                types = [];
            } else {
            	for (var j = 0, typeLen = types.length; j < typeLen; ++j) {
                    if (types[j] && types[j].split('*')[0] > count) {
                        me.removeType(types[j]);
                        j--;
                        typeLen--;
                    }
                }
            }
            var isDg = true;
            $.each(matches, function (index, match) {
                $.each(match.playOptions, function (playType) {
                    if (match.playOptionsCount[playType] && !match.options[playType + 'Fu']) {
                        isDg = false;
                        return false;
                    }
                });
            });
            
            if (matches.length && !types.length)  $ggTypes.eq(1).trigger('click');
        };

        $ggTypes.click(function () {      
            var $this = $(this),
                type = $this.data('type');
            if ($this.hasClass('selected')) {
                $this.removeClass('selected');
                me.removeType(type);
            } else {
                var matches = MatchCollection.getArray();
                if ($this.hasClass('single')) {
                    var playTypes = ['sf', 'rfsf', 'dxf', 'sfc'];
                    for (var i = 0; i < matches.length; ++i) {
                        var match = matches[i];
                        for (var j = 0; j < playTypes.length; ++j) {
                            var playType = playTypes[j];
                            if (!match.options[playType + 'Fu']) {
                                $.each(match.domOptions[playType], function (playOption, dom) {
                                    dom.trigger('click');
                                });
                            }
                        }
                    }
                    $this.siblings().removeClass('selected');
                    types = [];

                    $this.show();
                } else {
                    $this.siblings('.single').removeClass('selected');
                    me.removeType('1*1');
                }

                matches = MatchCollection.getArray();
                if (matches.length > 0) {
                	var multiflag = ($this.data('type').replace(/\d\*/, '') > 1);
                	$ggTypes.filter('.selected').each(function(k, v){
                       if (multiflag || $(this).data('type').replace(/\d\*/, '') > 1) {
                           $(this).removeClass('selected');
                           me.removeType($(this).data('type'));
                       }
                	})
       				if (multiflag) {
       					$("#optimize").html('<span class="seleView bubble-tip" tiptext="您好，奖金优化只支持N串1的过关方式">奖金优化</span>');
       				}else {
       					$("#optimize").html('<a class="seleView start-detail main-color-s">奖金优化</a>');
       				}
                    $this.addClass('selected');
                    me.addType(type);
                }
            }
            var num = $('.more-type .more-type-pop').find('.selected').length;
			if (num == 0) {
				$('.more-type').find('.gg-type-more').removeClass('selected').html('更多过关 ');
			}else {
				$('.more-type').find('.gg-type-more').html('更多过关 ' + num);
			}
            CastData.refresh();
        });

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
                matches[i].max=matches[i].optionCount;
                matches[i].min=matches[i].optionCount;
            }
            this.max = cx.Math.multiply(this.max, matches[i].max);
            this.min = cx.Math.multiply(this.min, matches[i].min);
            if(this.min==0)
            {
                this.min=this.max;
            }
            this.allOld = this.allOld * matches[i].options.old;
            this.combineCount = cx.Math.multiply(this.combineCount, matches[i].optionCount);
        }
        
    };

    var CombineCollection = (function () {
        var me = {}, combines = {}, multicombines = {};
        this.allOdd=1;
        this.oldcombines = {};

        me.reset = function () {
            combines = {};
            multicombines = {};
            this.oldcombines = {};
            this.allOdd=1;
        };

        me.addByLen = function (lenCombines, len, cb) {
            if (!(len in combines)) {
                combines[len] = {};
            }
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
            if ($.isFunction(cb)) {
                cb(combineCount);
            }
        }
        
        me.addMultiByLen = function(lenCombines, len, cb) {
			if (!(len in multicombines)) multicombines[len] = {};
			if (!('max' in multicombines[len])) multicombines[len].max = {};
			if (!('min' in multicombines[len])) multicombines[len].min = {};
		                    
		    $.each(lenCombines, function(k, combine){
				if (!(combine.splitid in multicombines[len].max) || !(combine.splitid in multicombines[len].min)) {
					multicombines[len].max[combine.splitid] = combine;
					multicombines[len].min[combine.splitid] = combine;
				}else {
					multicombines[len].max[combine.splitid].splitcount++;
				}
		    })
		
		    if ($.isFunction(cb)) {
		        var combineCount = 0;
		        $(lenCombines).each(function (key, lenCombine) {
		            combineCount += lenCombine.combineCount;
		        });
		        cb(combineCount);
		    }
		}

        //计算已出比分最大奖金
        me.caloldmax = function(lenCombine,len){
            this.allOdd=this.allOdd * lenCombine.allOld;
            var a = 1;
            if (lenCombine.allOld==1) {
                if (!(len in this.oldcombines)) {
                    this.oldcombines[len] = 0;
                }
                this.oldcombines[len]=lenCombine.max+this.oldcombines[len];
            }
            else
            {
                $(lenCombine.matches).each(function (key, lmatch) {
                    if(lmatch.playOptionsCount.sf==2 || lmatch.playOptionsCount.dxf==2 || lmatch.playOptionsCount.rfsf==2 || lmatch.playOptionsCount.sfc==12)
                    {
                        a = a*lmatch.min;
                    }
                });
            }
            if(a > 1)
            {
                this.oldcombines[len]=lenCombine.min+this.oldcombines[len];
            }
        };

        me.getBySlice = function (len, type, start, end) {
            var combinesByLenByType = combines[len][type];
            end || (end = combinesByLenByType.length);
            return combinesByLenByType.slice(start, end);
        };
        
        me.getByMultiSlice = function (len, type) {
        	return multicombines[len][type];
        }

        return me;
    })();

    var CastData = cx.CastData = (function () {
        var me = {}, betNum = 0, betMoney = 0, maxTime = '1970-01-01 00:00:00', minTime = '3000-12-31 23:59:59', buyMoney = 0, commission = 0, guarantee = 0, openStatus = 0
        optimization = {}, multiModifier = new cx.AdderSubtractor('.multi-modifier');
        multiModifier.setCb(function () {
            betMoney = cx.Math.multiply(cx.Math.multiply(betNum, this.value), ONE_MONEY);
            me.renderBet();
            me.renderMinMaxMoney();
        });

        var $count = $('.count-matches');
        var $minMoney = $('.min-money');
        var $maxMoney = $('.max-money, .old-max-money');
        var $betNum = $('.bet-num');
        var $betMoney = $('.bet-money');

        me.getBetMoney = function () {
            return betMoney;
        };

        me.renderMinMaxMoney = function () {
            var len = 0,
                matchesCount,
                ggCount,
                $optimize = $('#optimize'),
                multi,
                maxOdd,
                minOdd,
                ggStringfy1;
            ggCount = GGTypes.getOptions()[0].length;
            ggStringfy1 = JSON.stringify(GGTypes.getOptions()[1]);
            if (ggCount > 0) len = GGTypes.getOptions()[0][0];
            matchesCount = MatchCollection.count();
            
            if (matchesCount < 1 || (ggCount < 1 && ggStringfy1 === '{}') || (matchesCount == 1 && !GGTypes.hasType('1*1'))) {
                $minMoney.html('0.00');
                $maxMoney.html('0.00');
                $optimize.addClass(cssClass.panelHide);
                return;
            }
            $optimize.toggleClass(cssClass.panelHide, (ggCount < 1 && ggStringfy1 === '{}'));
            multi = multiModifier.getValue();
            maxOdd = 0;
            minOdd = 0;
            multiminOdd = Number.POSITIVE_INFINITY;
            if (matchesCount in optimization[0]) {
                $(optimization[0][matchesCount].max).each(function (key, combine) {
                    maxOdd += combine.max;
                });
                $(optimization[0][len].min).each(function (key, combine) {
                    minOdd += combine.min;
                });
                if(CombineCollection.allOdd!=1){
                    if(CombineCollection.oldcombines[len] && CombineCollection.oldcombines[len]!=0){
                        minOdd=CombineCollection.oldcombines[len];
                    }
                    $(".ycjj").removeClass("hidden");
                    $(".fycjj").addClass("hidden");
                }
                else
                {
                    $(".ycjj").addClass("hidden");
                    $(".fycjj").removeClass("hidden");
                }
            }
            
            if (ggStringfy1 !== '{}') {
            	var minoption = Number.POSITIVE_INFINITY;
            	$.each(GGTypes.getOptions()[1], function(combineNum, optionArr){
            		$.each(optionArr, function(key, options){
            			$.each(options, function(k, option){
            				minoption = Math.min(option, minoption);
            			})
            		})
            	})
            	$.each(optimization[1], function(combineNum, optimizat) {
            		if ('max' in optimizat) {
                       	$.each(optimizat.max, function(k, combine){
                       		maxOdd += combine.max * combine.splitcount;
                       		var mintmp = combine.min * combine.splitcount;
                       		if (minoption == combineNum && mintmp > 0 && mintmp < multiminOdd) multiminOdd = mintmp;
                       	})
            		}
                })
            }
            if (multiminOdd == Number.POSITIVE_INFINITY) multiminOdd = 0;
            $minMoney.html(cx.Math.multiply(round2(cx.Math.multiply(minOdd+multiminOdd, ONE_MONEY)), multi));
            $maxMoney.html(cx.Math.multiply(round2(cx.Math.multiply(maxOdd, ONE_MONEY)), multi));
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
        	var combOptions0 = GGTypes.getOptions()[0], combOptions1 = GGTypes.getOptions()[1];;
            var mi;
            var ci;
            var combOption;
            var matchesCount = MatchCollection.count();
            var multimatches = MatchCollection.getAllMulti();
            var tmpCount;
            var odds = {}, multiodds = {};
            if (combOptions0.length) {
            	for (mi = matchesCount; mi >= 1; --mi) {
                    for (ci = 0; ci < combOptions0.length; ++ci) {
                        combOption = combOptions0[ci];
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
            }
            
            if (JSON.stringify(combOptions1) !== '{}') {
            	$.each(combOptions1, function(num, options){
                	$.each(options, function(k, option){
                		$.each(option, function(k, opt){
                			if (!('opt' in multiodds)) {
                				multiodds[opt] = {};
                				multiodds[opt].max = CombineCollection.getByMultiSlice(opt, 'max');
                				multiodds[opt].min = CombineCollection.getByMultiSlice(opt, 'min');
                			}
                		})
                	})
                })
            }
            
            return [odds, multiodds];
        };

        me.renderBet = function () {
            $betNum.html(betNum);
            $betMoney.html(betMoney);
        };

        me.calcCombines = function () {
            CombineCollection.reset();
            betNum = 0;
            if (GGTypes.getOptions()[0].length) {
            	var allCombines = cx.Math.combineList(MatchCollection.getArray(), GGTypes.getOptions()[0], function (matches) {
                    return new Combine(matches);
                });
                $.each(allCombines, function (combOption, combines) {
                    CombineCollection.addByLen(combines, combOption, function (combineCount) {
                        betNum += combineCount;
                    });
                });
            }
            
            if (JSON.stringify(GGTypes.getOptions()[1]) !== '{}') {
            	var combineArr = [], combinemid = '', splitmatches = {}, multiCombines = {};
                $.each(GGTypes.getOptions()[1], function(combineNum, cOptionList){
                	cx.Math.combineList(MatchCollection.getArray(), [combineNum], function (matches) {
                		combineArr = [];
                		$.each(matches, function(k, match){
    			        	combineArr.push(match.options.mid);
                		})
                		combinemid = combineArr.join('_');
                		multiCombines[combinemid] = {};
                		splitmatches = MatchCollection.getsplitArray(combineArr);
                		$.each(splitmatches, function(key, splitmatch){
    			        	if (!(key in multiCombines[combinemid])) multiCombines[combinemid][key] = {};
    			        	$.each(cOptionList, function(k, cOption){
    			            	var tmp = cx.Math.combineList(splitmatch, cOption, function (matchs) {
    				            	newcombine =  new Combine(matchs);
    				            	newcombine.setsplitid();
    				            	return newcombine;
    				            });
    				            $.each(tmp, function(tk, tv){
    				            	if (!(tk in multiCombines[combinemid][key])) multiCombines[combinemid][key][tk] = [];
    				            	multiCombines[combinemid][key][tk] = multiCombines[combinemid][key][tk].concat(tv);
    				            })
    			        	})
                		})
                    })
                })
                    
                $.each(multiCombines, function(combinemid, combinesArr){
                	$.each(combinesArr, function(splitid, combinecollections){
                		$.each(combinecollections, function(n, combines){
    		            	CombineCollection.addMultiByLen(combines, n, function (combineCount) {
    		            		betNum += combineCount;
    		            	});
                		})
                	})
                })
            }
            
            betMoney = cx.Math.multiply(cx.Math.multiply(betNum, multiModifier.getValue()), ONE_MONEY);
            me.renderBet();
        };

        me.refresh = function () {
            me.calcCombines();
            var matchesCount = MatchCollection.count();
            $count.html(matchesCount);
            optimization = me.calcOptimization();
            me.renderMinMaxMoney();
            me.setTime();
        };

        var $cast = $('.cast-panel');
        $cast.find('.submit').click(function (e) {
            var $this = $(this);
            
            $this.addClass('needTigger');
            
            if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
                me.notLogin($this, e);
                return;
            }

            var $agreement = $cast.find(".ipt_checkbox#agreenment");
            if ($agreement.get(0) && !$agreement.is(":checked")) {
                new cx.Alert({content: "请阅读并同意《用户委托投注协议》"});
                return;
            }

            if ($this.hasClass('not-bind')) {
                return;
            }
            if (MatchCollection.count() < 1) {
                new cx.Alert({
                    content: '请至少选择一场比赛'
                });
                return;
            }
            if (MatchCollection.count() == 1) {
                if (!($.inArray('1*1', GGTypes.getTypes()) > -1)) {
                    new cx.Alert({
                        content: '请至少选择两场比赛'
                    });
                    return;
                }
            }
            if (GGTypes.getTypes().length <= 0) {
                new cx.Alert({
                    content: '请至少选择一种过关方式'
                });
                return;
            }
            var data = me.getCastOptions1();
            if (data.money > MAX_BET_MONEY) {
                new cx.Alert({
                    content: ["<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>",
                        cx.Money.format(MAX_BET_MONEY), "</span>元，请修改订单后重新投注"].join('')
                });
                return;
            }
            data.isToken = 1;
            var self = this;

            cx.castCb(data, {ctype:'create', lotteryId:42, orderType:0, betMoney:data.money, typeCnName:typeCnName});
        });
        
        $cast.find(".btn-hemai:not([class*='btn-disabled'])").click(function(e){
        	
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
		            hemaiPop()
		        }
		    });
		})
		
		hemaiPop = function() {
        	var rgpctmin = 5;
        	var showhmTime = new Date(Date.parse(minTime.replace(/-/g, "/"))  - hmahead * 60000);
        	$('.jzdt').html(showhmTime.getFullYear()+"-"+(padd(showhmTime.getMonth() + 1))+"-"+padd(showhmTime.getDate())+" "+padd(showhmTime.getHours())+":"+ padd(showhmTime.getMinutes())+":"+padd(showhmTime.getSeconds()));
        	$('.betNum').html(betNum);
        	$('.Multi').html(multiModifier.getValue());
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
            var mids = [];
            for (var i = 0; i < matches.length; ++i) {
                match = matches[i];
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
                            tmp += '{' + (match.handicap > 0 ? ('+' + match.handicap) : match.handicap) + '}';
                        }
                        tmp += '(' + match.playOptions[playType][option] + ')';
                        playOptions.push(tmp);
                    }
                    matchStr += playOptions.join('/');
                    if (playType == 'dxf') {
                        matchStr += '{' + match.options.prescore + '}';
                    }
                    matchStrs.push(matchStr);
                }
            }
            castStr += matchStrs.join(',') + '|' + ggTypes + '@' + mids.join(' ');
            return castStr;
        }

        me.getCastOptions1 = function () {
            var castStr = getCastStr().split('@');
            var codecc = castStr[1];
            castStr = castStr[0];
            return {
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
                endTime: minTime,
                codecc: codecc,
                forecastBonus: $('.ycjj p em:first').html()+"|"+$('.ycjj p em:last').html()
            };
        };
        
        me.getCastOptions2 = function () {
            var castStr = getCastStr().split('@'), codecc = castStr[1], openEndtime = new Date(Date.parse(maxTime.replace(/-/g, "/")) + ahead * 60000), endTime = new Date(Date.parse(minTime.replace(/-/g, "/")) - hmahead * 60000);
            castStr = castStr[0];
            openEndtime = openEndtime.getFullYear()+"-"+(padd(openEndtime.getMonth() + 1))+"-"+padd(openEndtime.getDate())+" "+padd(openEndtime.getHours())+":"+ padd(openEndtime.getMinutes())+":"+padd(openEndtime.getSeconds());
            endTime = endTime.getFullYear()+"-"+(padd(endTime.getMonth() + 1))+"-"+padd(endTime.getDate())+" "+padd(endTime.getHours())+":"+ padd(endTime.getMinutes())+":"+padd(endTime.getSeconds());
            return {
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
                orderType: 4,
                codecc: codecc,
                endTime: endTime,
                buyMoney: buyMoney,
                commissionRate: commission,
                guaranteeAmount: guarantee,
                openStatus: openStatus,
                openEndtime: openEndtime,
                ForecastBonusv: $('.ycjj p em:first').html()+"|"+$('.ycjj p em:last').html()
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
    
    cx.Combine.prototype.setsplitid = function() {
    	var splitidArr = [];
    	$.each(this.matches, function(k, match){
    		splitidArr.push(match.options.mid+match.playType);
    	})
    	this.splitid = splitidArr.join('_');
    	this.splitcount = 1;
    }

    var selectedClass = 'selected';
    var openedClass = 'opened';
    $.each(orderedPlayType, function (index, key) {
        var option = '.' + key + '-option';
        $('.jc-box').on('click', option, function () {        
            var $this = $(this);
            var $matchLn = $this.closest('tr');
            if (!$matchLn.hasClass('match')) {
                $matchLn = $matchLn.prev();
            }

            var mid = $matchLn.data('mid');
            var val = $this.data('val');
            var odd = $this.data('odd');

            if ($this.hasClass(selectedClass)) {
                $matchLn.data('select',$matchLn.data('select')-1);
                removeMatch($this, $matchLn, mid, key, val);
            } else {
                $matchLn.data('select',$matchLn.data('select')+1);
                $this.addClass(selectedClass);
                var match = cx.MatchCollection.get(mid);
                if (match === null) {
                    match = new cx.Match({
                        mid: mid,
                        home: $matchLn.data('home'),
                        away: $matchLn.data('away'),
                        handicap: $matchLn.data('let'),
                        wid: $matchLn.data('wid'),
                        sfFu: $matchLn.data('sf_fu'),
                        rfsfFu: $matchLn.data('rfsf_fu'),
                        sfcFu: $matchLn.data('sfc_fu'),
                        dxfFu: $matchLn.data('dxf_fu'),
                        jzdt: $matchLn.data('jzdt'),
                        prescore: $matchLn.data('prescore'),
                        cancel: $matchLn.data('cancel'),
                        old: $matchLn.data('old')
                    });
                    cx.MatchCollection.add(mid, match);
                }
                match.addOption(key, val, odd);
                match.addDom(key, val, $this);
                match.splitMatch();
            }
            cx.MatchCollection.removemulti(mid);
            var count = MatchCollection.count(), single = true, opt, oldselected = 0;
    		$('.jc-option a.selected').each(function(){
    			opt = $(this).data('option') || key;
        		if (!$(this).parents('tr').data(opt + '_fu')) {
                                single = false;
            	        }
                        if($(this).parents('tr').hasClass('hasmatched')){
                            oldselected = 1;
                        }
        	})
        	$('.attach-options a.selected').each(function(){
        		if (!$(this).parents('tr').prev().data('sfc_fu')) {
        			single = false;
            	        }
                        if($(this).parents('tr').hasClass('hasmatched')){
                            oldselected = 1;
                        }
        	})
        	
        	if (count == 1 && (single ^ $('.single').hasClass('selected'))) $(".single").trigger('click');
            if (count == 2){
            	$(".gg-type[data-type='2*1']").trigger('click');
            	if ($(".single").hasClass('selected')) $(".single").trigger('click');
            }else if (count > MAX_MATCH_COUNT) {
                new cx.Alert({content: ['只支持', MAX_MATCH_COUNT, '场以内的投注！'].join('')});
                removeMatch($this, $matchLn, mid, key, val);
            }
                if(oldselected==1)
                {
                    $("#optimize").addClass("hidden");
                    $(".btn").addClass('hidden');
                    $(".jjyc-img").removeClass('hidden');
                }
                else
                {
                    $("#optimize").removeClass("hidden");
                    $(".btn").removeClass('hidden');
                    $(".jjyc-img").addClass('hidden');
                }
            GGTypes.refreshGGType(MatchCollection.count());
            SelectedMatches.show();
            CastData.refresh();
        });
    });

    function removeMatch($el, $match, mid, type, val) {
        $el.removeClass(selectedClass);
        var match = cx.MatchCollection.get(mid);
        match.removeOption(type, val);
        match.splitMatch();
        var attachTypes = ['sfc'];
        if ($.inArray(type, attachTypes) > -1) {
            var playCount = 0;
            if (match != null) {
                $.each(attachTypes, function () {
                    playCount += match.playOptionsCount[this];
                });
            }
            var isSingle = $match.data('sfc_fu') ? '<div class="mod-sup"><i class="mod-sup-bg"></i><u>单</u></div>' : '';
            if (playCount == 0) {
                $match.find('.open-attach').removeClass(selectedClass).empty()
                    .html(('<div class="jc-table-action"><a href="javascript:;">胜分差<i class="arrow"></i>' + isSingle + '</a></div>'));
            } else {
                $match.find('.open-attach').empty()
                    .html(('<div class="jc-table-action selected"><a href="javascript:;" class="selected">已选' + playCount
                    + '项<i class="arrow"></i>' + isSingle + '</a></div>'));
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

    $('.jc-box').on('click', '.jc-table-action a', function () {
        var $this = $(this);
        var $tr = $this.closest('.match');
        var $nextLn = $tr.next();
        if ($nextLn.html() == '') {
            $nextLn.empty().load('ajax/attachOptionJL', {mid: $tr.data('mid')}, function () {
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
            $.each(['sfc'], function (index, option) {
                playCount += match.playOptionsCount[option];
            });
        }

        var isSingle = $match.data('sfc_fu') ? '<div class="mod-sup"><i class="mod-sup-bg"></i><u>单</u></div>' : '';
        $match.find('.jc-table-action').empty().html(function () {
            return (playCount > 0) ? ('<a href="javascript:;" class="selected">已选' + playCount + '项<i class="arrow"></i>' + isSingle + '</a>')
                : ('<a href="javascript:;">胜分差<i class="arrow"></i>' + isSingle + '</a>');
        }).parent().addClass(function () {
            return (playCount > 0) ? selectedClass : '';
        });
        $this.parents('.attach-options').removeClass(openedClass).find('td').hide();
    });

    var SelectedMatches = (function () {
        var me = {};

        me.show = function () {
            var matches = MatchCollection.getArray(),
                matchLen = matches.length;
            $('#no-matches').toggleClass(cssClass.panelHide, matchLen > 0);
            $('.has-matches').toggleClass(cssClass.panelHide, !matchLen);
            var tpl = '<colgroup><col width="60"><col width="83"><col width="83"><col width="264"><col width="60"></colgroup>';
            $.each(matches, function (index, match) {
                tpl += renderMatch(index, match);
            });
            $('.selected-matches').html(tpl);
            $('.seleFiveTit').children().toggleClass('bet-blue', matchLen > 0);
        };

        function renderMatch(i, match) {
            var vs = '<td>' + match.options.wid + '</td><td class="tar"><span>' + match.options.away + '</span></td>' + '<td class="tal"><span>'
                + match.options.home + '</span></td>';
            var optionTpl = '<td class="tal"><span class="selected-option-group">';
            $.each(match.playOptions, function (playType, playOption) {
                $.each(playOption, function (option, optionValue) {
                    optionTpl += '<a class="selected-option" href="javascript:;" data-index="' + i + '" data-type="'
                        + playType + '" data-val="' + option + '">' + playOptionNames[playType][option]
                        + '(' + optionValue + ')' + '</a>';
                });
            });
            optionTpl += '</span></td>';

            return '<tr data-mid="' + match.options.mid + '">' + vs + optionTpl + '<td><a class="del-match" href="javascript:;" data-index="' + i + '">&times;</a></td></tr>';
        }

        return me;
    })();

    $('.table-option').mouseover(function () {
        $(this).addClass('table-option-hover');
    }).mouseout(function () {
        $(this).removeClass('table-option-hover');
    });

    $('body').on('click', '.jc-box-action', function () {
        var $this = $(this);
        $this.html(function () {
            return $this.closest('.jc-box').hasClass('jc-box-hide') ? '隐藏' : '显示';
        }).closest('.jc-box').toggleClass('jc-box-hide');
    }).on('click', '.selected-option', function () {
        var $this = $(this),
            index = $this.data('index'),
            type = $this.data('type'),
            val = $this.data('val'),
            matches = MatchCollection.getArray(),
            match = matches[index],
            $el = match.domOptions[type][val];
        $el.trigger('click');
        if (MatchCollection.count() == 0) {
        	$('.seleFiveTit').removeClass('seleFiveTit2').find('a').removeClass('bet-blue');
            $('.seleFiveBox').hide();
        }
    }).on('click', '.del-match', function () {
        var matches = MatchCollection.getArray(),
            $this = $(this),
            index = $this.data('index'),
            match = matches[index];
        $.each(match.domOptions, function (playKey, playType) {
            $.each(playType, function (key, dom) {
                dom.removeClass(selectedClass).closest('.attach-options').prev()
                    .children('.open-attach').removeClass(selectedClass).empty()
                    .html(('<div class="jc-table-action"><a href="javascript:;">胜分差<i class="arrow"></i><div class="mod-sup"><i class="mod-sup-bg"></i><u>单</u></div></a></div>'));
            });
        });
        MatchCollection.remove(match.options.mid);
        var count = MatchCollection.count();
        GGTypes.refreshGGType(count);
        SelectedMatches.show();
        CastData.refresh();
        if (count == 0) {
        	$('.seleFiveTit').removeClass('seleFiveTit2').find('a').removeClass('bet-blue');
            $('.seleFiveBox').hide();
        }
    }).on('click', '.clear-matches', function () {
    	cx.Confirm({
    		single: '您确定要删除已选择的场次吗？',
            btns: [{type: 'confirm',txt: '确定',},{type: 'cancel',txt: '取消'}],
            confirmCb: function () {
            	$.each(MatchCollection.getArray(), function (index, match) {
                    $.each(match.domOptions, function (playKey, playType) {
                        $.each(playType, function (key, dom) {
                            dom.removeClass(selectedClass).closest('.attach-options').prev()
                                .children('.open-attach').removeClass(selectedClass).empty()
                                .html(('<div class="jc-table-action"><a href="javascript:;">胜分差<i class="arrow"></i><div class="mod-sup"><i class="mod-sup-bg"></i><u>单</u></div></a></div>'));
                        });
                    });
                });
                MatchCollection.empty();
                GGTypes.refreshGGType(MatchCollection.count());
                SelectedMatches.show();
                CastData.refresh();
                $('.seleFiveTit').removeClass('seleFiveTit2').find('a').removeClass('bet-blue');
                $('.seleFiveBox').hide();
            }
        });
            
        })
        .on('click', '#optimize a', function () {
            var data = CastData.getCastOptions2();
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

            var nowDate = new Date(),
                dateTimeAry = data.endTime.split(' '),
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
                return void new cx.Alert({
                    content: "<i class='icon-font'>&#xe611;</i>有场次已过投注截止时间",
                    confirmCb: function(){
                        location.reload();
                    }
                });
            } else {
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
            }
        });

    $('.lotteryTableTH-fixed-box').css({'height': $('.lotteryTableTH').outerHeight()});
    $(".lotteryPlayWrap").hover(function () {
        $(this).find('h3').addClass("hover").next("div.lotteryPlayBox").show();
    }, function () {
        $(".lotteryPlayWrap h3").removeClass().next("div.lotteryPlayBox").hide();
    });

    $(".seleFiveTit").click(function () {
    	if (MatchCollection.count() > 0) {
    		$(this).toggleClass("seleFiveTit2").next("div.seleFiveBox").toggle();
    	}
    });
});

function padd(num) {
    return ('0' + num).slice(-2);
}

//展示排序
function matchsCompare(propertyName) {
	return function(obj1, obj2) {
		var val1 = obj1.options[propertyName];
		var val2 = obj2.options[propertyName];
		if (val2 < val1) {
			return 1;
		} else if (val2 > val1) {
			return -1;
		} else {
			return 0;
		}
	}
};