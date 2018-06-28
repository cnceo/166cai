<div class="pub-pop pop-contrast">
	<div class="pop-in">
		<div class="pop-head">
			<h2></h2>
			<span class="pop-close" title="关闭">&times;</span>
		</div>
		<div class="pop-body">
			<div class="bet-area-box">
            	<div class="bet-area-box-bd">
            		 <div class="inner">
            			<ul class="cast-list"></ul>
					</div>
				</div>
			</div>

        	<div class="periods">
        		对比期数：
        		<input type="radio" name="periods" value="0" id="num_0"><label for="num_0">全部期数</label><input type="radio" name="periods" value="300" checked id="num_300"><label for="num_300">近300期</label><input type="radio" name="periods" value="100" id="num_100"><label for="num_100">近100期</label><input type="radio" name="periods" value="50" id="num_50"><label for="num_50">近50期</label><input type="radio" name="periods" value="30" id="num_30"><label for="num_30">近30期</label>
        	</div>
        	
        	<div class="tac">
				<a href="javascript:;" class="btn btn-main btn-jsq">开始对比<i class="icon-font"></i></a>
			</div>
        	<div class="contrast" style="display: none">
        		<div class="contrast-hd">
        			<span class="contrast-title">全部期数对比结果</span>
        		</div>
        		<div class="contrast-bd">
        		</div>
        		<div class="contrast-ft"><p></p></div>
        	</div>
        	<div class="btn-group" style="display: none">
                <a class="bet-blue btn-pop-confirm" href="javascript:;">立即预约</a>
			</div>
		</div>
	</div>
</div>
<script>
var notbind = '<?php echo $showBind ?>';
var ie6=!-[1,]&&!window.XMLHttpRequest;
var enNames = {
		23529:'dlt',
        51:'ssq'
}
var higArr = {
		23529: ['0', '1', '2'],
        51: ['0', '1']
}
$(function(){
	$(".pop-contrast .pop-head h2").html(getCnName(cx.Lottery.lid)+'历史开奖对比');
})
$(".pop-contrast").on('click', '.btn-jsq', function(){
	$(".contrast").show();
	var docHeight = $('.pop-mask').height();
	$.ajax({
        type: 'post',
        url:  '/api/'+enNames[cx.Lottery.lid]+'compareresult',
        data: {'codes':cx.castData.toCastString(cx._basket_.strings),'issueCount':$(".periods :checked").val()},
        dataType: 'json',
        beforeSend: function(){
        	$(".contrast-bd").empty();
        	$(".contrast-bd").html('<div class="pop-loading"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/pop-loading.gif');?>" width="28" height="28" alt=""></div>');
        },
        success: function(response) {
            $(".contrast-bd").empty();
            $(".contrast-bd").append('<ul></ul>');
            var arr = ['一', '二', '三', '四', '五', '六'];
            for (i in response.data) {
                if (response.data[i].total > 0) {
                	str = '<li class="contrast-item"><table><colgroup><col width="135"><col width="170"><col width="135"></colgroup><thead><tr><th><span class="b">';
                    str += arr[i]+'等奖：<em>'+response.data[i].total+'</em> 次</span></th><th colspan="2"><div class="has-bd">';
                    if (response.data[i].max == 0) {
                        str += '--';
                    } else if ($.inArray(i, higArr[cx.Lottery.lid]) > -1) {
                    	str += '单注最高奖金'+response.data[i].max+'元';
                    } else {
                    	str += '单注固定奖金'+response.data[i].max+'元';
                    }
                    str += '<i></i></div></th></tr></thead>';
                    if (response.data[i].detail.length > 0) {
                        str += '<tfoot>';
                        if (response.data[i].total > 5 && response.data[i].detail.length == 5) {
                            str += '<tr><td colspan="3"><span>最多显示5条</span></td></tr>';
                        }
                        str += '</tfoot><tbody>';
                        $.each(response.data[i].detail, function(j, ele){
                        	str += '<tr><td>'+response.data[i].detail[j].issue+'期开奖</td><td><div class="num-group col2">';
                        	$.each(response.data[i].detail[j].awardNum.split('|')[0].split(','), function(k, e){
                        		str += '<span>'+response.data[i].detail[j].awardNum.split('|')[0].split(',')[k]+'</span>';
                          	})
                          	$.each(response.data[i].detail[j].awardNum.split('|')[1].split(','), function(k, e){
                          		str += '<span class="num-blue">'+response.data[i].detail[j].awardNum.split('|')[1].split(',')[k]+'</span>';
                          	})
                    		str += '</div></td><td>奖金：';
                    		if (cx.Lottery.lid == cx.Lid.DLT && $.inArray(i, ['3', '4', '5']) > -1) {
                        		var bonarr = [200, 10, 5];
                        		str += bonarr[i-3];
                    		}else if (response.data[i].detail[j].bonus) {
                    			str += response.data[i].detail[j].bonus;
                    		}else {
                    			str += 0;
                    		}
                    		str += '元</td></tr>';
                        })
                    }
                    str += '</tbody></table></li>';
                    $(".contrast-bd ul").append(str);
                }
            }


            if($('.pop-contrast').outerHeight() > $(window).height()){
            	$('.pop-contrast').css({
        			'top': docHeight - $('.pop-contrast').outerHeight() -40 + 'px'
        		})
            }else{
            	$('.pop-contrast').css({
            		'position': 'absolute',
        			'top': $(window).scrollTop() + $(window).height()/2 - $('.pop-contrast').outerHeight()/2 + 'px',
        			'margin-top': 0
        		})
            }
        	
        	$('.pop-mask').css({'position': 'fixed'})	
            
            hig = false;
            for (i in higArr[cx._basket_.lotteryId]) {
                if (response.data[i].total > 0) {
                    hig = true;
                    break;
                }
            }
            if (hig) {
            	$(".contrast-ft p").html("曾经有一份大奖摆在你面前，你没有好好珍惜，这次不要那么土豪，要抓紧哦！");
            } else {
            	$(".contrast-ft p").html("您所选的号码在所选期数内尚未中过大奖，快来试试手气吧！ ");
            }
            $(".btn-group").show();
            
            $(".contrast-item tfoot, .contrast-item tbody").click(function(e){
            	e.stopPropagation();
            })
            $(".bet-blue").click(function(){
                if (notbind) {
                	cx.PopAjax.bind();
                }else {
                	cx._basket_.submit($("#pd_ssq_buy"), cx._basket_);
                }
                $(".pop-contrast").remove();
            })
        }
	})
})
$(".pop-contrast").on('click', '.contrast-item', function(){
	if($(this).hasClass('contrast-item-active')) {
		$(".pop-contrast .contrast-item").removeClass('contrast-item-active');
	} else {
		$(".pop-contrast .contrast-item").removeClass('contrast-item-active');
		$(this).addClass('contrast-item-active');
	}
	$('.pop-mask').css({'position': 'fixed'})
})
</script>