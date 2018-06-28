<?php $this->load->view("templates/head") ?>
<style type="text/css">
	.redSpan{color:#f00;}
	.modifyCofigBox {margin-top:10px;}
	.modifyCofigBox div{ height: 30px; width: 300px; font-size: 14px; padding-left: 20px;padding-right: 20px;}
	.modifyCofigBox div input{width: 120px;border: 1px solid #ccc;color:#000;text-indent: 2px;}
	.modifyCofigBox div strong{font-weight: normal;text-align: right;display: inline-block; width: 75px;}
</style>
<div class="path">您的位置：<a href="javascript:;">用户成长管理</a>&nbsp;&gt;&nbsp;<a href="/backend/Growth/stockManage">库存管理</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd">
	    <ul>
		    <li ><a href="/backend/Growth/pointMonitor">积分监测</a></li>
		    <li ><a href="/backend/Growth/exchangeLog">兑换记录</a></li>
		    <li class="current"><a href="/backend/Growth/stockManage">库存管理</a></li>
		    <li ><a href="/backend/Growth/pointDetail">积分明细</a></li>
	    </ul>
  	</div>
  	<div class="data-table-list mt20">
        <table>
            <colgroup>
                <col width="90"/>
                <col width="90"/>
                <col width="90"/>
                <col width="110"/>
                <col width="110"/>
                <col width="110"/>
                <col width="90"/>
                <col width="90"/>
                <col width="90"/>
                <col width="120"/>
            </colgroup>
            <thead>
	            <tr>
	                <th>红包金额</th>
	                <th>使用条件</th>
	                <th>适用彩种</th>
	                <th>青铜白银需积分</th>
	                <th>黄金铂金需积分</th>
	                <th>钻石需积分</th>
	                <th>今日发放</th>
	                <th>明日发放</th>
	                <th>今日剩余库存</th>
	                <th>操作</th>
	            </tr>
            </thead>
            <tbody>
             <form action="" method="post" id='modifyStock'>
            <?php foreach ($data as $k => $v): ?>
                <tr>
                    <td><?php echo  m_format($v['money']);?>&nbsp;元</td>
                    <td><?php echo $v['use_desc'];?></td>
                    <td><?php echo $v['c_name'];?></td>
                    <?php $useParams = json_decode($v['use_params'],true);?>
                    <td><?php echo $useParams['lv3'];?></td>
                    <td><?php echo $useParams['lv4'];?></td>
                    <td><?php echo $useParams['lv6'];?></td>
                    <td><?php echo $v['today_out'];?></td>
                    <td><?php echo $v['next_out'];?></td>
                    <td style="color:#f00;"><?php echo $v['today_out'] - $v['already_out'];?></td>
                    <input type="hidden" name='id[]'  value='<?php echo $v['id'];?>' />
                    <input type="hidden" name='rid[]'  value='<?php echo $v['rid'];?>' />
                    <input type="hidden" name='next_out[]' class='next_out' value='<?php echo $v['next_out'];?>' />
                    <input type="hidden" name='emptyStock[]' class='emptyStockInput' value='0' />
                    <input type="hidden" name='modifyConfig[]' class='modifyConfigInput' value='' />
                    <td><a href="javascript:;" class='emptyStock'>库存清零</a>&nbsp;&nbsp;<a href="javascript:;" class='modifyConfig' data-money="<?php echo  m_format($v['money']);?>" data-qtby="<?php echo $useParams['lv3'];?>" data-hjbj="<?php echo $useParams['lv4'];?>" data-zs="<?php echo $useParams['lv6'];?>" next-out="<?php echo $v['next_out'];?>">修改配置</a></td>
                </tr>
            <?php endforeach; ?>
              </form>
            </tbody>
            <tfoot>
             <tr>
             <td colspan="10" style="text-align: left;">
             	<a class="btn-blue mt20 submit weightsubmit">保存并上线</a>
             </td>
         	</tr>
             <tr>
             	<td colspan="10" style="text-align: left;">
					注：<br/>
					1、礼包每天0点更新数量，所以在0点之前修改【明日发放】数量会生效；<br/>
					2、每个红包红包兑换标准：<br/>
					① 1元：青铜白银550、黄金铂金500、钻石450；<br/>
					② 2元：青铜白银1050、黄金铂金1000、钻石950；<br/>
					③ 5元：青铜白银2600、黄金铂金2500、钻石2400；<br/>
					④ 10元：青铜白银5100、黄金铂金5000、钻石4900；<br/>
					⑤ 100元：青铜白银48500、黄金铂金48000、钻石47500；<br/>
					⑥ 500元：青铜白银247000、黄金铂金246000、钻石245000；<br/>
					⑦ 1000元：黄金铂金480000、钻石475000；<br/>
					⑧ 5000元：黄金铂金2350000、钻石2340000；<br/>
             	</td>
             </tr>
            </tfoot>
        </table>  
    </div>	
</div>
<div></div>
<!-- <div class='modify_box' style="display: none;">
	<div class='modifyCofigBox'>
		<div><strong>红包金额：</strong><span class='redSpan'></span>&nbsp;元</div>
		<div><strong>青铜白银：</strong><input type="text" name="qtby" ><span>&nbsp;分可兑换</span></div>
		<div><strong>黄金铂金：</strong><input type="text"  name='hjbj'  ><span>&nbsp;分可兑换</span></div>
		<div><strong>钻石：</strong><input type="text"  name='zs' ><span>&nbsp;分可兑换</span></div>
		<div><strong>明日发放：</strong><input type="text"  name='next_out' ><span>&nbsp;个</span></div>
	</div>
</div> -->
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script type="text/javascript">
$(function(){
	//库存清零
	$('.emptyStock').click(function(){
		//权限验证
		$(this).parent().parent().find('.emptyStockInput').val(1);
		$(this).parent().parent().find('td').eq(8).html(0);
	});
	$('.modifyConfig').click(function(){
		var str = "<div class='modifyCofigBox'><div><strong>红包金额：</strong><span class='redSpan'>"+$(this).attr('data-money')+"</span>&nbsp;元</div>";
			str+= "<div><strong>青铜白银：</strong><input type='text' name='qtby' value='"+$(this).attr('data-qtby')+"' ><span>&nbsp;分可兑换</span></div>";
			str+= "<div><strong>黄金铂金：</strong><input type='text'  name='hjbj'  value='"+$(this).attr('data-hjbj')+"'><span>&nbsp;分可兑换</span></div>";
			str+= "<div><strong>钻石：</strong><input type='text'  name='zs' value='"+$(this).attr('data-zs')+"'><span>&nbsp;分可兑换</span></div>";
			str+= "<div><strong>明日发放：</strong><input type='text'  name='next_out' value='"+$(this).attr('next-out')+"' ><span>&nbsp;个</span></div>";
			str+='</div>';
		var trObj = $(this).parent().parent();
		var modifyConfigInputObj = $(this).parent().parent().find('.modifyConfigInput');
	 	layer.open({
	 	  'title':'修改配置',
		  'type': 1,
		  'area': '300px;',
		  'closeBtn': 1, //不显示关闭按钮
		  'btn': ['确认'],
		  'shadeClose': true, //开启遮罩关闭
		  'content': str, 
		  'btnAlign': 'c',
		  'yes': function()
		  	{
		  		var lv3 = $('input[name=qtby]').val();
		  		var lv4 = $('input[name=hjbj]').val();
		  		var lv6 = $('input[name=zs]').val();
		  		var zs = $('input[name=zs]').val();
		  		var next_out = $('input[name=next_out]').val();
		  		//验证是数字
		　　　　if (!(/(^[1-9]\d*$)/.test(lv3)) && lv3!='--' ) 
				{ 
		　　　　　　layer.alert('输入数据格式有误', {icon: 2,btn:'',title:'温馨提示',time:0});
		　　　　　　return false; 
		　　　　}
		　　　　if (!(/(^[1-9]\d*$)/.test(lv4))) 
				{ 
		　　　　　　layer.alert('输入数据格式有误', {icon: 2,btn:'',title:'温馨提示',time:0});
		　　　　　　return false; 
		　　　　}
		　　　　if (parseInt(lv4) > parseInt(lv3) ) 
				{ 
		　　　　　　layer.alert('黄金铂金兑换积分大于青铜白银兑换积分', {icon: 2,btn:'',title:'温馨提示',time:0});
		　　　　　　return false; 
		　　　　}
		　　　　if (!(/(^[1-9]\d*$)/.test(lv6))) 
				{ 
		　　　　　　layer.alert('输入数据格式有误', {icon: 2,btn:'',title:'温馨提示',time:0});
		　　　　　　return false; 
		　　　　}
		　　　　if (parseInt(lv6) > parseInt(lv4) ) 
				{ 
		　　　　　　layer.alert('钻石兑换积分大于黄金铂金兑换积分', {icon: 2,btn:'',title:'温馨提示',time:0});
		　　　　　　return false; 
		　　　　}
			  	if(lv3!='--'){
		  			modifyConfigInputObj.val('{"no_expire":1,"lv1":"--","lv2":"'+lv3+'","lv3":"'+lv3+'","lv4":"'+lv4+'","lv5":"'+lv4+'","lv6":"'+lv6+'","price":"'+lv3+'"}');
		  		}else{
		  			modifyConfigInputObj.val('{"no_expire":1,"lv1":"--","lv2":"'+lv3+'","lv3":"'+lv3+'","lv4":"'+lv4+'","lv5":"'+lv4+'","lv6":"'+lv6+'","price":"'+lv4+'"}');
		  		}
		  		trObj.find('td').eq(3).html(lv3);
		  		trObj.find('td').eq(4).html(lv4);
		  		trObj.find('td').eq(5).html(lv6);
		  		trObj.find('td').eq(7).html(next_out);
		  		trObj.find('.next_out').val(next_out);
		  		layer.closeAll();
	  	 	}
		});
	});
	//保存并上线
	$('.weightsubmit').click(function(){
		$.ajax({
		    type: "post",
		    url: "/backend/Growth/modifyStock",
		    data: $('#modifyStock').serialize(),
		    success: function(data)
		    {
		    	var json = jQuery.parseJSON(data);
		    	layer.closeAll();
		    	if(json.status == 'SUCCESSS')
		    	{
		    		layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();}});
		    	}else{
		    		layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
		    	}
		    }
		})
	});
});
</script>