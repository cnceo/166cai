<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">信息管理</a>&nbsp;&gt;&nbsp;<a href="">首页管理</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd remind-hd">
		<ul>
			<li><a href="/backend/Shouye">轮播图管理</a></li>
      		<li><a href="/backend/Shouye">首页资讯管理</a></li>
      		<li class="current"><a href="/backend/Shouye/zhongjiang">中奖墙管理</a></li>
    	</ul>
    </div>
    <div class="mod-tab-bd">
        <div class="data-table-filter mt10">
            <form action="/backend/Shouye/zhongjiang" method="get" id="search_form">
                <table>
                    <colgroup>
                        <col width="35%"/>
                        <col width="25%"/>
                        <col width="40%"/>
                    </colgroup>
                    <tbody>
                    <tr>
                        <td>
                            标题：
                            &nbsp;&nbsp;
                            <input type="text" class="ipt w130" name="title"
                                   value="<?php echo $search['title'] ?>" placeholder=""/>
                        </td>
                        <td>
                            中奖彩种：
                            <input type="text" class="ipt w130" name="lname" value="<?php echo $search['lname'] ?>" placeholder=""/>
                        </td>
                        <td>
                            是否显示：
                            <select name="status">
                            	<option value=''>所有</option>
                            	<option value='1' <?php if ($search['status'] === '1'){?>selected<?php }?>>是</option>
                            	<option value='0' <?php if ($search['status'] === '0'){?>selected<?php }?>>否</option>
                            </select>
                        </td>
                        <td>
                            创建时间：
                            <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1"/><i></i></span>
                            <span class="ml8 mr8">至</span>
                            <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1"/><i></i></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            提交人：
                            <input type="text" class="ipt w130" name="submitter"
                                   value="<?php echo $search['submitter'] ?>">
                            <a href="javascript:void(0);" class="btn-blue "
                               onclick="$('#search_form').submit();">查询</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <a href="javascript:;" class="btn-blue" id="add_info">新建资讯</a>
        <div class="data-table-list mt10">
            <table id="tablesorter" class="tablesorter">
                <colgroup>
                    <col width="15%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="15%">
                    <col width="10%">
                    <col width="10%">
                </colgroup>
                <thead>
                <tr>
                    <th>资讯标题</th>
                    <th>创建时间</th>
                    <th>URL地址</th>
                    <th>中奖金额</th>
                    <th>中奖彩种</th>
                    <th>是否显示</th>
                    <th>操作</th>
                    <th>置顶</th>
                    <th>提交人</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($info as $key => $items): ?>
                    <tr>
                        <td><?php echo $items['title'] ?></td>
                        <td><?php echo $items['created'] ?></td>
                        <td><a href="<?php echo $items['url'] ?>" target="_blank" class="cBlue"><?php echo $items['url'] ?></a></td>
                        <td><?php echo $items['content'] ?></td>
                        <td><?php echo $items['lname'] ?></td>
                        <td><?php echo $items['status'] ? "是" : "否" ?></td>
                        <td>
                            <a href="/backend/Info/notice_view/?id=<?php echo $items['newsId'] ?>" class="cBlue mr5" target="_blank">预览</a>
                            <a href="javascript:;" class="cBlue mr5 setTop" data-index="<?php echo $items['id'] ?>">置顶</a>
                            <a href="javascript:;" class="cBlue mr5 delete" data-index="<?php echo $items['id'] ?>">删除</a>
                            <a href="javascript:;" class="cBlue mr5 modify" data-index="<?php echo $items['id'] ?>">编辑</a>
                        </td>
                        <td><?php echo $items['is_top'] ? "是" : "否"; ?></td>
                        <td><?php echo $items['submitter'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="9">
                        <div class="stat">
                            <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
                            <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
                            <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
                        </div>
                    </td>
                </tr>
                </tfoot>
            </table>
            <div class="page mt10">
                <?php echo $pages[0] ?>
            </div>
        </div>
    </div>
    <!-- 信息弹层 start -->
    <div class="pop-dialog" id="pop_add_info">
        <div class="pop-in">
            <div class="pop-head">
                <h2>新建中奖资讯</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
                <div class="data-table-filter del-percent">
                    <table>
                        <colgroup>
                            <col width="60">
                            <col width="240">
                        </colgroup>
                        <tbody>
                            <tr>
                                <td>资讯标题：</td>
                                <td>
                                	<input type="text" class="ipt tac w130" name="title" value="">
                                </td>
                            </tr>
                            <tr>
                                <td>链接地址：</td>
                                <td>
                                	<input type="text" class="ipt tac w130" name="url" value="">
                                </td>
                            </tr>
                            <tr>
                                <td>中奖金额：</td>
                                <td>
                                	<input type="text" class="ipt tac w130" name="content" value=""><span class="ml10">输入文案即为展示金额</span>
                                </td>
                            </tr>
                            <tr>
                                <td>中奖彩种：</td>
                                <td>
                                	<input type="text" class="ipt tac w130" name="lname" value="">
                                </td>
                            </tr>
                            <tr>
                                <td>是否显示：</td>
                                <td>
                                	<label for="infoStatus" class="mr20"><input type="radio" name="status" value="1" checked>是</label>
                                	<label for="infoStatus" class="mr20"><input type="radio" name="status" value="0">否</label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="pop-confirm">确认</a>
        		<a href="javascript:;" class="btn-blue-h32 mlr15 pop-cancel">取消</a>
            </div>
        </div>
    </div>
    <!-- 信息弹层 end -->
    <!-- 编辑弹层 start -->
    <div class="pop-dialog" id="pop_modify_info">
        <div class="pop-in">
            <div class="pop-head">
                <h2>编辑中奖资讯</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
                <div class="data-table-filter del-percent">
                    <table>
                        <colgroup>
                            <col width="60">
                            <col width="240">
                        </colgroup>
                        <tbody>
                            <tr>
                                <td>资讯标题：</td>
                                <td>
                                    <input type="hidden" class="ipt tac w130" name="id" value="">
                                    <input type="text" class="ipt tac w130" name="title" value="">
                                </td>
                            </tr>
                            <tr>
                                <td>链接地址：</td>
                                <td>
                                    <input type="text" class="ipt tac w130" name="url" value="">
                                </td>
                            </tr>
                            <tr>
                                <td>中奖金额：</td>
                                <td>
                                    <input type="text" class="ipt tac w130" name="content" value=""><span class="ml10">输入文案即为展示金额</span>
                                </td>
                            </tr>
                            <tr>
                                <td>中奖彩种：</td>
                                <td>
                                    <input type="text" class="ipt tac w130" name="lname" value="">
                                </td>
                            </tr>
                            <tr>
                                <td>是否显示：</td>
                                <td id="modify_status">
                                    <label for="infoStatus" class="mr20"><input type="radio" name="status" value="1">是</label>
                                    <label for="infoStatus" class="mr20"><input type="radio" name="status" value="0">否</label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="pop-confirm-modify">确认</a>
                <a href="javascript:;" class="btn-blue-h32 mlr15 pop-cancel">取消</a>
            </div>
        </div>
    </div>
    <!-- 编辑弹层 end -->
</div>
<script src="/source/date/WdatePicker.js"></script>
<script>
	$(function() {
		$(".Wdate1").focus(function () {
            dataPicker();
        });

        $("#add_info").click(function(){
        	popdialog("pop_add_info");
        })

        // 新建
        var selectTag = true;
        $("#pop-confirm").click(function(){
        	var title = $('#pop_add_info input[name="title"]').val();
        	var url = $('#pop_add_info input[name="url"]').val();
        	var content = $('#pop_add_info input[name="content"]').val();
        	var lname = $('#pop_add_info input[name="lname"]').val();
        	var status = $('#pop_add_info input[name="status"]:checked').val();

        	if(selectTag){
        		selectTag = false;
        		$.ajax({
        			type: 'post',
                	url: '/backend/Shouye/addWinInfo',
                	data: {title:title,url:url,content:content,lname:lname,status:status},
                	success: function (response) {
	                    var response = $.parseJSON(response);
	                    if(response.status == 'y')
	                    {
	                        selectTag = true;
	                        closePop();
	                        alert(response.message);
	                        window.location.reload();
	                    }else{
	                        selectTag = true;
	                        alert(response.message);
	                    }
	                },
	                error: function () {
	                    selectTag = true;
	                    alert('网络异常，请稍后再试');
	                }
        		});
        	}
        })

        // 置顶
        $(".setTop").click(function(){
        	var id = $(this).data('index');
            var title = $(this).parent().parent().find('td').eq(0).text();
        	$.ajax({
    			type: 'get',
            	url: '/backend/Shouye/setTopWin',
            	data: {id:id, title:title},
            	success: function (response) {
                    var response = $.parseJSON(response);
                    if(response.status == 'y')
                    {
                        alert(response.message);
                        window.location.reload();
                    }else{
                        alert(response.message);
                    }
                },
                error: function () {
                    selectTag = true;
                    alert('网络异常，请稍后再试');
                }
    		});
        })

        // 删除
        $(".delete").click(function(){
        	var id = $(this).data('index');
            var title = $(this).parent().parent().find('td').eq(0).text();
        	$.ajax({
    			type: 'get',
            	url: '/backend/Shouye/setDeleteWin',
            	data: {id:id, title:title},
            	success: function (response) {
                    var response = $.parseJSON(response);
                    if(response.status == 'y')
                    {
                        alert(response.message);
                        window.location.reload();
                    }else{
                        alert(response.message);
                    }
                },
                error: function () {
                    selectTag = true;
                    alert('网络异常，请稍后再试');
                }
    		});
        })

        // 编辑
        $(".modify").click(function(){
            var id = $(this).data('index');
            $.ajax({
                type: 'get',
                url: '/backend/Shouye/getWinDetail',
                data: {id:id},
                success: function (response) {
                    var response = $.parseJSON(response);
                    if(response.status == '1')
                    {
                        $('#pop_modify_info input[name="id"]').val(response.data.id);
                        $('#pop_modify_info input[name="title"]').val(response.data.title);
                        $('#pop_modify_info input[name="url"]').val(response.data.url);
                        $('#pop_modify_info input[name="content"]').val(response.data.content);
                        $('#pop_modify_info input[name="lname"]').val(response.data.lname);
                        // 是否显示
                        if(response.data.status == '1'){
                            $('#modify_status').find('input').eq(0).attr("checked", "checked");
                        }else{
                            $('#modify_status').find('input').eq(1).attr("checked", "checked");
                        }
                        popdialog("pop_modify_info");
                    }else{
                        alert(response.message);
                    }
                },
                error: function () {
                    selectTag = true;
                    alert('网络异常，请稍后再试');
                }
            });
        })

        // 确认修改
        $('#pop-confirm-modify').click(function(){
            var id = $('#pop_modify_info input[name="id"]').val();
            var title = $('#pop_modify_info input[name="title"]').val();
            var url = $('#pop_modify_info input[name="url"]').val();
            var content = $('#pop_modify_info input[name="content"]').val();
            var lname = $('#pop_modify_info input[name="lname"]').val();
            var status = $('#pop_modify_info input[name="status"]:checked').val();

            if(selectTag){
                selectTag = false;
                $.ajax({
                    type: 'post',
                    url: '/backend/Shouye/modifyWinInfo',
                    data: {id:id,title:title,url:url,content:content,lname:lname,status:status},
                    success: function (response) {
                        var response = $.parseJSON(response);
                        if(response.status == 'y')
                        {
                            selectTag = true;
                            closePop();
                            alert(response.message);
                            window.location.reload();
                        }else{
                            selectTag = true;
                            alert(response.message);
                        }
                    },
                    error: function () {
                        selectTag = true;
                        alert('网络异常，请稍后再试');
                    }
                });
            }
        })
	});
</script>
