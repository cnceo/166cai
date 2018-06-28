<?php
 $this->load->view("templates/head");
 $status = array(
 	'' => '全部',
 	'0' => '等待出票',
 	'602' => '系统撤单',
 	'603' => '已撤单(中奖后停止追号)',
 	'601' => '已撤单(手动停止追号)'
 );
?>
<div class="frame-container" style="margin-left:0;">
    <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/monitorTicket">出票监控</a></div>
    <div class="mod-tab mt20">
        <div class="mod-tab-hd">
            <ul>
                <li><a href="/backend/Management/huizong">出票汇总</a></li>
				<li><a href="/backend/Management/monitorTicket">单彩种监控</a></li>
				<li class="current"><a href="/backend/Management/chaseCancel">追号监控</a></li>
				<li><a href="/backend/Management/ticketLimit">出票限制</a></li>
            </ul>
        </div>
    <li>
        <div class="data-table-filter" style="width:100%;">
            <form action="/backend/Management/chaseCancel" method="get"  id="search_form_order">
			  <table>
			  <colgroup>
			      <col width="62" />
			      <col width="132" />
			      <col width="62" />
			      <col width="100" />
			      <col width="62" />
			      <col width="100" />
			    </colgroup>
			    <tbody>
			    <tr>
				    <th>彩种玩法：</th>
				    <td>
				    	<select class="selectList"  name="lid" id="lid">
				        	<?php foreach ($lidMap as $key => $cp): ?>
				            <option value="<?php echo $key; ?>" <?php if ($search['lid'] === "{$key}"): echo "selected"; endif; ?>><?php echo $cp['name'] ?></option>
				            <?php endforeach; ?>
				        </select>
				  	</td>
				  	<th style="text-align:right;">期次：</th>
			      	<td>
				    	<select class="selectList mr20"  name="issue" id="issue">
				    	<?php foreach ($issues as $issue): ?>
				    	<option value="<?php echo $issue; ?>" <?php if ($search['issue'] == "{$issue}"): echo "selected"; endif; ?>><?php if($issue == $currentIssue) { echo $issue . "(当前期)";}else{ echo $issue;} ?></option>
				    	<?php endforeach; ?>
				        </select>
				  	</td>
				  	<th><a id="inputIssue" href="javascript:void(0);" class="cBlue">输入期次</a></th>
				  	<th>订单状态：</th>
			      	<td>
				    	<select class="selectList"  name="status">
				    		<?php foreach ($status as $key => $val):?>
				            <option value="<?php echo $key; ?>" <?php if ($search['status'] === "{$key}"): echo "selected"; endif; ?>><?php echo $val;?></option>
				            <?php endforeach;?>
				        </select>
				  	</td>
				  	<td style="text-align:right;" width="280">
			          <a id="issueCancel" href="javascript:void(0);" class="btn-blue ml20">期次撤单</a>
			      </td>
			    </tr>
			    <tr>
			      <th>关键字：</th>
			      <td>
			          <input type="text" class="ipt w222"  name="name" value="<?php echo $search['name'] ?>"  placeholder="用户名/订单" />
			      </td>
			      <td >
			      	  <input type="hidden" name="selectLid" value="<?php echo $search['lid'];?>" />
			      	  <input type="hidden" name="selectIssue" value="<?php echo $search['issue'];?>" />
			          <a id="search" href="javascript:void(0);" class="btn-blue ml20" onclick="">查询</a>
			      </td>
			    </tr>
			    </tbody>
			  </table>
		  </form>
        </div>
        <div class="data-table-list mt10">
            <table>
                <colgroup>
                    <col width="10%" />
                    <col width="20%" />
                    <col width="15%" />
                    <col width="10%" />
                    <col width="10%" />
                    <col width="15%" />
                    <col width="10%" />
                    <col width="15%" />
                    <col width="10%" />
                    <col width="10%" />
                </colgroup>
                <thead>
                <tr>
		            <td colspan="10">
		                <div class="tal">
		                    <strong>等待出票个数：</strong>
		                    <span><?php echo $tj['dCount'];?></span>
		                    <strong class="ml20">系统撤单个数：</strong>
		                    <span><?php echo $tj['sCount'];?></span>
		                    <strong class="ml20">手动撤单个数：</strong>
		                    <span><?php echo $tj['uCount'];?></span>
		                    <strong class="ml20">中奖撤单个数：</strong>
		                    <span><?php echo $tj['aCount'];?></span>
		                    <span style="float: right;"><a class="btn-red mr10" id="cheChanConfirm" href="javascript:void(0);">确认撤单</a></span>
		                </div>
		            </td>
		        </tr>
                <tr>
                	<th><input type="checkbox" class="_ck" />全选</th>
                    <th>追号订单编号</th>
                    <th>用户名</th>
                    <th>彩种</th>
                    <th>玩法</th>
                    <th>期次</th>
                    <th>倍数</th>
                    <th>方案金额（元）</th>
                    <th>status</th>
                    <th>订单状态</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($orders as $key => $value):?>
                <tr>
                    <td><?php if($value['status'] == '0'):?><input type="checkbox" class="ck_" value="<?php echo $value['id'];?>"><?php endif;?></td>
                    <td><?php echo $value['chaseId'];?></td>
                    <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $value['uid']; ?>" class="cBlue"><?php echo $value['userName'];?></a></td>
                    <td><?php echo $this->caipiao_cfg[$value['lid']]['name'];?></td>
                    <td><?php if ( ! empty($this->caipiao_cfg[$value['lid']]['play'])): echo print_playtype($value['lid'], $value['playType'], $this->caipiao_cfg[$value['lid']]['play']); else: echo "--";  endif; ?></td>
                    <td><?php echo $value['issue'];?></td>
                    <td><?php echo $value['multi'];?></td>
                    <td><?php echo m_format($value['money']);?></td>
                    <td><?php echo $value['status'];?></td>
                    <td>
                    <?php 
                    	if($value['status'] == '0')
                    	{
                    		echo '<span class="cBlue">等待出票</span>';
                    	}
                    	elseif ($value['status'] == '601')
                    	{
                    		echo $value['cancel_flag'] == '1' ? '用户撤单' : '撤单中';
                    	}
                    	elseif ($value['status'] == '602')
                    	{
                    		echo $value['cancel_flag'] == '1' ? '系统撤单' : '撤单中';
                    	}
                    	elseif ($value['status'] == '603')
                    	{
                    		echo $value['cancel_flag'] == '1' ? '中奖撤单' : '撤单中';
                    	}
                    ?>
                    </td>
                </tr>
                <?php endforeach;?>
                </tbody>
                <tfoot>
			      <tr>
			        <td colspan="11">
			          <div class="stat">
			            <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
			            <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
			            <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
			          </div>
			        </td>
			      </tr>
			    </tfoot>
            </table>
        </div>
    </li>
</div>
<div class="page mt10 order_info">
   <?php echo $pages[0] ?>
</div>
</div>
<div class="pop-dialog" id="dialog-issueCancel" style="display:none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2>确认期次撤单</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body" id="pop-body">
		</div>
		<div class="pop-foot tac">
			<a href="javascript:issueCancel();" class="btn-blue-h32 mlr15">确认</a>
			<a href="javascript:closePop();" class="btn-b-white mlr15">取消</a><br/><br/>
		</div>
	</div>
</div>
<div class="pop-dialog" id="dialog-inputIssue" style="display:none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2>输入期次</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-filter del-percent">
				<table>
					<colgroup>
						<col width="68" />
		                <col width="350" />
					</colgroup>
					<tbody id="tbody1"><tr><th>彩种期次：</th><td><input type="text" name="inputIssue" class="ipt w222" value=""></td></tr></tbody>
				</table>
			</div>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:inputIssue();" class="btn-blue-h32 mlr15">确认</a>
			<a href="javascript:closePop();" class="btn-b-white mlr15">取消</a><br/><br/>
		</div>
	</div>
</div>
<!-- 撤单配置 -->
<script>
    $(function(){
    	$("#search").click(function(){
    		$('#search_form_order').submit();
    	});
    	$("._ck").click(function(){
    		var self = this;
    		$(".ck_").each(function(){
    			if(self.checked)
    			{
    				$(this).attr("checked", true);
    			}
    			else
    			{
    				$(this).attr("checked", false);
    			}
    		});
    	});
    	$("#lid").change(function(){
    		$('#search_form_order').submit();
    		return false;
    	});
    	
    	$("#issueCancel").click(function(){
        	var lid = $("#lid").find("option:selected").text();
        	var issue = $("#issue").find("option:selected").text();
        	var html = '是否执行<span style="color:red;">'+lid+'</span>第<span style="color:red;">'+issue+'</span>期的撤单操作？';
        	$("#pop-body").html(html);
        	popdialog("dialog-issueCancel");
    		return false;
    	});

    	$("#inputIssue").click(function(){
        	popdialog("dialog-inputIssue");
    		return false;
    	});

    	$("#cheChanConfirm").click(function(){
    		ids = [];
    		$(".ck_").each(function(){
    			if(this.checked)
    			{
    				ids.push($(this).val());
    			}
    		})
    	    if(ids.length < 1)
    	    {
    	      alert('请先选择要撤销的订单');
    	      return false;
    	    }
    	    else
    	    {
    	    	var lid = $("input[name='selectLid']").val();
    	    	var issue = $("input[name='selectIssue']").val();
    	    	$.ajax({
    			    type: "post",
    			    url: "/backend/Management/chaseCancelById/",
    			    data: {'ids':ids, 'lid':lid, 'issue':issue},
    			    success: function(data){
    			    	var json = jQuery.parseJSON(data);
    	                alert(json.message);
    	                $(".ck_").each(function(){
    	        			$(this).attr("checked", false);
    	        		});
    	                location.reload();
    			    }
    			});
    	    }
    	});
    });
    function issueCancel()
	{
    	var lid = $("#lid").val();
    	var issue = $("#issue").val();
		$.ajax({
		    type: "post",
		    url: "/backend/Management/chaseCancelByIssue/",
		    data: {'lid':lid, 'issue':issue},
		    success: function(data){
		    	var json = jQuery.parseJSON(data);
                alert(json.message);
                closePop();
                $(".ck_").each(function(){
        			$(this).attr("checked", false);
        		});
                location.reload();
		    }
		})
	}
    function inputIssue()
	{
    	var inputIssue = $("input[name='inputIssue']").val();
    	var flag = false;
    	$("#issue option").each(function(){
            var value = $(this).val(); //获取option的内容
            if(value == inputIssue){
                flag = true;
            }
        });
        if(flag == false)
        {
            alert('输入期次下拉框内不存在');
        }

        $("#issue").find("option[value='"+inputIssue+"']").attr("selected",true);
        closePop();
	}
</script>