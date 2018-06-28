<?php 
$hmselling = 0;
$hmendTime = $info['next']['seFsendtime']/1000 - $lotteryConfig[PLS]['united_ahead'] * 60;
if(!$lotteryConfig[PLS]['status']) {
	$selling = 0;
}elseif (isset($info['current']['endTime']) && time() > floor($info['current']['seFsendtime']/1000) && time() < floor($info['current']['endTime']/1000)) {
	$selling = 1;
}else {
	$selling = 2;
	if ($lotteryConfig[PLS]['united_status']) $hmselling = 1;
}?>
<script type="text/javascript">
var PLS_ISSUE = "<?php echo $info['next']['issue']?>", ENDTIME = "<?php echo date('Y-m-d H:i:s', $info['next']['seFsendtime']/1000)?>", type = '<?php echo $plsType; ?>', boxCount = <?php echo $boxCount; ?>, 
chases = $.parseJSON('<?php echo json_encode($chases)?>'), chaselength = <?php echo $chaselength?>, selling = <?php echo $selling?>, hmendTime = '<?php echo $hmendTime?>', hmDate = new Date(hmendTime * 1000), 
hmselling = '<?php echo $hmselling?>', realendTime = '<?php echo date('Y-m-d H:i:s', $info['next']['seEndtime']/1000)?>'
var CUR_ISSUE = "<?php echo $info['next']['issue']?>";
var LOTTERY_ID = "<?php echo $lotteryId?>";
var _CURR_LI = 0;
var baseUrl = "<?php echo $baseUrl; ?>";
var _STATIC_FILE = baseUrl+"/caipiaoimg/v1.1/";
var playType = 2; //玩法
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/pls.min.js');?>"></script>
<?php 
function calculate($str)
{
	$arr = explode(',', $str);
	switch (count(array_unique($arr)))
	{
		case 1:
			$type = '豹子';
			break;
		case 2:
			$type = '组三';
			break;
		case 3:
		default:
			$type = '组六';
			break;
	}
	return array('hezhi' => array_sum($arr), 'type' => $type);
}
?>
<div class="wrap cp-box bet-num bet-pls">
  <!--彩票信息-->
    <?php $this->load->view('v1.1/elements/lottery/info_panel', array('noIssue' => true)); ?>
    <div class="cp-box-bd bet">
        <div class="bet-main">
            <div class="bet-link-bd">
                <div class="pick-area-box bet-tab">
                    <!-- 数字彩投注区 start -->
                    <div class="bet-tab-hd _Jfc3d">
                        <ul>
                        <?php foreach ($typeMAP as $k => $type) {?>
                            <li data-type='<?php echo $k?>' <?php if ($k === $plsType) {?> class='current' <?php } ?>><a href="/pls?playType=<?php echo $k?>"><?php echo $type['cnName']?></a></li>
                        <?php }?>
                        </ul>
                    </div>
                    <div class="bet-tab-bd">
                       <!--内容循环开始-->
                       <?php foreach ($miss as $mk => $ms): ?> 
                        <div class="bet-tab-bd-inner tab-radio <?php echo $mk?>" <?php if ($plsType !== $mk) {?> style="display:none" <?php }?> >
                            <div class="tab-radio-hd _radio_selected">
                                <ul>
                                    <li class="selected" data-url='pls?playType=<?php echo $mk?>'>
                                        <label><input type="radio" name="<?php echo $mk?>_betTypeTab0">普通</label>
                                    </li>
                                    <li class="dssc" play-type="<?php 
                                    if($mk == 'zx'){echo 1;}
                                    if($mk == 'z3'){echo 2;}
                                    if($mk == 'z6'){echo 3;}
                                        ?>" data-url='pls/dssc/<?php echo $mk?>'>
                                        <label><input type="radio" checked name="<?php echo $mk?>_betTypeTab0">单式上传</label>
                                    </li> 
                                </ul>
                            </div>
                            <!--内容-->
                            <div class="tab-radio-bd">
                                  <!--单式上传-->
                                  <!--组三-->
                                  <?php if ($mk == 'z3'): ?> 
                                  <div class="tab-radio-inner">
                                      <div class="pick-area">
                                          <p class="pick-area-explain"><i class="icon-font">&#xe61b;</i>玩法说明：上传方案中的单式号码与开奖号码相同（顺序不限）即中346元！</p>
                                      </div>
                                      <div class="upload">
                                          <div class="rule">
                                              <h3>上传规则：</h3>
                                              <ol>
                                                  <li>1、单式上传提前<?php echo $dsjzsj;?>分钟截止，上传文件必须是（.txt）文本，文件大小不能超过256KB；</li>
                                                  <li>2、每个号码为1位数字，一行一个投注号，只支持单式号码上传；</li>
                                                  <li>3、号码之间用“空格”或“,”分隔；</li>
                                                  <li class="example">
                                                      <span>4、投注示例：</span>
                                                      <div class="example-cnt">
                                                          <p>1,2,2或1 1 4或655；</p>
                                                      </div>
                                                  </li>
                                              </ol>
                                          </div>
                                          <div id="uploader0" class="up-prog">
                                              <div id="picker0">选择文件</div>
                                              <!--用来存放文件信息-->
                                              <div id="thelist0" class="uploader-list"><span class="uploader-list-tips">未选择任何文件</span></div>
                                              <a href="javascript:;" id="ctlBtn0" class="btn btn-default">开始上传</a>
                                          </div>
                                      </div>
                                  </div>
                                  <?php endif; ?>
                                  <!--组六-->
                              </div>
                        </div>
                        <?php endforeach; ?> 
                        <!--内容循环结束-->
                    </div>                
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
	                                                <input type="checkbox">中奖后停止追号
	                                                <span class="mod-tips">
	                                                    <i class="icon-font">&#xe613;</i>
	                                                    <div class="mod-tips-t">
	                                                        <em>中奖停追：</em>勾选后，您的追号方案中的某一期中<br>奖后，后续的追号订单将被撤销，资金返还您的<br>账户中。如不勾选，系统一直帮您购买所有的追<br>号投注任务。
	                                                        <b></b><s></s>
	                                                    </div>
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
                  <a class="btn btn-main _submit <?php echo $showBind ? ' not-bind': '';?>" >确认预约</a> 
                	<a id="pd_pls_buy" class="btn btn-main  _gc_buy  <?php echo $showBind ? ' not-bind': '';?>" style="display: none;">确认预约</a> 
                    <div class="btn-group-txt">
                        <p>
                            <input class="ipt_checkbox" type="checkbox" checked="checked" id="agreenment1"><label for="agreenment1">我已阅读并同意</label>
                            <a href="javascript:;" class="lottery_pro lnk-txt">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro lnk-txt">《限号投注风险须知》</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 投注页侧边栏模块 -->
        <div class="bet-side">
            <div class="bet-side-item bet-side-notice">
                <h2 class="bet-side-title"><a href="<?php echo $baseUrl; ?>kaijiang/pl3" target="_blank">排列三开奖结果</a></h2>
                <div class="ball-group-box">
                    <p class="ball-group-title"><a href="<?php echo $baseUrl; ?>kaijiang/pl3" target="_blank" class="lnk-detail">详情<i>»</i></a>排列三第<b><?php echo $info['current']['issue']?></b>期</p>
                    <?php $curcal = calculate($info['current']['awardNum'])?>
                    <p class="fr"><span class="mr20">类型：<?php echo $curcal['type']?></span>和值：<?php echo $curcal['hezhi']?></p>
                    <div class="ball-group-s">
                        <?php foreach (explode(',', $info['current']['awardNum']) as $award)
				          {?>
				          	<span><?php echo $award?></span>
				          <?php }?>
                    </div>

                    <table class="table-zj">
                        <colgroup><col width="30%"><col width="35%"><col width="35%"></colgroup>
                        <thead><tr><th>奖项</th><th>中奖注数</th><th>单注中奖</th></tr></thead>
                        <tbody>
                        <?php
                        foreach ($info['current']['bonusDetail'] as $k => $bonusDetail) {?>
                        	<tr>
                                <td><?php echo $typeMAP[$k]['cnName']?></td>
                                <td><?php echo $bonusDetail['zs']?></td>
                                <td><?php echo $bonusDetail['dzjj']?></td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
                <table class="table-kj">
                    <colgroup><col width="30%"><col width="30%"><col width="20%"><col width="20%"></colgroup>
                    <thead><tr><th>期次</th><th>开奖号码</th><th>类型</th><th>和值</th></tr></thead>
                    <tbody>
                    	<?php foreach ($info['kj'] as $kj)
				          {
				          $awardArr = explode("|", $kj['awardNum']);
				          $kjCal = calculate($kj['awardNum'])?>
				          	<tr>
								<td><?php echo $kj['issue']?>期</td>
								<td>
									<div class="num-group">
			              			<?php foreach (explode(',', $awardArr[0]) as $award){?><span><?php echo $award?></span><?php }?>
			              			</div>
								</td>
								<td><?php echo $kjCal['type']?></td>
								<td><?php echo $kjCal['hezhi']?></td>
							</tr>
			          <?php }?>
                    </tbody>
                </table>
                <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_danxuan.html" target="_blank" class="lnk-more">更多<i>»</i></a>
            </div>
            <div class="bet-side-item ">
                <h2 class="bet-side-title"><a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_danxuan.html" target="_blank">排列三走势图</a></h2>
                <div class="lnk-chart">
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_danxuan.html" target="_blank">基本走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_zonghe.html" target="_blank">综合走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_hezhi.html" target="_blank">和值走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_weishu-kd.html" target="_blank">跨度走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_2numdx-dx.html" target="_blank">大小走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_hezhiqj.html" target="_blank">和值区间走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_2numdx-jo.html" target="_blank">奇偶走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_2numdx-zh.html" target="_blank">质合走势</a>
                    <a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_3numshuxing-lrw.html" target="_blank">冷热温分析走势</a>
                </div>
            </div>
            <div class="bet-side-item ">
				<h3 class="bet-side-title"><a href="/info" target="_blank" class="more">更多<i>»</i></a><a href="/info/lists/5" target="_blank">排列三推荐</a></h3>
				<ol class="lnk-help">
				<?php foreach ($infoList as $k => $if) {?>
					<li><a href="/info/qttc/<?php echo $if['id']?>" target="_blank"><?php echo ($k+1).".".(mb_strlen($if['title'], 'utf-8') > 18 ? mb_substr($if['title'], 0, 18, 'utf-8')."..." : $if['title'])?></a></li>
				<?php }?>
				</ol>
			  </div>
            <?php $this->load->view('v1.1/elements/common/lottery_help');?>
        </div>
    </div>
</div>
<?php $this->load->view('v1.1/elements/common/editballs');?>
<script type="text/javascript">
$(function(){
  $('.bet-tab-hd li').eq(1).trigger('click');
  $('.bet-tab-hd li').unbind();
});
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/webuploader.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/dssc.js');?>"></script>