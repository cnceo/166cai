<?php 
$hmselling = 0;
$hmendTime = $info['next']['seFsendtime']/1000 - $lotteryConfig[DLT]['united_ahead'] * 60;
if(!$lotteryConfig[DLT]['status']) {
	$selling = 0;
}elseif (isset($info['current']['endTime']) && time() > floor($info['current']['seFsendtime']/1000) && time() < floor($info['current']['endTime']/1000)) {
	$selling = 1;
}else {
	$selling = 2;
	if ($lotteryConfig[DLT]['united_status']) {
		$hmselling = 1;
	}
}?>
<script type="text/javascript">
var DLT_ISSUE = "<?php echo $info['next']['issue']?>", ENDTIME = "<?php echo date('Y-m-d H:i:s', $info['next']['seFsendtime']/1000)?>" ,tzjqurl = "<?php echo $tzjqurl?>",chases = $.parseJSON('<?php echo json_encode($chases)?>'), 
chaselength = <?php echo $chaselength?>, selling = <?php echo $selling?>, hmendTime = '<?php echo $hmendTime?>', hmDate = new Date(hmendTime * 1000), hmselling = '<?php echo $hmselling?>',
realendTime = '<?php echo date('Y-m-d H:i:s', $info['next']['seEndtime']/1000)?>';
var CUR_ISSUE = "<?php echo $info['next']['issue']?>";
var LOTTERY_ID = "<?php echo $lotteryId?>";
var _CURR_LI = 0;
var baseUrl = "<?php echo $baseUrl; ?>";
var _STATIC_FILE = baseUrl+"/caipiaoimg/v1.1/";
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/dlt.js');?>"></script>
<!--容器-->
<div class="wrap cp-box bet-num bet-dlt">
    <!--彩票信息-->
    <?php $this->load->view('v1.1/elements/lottery/info_panel', array('noIssue' => true)); ?>
    <!--彩票信息end-->
  <div class="cp-box-bd bet">
        <div class="bet-main">
        	<div class="bet-link-bd">
            <div class="bet-tab-hd _bet_tab_hd"><ul><li class="current" data-type="default"><a href="javascript:;">普通投注</a></li><li data-type="dt"><a href="javascript:;">胆拖投注</a></li><li data-type="ddsh"><a href="javascript:;">定胆杀号</a></li><li data-type="dssc" class="dssc"><a href="/dlt/dssc">单式上传</a></li></ul></div>
        	</div>
            <!-- 数字彩投注区 start -->
            <?php $this->load->view('v1.1/static/dlt_pickarea'); ?>
            
            <div class="cast-basket">
                <!--投注列表-->
                <div class="bet-area">
                	<div class="bet-area-box"><div class="bet-area-box-bd"><div class="inner"><ul class="cast-list"></ul></div></div><div class="bet-area-box-ft history"><a href="javascript:;" class="historyCompare histoy">历史开奖对比</a></div></div>
                    <div class="bet-area-qbtn">
                        <a class="btn-sup rand-cast" href="javascript:;" data-amount="1">机选1注</a><a class="btn-sup rand-cast" href="javascript:;" data-amount="5">机选5注</a>
                        <a class="btn-sup rand-cast" href="javascript:;" data-amount="10">机选10注</a><a class="btn-sup clear-list" href="javascript:;">清空列表</a>
                        <div class="ptips-bd ptips-bd-b">机选一注，试试手气<a href="javascript:;" class="ptips-bd-close">&times;</a><b></b><s></s></div>
                    </div>
                </div>
                <div class="bet-area-txt">
                    <p class="add-bets"><input type="checkbox" id="addBets"><label for="addBets"><i class="main-color-s">追加投注</i></label></p>
                    已选<strong class="num-multiple betNum _betNum">0</strong>注，投
                    <div class="multi-modifier">
                        <a href="javascript:;" class="minus _minus"">-</a><label><input class="multi number _multi" type="text" value="<?php echo $multi?>" autocomplete="off"  /></label><a href="javascript:;" class="plus _plus" data-max="99">+</a>
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
	                                        <th>序号</th><th class="tal"><input checked type="checkbox">追<input type="text" value="10" data-max="<?php echo count($chases) > 50 ? 50 : count($chases)?>" class="ipt-txt follow-issue _follow-issue">期<s>（共50期）</s></th>
	                                        <th><input type="text" class="ipt-txt follow-multi _follow-multi" value="<?php echo $multi?>" data-max="99">倍</th><th>方案金额（元）</th><th>预计开奖时间</th>
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
						</div>
						<!-- 追号弹层end -->
	                    <div class="tab-radio-inner"><?php $this->load->view('v1.1/elements/lottery/hemai');?></div>
                    </div> 
                </div>
                <!-- 追号系统 end -->
                <div class="btn-group">
                    <a class="btn btn-main _submit <?php echo $showBind ? ' not-bind': '';?>" style="display: none;">确认预约</a>
                    <a id="pd_ssq_buy" class="btn btn-main _gc_buy <?php echo $showBind ? ' not-bind': '';?>">确认预约</a>
                    <p class="btn-group-txt"><input class="ipt_checkbox" type="checkbox" checked="checked" id="agreenment"><label for="agreenment">我已阅读并同意</label><a href="javascript:;" class='lottery_pro'>《用户委托投注协议》</a></p>
                </div>
            </div>
    </div>

    <!-- 投注页侧边栏模块 -->
    <div class="bet-side">
      <div class="bet-side-item bet-side-jc">
        <h2 class="bet-side-title"><a href="<?php echo $baseUrl; ?>kaijiang/dlt" target="_blank">大乐透奖池</a></h2>
        <?php $pool = explode('|', $info['current']['pool'])?>
        <p class="num-jc"><em><?php if (!empty($pool[0])) { ?><b><?php echo $pool[0]?></b>亿<?php }?><?php if (empty($pool[0]) && empty($pool[1])) {?>奖池更新中...<?php }else {?><b><?php echo $pool[1]?></b>万<?php }?></em></p>
        <p>至少可开出<em><?php echo empty($pool[3]) ? '--' : $pool[3]?></em>注500万大奖</p>
      </div>
      <div class="bet-side-item bet-side-notice">
        <h2 class="bet-side-title"><a href="<?php echo $baseUrl; ?>kaijiang/dlt" target="_blank">大乐透开奖结果</a></h2>
        <div class="ball-group-box">
          <p class="ball-group-title"><a href="<?php echo $baseUrl; ?>kaijiang/dlt" target="_blank" class="more lnk-detail">详情<i>»</i></a>大乐透第<b><?php echo $info['current']['issue']?></b>期</p>
          <?php $awardArr = explode(":", $info['current']['awardNum'])?>
          <div class="ball-group-s">
          <?php foreach (explode(',', $awardArr[0]) as $award) {?><span><?php echo $award?></span><?php }
          foreach (explode(',', $awardArr[1]) as $award) {?><span class="ball-blue"><?php echo $award?></span><?php }?></div>
        </div>
        <?php $this->load->view('v1.1/static/dlt_kj');?>
        <a href="https://zoushi.166cai.cn/cjwdlt/view/dltjiben.html" rel="nofollow" target="_blank" class="more lnk-more">更多<i>»</i></a>
      </div>
      <div class="bet-side-item ">
        <h2 class="bet-side-title"><a href="https://zoushi.166cai.cn/cjwdlt/view/dltjiben.html" rel="nofollow" target="_blank">大乐透走势图</a></h2>
        <div class="lnk-chart">
          <a href="https://zoushi.166cai.cn/cjwdlt/view/dltjiben.html" rel="nofollow" target="_blank">基本走势</a>
          <a href="https://zoushi.166cai.cn/cjwdlt/view/dltyqzhihe.html" rel="nofollow" target="_blank">质合走势</a>
          <a href="https://zoushi.166cai.cn/cjwdlt/view/dlteqzonghe.html" rel="nofollow" target="_blank">后区综合走势</a>
          <a href="https://zoushi.166cai.cn/cjwdlt/view/dltyqdaxiao.html" rel="nofollow" target="_blank">大小走势</a>
          <a href="https://zoushi.166cai.cn/cjwdlt/view/dltyqjiou.html" rel="nofollow" target="_blank">奇偶走势</a>
          <a href="https://zoushi.166cai.cn/cjwdlt/view/dltyqsfq.html" rel="nofollow" target="_blank">前区三区走势</a>
          <a href="https://zoushi.166cai.cn/cjwdlt/view/dltyqwuxing.html" rel="nofollow" target="_blank">五行走势</a>
          <a href="https://zoushi.166cai.cn/cjwdlt/view/dltyqhezhi.html" rel="nofollow" target="_blank">前区和值</a>
          <a href="https://zoushi.166cai.cn/cjwdlt/view/dlteqhezhi.html" rel="nofollow" target="_blank">后区和值</a>
        </div>
      </div>
      <?php $this->load->view('v1.1/static/dlt_info');?>
      <?php $this->load->view('v1.1/elements/common/lottery_help');?>
    </div>
</div>
</div>
<!--容器end-->
<?php $this->load->view('v1.1/elements/common/editballs_gaoji');?>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/single.js');?>"></script>
<script type="text/javascript">
  $(function(){
    $('.dsjzsj').html(<?php echo $dsjzsj;?>);
    var _MplayType = "<?php echo isset($_GET['playType']) ? $_GET['playType'] :''; ?>";
    if(_MplayType=='default')
    {
      $('.bet-tab-hd li').eq(0).trigger('click');
    }
    if(_MplayType=='dt')
    {
      $('.bet-tab-hd li').eq(1).trigger('click');
    }
    if(_MplayType=='ddsh')
    {
      $('.bet-tab-hd li').eq(2).trigger('click');
    }
    $('.bet-tab-hd li').eq(3).unbind();

  });
</script>