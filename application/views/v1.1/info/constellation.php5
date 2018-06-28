<?php
$starNames = array(
    0  => '白羊座',
    1  => '金牛座',
    2  => '双子座',
    3  => '巨蟹座',
    4  => '狮子座',
    5  => '处女座',
    6  => '天秤座',
    7  => '天蝎座',
    8  => '射手座',
    9  => '摩羯座',
    10 => '水瓶座',
    11 => '双鱼座',
);
$starIcons = array(
    0  => '&#xe63c;',
    1  => '&#xe63e;',
    2  => '&#xe638;',
    3  => '&#xe63f;',
    4  => '&#xe642;',
    5  => '&#xe63d;',
    6  => '&#xe63a;',
    7  => '&#xe63b;',
    8  => '&#xe641;',
    9  => '&#xe640;',
    10 => '&#xe639;',
    11 => '&#xe637;',
);
?>
    <div class="ds-mod">
        <div class="ds-mod-hd clearfix">
            <h3>星座幸运投注</h3>
        </div>
        <div class="ds-mod-bd">
            <ul class="mod-tab clearfix">
            	<?php 
            	foreach ($luckyArr as $k => $lucky) {?>
            	<li <?php if ($k == 0) {?>class="current"<?php }?>><?php echo $lucky['cname']?></li>
            	<?php }?>
            </ul>
            <div class="mod-tab-con">
            	<?php foreach ($luckyArr as $k => $luckey) {?>
            		<div class="mod-tab-item mod-bd-lucky" <?php if ($k == 0) {?> style="display:block" <?php }?>>
                        <span class="">第<?php echo $luckey['info']['next']['issue'] ?>期</span>
                        <span class="arrow-tag">2元赢取1000万</span>
                        <div class="list-xz">
                            <div class="caption">
                                【<?php echo $starNames[0] ?>】今日幸运数字<span><?php echo $luckyNumbs[0] ?></span>
                            </div>
                            <div class="list-xz-bd">
                                <ul>
                                    <?php foreach ($starNames as $key => $star): ?>
                                        <li>
                                            <a href="javascript:;"
                                               class="lucky<?php echo $key == 0 ? ' current' : '' ?>"
                                               data-name="<?php echo $star ?>"
                                               data-num="<?php echo $luckyNumbs[$key] ?>"
                                               data-scheme="<?php echo $schemes[$luckey['ename']][$key] ?>"
                                            >
                                                <i class="icon-font"><?php echo $starIcons[$key] ?></i><?php echo $star ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="btn btn-main obtain-luck">
                            获取幸运号码
                            <i class="hq-icon"></i>
                        </div>
                        <div class="lucky-num clearfix">
                        <?php switch ($luckey['lid']) {
                        	case SSQ:
                        		$red = 6;
                        		for ($i = 0; $i < 6; $i++) {?>
                        		<span class="red">?</span>
                        	<?php }?>
                        		<span class="blue">?</span>
                        	<?php break;
                        	case DLT:
                        		$red = 5;
                        		for ($i = 0; $i < 5; $i++) {?>
                        		<span class="red">?</span>
                        	<?php }
                        		for ($i = 0; $i < 2; $i++) {?>
                        		<span class="blue">?</span>
                        	<?php }
                        		break;
							case QLC:
								$red = 7;
								for ($i = 0; $i < 7; $i++) {?>
								<span class="red">?</span>
							<?php }
                        	case PLS:
                        		break;?>
                        <?php }?>
                        </div>
                        <div class="btn btn-main btn-tz cast-away btn-disabled"
                             data-url="/<?php echo $luckey['ename']?>" data-scheme="" data-red="<?php echo $red?>">
                            立即预约
                        </div>
                    </div>
            	<?php }?>
            </div>
        </div>
    </div>
<script>
$(function(){
	$('body').on('click', '.lucky', function () {
	    var $this = $(this),
	        scheme = $this.data('scheme');
	    $this.closest('.list-xz').find('.caption')
	        .html('【' + $this.data('name') + '】今日幸运数字<span>' + $this.data('num')
	            + '</span>')
	        .end().find('.current').removeClass('current');
	    $this.addClass('current');
	}).on('click', '.obtain-luck', function () {
	    var $this = $(this),
	        $luckBody = $this.closest('.mod-bd-lucky');
	    $luckBody.find('.lucky').each(function () {
	        var $this = $(this);
	        if ($this.hasClass('current')) {
	            var scheme = $this.data('scheme'),
	                schemeAry = scheme.split(',');
	            $luckBody.find('.lucky-num').children().each(function () {
	                var $this = $(this),
	                    num = schemeAry.shift();
	                $this.html(num > 9 ? num : ('0' + num));
	            });
	            $luckBody.find('.cast-away').data('scheme', scheme);
	        }
	    });
	    $luckBody.find(".cast-away").removeClass('btn-disabled');
	}).on('click', '.cast-away', function () {
	    var $this = $(this),
	        url = $this.data('url'),
	        scheme = $this.data('scheme'),
	        schemeAry = scheme.split(','),
	        red = $this.data('red'),
	        castAry = [],
	        cast;
	    if (!$this.hasClass('btn-disabled')) {
	        for (var i = 0; i < red; i++) {
	            castAry.push(schemeAry.shift());
	        }
	        cast = castAry.join(',');
	        if (schemeAry.length > 0) {
	            cast = cast + '|' + schemeAry.join(',');
	        }
	        cast += ':1:1';
	        location.href = url + '?codes=' + encodeURIComponent(cast);
	    }
	});
})


</script>