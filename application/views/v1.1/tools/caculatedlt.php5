<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-custom.min.css');?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js');?>"></script>
<div class="wrap mod-box p-calc">
	<div class="lotteryTit issue cp-box-hd">
		<div class="lottery-info">
			<div class="lottery-info-img lottery-dlt">
				<div class="lottery-img">
					<svg width="320" height="320">
						<image alt="大乐透" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
					</svg>
				</div>
			</div>
			<div class="lottery-info-txt">
				<div class="lottery-info-top"><div class="lottery-info-time"><span>其他工具：<a href="/tools/caculate/ssq" target="_blank" class="sub-color">双色球</a></span></div><h1 class="lottery-info-name">大乐透工具大全</h1></div>
				<div class="lottery-info-bt">
					<ul class="bet-type-link">
						<li class="selected"><a href="javascript:;">奖金计算</a></li><li><a href="/info/lists/4?gongju" target="_blank">专家推荐</a></li><li><a href="https://zoushi.166cai.cn/cjwdlt/" target="_blank">走势图表</a></li>
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
 						$bonusStr = "A-0-B-0-C-0";
 						if (!empty($issue['bonusDetail'])) {
							$bonusDetail = json_decode($issue['bonusDetail'], true);
							$jb1 = is_numeric($bonusDetail['1dj']['jb']['dzjj']) ? $bonusDetail['1dj']['jb']['dzjj'] : 'A';
							$zj1 = is_numeric($bonusDetail['1dj']['zj']['dzjj']) ? $bonusDetail['1dj']['zj']['dzjj'] : '0';
							$jb2 = is_numeric($bonusDetail['2dj']['jb']['dzjj']) ? $bonusDetail['2dj']['jb']['dzjj'] : 'B';
							$zj2 = is_numeric($bonusDetail['2dj']['zj']['dzjj']) ? $bonusDetail['2dj']['zj']['dzjj'] : '0';
							$jb3 = is_numeric($bonusDetail['3dj']['jb']['dzjj']) ? $bonusDetail['3dj']['jb']['dzjj'] : 'C';
							$zj3 = is_numeric($bonusDetail['3dj']['zj']['dzjj']) ? $bonusDetail['3dj']['zj']['dzjj'] : '0';
							$bonusStr = $jb1."-".$zj1."-".$jb2."-".$zj2."-".$jb3."-".$zj3;
						}
 						?>
 						<option value=<?php echo $issue['issue']."-".$issue['awardNum']."-".$bonusStr?>><?php echo $issue['issue']?></option>
 					<?php }?>
 					</select>
 					</div>
					<div class="sub">当期开奖号码：
						<div class="award-nums">
						<?php $awardArr = explode('|', $issueArr[0]['awardNum']);
						foreach (explode(',', $awardArr[0]) as $award) {?><span class="ball ball-red"><?php echo $award?></span><?php } foreach (explode(',', $awardArr[1]) as $award) {?><span class="ball ball-blue"><?php echo $award?></span><?php }?>
						</div>
						<a href="/kaijiang/dlt" target="_blank" class="sub-color">查看详情</a>
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
							<div class="sub">选择前区个数：<select><?php for ($i = 5; $i <= 35; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">选择后区个数：<select><?php for ($i = 2; $i <= 12; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub"><label for="zjtz" class="zjtz"><input type="checkbox" id="zjtz" name="zj">追加投注</label></div>
						</div>
						<div class="calc-form-item"><div class="sub">投注金额：共<em class="betNum">1</em>注，共<em class="betMoney">2</em>元</div></div>
						<div class="calc-form-item hit">
							<div class="sub">预计命中前区：<select><?php for ($i = 0; $i <= 5; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">预计命中后区：<select><?php for ($i = 0; $i <= 2; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
						</div>
					</div>
					<div class="tab-radio-inner" tab="DT" style="display: none;">
						<div class="calc-form-item selball">
							<div class="sub">选择前区胆码：<select><?php for ($i = 0; $i <= 4; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">选择前区拖码：<select><?php for ($i = 5; $i <= 33; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">选择后区胆码：<select><?php for ($i = 0; $i <= 1; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">选择后区拖码：<select><?php for ($i = 2; $i <= 12; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub"><label for="dtzjtz" class="dtzjtz"><input type="checkbox" id="dtzjtz" name="dtzj">追加投注</label></div>
						</div>
						<div class="calc-form-item"><div class="sub">投注金额：共<em class="betNum">0</em>注，共<em class="betMoney">0</em>元</div></div>
						<div class="calc-form-item hit">
							<div class="sub">命中前区胆码：<select><?php for ($i = 0; $i <= 0; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">命中前区拖码：<select><?php for ($i = 0; $i <= 5; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">命中后区胆码：<select><?php for ($i = 0; $i <= 0; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
							<div class="sub">命中后区拖码：<select><?php for ($i = 0; $i <= 2; $i++) {?><option><?php echo $i?></option><?php }?></select></div>
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
					<?php $bonusDetail = json_decode($issueArr[0]['bonusDetail'], true);
						$jb1 = is_numeric($bonusDetail['1dj']['jb']['dzjj']) ? $bonusDetail['1dj']['jb']['dzjj'] : 'A';
						$jb2 = is_numeric($bonusDetail['2dj']['jb']['dzjj']) ? $bonusDetail['2dj']['jb']['dzjj'] : 'B';
						$jb3 = is_numeric($bonusDetail['3dj']['jb']['dzjj']) ? $bonusDetail['3dj']['jb']['dzjj'] : 'C';
						?>
						<tr>
							<td>一等奖</td>
							<td><span class="num-red">5</span>+<span class="num-blue">2</span></td>
							<td>0</td>
							<td data-zj="<?php echo is_numeric($bonusDetail['1dj']['zj']['dzjj']) ? $bonusDetail['1dj']['zj']['dzjj'] : '0';?>" data-jb="<?php echo $jb1;?>"><?php echo $jb1;?>元</td>
							<td class="mark"><em>0元</em></td>
						</tr>
						<tr>
							<td>二等奖</td>
							<td><span class="num-red">5</span>+<span class="num-blue">1</span></td>
							<td>0</td>
							<td data-zj="<?php echo is_numeric($bonusDetail['2dj']['zj']['dzjj']) ? $bonusDetail['2dj']['zj']['dzjj'] : '0';?>" data-jb="<?php echo $jb2;?>"><?php echo $jb2;?>元</td>
							<td class="mark"><em>0元</em></td>
						</tr>
						<tr>
							<td>三等奖</td>
							<td><span class="num-red">5</span>+<span class="num-blue">0</span> / <span class="num-red">4</span>+<span class="num-blue">2</span></td>
							<td>0</td>
							<td data-zj="<?php echo is_numeric($bonusDetail['3dj']['zj']['dzjj']) ? $bonusDetail['3dj']['zj']['dzjj'] : '0';?>" data-jb="<?php echo $jb3;?>"><?php echo $jb3;?>元</td>
							<td class="mark"><em>0元</em></td>
						</tr>
						<tr>
							<td>四等奖</td>
							<td><span class="num-red">3</span>+<span class="num-blue">2</span> / <span class="num-red">4</span>+<span class="num-blue">1</span></td>
							<td>0</td>
							<td data-zj="100" data-jb="200">200元</td>
							<td class="mark"><em>0元</em></td>
						</tr>
						<tr>
							<td>五等奖</td>
							<td><span class="num-red">4</span>+<span class="num-blue">0</span> / <span class="num-red">3</span>+<span class="num-blue">1</span> / <span class="num-red">2</span>+<span class="num-blue">2</span></td>
							<td>0</td>
							<td data-zj="5" data-jb="10">10元</td>
							<td class="mark"><em>0元</em></td>
						</tr>
						<tr>
							<td>六等奖</td>
							<td><span class="num-red">3</span>+<span class="num-blue">0</span> / <span class="num-red">2</span>+<span class="num-blue">1</span> / <span class="num-red">1</span>+<span class="num-blue">2</span> / <span class="num-red">0</span>+<span class="num-blue">2</span></td>
							<td>0</td>
							<td data-zj="0" data-jb="5">5元</td>
							<td class="mark"><em>0元</em></td>
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
	1:[[5,2]],
	2:[[5,1]],
	3:[[5,0],[4,2]],
	4:[[3,2],[4,1]],
	5:[[4,0],[3,1],[2,2]],
	6:[[3,0],[2,1],[1,2],[0,2]]
}
$(function(){
	var index = 0, winmap = {1:0, 2:0, 3:0, 4:0, 5:0, 6:0};
	$('.JS_TAB').tabPlug({callbackFun:function(i){
		index = i;
	}});

	var pt = (function(){
		var num0 = 5, num1 = 2, bet0 = 1, bet1 = 1, hit0 = 0, hit1 = 0, betNum = 1, _betMoney = 2, zj = false, $div = $('.tab-radio-bd .tab-radio-inner:first');
		$div.on('change', '.selball select:first', function(){
			num0 = parseInt($(this).val(), 10);
			bet0 = cx.Math.combine(num0, 5);
			calBet();
		})
		$div.on('change', '.selball select:last', function(){
			num1 = parseInt($(this).val(), 10);
			bet1 = cx.Math.combine(num1, 2);
			calBet();
		})
		$div.on('change', '.hit select:first', function(){
			hit0 = parseInt($(this).val(), 10);
		})
		$div.on('change', '.hit select:last', function(){
			hit1 = parseInt($(this).val(), 10);
		})
		$('#zjtz').click(function(){
			_betMoney = 2;
			if ($(this).attr('checked')) _betMoney = 3;
			$div.find('.betMoney').html(betNum * _betMoney);
		})
		$('.caculate').click(function(){
			winmap = {1:0, 2:0, 3:0, 4:0, 5:0, 6:0};
			if (index == 0) {
				var min0 = Math.max(0, 5 - num0 + hit0), max0 = hit0;
				$.each(hitmap, function(k, hits){
					$.each(hits, function(h, hit){
						if (min0 <= hit[0] && max0 >= hit[0] && hit1 >= hit[1] && 2 - hit[1] <= num1 - hit1) 
							winmap[k] += cx.Math.combine(hit0, hit[0]) * cx.Math.combine(num0 - hit0, 5 - hit[0]) * cx.Math.combine(hit1, hit[1]) * cx.Math.combine(num1 - hit1, 2 - hit[1]);
					})
				})
				renderResult(_betMoney);
			}
		})
		var calBet = function(){
			betNum = bet0 * bet1;
			$div.find('.betNum').html(betNum);
			$div.find('.betMoney').html(betNum * _betMoney);
		}
	})();

	var dt = (function(){
		var dnum0 = 0, tnum0 = 5, dnum1 = 0, tnum1 = 2, bet0 = 1, bet1 = 1, dhit0 = 0, thit0 = 0, dhit1 = 0, thit1 = 0, betNum = 0, _betMoney = 2, zj = false,
		$div = $('.tab-radio-bd .tab-radio-inner:last'), selectMap0 = {0:[5,33], 1:[5,23], 2:[4,33], 3:[3,32], 4:[2,31]}, selectMap1 = {0:[2,12], 1:[2,11]};
		$div.on('change', '.selball select:first', function(){
			dnum0 = parseInt($(this).val(), 10);
			renderselect($div.find('.selball select:eq(1)'), selectMap0[dnum0][1], tnum0, selectMap0[dnum0][0]);
			renderselect($div.find('.hit select:first'), dnum0, dhit0);
			tnum0 = parseInt($div.find('.selball select:eq(1)').val(), 10);
			dhit0 = parseInt($div.find('.hit select:first').val(), 10);
			renderselect($div.find('.hit select:eq(1)'), Math.min(5-dhit0, tnum0), thit0);
			thit0 = parseInt($div.find('.hit select:eq(1)').val(), 10);
			bet0 = cx.Math.combine(tnum0, 5 - dnum0);
			calBet();
		})
		$div.on('change', '.selball select:eq(1)', function(){
			tnum0 = parseInt($(this).val(), 10);
			renderselect($div.find('.hit select:eq(1)'), Math.min(5-dhit0, tnum0), thit0);
			thit0 = parseInt($div.find('.hit select:eq(1)').val(), 10);
			bet0 = cx.Math.combine(tnum0, 5 - dnum0);
			calBet();
		})
		$div.on('change', '.selball select:eq(2)', function(){
			dnum1 = parseInt($(this).val(), 10);
			renderselect($div.find('.selball select:last'), selectMap1[dnum1][1], tnum1, selectMap1[dnum1][0]);
			renderselect($div.find('.hit select:eq(2)'), dnum1, dhit1);
			tnum1 = parseInt($div.find('.selball select:last').val(), 10);
			dhit1 = parseInt($div.find('.hit select:eq(2)').val(), 10);
			renderselect($div.find('.hit select:last'), Math.min(2-dhit1, tnum1), thit1);
			thit1 = parseInt($div.find('.hit select:last').val(), 10);
			bet1 = cx.Math.combine(tnum1, 2 - dnum1);
			calBet();
		})
		$div.on('change', '.selball select:last', function(){
			tnum1 = parseInt($(this).val(), 10);
			renderselect($div.find('.hit select:last'), Math.min(2-dhit1, tnum0), thit1);
			thit1 = parseInt($div.find('.hit select:last').val(), 10);
			bet1 = cx.Math.combine(tnum1, 2 - dnum1);
			calBet();
		})
		$div.on('change', '.hit select:first', function(){
			dhit0 = parseInt($(this).val(), 10);
			renderselect($div.find('.hit select:eq(1)'), Math.min(5-dhit0, tnum0), thit0);
			thit0 = parseInt($div.find('.hit select:eq(1)').val(), 10);
		})
		$div.on('change', '.hit select:eq(1)', function(){
			thit0 = parseInt($(this).val(), 10);
		})
		$div.on('change', '.hit select:eq(2)', function(){
			dhit1 = parseInt($(this).val(), 10);
			renderselect($div.find('.hit select:last'), Math.min(2-dhit1, tnum1), thit1);
			thit1 = parseInt($div.find('.hit select:last').val(), 10);
		})
		$div.on('change', '.hit select:last', function(){
			thit1 = parseInt($(this).val(), 10);
		})
		$('#dtzjtz').click(function(){
			_betMoney = 2;
			if ($(this).attr('checked')) _betMoney = 3;
			$div.find('.betMoney').html(betNum * _betMoney);
		})
		$('.caculate').click(function(){
			winmap = {1:0, 2:0, 3:0, 4:0, 5:0, 6:0};
			if (index == 1) {
				if (dnum0 == 0 && dnum1 == 0) cx.Alert({content:'<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">1</span>个前区胆码或<span class="num-red">1</span>个后区胆码'});
				var min0 = Math.max(0, 5 - tnum0 + thit0 - dnum0), max0 = Math.min(dhit0 + thit0, 5 - dnum0 + dhit0), min1 = Math.max(0, 2 - tnum1 + thit1 - dnum1), max1 = Math.min(dhit1 + thit1, 2 - dnum1 + dhit1);
				$.each(hitmap, function(k, hits){
					$.each(hits, function(h, hit){
						if (dhit0 <= hit[0] && min0 <= hit[0] && max0 >= hit[0] && 5-dnum0-hit[0]+dhit0 <= tnum0-thit0 && dhit1 <= hit[1] && min1 <= hit[1] && max1 >= hit[1] && 2-dnum1-hit[1]+dhit1 <= tnum1-thit1) 
							winmap[k] += cx.Math.combine(thit0,hit[0]-dhit0) * cx.Math.combine(tnum0-thit0,5-dnum0-hit[0]+dhit0) * cx.Math.combine(thit1,hit[1]-dhit1) * cx.Math.combine(tnum1-thit1,2-dnum1-hit[1]+dhit1);
					})
				})
				renderResult(_betMoney);
			}
		})
		var calBet = function(){
			betNum = 0;
			if (dnum0 > 0 || dnum1 > 0) betNum = bet0 * bet1;
			$div.find('.betNum').html(betNum);
			$div.find('.betMoney').html(betNum * _betMoney);
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

	var renderResult = function(betMoney){
		var allbonus = 0, strArr = {}, res = [];
		$.each(winmap, function(k, val){
			var singlebonus = $(".cp-box-bd .table table tbody tr:eq("+(k-1)+") td:eq(3)").attr('data-jb');
			if ($.inArray(singlebonus, ['A', 'B', 'C']) === -1) {
				singlebonus = parseInt(singlebonus, 10);
				if (betMoney == 3) singlebonus += parseInt($(".cp-box-bd .table table tbody tr:eq("+(k-1)+") td:eq(3)").attr('data-zj'), 10);
				var bonus = singlebonus * val;
				allbonus += bonus;
			}else {
				if (betMoney == 3 && val > 0) {
					bonus = val * 8 / 5+"*"+singlebonus;
					strArr[k] = bonus;
				}else if (val > 1) {
					bonus = val+"*"+singlebonus;
					strArr[k] = bonus;
				}else if(val == 1) {
					bonus = singlebonus;
					strArr[k] = bonus;
				}else {
					bonus = 0;
				}
				if (betMoney == 3) singlebonus = "1.6*"+singlebonus;
			}
			$(".cp-box-bd .table table tbody tr:eq("+(k-1)+") td:eq(3)").html(singlebonus+'元');
			if (val > 0) {
				$(".cp-box-bd .table table tbody tr:eq("+(k-1)+") td:eq(4)").html("<em>" + bonus + "元</em>");
			}else {
				$(".cp-box-bd .table table tbody tr:eq("+(k-1)+") td:eq(4)").html(bonus + "元");
			}
			$(".cp-box-bd .table table tbody tr:eq("+(k-1)+") td:eq(2)").html(val);
		})
		strArr[4] = allbonus;
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
	$('.award-nums').html(tpl).next('a').attr('href', "/kaijiang/dlt/"+issue);
	$(".cp-box-bd .table table tbody tr:first td:eq(3)").html(value[2] + "元").attr('data-jb', value[2]).attr('data-zj', value[3]);
	$(".cp-box-bd .table table tbody tr:eq(1) td:eq(3)").html(value[4] + "元").attr('data-jb', value[4]).attr('data-zj', value[5]);
	$(".cp-box-bd .table table tbody tr:eq(2) td:eq(3)").html(value[6] + "元").attr('data-jb', value[6]).attr('data-zj', value[7]);
})
</script>