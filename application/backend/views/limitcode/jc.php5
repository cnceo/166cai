<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营管理</a>&nbsp;&gt;&nbsp;<a href="">竞彩限号配置</a></div>
<div class="mod-tab-bd">
    <div class="data-table-filter mt10">
        <table>
            <colgroup><col width="150"><col width="100"></colgroup>
            <tbody>
                <tr>
                    <td>
                        <select class="ipt w108">
                            <option value="42">竞彩足球</option>
                        </select>
                    </td>
                    <td>
                        <a href="javascript:;" class="btn-blue" id="add">新增限号</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="data-table-list mt10">
        <table>
            <colgroup><col width="40"><col width="300"><col width="50"><col width="150"><col width="100"><col width="100"><col width="70"></colgroup>
            <thead><tr><th>编号</th><th>方案内容</th><th>过关方式</th><th>返回消息</th><th>开始时间</th><th>结束时间</th><th>操作</th></tr></thead>
            <tbody>
            <?php foreach ($data as $row) {
            $codesArr = explode('|', $row['codes']);?>
                <tr>
                    <td><?php echo $row['issue']?></td>
                    <td><?php echo $codesArr[0]?></td>
                    <td><?php echo str_replace('*', '串', $codesArr[1])?></td>
                    <td><?php echo $row['msg']?></td>
                    <td><?php echo $row['created']?></td>
                    <td><?php echo $row['endTime']?></td>
                    <td><?php if (!$row['endTime']) {?><a href="javascript:;" class="overLimit" data-id="<?php echo $row['id']?>">结束限号</a><?php }?></td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
    <div class="page mt10 order_info">
      <?php echo $pages[0] ?>
    </div>
</div>
<!--添加和修改弹窗-->
<div class="pop-mask" style="display:none"></div>
<form id='addForm' method='post' action=''>
    <div class="pop-dialog" id="addPop">
        <div class="pop-in">
            <div class="pop-head">
                <h2>新增竞彩限号</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
                <div class="data-table-filter del-percent">
                    <table>
                        <colgroup>
                            <col width="68" />
                            <col width="100" />
                            <col width="100" />
                            <col width="70" />
                            <col width="70" />
                        </colgroup>
                        <tbody id="tbody">
                            <tr><th>限号彩种</th><td colspan="4">竞彩足球<input type="hidden" name="lid" value="42"></td></tr><!--目前只有竞足写死 -->
                            <tr>
                                <th>场次方案</th>
                                <td><input class="ipt w95" name="matches[0][mid]"></td>
                                <td>
                                    <select class="selectList w95" name="matches[0][playtype]">
                                        <option value="SPF">胜平负</option>
                                        <option value="RQSPF">让球胜平负</option>
                                        <option value="CBF">比分</option>
                                        <option value="BQC">半全场</option>
                                        <option value="JQS">总进球</option>
                                    </select>
                                </td>
                                <td><input class="ipt w60" name="matches[0][comment]"></td>
                                <td><a href="javascript:;" id="addMatch">添加一场</a></td>
                            </tr>
                            <tr>
                                <th>过关方式</th>
                                <td colspan="4">
                                    <select class="selectList w95" name="ggType">
                                    <?php for ($i = 1; $i <= 8; $i++) {?>
                                        <option value="<?php echo $i."*1"?>"><?php echo $i."串1"?></option>
                                    <?php }?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>提示文案</th>
                                <td colspan="4"><input class="ipt w360" name="msg"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="addSub">确定</a>
                <a href="javascript:;" class="btn-b-white mlr15 pop-cancel">关闭</a>
            </div>
        </div>
    </div>
</form>
<script>
var i = 0;
$("#add").click(function(){
	popdialog("addPop");
})
$("#addMatch").click(function(){
	i++;
	$("#addPop tbody tr:eq(-2)").before('<tr><th>场次方案</th><td><input class="ipt w95" name="matches['+i+'][mid]"></td>\
			<td><select name="matches['+i+'][playtype]" class="selectList w95"><option value="SPF">胜平负</option>\
		    <option value="RQSPF">让球胜平负</option><option value="CBF">比分</option><option value="BQC">半全场</option><option value="JQS">总进球</option>\
			</select></td><td colspan="2"><input class="ipt w60" name="matches['+i+'][comment]"></td></tr>')
})
$("#addSub").click(function(){
	$.ajax({
        type: 'post',
        url : '/backend/Limitcode/createJcLimit',
        data: $("#addForm").serialize(),
        dataType:'json',
        success:function(res){
            if (res.status === 'y') {
                alert('添加成功！');
                location.reload();
            }
            else alert(res.message);
        }
    });
})
$('.overLimit').click(function(){
	$.post('/backend/Limitcode/overJcLimit', {id:$(this).data('id')}, function(res){
		if (res.status === 'y') {
			alert('结束限号成功！');
	        location.reload();
		}
		else alert(res.message);
	}, 'json')
})
</script>