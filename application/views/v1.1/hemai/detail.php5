<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/hemai.min.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-custom.min.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-public.min.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/order.js');?>"></script>
<div class="wrap p-hemai p-hemai-detail">
      <!-- 彩票信息 -->
      <div class="lotteryTit" style="height:auto;">
        <div class="lottery-info">
          <div class="lottery-img lottery-<?php echo $orderInfo['enName']?>"><svg width="320" height="320"><image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" /></svg></div>
          <div class="lottery-info-txt">
            <div class="th"><h1><?php echo $orderInfo['cnName']?></h1><?php echo in_array($orderInfo['lid'], array(SSQ, DLT, QLC, QXC, PLS, FCSD, PLW, SFC, RJ)) ? "<b class='sIssue'>第".$orderInfo['issue']."期" : ''?></b><span class="sTime">发起时间：<?php echo $orderInfo['created']?></span><span class="sCode">订单编号：hm<?php echo $orderInfo['orderId']?></span><a href="javascript:;" class="sub-color aCopyBtn share-copy">点击复制链接</a></div>
            <div class="tb">
              <div class="tit">
                <i class="iSponsor"></i><a href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $orderInfo['uid'])), 'ENCODE'));?>" target="_blank" class="aName"><?php echo $orderInfo['uname']?></a><td>
                <span class='level'><?php echo calGrade($orderInfo['points'], 5, 3)?></span><span class="sDes">已买约<?php echo round($orderInfo['buyMoney']*100/$orderInfo['money'])?>%<?php if ($orderInfo['guaranteeAmount']) { echo "，承诺保底约".round($orderInfo['guaranteeAmount']*100/$orderInfo['money'])."%"; }?></span>
                <a href="javascript:;" data-lid="<?php echo $orderInfo['lid']; ?>" data-uid="<?php echo $user['uid']; ?>" class="btn-ss btn-main fr gendan">定制跟单</a>
              </div>
              <p class="pTips"><?php if ($this->uid == $user['uid']) { ?>
                            <?php
                            if ($user['introduction_status'] == 0 || $user['introduction_status'] == 1) {
                                echo $user['introduction']?$user['introduction']:'想中大奖的，抓紧跟单啦！';
                            } else {
                                echo '想中大奖的，抓紧跟单啦！';
                            }
                            ?>
                        <?php }else{
                            if ($user['introduction_status'] == 1) {
                                echo $user['introduction'];
                            }else{
                                echo '想中大奖的，抓紧跟单啦！';
                            }
                        } ?></p>
            </div>
          </div>
        </div>
      </div>
      <!-- 彩票信息 -->

      <div class="homepage_detail">
        <i class="homepage_detail_border"></i>
        <div class="homepage_detail_con">
          <!-- 左侧栏 -->
          <div class="homepage-side">
            <ul class="ulBuy clearfix">
              <li><span class="sTit">截止时间：</span><span class="sTime"><em class="emNum"><?php echo date('m-d H:i', $orderInfo['endTime'])?></em>（星期<?php echo $weekdayarr[date('w', $orderInfo['endTime'])]?>）</span></li>
              <li><span class="sTit">合买进度：</span><span class="sSchedule"><i class="iLine"><em style="width:<?php echo $orderInfo['buyTotalMoney'] * 100/$orderInfo['money']?>%"></em></i><em class="emNum"><?php echo round($orderInfo['buyTotalMoney'] * 100/$orderInfo['money'], 2)?>%</em></span><?php if ($orderInfo['qb'] == 1) {?><img src="/caipiaoimg/v1.1/img/icon-all-warranty.png" width="24" height="14" alt="保"><?php }?></li>
              <li><span class="sTit">方案金额：</span><span class="sMoney"><em class="emNum"><?php echo number_format(ParseUnit($orderInfo['money'], 1))?></em>&nbsp;元</span></li>
              <li><span class="sTit">剩余金额：</span><span class="sLastMoney"><em class="emNum"><?php $lastmoney = ParseUnit(($orderInfo['money'] - $orderInfo['buyTotalMoney']), 1); echo number_format($lastmoney)?></em>&nbsp;元</span><a href="javascript:;" target="_self" class="aRefresh"><?php if ($orderInfo['buyTotalMoney'] < $orderInfo['money'] && time()< $orderInfo['endTime'] && $orderInfo['status'] < 600) {?><i class="icon-font">&#xe625;</i>刷新</a></li>
              <li><span class="sTit">我要认购：</span><span class="sPay"><input type="text" class="number" value='<?php echo $lastmoney > 5 ? 5 : $lastmoney?>'>&nbsp;元</span><?php }?></li>
              <li><a href="javascript:;" target="_self" class="btn btn-buy <?php if ($orderInfo['buyTotalMoney'] < $orderInfo['money'] && !in_array($orderInfo['status'], array('600', '610', '620')) && $orderInfo['endTime'] > time()) {?>btn-main <?php echo $showBind ? ' not-bind': '';?>">立即认购<?php }else {?> btn-disabled">方案已<?php echo in_array($orderInfo['status'], array(600, 610, 620)) ? '撤单' : ($orderInfo['buyTotalMoney'] == $orderInfo['money'] ? '满员' : '截止');}?></a>
              <?php if (in_array($orderInfo['status'], array('600', '610', '620')) || $orderInfo['buyTotalMoney'] == $orderInfo['money'] || $orderInfo['endTime'] <= time()) {?>
              <p class="pOther"><a href="/hemai/<?php echo str_replace(array('rj', 'plw'), array('sfc', 'pls'), BetCnName::getEgName($orderInfo['lid']))?>" target="_blank">您可以选择参加其他合买></a></p>
              <?php }?>
              </li>
            </ul>
            <ul class="ulInf clearfix">
              <li>方案状态：<em><?php echo parse_hemai_status($orderInfo['status'], $orderInfo['my_status'])?></em></li><li>盈利佣金：<?php echo $orderInfo['commissionRate']?>%<?php if ($orderInfo['commission']) {echo "（<em class='main-color-s'>".number_format(ParseUnit($orderInfo['commission'], 1), 2)."元</em>）";}?></li>
              <li>发起人买：<?php echo number_format(ParseUnit($orderInfo['buyMoney'], 1))?>&nbsp;&nbsp;元&nbsp;(约占<?php echo floor($orderInfo['buyMoney']*100/$orderInfo['money'])?>%)</li>
              <?php if ($orderInfo['guaranteeAmount']) {?><li>承诺保底：<?php echo number_format(ParseUnit($orderInfo['guaranteeAmount'], 1));if ($orderInfo['guaranteeAmount']) {?>&nbsp;元&nbsp;(约占<?php echo floor($orderInfo['guaranteeAmount']*100/$orderInfo['money'])?>%)<?php }?></li><?php }?>
              <li>是否公开：<?php echo $openStatus[$orderInfo['openStatus']]?></li><?php if ($orderInfo['cname']) {?><li>出票站点：由<?php echo $orderInfo['cname']?>出票</li><?php }?>
            </ul>
            <!-- 大乐透 - 乐善奖 -->
            <?php if($showdetail && !empty($lsDetail['detail'])): ?>
            <div class="ulInf lsj-link-group">
                <a href="/hemai/lsDetail/<?php echo $orderInfo['orderId']; ?>" target="_blank">查看订单乐善码<?php if($lsDetail['totalMargin'] > 0):?>（中奖）<?php endif;?> &gt;</a>
                <a href="/info/csxw/132122" target="_blank" class="only-icon" title="什么是乐善码"><i class="icon-font"></i></a>
            </div>
            <?php endif; ?>
          </div>
          <!-- 左侧栏 -->

          <!-- 右侧主内容 -->
          <div class="homepage-main">
            <div class="th clearfix">
            <?php if ($orderInfo['uid'] == $this->uid && !in_array($orderInfo['status'], array(600, 610, 620)) && $orderInfo['buyTotalMoney'] + $orderInfo['guaranteeAmount'] < $orderInfo['money']/2 && $orderInfo['endTime'] > time()) {?>
                <a href="javascript:;" class="btn-ss btn-ss-bet cancelOrder">发起人撤单</a>
            <?php }?>
            <?php if ($orderInfo['uid'] == $this->uid && !in_array($orderInfo['lid'], array(JCZQ, JCLQ, SFC, RJ))){ ?>
             <a href="/<?php echo BetCnName::getEgName($orderInfo['lid']);?>?orderId=<?php echo $orderInfo['orderId'];?>" class="btn-ss btn-ss-bet">继续预约此方案</a>
            <?php }?>   
            <ul class="mod-tab-hemai clearfix">
            	<li class="current"><a href="javascript:;">方案内容</a><i class="iTabLine"></i></li>
            	<?php if (in_array($orderInfo['lid'], array(JCZQ, JCLQ)) && $orderInfo['status'] >= 500 && !in_array($orderInfo['status'], array(600, 610, 620)) && $orderInfo['playType'] == '7') {?><li><a href="javascript:;">奖金优化明细</a><i class="iTabLine"></i></li><?php }?>
            	<?php if (in_array($orderInfo['lid'], array(JCZQ, JCLQ, SFC, RJ)) && $orderInfo['status'] >= 500 && !in_array($orderInfo['status'], array(600, 610, 620))) {?><li><a href="javascript:;">出票明细</a><i class="iTabLine"></i></li><?php }?>
            	<li><a href="javascript:;">参与用户<i class="iTips"><?php echo $orderInfo['popularity']?></i></a><i class="iTabLine"></i></li>
            	<li><a href="javascript:;">我的参与<?php if ($orderInfo['suid']) {?><sup class="has-order"></sup><?php }?></a></li>
            </ul>
            <i class="iBottomLine"></i></div>
              <div class="mod-tab-hemai-con">
              	<div class="mod-tab-item form-info" data-action="info" style="display: block"><?php echo $this->load->view('v1.1/hemai/detail_info')?></div>
              	<?php if (in_array($orderInfo['lid'], array(JCZQ, JCLQ)) && $orderInfo['status'] >= 500 && !in_array($orderInfo['status'], array(600, 610, 620)) && $orderInfo['playType'] == '7') {?>
              	<div class="mod-tab-item form-bonusOpt" data-action="bonusOpt"></div>
              	<?php }?>
              	<?php if (in_array($orderInfo['lid'], array(JCZQ, JCLQ, SFC, RJ)) && $orderInfo['status'] >= 500 && !in_array($orderInfo['status'], array(600, 610, 620))) {?>
              	<div class="mod-tab-item form-split" data-action="split"></div>
              	<?php }?>
              	<div class="mod-tab-item " data-action="user"><p class="total-prize-money"><i class="icon-font">&#xe626;</i>已有<em class="emNum"><?php echo $orderInfo['popularity']?></em>人次，共认购了<em class="emNum"><?php echo number_format(ParseUnit($orderInfo['buyTotalMoney'], 1))?></em>元</p><div class="form-user"></div></div>
              	<div class="mod-tab-item form-my" data-action="my"></div>
              </div>
          </div>
          <!-- 右侧主内容 -->

        </div>
        <i class="homepage_detail_border"></i>
      </div>
	<?php if (empty($this->uinfo['email'])) {?>
	<a href="javascript:;" class="fixed bind-email bind_email">绑定邮箱，出票给你发邮件<span class="close"></span></a>
	<?php }?>
    </div>
    <input type="text" id="url" style="opacity:0.0; filter:alpha(opacity=0);" value='<?php echo $this->config->item('base_url')?>hemai/detail/hm<?php echo $orderInfo['orderId']?>'>
    
    <script>
    var buyMoneyModifier = new cx.AdderSubtractor('.sPay'), money = '<?php echo $orderInfo['money']?>', target = location.href;
    $(function(){
    	new cx.vform('.form-my', {
            submit: function(data) {
                var self = this;
                $.ajax({type: 'post', url: target, data: data, success: function(response) {$('.form-my').html(response)}});
            }
    	 });

    });
    $(function(){
    	new cx.vform('.form-user', {
            submit: function(data) {
                var self = this;
                $.ajax({type: 'post', url: target, data: data, success: function(response) {$('.form-user').html(response)}});
            }
    	 });
    });
    $('.bind-email').click(function(){
    	if (!$.cookie('name_ie')) {//登录过期
    		cx.PopAjax.login();
            return;
        }
    	window.open("/safe/bindEmail");    
    })
    $('.bind-email .close').click(function(){
        $('.bind-email').remove();
    })
    $('.aRefresh').click(function(){
        $.post('/hemai/getOrderState', 
            {orderId:'<?php echo $orderInfo['orderId']?>'}, 
            function(data){
                var pcnt = Math.round(data.buyTotalMoney * 10000 / money) / 100;
                $('.sSchedule').html("<i class='iLine'><em style='width:"+pcnt+"%'></em></i><em class='emNum'>"+pcnt+"%</em>");
                $('.sLastMoney').html("<em class='emNum'>"+fmoney(parseInt((money - data.buyTotalMoney), 10)/100, 3)+"</em>&nbsp;元");
                if ($.inArray(data.status, ['600', '610', '620']) > -1) {
                    $('.ulBuy li:last').html("<a href='javascript:;' target='_self' class='btn btn-disabled btn-buy'>方案已撤单</a><p class='pOther'><a href='/hemai' target='_blank'>您可以选择参加其他合买></a></p>");
                    $('.aRefresh').remove();
                    $('.sPay').parents('li').remove();
                }else if (money == data.buyTotalMoney) {
                	$('.ulBuy li:last').html("<a href='javascript:;' target='_self' class='btn btn-disabled btn-buy'>方案已满员</a><p class='pOther'><a href='/hemai' target='_blank'>您可以选择参加其他合买></a></p>");
                    $('.aRefresh').remove();
                    $('.sPay').parents('li').remove();
                }else if(data.end) {
                	$('.ulBuy li:last').html("<a href='javascript:;' target='_self' class='btn btn-disabled btn-buy'>方案已截止</a><p class='pOther'><a href='/hemai' target='_blank'>您可以选择参加其他合买></a></p>");
                    $('.aRefresh').remove();
                    $('.sPay').parents('li').remove();
                }
                $('.ulInf em:first').html(cx.HmOrder.getStatus(data.status));
                $('.mod-tab .iTips, .mod-tab-con .mod-tab-item:eq(1) .total-prize-money em:first').html(data.popularity);
                $('.mod-tab-con .mod-tab-item:eq(1) .total-prize-money em:last').html(fmoney(data.buyTotalMoney / 100));
            }
        )
    })
    $('.btn-buy:not(.btn-disabled)').click(function(){
        var buymoney = buyMoneyModifier.getValue();
    	if (!$.cookie('name_ie')) {//登录过期
    		$(this).addClass('needTigger');
    		cx.PopAjax.login(1);
            return;
        }
        if ($(this).hasClass('not-bind')) return;


        cx.castCb({orderId:'<?php echo $orderInfo['orderId']?>', buyMoney:buymoney}, {ctype:'paysearch', orderType:4, buyMoney:buymoney,msgconfirmCb:function(){location.reload();},btns : [{type: 'confirm', txt: '确定', href: 'javascript:;'}]});
    })
    
    $('.cancelOrder').click(function(){
        cx.Confirm({single: '<i class="icon-font">&#xe611;</i>发起人撤单则整个方案撤单，是否继续撤单？',
        		confirmCb: function() {
                	$.post('hemai/cancelOrder', {orderId: '<?php echo $orderInfo['orderId']?>'}, 
                       function(response) {
	                        cx.Confirm({single:"<i class='icon-font'>&#xe611;</i>"+response.msg, btns: [{type: 'confirm',href: 'javascript:;',txt: '确定'}], confirmCb:function(){location.reload();}})
                        }, 'json'
                    );
                }})
    })
    $(function () {
    	$('body').on('click', ".share-copy", function(){
			$("#url").select(); //选择对象 
	        document.execCommand("Copy"); //执行浏览器复制命令
	        cx.Alert({content:"<i class='icon-font'>&#xe611;</i>复制合买链接成功"});
		})

    	$(".mod-tab-hemai").tabPlug({
            cntSelect: '.mod-tab-hemai-con',
            menuChildSel: 'li',
            onStyle: 'current',
            cntChildSel: '.mod-tab-item',
            eventName: 'click',
            callbackFun: function (k, e) {
                switch (e.data('action')) {
                	case 'my':
                		if (!$.cookie('name_ie')) {
                        	cx.PopAjax.login(1);
                            return ;
                        }
                    	$.post(location.href+'?cpage=1', {orderId:'<?php echo $orderInfo['orderId']?>', action:'my'}, function(data){e.html(data)})
                    	break;
                    default:
                        action = e.data('action');
                    	$.post(location.href+'?cpage=1', {orderId:'<?php echo $orderInfo['orderId']?>', action:action}, function(data){$('.form-'+action).html(data)})
                    	break;
                }
            }
        });
    })
    function fmoney(s) 
	{   
		s = s.toString().split("").reverse().join("").substring(0, s.toString().length);
		sArr = s.replace(/(\d{3})/g, '$1,').split("").reverse();
		if (sArr[0] == ',') delete sArr[0];
		return sArr.join("");
	}


    $(".homepage-main").on("mouseenter mouseleave", ".jj-arrow", function(event){
	    if( event.type == "mouseenter"){
	    	$.bubble({
	            target: this,
	            position: 'b',
	            align: 'c',
	            content: $(this).attr('tiptext'),
	            width:'auto',
	            autoClose: false
	        })    
	    }else if(event.type == "mouseleave" ){
	    	$('.bubble').hide(); 
	    }           
	});
	$('.p-hemai').on('click', '.gendan', function () {
            var  uid = $(this).data('uid');
            var  lid = $(this).data('lid');
            if (!$.cookie('name_ie')) {//登录过期
            	$(this).addClass('needTigger');
                cx.PopAjax.login(1);
                return;
            }
            if ($(this).hasClass('not-bind'))
                return;
            $.ajax({
                type: "post",
                url: "/pop/gendan",
                data: {
                    'uid': uid,
                    'lid': lid,
                    'version':version
                },
                success: function (res) {
                    if (res==1) {
                        cx.Alert({content: '<i class="icon-font">&#xe600;</i>您已定制发起人的方案，换个彩种试试吧',
                            confirmCb: function () {
                                $('.gendan').find('.submit').trigger('click');
                        }});
                        return false;
                    }
                    if (res==2) {
                        cx.Alert({content: '<i class="icon-font">&#xe600;</i>定制人数已达上限，换个彩种试试吧',
                            confirmCb: function () {
                                $('.gendan').find('.submit').trigger('click');
                        }});
                        return false;
                    }
                    $('body').append(res);
                    cx.PopCom.show('.pop-id');
                    cx.PopCom.close('.pop-id');
                    cx.PopCom.cancel('.pop-id');
                }
            });
        });
    </script>
