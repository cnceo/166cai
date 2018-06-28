<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-custom.min.css');?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js');?>"></script>
<div class="wrap mod-box p-calc">
	<div class="lotteryTit issue cp-box-hd">
		<div class="lottery-info">
			<div class="lottery-info-img lottery-ssq">
				<div class="lottery-img">
					<svg width="320" height="320">
						<image alt="双色球" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
					</svg>
				</div>
			</div>
			<div class="lottery-info-txt">
				<div class="lottery-info-top"><div class="lottery-info-time"><span>其他工具：<a target="_blank" href="/tools/caculate/dlt" class="sub-color">大乐透</a></span></div><h1 class="lottery-info-name">双色球工具大全</h1></div>
				<div class="lottery-info-bt">
					<ul class="bet-type-link">
						<li class="selected"><a href="javascript:;">奖金计算</a></li><li><a href="/info/lists/2?gongju" target="_blank">专家推荐</a></li><li><a href="https://zoushi.166cai.cn/cjwssq/" target="_blank">走势图表</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<!--彩票-->
	<div class="cp-box-bd ">
		<div class="calc">
			<div class="calc-form tab-radio">
				<div class="calc-form-item">
 					<div class="sub">查询期数：
 					<select id="issueList">
 					<?php foreach ($issueArr as $issue) {
 						$bonusDetail = json_decode($issue['bonusDetail'], true);
 						$dzjj1 = is_numeric($bonusDetail['1dj']['dzjj']) ? $bonusDetail['1dj']['dzjj'] : 'A';
						$dzjj2 = is_numeric($bonusDetail['2dj']['dzjj']) ? $bonusDetail['2dj']['dzjj'] : 'B';?>
 						<option value=<?php echo $issue['issue']."-".$issue['awardNum']."-".$dzjj1."-".$dzjj2?>><?php echo $issue['issue']?></option>
 					<?php }?>
 					</select>
 					</div>
					<div class="sub">当期开奖号码：
						<div class="award-nums">
						<?php $awardArr = explode('|', $issueArr[0]['awardNum']);
						foreach (explode(',', $awardArr[0]) as $award) {?><span class="ball ball-red"><?php echo $award?></span><?php } foreach (explode(',', $awardArr[1]) as $award) {?><span class="ball ball-blue"><?php echo $award?></span><?php }?>
						</div>
						<a href="/kaijiang/ssq" target="_blank" class="sub-color">查看详情</a>
					</div>
				</div>
				<div class="calc-form-item">
					<div class="sub">投注类型：
						<ul class="tab-radio-hd JS_TAB" data-rule='{"currentClass": "active", "linkItem": "DT"}'>
							<li class="active"><label><input name="radioTab" type="radio" checked class="form-item-radio">普通投注</label></li>
							<li><label><input name="radioTab" type="radio" class="form-item-radio">胆拖投注</label></li>
						</ul>
					</div>
				</div>
				<div class="tab-radio-bd">
					<div class="tab-radio-inner" tab="DT">
						<div class="calc-form-item selball">
							<div class="sub">选择红球个数：<select><?php for ($i = 6; $i <= 33; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">选择蓝球个数：<select><?php for ($i = 1; $i <= 16; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
						</div>
						<div class="calc-form-item"><div class="sub">投注金额：共<em class="betNum">1</em>注，共<em class="betMoney">2</em>元</div></div>
						<div class="calc-form-item hit">
							<div class="sub">预计命中红球：<select><?php for ($i = 0; $i <= 6; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">预计命中蓝球：<select><?php for ($i = 0; $i <= 1; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
						</div>
					</div>
					<div class="tab-radio-inner" tab="DT" style="display: none;">
						<div class="calc-form-item selball">
							<div class="sub">选择红球胆码：<select><?php for ($i = 1; $i <= 5; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">选择红球拖码：<select><?php for ($i = 6; $i <= 18; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">选择蓝球个数：<select><?php for ($i = 1; $i <= 16; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
						</div>
						<div class="calc-form-item"><div class="sub">投注金额：共<em class="betNum">6</em>注，共<em class="betMoney">12</em>元</div></div>
						<div class="calc-form-item hit">
							<div class="sub">命中红球胆码：<select><?php for ($i = 0; $i <= 1; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">命中红球拖码：<select><?php for ($i = 0; $i <= 6; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">命中蓝球个数：<select><?php for ($i = 0; $i <= 1; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
						</div>
					</div>
				</div>
				<a href="javascript:;" class="btn-s btn-main caculate">计算奖金</a>
			</div>
			<div class="table">
				<div class="caption">预测奖金（税前）为：<em>0</em>元</div>
				<table>
					<thead><tr><th width="20%">奖级</th><th width="20%">中奖条件</th><th width="20%">中奖注数</th><th width="20%">单注奖金</th><th width="20%">预测奖金（税前）</th></tr></thead>
					<tbody>
					<?php $bonusDetail = json_decode($issueArr[0]['bonusDetail'], true);?>
						<tr><td>一等奖</td><td><span class="num-red">6</span>+<span class="num-blue">1</span></td><td>0</td><td><?php echo is_numeric($bonusDetail['1dj']['dzjj'])?$bonusDetail['1dj']['dzjj']:'A'?>元</td><td class="mark"><em>0元</em></td></tr>
						<tr><td>二等奖</td><td><span class="num-red">6</span>+<span class="num-blue">0</span></td><td>0</td><td><?php echo is_numeric($bonusDetail['2dj']['dzjj'])?$bonusDetail['2dj']['dzjj']:'B'?>元</td><td class="mark"><em>0元</em></td></tr>
						<tr><td>三等奖</td><td><span class="num-red">5</span>+<span class="num-blue">1</span></td><td>0</td><td>3000元</td><td class="mark"><em>0元</em></td></tr>
						<tr><td>四等奖</td><td><span class="num-red">5</span>+<span class="num-blue">0</span> / <span class="num-red">4</span>+<span class="num-blue">1</span></td><td>0</td><td>200元</td><td class="mark"><em>0元</em></td></tr>
						<tr><td>五等奖</td><td><span class="num-red">4</span>+<span class="num-blue">0</span> / <span class="num-red">3</span>+<span class="num-blue">1</span></td><td>0</td><td>10元</td><td class="mark"><em>0元</em></td></tr>
						<tr>
							<td>六等奖</td><td><span class="num-red">2</span>+<span class="num-blue">1</span> / <span class="num-red">1</span>+<span class="num-blue">1</span> / <span class="num-red">0</span>+<span class="num-blue">1</span></td>
							<td>0</td><td>5元</td><td class="mark"><em>0元</em></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!--彩票end-->
</div>
<script>
var hitmap = {
	1:[[6,1]],
	2:[[6,0]],
	3:[[5,1]],
	4:[[5,0],[4,1]],
	5:[[4,0],[3,1]],
	6:[[2,1],[1,1],[0,1]]
}
$(function(){
	var index = 0, winmap = {1:0, 2:0, 3:0, 4:0, 5:0, 6:0};;
	$('.JS_TAB').tabPlug({callbackFun:function(i){
		index = i;
	}});

	var pt = (function(){
		var num0 = 6, num1 = 1, bet0 = 1, bet1 = 1, hit0 = 0, hit1 = 0, betNum = 1, $div = $('.tab-radio-bd .tab-radio-inner:first');
		$div.on('change', '.selball select:first', function(){
			num0 = parseInt($(this).val(), 10);
			bet0 = cx.Math.combine(num0, 6);
			calBet();
		})
		$div.on('change', '.selball select:last', function(){
			num1 = parseInt($(this).val(), 10);
			bet1 = cx.Math.combine(num1, 1);
			calBet();
		})
		$div.on('change', '.hit select:first', function(){
			hit0 = parseInt($(this).val(), 10);
		})
		$div.on('change', '.hit select:last', function(){
			hit1 = parseInt($(this).val(), 10);
		})
		$('.caculate').click(function(){
			winmap = {1:0, 2:0, 3:0, 4:0, 5:0, 6:0};
			if (index == 0) {
				var min0 = Math.max(0, 6 - num0 + hit0), max0 = hit0;
				$.each(hitmap, function(k, hits){
					$.each(hits, function(h, hit){
						if (min0 <= hit[0] && max0 >= hit[0] && hit1 >= hit[1] && 1 - hit[1] <= num1 - hit1) 
							winmap[k] += cx.Math.combine(hit0, hit[0]) * cx.Math.combine(num0 - hit0, 6 - hit[0]) * cx.Math.combine(num1 - hit1, 1 - hit[1]);
					})
				})
				renderResult();
			}
		})
		var calBet = function(){
			betNum = bet0 * bet1;
			$div.find('.betNum').html(betNum);
			$div.find('.betMoney').html(betNum * 2);
		}
	})();

	var dt = (function(){
		var dnum0 = 1, tnum0 = 6, num1 = 1, bet0 = 1, bet1 = 1, dhit0 = 0, thit0 = 0, hit1 = 0, betNum = 1, $div = $('.tab-radio-bd .tab-radio-inner:last'),
		selectMap = {1:[6,18], 2:[5,23], 3:[4,30], 4:[3,29], 5:[2,28]};
		$div.on('change', '.selball select:first', function(){
			dnum0 = parseInt($(this).val(), 10);
			renderselect($div.find('.selball select:eq(1)'), selectMap[dnum0][1], tnum0, selectMap[dnum0][0]);
			renderselect($div.find('.hit select:first'), dnum0, dhit0);
			tnum0 = parseInt($div.find('.selball select:eq(1)').val(), 10);
			dhit0 = parseInt($div.find('.hit select:first').val(), 10);
			renderselect($div.find('.hit select:eq(1)'), Math.min(6-dhit0, tnum0), thit0);
			thit0 = parseInt($div.find('.hit select:eq(1)').val(), 10);
			bet0 = cx.Math.combine(tnum0, 6 - dnum0);
			calBet();
		})
		$div.on('change', '.selball select:eq(1)', function(){
			tnum0 = parseInt($(this).val(), 10);
			renderselect($div.find('.hit select:eq(1)'), Math.min(6-dhit0, tnum0), thit0);
			thit0 = parseInt($div.find('.hit select:eq(1)').val(), 10);
			bet0 = cx.Math.combine(tnum0, 6 - dnum0);
			calBet();
		})
		$div.on('change', '.selball select:last', function(){
			num1 = parseInt($(this).val(), 10);
			bet1 = cx.Math.combine(num1, 1);
			calBet();
		})
		$div.on('change', '.hit select:first', function(){
			dhit0 = parseInt($(this).val(), 10);
			renderselect($div.find('.hit select:eq(1)'), Math.min(6-dhit0, tnum0), thit0);
			thit0 = parseInt($div.find('.hit select:eq(1)').val(), 10);
		})
		$div.on('change', '.hit select:eq(1)', function(){
			thit0 = parseInt($(this).val(), 10);
		})
		$div.on('change', '.hit select:last', function(){
			hit1 = parseInt($(this).val(), 10);
		})
		$('.caculate').click(function(){
			winmap = {1:0, 2:0, 3:0, 4:0, 5:0, 6:0};
			if (index == 1) {
				var min0 = Math.max(0, 6 - tnum0 + thit0 - dnum0), max0 = Math.min(dhit0 + thit0, 6 - dnum0 + dhit0);
				$.each(hitmap, function(k, hits){
					$.each(hits, function(h, hit){
						if (dhit0 <= hit[0] && min0 <= hit[0] && 6-dnum0-hit[0]+dhit0 <= tnum0-thit0 && max0 >= hit[0] && hit1 >= hit[1] && 1 - hit[1] <= num1 - hit1) {
							winmap[k] += cx.Math.combine(thit0, hit[0]-dhit0) * cx.Math.combine(tnum0-thit0, 6-dnum0-hit[0]+dhit0) * cx.Math.combine(num1-hit1, 1-hit[1]);
						}
					})
				})
				renderResult();
			}
		})
		var calBet = function(){
			betNum = bet0 * bet1;
			$div.find('.betNum').html(betNum);
			$div.find('.betMoney').html(betNum * 2);
		}

		var renderselect = function ($selector, max, value, min) {
			var str = '';
			min = min || 0;
			for (var i = min; i <= max; i++) {
				if (i == value) {
					str += "<option selected>"+i+"</option>";
				}else {
					str += "<option>"+i+"</option>";
				}
			}
			$selector.html(str);
		}
	})();
	
	var renderResult = function(){
		var allbonus = 0, strArr = {}, res = [];
		$.each(winmap, function(k, val){
			var singlebonus = $(".cp-box-bd .table table tbody tr:eq("+(k-1)+") td:eq(3)").html().replace(/元/g, '');
			if ($.inArray(singlebonus, ['A', 'B']) === -1) {
				singlebonus = parseInt(singlebonus, 10);
				var bonus = singlebonus * val;
				allbonus += bonus;
			}else {
				if (val > 1) {
					bonus = val+"*"+singlebonus;
					strArr[k] = bonus;
				}else if(val == 1) {
					bonus = singlebonus;
					strArr[k] = bonus;
				}else {
					bonus = 0;
				}
			}
			if (val > 0) {
				$(".cp-box-bd .table table tbody tr:eq("+(k-1)+") td:eq(4)").html("<em>" + bonus + "元</em>");
			}else {
				$(".cp-box-bd .table table tbody tr:eq("+(k-1)+") td:eq(4)").html(bonus + "元");
			}
			$(".cp-box-bd .table table tbody tr:eq("+(k-1)+") td:eq(2)").html(val);
		})
		strArr[3] = allbonus;
		$.each(strArr, function(k, val){
			if (res.length && val == 0) return; 
			res.push(val);
		})
		$('.caption em').html(res.join('+'));
	}
})

$('#issueList').on('change', function(){
	var value = $(this).val().split('-'), issue = value[0], awardArr = value[1].split('|'), tpl = '';
	$.each(awardArr[0].split(','), function(k, award){
		tpl += "<span class='ball ball-red'>"+award+"</span>"
	})
	$.each(awardArr[1].split(','), function(k, award){
		tpl += "<span class='ball ball-blue'>"+award+"</span>"
	})
	$('.award-nums').html(tpl).next('a').attr('href', "/kaijiang/ssq/"+issue);
	$(".cp-box-bd .table table tbody tr:first td:eq(3)").html(value[2] + "元");
	$(".cp-box-bd .table table tbody tr:eq(1) td:eq(3)").html(value[3] + "元");
})
</script>