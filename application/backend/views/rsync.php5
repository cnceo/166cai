<?php if($fromType != 'ajax'): $this->load->view("templates/head") ?>
<div class="path">您的位置：代码发布&nbsp;>&nbsp;<a href="">开启关闭</a></div>
<?php endif; ?>
<div align = "center">
<input class = "btn-blue mr10" type="button" name="subbtn" value="开启" onclick="javascript:rsyncstart('start');"/>
<input class = "btn-blue mr10" type="button" name="subbtn" value="关闭" onclick="javascript:rsyncstart('close');"/>
</br>
</br>
<input class = "btn-blue mr10" type="button" name="subbtn" value="静态同步开启" onclick="javascript:rsyncstart('static_start');"/>
<input class = "btn-blue mr10" type="button" name="subbtn" value="静态同步关闭" onclick="javascript:rsyncstart('static_close');"/>
<input class = "btn-blue mr10" type="button" name="process" value="彩票进程重启" onclick="javascript:process();"/>
<?php if(!empty($taskInfo)):?>
<div class="data-table-list mt10">
    <table style="width: auto" data-action="kjdh">
        <colgroup>
            <col width="50px">
            <col width="220px">
            <col width="180px">
            <col width="100px">
            <col width="80px">
            <col width="80px">
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>任务名</th>
                <th>任务说明</th>
                <th>文件夹位置</th>
                <th>操作</th>
                <th>操作</th>
            </tr>
        </thead>
            <tbody id="pic-table">
                <?php foreach ($taskInfo as $key => $task):?>
                <tr data-index="<?php echo $task['fname']; ?>">
                    <td><?php echo $task['id']; ?></td>
                    <td><?php echo $task['fname']; ?></td>
                    <td><?php echo $task['mark']; ?></td>
                    <td><input type="text" class="ipt w60 tac" name="folder" value="<?php echo $task['folder']; ?>"></td>
                    <td><a href="javascript:;" class="<?php echo ($task['status'] == '1') ? 'cRed' : 'cBlue'; ?> restart" data-type="1">开启</a></td>
                    <td><a href="javascript:;" class="<?php echo ($task['status'] == '0') ? 'cRed' : 'cBlue'; ?> restart" data-type="0">关闭</a></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<script type="text/javascript">
	function rsyncstart(flag)
	{
		$.ajax({
            type: 'post',
            url:  '/backend/Rsync/index',
            data: {'action':flag},
            success: function(response) {
                if(response == 1)
                {
                	alert('操作成功!');
                }else{
                	alert(response);
                }
            }
        });
	}

    $(".restart").click(function(){
        var status = $(this).attr('data-type');
        var task = $(this).parents('tr').attr('data-index');
        var folder = $(this).parents('tr').find('input').val();
        $.ajax({
            type: 'post',
            url:  '/backend/Rsync/restartTask',
            data: {'task':task, 'status':status, 'folder':folder},
            success: function(response) {
                if(response == 1){
                    alert('操作成功!');
                    location.reload()
                }else{
                    alert(response);
                }  
            }
        });
    })
    function process()
    {
        $.ajax({
            type: 'post',
            url:  '/backend/Rsync/process/pro',
            dataType:"json",
            success: function(response) {
                alert(response.message)
            }
        });
    }
</script>
<?php if($fromType != 'ajax'): ?>
</body>
</html>
<?php endif; ?>