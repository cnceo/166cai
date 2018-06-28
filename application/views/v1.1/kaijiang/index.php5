<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/kaijiang.min.css');?>">
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/lottery_1.0.min.js');?>"></script>
<script type="text/javascript">
$(function() {
    var ssqAward = '<?php echo $awards[Lottery_Model::SSQ]['awardNumber']; ?>';
    var ssqHtml = cx.Lottery.renderAward(cx.Lottery.SSQ, ssqAward);
    $('.ssq-award-nums').html(ssqHtml);

    var syxwAward = '<?php echo $awards[Lottery_Model::SYYDJ]['awardNumber']; ?>';
    var syxwHtml = cx.Lottery.renderAward(cx.Lottery.SYXW, syxwAward);
    $('.syxw-award-nums').html(syxwHtml);

    var jxsyxwAward = '<?php echo $awards[Lottery_Model::JXSYXW]['awardNumber']; ?>';
    var jxsyxwHtml = cx.Lottery.renderAward(cx.Lottery.JXSYXW, jxsyxwAward);
    $('.jxsyxw-award-nums').html(jxsyxwHtml);

    var hbsyxwAward = '<?php echo $awards[Lottery_Model::HBSYXW]['awardNumber']; ?>';
    var hbsyxwHtml = cx.Lottery.renderAward(cx.Lottery.HBSYXW, hbsyxwAward);
    $('.hbsyxw-award-nums').html(hbsyxwHtml);

    var gdsyxwAward = '<?php echo $awards[Lottery_Model::GDSYXW]['awardNumber']; ?>';
    var gdsyxwHtml = cx.Lottery.renderAward(cx.Lottery.GDSYXW, gdsyxwAward);
    $('.gdsyxw-award-nums').html(gdsyxwHtml);

    var dltAward = '<?php echo $awards[Lottery_Model::DLT]['awardNumber']; ?>';
    var dltHtml = cx.Lottery.renderAward(cx.Lottery.DLT, dltAward);
    $('.dlt-award-nums').html(dltHtml);

    var fcsdAward = '<?php echo $awards[Lottery_Model::FCSD]['awardNumber']; ?>';
    var fcsdHtml = cx.Lottery.renderAward(cx.Lottery.FCSD, fcsdAward);
    $('.fcsd-award-nums').html(fcsdHtml);

    var qxcAward = '<?php echo $awards[Lottery_Model::QXC]['awardNumber']; ?>';
    var qxcHtml = cx.Lottery.renderAward(cx.Lottery.QXC, qxcAward);
    $('.qxc-award-nums').html(qxcHtml);

    var qlcAward = '<?php echo $awards[Lottery_Model::QLC]['awardNumber']; ?>';
    var qlcHtml = cx.Lottery.renderAward(cx.Lottery.QLC, qlcAward);
    $('.qlc-award-nums').html(qlcHtml);

    var plsAward = '<?php echo $awards[Lottery_Model::PLS]['awardNumber']; ?>';
    var plsHtml = cx.Lottery.renderAward(cx.Lottery.PLS, plsAward);
    $('.pls-award-nums').html(plsHtml);

    var plwAward = '<?php echo $awards[Lottery_Model::PLW]['awardNumber']; ?>';
    var plwHtml = cx.Lottery.renderAward(cx.Lottery.PLW, plwAward);
    $('.plw-award-nums').html(plwHtml);

    var rjAward = '<?php echo $awards[Lottery_Model::RJ]['awardNumber']; ?>';
    var rjHtml = cx.Lottery.renderAward(cx.Lottery.RJ, rjAward);
    $('.rj-award-nums').html(rjHtml.replace(/ball-red/g, '').replace(/ball-blue/g, ''));

    var sfcAward = '<?php echo $awards[Lottery_Model::SFC]['awardNumber']; ?>';
    var sfcHtml = cx.Lottery.renderAward(cx.Lottery.SFC, sfcAward);
    $('.sfc-award-nums').html(sfcHtml.replace(/ball-red/g, '').replace(/ball-blue/g, ''));
});
</script>
<div class="wrap_in kj-container">
	<!-- 数字彩 -->
	<div class="mod-box">
		<div class="mod-box-hd">
			<span class="go-dl-app"><a href="<?php echo $baseUrl; ?>activity/newmode" target="_blank"><i class="icon-font">&#xe61c;</i><em>下载手机彩票，开奖自动推送</em></a></span>
			<h2 class="mod-box-title">数字彩</h2>
		</div>
		<div class="mod-box-bd">
			<table class="kj-data">
				<colgroup>
					<col width="12%">
					<col width="10%">
					<col width="19%">
					<col width="30%">
					<col width="9%">
					<col width="8%">
					<col width="12%">
				</colgroup>
				<thead>
					<tr>
						<th class="pl20">彩种</th>
						<th>期次</th>
						<th>开奖日期</th>
						<th>开奖号码</th>
						<th>详情</th>
						<th>走势图</th>
						<th>当期投注</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>ssq" target="_blank" >双色球</a></td>
						<td>第<?php echo $awards[Lottery_Model::SSQ]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::SSQ]['awardTime'] / 1000); ?></td>
						<td class='ssq-award-nums award-nums'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/ssq">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjwssq/view/ssqzonghe.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>ssq" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>dlt" target="_blank" >大乐透</a></td>
						<td>第<?php echo $awards[Lottery_Model::DLT]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::DLT]['awardTime'] / 1000); ?></td>
						<td class='dlt-award-nums award-nums'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/dlt">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjwdlt/view/dltjiben.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>dlt" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>fcsd" target="_blank" >福彩3D</a></td>
						<td>第<?php echo $awards[Lottery_Model::FCSD]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::FCSD]['awardTime'] / 1000); ?></td>
						<td class='fcsd-award-nums award-nums'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/fc3d">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjw3d/view/3d_danxuan.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>fcsd" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>pls" target="_blank" >排列三</a></td>
						<td>第<?php echo $awards[Lottery_Model::PLS]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::PLS]['awardTime'] / 1000); ?></td>
						<td class='pls-award-nums award-nums'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/pl3">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl3/view/pl3_danxuan.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>pls" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>plw" target="_blank" >排列五</a></td>
						<td>第<?php echo $awards[Lottery_Model::PLW]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::PLW]['awardTime'] / 1000); ?></td>
						<td class='plw-award-nums award-nums'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/pl5">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjwpl5/view/pl5_zst.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>plw" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>qxc" target="_blank" >七星彩</a></td>
						<td>第<?php echo $awards[Lottery_Model::QXC]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::QXC]['awardTime'] / 1000); ?></td>
						<td class='qxc-award-nums award-nums'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/qxc">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjw7xc/view/7xc_haoma.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>qxc" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>qlc" target="_blank" >七乐彩</a></td>
						<td>第<?php echo $awards[Lottery_Model::QLC]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::QLC]['awardTime'] / 1000); ?></td>
						<td class='qlc-award-nums award-nums'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/qlc">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjwqlc/view/qlcjiben.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>qlc" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<!-- 数字彩 end -->
	<!-- 竞技彩 -->
	<div class="mod-box">
		<div class="mod-box-hd">
			<span class="go-dl-app"><a href="<?php echo $baseUrl; ?>activity/newmode" target="_blank"><i class="icon-font">&#xe61c;</i><em>安装手机彩票，开奖早知道</em></a></span>
			<h2 class="mod-box-title">竞技彩</h2>
		</div>
		<div class="mod-box-bd">
			<table class="kj-data">
				<colgroup>
					<col width="12%">
					<col width="10%">
					<col width="19%">
					<col width="30%">
					<col width="17%">
					<col width="12%">
				</colgroup>
				<thead>
					<tr>
						<th class="pl20">彩种</th>
						<th>期次</th>
						<th>开奖日期</th>
						<th>开奖号码</th>
						<th>详情</th>
						<th>当期投注</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>jczq/hh" target="_blank" >竞彩足球</a></td>
						<td></td>
						<td>不定期</td>
						<td>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/jczq">详情</a></td>
						<td><a href="<?php echo $baseUrl; ?>jczq/hh" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>jclq/hh" target="_blank" >竞彩篮球</a></td>
						<td></td>
						<td>不定期</td>
						<td>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/jclq">详情</a></td>
						<td><a href="<?php echo $baseUrl; ?>jclq/hh" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>sfc" target="_blank" >胜负彩</a></td>
						<td>第<?php echo $awards[Lottery_Model::SFC]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::SFC]['awardTime'] / 1000); ?></td>
						<td class='sfc-award-nums award-nums-nobg pNum allBlack'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/sfc">详情</a></td>
						<td><a href="<?php echo $baseUrl; ?>sfc" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>rj" target="_blank" >任选九</a></td>
						<td>第<?php echo $awards[Lottery_Model::RJ]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::RJ]['awardTime'] / 1000); ?></td>
						<td class="rj-award-nums award-nums-nobg pNum allBlack">
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/rj">详情</a></td>
						<td><a href="<?php echo $baseUrl; ?>rj" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<!-- 竞技彩 end -->
	<!-- 高频彩 -->
	<div class="mod-box">
		<div class="mod-box-hd">
			<span class="go-dl-app"><a href="<?php echo $baseUrl; ?>activity/newmode" target="_blank"><i class="icon-font">&#xe61c;</i><em>下载手机彩票，开奖期期不落</em></a></span>
			<h2 class="mod-box-title">高频彩</h2>
		</div>
		<div class="mod-box-bd">
			<table class="kj-data">
				<colgroup>
					<col width="12%">
					<col width="10%">
					<col width="19%">
					<col width="30%">
					<col width="9%">
					<col width="8%">
					<col width="12%">
				</colgroup>
				<thead>
					<tr>
						<th class="pl20">彩种</th>
						<th>期次</th>
						<th>开奖日期</th>
						<th>开奖号码</th>
						<th>详情</th>
						<th>走势图</th>
						<th>当期投注</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>syxw" target="_blank" >老11选5</a></td>
						<td>第<?php echo $awards[Lottery_Model::SYYDJ]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::SYYDJ]['awardTime'] / 1000); ?></td>
						<td class='syxw-award-nums award-nums'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/syxw">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjw11x5_qs/view/11x5_jiben-5-11ydj-11.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>syxw" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>jxsyxw" target="_blank" >新11选5</a></td>
						<td>第<?php echo $awards[Lottery_Model::JXSYXW]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::JXSYXW]['awardTime'] / 1000); ?></td>
						<td class='jxsyxw-award-nums award-nums'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/jxsyxw">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjw11x5_qs/view/11x5_jiben-5-11x5-11.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>jxsyxw" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>ks" target="_blank" >经典快3</a></td>
						<td>第<?php echo substr($awards[Lottery_Model::KS]['seExpect'], 2); ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::KS]['awardTime'] / 1000); ?></td>
						<td>
							<div class="ks-num">
							<?php foreach (explode(',', $awards[Lottery_Model::KS]['awardNumber']) as $aw) {?>
								<span class="ks-num-<?php echo $aw?>"><?php echo $aw?></span>
							<?php }?>
                            </div>
                            <div class="award-nums award-nums-nobg pNum allBlack">
                            <?php foreach (explode(',', $awards[Lottery_Model::KS]['awardNumber']) as $aw) {?>
								<span class="ball"><?php echo $aw?></span>
							<?php }?>
			                </div>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/ks">详情</a></td>
						<td></td>
						<td><a href="<?php echo $baseUrl; ?>ks" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>klpk">快乐扑克</a></td>
						<td>第<?php echo $awards[Lottery_Model::KLPK]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::KLPK]['awardTime'] / 1000); ?></td>
						<td>
							<div class="klpk-num">
							<?php $numArr = array('', 'A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'); 
							$awardNum = explode('|', $awards[Lottery_Model::KLPK]['awardNumber']);
							$award[] = explode(',', $awardNum[0]);
							$award[] = explode(',', $awardNum[1]);
							for ($i = 0; $i < 3; $i++) {?>
								<span class="klpk-num-<?php echo strtolower($award[1][$i])?>"><?php echo $numArr[(int)$award[0][$i]]?></span>
							<?php }?>
                            </div>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/klpk">详情</a></td>
						<td></td>
						<td><a href="<?php echo $baseUrl; ?>klpk" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>hbsyxw" target="_blank" >惊喜11选5</a></td>
						<td>第<?php echo $awards[Lottery_Model::HBSYXW]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::HBSYXW]['awardTime'] / 1000); ?></td>
						<td class='hbsyxw-award-nums award-nums'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/hbsyxw">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjw11x5_qs/view/11x5_jiben-5-hub11x5-11.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>hbsyxw" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>jlks" target="_blank" >易快3</a></td>
						<td>第<?php echo substr($awards[Lottery_Model::JLKS]['seExpect'], 2); ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::JLKS]['awardTime'] / 1000); ?></td>
						<td>
							<div class="ks-num">
							<?php foreach (explode(',', $awards[Lottery_Model::JLKS]['awardNumber']) as $aw) {?>
								<span class="ks-num-<?php echo $aw?>"><?php echo $aw?></span>
							<?php }?>
                            </div>
                            <div class="award-nums award-nums-nobg pNum allBlack">
                            <?php foreach (explode(',', $awards[Lottery_Model::JLKS]['awardNumber']) as $aw) {?>
								<span class="ball"><?php echo $aw?></span>
							<?php }?>
			                </div>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/jlks">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjwk3/index-jilin.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>jlks" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>jxks" target="_blank" >红快3</a></td>
						<td>第<?php echo substr($awards[Lottery_Model::JXKS]['seExpect'], 2); ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::JXKS]['awardTime'] / 1000); ?></td>
						<td>
							<div class="ks-num">
							<?php foreach (explode(',', $awards[Lottery_Model::JXKS]['awardNumber']) as $aw) {?>
								<span class="ks-num-<?php echo $aw?>"><?php echo $aw?></span>
							<?php }?>
                            </div>
                            <div class="award-nums award-nums-nobg pNum allBlack">
                            <?php foreach (explode(',', $awards[Lottery_Model::JXKS]['awardNumber']) as $aw) {?>
								<span class="ball"><?php echo $aw?></span>
							<?php }?>
			                </div>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/jxks">详情</a></td>
						<td></td>
						<td><a href="<?php echo $baseUrl; ?>jxks" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
					<tr>
						<td class="pl20"><a href="<?php echo $baseUrl; ?>gdsyxw" target="_blank" >乐11选5</a></td>
						<td>第<?php echo $awards[Lottery_Model::GDSYXW]['seExpect']; ?>期</td>
						<td><?php echo date('Y年m月d日 H:i', $awards[Lottery_Model::GDSYXW]['awardTime'] / 1000); ?></td>
						<td class='gdsyxw-award-nums award-nums'>
						</td>
						<td><a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/gdsyxw">详情</a></td>
						<td><a rel="nofollow" href="https://zoushi.166cai.cn/cjw11x5/index-gd11x5.html" target="_blank">走势图</a></td>
						<td><a href="<?php echo $baseUrl; ?>gdsyxw" target="_blank" class="btn-ss btn-ss-bet">立即预约</a></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<!-- 高频彩 end -->
</div>