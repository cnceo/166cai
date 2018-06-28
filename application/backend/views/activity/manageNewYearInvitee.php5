<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">新年活动</a></div>
<div class="mod-tab mt20 mb20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/Activity/newYearActivity">分享人</a></li>
            <li><a href="/backend/Activity/newYearInvitee">被分享人</a></li>
            <li class="current"><a href="/backend/Activity/manageNewYearInvitee">管理开关</a></li>
            <li><a href="/backend/Activity/newYearPrize">抽奖配置</a></li>
            <li><a href="/backend/Activity/newYearChjList">抽奖记录</a></li>
        </ul>
    </div>
    <div class="data-table-filter mt10" style="width:1080px">
        <form action="/backend/Activity/manageNewYearInvitee" method="get" id="search_form">
            <table>
                <colgroup>
                    <col width="62" />
                    <col width="150" />
                    <col width="62" />
                    <col width="420" />
                    <col width="62" />
                    <col width="320" />
                </colgroup>
                <tbody>
                    <tr>
                        <th>活动当前状态：</th>
                        <td>
                            <?php if ($delete_flag == '2' ){ echo "打开";
                            }else{ echo "关闭";} ?>
                        </td>
                    </tr>
                    <tr>
                        <th>活动状态修改：</th>
                        <td>
                        	<label for="chanel2" class="mr10"><input type="radio" class="radio" name="delete_flag" value="2" <?php if ($delete_flag==2): echo "checked";
endif; ?>>打开</label>
                            <label for="chanel1" class="mr10"><input type="radio" class="radio" name="delete_flag" value="1" <?php if ($delete_flag==1): echo "checked";
endif; ?>>关闭</label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <a href="javascript:void(0);" class="btn-blue submit-prize">保存</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<!-- 确认弹层 -->
    <div class="pop-mask" style="display:none;width:200%"></div>
    <div class="pop-dialog" id="confirm-submit">
        <div class="pop-in">
            <div class="pop-head">
                <h2>活动状态修改确认</h2>
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
    <script>
    // 确认弹层
    $(".submit-prize").click(function(){
        popdialog("confirm-submit");
        return false;
    })
    // 提交
    $("#confirm-prize").click(function(){
    	$('#search_form').submit();
    })
</script>