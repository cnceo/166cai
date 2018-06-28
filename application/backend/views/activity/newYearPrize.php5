<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">新年活动</a></div>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li><a href="/backend/Activity/newYearActivity">分享人</a></li>
            <li><a href="/backend/Activity/newYearInvitee">被分享人</a></li>
            <li><a href="/backend/Activity/manageNewYearInvitee">管理开关</a></li>
            <li class="current"><a href="/backend/Activity/newYearPrize">抽奖配置</a></li>
            <li><a href="/backend/Activity/newYearChjList">抽奖记录</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
  		<div class="tal mt20 mb15">活动时间：<?php echo $activity['start_time'];?> - <?php echo $activity['end_time'];?></div>
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-list mb10">
        			<form action="" method="post" id="prize_form">
	          			<table>
                            <colgroup>
                                <col width="200">
                                <col width="200">
                                <col width="200">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>奖品</th>
                                    <th>概率</th>
                                    <th>库存</th>
                                </tr>
                            </thead>
                            <tbody id="pic-table">
                                <?php if(!empty($info)):?>
                                <?php foreach ($info as $key => $items):?>
                                <tr>
                                    <td>
                                        <?php echo $items['name']; ?>
                                        <input type="hidden" class="ipt w150 tac" name="prize[<?php echo $key?>][id]" value="<?php echo $items['id']; ?>" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="ipt w150 tac" name="prize[<?php echo $key?>][lv]" value="<?php echo $items['lv'] * 100; ?>"> %
                                    </td>
                                    <td>
                                        <input type="text" class="ipt w150 tac" name="prize[<?php echo $key?>][num]" value="<?php echo $items['num']; ?>" >
                                    </td>
                                </tr>
                                <?php endforeach;?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="tac">
                            <a class="btn-blue mt20 submit-prize">保存并上线</a>
                        </div>
          			</form>
        		</div>
      		</li>
    	</ul>
  	</div>
    <!-- 确认弹层 -->
    <div class="pop-mask" style="display:none;width:200%"></div>
    <div class="pop-dialog" id="confirm-submit">
        <div class="pop-in">
            <div class="pop-head">
                <h2>确认页</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
                是否确认当前的修改？
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirm-prize">确认</a>
                <a href="javascript:closePop();" class="btn-b-white mlr15" id="confirm-cancel">取消</a>
            </div>
        </div>
    </div>
    <!-- 确认弹层 -->
  	<div class="page mt10 order_info">
      <?php echo $pages[0] ?>
    </div>
</div>
<script>
    // 确认弹层
    $(".submit-prize").click(function(){
        popdialog("confirm-submit");
        return false;
    })
    // 提交
    var selectTag = true;
    $("#confirm-prize").click(function(){
        if(selectTag){
            selectTag = false;
            $.ajax({
                type: 'post',
                url: '/backend/Activity/updateNewYearPrize',
                data: $('#prize_form').serialize(),
                success: function (response) {
                    var response = $.parseJSON(response);
                    if(response.status == '1'){
                        selectTag = true;
                        closePop();
                        alert(response.message);
                        window.location.reload();
                    }else{
                        selectTag = true;
                        closePop();
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
</script>