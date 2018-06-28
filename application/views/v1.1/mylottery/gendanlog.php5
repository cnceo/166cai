<?php if(!$this->is_ajax):?>
<?php $this->load->view('v1.1/elements/user/menu');?>
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/hemai.min.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-public.min.css');?>" rel="stylesheet" />
<div class="l-frame-cnt">
<div class="uc-main">
	<h2 class="tit">跟单记录</h2>
	<!--表单筛选 begin-->
	<div class="filter-oper">
		<div class="lArea gendanlog-form">
			<input type='hidden' class='submit' >
			<dl class="simu-select select-small" data-target='submit'>
	            <dt>
	            	<span class='_scontent'>最近六个月</span><i class="arrow"></i>
	            	<input type='hidden' name='date' class="vcontent" value='3' >
	            </dt>
	            <dd class="select-opt">
	            	<div class="select-opt-in" data-name='date'>
                        <?php foreach( $dateSpan as $key => $val ): ?>
	            		<a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?></a>
                        <?php endforeach; ?>
		            </div>
	            </dd>
	        </dl>
	        <dl class="simu-select select-small" data-target='submit'>
	            <dt>
                    <span class='_scontent'>所有彩种</span><i class="arrow"></i>
	            	<input type='hidden' name='lid' class="vcontent" value='0' >
	            </dt>
	            <dd class="select-opt">
	            	<div class="select-opt-in" data-name='lid'>
                        <?php foreach( $betType as $key => $val ): ?>
                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?></a>
                        <?php endforeach; ?>
		            </div>
	            </dd>
	        </dl>
	        <label for="is_chase"><input type="checkbox" class="ckbox vcontent" name="is_chase" id="is_chase">进行中</label>
	        <label for="has_bonus"><input type="checkbox" class="ckbox vcontent" name="has_bonus" id="has_bonus">只看中奖</label>
		</div>
	</div>
<!--表格 begin-->
<div id='container-gendanlog'>
<?php endif;?>
<div>
		<table class="mod-tableA">
			<thead>
				<tr>
					<th width="10%">定制时间</th>
					<th width="12%">彩种</th>
                                        <th class="tal" width="17%">订单信息</th>
                                        <th width="10%">每次认购</th>
					<th width="13%">已跟/总次数</th>
					<th width="13%">订单状态</th>
					<th width="14%">总奖金（元）</th>
					<th width="14%">操作</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($orders)):?>
				<?php foreach ($orders as $order):?>
				<?php 
					$ctime = strtotime($order['effectTime']);
				?>
				<tr>
					<td><strong><?php echo date('m月d日', $ctime);?></strong><br /><?php echo date('H:i:s', $ctime);?></td>
					<td>
					<a href="/<?php echo BetCnName::getEgName($order['lid']);?>" target = "_blank">
						<?php echo BetCnName::getCnName($order['lid']); ?>
					</a>
					</td>
                                        <td class="tal">发起人:<a href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $order['uid'])), 'ENCODE')); ?>?gendan=1"><?php echo uname_cut($order['uname'], 1, 5);?></a><br>
                                        <?php  echo $order['payType']==0?'预付扣款':'实时扣款'?>
                                        </td>
					<td><?php echo ($order['followType']==0)?($order['buyMoney']/100).'元':$order['buyMoneyRate'].'%'; ?></td>
                                        <td><span class="num-red"><?php echo $order['followTimes'];?></span>/<?php echo $order['followTotalTimes']; ?></td>
					<td><?php echo parse_gendan_order_status($order['status'],$order['my_status']); ?></td>
					<td><?php echo $order['totalMargin'] > 0 ? "<div class='bingo'>".number_format(ParseUnit($order['totalMargin'], 1), 2)."</div>" : ($order['status'] > 1 ? '0.00' : '--'); ?></td>
                                        <td class="caozuo">
				            <a target="_blank" href="/hemai/gdetail/gd<?php echo $order['followId']; ?>" style="display:block;">查看详情</a>
                                            <?php if ($order['status'] == 1) {?>
                                                <a href="javascript:;" class="btns gendan-cancel" data-id="<?php echo $order['followId']?>" data-lid="<?php echo $order['lid']?>" data-uid="<?php echo $order['uid']?>" style="display:block;">停止跟单</a>
                                            <?php }else {?>
                                                <a href="javascript:;" class="btns gendan-contine" data-lid="<?php echo $order['lid']?>" data-uid="<?php echo $order['uid']?>" style="display:block;">继续跟单</a>
                                            <?php } ?>
                                        </td>
				</tr>
				<?php endforeach;?>
			<?php else :?>
				<tr>
					<td colspan="8" class="no-data">
						<p>没有记录,去 <a class="main-color-s" href="wallet/recharge">充值</a> 或 <a class="main-color-s" href="hall">购买彩票</a> ,大奖等你拿!</p>
					</td>
				</tr>
			<?php endif;?>
			</tbody>
			<?php if (!empty($orders)) {?>
			<tfoot>
				<tr>
					<td colspan="7" class="tar">
						<div class="fr tar table-page">
							<span class="mlr10">本页<em class="mlr5"><?php echo count($orders);?></em>条记录</span><span>共<em class="mlr5"><?php echo $pagenum;?></em>页</span>
						</div>
					</td>
				</tr>
			</tfoot>
			<?php }?>
		</table>
		<?php if (!empty($orders)) { echo $pagestr;}?>
</div>
<?php if(!$this->is_ajax):?>
</div>
</div>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/lottery_gaoji.min.js');?>"></script>
<script type="text/javascript">
var target = '/mylottery/gendanlog';
$(function(){
	 new cx.vform('.gendanlog-form', {
		 	checklogin: true,
	        submit: function(data) {
	            var self = this;
	            $.ajax({
	                type: 'post',
	                url:  target,
	                data: data,
	                success: function(response) {
	            		$('#container-gendanlog').html(response);
	                }
	            });
	        }
	 });

     $('#is_chase, #has_bonus').on('click', function(){
        $('.gendanlog-form .submit').trigger('click');
     });

});
$('#container-gendanlog').on("click",".gendan-cancel",function(){
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
        },
        cancel:'取消'
    });
});
$('#container-gendanlog').on("click",".gendan-contine",function(){
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
</div>
<?php $this->load->view('v1.1/elements/user/menu_tail');?>
<?php endif;?>