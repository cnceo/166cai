<?php if (!$this->is_ajax ) {?>
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/hemai.min.css');?>" rel="stylesheet" type="text/css" />
<div class="wrap p-hemai p-hemai-index">
	<div class="fn-sticky">
		<div class="fn-sticky-inner filter-tab">
			<div class="fixmacOS" style="margin-right: -10px;">
				<span <?php if ($this->act === 'index') {?> class="current" <?php }?>><a href="/hemai">全部彩种</a></span>
		      <?php foreach ($lidArr as $key => $val) {?>
		      	<span <?php if ($this->act === $key) {?> class="current" <?php }?>><a href="/hemai/<?php echo $key?>"><?php echo $val?></a></span>
			  <?php }?>
			</div>
		</div>
    </div>
	<div class="popular">
		<div class="popular-hd">
			<h2 class="popular-title"><i class="icon-font">&#xe636;</i><?php echo array_key_exists($this->act, $lidArr) ? $lidArr[$this->act] : ''?>合买红人</h2>
			<span class="popular-notes"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/popular-active.gif');?>" width="12" height="14" alt="">表示有方案可参与！</span>
		</div>
		<div class="popular-bd">
			
      <?php
	
if (! empty ( $hotPlanner ) && (!empty($hotPlanner[0]) || !empty($hotPlanner[1])))
	{?><div class="popular-group">
		<?php $arr = array ('hg', 'ty', 'yl', 'xx');
		if (!empty($hotPlanner[0])) {
		foreach ( $hotPlanner[0] as $planner )
		{
			$hg1 = 0;
			list ( $hg, $ty, $yl, $xx ) = str_split ( str_pad ( $planner['united_points'], 4, '0', STR_PAD_LEFT ) );
			if (strlen($planner['united_points']) > 4) {
				$hg1 = substr($planner['united_points'], 0, -4);
				if ($hg1 >= 2) $xx = 0;
				if ($hg1 >= 3) $yl = 0;
				if ($hg1 >= 4) {$ty = $hg = 0; $hg1 = 5;}
			}
			if ($planner ['isOrdering'])
			{
				?>
      	<span class="active"> <a target="_blank" <?php if ($planner['united_points'] || $planner['monthBonus']) {?> class="bubble-tip" <?php }?> tiptext="<?php if ($planner['united_points']) {?>战绩：<span class='level'><?php echo calGrade($planner['united_points'], 5)?></span><br>
	      	<?php }?>近1月中奖：<strong class='main-color-s'><?php echo number_format(ParseUnit($planner['monthBonus'], 1), 2)?></strong>元" href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $planner['uid'])), 'ENCODE'));?>">
	      	<?php echo uname_cut($users[$planner['uid']]['uname']);?>
	      	</a> <sup><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/popular-active.gif');?>" width="12" height="14" alt=""></sup> </span>
	  <?php }else {?>
	  	<span> <a target="_blank" <?php if ($planner['united_points'] || $planner['monthBonus']) {?> class="bubble-tip" <?php }?>tiptext="<?php if ($planner['united_points']) {?>战绩：<span class='level'><?php echo calGrade($planner['united_points'], 5)?></span><br>
		  	<?php }?>近1月中奖：<strong class='main-color-s'><?php echo number_format(ParseUnit($planner['monthBonus'], 1), 2)?></strong>元" href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $planner['uid'])), 'ENCODE'));?>">
		  	<?php echo uname_cut($users[$planner['uid']]['uname']);?>
		  	</a> </span>
	  <?php
			
}
		}
}
if (!empty($hotPlanner[1])) {
		foreach ( $hotPlanner[1] as $planner )
		{
			$hg1 = 0;
			list ( $hg, $ty, $yl, $xx ) = str_split ( str_pad ( $planner ['united_points'], 4, '0', STR_PAD_LEFT ) );
			if (strlen($planner ['united_points']) > 4) {
				$hg1 = substr($planner ['united_points'], 0, -4);
				if ($hg1 >= 2) $xx = 0;
				if ($hg1 >= 3) $yl = 0;
				if ($hg1 >= 4) {$ty = $hg = 0; $hg1 = 5;}
			}
			if ($planner ['isOrdering'])
			{
				?>
      	<span class="active"> <a target="_blank" <?php if ($planner['united_points'] || $planner['monthBonus']) {?> class="bubble-tip" <?php }?> tiptext="<?php if ($planner['united_points']) {?>战绩：<span class='level'><?php echo calGrade($planner['united_points'], 5)?></span><br>
	      	<?php }?>近1月中奖：<strong class='main-color-s'><?php echo number_format(ParseUnit($planner['monthBonus'], 1), 2)?></strong>元" href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $planner['uid'])), 'ENCODE'));?>">
	      	<?php echo uname_cut($users[$planner['uid']]['uname']);?>
	      	</a> <sup><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/popular-active.gif');?>" width="12" height="14" alt=""></sup> </span>
	  <?php }else {?>
	  	<span> <a target="_blank" <?php if ($planner['united_points'] || $planner['monthBonus']) {?> class="bubble-tip" <?php }?>tiptext="<?php if ($planner['united_points']) {?>战绩：<span class='level'><?php echo calGrade($planner['united_points'], 5)?></span><br>
		  	<?php }?>近1月中奖：<strong class='main-color-s'><?php echo number_format(ParseUnit($planner['monthBonus'], 1), 2)?></strong>元" href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $planner['uid'])), 'ENCODE'));?>">
		  	<?php echo uname_cut($users[$planner['uid']]['uname']);?>
		  	</a> </span>
	  <?php
			
}
		}
		}?></div>
		<?php 
	} else
	{
		?>
      <div class="popular-none">暂无合买红人! 赶紧<a target="_blank" href="/hall">发起合买</a>做红人~</div>
      <?php }?>
      </div>
	</div>

	<div class="hemai-table hemai_form">
<?php }?>
		<div class="ui-tab">
			<div class="ui-tab-hd">
				<div class="hemail-type">
          <?php if ($lid) {?>
		      <strong><?php echo $lidArr[BetCnName::getEgName($lid)]?></strong>
		      <?php if ($issues) {?>
		      <select id="issue" name="issue" class="vcontent"><?php foreach ($issues as $issue) {?><option <?php if ($search['issue'] == $issue) {?>selected<?php }?>><?php echo $issue?></option><?php }?></select>期
		      <?php } 
		      if (in_array ( $lid, array (SSQ, DLT, PLS, FCSD, QXC, QLC, SFC, RJ))) {
				$weekArr = array ('日', '一', '二', '三', '四', '五', '六');
				$endtime = $seFsendtime / 1000 - $lotteryConfig [$lid] ['united_ahead'] * 60;?>
		      <span id="hmstr">合买截止时间：<s class="main-color-s"><?php echo date('m-d H:i', $endtime)."（星期".$weekArr[date('w', $endtime)]."）"?></s></span>
		      <?php } else {?>
		      <span id="hmstr">合买截止时间：赛前或官方截止前<s class="main-color-s"><?php echo $lotteryConfig[$lid]['united_ahead']+$lotteryConfig[$lid]['ahead']?></s>分钟</span>
			  <?php }?>
		    <?php }else {?>
		    <strong>热门方案</strong>
		    <?php }?>
		    </div>
				<span class="ui-tab-note">合买方案进度满95%网站保底！</span>
			</div>
		</div>
<?php $orderArr = str_split ( $search ['order'] )?>   
			<input type="hidden" name="order" class="vcontent" value="<?php echo $search['order'] ? $search['order'] : '00'?>">
			<div class="filter-bar">
				<div class="filter-bar-l">
					<dl class="simu-select select-small" data-target="submit">
						<dt><span class="_scontent"><?php echo $search['state'] ? $stateArr[$search['state']] : '等待满员'?></span><i class="arrow"></i><input type="hidden" name="state" class="vcontent" value='<?php echo $search['state'] ? $search['state']:0?>'></dt>
						<dd class="select-opt"><div class="select-opt-in" data-name="state"><?php foreach ($stateArr as $key => $state) {?><a href="javascript:;" data-value="<?php echo $key?>"><?php echo $state?></a><?php }?></div></dd>
					</dl>
					<dl class="simu-select select-small" data-target="submit">
						<dt><span class="_scontent"><?php echo $search['money'] ? $moneyArr[$search['money']] : '不限金额'?></span><i class="arrow"></i><input type="hidden" name="money" class="vcontent" value='<?php echo $search['money'] ? $search['money']:0?>'></dt>
						<dd class="select-opt"><div class="select-opt-in" data-name="money"><?php foreach ($moneyArr as $key => $money) {?><a href="javascript:;" data-value="<?php echo $key?>"><?php echo $money?></a><?php }?></div></dd>
					</dl>
					<dl class="simu-select select-small" data-target="submit">
						<dt><span class="_scontent"><?php echo $search['commission'] ? (($search['commission'] == 1) ? '=0%' : "<=".($search['commission']-1)."%") : '不限佣金'?></span><i class="arrow"></i> <input type="hidden" name="commission" class="vcontent" value='<?php echo $search['commission'] ? $search['commission']:0?>'></dt>
						<dd class="select-opt"><div class="select-opt-in" data-name="commission"><a href="javascript:;" data-value="0">不限佣金</a><a href="javascript:;" data-value="1">=0%</a><?php for ($i=2; $i<=11; $i++) {?><a href="javascript:;" data-value="<?php echo $i?>"><=<?php echo $i-1?>%</a></a><?php }?></div></dd>
					</dl>
					<input type="text" class="nickname vcontent" name="uname" value="<?php echo $search['uname']?>" placeholder="发起人..." c-placeholder="发起人...">
					<button type="button" class="btn-ss btn-search submit">搜索</button>
					<a href="javascript:;" class="reset">恢复默认</a>
				</div>
				<div class="filter-bar-r">
					<a href="javascript:;" class="refresh"><i class="icon-font">&#xe625;</i>刷新</a>
					<div class="go-hemai">
						<a href="javascript:;" class="btn-ss btn-specail">发起合买<i></i></a>
						<div class="go-hemai-box">
							<ul>
								<li>
									<div class="title"><i class="icon-szc"></i>数字彩</div>
									<div class="cnt">
										<span><a target="_blank" href="/ssq">双色球</a></span>
										<span><a target="_blank" href="/dlt">大乐透</a></span>
										<span><a target="_blank" href="/fcsd">福彩3D</a></span>
										<span><a target="_blank" href="/pls">排列三</a></span> 
										<span><a target="_blank" href="/qlc">七乐彩</a></span>
										<span><a target="_blank" href="/qxc">七星彩</a></span>
										<span><a target="_blank" href="/plw">排列五</a></span>
									</div>
								</li>
								<li>
									<div class="title"><i class="icon-jjc"></i>竞技彩</div>
									<div class="cnt"><span><a target="_blank" href="/jczq">竞彩足球</a></span><span><a target="_blank" href="/jclq">竞彩篮球</a></span><span><a target="_blank" href="/rj">任选九</a></span><span><a target="_blank" href="/sfc">胜负彩</a></span></div>
								</li>

							</ul>
						</div>
					</div>
				</div>
			</div>
			<table class="mod-tableA">
				<thead>
					<tr>
						<th width="50"></th>
						<th width="80" class="tal">彩种</th>
						<th width="135" class="tal">发起人</th>
						<th width="142" class="tal filter-arrow <?php if ($orderArr[0] == 2) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="2">合买战绩<i></i></th>
						<th width="100" class="filter-arrow <?php if ($orderArr[0] == 1) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="1">参与人气<i></i></th>
						<th width="100" class="filter-arrow <?php if ($orderArr[0] == 0) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="0">进度+保底<i></i></th>
						<th width="90" class="tar filter-arrow <?php if ($orderArr[0] == 3) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="3">方案金额<i></i></th>
						<th width="90" class="tar">剩余金额</th>
						<th width="154">认购金额</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
        <?php if (! empty ( $orders ['data'] )) {
				foreach ( $orders ['data'] as $k => $order )
				{
					$lastmoney = ParseUnit($order['money']-$order['buyTotalMoney'], 1);
				?>
		        <tr <?php if ($order['ujoin']) {?>class="follow"<?php }?>>
						<td>
							<span <?php if ($order['isTop']) {?> class="recommend" <?php }?>><?php echo str_pad(($cpage-1)*$perPage+$k+1, 2, '0', STR_PAD_LEFT)?></span>
							<?php if ($order['ujoin']) {?><span class="icon-follow">已跟</span><?php }?>
							
						</td>
						<td class="tal"><?php echo BetCnName::getCnName($order['lid'])?></td>
						<td class="tal">
							<a href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $order['uid'])), 'ENCODE'));?>" target="_blank">
								<?php echo uname_cut($users[$order['uid']]['uname']);?>
							</a>
							<?php if ($users[$order['uid']]['isHot']) {?><span class="icon-poplular">红人</span><?php }?>
						</td>
						<td class="tal"><span class='level'><span class='level'><?php echo calGrade($order['united_points'], 5, $order['ujoin'] ? 3 : '')?></span></span></td>
						<td><?php echo $order['popularity']?>人参与</td>
						<td><?php echo $order['jd'] * 100?>%<?php if ($order['bd'] > 0) {?>+<?php echo round($order['bd'] * 100)?>%<span class="icon-guaranteed">保</span><?php }?></td>
						<td class="tar"><?php echo number_format(ParseUnit($order['money'], 1))?>元</td>
						<td class="tar"><em class="main-color-s"><?php  echo number_format($lastmoney);?></em>元</td>
		            <?php if ($search['state'] == 0) {?>
		            <td class="sPay">
		            	<input type="text" class="numInput" value="<?php echo $lastmoney > 5 ? 5 : $lastmoney?>" data-max="<?php echo $lastmoney?>">&nbsp;元 
		            	<a href="javascript:;" class="btn btn-main btn-buy <?php echo $showBind ? ' not-bind': '';?>" orderId="<?php echo $order['orderId']?>" cnName="<?php echo BetCnName::getCnName($order['lid'])?>" 
		            	issue="<?php echo $order['issue']?>" lid="<?php echo $order['lid']?>" playType="<?php echo BetCnName::getCnPlaytype($order['lid'], $order['playType'])?>">购买</a>
					</td>
		            <?php }elseif ($search['state'] == 1) {?>
		            <td class="fcw">已满员</td>
		            <?php }else {?>
		            <td class="fcw">已撤单</td>
		        	<?php }?>
		            <td><a href="/hemai/detail/hm<?php echo $order['orderId']?>" target="_blank">详情</a></td>
					</tr>
        	<?php }
        	} else {?>
        			<tr>
						<td colspan="9">
							<div class="no-data">
								<div class="no-data-img"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/img-noData.png');?>" width="61" height="67" alt=""></div>
								<div class="no-data-txt"><p>暂无合买记录</p><a href="/hall" target="_blank">马上发合买~</a></div>
							</div>
						</td>
					</tr>
		<?php }?>
        </tbody>
				<tfoot>
					<tr>
						<td colspan="10" class="tar">本页<?php echo count($orders['data'])?>条记录；共 <?php echo $pagenum?> 页</td>
					</tr>
				</tfoot>
			</table>
			<script>
      var orderArr = '<?php echo isset($search['order']) ? $search['order'] : '00'?>'.split('');
      </script>
			<!-- pagination -->
      <?php echo $pagestr?>
      <!-- pagination end -->
<?php if (!$this->is_ajax) {?> 
</div>
</div>
<script>
var target = '/hemai/<?php echo $this->act?>';
<?php if (in_array ( $lid, array (SSQ, DLT, PLS, FCSD, QXC, QLC, SFC, RJ))) {?>
var hmstr = '合买截止时间：<s class="main-color-s"><?php echo date('m-d H:i', $endtime)."（星期".$weekArr[date('w', $endtime)]."）"?></s>';
<?php }else {?>
var hmstr = '合买截止时间：赛前或官方截止前<s class="main-color-s"><?php echo $lotteryConfig[$lid]['united_ahead']+$lotteryConfig[$lid]['ahead']?></s>分钟';
<?php }?>
$(function(){
	var timer = null;
	$('.popular-group a').mouseenter(function() {
	    var that = $(this);
	    // clearTimeout(timer);
	    timer = setTimeout(function() {
	        $.bubble({
	            target: that,
	            position: 'b',
	            align: 'l',
	            content: that.attr('tiptext'),
	            width: 'auto',
	            autoClose: false
	        })
	    }, 500)
	}).mouseleave(function() {
	    clearTimeout(timer);
	    $('.bubble').hide();
	});

	(function() {
	    // 这边没有计算底部到什么位置停止
	    var fnSticky = $('.fn-sticky');
	    var fnStickyInner = $('.fn-sticky-inner');
	    var fnStickyTop = fnSticky.offset().top;
	    $(window).on('scroll', function() {
	        if ($(this).scrollTop() > fnStickyTop) {
	            fnStickyInner.addClass('fixed');
	        } else {
	            fnStickyInner.removeClass('fixed');
	        }
	    })
	})()
	
	new cx.vform('.hemai_form', {
	    submit: function(data) {
		    if (data.uname === $('.hemai_form').find('input[name=uname]').attr('placeholder')) data.uname = '';
	        var self = this;
	        $.ajax({
	            type: 'post',
	            url:  target,
	            data: data,
	            success: function(response) {
	           		$('.hemai_form').html(response);
	            	$('#hmstr').html(hmstr);
	            	fnPlaceholder();
	            }
	        });
	    }
	 });
	 <?php if ($issues) {?>
	 $('.hemai_form').on('change', '#issue', function(k){
		 if ($(this).val() === '<?php echo max($issues)?>') $('input[name=state]').val('0');
		 $('.hemai_form .submit').trigger('click');
	 })
	 <?php }?>
	 $('.hemai_form').on('click', ".filter-bar-l .reset", function(){
    	 $('.hemai_form').find('input[name=state]').val(0).parents('dt').find('span._scontent').html('等待满员');
    	 $('.hemai_form').find('input[name=money]').val(0).parents('dt').find('span._scontent').html('不限金额');
    	 $('.hemai_form').find('input[name=commission]').val(0).parents('dt').find('span._scontent').html('不限佣金');
    	 $('.hemai_form').find('input[name=order]').val('00');
    	 $('.hemai_form').find('.nickname').val('');
    	 $('.hemai_form').find('.submit').trigger('click');
   	}).on('click', '.refresh', function(){
	   $('.hemai_form').find('.submit').trigger('click');
	}).on('click', "table thead .filter-arrow", function(){
       if ($(this).hasClass('filter-arrow-t')) {
      	 $('.hemai_form').find("input[name=order]").val(orderArr[0]+'1');
       }else if ($(this).hasClass('filter-arrow-b')) {
      	 $('.hemai_form').find("input[name=order]").val(orderArr[0]+'0');
       }else {
      	 $('.hemai_form').find("input[name=order]").val($(this).data('value')+'0');
       }
       $('.hemai_form').find('.submit').trigger('click');
   	}).on('click', '.btn-buy', function(){
	    var buymoney = $(this).parents('td').find('.numInput').val(),orderId = $(this).attr('orderId');
		if (!$.cookie('name_ie')) {//登录过期
			$(this).addClass('needTigger');
			cx.PopAjax.login(1);
			return;
	    }
	    if ($(this).hasClass('not-bind')) return;
	    cx.castCb({orderId:orderId, buyMoney:buymoney}, {ctype:'paysearch', orderType:4,buyMoney:buymoney,msgconfirmCb:function(){$('.hemai_form').find('.submit').trigger('click');}});
	}).on('click blur', '.numInput', function(){
		var $this = $(this);
        if ((/^(.*)\D+(.*)$/.test($(this).val()))) $this.val($(this).val().replace(/\D+/, ''));
        if (!$this.val()) $this.val(1);
        if ($this.data('max') && $this.val() >= parseInt($this.data('max'))) $this.val($this.data('max'));
	})

});

</script>
<?php }?>