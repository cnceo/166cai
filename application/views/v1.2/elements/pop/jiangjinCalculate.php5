<div class="pub-pop pop-jsq jiangjinCalculate">
	<div class="pop-in">
		<div class="pop-head">
			<h2>双色球奖金计算器</h2>
			<span class="pop-close" title="关闭">&times;</span>
		</div>
		<div class="pop-body">
			<div class="pop-jsq-p">
				<div class="fr">
					选择蓝球个数：
					<select class="rand-count blue">
						<?php for($i = 1; $i <= 16; $i++):?>
	                    <option value="<?php echo $i;?>"><?php echo $i;?>
	                    <?php endfor;?>
	                </select>
				</div>
				<div>
					选择红球个数：
					<select class="rand-count red">
	                    <?php for($i = 6; $i <= 33; $i++):?>
	                    <option value="<?php echo $i;?>"><?php echo $i;?>
	                    <?php endfor;?>
	                </select>
                </div>
			</div>
			<p>投注金额：共 <b class="main-color-s multiple">1</b> 注，共 <b class="main-color-s mney">2</b> 元</p>
			<div class="pop-jsq-p">
				<div class="fr">
					预计命中蓝球：
					<select class="rand-count bluePre">
	                    <?php for($i = 0; $i <= 1; $i++):?>
	                    <option value="<?php echo $i;?>"><?php echo $i;?>
	                    <?php endfor;?>
	                </select>
				</div>
				<div>
					预计命中红球：
					<select class="rand-count redPre">
	                    <?php for($i = 0; $i <= 6; $i++):?>
	                    <option value="<?php echo $i;?>"><?php echo $i;?>
	                    <?php endfor;?>
	                </select>
                </div>
			</div>
			<div class="tac">
				<a href="javascript:;" class="btn btn-main btn-jsq jjjs">计算奖金<i class="icon-font">&#xe614;</i></a>
			</div> 
			<div class="pop-jsq-table" style="display: none">
				<table>
					<thead>
						<tr>
							<th>奖级</th>
							<th>中奖注数</th>
							<th>单注奖金</th>
							<th>预测奖金</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>一等奖</td>
							<td><div class = "first">-</div></td>
							<td class="main-color-s">A元</td>
							<td class="main-color-s"><div class = "firstmoney">0元</div></td>
						</tr>
						<tr>
							<td>二等奖</td>
							<td><div class = "sec">-</div></td>
							<td class="main-color-s">B元</td>
							<td class="main-color-s"><div class = "secmoney">0元</div></td>
						</tr>
						<tr>
							<td>三等奖</td>
							<td><div class = "thr">-</div></td>
							<td class="main-color-s">3000元</td>
							<td class="main-color-s"><div class = "thrmoney">0元</div></td>
						</tr>
						<tr>
							<td>四等奖</td>
							<td><div class = "fourth">-</div></td>
							<td class="main-color-s">200元</td>
							<td class="main-color-s"><div class = "fourthmoney">0元</div></td>
						</tr>
						<tr>
							<td>五等奖</td>
							<td><div class = "fifth">-</div></td>
							<td class="main-color-s">10元</td>
							<td class="main-color-s"><div class = "fifmoney">0元</div></td>
						</tr>
						<tr>
							<td>六等奖</td>
							<td><div class = "six">-</div></td>
							<td class="main-color-s">5元</td>
							<td class="main-color-s"><div class = "sixmoney">0元</div></td>
						</tr>
					</tbody>
				</table>
				<i class="arrow"></i>
			</div>
			
			<div class="pop-jsq-result" style="display: none">预测奖金（税前）为：<em class="money">A+B+19200元</em></div>
			<div class="btn-group" style="display: none">
                <a class="bet-blue" href="javascript:;">确定</a>
			</div>
			
		</div>
	</div>
</div>

<script>
$(function(){
    function  combine(n, m) {
        if( m <= n )
        {
            var dividend = factorial(n, n - m + 1); //被除数
            var divisor = factorial(m, 1);   //除数
            return dividend / divisor;
        }
        else
        {
            return 0;
        }

    }
    function  factorial(n, s) {
        if (n == 0) {
            return 1;
        }
        var product = 1;
        (s > 0) || (s = 1);
        for (var i = s; i <= n; ++i) {
            product *= i;
        }
        return product;
    }
    function   product(arr) {
        var p = 1;
        for (var i = 0, len = arr.length; i < len; ++i) {
            if ($.isArray(arr[i])) {
                p *= arr[i].length;
            } else {
                p *= arr[i];
            }
        }
        return len ? p : 0;
    }
    $(".red").change(function(){
        var redValue = $('.red option:selected') .val();
        var blueValue = $('.blue option:selected') .val();
        var ball = combine(redValue, 6) * combine(blueValue, 1);
        var ballmoney = ball * 2;
        $(".multiple").html(ball);
        $(".mney").html(ballmoney);
    });
    $(".blue").change(function(){
        var redValue = $('.red option:selected') .val();
        var blueValue = $('.blue option:selected') .val();
        var ball = combine(redValue, 6) * combine(blueValue, 1);
        var ballmoney = ball * 2;
        $(".multiple").html(ball);
        $(".mney").html(ballmoney);

    });
    $(".pop-jsq").on('click', '.btn-jsq', function(){
    	var docHeight = $('.pop-mask').height();
        $(".pop-jsq-table").show();
        $(".pop-jsq-result").show();
        $(".btn-group").show();
        if($('.pop-jsq').outerHeight() > $(window).height()){
        	$('.pop-jsq').css({
    			'top': docHeight - $('.pop-jsq').outerHeight() -40 + 'px'
    		})
        	var ie6=!-[1,]&&!window.XMLHttpRequest;
			if(ie6){
        		$('.pop-mask').css({
            		'position': 'absolute'
                })
        	}else{
        		$('.pop-mask').css({
            		'position': 'fixed'
                })	
        	}
		}else{
			$('.pop-jsq').css({
    			'top': $(window).scrollTop() + $(window).height()/2 - $('.pop-jsq').outerHeight()/2 + 'px',
    			'margin-top': 0
    		})
		}
        var redValue = $('.red option:selected') .val();
        var blueValue = $('.blue option:selected') .val();
        var redValuePre = $('.redPre option:selected') .val();
        var blueValuePre = $('.bluePre option:selected') .val();
        var bets_fir = combine(redValuePre, 6) * combine(blueValuePre, 1);
        var bets_sec = combine(redValuePre, 6) * combine(blueValue-blueValuePre, 1);
        var bets_thr = combine(redValuePre, 5) * combine(redValue - redValuePre, 6-5) * combine(blueValuePre, 1);
        var bets_fourth = combine(redValuePre, 5) * combine(redValue - redValuePre, 6-5) * combine(blueValue-blueValuePre, 1) + combine(redValuePre, 4) * combine(redValue - redValuePre, 6-4) * combine(blueValuePre, 1);
        var bets_fifth = combine(redValuePre, 4) * combine(redValue - redValuePre, 6-4) * combine(blueValue-blueValuePre, 1) + combine(redValuePre, 3) * combine(redValue - redValuePre, 6-3) * combine(blueValuePre, 1);
        var bets_six = combine(redValuePre, 2) * combine(redValue -redValuePre, 6-2) * combine(blueValuePre, 1) + combine(redValuePre, 1) * combine(redValue -redValuePre, 6-1) * combine(blueValuePre, 1) + combine(redValue -redValuePre, 6) * combine(blueValuePre, 1);
        var money_fir = (bets_fir == 0 ? 0 : "A");
        var money_sec = (bets_sec == 0 ? 0 : (bets_sec == 1 ? "B" : bets_sec +"*B"));
        var money_thr = bets_thr * 3000;
        var money_fourth = bets_fourth * 200;
        var money_fifth = bets_fifth * 10;
        var money_six = bets_six *5;
        var money_fs = (money_fir == 0 ? "" : money_fir +"+") + (money_sec == 0 ? "" : money_sec + "+");
        var money_last =  money_thr + money_fourth + money_fifth + money_six;
        money_fs = (money_last == 0 ? money_fs.substring(0,money_fs.length-1) : money_fs);
        money_last = (money_last == 0 ? "" : money_last);
        $(".first").html( bets_fir);
        $(".sec").html( bets_sec);
        $(".thr").html( bets_thr);
        $(".fourth").html( bets_fourth);
        $(".fifth").html( bets_fifth);
        $(".six").html( bets_six);
        $(".firstmoney").html( money_fir+'元');
        $(".secmoney").html( money_sec+'元');
        $(".thrmoney").html( money_thr+'元');
        $(".fourthmoney").html( money_fourth+'元');
        $(".fifmoney").html( money_fifth+'元');
        $(".sixmoney").html( money_six+'元');
        (money_fs == 0 && money_last == 0) ? $(".money").html("0元") : $(".money").html(money_fs + money_last + "元");
    });
});
$(".bet-blue").click(function(){
	cx.PopCom.hide(".pop-jsq");
})

</script>

