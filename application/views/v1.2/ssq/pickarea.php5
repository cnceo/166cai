<div class="bet-tab-bd">
            <!-- 普通投注 -->
            <div class="bet-tab-bd-inner default">
                <div class="pick-area-box">
                    <div class="pick-area">
                        <div class="pick-area-tips">
                            <span class="choose-tip">选号<i class="arrow"></i></span>
                            <div class="mod-tips">遗漏
                                <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                            </div>
                        </div>
                        <div class="pick-area-red pre-box">          
                            <div class="pick-area-hd">
                                <em>红球区</em>至少选择6个红球 
                            </div>
                            <ol class="pick-area-ball balls">
                                <?php $miss = $miss[0];
					            $i = 0;
					            list($red, $blue) = explode("|", $miss); 
					            $red = explode(",", $red);
					            foreach ($red as $m)
					            {
					            	$i++;?>
					            	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($red)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
					            <?php }?>
                            </ol>
                            <div>
	                            <select class="rand-count">
	                            <?php for ($i = 6; $i <=16; $i++) {?>
	                            	<option value="<?php echo $i?>"><?php echo $i?></option>
	                            <?php }?>
                                </select>
                                <a href="javascript:;" class="rand-select">机选红球</a>
                                <a href="javascript:;" class="clear-balls">清空</a>
                            </div>
                        </div>
                        <div class="pick-area-blue post-box">
                            <div class="pick-area-hd">
                                <em>蓝球区</em>至少选择1个蓝球
                            </div>
                            <ol class="pick-area-ball">
                                <?php 
					            $i = 0;
					            $blue = explode(",", $blue);
					            foreach ($blue as $m)
					            {
					            	$i++;?>
					            	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($blue)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
					            <?php }?>
                            </ol>
                            <div>
                                <select class="rand-count">
                                <?php for ($i = 1; $i <=16; $i++) {?>
	                            	<option value="<?php echo $i?>"><?php echo $i?></option>
	                            <?php }?>
                                </select>
                                <a href="javascript:;" class="rand-select">机选蓝球</a>
                                <a href="javascript:;" class="clear-balls">清空</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 数字彩投注区 end -->

                <!--添加到投注列表-->
                <div class="bet-solutions box-collection">
                    <p><span>您已选中了 <strong class="num-red">0</strong> 个红球， <strong class="num-blue">0</strong> 个蓝球，共 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span></p>
                    <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font">&#xe614;</i></a></div>
                </div>
            </div>
                
            <!-- 胆拖投注 -->
            <div class="bet-tab-bd-inner bet-ssq-dt dt" style="display: none;">
                    <div class="pick-area-box">
                        <p class="pick-area-explain"><i class="icon-font">&#xe61b;</i>玩法说明：选择1-5个红球胆码、至少2个红球拖码(胆码＋拖码≥7个），至少1个蓝球组合后进行投注。</p>
                        <div class="pick-area">
                        	<div class="pick-area-tips">
                            <span class="choose-tip">选号<i class="arrow"></i></span>
                            <div class="mod-tips">遗漏
                                <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                            </div>
                        </div>
                            <div class="pick-area-red pre-box">          
                                <div class="pick-area-hd">
                                    <em>红球胆码区</em>您认为必出的号码（<i class="main-color-s">选择1-5个</i>）
                                </div>
                                <ol class="pick-area-ball balls">
                                    <?php 
						            $i = 0;
						            foreach ($red as $m)
						            {
						            	$i++;?>
						            	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($red)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
						            <?php }?>
                                </ol>
                            </div>
                            <div class="pick-area-red pre-box">          
                                <div class="pick-area-hd">
                                    <em>红球拖码区</em>您认为可能出的号码（<i class="main-color-s">至少选择2个</i>）
                                </div>
                                <ol class="pick-area-ball balls">
                                    <?php 
						            $i = 0;
						            foreach ($red as $m)
						            {
						            	$i++;?>
						            	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($red)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
						            <?php }?>
                                </ol>
                                <div class="fr">
                                    <select class="rand-count">
                                    <?php for ($i = 2; $i <=30; $i++) {?>
		                            	<option value="<?php echo $i?>" <?php if ($i === 5) echo 'selected'?>><?php echo $i?></option>
		                            <?php }?>
                                    </select>
                                    <a href="javascript:;" class="rand-select">机选拖码</a>
                                    <a href="javascript:;" class="clear-balls">清空</a>
                                </div>
                            </div>
                            <div class="pick-area-blue post-box">
                                <div class="pick-area-hd">
                                    <em>蓝球区</em>至少选择1个蓝球
                                </div>
                                <ol class="pick-area-ball">
                                    <?php 
						            $i = 0;
						            foreach ($blue as $m)
						            {
						            	$i++;?>
						            	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($blue)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
						            <?php }?>
                                </ol>
                                <div class="fr">
                                    <select class="rand-count">
                                    <?php for ($i = 1; $i <=16; $i++) {?>
		                            	<option value="<?php echo $i?>"><?php echo $i?></option>
		                            <?php }?>
                                    </select>
                                    <a href="javascript:;" class="rand-select">机选蓝球</a>
                                    <a href="javascript:;" class="clear-balls">清空</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <!--添加到投注列表-->
                <div class="bet-solutions box-collection">
                    <p><span>您已选中了 <strong class="num-red">0</strong> 个红球（<strong class="num-red">0</strong>胆码，<strong class="num-red">0</strong>拖码）， <strong class="num-blue">0</strong> 个蓝球，共 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span></p>
                    <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font">&#xe614;</i></a></div>
                </div>
            </div>

            <!-- 定胆杀号 -->
            <div class="bet-tab-bd-inner ddsh" style="display: none;">
                <div class="pick-area-box">
                    <p class="pick-area-explain"><i class="icon-font">&#xe61b;</i>玩法说明：同一个号码点击一下为"定胆"、点击两下为"杀号"、点击三下"还原"。</p>
                        <div class="pick-area">
                            <div class="pick-area-tips">
                                <span class="choose-tip">选号<i class="arrow"></i></span>
                                <div class="mod-tips">遗漏
                                    <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                </div>
                            </div>
                            <div class="pick-area-red pre-box">          
                                <div class="pick-area-hd">
                                    <em>红球区</em>最多定胆5个，超限后默认为杀号
                                </div>
                                <ol class="pick-area-ball balls">
                                    <?php 
				            $i = 0;
				            foreach ($red as $m)
				            {
				            	$i++;?>
				            	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($red)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
				            <?php }?>
                                </ol>
                            </div>
                            <div class="pick-area-blue post-box">
                                <div class="pick-area-hd">
                                    <em>蓝球区</em>最多杀号15个
                                </div>
                                <ol class="pick-area-ball">
                                    <?php 
				            $i = 0;
				            foreach ($blue as $m)
				            {
				            	$i++;?>
				            	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($blue)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
				            <?php }?>
                                </ol>
                            </div>
	                        <div class="tac">
	                            机选
	                            <select class="rand-count">
	                            <?php for ($i = 6; $i <=16; $i++) {?>
	                            	<option value="<?php echo $i?>"><?php echo $i?></option>
	                            <?php }?>
	                            </select>
	                            个<span class="num-red">红球</span>，
	                            <select class="rand-count">
	                            <?php for ($i = 1; $i <=16; $i++) {?>
	                            	<option value="<?php echo $i?>"><?php echo $i?></option>
	                            <?php }?>
	                            </select>
	                            个<span class="num-blue">蓝球</span>，
	                            <select class="rand-count">
	                                <option value="1">1</option>
	                                <option value="2">2</option>
	                                <option value="5">5</option>
	                                <option value="10">10</option>
	                                <option value="20">20</option>
	                                <option value="50">50</option>
	                            </select>
	                            组号码
	                        </div>  
                        </div>
                         

                </div>
                <!-- 数字彩投注区 end -->
                <!--添加到投注列表-->
                <div class="bet-solutions box-collection">
                    <p><span>您已选中了 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span></p>
                    <div class="btn-pools">
                        <a class="btn btn-specail add-basket btn-disabled btn-add2bet" href="javascript:;">添加到投注区<i class="icon-font">&#xe614;</i></a>
                        <a href="javascript:;" class="clear-pickball">清空以上选号</a>
                    </div>
                </div>
            </div>
        </div>