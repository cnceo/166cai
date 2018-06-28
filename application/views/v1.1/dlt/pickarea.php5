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
                                    <em>前区</em>至少选择5个红球
                                </div>
                                <ol class="pick-area-ball">
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
                                    <?php for ($i = 5; $i <=18; $i++) {?>
		                            	<option value="<?php echo $i?>"><?php echo $i?></option>
		                            <?php }?>
                                    </select>
                                    <a href="javascript:;" class="rand-select">机选前区</a>
                                    <a href="javascript:;" class="clear-balls">清空</a>
                                </div>
                            </div>
                            <div class="pick-area-blue post-box">
                                <div class="pick-area-hd">
                                    <em>后区</em>至少选择2个蓝球
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
                                    <?php for ($i = 2; $i <=12; $i++) {?>
		                            	<option value="<?php echo $i?>"><?php echo $i?></option>
		                            <?php }?>
                                    </select>
                                    <a href="javascript:;" class="rand-select">机选后区</a>
                                    <a href="javascript:;" class="clear-balls">清空</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--添加到投注列表-->
                    <div class="bet-solutions box-collection">
                        <p><span>您已选中了 <strong class="num-red">0</strong> 个前区号码， <strong class="num-blue">0</strong> 个后区号码，共 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span></p>
                        <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font">&#xe614;</i></a></div>
                    </div>
                </div>
                
                <!-- 胆拖投注 -->
                <div class="bet-tab-bd-inner dt" style="display: none;">
                    <div class="pick-area-box">
                        <p class="pick-area-explain"><i class="icon-font">&#xe61b;</i>玩法说明：前区选择（胆码+拖码≥6个）、后区选择（胆码+拖码≥2个），组合后进行投注。</p>
                        <div class="pick-area">
                            <div class="pick-area-tips">
                                <span class="choose-tip">选号<i class="arrow"></i></span>
                                <div class="mod-tips">遗漏
                                    <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                </div>
                            </div>
                            <div class="pick-area-title"><span><em>胆码区</em>您认为<i class="num-red">必出</i>的号码</span></div>
                            <div class="pick-area-red pre-box">          
                                <div class="pick-area-hd">
                                    <em>前区</em>选择1-4个
                                </div>
                                <ol class="pick-area-ball">
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
                                    <em>后区</em>选择0-1个
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
                        </div>
                        <div class="pick-area">
                            <div class="pick-area-title"><span><em>拖码区</em>您认为<i class="num-red">可能出</i>的号码</span></div>
                            <div class="pick-area-red pre-box">          
                                <div class="pick-area-hd">
                                    <em>前区</em>至少选择2个
                                </div>
                                <ol class="pick-area-ball">
                                    <?php 
						            $i = 0;
						            foreach ($red as $m)
						            {
						            	$i++;?>
						            	<li><a href="javascript:;"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></a><i <?php if ($m == max($red)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
						            <?php }?>
                                </ol>
                                <div>
                                    <select class="rand-count">
                                    <?php for ($i = 2; $i <=33; $i++) {?>
		                            	<option <?php if ($i == 5){?>selected<?php }?> value="<?php echo $i?>"><?php echo $i?></option>
		                            <?php }?>
                                    </select>
                                    <a href="javascript:;" class="rand-select">机选前区</a>
                                    <a href="javascript:;" class="clear-balls">清空</a>
                                </div>
                            </div>
                            
                            <div class="pick-area-blue post-box">
                                <div class="pick-area-hd">
                                    <em>后区</em>至少选择2个
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
                                <div>
                                    <select class="rand-count">
                                    <?php for ($i = 2; $i <=12; $i++) {?>
		                            	<option value="<?php echo $i?>"><?php echo $i?></option>
		                            <?php }?>
                                    </select>
                                    <a href="javascript:;" class="rand-select">机选后区</a>
                                    <a href="javascript:;" class="clear-balls">清空</a>
                                </div>
                            <div>
                        </div>
                    </div>
                </div>
            </div>
            <!--添加到投注列表-->
            <div class="bet-solutions box-collection">
                <p><span>您已选中了 <strong class="num-red">62 </strong>  个前区（<strong class="num-red">4</strong>胆码，<strong class="num-red">20</strong>拖码）， <strong class="num-blue">20</strong> 个后区（<strong class="num-blue">0</strong>胆码，<strong class="num-blue">11</strong>拖码），共 <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span></p>
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
                                    <em>前区</em>最多定胆4个，超限后默认为杀号
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
                                    <em>后区</em>最多杀号10个
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
                                <?php for ($i = 5; $i <=18; $i++) {?>
	                            	<option value="<?php echo $i?>"><?php echo $i?></option>
	                            <?php }?>
                                </select>
                                个<span class="num-red">前区号码</span>，
                                <select class="rand-count">
                                <?php for ($i = 2; $i <=12; $i++) {?>
	                            	<option value="<?php echo $i?>"><?php echo $i?></option>
	                            <?php }?>
                                </select>
                                个<span class="num-blue">后区号码</span>，
                                <select class="rand-count">
                                    <option value="1">1</option>
	                                <option value="2">2</option>
	                                <option value="5">5</option>
	                                <option value="10">10</option>
	                                <option value="20">20</option>
	                                <option value="50">50</option>
                                </select>
                                个组号码
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
                <!--单式上传-->
                <div class="bet-tab-bd-inner  dssc" style="display:none;">
                    <div class="pick-area">
                        <p class="pick-area-explain"><i class="icon-font">&#xe61b;</i>玩法说明：上传方案中的单式号码与开奖号码相同，即中一等奖，单注最高奖金1000万元！</p>
                    </div>
                    <div class="upload">
                        <div class="rule">
                            <h3>上传规则：</h3>
                            <ol>
                                <li>1、单式上传提前<span class="dsjzsj">15</span>分钟截止，上传文件必须是（.txt）文本，文件大小不能超过256KB；</li>
                                <li>2、每个号码为2位数字，一行一个投注号，只支持单式号码上传；</li>
                                <li>3、前区间用“空格”或“,”或“.”分隔，前区和后区间用“|”或“+”或“:”分隔；</li>
                                <li class="example">
                                    <span>4、投注示例：</span>
                                    <div class="example-cnt">
                                        <p>01 02 03 04 05:05 06</p>
                                        <p>01,02,03,04,05+05,06</p>
                                        <p>01.02.03.04.05|05.06</p>
                                    </div>
                                </li>
                            </ol>
                        </div>

                        <div id="uploader" class="up-prog">
                            <div id="picker0">选择文件</div>
                            <div id="thelist0" class="uploader-list"><span class="uploader-list-tips">未选择任何文件</span></div>
                            <a href="javascript:;" id="ctlBtn0" class="btn btn-default">开始上传</a>
                        </div>
                    </div>

                </div>
            </div>