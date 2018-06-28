<?php 
if(!$lotteryConfig[CQSSC]['status']) {
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
chases = $.parseJSON('<?php echo json_encode($chases)?>'), chaselength = <?php echo $chaselength?>, enName = '<?php echo $enName?>', selling = <?php echo $selling?>,
miss = eval(<?php echo json_encode($miss)?>), type = '1xzhi'
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/order.min.js');?>"></script>
<!--[if lt IE 9]><script type="text/javascript" src="../../caipiaoimg/v1.1/js/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.2/js/cqssc.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.2/js/gaopin.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/template.js');?>"></script>
<script type="text/javascript">
$(function(){
	$("."+cx._basket_.tab+":first").trigger('click');
})
</script>
<div class="bet-syxw bet-cqssc">
    <div class="wrap cp-box bet-num">
    	<?php $this->load->view('v1.2/elements/lottery/info_panel', array('noIssue' => true)); ?>
        <div class="cp-box-bd bet">
            <div class="bet-main">
                <div class="bet-syxw-hd">
                    <ul class="bet-type-link">
                        <li data-type="1xzhi"><a href="javascript:;">一星直选<span>10元</span></a></li>
                        <li data-type="2xzhi"><a href="javascript:;">二星直选<span>100元</span></a></li>
                        <li data-type="2xzu"><a href="javascript:;">二星组选<span>50元</span></a></li>
                        <li data-type="3xzhi"><a href="javascript:;">三星直选<span><u>热</u>1000元</span></a></li>
                        <li data-type="3xzu3"><a href="javascript:;">三星组三<span>320元</span></a></li>
                        <li data-type="3xzu6"><a href="javascript:;">三星组六<span>160元</span></a></li>
                        <li data-type="5xzhi"><a href="javascript:;">五星直选<span><u>高</u>10万元</span></a></li>
                        <li data-type="5xt"><a href="javascript:;">五星通选<span>20440元</span></a></li>
                        <li data-type="dxds"><a href="javascript:;">大小单双<span><u>易</u>4元</span></a></li>
                    </ul>
                    <span class="periods-num">每日120期，已售<?php echo $count?>期，还剩<b class="main-color-s"><?php echo $rest?></b>期</span>
                </div>
                <div class="ykj-info">
                	<div id="chart"></div>
                    
                    <div class="canvas-mask"></div>
                    <div class="ykj-info-mask"></div>
                    <div class="ykj-info-action">近期走势<i class="arrow"></i></div>
                </div>
                <div class="bet-syxw-bd">
                    <div class="bet-type-link-bd">
                        <div class="bet-type-link-item php-1xzhi"></div>
                        <div class="bet-type-link-item php-2xzhi"></div>
                        <div class="bet-type-link-item php-2xzu"></div>
                        <div class="bet-type-link-item php-3xzhi"></div>
                        <div class="bet-type-link-item php-3xzu3"></div>
                        <div class="bet-type-link-item php-3xzu6"></div>
                        <div class="bet-type-link-item php-5xzhi"></div>
                        <div class="bet-type-link-item php-5xt"></div>
                        <div class="bet-type-link-item php-dxds"></div>
                    </div>
                    <div class="cast-basket">
                        <!--投注列表-->
                        <div class="bet-area">
                            <div class="bet-area-box">
                                <div class="bet-area-box-bd">
                                    <div class="inner">
                                        <ul class="cast-list">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="bet-area-qbtn">
                                <a class="btn-sup rand-cast" href="javascript:;" data-amount="1">机选1注</a><a class="btn-sup rand-cast" href="javascript:;" data-amount="5">机选5注</a>
                        		<a class="btn-sup rand-cast" href="javascript:;" data-amount="10">机选10注</a><a class="btn-sup clear-list" href="javascript:;">清空列表</a>
                                <div class="ptips-bd ptips-bd-b">机选一注，试试手气<a href="javascript:;" class="ptips-bd-close">×</a><b></b><s></s></div>
                            </div>
                        </div>
                        <div class="bet-area-txt">
                          已选<strong class="num-multiple betNum">0</strong>注，投
                          <div class="multi-modifier">
                            <a href="javascript:;" class="minus">-</a>
                            <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                            <a href="javascript:;" class="plus" data-max="10000">+</a>
                          </div>
                          倍（最大10000倍），共计<strong class="num-money betMoney">0</strong>元
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
	                                            <input checked type="checkbox">追<input type="text" value="10" data-max="<?php echo count($chases) > 120 ? 120 : count($chases)?>" class="ipt-txt follow-issue">期<s>（共120期）</s>
	                                        </th>
	                                        <th><input type="text" class="ipt-txt follow-multi" data-max="10000" value="<?php echo $multi?>">倍</th>
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
                        <div class="btn-group">
                            <a id="pd_cqssc_buy" class="btn btn-main btn-betting submit<?php echo $showBind ? ' not-bind': '';?>">确认投注</a>

                            <p class="btn-group-txt">
                                <input class="ipt_checkbox" type="checkbox" checked="checked" id="agreenment"><label for="agreenment">我已阅读并同意</label>
                                <a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="wrap bet-drop-ft">
        <dl>
            <dt class="view-kj"><i class="icon-font icon-arrow">&#xe62a;</i>今日开奖</dt>
            <dd>
                <div class="k3-table-kj cqssc-table-kj">
                </div>
            </dd>
        </dl>
        <dl>
            <dt><i class="icon-font icon-arrow">&#xe62a;</i>投注风险提示</dt>
            <dd>
                <p>若用户设置的为中奖后停止追号的方案，则在执行追号过程中，发生某期追号方案直到当期网站销售截止前二分钟仍不明确上期追号的中奖状态，则网站会对当期追号方案做继续追号的处理。敬请知悉由此产生的追号风险。</p>
                <p>查看<a href="/help/index/b5-s17" target="_blank">《玩法介绍》</a></p>
            </dd>
        </dl>
    </div>
</div>

 <?php $this->load->view('v1.2/elements/common/editballs');?>