<?php
$url = $lotteryId == JCZQ ? '/jczq' : '/jclq';
?>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/detail.min.css'); ?>"/>
<div class="wrap_in">
    <?php include ('breadcrumb.php5')?>
    <div class="detail-container clearfix">
        <div class="detail-container-l">
            <div class="article">
                <h1 class="article-title"><?php echo $result['title']; ?></h1>
				<div class="article-source"><i><?php echo substr($result['show_time'], 0, 16); ?></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>来源：<?php echo empty($result['submitter_id']) ? '转载' : '166彩票'?></i>
				</div>
				<?php echo htmlspecialchars_decode($result['content']); ?>
                <?php include('share.php5') ?>
            </div>
            <div class="compet-tz"  data-count='1'>
                <?php if ($allHotCount): ?>
                    <div class="tz-md-hd clearfix">
                        <h3>竞彩<?php echo $lotteryId == JCZQ ? '足球': '篮球'?>热门比赛</h3>
                        <a href="<?php echo $url ?>" target="_blank" class="more-game">更多比赛></a>
                    </div>
                    <?php foreach ($hotMatches as $match): ?>
                        <div class="tz-md-bd">
                            <div class="mod-bet-hd"></div>
                            <div class="mod-bet-bd <?php echo $lotteryId == JCZQ ? 'jczq': 'jclq'?>"></div>
                            <div class="mod-bet-ft clearfix">
                                <div class="mod-bet-ft-l">
                                    <div class="multi-modifier-s">
                                        <a href="javascript:;" class="minus">-</a>
                                        <label><input class="multi number" type="text" value="1"
                                                      autocomplete="off"></label>
                                        <a href="javascript:;" class="plus" data-max="100000">+</a>
                                    </div>
                                    <span>共 <em>2</em> 元</span><span>预计奖金：<em></em>元</span>
                                </div>
                                <div class="mod-bet-ft-r clearfix">
                                    <a href="javascript:;" class="btn btn-main btn-bet sumit">立即预约</a>
                                    <?php if ($allHotCount > 1): ?>
                                        <a href="javascript:;" class="change"><i class="icon-font">&#xe625;</i>换一换</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="tz-md-hd clearfix">
                        <h3>热门竞技彩</h3>
                    </div>
                    <div class="tz-md-bd hotjjc">
                        <div class="mod-bet-bd">
                            <ul class="clearfix">
                                <li class="notice-jczq">
                                    <div class="picTxt">
                                        <img src="/caipiaoimg/v1.1/img/sprite-xz-img.png?v=99"
                                             alt=""
                                             onclick="location.href='/jczq'"
                                             width="228" height="171" class="logoPic">
                                        <span class="sName">竞彩足球</span>
                                    </div>
                                    <p>返奖率最高73%，足球迷首选</p>
                                    <a href="/jczq" class="btn btn-main btn-bet" target="_blank">我要试试手</a>
                                </li>
                                <li class="notice-jclq">
                                    <div class="picTxt">
                                        <img src="/caipiaoimg/v1.1/img/sprite-xz-img.png?v=99"
                                             alt=""
                                             onclick="location.href='/jclq'"
                                             width="228" height="171" class="logoPic">
                                        <span class="sName">竞彩篮球</span>
                                    </div>
                                    <p>69%返奖率，覆盖NBA比赛</p>
                                    <a href="/jclq" class="btn btn-main btn-bet" target="_blank">我要试试手</a>
                                </li>
                                <li class="notice-sfc">
                                    <div class="picTxt">
                                        <img src="/caipiaoimg/v1.1/img/sprite-xz-img.png?v=99"
                                             alt=""
                                             onclick="location.href='/sfc'"
                                             width="228" height="171" class="logoPic">
                                        <span class="sName">胜负彩</span>
                                    </div>
                                    <p>返奖率最高73%，足球迷首选</p>
                                    <a href="/sfc" class="btn btn-main btn-bet" target="_blank">我要试试手</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($result['category_id'] < 9) {?>
            <div class="mod-links-article">
		        <p><span>上一篇：<?php if (!empty($left['id'])) {?><a href="/info/<?php echo $this->act."/".$left['id']?>"><?php echo $left['title']?></a><?php }else {echo '无'; }?></span></p>
		        <p><span>下一篇：<?php if (!empty($right['id'])) {?><a href="/info/<?php echo $this->act."/".$right['id']?>"><?php echo $right['title']?></a><?php }else {echo '无'; }?></span></p>
		    </div>
		    <div class="related-article">
		        <h4>相关阅读</h4>
		        <ul class="mod-article-list">
		        <?php foreach ($xg as $val) {?>
		        	<li><i class="dian"></i><a href="/info/<?php echo $this->act."/".$val['id']?>"><?php echo $val['title']?></a><span><?php echo $val['created']?></span></li>
		        <?php }?>
		        </ul>
		    </div>
	        <?php }?>
        </div>
        <div class="detail-side">
        <?php 
        include('jc_mod_ad.php5');
        if ($result['category_id'] < 9) {
			include('jc_mod_recomed.php5');
		}
        include('jc_ad.php5') ?>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
var matches = $.parseJSON('<?php echo json_encode($matches)?>'), hotArr = $.parseJSON('<?php echo json_encode($hot)?>'), hot = 1, midindex = 0;
var lottery = '<?php echo ($lotteryId == JCZQ) ? 'jczq' : 'jclq'?>';
var modifider = new cx.AdderSubtractor('.multi-modifier-s');
modifider.setCb(function(){
	$(".mod-bet-ft-l em:eq(-2)").html(2*parseInt(this.getValue(), 10)*$(".compet-tz").attr('data-count'));
	var max = $(".compet-tz").attr('data-max'), min = $(".compet-tz").attr('data-min');
	if (max == min) {
		$(".compet-tz").find(".mod-bet-ft-l em:last").html(treatodd(min) * parseInt(modifider.getValue(), 10) * 2 / 100);
	}else {
		$(".compet-tz").find(".mod-bet-ft-l em:last").html(treatodd(min) * parseInt(modifider.getValue(), 10) * 2 / 100+"~"+treatodd(max) * parseInt(modifider.getValue(), 10) * 2 / 100);
	}
})
$(function(){
	<?php if ( ! empty($hotMatches)){ ?>
	randseljc(matches[randmid(lottery)], $(".compet-tz"));
	<?php }?>
})
var randseljc = function(info, div){
	
	if (div.length > 0) {
		var jztime = new Date(info.jzdt);
		var time = new Date(info.dt);
		div.attr('data-mid', info.mid);
		var hstr = '<span>'+info.nameSname+'</span><span>比赛时间：'+pad(time.getMonth()+1)+'-'+pad(time.getDate())+'  '+pad(time.getHours())+':'+pad(time.getMinutes())+'</span>';
		hstr += '<span>停售时间：'+pad(jztime.getMonth()+1)+'-'+pad(jztime.getDate())+'  '+pad(jztime.getHours())+':'+pad(jztime.getMinutes());
		div.find('.mod-bet-hd').html(hstr);
		if (lottery == 'jczq') {
			var str = '<ul class="mod-match-b"><li class="mod-match-zhu" data-type="3" data-odd="'+info.spfSp3+'"><a href="javascript:;" class="selected" target="_self">';
			str += '<strong>'+info.homeSname+' 胜</strong><span>'+info.spfSp3+'</span></a></li><li data-type="1" class="mod-match-ping" data-odd="'+info.spfSp1+'"><a href="javascript:;" target="_self">';
			str += '<strong>平局</strong><span>'+info.spfSp1+'</span></a></li><li data-type="0" class="mod-match-ke" data-odd="'+info.spfSp0+'"><a href="javascript:;" target="_self"><strong>'+info.awarySname+' 胜</strong>';
			str += '<span>'+info.spfSp0+'</span></a></li></ul>';
			div.find('.mod-bet-bd').html(str);
			div.find(".mod-bet-ft-l em:last").html(treatodd(info.spfSp3) * parseInt(modifider.getValue(), 10) * 2 / 100);
			div.attr('data-max', info.spfSp3);
			div.attr('data-min', info.spfSp3);
			var odds = {3:info.spfSp3};
		}else {
			var str = '<ul class="mod-match-b"><li class="mod-match-ke" data-type="0" data-odd="'+info.rfsfHf+'"><a href="javascript:;" target="_self"><strong>'+info.awarySname+' 胜</strong>';
			str += '<span>'+info.rfsfHf+'</span></a></li><li class="mod-match-ping"></li><li class="mod-match-zhu" data-type="1" data-odd="'+info.rfsfHs+'"><a target="_self" href="javascript:;" class="selected">';
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
	randseljc(matches[randmid(lottery, $(".compet-tz"))], $(".compet-tz"), lottery);
})

$(".sumit").click(function(){
	var $item = $(this).parents('.compet-tz');
	if (lottery == 'jczq') {
		var url = lottery +  '/hh?mid=' + $item.attr('data-mid'), spf = '';
		spf += $item.find('.mod-match-zhu a').hasClass('selected') ? '3' : '';
		spf += $item.find('.mod-match-ping a').hasClass('selected') ? '1' : '';
		spf += $item.find('.mod-match-ke a').hasClass('selected') ? '0' : '';
		url += '&spf='+spf+'&multiple=' +  $item.find(".multi-modifier-s .number").val();
		location.href = url; 
	}else if (lottery == 'jclq') {
		var url = lottery +  '/hh?midp=' + $item.attr('data-mid'), rfsf = '';
		rfsf += $item.find('.mod-match-zhu a').hasClass('selected') ? '3' : '';
		rfsf += $item.find('.mod-match-ke a').hasClass('selected') ? '0' : '';
		url += '&rfsf='+rfsf+'&multiple=' + $item.find(".multi-modifier-s .number").val();
		location.href = url; 
	}
})
</script>
