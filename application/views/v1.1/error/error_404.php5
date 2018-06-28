<div class="p-404">
	<img src="/caipiaoimg/v1.1/img/img-404.png" width="960" height="120" alt="对不起，您访问的页面不存在">
	<div class="p-404-bg"></div>
	<div class="p-404-side">
		<h2>这个页面都被发现了，看来是要中奖的节奏</h2>
		<div class="mod-bet">
			<div class="mod-bet-bd">
				<div class="mod-bet-l ball-group-b inputArea"></div>
				<div class="mod-bet-r">
					<a href="javascript:;" target="_self" class="change"><i class="icon-font"></i>换一换</a><a href="javascript:;" target="_self" class="btn btn-main btn-bet">立即投注</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	randsel();
})

var randsel = function(){
	var minConfig = [6, 1], amountConfig = [33, 16], balls = {}, str = '', castStr = '', max = [33, 16];
	for (i in minConfig) {
		balls[i] = [];
		while (balls[i].length < minConfig[i]) {
    		j = Math.ceil(Math.random() * amountConfig[i]);
    		if ($.inArray(j, balls[i]) === -1){
    			balls[i].push(j);
    		}
    	}
    	balls[i].sort(function(a, b){
			a = parseInt(a, 10);
			b = parseInt(b, 10);
			return a > b ? 1 : ( a < b ? -1 : 0 );
		})
	}
	renderCast(balls, max);
}
function renderCast(balls, max){
	var castStr = '',str = '';;
	for(i in balls){
		for(j in balls[i]) {
			if (i == 1) {
				str += renderBlue(balls[i][j], max[i], j);
			}else {
				str += renderRed(balls[i][j], max[i], j);
			}
			castStr += balls[i][j]+","
		}
		castStr = castStr.slice(0, -1)+"|";
	}
	castStr = castStr.slice(0, -1)+":1:1";
	$(".mod-bet-bd").attr('data-code', castStr);
	$(".ball-group-b").html(str);
}
function renderRed(ball, max, index){
	return "<span><input class='rotate' value='"+pad(ball)+"' max='"+max+"' index='"+index+"'></span>";
}

function renderBlue(ball, max, index){
	return "<span class='ball-blue'><input class='rotate blue' value='"+pad(ball)+"' max='"+max+"' index='"+index+"'></span>";
}

$(".change").click(function(){
	randsel();
	$(this).parents('.mod-bet-bd').find('.btn-bet').attr('error', '0');
})


$(".mod-bet-bd").on('blur', ".rotate", function(){
	var max = $(this).attr('max'), str = '';
	if ($(this).val().match(/\D/g) !== null) {//非法字符
		$(this).val('');
	}else if(parseInt($(this).val()) > max){
		cx.Alert({content: "号码球超出范围"});
    	$(this).val('');
    }else if (!$(this).val() || $(this).val() == 0){
    	$(this).val('');
    }
    if ($(this).val() !== '') {
    	$(this).val(pad($(this).val()));
    }else {
    	$(this).parents('.mod-bet-bd').find('.btn-bet').attr('error', '1');
    	$(this).parents('.mod-bet-bd').find(".mod-bet-ft em:last").html('0');
    	return;
    }
    	
    var code = $(this).parents('.mod-bet-bd').data('code');
    var codesArr = code.split(':');
    var codeArr = codesArr[0].split('|');
    if ($(this).hasClass('blue')) {
        var arr = codeArr[1].split(',');
        if ($.inArray(parseInt($(this).val(), 10).toString(), arr) > -1 && arr[$(this).attr('index')] != parseInt($(this).val(), 10)) {
        	$(this).val('');
        	$(this).parents('.mod-bet-bd').find('.btn-bet').attr('error', '1');
        	cx.Alert({content: "号码球数字重复"});
        	$(this).parents('.mod-bet-bd').find(".mod-bet-ft em:last").html('0');
        	return;
        }else {
        	arr[$(this).attr('index')] = $(this).val();
        	$(this).parents('.mod-bet-bd').find('.btn-bet').attr('error', '0');
            var str = codeArr[0]+"|"+arr.join(',')+":"+codesArr[1]+":"+codesArr[2];
        }
    }else {
    	var arr = codeArr[0].split(',');
    	if ($.inArray(parseInt($(this).val(), 10).toString(), arr) > -1 && arr[$(this).attr('index')] != parseInt($(this).val(), 10)) {
        	cx.Alert({content: "号码球数字重复"});
        	$(this).val('');
        	$(this).parents('.mod-bet-bd').find('.btn-bet').attr('error', '1');
        	$(this).parents('.mod-bet-bd').find(".mod-bet-ft em:last").html('0');
        	return;
        }else {
        	arr[$(this).attr('index')] = $(this).val();
        	$(this).parents('.mod-bet-bd').find('.btn-bet').attr('error', '0');
        	var str = arr.join(',')+"|"+codeArr[1]+":"+codesArr[1]+":"+codesArr[2];
        }
    }
    $(this).parents('.mod-bet-bd').attr('data-code', str);
})

function pad(i) {
    i = '' + i;
    if (i.length < 2) {
        i = '0' + i;
    }
    return i;
}

$(".btn-bet").click(function(){
	var $item = $(this).parents('.mod-bet-bd');
	if($(this).attr('error') == 1){
		cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请至少选择<span class='num-red'>６</span>个红球和<span class='num-blue'>１</span>个蓝球"});
	}
	location.href = '/ssq?codes=' + encodeURIComponent( $item.attr('data-code') ) + '&playType=1&multi=1';
})
</script>
