$(function () {
    var ONE_MONEY = 2,
        MAX_MATCH_COUNT = 15,
        MAX_BET_MONEY = 200000,
        MAX_OPT_BET = 1000,
        playTypeMap = {
            hh: 0,
            spf: 1,
            rqspf: 2,
            bqc: 3,
            jqs: 4,
            cbf: 5,
            optimize: 7
        },
        playOptionNames = {
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
        },
        orderedPlayType = ['spf', 'rqspf', 'bqc', 'cbf', 'jqs'],
        cssClass = {
            panelHide: 'panel-hide'
        },
        selectedClass = 'selected',
        openedClass = 'opened',
        openAttachStr = (type == 'cbf') ? '展开比分投注区' : '半全场/比分/总进球',
        singleCorner = '<div class="mod-sup"><i class="mod-sup-bg"></i><u>单</u></div>';
    
    function round2(f) {
    	if (f == 0) {
            return '0.00';
        }
        return cx.Math.round2(cx.Math.multiply(f, 100)) / 100;
    }

    var Match = cx.Match = function (options) {
        this.options = options;
        this.handicap = parseInt(this.options.handicap, 10);
        this.bfCheckMap = null;

        this.max = 0;
        this.min = Number.POSITIVE_INFINITY;
        this.maxOptions = {};
        this.minOptions = {};

        this.optionCount = 0;
        this.splitMatchs = {};

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

    Match.prototype.addOptions = function (playType, objects) {
        var $this = this;
        $.each(objects, function (index, object) {
            $this.playOptions[playType][object.val] = parseFloat(object.odd);
            $this.optionCount += 1;
            $this.playOptionsCount[playType] += 1;
        });
        $this.refreshOdds();
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

    Match.prototype.removeOptions = function (playType, options) {
        var $this = this;
        $.each(options, function (index, option) {
            delete $this.playOptions[playType][option];
        });
        $this.refreshOdds();
        $this.optionCount -= options.length;
        $this.playOptionsCount[playType] -= options.length;
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
                var spfOpt = {};
                spfOpt['spf'] = 0;
                spfOpt['rqspf'] = 0;
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
                            if ((playType.toUpperCase() == 'SPF' || playType.toUpperCase() == 'RQSPF') && opt == 1) {
                                spfOpt[playType] += optValue
                            } else {
                                sum += optValue;
                                minSum += optValue;
                            }
                        });
                    }
                });
                if (spfOpt['spf'] > spfOpt['rqspf']) {
                    sum += spfOpt['spf'];
                    if (spfOpt['rqspf'] > 0) {
                        minSum += spfOpt['rqspf'];
                    } else {
                        minSum += spfOpt['spf'];
                    }
                } else {
                    sum += spfOpt['rqspf'];
                    if (spfOpt['spf'] > 0) {
                        minSum += spfOpt['spf'];
                    } else {
                        minSum += spfOpt['rqspf'];
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
    }

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
                //将返回数组按照mid进行排序
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
        })(),
        PlayOptions = cx.PlayOptions = (function () {
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
                    if (bf.name === '91' && Math.abs(handicap) > 5) {
                        rqspf = '1';
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
        })(),
        GGTypes = cx.GGTypes = (function () {
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
                for (i = 0; i < length; ++i) {
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
                return [options, optionsmore];
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
                    type = $this.data('type'),
                    matches,
                    i,
                    length,
                    match;
                if ($this.hasClass('selected')) {
                    $this.removeClass('selected');
                    me.removeType(type);
                } else {
                    matches = MatchCollection.getArray();
                    if ($this.hasClass('single')) {
                        for (i = 0, length = matches.length; i < length; ++i) {
                            match = matches[i];
                            for (var j = 0; j < orderedPlayType.length; ++j) {
                                var playType = orderedPlayType[j];
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
        })(),
        Combine = cx.Combine = function (matches) {
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
        },
        CombineCollection = (function () {
            var me = {},
                combines = {};
            	multicombines = {};
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
                if ($.isFunction(cb)) {
                    var combineCount = 0;
                    $(lenCombines).each(function (key, lenCombine) {
                        combineCount += lenCombine.combineCount;
                        me.caloldmax(lenCombine,len);
                    });
                    cb(combineCount);
                }
            };
            
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
                var a =1;
                this.allOdd=this.allOdd * lenCombine.allOld;
                if (lenCombine.allOld==1) {
                    if (!(len in this.oldcombines)) {
                        this.oldcombines[len] = 0;
                    }
                    this.oldcombines[len]=lenCombine.max+this.oldcombines[len];
                }
                else
                {
                    $(lenCombine.matches).each(function (key, lmatch) {
                        if(lmatch.playOptionsCount.spf==3 || lmatch.playOptionsCount.rqspf==3 || lmatch.playOptionsCount.bqc==9 || lmatch.playOptionsCount.jqs==8 || lmatch.playOptionsCount.cbf==31)
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
        })(),
        CastData = cx.CastData = (function () {
            var me = {}, betNum = 0, betMoney = 0, maxTime = '1970-01-01 00:00:00', minTime = '3000-12-31 23:59:59', buyMoney = 0, commission = 0, guarantee = 0, openStatus = 0, optimization = {}, 
            multiModifier = new cx.AdderSubtractor('.multi-modifier'), $count = $('.count-matches'), $minMoney = $('.min-money'), $maxMoney = $('.max-money, .old-max-money');
            	
            multiModifier.setCb(function () {
            	betMoney = cx.Math.multiply(cx.Math.multiply(betNum, this.value), ONE_MONEY);
                me.renderBet();
                me.renderMinMaxMoney();
            });

            me.getBetMoney = function () {
                return betMoney;
            };

            me.setBetMoney = function (val) {
                betMoney = val;
            };

            me.renderMinMaxMoney = function () {
                var len = 0,
                    matchesCount,
                    ggCount,
                    $optimize = $('#optimize'),
                    multi,
                    maxOdd,
                    minOdd,
                    ggStringfy;
                ggCount = GGTypes.getOptions()[0].length;
                ggStringfy = JSON.stringify(GGTypes.getOptions()[1]);
                if (ggCount > 0) len = GGTypes.getOptions()[0][0];
                matchesCount = MatchCollection.count();
                if (matchesCount < 1 || (ggCount < 1 && ggStringfy === '{}') || (matchesCount == 1 && !GGTypes.hasType('1*1'))) {
                	$minMoney.html('0.00');
                    $maxMoney.html('0.00');
                    $optimize.addClass(cssClass.panelHide);
                    return;
                }
                $optimize.toggleClass(cssClass.panelHide, (ggCount < 1 && ggStringfy === '{}'));
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
                    
                    if(CombineCollection.allOdd==1)
                    {
                        $(".ycjj").addClass("hidden");
                        $(".fycjj").removeClass("hidden");
                    }
                    else
                    {
                        if(CombineCollection.oldcombines[len] && CombineCollection.oldcombines[len]!=0){
                            minOdd=CombineCollection.oldcombines[len];
                        }
                        $(".ycjj").removeClass("hidden");
                        $(".fycjj").addClass("hidden");
                    }
                }
                
                if (ggStringfy !== '{}') {
                	var minoption = Number.POSITIVE_INFINITY;;
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
                var combOptions0 = GGTypes.getOptions()[0], combOptions1 = GGTypes.getOptions()[1];
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
                            if (combOption > mi) continue;
                            tmpCount = cx.Math.combine(mi, combOption);
                            if (!(mi in odds)) odds[mi] = {};
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
                $('.bet-num').empty().html(betNum);
                $('.bet-money').empty().html(betMoney);
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
                $count.html(MatchCollection.count());
                optimization = me.calcOptimization();
                me.renderMinMaxMoney();
                me.setTime();
            };

            var $cast = $('.cast-panel');
            $cast.find('#optimize').on('click', 'a', function () {
                var data = CastData.getCastOptions2();

                if (data.money > MAX_BET_MONEY) 
                    return void new cx.Alert({content: ["<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>", cx.Money.format(MAX_BET_MONEY), "</span>元，请修改订单后重新投注"].join('')});

                if (data.betTnum > MAX_OPT_BET) return void new cx.Alert({content: ["<i class='icon-font'>&#xe611;</i>奖金优化最多支持", MAX_OPT_BET, "注"].join('')});

                var nowDate = new Date(), dateTimeAry = minTime.split(' '), dateAry = dateTimeAry[0].split('-'), year = dateAry[0], month = dateAry[1] - 1,
                    day = dateAry[2], timeAry = dateTimeAry[1].split(':'), hour = timeAry[0], minute = timeAry[1], second = timeAry[2], endDate = new Date(year, month, day, hour, minute, second);
                if (endDate <= nowDate) {
                    return void new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>有场次已过投注截止时间", confirmCb: function () {location.reload();}});
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
            $cast.find('.submit').click(function (e) {
                var $this = $(this),
                    $agreement;
                
                $this.addClass('needTigger');
                if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
                    me.notLogin($this, e);
                    return;
                }

                if ($this.hasClass('not-bind')) return;
                $agreement = $cast.find(".ipt_checkbox#agreenment");
                if ($agreement.get(0) && !$agreement.is(":checked")) return void new cx.Alert({content: "请阅读并同意《用户委托投注协议》"});
                if (MatchCollection.count() < 1) return void new cx.Alert({content: '请至少选择一场比赛'});
                if (MatchCollection.count() == 1 && !($.inArray('1*1', GGTypes.getTypes()) > -1)) return void new cx.Alert({content: '请至少选择两场比赛'});
                if (GGTypes.getTypes().length < 1) return void new cx.Alert({content: '请至少选择一种过关方式'});
                var data = me.getCastOptions1();
                if (data.money > MAX_BET_MONEY) 
                    return void new cx.Alert({content: ["<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>", cx.Money.format(MAX_BET_MONEY), "</span>元，请修改订单后重新投注"].join('')});
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
                var matches = MatchCollection.getArray(),
                    ggTypes = GGTypes.getTypes(),
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
                            tmp += '(' + match.playOptions[playType][option] + ')';
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
                castStr += '@' + midAry.join(' ');
                return castStr;
            }

            me.getCastOptions1 = function () {
                var castStr = getCastStr().split('@'), codecc = castStr[1]; castStr = castStr[0];
                return {
                    ctype: 'create',
                    buyPlatform: 0,
                    codes: castStr,
                    lid: 42,
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
                    lid: 42,
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
                $this.closest('tr').data('select',$this.closest('tr').data('select')-1);
                removeMatch($this, $matchLn, mid, key, val);
            } else {
                $matchLn.data('select',$matchLn.data('select')+1);
                $this.closest('tr').data('select',$this.closest('tr').data('select')+1);
                $this.addClass(selectedClass);
                var match = cx.MatchCollection.get(mid);
                if (match === null) {
                    match = new cx.Match({
                        mid: mid,
                        handicap: $matchLn.data('let'),
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
                match.splitMatch();
            }
            cx.MatchCollection.removemulti(mid);
            var count = MatchCollection.count(), single = true, opt, oldselected = 0;
    		$('.jc-option a.selected').each(function(){
        		opt = $(this).data('option') || key;
        		if (!$(this).parents('tr').data(opt + '_fu')) single = false;
                        if($(this).parents('tr').hasClass('hasmatched')){
                            oldselected = 1;
                        }
        	})
        	$('.attach-options a.selected').each(function(){
        		if ($(this).hasClass('bqc-option')) {
        			opt = 'bqc';
        		}else if($(this).hasClass('cbf-option')) {
        			opt = 'cbf';
        		}else {
        			opt = 'jqs';
        		}
        		if (!$(this).parents('tr').prev().data(opt + '_fu')) single = false;
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
        var match = MatchCollection.get(mid);
        match.removeOption(type, val);
        match.splitMatch();
        var attachTypes = ['cbf', 'jqs', 'bqc'];
        if ($.inArray(type, attachTypes) > -1) {
            var playCount = 0;
            if (match != null) {
                $.each(attachTypes, function () {
                    playCount += match.playOptionsCount[this];
                });
            }
            var isSingle = (match.options.cbfFu || match.options.jqsFu || match.options.bqcFu) ? singleCorner : '';
            if (playCount == 0) {
                $match.find('.open-attach').removeClass(selectedClass).empty()
                    .html(('<div class="jc-table-action"><a href="javascript:;">' + openAttachStr + '<i class="arrow"></i>' + isSingle + '</a></div>'));
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
        var $this = $(this),
            $tr = $this.closest('.match'),
            $nextLn = $tr.next();
        if ($nextLn.html() == '') {
            $nextLn.load('ajax/attachOption', {playType: type, mid: $tr.data('mid')}, function () {
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
            var isSingle = singleCorner;
            if($match.data('mid')){
                isSingle =($match.data('cbf_fu')|| $match.data('jqs_fu') || $match.data('bqc_fu')) ? singleCorner : '';
            }
            $match.find('.jc-table-action').empty().html(function () {
                return (playCount > 0) ? ('<a href="javascript:;" class="selected">已选' + playCount + '项<i class="arrow"></i>' + isSingle + '</a>')
                    : ('<a href="javascript:;">' + openAttachStr + '<i class="arrow"></i>' + isSingle + '</a>');
            }).parent().addClass(function () {
                return (playCount > 0) ? selectedClass : '';
            });
        } else {
            var isSingle = singleCorner;
            if($match.data('mid')){
                isSingle =($match.data('cbf_fu')|| $match.data('jqs_fu') || $match.data('bqc_fu')) ? singleCorner : '';
            }
            $match.find('.jc-table-action').empty().html('<a href="javascript:;">' + openAttachStr + '<i class="arrow"></i>' + isSingle + '</a>');
        }

        $this.parents('.attach-options').removeClass(openedClass).find('td').hide();
    });

    $('body').on('click', '.odds-refer', function () {
        var $this = $(this),
            opName = $this.html();
        $.post('ajax/queryReferOdds', {
            lid: 42,
            cid: $this.data('cid'),
            issue: issueStr
        }, function (data) {
            $this.closest('.table-option-list').find('.selected').removeClass(selectedClass)
                .end().end().addClass(selectedClass);
            $('.match').each(function () {
                var $this = $(this),
                    mid = $this.data('mid');
                if (!(mid in data)) {
                    data[mid] = {
                        oh: '0.00',
                        od: '0.00',
                        oa: '0.00'
                    };
                }
                $this.find('.pjop').children('.op-oh').text(parseFloat(data[mid]['oh']).toFixed(2)).end()
                    .children('.op-od').text(parseFloat(data[mid]['od']).toFixed(2)).end()
                    .children('.op-oa').text(parseFloat(data[mid]['oa']).toFixed(2)).end();
            });
            $('#op-name').html(opName + '<i class="table-option-arrow"></i>');
        }, 'json');
    }).on('click', '.jc-box-action', function () {
        var $this = $(this);
        $this.html(function () {
            return $this.closest('.jc-box').hasClass('jc-box-hide') ? '隐藏' : '显示';
        }).closest('.jc-box').toggleClass('jc-box-hide');
    }).on('click', '.pick-all', function () {
        var $line = $(this),
            values = [];
        $matchLn = $line.closest('.attach-options').prev();
        $line.toggleClass('pick-all clear-all').html('清空')
            .prev().children().each(function () {
            var $this = $(this),
                object;
                $matchLn.data('select',$matchLn.data('select')+1);
            if (!$this.hasClass(selectedClass)) {
                $this.addClass(selectedClass);
                object = {};
                object.val = $this.data('val');
                object.odd = $this.data('odd');
                object.dom = $this;
                values.push(object);
            }
        });

        var type = 'cbf',
            $matchLn = $line.closest('.attach-options').prev(),
            mid = $matchLn.data('mid'),
            match = MatchCollection.get(mid);
        if (match === null) {
            match = new cx.Match({
                mid: mid,
                handicap: $matchLn.data('let'),
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
        match.addOptions(type, values);
        $.each(values, function (index, object) {
            match.addDom(type, object.val, object.dom);
        });
        match.splitMatch();
        cx.MatchCollection.removemulti(mid);
        
        var count = MatchCollection.count(), single = true, opt, oldselected = 0;
		$('.jc-option a.selected').each(function(){
    		opt = $(this).data('option') || key;
    		if (!$(this).parents('tr').data(opt + '_fu')) single = false;
                    if($(this).parents('tr').hasClass('hasmatched')){
                        oldselected = 1;
                    }
    	})
    	$('.attach-options a.selected').each(function(){
    		if ($(this).hasClass('bqc-option')) {
    			opt = 'bqc';
    		}else if($(this).hasClass('cbf-option')) {
    			opt = 'cbf';
    		}else {
    			opt = 'jqs';
    		}
    		if (!$(this).parents('tr').prev().data(opt + '_fu')) single = false;
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
    }).on('click', '.clear-all', function () {
        var $line = $(this),
            values = [],
            type = 'cbf',
            match = MatchCollection.get($line.closest('.attach-options').prev().data('mid')),
            playCount = 0,
            isSingle = (match.options.cbfFu || match.options.jqsFu || match.options.bqcFu) ? singleCorner : '';
        $matchLn = $line.closest('.attach-options').prev();
        $line.toggleClass('clear-all pick-all').html('全包').prev().children().each(function () {
            $matchLn.data('select',$matchLn.data('select')-1);
            var $this = $(this);
            if ($this.hasClass(selectedClass)) {
                $this.removeClass(selectedClass);
                values.push($this.data('val'));
            }
        });

        $.each(['cbf', 'jqs', 'bqc'], function (index, option) {
            playCount += match.playOptionsCount[option];
        });
        match.removeOptions(type, values);
        $.each(values, function (val) {
            match.removeDom(type, val);
        });
        $.each(match.domOptions, function (playKey, playType) {
            $.each(playType, function (key, dom) {
                if (playCount) dom.closest('.attach-options').prev().children('.open-attach').removeClass(selectedClass).empty()
                        .html(('<div class="jc-table-action"><a href="javascript:;">' + openAttachStr + '<i class="arrow"></i>' + isSingle + '</a></div>'));
            });
        });

        GGTypes.refreshGGType(MatchCollection.count());
        SelectedMatches.show();
        CastData.refresh();
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
            match = matches[index],
            playCount = 0;
        $.each(['cbf', 'jqs', 'bqc'], function (index, option) {
            playCount += match.playOptionsCount[option];
        });
        var isSingle = (match.options.cbfFu || match.options.jqsFu || match.options.bqcFu)
            ? singleCorner
            : '';
        $.each(match.domOptions, function (playKey, playType) {
            $.each(playType, function (key, dom) {
                dom.removeClass(selectedClass).parent().next('.clear-all').toggleClass('clear-all pick-all').html('全包');
                if (playCount) {
                    dom.closest('.attach-options').prev()
                        .children('.open-attach').removeClass(selectedClass).empty()
                        .html(('<div class="jc-table-action"><a href="javascript:;">' + openAttachStr + '<i class="arrow"></i>' + isSingle + '</a></div>'));
                }
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
                    var playCount = 0;
                    $.each(['cbf', 'jqs', 'bqc'], function (index, option) {
                        playCount += match.playOptionsCount[option];
                    });
                    var isSingle = (match.options.cbfFu || match.options.jqsFu || match.options.bqcFu)
                        ? singleCorner
                        : '';
                    $.each(match.domOptions, function (playKey, playType) {
                        $.each(playType, function (key, dom) {
                            dom.removeClass(selectedClass);
                            if (playCount) {
                                dom.closest('.attach-options').prev()
                                    .children('.open-attach').removeClass(selectedClass).empty()
                                    .html(('<div class="jc-table-action"><a href="javascript:;">' + openAttachStr + '<i class="arrow"></i>' + isSingle + '</a></div>'));
                            }
                        });
                    });
                });
                $('.clear-all').toggleClass('clear-all pick-all').html('全包');

                MatchCollection.empty();
                GGTypes.refreshGGType(MatchCollection.count());
                SelectedMatches.show();
                CastData.refresh();
                $('.seleFiveTit').removeClass('seleFiveTit2').find('a').removeClass('bet-blue');
                $('.seleFiveBox').hide();
            }
        });
    });

    var SelectedMatches = (function () {
        var me = {};

        me.show = function () {
            var matches = MatchCollection.getArray(),
                matchLen = matches.length;
            $('#no-matches').toggleClass('panel-hide', matchLen > 0);
            $('.has-matches').toggleClass('panel-hide', !matchLen);
            var tpl = '<colgroup><col width="60"><col width="83"><col width="83"><col width="264"><col width="60"></colgroup>';
            $.each(matches, function (index, match) {
                tpl += renderMatch(index, match);
            });
            $('.selected-matches').html(tpl);
            $('.seleFiveTit').children().toggleClass('bet-blue', matchLen > 0);
        };
        
        function renderMatch(i, match) {
            var vs = '<td>' + match.options.wid + '</td><td class="tar"><span>' + match.options.home + '</span></td>' + '<td class="tal"><span>'
                    + match.options.away + '</span></td>',
                optionTpl = '<td class="tal"><span class="selected-option-group">';
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

    $(".lotteryPlayWrap").hover(function () {
        $(this).find('h3').addClass("hover").next("div.lotteryPlayBox").show();
    }, function () {
        $(".lotteryPlayWrap h3").removeClass().next("div.lotteryPlayBox").hide();
    });

    $(".seleFiveTit").click(function () {
    	if (MatchCollection.count() > 0) $(this).toggleClass("seleFiveTit2").next("div.seleFiveBox").toggle();
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