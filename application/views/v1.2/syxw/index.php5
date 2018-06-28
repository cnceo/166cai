<?php 
if(!$lotteryConfig[SYXW]['status']) {
	$selling = 0;
}else {
	$selling = 2;
	if ($lotteryConfig['united_status']) {
		$hmselling = 1;
	}
}?>
<script>
var ISSUE = "<?php echo $info['cIssue']['seExpect']?>", ENDTIME = "<?php echo date('Y-m-d H:i:s', $info['cIssue']['seFsendtime']/1000)?>", MULTI = <?php echo (int)$multi?> || 1,
endtime = "<?php echo $info['cIssue']['seFsendtime'] ?>", hsty = eval(<?php echo json_encode($history)?>), mall = eval(<?php echo json_encode($mall)?>),
tm = <?php echo $info['cIssue']['seFsendtime']/1000-time();?>, atm = <?php echo $info['nlIssue']['awardTime']/1000-time();?>, vJson = [<?php echo $awardNum?>],
chases = $.parseJSON('<?php echo json_encode($chases)?>'), chaselength = <?php echo $chaselength?>, enName = '<?php echo $enName?>', selling = <?php echo $selling?>
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/order.min.js');?>"></script>
<!--[if lt IE 9]><script type="text/javascript" src="../../caipiaoimg/v1.1/js/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.2/js/gaopin.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.2/js/syxw.min.js');?>"></script>

<div class="bet-syxw bet-sdsyxw">
    <div class="wrap cp-box bet-num">
    	<?php $this->load->view('v1.2/elements/lottery/info_panel', array('noIssue' => true)); ?>
        <div class="cp-box-bd bet">
            <div class="bet-main">
                <div class="bet-syxw-hd">
                    <ul class="bet-type-link">
                    <?php foreach ($typeMAP as $k => $type) {?>
                    	<li data-type="<?php echo $k?>" <?php if ($k == 'rx8'){?>class="selected"<?php }elseif ($k == 'lexuan5') {?>class="last"<?php }?>><a href="javascript:;"><?php echo $type['cnName']?><span>
                    	<?php if ($k == 'rx2') {echo '<u>易</u>';} elseif ($k == 'rx8') {echo '<u>热</u>'; } elseif ($k == 'qzhi3') {echo '<u>高</u>'; }elseif ($k == 'lexuan5') {echo '<u>乐选玩法</u>'; } echo $type['bonus']?></span></a></li>
                    <?php }?>
                    </ul>
                </div>
                <div class="ykj-info">
                    <table>
                        <colgroup><col width="160"><col width="506"><col width="152"><col width="60"><col width="60"><col width="60"></colgroup>
                        <thead>
                            <tr>
                               <th>期次</th>
                                <th class="column-num"><div class="ball-group-s"><span>01</span><span>02</span><span>03</span><span>04</span><span>05</span><span>06</span><span>07</span><span>08</span><span>09</span><span>10</span><span>11</span></div></th>
                                <th>开奖号码</th>
                                <th>和值</th>
                                <th>大小比</th>
                                <th>奇偶比</th> 
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($history as $k => $h) {?>
                            <tr>
                            	<td><?php echo $h['issue']?></td>
                               	<?php if (empty($h['awardNum'])) {?>
	                               	<td>正在开奖中...</td><td><span class="main-color-s">--</span></td><td>--</td><td>--</td><td>--</td> 
                        		<?php } else {
								$awardNum = explode(',', $h['awardNum']);?>
									<td>
	                                    <div class="ball-group-s">
	                                    <?php 
	                                    $i = 0;
	                                    foreach (explode(',', $mall[$h['issue']][0]) as $n) {
										$i++;
										if (in_array($i, $awardNum)){?>
	                                    	<span class="selected"><?php echo $i?></span>
	                                    <?php } else {?>
	                                    	<span><?php echo $n?></span>
										<?php }
										}?>
	                                    </div>
	                                </td>
	                                <td>
	                                    <div class="num-group"><?php foreach ($awardNum as $aw) {?><span><?php echo $aw?></span><?php }?></div>
	                                </td>
	                                <td><?php echo $h['he']?></td>
	                                <td><?php echo $h['dx']?></td>
	                                <td><?php echo $h['jo']?></td>
                        		<?php }?>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                    <div class="canvas-mask"></div>
                    <div class="ykj-info-mask"></div>
                    <div class="ykj-info-action">近期走势<i class="arrow"></i></div>
                </div>
                <div class="bet-syxw-bd">
                    <div class="bet-type-link-bd">
                    <?php for ($r = 2; $r <= 8; $r++){?>
                    	<div class="bet-type-link-item rx<?php echo $r?>" <?php if ($r !== 8) { ?>style="display: none"<?php }?>>
                            <ul class="tab-list-hd bet-type-tab-hd">
                            <?php if ($r !== 8) {?>
                            	<li><input type="radio" id="rx<?php echo $r?>" name="rx<?php echo $r?>BetType" checked><label for="rx<?php echo $r?>">普通投注</label></li>
                                <li><input type="radio" id="rx<?php echo $r?>dt" name="rx<?php echo $r?>BetType"><label for="rx<?php echo $r?>dt">胆拖投注</label></li>
							<?php }?>
                            </ul>
                            <div class="bet-type-tab-bd">
                                <div class="bet-pick-area default">
                                    <div class="pick-area-box">
                                        <p class="pick-area-explain"><i class="icon-font"></i><?php echo $typeMAP['rx'.$r]['rule'][0]?></p>
                                        <div class="pick-area ball-box-1">
                                            <div class="pick-area-time"></div>
                                            <style>
                                                /*暂时应付春节期间11选5停售*/
                                                .bet-syxw-bd .pick-area-time span {
                                                  font-size: 18px;
                                                }
                                            </style>
                                            <div class="pick-area-red pre-box">      
                                            	<div class="pick-area-tips">
                                                    <span class="choose-tip">选号<i class="arrow"></i></span>
                                                    <div class="mod-tips">遗漏
                                                        <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                                    </div>
                                                </div>    
                                                <ol class="pick-area-ball balls">
                                                    <?php 
	                                                $i = 0;
	                                                $ms0 = explode(',', $miss[0]);
	                                                foreach ($ms0 as $m) {
														$i++;?>
	                                                	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($ms0)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
	                                                <?php }?>
                                                </ol>
                                                <div class="pick-area-select">
                                                    <a href="javascript:;" class="filter-all">全</a><a href="javascript:;" class="filter-bigs">大</a><a href="javascript:;" class="filter-smalls">小</a><a href="javascript:;" class="filter-odds">奇</a><a href="javascript:;" class="filter-evens">偶</a><a href="javascript:;" class="clear-balls">清空</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--添加到投注列表-->
                                    <div class="bet-solutions box-collection">
                                        <p><span>您已选中了 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span><span class="sub-txt1" style="display: none">（如中奖，奖金 <span class="main-color-s">0</span> 元，盈利 <span class="main-color-s">0</span> 元）</span></p>
                                        <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font"></i></a></div>
                                    </div>
                                </div>
                                <?php if ($r !== 8) {?>
                                <div class="bet-pick-area dt" style="display: none">
                                    <div class="pick-area-box">
                                        <p class="pick-area-explain"><i class="icon-font"></i><?php echo $typeMAP['rx'.$r]['rule'][1]?></p>
                                        <div class="pick-area ball-box-1">
                                            <div class="pick-area-time"></div>
                                            <div class="pick-area-red pre-box">
                                            	<div class="pick-area-tips">
                                                    <span class="choose-tip">选号<i class="arrow"></i></span>
                                                    <div class="mod-tips">遗漏
                                                        <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                                    </div>
                                                </div> 
                                                <div class="pick-area-hd">
                                                    <em>胆码区</em>您认为必出的号码（<i class="main-color-s">选择1<?php if ($r !== 2) echo "-".($r-1)?>个</i>）
                                                </div>        
                                                <ol class="pick-area-ball balls">
                                                    <?php 
	                                                $i = 0;
	                                                foreach ($ms0 as $m) {
														$i++;?>
	                                                	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($ms0)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
	                                                <?php }?>
                                                </ol>
                                            </div>
                                        </div> 

                                        <div class="pick-area ball-box-1">
                                            <div class="pick-area-red pre-box">
                                                <div class="pick-area-hd">
                                                    <em>拖码区</em>您认为可能的号码（<i class="main-color-s">至少选择2个</i>）
                                                </div>        
                                                <ol class="pick-area-ball balls">
                                                    <?php 
	                                                $i = 0;
	                                                foreach ($ms0 as $m) {
														$i++;?>
	                                                	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($ms0)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
	                                                <?php }?>
                                                </ol>
                                                <div class="pick-all-box">
                                                    <a href="javascript:;" class="filter-all">拖码全包</a>
                                                    <a href="javascript:;" class="clear-balls">清空</a>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <!-- 数字彩投注区 end -->

                                    <!--添加到投注列表-->
                                    <div class="bet-solutions box-collection">
                                        <p><span>您已选中了 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span><span class="sub-txt1" style="display: none">（如中奖，奖金 <span class="main-color-s">0</span> 元，盈利 <span class="main-color-s">0</span> 元）</span></p>
                                        <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font"></i></a></div>
                                    </div>
                                </div>
								<?php }?>
                            </div>
                        </div>
                    <?php }?>
                        <div class="bet-type-link-item q1" style="display: none">
                            <ul class="tab-list-hd bet-type-tab-hd"></ul>
                            <div class="bet-type-tab-bd">
                                <div class="bet-pick-area default">
                                    <div class="pick-area-box">
                                        <p class="pick-area-explain"><i class="icon-font"></i><?php echo $typeMAP['q1']['rule'][0]?></p>
                                        <div class="pick-area ball-box-1">
                                            <div class="pick-area-time"></div>
                                            <div class="pick-area-red pre-box">  
                                            	<div class="pick-area-tips">
                                                    <span class="choose-tip">第一位<i class="arrow"></i></span>
                                                    <div class="mod-tips">遗漏
                                                        <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                                    </div>
                                                </div>         
                                                <ol class="pick-area-ball balls">
                                                    <?php 
	                                                $i = 0;
	                                                $ms1 = explode(',', $miss[1]);
	                                                foreach ($ms1 as $m) {
														$i++;?>
	                                                	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($ms1)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
	                                                <?php }?>
                                                </ol>
                                                <div class="pick-area-select">
                                                    <a href="javascript:;" class="filter-all">全</a><a href="javascript:;" class="filter-bigs">大</a><a href="javascript:;" class="filter-smalls">小</a><a href="javascript:;" class="filter-odds">奇</a><a href="javascript:;" class="filter-evens">偶</a><a href="javascript:;" class="clear-balls">清空</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--添加到投注列表-->
                                    <div class="bet-solutions box-collection">
                                        <p><span>您已选中了 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span><span class="sub-txt1" style="display: none">（如中奖，奖金 <span class="main-color-s">0</span> 元，盈利 <span class="main-color-s">0</span> 元）</span></p>
                                        <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php for ($r = 2; $r <= 3; $r++) {?>
                        <div class="bet-type-link-item qzu<?php echo $r?>" style="display: none">
                            <ul class="tab-list-hd bet-type-tab-hd">
                                <li><input type="radio" id="qzu<?php echo $r?>" name="qzu<?php echo $r?>BetType" checked><label for="qzu<?php echo $r?>">普通投注</label></li>
                                <li><input type="radio" id="qzu<?php echo $r?>dt" name="qzu<?php echo $r?>BetType"><label for="qzu<?php echo $r?>dt">胆拖投注</label></li>
                            </ul>
                            <div class="bet-type-tab-bd">
                                <div class="bet-pick-area default">
                                    <div class="pick-area-box">
                                        <p class="pick-area-explain"><i class="icon-font"></i><?php echo $typeMAP['qzu'.$r]['rule'][0]?></p>
                                        <div class="pick-area ball-box-1">
                                            <div class="pick-area-time"></div>
                                            <div class="pick-area-red pre-box">     
                                            	<div class="pick-area-tips">
                                                    <span class="choose-tip">选号<i class="arrow"></i></span>
                                                    <div class="mod-tips">遗漏
                                                        <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                                    </div>
                                                </div>      
                                                <ol class="pick-area-ball balls">
                                                    <?php 
	                                                $i = 0;
	                                                $mstr = 'ms'.($r+2);
	                                                $$mstr = explode(',', $miss[$r+2]);
	                                                foreach ($$mstr as $m) {
														$i++;?>
	                                                	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($$mstr)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
	                                                <?php }?>
                                                </ol>
                                                <div class="pick-area-select">
                                                    <a href="javascript:;" class="filter-all">全</a><a href="javascript:;" class="filter-bigs">大</a><a href="javascript:;" class="filter-smalls">小</a><a href="javascript:;" class="filter-odds">奇</a><a href="javascript:;" class="filter-evens">偶</a><a href="javascript:;" class="clear-balls">清空</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--添加到投注列表-->
                                    <div class="bet-solutions box-collection">
                                        <p><span>您已选中了 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span><span class="sub-txt1" style="display: none">（如中奖，奖金 <span class="main-color-s">0</span> 元，盈利 <span class="main-color-s">0</span> 元）</span></p>
                                        <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font"></i></a></div>
                                    </div>
                                </div>

                                <div class="bet-pick-area dt" style="display: none">
                                    <div class="pick-area-box">
                                        <p class="pick-area-explain"><i class="icon-font"></i><?php echo $typeMAP['qzu'.$r]['rule'][1]?></p>
                                        <div class="pick-area ball-box-1">
                                            <div class="pick-area-time"></div>
                                            <div class="pick-area-red pre-box">
                                            	<div class="pick-area-tips">
                                                    <span class="choose-tip">选号<i class="arrow"></i></span>
                                                    <div class="mod-tips">遗漏
                                                        <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                                    </div>
                                                </div> 
                                                <div class="pick-area-hd">
                                                    <em>胆码区</em>您认为必出的号码（<i class="main-color-s">选择1<?php if ($r !== 2) echo "-".($r-1)?>个</i>）
                                                </div>        
                                                <ol class="pick-area-ball balls">
                                                    <?php 
	                                                $i = 0;
	                                                foreach ($$mstr as $m) {
														$i++;?>
	                                                	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($$mstr)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
	                                                <?php }?>
                                                </ol>
                                            </div>
                                        </div> 
                                        <div class="pick-area ball-box-1">
                                            <div class="pick-area-red pre-box">
                                                <div class="pick-area-hd">
                                                    <em>拖码区</em>您认为可能的号码（<i class="main-color-s">至少选择2个</i>）
                                                </div>        
                                                <ol class="pick-area-ball balls">
                                                    <?php 
	                                                $i = 0;
	                                                foreach ($$mstr as $m) {
														$i++;?>
	                                                	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($$mstr)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
	                                                <?php }?>
                                                </ol>
                                                <div class="pick-all-box">
                                                    <a href="javascript:;" class="filter-all">拖码全包</a>
                                                    <a href="javascript:;" class="clear-balls">清空</a>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <!-- 数字彩投注区 end -->

                                    <!--添加到投注列表-->
                                    <div class="bet-solutions box-collection">
                                        <p><span>您已选中了 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span><span class="sub-txt1" style="display: none">（如中奖，奖金 <span class="main-color-s">0</span> 元，盈利 <span class="main-color-s">0</span> 元）</span></p>
                                        <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bet-type-link-item qzhi<?php echo $r?>" style="display: none">
                            <ul class="tab-list-hd bet-type-tab-hd"></ul>
                            <div class="bet-type-tab-bd">
                                <div class="bet-pick-area default">
                                    <div class="pick-area-box">
                                        <p class="pick-area-explain"><i class="icon-font"></i><?php echo $typeMAP['qzhi'.$r]['rule'][0]?></p>
                                        <?php for ($k = 1; $k <= $r; $k++) {?>
                                        	<div class="pick-area ball-box-<?php echo $k?>">
                                        	<?php if ($k == 1) {?>
                                        		<div class="pick-area-time"></div>
											<?php }?>
	                                            <div class="pick-area-red pre-box">   
	                                            <?php if ($k == 1) {?>
	                                            	<div class="pick-area-tips">
	                                                    <span class="choose-tip">第一位<i class="arrow"></i></span>
	                                                    <div class="mod-tips">遗漏
	                                                        <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
	                                                    </div>
	                                                </div>   
	                                            <?php }else {
	                                            	$arr = array(2 => '二', 3 => '三')?> 
	                                            	<div class="pick-area-tips">
	                                            		<span class="choose-tip">第<?php echo $arr[$k]?>位<i class="arrow"></i></span>
	                                            	</div>
	                                            <?php }?>     
	                                                <ol class="pick-area-ball balls">
	                                                    <?php 
		                                                $i = 0;
		                                                $mstr = "ms".$k;
		                                                $$mstr = explode(',', $miss[$k]);
		                                                foreach ($$mstr as $m) {
															$i++;?>
		                                                	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($$mstr)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
		                                                <?php }?>
	                                                </ol>
	                                                <div class="pick-area-select">
	                                                    <a href="javascript:;" class="filter-all">全</a><a href="javascript:;" class="filter-bigs">大</a><a href="javascript:;" class="filter-smalls">小</a><a href="javascript:;" class="filter-odds">奇</a><a href="javascript:;" class="filter-evens">偶</a><a href="javascript:;" class="clear-balls">清空</a>
	                                                </div>
	                                            </div>
	                                        </div>
										<?php }?>
                                    </div>
                                    <!--添加到投注列表-->
                                    <div class="bet-solutions box-collection">
                                        <p><span>您已选中了 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span><span class="sub-txt1" style="display: none">（如中奖，奖金 <span class="main-color-s">0</span> 元，盈利 <span class="main-color-s">0</span> 元）</span></p>
                                        <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }?>
                        <div class="bet-type-link-item lexuan" style="display: none">
                        	<ul class="tab-list-hd bet-type-tab-hd">
                            	<li><input type="radio" id="lexuan3" name="lexuanBetType"><label for="lexuan3">乐选三</label></li>
                                <li><input type="radio" id="lexuan4" name="lexuanBetType"><label for="lexuan4">乐选四</label></li>
                                <li><input type="radio" id="lexuan5" name="lexuanBetType" checked><label for="lexuan5">乐选五</label></li>
                            </ul>
                            <div class="bet-type-tab-bd">
                                <div class="bet-pick-area lexuan3" style="display: none">
                                    <div class="pick-area-box">
                                        <p class="pick-area-explain"><i class="icon-font"></i><?php echo $typeMAP['lexuan5']['rule'][0]?></p>
                                        <?php for ($k = 1; $k <= 3; $k++) {?>
                                        	<div class="pick-area ball-box-<?php echo $k?>">
                                        	<?php if ($k == 1) {?>
                                        		<div class="pick-area-time"></div>
											<?php }?>
	                                            <div class="pick-area-red pre-box">   
	                                            <?php if ($k == 1) {?>
	                                            	<div class="pick-area-tips">
	                                                    <span class="choose-tip">第一位<i class="arrow"></i></span>
	                                                    <div class="mod-tips">遗漏
	                                                        <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
	                                                    </div>
	                                                </div>   
	                                            <?php }else {
	                                            	$arr = array(2 => '二', 3 => '三')?> 
	                                            	<div class="pick-area-tips">
	                                            		<span class="choose-tip">第<?php echo $arr[$k]?>位<i class="arrow"></i></span>
	                                            	</div>
	                                            <?php }?>     
	                                                <ol class="pick-area-ball balls">
	                                                    <?php 
		                                                $i = 0;
		                                                $mstr = "ms".$k;
		                                                $$mstr = explode(',', $miss[$k]);
		                                                foreach ($$mstr as $m) {
															$i++;?>
		                                                	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($$mstr)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
		                                                <?php }?>
	                                                </ol>
	                                                <div class="pick-area-select">
	                                                    <a href="javascript:;" class="filter-all">全</a><a href="javascript:;" class="filter-bigs">大</a><a href="javascript:;" class="filter-smalls">小</a><a href="javascript:;" class="filter-odds">奇</a><a href="javascript:;" class="filter-evens">偶</a><a href="javascript:;" class="clear-balls">清空</a>
	                                                </div>
	                                            </div>
	                                        </div>
										<?php }?>
                                    </div>
                                    <!--添加到投注列表-->
                                    <div class="bet-solutions box-collection">
                                        <p><span>您已选中了 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span><span class="sub-txt1" style="display: none">（如中奖，奖金 <span class="main-color-s">0</span> 元，盈利 <span class="main-color-s">0</span> 元）</span></p>
                                        <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font"></i></a></div>
                                    </div>
                                </div> 
                                <?php for ($r = 4; $r <= 5; $r++) {?>
                                <div class="bet-pick-area lexuan<?php echo $r?>" <?php if ($r == 4) {?>style="display: none"<?php }?>>
                                    <div class="pick-area-box">
                                        <p class="pick-area-explain"><i class="icon-font"></i><?php echo $typeMAP['lexuan5']['rule'][$r-3]?></p>
                                        <div class="pick-area ball-box-1">
                                            <div class="pick-area-time"></div>
                                            <div class="pick-area-red pre-box">      
                                            	<div class="pick-area-tips">
                                                    <span class="choose-tip">选号<i class="arrow"></i></span>
                                                    <div class="mod-tips">遗漏
                                                        <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                                    </div>
                                                </div>    
                                                <ol class="pick-area-ball balls">
                                                    <?php 
	                                                $i = 0;
	                                                $ms0 = explode(',', $miss[0]);
	                                                foreach ($ms0 as $m) {
														$i++;?>
	                                                	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($ms0)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
	                                                <?php }?>
                                                </ol>
                                                <div class="pick-area-select">
                                                    <a href="javascript:;" class="filter-all">全</a><a href="javascript:;" class="filter-bigs">大</a><a href="javascript:;" class="filter-smalls">小</a><a href="javascript:;" class="filter-odds">奇</a><a href="javascript:;" class="filter-evens">偶</a><a href="javascript:;" class="clear-balls">清空</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--添加到投注列表-->
                                    <div class="bet-solutions box-collection">
                                        <p><span>您已选中了 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span><span class="sub-txt1" style="display: none">（如中奖，奖金 <span class="main-color-s">0</span> 元，盈利 <span class="main-color-s">0</span> 元）</span></p>
                                        <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font"></i></a></div>
                                    </div>
                                </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                    <div class="cast-basket">
                        <!--投注列表-->
                        <div class="bet-area">
                            <div class="bet-area-box">
                                <div class="bet-area-box-bd">
                                    <div class="inner">
                                        <ul class="cast-list"></ul>
                                    </div>
                                </div>
                            </div>
                            <div class="bet-area-qbtn">
                                <a class="btn-sup rand-cast" data-amount="1" href="javascript:;">机选1注</a>
                                <a class="btn-sup rand-cast" data-amount="5" href="javascript:;">机选5注</a>
                                <a class="btn-sup rand-cast" data-amount="10" href="javascript:;">机选10注</a>
                                <a class="btn-sup clear-list" href="javascript:;">清空列表</a>
                                <div class="ptips-bd ptips-bd-b">机选一注，试试手气<a href="javascript:;" class="ptips-bd-close">×</a><b></b><s></s></div>
                            </div>
                        </div>
                        <div class="bet-area-txt">
                          已选<strong class="num-multiple betNum">0</strong>注，投
                          <div class="multi-modifier">
                            <a href="javascript:;" class="minus">-</a>
                            <label><input class="multi number" type="text" value="<?php echo $multi?>" autocomplete="off"></label>
                            <a href="javascript:;" class="plus" data-max="99">+</a>
                          </div>
                          倍（最大99倍），共计<strong class="num-money betMoney">0</strong>元
                        </div>
                        
                        <!-- 追号系统 start -->
                <div class="buy-type tab-radio">
                    <div class="buy-type-hd tab-radio-hd">
                    	<div class="chase-number-notes">由购买人自行全额购买彩票，独享奖金<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>自购：</em>选好投注号码后，由自己全额<br>支付购彩款。中奖后，自己独享全部<br>税后奖金。">&#xe613;</i></span></div>
                        <em>购买方式：</em>
                        <ul>
                        	<li><label for="ordertype0"><input type="radio" id="ordertype0" name="chaseNumberTab" checked>自购</label></li>
                        	<li><label for="ordertype1" class="main-color-s"><input type="radio" id="ordertype1" name="chaseNumberTab">我要追号</label><div class="mod-tips-t ptips-bd">花小钱，追百万大奖 <i class="ptips-bd-close">×</i><b></b><s></s></div></li>
                        </ul>
                        <?php 
                        	$banner = $cpbanner['chase'][$this->con."/".$this->act];
                        	if($banner):
                        ?>
                        	<a class="dw-chasenum" target="_blank" href="<?php echo $banner['url'];?>"><img alt="追号不中包赔" src="/uploads/banner/<?php echo $banner['path'];?>"></a>
                        <?php endif;?>
                    </div>
                    <div class="buy-type-bd tab-radio-bd">
                    	<div class="tab-radio-inner hide"></div>
                    	<!-- 追号弹层start -->
                    	<div class="tab-radio-inner">
	                        <div class="chase-number-table">
	                            <table class="chase-number-table-hd">
	                                <thead>
	                                    <colgroup><col width="40"><col width="200"><col width="80"><col width="140"><col width="160"></colgroup>
	                                    <tr>
	                                        <th>序号</th>
	                                        <th class="tal">
	                                            <input checked type="checkbox">追<input type="text" value="10" data-max="<?php echo count($chases) > 87 ? 87 : count($chases)?>" class="ipt-txt follow-issue">期<s>（共87期）</s>
	                                        </th>
	                                        <th><input type="text" class="ipt-txt follow-multi" data-max="99" value="<?php echo $multi?>">倍</th>
	                                        <th>方案金额（元）</th>
	                                        <th>预计开奖时间</th>
	                                    </tr>
	                                </thead>
	                            </table>
	                            <div class="chase-number-table-bd">
	                                <table>
	                                    <colgroup><col width="40"><col width="200"><col width="80"><col width="140"><col width="140"></colgroup>
	                                    <tbody>
	                                    <?php 
	                                    $k = 0;
	                                    foreach ($chases as $issue => $chase) {
	                                     	if ($k < $chaselength) {?>
	                                    	<tr data-issue="<?php echo $issue?>">
	                                            <td><?php echo $k+1?></td>
	                                            <td class="tal"><input type="checkbox" checked><?php echo $issue?>期
	                                            <?php if ($issue == $info['cIssue']['seExpect']) {?><span class="main-color-s">（当前期）</span><?php }?></td>
	                                            <td><input type="text" value="<?php echo $multi?>" class="ipt-txt follow-multi">倍</td>
	                                            <td><span class="main-color-s follow-money"><?php echo $chase['money']?></span>元</td>
	                                            <td><?php echo substr($chase['award_time'], 0, -3)?></td>
	                                        </tr>
	                                    <?php 
	                                    	$k++;
											}else {
												break;
											}
										}?>
	                                    </tbody>
	                                </table>
	                            </div>
	                            <table class="chase-number-table-ft">
	                                <colgroup><col width="50%"><col width="50%"></colgroup>
	                                <tfoot>
	                                    <tr>
	                                        <td><span class="fbig">共追号<em>7</em>期，总投注金额<em>14</em>元</span></td>
	                                        <td class="tar">
	                                        	<div>
	                                                <input type="checkbox">中奖后停止追号
	                                                <span class="mod-tips">
	                                                    <i class="icon-font">&#xe613;</i><div class="mod-tips-t"><em>中奖停追：</em>勾选后，您的追号方案中的某一期中<br>奖后，后续的追号订单将被撤销，资金返还您的<br>账户中。如不勾选，系统一直帮您购买所有的追<br>号投注任务。<b></b><s></s></div>
	                                                </span>
	                                            </div>
	                                        </td>
	                                    </tr>
	                                </tfoot>
	                            </table>
	                        </div>
	                    </div>
	                    <!-- 追号弹层end -->
                    </div> 
                </div>
                <!-- 追号系统 end -->
                        
                        <div class="btn-group">
			    <a id="pd_syxw_buy" class="btn btn-main btn-betting <?php echo $showBind ? ' not-bind': '';?>">确认预约</a>
                            <p class="btn-group-txt">
                                <input class="ipt_checkbox" type="checkbox" checked="checked" id="agreenment"><label for="agreenment">我已阅读并同意</label>
                                <a href="javascript:;" class="lottery_pro lnk-txt">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro lnk-txt">《限号投注风险须知》</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 <?php $this->load->view('v1.2/elements/common/editballs');?>