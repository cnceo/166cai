<link rel="stylesheet" href="/caipiaoimg/v1.1/styles/detail.min.css">
<div class="wrap_in">
<?php if (!empty($matchs)) {?>
    <div class="injury-mod">
        <div class="injury-mod-hd">
            <h1><i class="against-team"></i>对阵球队伤病查询</h1>
        </div>
        <div class="injury-mod-bd">
            <div class="injury-tab">
                <div class="injury-tab-day">
                    <a class="tab-day tab-day-l disable-day" href="javascript:;">
                        <span>上一天</span>
                        <i class="icon-font">&#xe623;</i>
                    </a>
                    <?php $i = 0;
                    foreach (array_keys($matchs) as $day) {?>
                    <div class="today" <?php if ($i != 0) {?>style="display:none"<?php }?>>
                    	<?php echo substr($day, 0, 2)?>/<?php echo substr($day, 2, 2)?>
                    </div>
                    <?php $i++;
					}?>
                    <a class="tab-day tab-day-r <?php if ($i == 1) {?>disable-day<?php }?>" href="javascript:;">
                        <span>下一天</span>
                        <i class="icon-font">&#xe629;</i>
                    </a>
                </div>
                <div class="injury-tab-con">
                    <a class="icon-font tab-btn-l tab-btn-dis" href="javascript:;">&#xe623;</a>
                    <a class="icon-font tab-btn-r" href="javascript:;">&#xe629;</a>
                    <div class="injury-tab-box">
                    <?php $i = 0;
                    foreach ($matchs as $day => $match) {?>
                    	<ul class="injury-tab-list" data-day="<?php echo $day?>" <?php if ($i != 0) {?>style="display:none"<?php }?>>
                    	<?php $j = 0;
                    	foreach ($match as $m => $mch) {?>
                    		<li data-hp="<?php echo $mch['hpriority']?>" data-ap="<?php echo $mch['apriority']?>" data-hz="<?php echo $mch['hzone']?>" data-az="<?php echo $mch['azone']?>" <?php if ($i == 0 && $j == 0) {?>class="cur-team"<?php }?>>
                                <a href="javascript:;">
                                    <span class="team-name">
                                        <em><?php echo $mch['awarySname']?></em>
                                        <em><?php echo $mch['homeSname']?></em>
                                    </span>
                                    <span class="team-day">
                                        <em><?php echo date('m-d', $mch['dt']/1000)?></em>
                                        <em><?php echo date('H:i', $mch['dt']/1000)?></em>
                                    </span>
                                </a>
                                <span class="cur-bs"></span>
                            </li>
						<?php $j++;
						}?>
                        </ul>
                    <?php $i++;
					}?>
                    </div> 
                </div>
            </div>
            <div class="injury-detail">
                <div class="injury-side">
                    <div class="injury-detail-side"></div>
                    <div class="injury-side-banner">
                        <a href="/jclq/hh"><img src="/caipiaoimg/v1.1/img/injury-side-banner.gif" alt=""></a>
                    </div>
                </div>
                <div class="injury-con">
                    <div class="injury-team-con"></div>
                    <div class="injury-team-con"></div>
                </div>
            </div>
        </div>
    </div>
<?php }?>
    <div class="injury-mod">
        <div class="injury-mod-hd">
            <h2><i class="all-team"></i>所有球队伤病查询</h2>
        </div>
        <div class="injury-mod-bd">
            <div class="injury-side">
                <div class="all-team-box conList">
                <?php foreach ($sqArr as $s => $sq) {?>
                	<dl class="injury-side-list con">
                        <dt><?php echo $sq?>赛区</dt>
                        <?php foreach ($nba[$s] as $p => $n) {?>
                        <dd data-p="<?php echo $p?>" data-z="<?php echo $s?>" <?php if ($s == 1 && $p == 1) {?>class="side-list-cur"<?php }?>><img class="team-logo-s" src="/caipiaoimg/v1.1/img/nba/<?php echo $n['id']?>.jpg" alt=""><?php echo $n['team']?><i class="icon-font">&#xe643;</i></dd>
                        <?php }?>
                    </dl>
                <?php }?>
                </div>
                <div class="team-btn-box">
                    <a class="team-btn team-btn-l" href="javascript:;"><i class="icon-font">&#xe623;</i></a>
                </div>
                <div class="team-btn-box team-btn-box-r">
                    <a class="team-btn team-btn-r" href="javascript:;"><i class="icon-font">&#xe629;</i></a>
                </div>
            </div>
            <div class="injury-con">
                <div class="injury-team-con"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src='<?php echo getStaticFile('/caipiaoimg/v1.1/js/slideFocus.js');?>'></script>
<script>
//球队伤病查询
//NBA伤情页tab切换
    var dayNum=0, nba = $.parseJSON('<?php echo json_encode($nba)?>'), first = $.parseJSON('<?php echo json_encode($first)?>');
    $('.tab-day-r').click(function(){
        if (!$(this).hasClass('disable-day')) {
        	if(dayNum==0){
                $(this).addClass('disable-day').siblings('.tab-day').removeClass('disable-day');
                $(this).parents('.injury-tab-day').find('.today:first').hide();
                $(this).parents('.injury-tab-day').find('.today:last').show();
                $(".injury-tab-box ul:first").hide();
                $(".injury-tab-box ul:last").show();
                $(".injury-tab-box ul:last li:first").trigger('click');
                dayNum=1;
            }
        }
    });
    $('.tab-day-l').click(function(){
    	if (!$(this).hasClass('disable-day')) {
    		if(dayNum==1){
                $(this).addClass('disable-day').siblings('.tab-day').removeClass('disable-day');
                $(this).parents('.injury-tab-day').find('.today:last').hide();
                $(this).parents('.injury-tab-day').find('.today:first').show();
                $(".injury-tab-box ul:last").hide();
                $(".injury-tab-box ul:first").show();
                $(".injury-tab-box ul:first li:first").trigger('click');
                dayNum=0;
            }
    	}
    });

    $('.injury-tab-list').on('click','li',function(){
        $(this).addClass('cur-team').siblings().removeClass('cur-team');
        render($(".injury-detail .injury-team-con:first"), $(this).data('ap'), $(this).data('az'), true);
    	render($(".injury-detail .injury-team-con:last"), $(this).data('hp'), $(this).data('hz'), true);
    	renderaside(nba[$(this).data('az')][$(this).data('ap')], nba[$(this).data('hz')][$(this).data('hp')]);
    });

    var listLiLen=$('.injury-tab-list li').length;
    var listLiW=$('.injury-tab-list li').outerWidth(true);
    var ulW=listLiLen*listLiW;
    var listLiNum=0;
    $('.injury-tab-list').width(ulW);
    if(listLiLen<=4){
        $('.injury-tab-con .icon-font').css('display','none');
    }else{
        $('.injury-tab-con .icon-font').css('display','block');
        $('.tab-btn-r').on('click',function(){
            listLiNum++;                
            if (listLiNum + 4 <= listLiLen ){
                $('.injury-tab-list').stop().animate({'left':-listLiNum*listLiW},1000);
                $('.tab-btn-l').removeClass('tab-btn-dis');
            }
            if(listLiNum + 4 >= listLiLen){
                $('.tab-btn-r').addClass('tab-btn-dis');
                listLiNum = listLiLen - 4
            }    
        });
        $('.tab-btn-l').on('click',function(){
            if(listLiNum >= 1){
                $('.injury-tab-list').stop().animate({'left':-(listLiNum-1)*listLiW},1000);
                $('.tab-btn-r').removeClass('tab-btn-dis');
                listLiNum--;
            }
            if(listLiNum == 0){
                $('.tab-btn-l').addClass('tab-btn-dis');
            }    
        })
    }
    
    $('.injury-side-list').on('click','dd',function(){
    	$('.injury-side-list dd').removeClass('side-list-cur');
        $(this).addClass('side-list-cur');
        render($(".injury-team-con:last"), $(this).data('p'), $(this).data('z'), false);
    })
$(".injury-side").slideFocusPlugin({
  arrowBtn: true,
  leftArrowBtnClass: 'team-btn-l',
  rightArrowBtnClass: 'team-btn-r',
  selectClass: "current",
  stepNum: 228,
  animateStyle:["left", ""],
  funType: "click",
  autoPlay: 0,
  callbackFun:function(prev, next){
	  $(".injury-side-list:eq("+prev+") dd:first").trigger('click');
  }
});
$(function(){
	render($(".injury-detail .injury-team-con:first"), first.apriority, first.azone, true);
	render($(".injury-detail .injury-team-con:last"), first.hpriority, first.hzone, true);
	$(".injury-side-list:first dd:first").trigger('click');
	renderaside(nba[first.azone][first.apriority], nba[first.hzone][first.hpriority]);
})

function renderaside(data1, data2) {
	str = "<div class='injury-team'><a href='/jclq/hh'><img class='team-logo-b' src='/caipiaoimg/v1.1/img/nba/"+data1.id+".jpg'></a><h4 class='team-name'>"+data1.sname+"</h4></div><div class='vs'>vs</div>";
	str += "<div class='injury-team'><a href='/jclq/hh'><img class='team-logo-b' src='/caipiaoimg/v1.1/img/nba/"+data2.id+".jpg'></a><h4 class='team-name'>"+data2.sname+"</h4></div></div>";
	$(".injury-detail-side").html(str);
}
function render(div, id, z, header) {
	var data = nba[z][id];
	str = '';
	if (header) str += "<div class='injury-con-hd'><h3>"+data.team+"</h3></div>";
	str += "<div class='injury-con-bd'><table><colgroup><col width='125'><col width='100'><col width='100'><col width='300'><col width='105'></colgroup><thead><tr><th>球员</th><th>位置</th><th>更新时间</th><th>伤情状态</th><th>影响指数</th></tr></thead><tbody>";
	
	if(data.injury) {
		$.each(data.injury, function(i, ele){
			str += "<tr><td class='f-bold'>"+ele.name+"</td><td>"+ele.position+"</td><td class='font-arial'>"+ele.updateTime+"</td><td class='c-red'>"+ele.injury+"</td><td>";
			for(j = 1; j <= 5; j++) {
				if(j <= data.injury[i].indices) {
					str += "<span class='rank-xx rank-xx-y'></span>";
				}else {
					str += "<span class='rank-xx rank-xx-n'></span>";
				}
			}
			str += "</td></tr>";
		})
		div.removeClass('no-injury');
	}else {
		str += '<tr><td colspan="5">全员健康</td></tr>';
		div.addClass('no-injury');
	}
	
	str += "</tbody></table></div>";
	div.html(str);
}
</script>