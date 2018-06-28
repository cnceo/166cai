(function ($) {
    var playTypes = ['SF', 'RFSF', 'DXF', 'SFC'];
    var playNames = {
        SF: '胜负',
        RFSF: '让分胜负',
        DXF: '大小分',
        SFC: '胜分差'
    };
    var playOptions = {
        SPF: [3, 0],
        RQSPF: [3, 0],
        DXF: [3, 0],
        SFC: ['01', '02', '03', '04', '05', '06', '11', '12', '13', '14', '15', '16']
    };

    var playOptionNames = {
		SF: {
			3: '胜',
            0: '负'
        },
        RFSF: {
        	3: '让胜',
            0: '让负'
        },
        DXF: {
        	3: '大分',
            0: '小分'
        },
        SFC: {
            '01': '胜1-5',
            '02': '胜6-10',
            '03': '胜11-15',
            '04': '胜16-20',
            '05': '胜21-25',
            '06': '胜26+',
            '11': '负1-5',
            '12': '负6-10',
            '13': '负11-15',
            '14': '负16-20',
            '15': '负21-25',
            '16': '负26+'
        }
    };

    function parseTicket(tickets, ticketInfo) {
        if (!tickets) {
            return {};
        }
        tickets = tickets.split(';');
        var cast = {};
        for (var i = 0; i < tickets.length; ++i) {
            var ticket = tickets[i];
            var parts = ticket.split('|');
            var playType = parts[0];
            var codes = parts[1];
            var passWays = parts[2];
            $(codes.split(',')).each(function (k, v) {
                var playType, matchId, options;
                var detail = v.split('=');
                if (v.indexOf('>') == -1) {
                    playType = parts[0];
                    matchId = detail[0];
                } else {
                    playType = detail[0].split('>')[0];
                    matchId = detail[0].split('>')[1];
                }
                options = detail[1].split(',');
                if (!(matchId in cast)) {
                    cast[matchId] = {
                        play: 0,
                        let: 0,
                        preScore: 0
                    };
                }
                if (!(playType in cast[matchId])) {
                    cast[matchId][playType] = [];
                    cast[matchId].play += 1;
                }
                var pattern = /([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?(?:\{(.*)\})?/;
                $(options).each(function (ks, vs) {
                    $(vs.split('/')).each(function (k, v) {
                        var matches = v.match(pattern);
                        if (matches) {
                            // 实际赔率
                            var pl;
                            var plArr = [];
                            var pks;
                            var pksArr = [];
                            if(ticketInfo !== null && ticketInfo !== undefined)
                            {
                            	$.each(ticketInfo[matchId][playType], function(pk, tickinfo) {
                            		if(pk !== '-') pksArr.push(pk);
                            		$.each(tickinfo, function(fa, tinfo) {
                            			if(fa.toString().indexOf(matches[1]) > -1){
                                            $.each(tinfo, function(key, ti) {
                                            	if($.inArray(ti, pksArr) == -1) plArr.push(ti);
                                            })
                                        } 
                            		})
                            	})
                                pl = plArr.join('&');
                                pks = pksArr.join('&');
                            }else{
                                pl = '';
                                pks = '-';
                            }
                            var option = {
                                cast: matches[1],
                                odd: pl, //parseFloat(matches[3])
                            };
                            if (playType == 'RFSF') {
                                cast[matchId].let = pks; //matches[2];
                            }
                            if (playType == 'DXF') {
                                cast[matchId].preScore = pks; //matches[4];
                            }
                            if ($.inArray(option, cast[matchId][playType]) == -1) {
                                cast[matchId][playType].push(option);
                            }
                        }
                    });
                });
            });
        }

        return cast;
    }

    function parseCastPassway(ticket) {
        var parts = ticket.split('|');
        var passWays = parts[2];
        return passWays;
    }

    function renderResult(casts, matches, ticketInfo) {
        var tpl = '';
        var cast;
        var tmp = [];
        for (var matchId in casts) {
            cast = casts[matchId];
            cast.matchId = matchId;
            tmp.push(cast);
        }
        tmp.sort(function (a, b) {
        	a = parseInt(a.matchId, 10);
			b = parseInt(b.matchId, 10);
			return a > b ? 1 : ( a < b ? -1 : 0 );
        });
        var c;
        for (var i = 0; i < tmp.length; ++i) {
            c = tmp[i];
            tpl += renderMatch(c.matchId, c, matches[c.matchId], ticketInfo);
        }

        return tpl;
    }

    function renderMatch(matchId, cast, matchInfo, ticketInfo) {
        var ticketData = [];
        var tpl = '<tr>';
        var rowspan = cast.play;
        if (matchId.length < 11) {
            matchId = '20' + matchId;
        }
        tpl += '<td rowspan="' + rowspan + '">' + getMatchId(matchId).join(' ') + '</td>';
        tpl += '<td rowspan="' + rowspan + '">' + _formatDateTime(matchInfo.dt) + '</td>';
        tpl += '<td rowspan="' + rowspan + '">' + matchInfo.away + '</td>';
        tpl += '<td rowspan="' + rowspan + '">' + matchInfo.home + '</td>';
        var playCount = 0;
        $(playTypes).each(function (k, playType) {
            if (playType in cast) {
                if (playCount > 0) {
                    tpl += '<tr>';
                }
                tpl += '<td>' + _renderLet(playType, cast.let, cast.preScore) + '</td>';	// 盘口
                tpl += '<td>' + (matchInfo.mStatus == '1' ? '场次取消' : _renderResult(playType, matchInfo, cast)) + '</td>';	// 比分
                //tpl += '<td>' + _renderAward(playType, cast[playType], cast.let, matchInfo) + '</td>';	// 开奖结果
                // 获取实际开奖赔率信息
                if(ticketInfo !== null && ticketInfo !== undefined){
                    ticketData = ticketInfo[matchId][playType];
                }
                tpl += '<td>' + _renderOptions(playType, cast[playType], cast.let, matchInfo, cast.preScore, ticketData) + '</td>'; // 投注方案
                tpl += '</tr>';
                playCount += 1;
            }
        });

        return tpl;
    }

    function _renderAward(playType, options, let, matchInfo) {
        var tpl = '';
        var isRight = false;
        var castName = '';
        var casts = [];
        $(options).each(function (k, option) {
            if ($.inArray(option.cast, casts) > -1) {
                //return ;
            }

            if (matchInfo.score) {
                isRight = _compareResult(playType, matchInfo.score, let, option, matchInfo.halfScore);
            }
            castName = _getCastName(playType, option.cast);
            if (isRight) {
                tpl += '<p>' + castName + '</p>';
            }

            casts.push(option.cast);
        });
        return tpl;
    }

    function _renderResult(playType, matchInfo, cast) {
        var score = matchInfo.score || '-';
        var tpl = '';
        //cast[playType].forEach(function(option, key) {
        var isRight = false;
        var castName = '-';
        if (matchInfo.score) {
            //isRight = _compareResult(playType, matchInfo.score, cast.let, option, matchInfo.halfScore);
            castName = _getCastName(playType, _getCastOption(playType, matchInfo.score, matchInfo.halfScore, cast.let));
        }
        if (isRight) {
            tpl += '<p style="color: red;">' + score + '</p>';
        } else {
            tpl += '<p>' + score + '</p>';
        }
        //});
        return tpl;
    }

    function _renderVs(matchInfo) {
        if (matchInfo) {
            return '<p>' + matchInfo.away + '</p><p>VS</p><p>' + matchInfo.home + '</p>';
        } else {
            return '';
        }
    }

    function _renderLet(playType, let, preScore) {
        if (playType === 'RFSF') {
            if (let !== null && let !== undefined) {
                return let;
            }
        }
        if (playType === 'DXF') {
            if (preScore !== null && preScore !== undefined) {
                return preScore;
            }
        }
        return '-';
    }

    function _getCastOption(playType, score, halfScore, let, preScore) {
        var awayScore = parseInt(score.split(':')[0], 10);
        var homeScore = parseInt(score.split(':')[1], 10);
        var let = parseFloat(let, 10);
        var result = null;
        var gap = 0;

        if (playType == 'SF') {
            result = _parseScore(homeScore, awayScore, 0);
        } else if (playType == 'RFSF') {
            result = _parseScore(homeScore, awayScore, let);
        } else if (playType == 'DXF') {
            result = (homeScore + awayScore > preScore) ? '3' : '0';
        } else if (playType == 'SFC') {
            if (homeScore >= awayScore) {
                result = '0';
                gap = homeScore - awayScore;
            } else {
                result = '1';
                gap = awayScore - homeScore;
            }
            if (gap >= 1 && gap <= 5) {
                result += '1';
            } else if (gap >= 6 && gap <= 10) {
                result += '2';
            } else if (gap >= 11 && gap <= 15) {
                result += '3';
            } else if (gap >= 16 && gap <= 20) {
                result += '4';
            } else if (gap >= 21 && gap <= 25) {
                result += '5';
            } else if (gap >= 26) {
                result += '6';
            }
        }
        return result;
    }

    function _getCastName(playType, cast) {
        if (cast in playOptionNames[playType]) {
            return playOptionNames[playType][cast];
        } else {
            return cast;
        }
    }

    function _renderOptions(playType, options, let, matchInfo, preScore, ticketData) {
        var tpl = '';
        var castName = '';
        var casts = [];
        $(options).each(function (k, option) {
            if ($.inArray(option.cast, casts) > -1) {
                return;
            }
            if (matchInfo.score) {
                var isRight = false;
                var getRight = 0;
                // 命中的赔率
                var rightOdds = [];
                // 让分胜负let 大小分根据preScore
                if(playType == 'RFSF'){
                    var letArr = let.split('&');
                    for (var i = 0; i < letArr.length; i++) {
                        isRight = _compareResult(playType, matchInfo.score, letArr[i], option, matchInfo.halfScore, preScore);
                        if(isRight){
                            if(ticketData !== null && ticketData !== undefined && ticketData[letArr[i]][option.cast] !== undefined){
                                for (var j = 0; j < ticketData[letArr[i]][option.cast].length; j++) {
                                    rightOdds.push(ticketData[letArr[i]][option.cast][j]);        
                                };
                                getRight = getRight + 1;
                            }
                        }
                    }
                }else if(playType == 'DXF'){
                    var preScoreArr = preScore.split('&');
                    for (var i = 0; i < preScoreArr.length; i++) {
                        isRight = _compareResult(playType, matchInfo.score, let, option, matchInfo.halfScore, preScoreArr[i]);
                        if(isRight){
                            if(ticketData !== null && ticketData !== undefined && ticketData[preScoreArr[i]][option.cast] !== undefined){
                                for (var j = 0; j < ticketData[preScoreArr[i]][option.cast].length; j++) {
                                    rightOdds.push(ticketData[preScoreArr[i]][option.cast][j]);        
                                };
                                getRight = getRight + 1;
                            }
                        }
                    }
                }else{
                    isRight = _compareResult(playType, matchInfo.score, let, option, matchInfo.halfScore, preScore);
                    if(isRight){
                        getRight = 1;
                    }
                }
            }
            castName = _getCastName(playType, option.cast);
            castName = playType != 'DXF' ? matchInfo.home + castName : castName;
            if (getRight > 0) {
                tpl += '<p><span class="main-color-s">' + castName + '</span>';
            } else {
                tpl += '<p>' + castName;
            }

            // 检查盘口所在赔率是否中奖
            if (option.odd) {
                if(playType == 'RFSF' || playType == 'DXF'){
                    tpl += '(';
                    var optionOdds = option.odd.split('&');
                    for (var x = 0; x < optionOdds.length; x++) { 
                        if($.inArray(optionOdds[x], rightOdds) > -1){
                            tpl += '<em class="main-color-s">' + optionOdds[x] + '</em>';
                            if(x < optionOdds.length - 1){
                                tpl += '&';
                            } 
                        }else{
                            tpl += optionOdds[x];
                            if(x < optionOdds.length - 1){
                                tpl += '&';
                            } 
                        }
                    };
                    tpl += ')';
                }else{
                    if (getRight > 0) {
                        tpl += '<em class="main-color-s">(' + option.odd + ')</em>';
                    }else{
                        tpl += '(' + option.odd + ')';
                    }
                }       
            }
            tpl += '</p>';
            casts.push(option.cast);
        });

        return tpl;
    }

    function getMatchId(dateStr) {
        if (dateStr.length < 11) {
            dateStr = '20' + dateStr;
        }
        var year = dateStr.substr(0, 4);
        var month = parseInt(dateStr.substr(4, 2), 10);
        var day = dateStr.substr(6, 2);
        var date = new Date(year, month - 1, day);
        var dayId = dateStr.substr(8, 3);

        return ['周' + '日一二三四五六'.charAt(date.getDay()), dayId];
    }

    function parseOrderCode(code) {
        var options = code.split('|')[1].split(',');
        var cast = {};
        $(options).each(function (k, v) {
            var matchId = v.split('=')[0];
            var options = v.split('=')[1].split('/');
            if (!(matchId in cast)) {
                cast[matchId] = options;
            }
        });

        return cast;
    }

    function _compareResult(playType, score, let, option, halfScore, preScore) {
        var awayScore = parseInt(score.split(':')[0], 10);
        var homeScore = parseInt(score.split(':')[1], 10);
        var let = parseFloat(let, 10);
        var result = _getCastOption(playType, score, halfScore, let, preScore);

        return result == option.cast;
    }

    function _parseScore(homeScore, awayScore, let) {
        homeScore += let;
        if (homeScore > awayScore) {
            return 3;
        } else if (homeScore === awayScore) {
            return 1;
        } else {
            return 0;
        }
    }

    function _renderTicket(key, result, passWay, multi, bonus) {
        var options;
        var matches = [];
        var castOptions = [];
        bonus = bonus || 0;
        for (var matchId in result) {
            castOptions = [];
            options = result[matchId];
            matchId = getMatchId(matchId);
            $(playTypes).each(function (k, playType) {
                if (playType in options) {
                    $(options[playType]).each(function (key, option) {
                        castOptions.push(_getCastName(playType, option.cast) + ':' + (option.odd || ''));
                    });
                }
            });
            castOptions = castOptions.join(',');
            matches.push(matchId.join('') + '(' + castOptions + ')');
        }
        matches = matches.join('<p>×</p>');
        var tpl = '<tr>';
        tpl += '<td>' + key + '</td>';
        tpl += '<td>' + matches + '</td>';
        tpl += '<td>' + passWay + '</td>';
        tpl += '<td>' + multi + '</td>';
        tpl += '<td>' + multi * 2 + '</td>';
        tpl += '</tr>'

        return tpl;
    }

    function _paddNum(num) {
        num += "";
        return num.replace(/^(\d)$/, "0$1");
    }

    function _formatDateTime(dateTime) {
        var dt = new Date(dateTime);
        return _paddNum((dt.getMonth() + 1)) + '-' + _paddNum(dt.getDate()) + ' ' +
            _paddNum(dt.getHours()) + ':' + _paddNum(dt.getMinutes());
    }

    function renderPassway(passWay) {
        console.log(passWay);
    }

    window.jcDetail = (function () {
        var me = {};

        me.renderOrderCast = function (cast, awards, ticketInfo) {
            var result = parseTicket(cast, ticketInfo);
            var matches = {};
            var passWay = cast.replace(/.*?\|(\d\*\d;?)/g, '$1').split(';');
            passWay = me.unique(passWay).sort().join(',').replace(/\*/g, '串');
            $(awards).each(function (key, award) {
                if (award && 'mid' in award) {
                    matches[award.mid] = {
                        home: award.home,
                        away: award.awary,
                        score: award.score,
                        halfScore: award.scoreHalf,
                        //preScore: award.preScore,
                        dt: award.dt,
                        mStatus: award.mStatus
                    };
                }
            });
            $('.pass-way').append(passWay);
            $('.match-award').append(renderResult(result, matches, ticketInfo));
        };

        me.renderOrderTicket = function (tickets) {
            var tpl = '';
            var result;
            $(tickets).each(function (key, ticket) {
                if (ticket.ticketMix) {
                    result = parseTicket(ticket.ticketMix, '');
                } else {
                    result = parseTicket(ticket.lotCodes, '');
                }
                var passWay = ticket.lotCodes.split('|')[2].replace('*', '串');
                tpl += _renderTicket(key + 1, result, passWay, ticket.multis, ticket.bonus);
            });
            $('.match-cast').append(tpl);
        };

        me.getMatchId = function (dateStr) {
            return getMatchId(dateStr);
        };
        
        me.unique = function (arr) {
            var result = [], hash = {};
            for (var i = 0, elem; (elem = arr[i]) != null; i++) {
                if (!hash[elem]) {
                    result.push(elem);
                    hash[elem] = true;
                }
            }
            return result;
        }

        return me;
    })();
})($);
