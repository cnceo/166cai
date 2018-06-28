<div class="mod mod-compet">
        <div class="ds-mod-hd clearfix">
          <h3>
          <?php if (empty($matches)) {
          	echo '热门竞技彩';
          }else {
          	echo '竞彩'.($lotteryId == JCZQ ? '足球' : '篮球').'热门比赛';
          }?>
         </h3>
          <a href="<?php echo $lotteryId == JCZQ ? 'jczq' : 'jclq'?>/hh" class="more">更多比赛></a>
        </div>
        <div class="tz-md-bd" data-count="1">
          <div class="mod-bet-hd"></div>
          <div class="mod-bet-bd <?php if (!empty($hotMatches)) { echo $lotteryId == JCZQ ? 'jczq' : 'jclq'; } else {echo 'hotjjc';}?>">
          	<ul class="clearfix">
              <li class="notice-jczq">
                <div class="picTxt">
                  <img src="/caipiaoimg/v1.1/img/sprite-xz-img.png?v=99" alt="" width="228" height="171" class="logoPic">
                  <span class="sName">竞彩足球</span>
                </div>        
                <p>返奖率最高73%，足球迷首选</p>
                <a href="/jczq/hh" class="btn btn-main btn-bet">我要试试手</a>
              </li>
              <li class="notice-jclq">
                <div class="picTxt">
                  <img src="/caipiaoimg/v1.1/img/sprite-xz-img.png?v=99" alt="" width="228" height="171" class="logoPic">
                  <span class="sName">竞彩篮球</span>
                </div>        
                <p>69%返奖率，覆盖NBA比赛</p>
                <a href="/jclq/hh" class="btn btn-main btn-bet">我要试试手</a>
              </li>
            </ul>
          </div>
          <?php if (!empty($hotMatches)) {?>
          <div class="mod-bet-ft clearfix">
            <div class="mod-bet-ft-l">
              <div class="multi-modifier-s">
                <a href="javascript:;" class="minus">-</a>
                <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                <a href="javascript:;" class="plus" data-max="100000">+</a>
              </div> 倍<br>
              <span>共 <em>2</em> 元</span>&nbsp;&nbsp;&nbsp;&nbsp;<span>预计奖金：<em></em>元</span>
            </div>
            <div class="mod-bet-ft-r clearfix">
              <a href="javascript:;" class="btn btn-main btn-bet sumit">立即预约</a>
              <a href="javascript:;" class="change"><i class="icon-font">&#xe625;</i>换一换</a>
            </div>
          </div>
          <?php }?>
        </div>
      </div>
      
<script type="text/javascript">
var matches = $.parseJSON('<?php echo json_encode($matches)?>'), hotArr = $.parseJSON('<?php echo json_encode($hot)?>'), hot = 1, midindex = 0;
var lottery = '<?php echo ($lotteryId == JCZQ) ? 'jczq' : 'jclq'?>';
var modifider = new cx.AdderSubtractor('.multi-modifier-s');
modifider.setCb(function(){
	$(".mod-bet-ft-l em:eq(-2)").html(2*parseInt(this.getValue(), 10)*$(".tz-md-bd").attr('data-count'));
	var max = $(".tz-md-bd").attr('data-max'), min = $(".tz-md-bd").attr('data-min');
	if (max == min) {
		$(".tz-md-bd").find(".mod-bet-ft-l em:last").html(treatodd(min) * parseInt(modifider.getValue(), 10) * 2 / 100);
	}else {
		$(".tz-md-bd").find(".mod-bet-ft-l em:last").html(treatodd(min) * parseInt(modifider.getValue(), 10) * 2 / 100+"~"+treatodd(max) * parseInt(modifider.getValue(), 10) * 2 / 100);
	}
})
$(function(){
	<?php if (!empty($hotMatches)) {?>
	randseljc(matches[randmid(lottery)], $(".tz-md-bd"));
	<?php }?>
})
var randseljc = function(info, div){
	if (div.length > 0) {
		var jztime = new Date(info.jzdt);
		var time = new Date(info.dt);
		div.attr('data-mid', info.mid);
		var hstr = '<span class="team-name">'+info.nameSname+'</span><span>停售时间：'+pad(jztime.getMonth()+1)+'-'+pad(jztime.getDate())+'  '+pad(jztime.getHours())+':'+pad(jztime.getMinutes());
		div.find('.mod-bet-hd').html(hstr);
		if (lottery == 'jczq') {
			var str = '<ul class="mod-match-b"><li class="mod-match-zhu" data-type="3" data-odd="'+info.spfSp3+'"><a href="javascript:;" class="selected" target="_self">';
			str += '<strong>'+info.homeSname+' 胜</strong><span>'+info.spfSp3+'</span></a></li><li data-type="1" class="mod-match-ping" data-odd="'+info.spfSp1+'">';
			str += '<a href="javascript:;" target="_self"><strong>平局</strong><span>'+info.spfSp1+'</span></a></li><li data-type="0" class="mod-match-ke" data-odd="'+info.spfSp0+'">';
			str += '<a href="javascript:;" target="_self"><strong>'+info.awarySname+' 胜</strong><span>'+info.spfSp0+'</span></a></li></ul>';
			div.find('.mod-bet-bd').html(str);
			div.find(".mod-bet-ft-l em:last").html(treatodd(info.spfSp3) * parseInt(modifider.getValue(), 10) * 2 / 100);
			div.attr('data-max', info.spfSp3);
			div.attr('data-min', info.spfSp3);
			var odds = {3:info.spfSp3};
		}else {
			var str = '<ul class="mod-match-b"><li data-type="0" data-odd="'+info.rfsfHf+'"><a href="javascript:;" target="_self"><strong>'+info.awarySname+' 胜</strong>';
			str += '<span>'+info.rfsfHf+'</span></a></li><li data-type="1" data-odd="'+info.rfsfHs+'"><a target="_self" href="javascript:;" class="selected">';
			str += '<strong>'+info.homeSname+info.let+' 胜</strong><span>'+info.rfsfHs+'</span></a></li></ul>';
			div.find('.mod-bet-bd').html(str);
			div.find(".mod-bet-ft-l em:last").html(treatodd(info.rfsfHs) * parseInt(modifider.getValue(), 10) * 2 / 100);
			div.attr('data-max', info.rfsfHs);
			div.attr('data-min', info.rfsfHs);
			var odds = {1:info.rfsfHs};
		}
		div.find("li").click(function(){
			tretodd($(this), odds, div)
		})
	}
	
}
function randmid(lottery, div){
	if (!div) {
		if (hotArr[hot] === undefined || hotArr[hot][midindex] === undefined) {
			if (hot < 9) {
				hot++;
			} else {
				hot = 0;
			}
			midindex = 0;
			return randmid(lottery);
		}
		mid = hotArr[hot][midindex];
		midindex++;
	}else {
		h = parseInt(div.attr('data-hot'));
		m = parseInt(div.attr('data-midindex'));
		if (hotArr[h] === undefined || hotArr[h][m] === undefined) {
			if (h < 9) {
				h++;
			} else {
				h = 0;
			}
			m = 0;
			div.attr('data-hot', h);
			div.attr('data-midindex', m);
			return randmid(lottery, div);
		}
		mid = hotArr[h][m];
		m++;
		div.attr('data-midindex', m);
	}
	return mid;
}

function pad(i) {
    i = '' + i;
    if (i.length < 2) {
        i = '0' + i;
    }
    return i;
}

function treatodd(odd){
	return parseInt(odd.replace(/\./, ''), 10);
}

var tretodd = function(self, odds, div){
	self.find('a').toggleClass('selected');
	if(self.find('a').hasClass('selected')) {
		odds[self.attr('data-type')] = self.attr('data-odd');
	}else {
		delete odds[self.attr('data-type')];
	}
	var max = '0', min ='0', count = 0;
	for(i in odds) {
		count++;
		if (max == '0' || treatodd(odds[i]) > treatodd(max)) {
			max = odds[i];
		}
		if (min == '0' || treatodd(odds[i]) < treatodd(min)) {
			min = odds[i];
		}
	}
	div.find(".mod-bet-ft-l em:eq(-2)").html(2*parseInt(modifider.getValue() * count, 10));
	div.attr('data-max', max);
	div.attr('data-min', min);
	div.attr('data-count', count);
	if (max == min) {
		div.find(".mod-bet-ft-l em:last").html(treatodd(min) * parseInt(modifider.getValue(), 10) * 2 / 100);
	}else {
		div.find(".mod-bet-ft-l em:last").html(treatodd(min) * parseInt(modifider.getValue(), 10) * 2 / 100+"~"+treatodd(max) * parseInt(modifider.getValue(), 10) * 2 / 100);
	}
}

$(".change").click(function(){
	randseljc(matches[randmid(lottery, $(".tz-md-bd"))], $(".tz-md-bd"), lottery);
})

$(".sumit").click(function(){
	var $item = $(this).parents('.tz-md-bd');

	if (lottery == 'jczq') {
		var url = lottery +  '/hh?mid=' + $item.attr('data-mid'), spf = '';
		spf += $item.find('.mod-match-zhu a').hasClass('selected') ? '3' : '';
		spf += $item.find('.mod-match-ping a').hasClass('selected') ? '1' : '';
		spf += $item.find('.mod-match-ke a').hasClass('selected') ? '0' : '';
		url += '&spf='+spf+'&multiple=' +  $item.find(".multi-modifier-s .number").val();
		location.href = url; 
	}else if (lottery == 'jclq') {
		var url = lottery +  '/hh?midp=' + $item.attr('data-mid'), rfsf = '';
		rfsf += $item.find('.mod-match-b li:last a').hasClass('selected') ? '3' : '';
		rfsf += $item.find('.mod-match-b li:first a').hasClass('selected') ? '0' : '';
		url += '&rfsf='+rfsf+'&multiple=' + $item.find(".multi-modifier-s .number").val();
		location.href = url; 
	}
})
</script>