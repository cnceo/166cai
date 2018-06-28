<?php 
$hmselling = 0;
$hmendTime = $info['next']['seFsendtime']/1000 - $lotteryConfig[QXC]['united_ahead'] * 60;
if(!$lotteryConfig[QXC]['status']) {
	$selling = 0;
}elseif (isset($info['current']['endTime']) && time() > floor($info['current']['seFsendtime']/1000) && time() < floor($info['current']['endTime']/1000)) {
	$selling = 1;
}else {
	$selling = 2;
	if ($lotteryConfig[QXC]['united_status']) {
		$hmselling = 1;
	}
}?>
<script type="text/javascript">
var ISSUE = "<?php echo $info['next']['issue']?>", ENDTIME = "<?php echo date('Y-m-d H:i:s', $info['next']['seFsendtime']/1000)?>", type = '<?php echo $qxcType; ?>', boxCount = <?php echo $boxCount; ?>, 
chases = $.parseJSON('<?php echo json_encode($chases)?>'), chaselength = <?php echo $chaselength?>, selling = <?php echo $selling?>, hmendTime = '<?php echo $hmendTime?>', hmDate = new Date(hmendTime * 1000), 
hmselling = '<?php echo $hmselling?>', realendTime = '<?php echo date('Y-m-d H:i:s', $info['next']['seEndtime']/1000)?>', MULTI = <?php echo (int)$multi?> || 1
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.2/js/qxc.min.js');?>"></script>
<div class="wrap cp-box bet-num bet-qxc">
	<?php $this->load->view('v1.2/elements/lottery/info_panel', array('noIssue' => true)); ?>
	<div class="cp-box-bd bet">
        <div class="bet-main">
            <div class="bet-link-bd">
                <div class="bet-pick-area bet-tab">
                    <!--玩法选项卡-->
                    <div class="bet-tab-hd _bet_tab_hd">
                        <ul>
                            <li class="current" data-type="default"><a href="javascript:;">普通投注</a></li>
                            <li data-type="dssc" class="dssc"><a href="/qxc/dssc">单式上传</a></li>
                        </ul>
                    </div>
                    <!--玩法选项卡结束-->
                    <!--玩法切换区域-->
                    <div class="bet-tab-bd">
                        <!--普通投注-->
                        <div class="bet-tab-bd-inner default">
                            <p class="pick-area-explain"><i class="icon-font">&#xe61b;</i>玩法说明：每位至少选择1个号码，所选号码与开奖号码相同（且顺序一致），即中一等奖，单注最高奖金500万元！ </p>
                            <div class="pick-area-box">
                            <?php 
                            ksort($miss);
                            foreach ($miss as $k => $each){?>
                                <div class="pick-area ball-box-<?php echo $k+1?>"> 
                                    <div class="pick-area-tips">
                                    <?php $arr = array(1 => '一', 2 => '二', 3 => '三', 4 => '四', 5 => '五', 6 => '六', 7 => '七')?>
                                        <span class="choose-tip">第<?php echo $arr[$k+1]?>位<i class="arrow"></i></span>
                                        <?php if ($k == 0) {?>
                                        <div class="mod-tips">遗漏
                                            <div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                        </div>
                                        <?php }?>
                                    </div>
                                    <div class="pick-area-red pre-box">          
                                        <ol class="pick-area-ball balls">
                                        <?php 
                                        $i = 0;
                                        $each = explode(',', $each);
                                        foreach ($each as $m)
                                        {?>
                                            <li><a href="javascript:;"><?php echo $i?></a><i <?php if ($m == max($each)){?> class="num-red" <?php }?>><?php echo $m?></i></li>
                                        <?php 
                                        $i++;
                                        }?>
                                        </ol>
                                        <div class="pick-area-select">
                                            <a href="javascript:;" class="filter-all">全</a><a href="javascript:;" class="filter-bigs">大</a><a href="javascript:;" class="filter-smalls">小</a><a href="javascript:;" class="filter-odds">奇</a><a href="javascript:;" class="filter-evens">偶</a><a href="javascript:;" class="clear-balls">清空</a>
                                        </div>
                                    </div>
                                </div>
                            <?php }?>
                            </div>
                        </div>

                    </div>
                    <!--玩法切换区域结束-->
                </div>
            </div>
            <div class="cast-basket">
                <!--添加到投注列表-->
                <div class="bet-solutions box-collection">
                    <p><span>您已选中了  <strong class="num-multiple">0</strong> 注，共 <strong class="num-money">0</strong> 元</span></p>
                    <div class="btn-pools"><a class="btn btn-specail add-basket btn-disabled btn-add2bet">添加到投注区<i class="icon-font"></i></a></div>
                </div>

                <!--投注列表-->
                <div class="bet-area">
                    <div class="bet-area-box"><div class="bet-area-box-bd"><div class="inner"><ul class="cast-list"></ul></div></div></div>
                    <div class="bet-area-qbtn">
                        <a href="javascript:;" class="btn-sup rand-cast" data-amount="1">机选1注</a><a href="javascript:;" class="btn-sup rand-cast" data-amount="5">机选5注</a>
		                <a href="javascript:;" class="btn-sup rand-cast" data-amount="10">机选10注</a><a href="javascript:;" class="btn-sup clear-list">清空列表</a>
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
                        	<li><label for="ordertype2" class="main-color-s"><input type="radio" id="ordertype2" name="chaseNumberTab">发起合买</label></li>
                        </ul>
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
	                                            <input checked type="checkbox">追<input type="text" value="10" data-max="<?php echo count($chases) > 50 ? 50 : count($chases)?>" class="ipt-txt follow-issue">期<s>（共50期）</s>
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
	                                            <?php if ($issue == $info['next']['issue']) {?><span class="main-color-s">（当前期）</span><?php }?></td>
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
	                                                <input type="checkbox">中奖金额><input class="ipt-txt setMoney" value="5000" type="text">元后停止追号
	                                                <span class="mod-tips">
	                                                    <i class="icon-font">&#xe613;</i><div class="mod-tips-t"><em>中奖停追：</em>勾选后，您的追号方案中的某一期中<br>奖金额大于您设定的金额后，后续的追号订单将<br>被撤销，资金返还您的账户中。如不勾选，系统<br>一直帮您购买所有的追号投注任务。<b></b><s></s></div>
	                                                </span>
	                                            </div>
	                                        </td>
	                                    </tr>
	                                </tfoot>
	                            </table>
	                        </div>
                        </div>
                        <!-- 追号弹层end -->
	                    <div class="tab-radio-inner"><?php $this->load->view('v1.1/elements/lottery/hemai');?></div>
                    </div> 
                </div>
                <!-- 追号系统 end -->
                <div class="btn-group">
	                <a id="pd_qxc_buy" class="btn btn-main <?php echo $showBind ? ' not-bind': '';?>">确认预约</a>
                    <p class="btn-group-txt">
                        <input class="ipt_checkbox" type="checkbox" checked="checked" id="agreenment"><label for="agreenment">我已阅读并同意</label>
                        <a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- 投注页侧边栏模块 -->
        <div class="bet-side">
            <div class="bet-side-item bet-side-jc">
                <?php $pool = explode('|', $info['current']['pool']);
		        	if ($info['current']['rStatus'] < 50) {?>
	        		<h2 class="bet-side-title"><a href="<?php echo $baseUrl; ?>kaijiang/qxc" target="_blank">七星彩奖池</a></h2>
					<p class="num-jc"><em>奖池更新中...</em></p>
					<p>2元赢取<em>500万</em>，下个大奖就是你！</p>
				<?php } elseif (empty($pool[1]) && empty($pool[2])) {?>
	        		<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/img-jiangchi.png');?>" width="230" height="100" alt="">
	        	<?php }else { ?>
	        		<h2 class="bet-side-title"><a href="<?php echo $baseUrl; ?>kaijiang/qxc" target="_blank">七星彩奖池</a></h2>
	            <?php $pool = explode('|', $info['current']['pool'])?>
	        		<p class="num-jc"><em><?php if (!empty($pool[1])) { ?><b><?php echo $pool[1]?></b>万<?php }?><b><?php echo $pool[2]?></b>元</em></p><p>2元赢取<em>500万</em>，下个大奖就是你！</p>
	        	<?php }?>
            </div>
            <div class="bet-side-item bet-side-notice">
                <h2 class="bet-side-title"><a href="<?php echo $baseUrl; ?>kaijiang/qxc" target="_blank">七星彩开奖结果</a></h2>
                <div class="ball-group-box">
                    <p class="ball-group-title"><a href="<?php echo $baseUrl; ?>kaijiang/qxc" target="_blank" class="lnk-detail">详情<i>»</i></a>七星彩第<b><?php echo $info['current']['issue']?></b>期</p>
	                <div class="ball-group-s">
	                  <?php foreach (explode(',', $info['current']['awardNum']) as $award)
			          {?>
			          	<span><?php echo $award?></span>
			          <?php }?>
	                </div>
	            </div>
                <table class="table-kj">
                    <thead><tr><th>期次</th><th>开奖号码</th></tr></thead>
                    <tbody>
                        <?php foreach ($info['kj'] as $kj)
				          {
				          $awardArr = explode("|", $kj['awardNum'])?>
				          	<tr>
									<td><?php echo $kj['issue']?>期</td>
									<td>
										<div class="num-group">
				              	<?php foreach (explode(',', $awardArr[0]) as $award){?><span><?php echo $award?></span><?php }?><?php foreach (explode(',', $awardArr[1]) as $award){?><span
												class="num-blue"><?php echo $award?></span><?php }?>
				              </div>
									</td>
								</tr>
				          <?php }?>
                    </tbody>
                </table>
                <a rel="nofollow" href="https://zoushi.166cai.cn/cjw7xc/view/7xc_haoma.html" target="_blank" class="lnk-more">更多<i>»</i></a>
            </div>
            
            <div class="bet-side-item ">
                <h2 class="bet-side-title"><a rel="nofollow" href="https://zoushi.166cai.cn/cjw7xc/view/7xc_haoma.html" target="_blank">七星彩走势图</a></h2>
                <div class="lnk-chart">
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjw7xc/view/7xc_haoma.html" target="_blank">基本走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjw7xc/view/7xc_hezhi.html" target="_blank">和值走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjw7xc/view/7xc_lianhao.html" target="_blank">连号走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjw7xc/view/7xc_leixing2type-jo.html" target="_blank">奇偶走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjw7xc/view/7xc_daxiao.html" target="_blank">大小走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjw7xc/view/7xc_leixing2type-zh.html" target="_blank">质合走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjw7xc/view/7xc_leixing3type-lengrewen.html" target="_blank">冷热温走势</a>
                </div>
            </div>
            <div class="bet-side-item ">
				<h3 class="bet-side-title"><a href="/info" target="_blank" class="more">更多<i>»</i></a><a href="/info/lists/1" target="_blank">彩票资讯</a></h3>
				<ol class="lnk-help">
				<?php foreach ($infoList as $k => $if) {?>
					<li><a href="/info/csxw/<?php echo $if['id']?>" target="_blank"><?php echo ($k+1).".".(mb_strlen($if['title'], 'utf-8') > 17 ? mb_substr($if['title'], 0, 17, 'utf-8')."..." : $if['title'])?></a></li>
				<?php }?>
				</ol>
			  </div>
            <?php $this->load->view('v1.1/elements/common/lottery_help');?>
        </div>
    </div>
</div>
<?php $this->load->view('v1.2/elements/common/editballs');?>
<script>
$(function(){
    $('.bet-pick-area.bet-tab').show();
    var ie6=!-[1,]&&!window.XMLHttpRequest;
    if(ie6){
        $('.mod-tips').on('mouseover', function(){
          $(this).find('.mod-tips-t').show();
        })
        $('.mod-tips').on('mouseout', function(){
          $(this).find('.mod-tips-t').hide();
        })
    }
     $('.bet-pick-area.bet-tab').show();
     $('.bet-tab-hd li').eq(1).unbind();

})
</script>