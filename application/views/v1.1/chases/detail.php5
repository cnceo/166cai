<!-- 追号结果页面 start -->
<?php $klpkNumArr = array('', 'A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K')?>
    <div class="wrap p-chasenum">
        <div class="chasenum-hd">
            <div class="lottery-logo notice-<?php echo $enName?>">
            	<div class="lottery-img">
            	<?php if ($enName == 'cqssc') {?>
            		<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/shishicai.png'); ?>" srcset="<?php echo getStaticFile('/caipiaoimg/v1.1/images/shishicai.svg'); ?> 2x" width="56" height="56" alt="">
            	<?php }else {?>
            		<svg width="224" height="224">
			           <image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="224" height="224"></image>
	                </svg>
				<?php }?>
                </div>
            </div>
            <p><span class="lottery-name"><?php echo $cnName?></span><span class="chasenum-txt specil-color"><?php echo parse_chase_status($order['status'])?></span></p>
            <ul>
                <li>订单编号：<?php echo $order['chaseId']?></li>
                <li>创建时间：<?php echo $order['created']?></li>
            <?php if ($order['status'] > 20) {?>
                <li>付款时间：<?php echo $order['pay_time']?></li>
            <?php }?>
            </ul>
        </div>

        <div class="chasenum-info">
            <h2 class="chasenum-mod-hd">追号信息</h2>
            <div class="chasenum-mod-bd">
                <ul class="chasenum-list">
                    <li class="chasenum-list-item">投注总额：<b><?php echo number_format(ParseUnit($order['money'], 1), 2)?>元</b></li>
                    <?php if ($order['setStatus'] == 1) {
                    	if (empty($order['setMoney'])) {
                    		echo '<li class="chasenum-list-item">追号设置：<b>中奖后停止追号</b></li>';
                    	}else {
                    		echo '<li class="chasenum-list-item">追号设置：<b>中奖金额><em class="specil-color">'.ParseUnit($order['setMoney'], 1).'</em>元停止追号</b></li>';
                    	}
                    }else {
                    	echo '<li class="chasenum-list-item">追号设置：<b>中奖后继续追号</b></li>';
                    }?>
                    
                    <li class="chasenum-list-item">追号进度：<b>共<?php echo $order['totalIssue']?>期，已追 <i class="num"><?php echo $order['chaseIssue']?></i>
                    	期<?php if ($order['failIssue'] > 0){ echo "，失败".$order['failIssue']."期";} 
                    	if ($order['revokeIssue'] > 0){echo "，撤单".$order['revokeIssue']."期"; }?></b></li>
                    <li class="chasenum-list-item">方案内容：
                    	<div class="chasenum-select" >
                        	<div class="chasenum-select-inner"></div>
                            <div class="m-bet-list" style="display: none">
                                <ul></ul>
                                <i class="arrow"></i>
                            </div>
                        </div>
                    </li>
                </ul>
				<?php switch ($order['status']) {
					case '0':
						$imgStr = 'dfk';
						$str = '<p>立即付款，开启中奖之旅吧！</p>';
						break;
					case '20':
						$imgStr = 'ddgqwfk';
						$str = '<p>下次不要忘支付哦，万一中大奖了呢</p>';
						break;
					case '21':
						$imgStr = 'ddgqytk';
						$str = '<p>订单已过期，如已支付将退款至账户</p>';
						break;
					case '700':
						if ($order['bonus'] > 0) {
							$imgStr = 'zj';
							$str = '<p>恭喜您已累积中奖<em class="specil-color">'.number_format(ParseUnit($order['bonus'], 1), 2).'</em>元！</p>';
						}else {
							$imgStr = 'zhwcwzj';
							$str = '<p>不要灰心，也许下个大奖就是你！</p>';
						}
						break;
					default:
						if ($order['bonus'] > 0) {
							$imgStr = 'zj';
							$str = '<p>恭喜您已累积中奖<em class="specil-color">'.number_format(ParseUnit($order['bonus'], 1), 2).'</em>元！</p>';
						}else {
							$imgStr = 'fkcg';
							$str = '<p>付款成功，耐心等待大奖的降临吧！</p>';
						}
						break;
				}?>
                <div class="chasenum-info-result zh-<?php echo $imgStr?>"><?php echo $str;
                if ($order['status'] == 0 && (strtotime("-{$lotteryConfig[$order['lid']]['ahead']} MINUTE", strtotime($order['endTime'])) > time())) {?>
                    	<a href="javascript:<?php if($order['singleFlag'] ==1): ?>cx.single<?php else: ?>cx.castCb<?php endif; ?>({orderId:'<?php echo $order['chaseId'];?>'}, {ctype:'paysearch', orderType:1});" class="lnk-btn lnk-btn-hover">立即支付</a>
                    <?php }else {?>
                    	<a target="_blank" href="/<?php echo BetCnName::getEgName($order['lid']);?>?chaseId=<?php echo $order['chaseId'];?>" class="lnk-btn">继续预约</a>
					<?php }?>
                </div>
            </div>
        </div>

        <div class="chasenum-detail">
            <h2 class="chasenum-mod-hd">追号详情</h2>
            <?php if ($order['hasstop']) {?>
            	<a href="javascript:;" class="btn-sup stopCheck">停止所选期次追号</a>
            <?php }?>
            <table>
            	<colgroup>
                    <col width="80"><col width="85"><col width="120"><col width="120">
            <?php if ($order['hasstop']) {?>
                    <col width="200"><col width="200"><col width="120"><col width="75">
            <?php } else {?>
                    <col width="240"><col width="240"><col width="115">
            <?php }?>
            	</colgroup>
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>期次</th>
                        <th>方案金额(元)</th>
                        <th>订单状态</th>
                        <th>当期开奖号码</th>
                        <th>中奖金额(元)</th>
                        <th>操作</th>
                        <th><?php if ($order['hasstop']) {?><input type="checkbox" class="stopIssue">全选<?php }?></th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($detail as $k => $dt) {?>
                	<tr data-issue="<?php echo $dt['issue']?>" data-issue="<?php echo $dt['issue']?>">
                        <td><?php echo $k+1?></td>
                        <td><?php echo $dt['issue']?></td>
                        <td><?php echo number_format(ParseUnit($dt['money'], 1), 2);?></td>
                        <td>
                        <?php echo ($dt['status'] == 601) ? '已撤单<br>（手动停止追号）' : (($dt['status'] == 603) ? '已撤单<br>（中奖后停止追号）' :
                        (parse_chase_order_status($order['status'],$dt['status']) === '等待出票' ? '<span class="fcw">等待出票</span>' : 
                        parse_chase_order_status($order['status'],$dt['status']))); ?>
                        </td>
                        <td>
                        <?php 
                        if (empty($award[$dt['issue']])) {?>
                        	<span class="fcw">预计开奖：<?php echo $dt['award_time']?></span>
						<?php } elseif ($order['lid'] == 54) {?>
							<div class="klpk-num">
							<?php $awArr = explode('|', $award[$dt['issue']]);
							$awd = array(explode(',', $awArr[0]), explode(',', $awArr[1]));
							for ($i = 0; $i < 3; $i++) {?>
								<span class="klpk-num-<?php echo strtolower($awd[1][$i])?>"><?php echo $klpkNumArr[(int)$awd[0][$i]]?></span>
							<?php }?>
						<?php }else {
							if ($order['lid'] == 23528) {
								$award[$dt['issue']] = preg_replace('/\((\d+)\)/', '|$1', $award[$dt['issue']]);
							}?>
                            <div class="num-group">
                            <?php foreach (explode('|', $award[$dt['issue']]) as $k => $awd) {
                            	foreach (explode(',', $awd) as $aw) {?>
                            	<span <?php if (in_array($order['lid'], array(51, 23529, 23528)) && $k == 1) {?>class="num-blue"<?php }?>><?php echo $aw?></span>
							<?php }
							}?>
                            </div>
                        <?php }?>
                        </td>
                        <td>
                        <?php 
                        if ($dt['bonus'] > 0) {?>
                        	<div class="bingo"><?php echo number_format(ParseUnit($dt['bonus'], 1), 2)?></div>
						<?php }elseif ($dt['status'] == 1000) { echo '0.00';}else { echo '--';}?>
                        </td>
                        <td>
                        <?php if (empty($dt['orderId'])) {?>--<?php }else {?>
							<a href="/orders/detail/<?php echo $dt['orderId']; ?>" target="_blank">查看详情</a>
						<?php }?>
                        </td>
                        <td><?php if ($order['hasstop'] && $dt['status'] == 0 && $dt['bet_flag'] == 0) {?><input type="checkbox" class="stopIssue" value="<?php echo $dt['issue']?>"><?php }?></td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
        </div>
    </div>
<!-- 追号结果页面 end -->
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/lottery_gaoji.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/single.js');?>"></script>
<script>
var curissue;


$(function(){

        ;(function(){
            $('.p-chasenum').on('click', '.chasenum-select-inner', function(){
                var that = $(this);
                that.parents('.chasenum-select').find('.m-bet-list').show();
                return false;
            })

            $('.p-chasenum').on('click', '.m-bet-list li', function(){
                var mBetList = $(this).parents('.m-bet-list');
                var chasenumSelectInner = $(this).parents('.chasenum-select').find('.chasenum-select-inner');
                chasenumSelectInner.html($(this).clone().html() + '<i class="arrow"></i>');
                mBetList.hide();
                return false;
            })

            $(document).on('click', function(){
                $('.p-chasenum').find('.m-bet-list').hide();
            })
        })()
	
	var order = $.parseJSON('<?php echo json_encode($order); ?>');

    if (!$.isArray(order)) {
        var lotteryId = order.lid;
        var castStr = order.codes;
        var casts = castStr.split(';');
        var cast;
        var castHtml;
        var castTpl = '';

        var tpl = '';
        var playType;
        for (var i = 0; i < casts.length; ++i) {
            
            castTpl = '';
            cast = casts[i];
            castHtml = cx.Lottery.renderCast(lotteryId, cast, null, 'chase');
            castTpl += castHtml;
            tpl += renderTr(/*order, playType, awardTpl, */ castTpl);
            if (i == 0) {
                $(".chasenum-select-inner").html(castTpl+"<i class='arrow'></i>");
            }
        }
        $('.m-bet-list ul').html(tpl);
    }

    function renderTr(castTpl) {
        return '<li>' + castTpl + '</li>';
    }
})
$(".chasenum-detail").on('click', ".stopIssue", function(){
	var index = $(".chasenum-detail .stopIssue").index(this);
	if (index == 0) {
	 	if ($(this).attr('checked') == 'checked') {
			$(".chasenum-detail .stopIssue").attr('checked', 'checked');
		}else {
			$(".chasenum-detail .stopIssue").removeAttr('checked');
		}
	}else {
		if ($(this).attr('checked') != 'checked') {
			$(".chasenum-detail .stopIssue:first").removeAttr('checked');
		}else if ($(".chasenum-detail .stopIssue[checked!='checked']").length == 1) {
			$(".chasenum-detail .stopIssue:first").attr('checked', 'checked');
		}
	}
})
$(".chasenum-detail").on('click', ".stopCheck", function(){
	var issue = [];
	$(".chasenum-detail tbody .stopIssue:checked").each(function(){
		issue.push($(this).val());
	})
	if (issue.length > 0) {
		cx.Alert({
			content:'<i class="icon-font">&#xe611;</i>真的要停止追号吗？',
			cancel:'取消',
			addtion:1,
			confirmCb: function(){
				$.ajax({
					type : 'post',
					data　: {chaseId:'<?php echo $order['chaseId']?>', issue:issue, lid:'<?php echo $order['lid']?>'},
					url  : 'chases/stoporders',
					success: function(data){
						if (data == 2) {
							cx.Confirm({
								btns: [{type: 'confirm',href: 'javascript:;', txt: '确定'}],
								content:'<div class="fz18 pt10 yahei c333 pop-new"><div class="pop-txt text-indent" style="margin-bottom:0px"><i class="icon-font">&#xe611;</i>您好，撤单操作异常，请重新选择停止追<br>号的期次。</div></div>',
								confirmCb: function(){location.reload();}
							});
						}else {
							cx.Confirm({
								btns: [{type: 'confirm',href: 'javascript:;', txt: '确定'}],
								content:'<div class="fz18 pt10 yahei c333 pop-new"><div class="pop-txt text-indent" style="margin-bottom:0px"><i class="icon-font">&#xe611;</i>您好，停止追号操作成功。将退款至您的账<br>户，请注意查收。</div></div>'
							});
							if ($(".stopIssue:first").attr('checked')) {
								$("colgroup").html("<col width='80'><col width='85'><col width='120'><col width='120'><col width='240'><col width='240'><col width='115'>");
								$(".chasenum-detail .stopCheck").remove();
								$(".chasenum-detail thead tr th:last").empty();
							}
							for (i in issue) {
								$("tr[data-issue='"+issue[i]+"']").find('td:eq(3)').html('已撤单<br>（手动停止追号）');
								$("tr[data-issue='"+issue[i]+"']").find('td:last').empty();
							}
						}
						
					}
				});
			},
			cancel:'取消'
		});
	}else {
		cx.Alert({content:'<i class="icon-font">&#xe611;</i>您好，请先选择追号停止期次！'})
	}
})
</script>
