<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/hemai.min.css');?>" rel="stylesheet" type="text/css" /> 
<div class="wrap p-chasenum">
        <div class="chasenum-hd">
            <div class="lottery-logo notice-<?php echo $orderInfo['enName'];?>">
            	<div class="lottery-img">
                    <svg width="224" height="224">
                               <image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg');?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png');?>" width="224" height="224"></image>
                    </svg>
                </div>
            </div>
            <p><span class="lottery-name"><?php echo $orderInfo['cnName'];?></span><span class="chasenum-txt specil-color"><?php echo parse_gendan_order_status($orderInfo['status'],$orderInfo['my_status'])?></span></p>
            <ul>
                <li>订单编号：<?php echo $orderInfo['followId']?></li>
                <li>创建时间：<?php echo $orderInfo['created']?></li>
                <li>定制时间：<?php echo $orderInfo['effectTime']?></li>
            </ul>
        </div>

        <div class="chasenum-info">
            <h2 class="chasenum-mod-hd">跟单信息</h2>
            <div class="chasenum-mod-bd">
                <ul class="chasenum-list">
                    <li class="chasenum-list-item">发起人：<b><?php echo $orderInfo['uname']; ?></b></li>
                    <li class="chasenum-list-item">扣款方式：<b>
                        <?php echo $orderInfo['payType']==0?'预付扣款（预付总额：'.($orderInfo['totalMoney']/100).'元）':'实时扣款'; ?>
                        </b></li>
                    <li class="chasenum-list-item">每次认购：<b><?php echo $orderInfo['followType']==1?$orderInfo['buyMoneyRate'].'%,但不超过'.($orderInfo['buyMaxMoney']/100):($orderInfo['buyMoney']/100); ?>元</b></li>
                    <li class="chasenum-list-item">跟单进度：<b>共<?php echo $orderInfo['followTotalTimes']?>次，已跟 <i class="num"><?php echo $orderInfo['followTimes']?></i>次<?php if($orderInfo['status']==3){ echo "，取消".($orderInfo['followTotalTimes']-$orderInfo['followTimes']).'次'; }?></b></li>
                </ul>
				<?php 
                                if($orderInfo['totalMargin']>0) {
                                    $imgStr = 'zj';
                                    $str = '<p>恭喜您已累积中奖<em class="specil-color">'.number_format(ParseUnit($orderInfo['totalMargin'], 1), 2).'</em>元！</p>';                                       
                                }
                                if($orderInfo['totalMargin']==0) {
                                   if($orderInfo['my_status']==1){
                                        $imgStr = 'zhwcwzj';
                                        $str = '<p>不要灰心，也许下个大奖就是你！</p>';                                 
                                   }else{
                                        $imgStr = 'fkcg';
                                        $str = '<p>定制成功，耐心等待大奖的降临吧！</p>';                                    
                                   }                                     
                                }
                                ?>    
                <div class="chasenum-info-result zh-<?php echo $imgStr?>"><?php echo $str;
                if ($orderInfo['status'] == 1) {?>
                        <a href="javascript:;" data-id="<?php echo $orderInfo['followId']?>" data-lid="<?php echo $orderInfo['lid']?>" data-uid="<?php echo $orderInfo['puid']?>" class="lnk-btn gendan-cancel">停止跟单</a>
                    <?php }else {?>
                    	<a href="javascript:;" data-lid="<?php echo $orderInfo['lid']?>" data-uid="<?php echo $orderInfo['puid']?>" class="lnk-btn gendan-contine">继续跟单</a>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="chasenum-detail">
            <h2 class="chasenum-mod-hd">跟单详情</h2>
            <table>
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>认购时间</th>
                        <th>期次</th>
                        <th>方案金额(元)</th>
                        <th>认购金额(元)</th>
                        <th>订单状态</th>
                        <th>我的奖金(元)</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($orders as $k => $order) {?>
                	<tr>
                        <td><?php echo $k+1?></td>
                        <td><?php echo $order['created']?></td>
                        <td><?php echo $order['issue']?></td>
                        <td><?php echo number_format(ParseUnit($order['money'], 1), 2);?></td>
                        <td><?php echo number_format(ParseUnit($order['buyMoney'], 1), 2);?></td>
                        <td>
                        <?php echo parse_hemai_status($order['status'], $order['my_status']);?>
                        </td>
                        <td>
                        <?php 
                        if ($order['status'] == 2000) {?>
                        	<div class="bingo"><?php echo number_format(ParseUnit($order['margin'], 1), 2)?></div>
		        <?php }else { echo '--';}?>
                        </td>
                        <td>
                        <?php if (empty($order['orderId'])) {?>--<?php }else {?>
							<a href="/hemai/detail/hm<?php echo $order['orderId']; ?>" target="_blank">查看详情</a>
						<?php }?>
                        </td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
        </div>
    </div>
<script>
$('.chasenum-info-result').on("click",".gendan-cancel",function(){
    var orderId=$(this).data("id");
    if (!$.cookie('name_ie')) {//登录过期
        $(this).addClass('needTigger');
        cx.PopAjax.login(1);
        return;
    }
    if ($(this).hasClass('not-bind'))
        return;
    cx.Alert({
            content:'<i class="icon-font">&#xe611;</i>确认要停止跟单吗？',
            cancel:'取消',
            addtion:1,
            confirmCb: function(){
            $.ajax({
                type: "post",
                url: "/hemai/cancelGendan",
                data: {
                    'orderId': orderId
                },
                dataType: "json",
                success: function (res) {
                    cx.Alert({content: '<div class="fz18 pt10 yahei c333 pop-new"><div class="pop-txt text-indent" style="margin-bottom:0px;text-align:left;"><i class="icon-font">&#xe600;</i>'+res.msg+'</div></div>',
                        confirmCb: function () {
                            if(res.code==200){
                                location.reload();
                            }   
                    }});
                }
            });
        }
    });
});
$('.chasenum-info-result').on("click",".gendan-contine",function(){
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