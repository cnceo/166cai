(function ($) {
    var playTypes = ['SPF', 'RQSPF', 'CBF', 'JQS', 'BQC'];
    var playNames = {
        SPF: '胜平负',
        RQSPF: '让球胜平负',
        CBF: '猜比分',
        JQS: '进球数',
        BQC: '半全场'
    };
    var playOptions = {
        SPF: [3, 1, 0],
        RQSPF: [3, 1, 0],
        JQS: [0, 1, 2, 3, 4, 5, 6, 7],
        CBF: ['1:0', '2:0', '2:1', '3:0', '3:1', '3:2', '4:0', '4:1', '4:2', '5:0', '5:1', '5:2', '9:0', '0:0', '1:1', '2:2', '3:3', '9:9', '0:1', '0:2', '1:2', '0:3', '1:3', '2:3', '0:4', '1:4', '2:4', '0:5', '1:5', '2:5', '0:9'],
        BQC: ['3:3', '3:1', '3:0', '1:3', '1:1', '1:0', '0:3', '0:1', '0:0']
    };

    var playOptionNames = {
		SPF: {
            3: '主胜',
            1: '平',
            0: '客胜'
        },
        RQSPF: {
            3: '主胜【让】',
            1: '平【让】',
            0: '客胜【让】'
        },
        JQS: {
            7: '7+'
        },
        CBF: {
            '9:0': '胜其他',
            '9:9': '平其他',
            '0:9': '负其他'
        },
        BQC: {
            '3-3': '胜-胜',
            '3-1': '胜-平',
            '3-0': '胜-负',
            '1-3': '平-胜',
            '1-1': '平-平',
            '1-0': '平-负',
            '0-3': '负-胜',
            '0-1': '负-平',
            '0-0': '负-负'
        }
    };

    function parseTicket(tickets, ticketInfo) {
        tickets = tickets.split(';');
        var cast = {};
        for (var i = 0; i < tickets.length; ++i) {
            var ticket = tickets[i];
            var parts = ticket.split('|');
            var codes = parts[1];
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
                        let: 0
                    };
                }
                if (!(playType in cast[matchId])) {
                    cast[matchId][playType] = [];
                    cast[matchId].play += 1;
                }
                var pattern = /([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?/;
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
                                odd: pl
                            };
                            if (playType == 'RQSPF') {
                                cast[matchId].let = pks; //matches[2];
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
        tpl += '<td rowspan="' + rowspan + '">' + matchInfo.home + '</td>';
        tpl += '<td rowspan="' + rowspan + '">' + matchInfo.away + '</td>';
        var playCount = 0;
        $(playTypes).each(function (k, playType) {
            if (playType in cast) {
                if (playCount > 0) {
                    tpl += '<tr>';
                }
                tpl += '<td>' + _renderLet(playType, cast.let) + '</td>';	// 让球
                tpl += '<td>' + (matchInfo.mStatus == '1' ? '场次取消' : _renderResult(playType, matchInfo, cast)) + '</td>';	// 比分
                //tpl += '<td>' + _renderAward(playType, cast[playType], cast.let, matchInfo) + '</td>';	// 开奖结果
                // 获取实际开奖赔率信息
                if(ticketInfo !== null && ticketInfo !== undefined){
                    ticketData = ticketInfo[matchId][playType];
                }
                tpl += '<td>' + _renderOptions(playType, cast[playType], cast.let, matchInfo, ticketData) + '</td>'; // 投注方案
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
                return;
            }
            tpl += '<p>';
            if (matchInfo.score) {
                isRight = _compareResult(playType, matchInfo.score, let, option, matchInfo.halfScore);
            }
            castName = _getCastName(playType, option.cast);
            if (isRight) {
                tpl += '<p style="color: red;">' + castName + '</p>';
            }
            tpl += '</p>';
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
            return '<p>' + matchInfo.home + '</p><p>VS</p><p>' + matchInfo.away + '</p>';
        } else {
            return '';
        }
    }

    function _renderLet(playType, let) {
        if (playType === 'RQSPF') {
            if (let !== null && let !== undefined) {
                return let;
            }
        }
        return '-';
    }

    function _getCastOption(playType, score, halfScore, let) {
        var homeScore = parseInt(score.split(':')[0], 10);
        var awayScore = parseInt(score.split(':')[1], 10);
        var let = parseInt(let, 10);
        var result = null;

        if (playType == 'SPF') {
            result = _parseScore(homeScore, awayScore, 0);
        } else if (playType == 'RQSPF') {
            result = _parseScore(homeScore, awayScore, let);
        } else if (playType == 'JQS') {
            result = homeScore + awayScore;
            result = (result > 7) ? 7 : result;
        } else if (playType == 'CBF') {
            result = homeScore + ':' + awayScore;
            if ($.inArray(result, playOptions[playType]) == -1) {
                if (homeScore > awayScore) {
                    result = '9:0';
                } else if (homeScore === awayScore) {
                    result = '9:9';
                } else {
                    result = '0:9';
                }
            }
        } else if (playType == 'BQC') {
            var halfHome = parseInt(halfScore.split(':')[0], 10);
            var halfAway = parseInt(halfScore.split(':')[1], 10);
            var halfResult = _parseScore(halfHome, halfAway, 0);
            var fullResult = _parseScore(homeScore, awayScore, 0);
            result = halfResult + '-' + fullResult;
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

    function _renderOptions(playType, options, let, matchInfo, ticketData) {
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
                // 让球胜平负 RQSPF
                if(playType == 'RQSPF'){
                    var letArr = let.split('&');
                    for (var i = 0; i < letArr.length; i++) {
                        isRight = _compareResult(playType, matchInfo.score, letArr[i], option, matchInfo.halfScore);
                        if(isRight){
                            if(ticketData !== null && ticketData !== undefined && ticketData[letArr[i]][option.cast] !== undefined){
                                for (var j = 0; j < ticketData[letArr[i]][option.cast].length; j++) {
                                    rightOdds.push(ticketData[letArr[i]][option.cast][j]);    
                                };
                                getRight = getRight + 1;
                            }
                        }
                    }
                }else{
                    isRight = _compareResult(playType, matchInfo.score, let, option, matchInfo.halfScore);
                    if(isRight){
                        getRight = 1;
                    }
                }
            }
            castName = _getCastName(playType, option.cast);
            if (getRight) {
                tpl += '<p><span class="main-color-s">' + castName + '</span>';
            } else {
                tpl += '<p>' + castName;
            }

            // 检查盘口所在赔率是否中奖
            if (option.odd) {
                if(playType == 'RQSPF'){
                    tpl += '(';
                    var optionOdds = option.odd.split('&');
                    for (var x = 0; x < optionOdds.length; x++) { 
                        if($.inArray(optionOdds[x], rightOdds) > -1){
                            tpl += '<em class="main-color-s">' + optionOdds[x] + '</em>';;
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

    function _compareResult(playType, score, let, option, halfScore) {
        var homeScore = parseInt(score.split(':')[0], 10);
        var awayScore = parseInt(score.split(':')[1], 10);
        var let = parseInt(let, 10);
        var result = _getCastOption(playType, score, halfScore, let);

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

    window.jcDetail = (function () {
        var me = {};

        me.renderOrderCast = function (cast, awards, ticketInfo) {
            var result = parseTicket(cast, ticketInfo);
            var matches = {};
            var passWay = cast.split('|')[2].replace(/\*/g, '串');
            $(awards).each(function (key, award) {
                if (award && 'mid' in award) {
                    matches[award.mid] = {
                        home: award.home,
                        away: award.awary,
                        score: award.score,
                        halfScore: award.scoreHalf,
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

        return me;
    })();
})($);
