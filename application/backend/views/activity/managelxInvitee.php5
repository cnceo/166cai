<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">推荐有礼</a></div>
<div class="mod-tab mt20 mb20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/Activity/newlxInviter">邀请人</a></li>
            <li><a href="/backend/Activity/newlxInvitee">受邀人</a></li>
            <li class="current"><a href="/backend/Activity/managelxInvitee">管理入口</a></li>
        </ul>
    </div>
    <div class="data-table-filter mt10" style="width:1080px">
        <form action="/backend/Activity/managelxInvitee" method="get" id="search_form">
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
                        <th>安卓当前状态：</th>
                        <td>
                            <?php if ($app==2){ echo "关闭";
                            }else{ echo "打开";} ?>
                        </td>
                    </tr>
                    <tr>
                        <th>安卓状态修改：</th>
                        <td>
                            <label for="chanel1" class="mr10"><input type="radio" class="radio" name="app_status" value="1" <?php if ($app==1): echo "checked";
endif; ?>>打开</label>
                            <label for="chanel2" class="mr10"><input type="radio" class="radio" name="app_status" value="2" <?php if ($app==2): echo "checked";
endif; ?>>关闭</label>
                        </td>
                    </tr>
                    <tr>
                        <th>安卓提示文案：</th>
                        <td>
                            <input type="text" class="ipt w222" name="app_content" value="<?php echo $app_content; ?>">
                            标题（长度建议在<span class="cRed">10</span>个字以内）
                        </td>
                    </tr>
                    <tr>
                        <th>ios当前状态：</th>
                        <td>
                            <?php if ($ios==2){ echo "关闭";
                            }else{ echo "打开";} ?>
                        </td>
                    </tr>
                    <tr>
                        <th>ios状态修改：</th>
                        <td>
                            <label for="chanel1" class="mr10"><input type="radio" class="radio" name="ios_status" value="1" <?php if ($ios==1): echo "checked";
endif; ?>>打开</label>
                            <label for="chanel2" class="mr10"><input type="radio" class="radio" name="ios_status" value="2" <?php if ($ios==2): echo "checked";
endif; ?>>关闭</label>
                        </td>
                    </tr>
                    <tr>
                        <th>IOS提示文案：</th>
                        <td>
                            <input type="text" class="ipt w222" name="ios_content" value="<?php echo $ios_content; ?>">
                            标题（长度建议在<span class="cRed">10</span>个字以内）
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <a href="javascript:void(0);" class="btn-blue submitLx">保存</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    $(function(){
        $(".submitLx").click(function(){
            var app_content = $("input[name='app_content']").val();
            var ios_content = $("input[name='ios_content']").val();
            if(app_content.length > 20){
                alert("安卓提示文案长度建议在10个字以内，不超过20个字");
                return false;
            }
            if(ios_content.length > 20){
                alert("IOS提示文案长度建议在10个字以内，不超过20个字");
                return false;
            }       
            $('#search_form').submit();
        });
    }); 
</script>