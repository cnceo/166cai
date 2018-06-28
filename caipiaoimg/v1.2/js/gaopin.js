$(function(){
	var m = Math.floor(tm / 60);
    var s = Math.floor(tm % 60);
    
    $(".bet-syxw .bet-type-link, .bet-k3 .bet-type-link, .bet-klpk .bet-type-link").tabPlug({
        cntSelect: '.bet-type-link-bd',
        menuChildSel: 'li',
        onStyle: 'selected',
        cntChildSel: '.bet-type-link-item',
        eventName: 'click',
        callbackFun: function (k, e) {
      	  var type = $("."+cx._basket_.tab).eq(k).data('type');
      	  cx._basket_.setType(type);
        }
    });
    
    $(".bet-type-link").tabPlug({
        cntSelect: '.bet-tab-bd',
        menuChildSel: 'li',
        onStyle: 'selected',
        cntChildSel: '.bet-pick-area',
        eventName: 'click',
        callbackFun: function (k, e) {
        	var type = $(".bet-type-link li").eq(k).data('type');
        	cx._basket_.setType(type);
        }
    });    
    
    $(".bet-type-tab-hd").tabPlug({
        cntSelect: '.bet-type-tab-bd',
        menuChildSel: 'li',
        onStyle: 'selected',
        cntChildSel: '.bet-pick-area',
        eventName: 'click',
        callbackFun: function (k, e) {
			var type = e.parents('.bet-type-link-item').find(".bet-type-tab-hd li:eq("+e.index()+") input:radio").attr('id');
			cx._basket_.setType(type);
		}
    });
})
var cont = 0;
function countdown() {
	if (tm % 5 == 0 && tm % 2 == 1 && $(".jiezhi").length > 0) {
		$(".jiezhi").parents('.pop-alert').remove();
		cx.Mask.hide();
	}
	if ((tm <= 600 && tm % 60 == 0) || tm < 0 || (atm <= 0 && isNaN(parseInt(vJson[0], 10)) && tm%10 == 0)) {
		if (cont < 10) {
			cont++;
			rurl = "/source/cache/issue_"+enName+".html?"+Math.floor(Math.random()*10000);
			if (tm <= 0 || $.inArray(cx.Lottery.lid, [cx.Lid.JLKS, cx.Lid.JXKS]) > -1 ) {
				rurl = "/source/cache/issuefollow_"+enName+".html?"+Math.floor(Math.random()*10000);
			}else if (($.inArray(cx.Lottery.lid, [cx.Lid.KS, cx.Lid.JXKS, cx.Lid.KLPK]) > -1 && $(".jqzs").parents('li').hasClass('selected')) || cx.Lottery.lid === cx.Lid.CQSSC) {
				if (isNaN(parseInt(vJson[0], 10)) || (atm <=0 && atm >= -10)) rurl = "/source/cache/issuehstyopen_"+enName+".html?"+Math.floor(Math.random()*10000);
			}
			
			$.ajax({
				url: rurl,
				dataType: 'json',
	            beforeSend: function () {
	                cy *= 2;
	                clearInterval(inteval);
	                inteval = setInterval("countdown()", cy);
	            },
	            success: function (data) {
	            	if (isnull(data, false) || isnull(data.issue, true) || isnull(data.seFsendtime, true)) {
	                    clearInterval(inteval);
	                    cy *= 2;
	                    inteval = setInterval("countdown()", cy);
	                } else if((data.issue === ISSUE && tm > 0) || (data.issue > ISSUE && tm <= 0)) {
	                	
	                	if (tm <= 0 || $.inArray(cx.Lottery.lid, [cx.Lid.JLKS, cx.Lid.JXKS]) > -1){
	                		var tbstr = '', num, j = 0, maxissue, issues = [], multi = parseInt(($(".chase-number-table-hd .follow-multi:first").val() || multiModifier.value), 10), LASTISSUE = ISSUE, 
	                		date = new Date(data.seFsendtime);
	                		//剩余时间、当前期、ENDTIME等 重置
	                		rest = data.restTime, cheseLen = 0, ISSUE = data.issue, 
	                		ENDTIME = date.getFullYear()+"-"+padd(date.getMonth() + 1)+"-"+padd(date.getDate())+" "+padd(date.getHours())+":"+padd(date.getMinutes())+":"+padd(date.getSeconds());
	                		
	                		if (rest > 0) {//重置当前期
	                			chases = data.chases;
	                			if (LASTISSUE !== ISSUE) {
	                				var chaseLength = cx._basket_.chase.chaseLength;
		                			$(".pub-pop").remove();
		                    		cx.Alert({content:"<p class='jiezhi tal'>您好，第<span class='num-red'>"+data.prev+"</span>期已截止，当前期是第<span class='num-red'>"+data.issue+"</span>期，投注时请确认选择的期号。</p>"});
		                    		if (cx._basket_.chase.chases[LASTISSUE]) {
		                    			cx._basket_.chase.chaseMulti -= cx._basket_.chase.chases[LASTISSUE].multi;
		                    			delete cx._basket_.chase.chases[LASTISSUE];
		                    			cx._basket_.chase.chaseLength--;
		                    		}
	                			}

	                    		for (i in cx._basket_.chase.chases) {
	                    			if (chases[i] === undefined) {
	                    				if ($.inArray(cx.Lottery.lid, [cx.Lid.KS, cx.Lid.JXKS, cx.Lid.JLKS]) === -1) cx._basket_.chase.chaseMulti -= cx._basket_.chase.chases[i].multi;
	                    				delete cx._basket_.chase.chases[i];
	                    				cx._basket_.chase.chaseLength--;
	                    			}else {
	                    				maxissue = i;
	                    			}
	                    		}
	                    		for (i in chases) {
	                    			cheseLen++;
	                    			if (j < chaseLength) {
	                    				if (cx._basket_.chase.chases[i]) {//用户选了这期
	                    					cx._basket_.chase.setChaseByI(i);//重置下award_time,end_time
	                    					j++;
	                    				}else if (i > maxissue) {//用户没选这期，但是需要补这期
	                    					cx._basket_.chase.setChaseByI(i);
	                    					cx._basket_.chase.chases[i].multi = multi;
	                    					cx._basket_.chase.chases[i].money = multi * cx._basket_.betMoney;
	                    					if ($.inArray(cx.Lottery.lid, [cx.Lid.KS, cx.Lid.JLKS, cx.Lid.JXKS]) === -1) cx._basket_.chase.chaseMulti += multi;
	                    					j++;
	                    					cx._basket_.chase.chaseLength++;
	                    				}
	                    				issues.push(i);
	                    			}else {
	                    				continue;
	                    			}
	                    		}
	                    		if ($.inArray(cx.Lottery.lid, [cx.Lid.KS, cx.Lid.JXKS, cx.Lid.JLKS]) === -1) {
	                    			cx._basket_.chase.chaseMoney = cx._basket_.chase.chaseMulti * cx._basket_.betMoney;
	                        		cx._basket_.chase.renderChase(issues);
	                        		$('.chase-number-table-hd .follow-issue').val(cx._basket_.chase.chaseLength).attr('data-max', cheseLen);
	                    			$(".chase-number-table-ft .fbig em:first").html(cx._basket_.chase.chaseLength);
	                    			$(".chase-number-table-ft .fbig em:last").html(cx._basket_.chase.chaseMoney);
	                    		}
	                    		
	                    		if ($.inArray(cx.Lottery.lid, [cx.Lid.KS, cx.Lid.JXKS, cx.Lid.JLKS, cx.Lid.KLPK]) > -1) {
	                    			$('.multi-modifier-s.chase').find('.plus').attr('data-max', cheseLen);
	                    			$('.multi-modifier-s.chase').find('.number').val(cx._basket_.chase.chaseLength);
	                    		}
	                		}
	                	}
	                    
	                	atm = data.awardresttime;
	                	tm = data.restTime;//重置倒计时变量
	                	vJson = data.awardNumber.replace(/\|/, ',').split(',');
	                	if (data.history) {
	                		hsty = eval(data.history);//重置历史数据全局变量
	                        mall = eval(data.miss);//重置遗漏数据全局变量
	                	}
	                	render(data);
	                	
	                    clearInterval(inteval);
	                    cy = 1000;
	                    inteval = setInterval("countdown()", cy);
	                }else {
	                	clearInterval(inteval);
	                    cy = 1000;
	                    inteval = setInterval("countdown()", cy);
	                }
	            }
			})
		}
	} else {
		cont = 0;
		tm--;
		if (atm && atm > 0) atm--;
        renderTime();
	}
}



function maketstr(time) {
	if (time < 0) {
    	return "00:00";
    } else {
    	h = Math.floor(time / 3600);
    	if (h > 0) {
    		time = time % 3600;
    		return padd(h)+":"+padd(Math.floor(time / 60)) + ":" + padd(time % 60);
    	} else {
    		return padd(Math.floor(time / 60)) + ":" + padd(time % 60);
    	}
    }
}


function isnull(exp, num) {
    if (!exp || typeof(exp) === "undefined" || exp === 0) return true;
    if (num && isNaN(parseInt(exp, 10))) return true;
    return false;
}

function padd(num) {
	if (num.toString().length <= 2) num = ('0' + num).slice(-2);
	return num;
}

