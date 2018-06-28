<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营管理</a>&nbsp;&gt;&nbsp;<a href="/backend/rebateManage/">推广管理</a>&nbsp;&gt;&nbsp;<a href="">详情</a></div>
<div class="data-table-brief mt10">
    <table>
        <colgroup>
            <col width="184" />
            <col width="206" />
            <col width="286" />
            <col width="164" />
            <col width="144" />
        </colgroup>
        <tbody>
            <tr>
                <td>
                    <strong>用户名：</strong>
                    <span><?php echo $uname; ?></span>
                </td>
                <td>
                    <strong>真实姓名：</strong>
                    <span><?php echo $real_name; ?></span>
                </td>
                <td>
                    <strong>手机号码：</strong>
                    <span><?php echo $phone; ?></span>
                </td>
                <td>
                    <strong>代理级别：</strong>
                    <span><?php if($puid > 0){ echo '二级';}else{ echo '一级';} ?></span>
                </td>
                <td>
                    <strong>申请时间：</strong>
                    <span><?php echo $applyTime; ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>代理编号：</strong>
                    <span><?php echo $id; ?></span>
                </td>
                <td>
                    <strong>上级用户：</strong>
                    <span><?php if(empty($up_uname)){ echo '网站';}else{ echo $up_uname;}?></span>
                </td>
                <td>
                    <strong>状态：</strong>
                    <span><?php if($stop_flag){ echo '停止返点';}else{ echo '正常';}?></span>
                </td>
                <td>
                    <strong>注册账号时间：</strong>
                    <span><?php echo $created; ?></span>
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="tab-nav">
            <ul class="clearfix">
                <li class="active"><a href="javascript:stab('detail','Management/rebateDetailList')"><span>返点明细</span></a></li>
                <li><a href="javascript:;"><span>比例设置</span></a></li>
                <?php if(!$puid):?><li><a href="javascript:stab('subordinate','Management/subordinate')"><span>下线管理</span></a></li><?php endif;?>
            </ul>
        </div>
        <div class="tab-content p-rebates">
            <div class="item" style="display:block;" id="detail" has_load='false'></div>
            <div class="item"   id="setOdds" has_load='true'>
            	<table class="data-table-list mt10" width="660">
		          <tbody>
		            <tr class="text-align:right;">
		              <td colspan="2"><a class="setRebate" data-id="<?php  echo $id;?>" href="javascript:;">设置比例</a></td>
		            </tr>
		          </tbody>
		        </table>
            	<?php 
            		$rebate_odds = json_decode($rebate_odds, true);
            	?>
            	<table class="data-table-list mt10">
		          <colgroup>
		            <col width="200">
		            <col width="460">
		          </colgroup>
		          <thead>
		            <tr>
		              <th>彩种</th>
		              <th>返点比例</th>
		            </tr>
		          </thead>
		          <tbody>
		            <tr>
		              <td>竞彩足球</td>
		              <td><span <?php if($rebate_odds[42] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[42];?>%</span></td>
		            </tr>
		            <tr>
		              <td>竞彩篮球</td>
		              <td><span <?php if($rebate_odds[43] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[43];?>%</span></td>
		            </tr>
		            <tr>
		              <td>双色球</td>
		              <td><span <?php if($rebate_odds[51] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[51];?>%</span></td>
		            </tr>
		            <tr>
		              <td>大乐透</td>
		              <td><span <?php if($rebate_odds[23529] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[23529];?>%</span></td>
		            </tr>
		            <tr>
		              <td>七星彩</td>
		              <td><span <?php if($rebate_odds[10022] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[10022];?>%</span></td>
		            </tr>
		            <tr>
		              <td>七乐彩</td>
		              <td><span <?php if($rebate_odds[23528] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[23528];?>%</span></td>
		            </tr>
		            <tr>
		              <td>老11选5</td>
		              <td><span <?php if($rebate_odds[21406] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[21406];?>%</span></td>
		            </tr>
		            <tr>
		              <td>福彩3D</td>
		              <td><span <?php if($rebate_odds[52] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[52];?>%</span></td>
		            </tr>
		            <tr>
		              <td>排列三</td>
		              <td><span <?php if($rebate_odds[33] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[33];?>%</span></td>
		            </tr>
		            <tr>
		              <td>排列五</td>
		              <td><span <?php if($rebate_odds[35] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[35];?>%</span></td>
		            </tr>
		            <tr>
		              <td>胜负彩</td>
		              <td><span <?php if($rebate_odds[11] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[11];?>%</span></td>
		            </tr>
		            <tr>
		              <td>任选九</td>
		              <td><span <?php if($rebate_odds[19] > 0):?>class="main-color"<?php endif;?>><?php echo $rebate_odds[19];?>%</span></td>
		            </tr>
		            <tr>
		              <td>新11选5</td>
		              <td><span <?php if($rebate_odds[21407] > 0):?>class="main-color"<?php endif;?>><?php if($rebate_odds[21407] > 0){ echo $rebate_odds[21407];}else{ echo '0.0';}?>%</span></td>
		            </tr>
		            <tr>
		              <td>上海快三</td>
		              <td><span <?php if($rebate_odds[53] > 0):?>class="main-color"<?php endif;?>><?php if($rebate_odds[53] > 0){ echo $rebate_odds[53];}else{ echo '0.0';}?>%</span></td>
		            </tr>
		            <tr>
		              <td>吉林快三</td>
		              <td><span <?php if($rebate_odds[56] > 0):?>class="main-color"<?php endif;?>><?php if($rebate_odds[56] > 0){ echo $rebate_odds[56];}else{ echo '0.0';}?>%</span></td>
		            </tr>
		            <tr>
		              <td>江西快三</td>
		              <td><span <?php if($rebate_odds[57] > 0):?>class="main-color"<?php endif;?>><?php if($rebate_odds[57] > 0){ echo $rebate_odds[57];}else{ echo '0.0';}?>%</span></td>
		            </tr>                            
		            <tr>
		              <td>惊喜11选5</td>
		              <td><span <?php if($rebate_odds[21408] > 0):?>class="main-color"<?php endif;?>><?php if($rebate_odds[21408] > 0){ echo $rebate_odds[21408];}else{ echo '0.0';}?>%</span></td>
		            </tr>
                    <tr>
                      <td>快乐扑克</td>
                      <td><span <?php if($rebate_odds[54] > 0):?>class="main-color"<?php endif;?>><?php if($rebate_odds[54] > 0){ echo $rebate_odds[54];}else{ echo '0.0';}?>%</span></td>
                    </tr>
                    <tr>
                      <td>老时时彩</td>
                      <td><span <?php if($rebate_odds[55] > 0):?>class="main-color"<?php endif;?>><?php if($rebate_odds[55] > 0){ echo $rebate_odds[55];}else{ echo '0.0';}?>%</span></td>
                    </tr>
                    <tr>
		              <td>乐11选5</td>
		              <td><span <?php if($rebate_odds[21421] > 0):?>class="main-color"<?php endif;?>><?php if($rebate_odds[21421] > 0){ echo $rebate_odds[21421];}else{ echo '0.0';}?>%</span></td>
		            </tr>
		          </tbody>
		        </table>
            </div>
            <?php if(!$puid):?><div class="item" id="subordinate" has_load='false'></div><?php endif;?>
        </div>
        
<div class="pop-mask" style="display:none;width:200%"></div>
<form id='setRebatesForm' method='post' action=''>
<div class="pop-dialog set-rebates" id="set-rebates">
	<div class="pop-in">
		<div class="pop-head">
			<h2>设置返点比例</h2>
			<span class="pop-close" title="关闭">×</span>
		</div>
		<div class="pop-body" id="pop-body">
		</div>
		<div class="pop-foot">
			<div class="pop-foot tac">
				<a href="javascript:;" class="btn-blue-h32 mlr15" id="submitForm">确定</a>
				<a href="javascript:;" class="btn-b-white pop-cancel">关闭</a>
			</div>
		</div>
	</div>
</div>
<input type="hidden" value="" name="rebateId"  id="rebateId"/>
</form>
    <script  src="/source/date/WdatePicker.js"></script>
	<script>
	var id = <?php echo $id;?>;
    $(function () {
        // tab切换
        $(".tab-nav li").bind("click", function () {
            var i = $(this).index();
            $(this).addClass('active').siblings().removeClass('active');
            $(this).parents(".tab-nav").next(".tab-content:eq(0)").find(".item").eq(i).show().siblings().hide();
        });
        $(".setRebate").click(function(){
       		var id = $(this).attr("data-id");
       		$.ajax({
                type: "post",
                url: '/backend/Management/getRebatePopHtml',
                data: {'id': id},
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    if(json.status =='y'){
                    	$("#pop-body").html(json.message);
                    	$("#rebateId").val(id);
                    	popdialog("set-rebates");
                    }else{
                        alert(json.message);
                    }
                }
            });
        });

       	$("#submitForm").click(function(){
            $.ajax({
                type: "post",
                url: '/backend/Management/setRebate',
                data: $("#setRebatesForm").serialize(),
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    if(json.status =='y')
                    {
                        alert('操作成功');
                        location.reload();
                    }else{
                    	$("#pop-body").html(json.message);
                    }
                }
            });
            return false;
        });
    });
    function stab(ele, url)
    {
        if($("#"+ele).attr("has_load") == 'false')
        {
            $("#"+ele).load("/backend/"+url+"?id="+id+"&fromType=ajax",function(){
                $("#"+ele).attr("has_load",'true')
            });
        }
    }
   stab('detail','Management/rebateDetailList');
</script>
</body>
</html>