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
var QXC_ISSUE = "<?php echo $info['next']['issue']?>", ENDTIME = "<?php echo date('Y-m-d H:i:s', $info['next']['seFsendtime']/1000)?>", type = '<?php echo $qxcType; ?>', boxCount = <?php echo $boxCount; ?>, 
chases = $.parseJSON('<?php echo json_encode($chases)?>'), chaselength = <?php echo $chaselength?>, selling = <?php echo $selling?>, hmendTime = '<?php echo $hmendTime?>', hmDate = new Date(hmendTime * 1000), 
hmselling = '<?php echo $hmselling?>', realendTime = '<?php echo date('Y-m-d H:i:s', $info['next']['seEndtime']/1000)?>'
var CUR_ISSUE = "<?php echo $info['next']['issue']?>";
var LOTTERY_ID = "<?php echo $lotteryId?>";
var _CURR_LI = 0;
var baseUrl = "<?php echo $baseUrl; ?>";
var _STATIC_FILE = baseUrl+"/caipiaoimg/v1.1/";
var playType = 1; //玩法
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/qxc.js');?>"></script>
<div class="wrap cp-box bet-num bet-qxc">
	<?php $this->load->view('v1.1/elements/lottery/info_panel', array('noIssue' => true)); ?>
	<div class="cp-box-bd bet">
        <div class="bet-main">
            <div class="bet-link-bd">
                <div class="bet-pick-area bet-tab">
                    <!--玩法选项卡-->
                    <div class="bet-tab-hd _bet_tab_hd">
                        <ul>
                            <li data-type="default"><a href="/qxc">普通投注</a></li>
                            <li data-type="dssc" class="dssc current"><a href="javascript:;">单式上传</a></li>
                        </ul>
                    </div> 
                    <!--玩法选项卡结束-->
                    <!--玩法切换区域-->
                    <div class="bet-tab-bd">
                        <!--单式上传-->
                        <div class="bet-tab-bd-inner  dssc" >
                            <div class="pick-area">
                                <p class="pick-area-explain"><i class="icon-font">&#xe61b;</i>玩法说明：上传方案中的单式号码与开奖号码相同，即中一等奖，单注最高奖金500万元！</p>
                            </div>
                            <div class="upload">
                                <div class="rule">
                                    <h3>上传规则：</h3>
                                    <ol>
                                        <li>1、单式上传提前<?php echo $dsjzsj;?>分钟截止，上传文件必须是（.txt）文本，文件大小不能超过256KB；</li>
                                        <li>2、每个号码为1位数字，一行一个投注号，只支持单式号码上传；</li>
                                        <li>3、号码之间用“空格”或“,”或“.”分隔；</li>
                                        <li class="example">
                                            <span>4、投注示例：</span>
                                            <div class="example-cnt">
                                                <p>1,2,3,4,5,6,7</p>
                                                <p>1 2 3 4 5 6 7</p>
                                                <p>1.2.3.4.5.6.7</p>
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
                        <!--单式上传结束-->
                    </div>
                    <!--玩法切换区域结束-->
                </div>
            </div>
            <div class="cast-basket">
                <div class="bet-area-txt">
                    已选<strong class="num-multiple betNum _betNum">0</strong>注，投
                    <div class="multi-modifier">
                        <a href="javascript:;" class="_minus">-</a>
                        <label><input class="_multi" type="text" value="<?php echo $multi?>" autocomplete="off"></label>
                        <a href="javascript:;" class="_plus" data-max="99">+</a>
                    </div>
                    倍（最大99倍），共计<strong class="num-money betMoney _betMoney">0</strong>元
                </div>
                <!-- 追号系统 start -->
                <div class="buy-type tab-radio">
                    <div class="buy-type-hd tab-radio-hd">
                    	<div class="chase-number-notes">由购买人自行全额购买彩票，独享奖金<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>自购：</em>选好投注号码后，由自己全额<br>支付购彩款。中奖后，自己独享全部<br>税后奖金。">&#xe613;</i></span></div>
                        <em>购买方式：</em>
                        <ul class='_select_buy_way'>
                        	<li><label for="ordertype0"><input type="radio" id="ordertype0" name="chaseNumberTab" checked>自购</label></li>
                        	<li style="display: none;"><label for="ordertype1" class="main-color-s"><input type="radio" id="ordertype1" name="chaseNumberTab">我要追号</label><div class="mod-tips-t ptips-bd">花小钱，追百万大奖 <i class="ptips-bd-close">×</i><b></b><s></s></div></li>
                        	<li><label for="ordertype2" class="main-color-s"><input type="radio" id="ordertype2" name="chaseNumberTab">发起合买</label></li>
                        </ul>
                    </div>
                    <div class="buy-type-bd tab-radio-bd">
                    	<div class="tab-radio-inner hide"></div>
                        <div class="tab-radio-inner hide"></div>
	                    <!-- 追号弹层start -->
<!-- 	                    <div class="tab-radio-inner">
	                        <div class="chase-number-table">
	                            <table class="chase-number-table-hd">
	                                <thead>
	                                    <colgroup><col width="40"><col width="200"><col width="80"><col width="140"><col width="160"></colgroup>
	                                    <tr>
	                                        <th>序号</th>
	                                        <th class="tal">
	                                            <input checked type="checkbox">追<input type="text" value="10" data-max="<?php echo count($chases) > 50 ? 50 : count($chases)?>" class="ipt-txt follow-issue _follow-issue">期<s>（共50期）</s>
	                                        </th>
	                                        <th><input type="text" class="ipt-txt follow-multi _follow-multi" data-max="99" value="<?php echo $multi?>">倍</th>
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
	                                            <td><span class="main-color-s follow-money _follow-money"><?php echo $chase['money']?></span>元</td>
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
	                                        <td><span class="fbig">共追号<em class='_totalQc'>7</em>期，总投注金额<em class='_totalMoney'>14</em>元</span></td>
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
                        </div> -->
                        <!-- 追号弹层end -->
	                    <div class="tab-radio-inner"><?php $this->load->view('v1.1/elements/lottery/hemai_dssc');?></div>
                    </div> 
                </div>
                <!-- 追号系统 end -->
                <div class="btn-group">
                    <a class="btn btn-main _submit <?php echo $showBind ? ' not-bind': '';?>">确认预约</a>
	                <a id="pd_qxc_buy" class="btn btn-main  _gc_buy <?php echo $showBind ? ' not-bind': '';?>" style="display: none;">确认预约</a>
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
<?php $this->load->view('v1.1/elements/common/editballs');?>
<script>
$(function(){
    var ie6=!-[1,]&&!window.XMLHttpRequest;
    if(ie6){
        $('.mod-tips').on('mouseover', function(){
          $(this).find('.mod-tips-t').show();
        })
        $('.mod-tips').on('mouseout', function(){
          $(this).find('.mod-tips-t').hide();
        })
    }
})
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/webuploader.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/dssc.js');?>"></script>
<script type="text/javascript">
$(function(){
    $('.bet-pick-area.bet-tab').show();
    $('.bet-tab-hd li').eq(1).unbind();
})
</script>