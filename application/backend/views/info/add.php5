<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="">资讯中心管理</a>&nbsp;&gt;&nbsp;<a href="">资讯编辑</a></div>
<div class="wbase mt20">
    <div class="data-table-log">
        <form action="/backend/Info/do_add_update/" method="post" id="add_form">
            <table>
                <colgroup>
                    <col width="65"/>
                    <col width="919"/>
                </colgroup>
                <tr>
                    <td class="fb">资讯标题</td>
                    <td>
                        <input type="text" class="ipt w910" name="title" value="<?php echo str_replace(array('"',"'"), array('&#34;','&#39;'), $notice['title']); ?>">
                    </td>
                </tr>
                <tr>
                    <td class="fb">权重</td>
                    <td>
                        <input type="text" class="ipt w910" name="weight" value="<?php echo $notice['weight'] ?>">
                    </td>
                </tr>
                <tr>
                    <td class="fb">分类</td>
                    <td>
                        <select class="selectList w150"  name="category" id="selectCat">
                            <?php foreach($categoryList as $key => $value): ?>
                                <option value="<?php echo $key;?>" <?php if($notice['category_id'] == $key): echo "selected"; endif;?>><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="fb">资讯内容</td>
                    <td>
                        <div class="richTextEdit-wrap w910">
                            <textarea name="" class="textarea w910" id="edit_notice" cols="30" rows="10"
                                      name="content"><?php echo $notice['content'] ?></textarea>
                        </div>
                    </td>
                </tr>
                <tr id="is_show">
                    <td class="fb">是否显示</td>
                    <td>
                        <label for="radio-yes" class="mr35"><input type="radio" class="radio" name="status" id="radio-yes"
                                                                   value="1" <?php if ($notice['is_show'] == 1): echo "checked"; endif; ?>>是</label>
                        <label for="radio-no"><input type="radio" class="radio" name="status" id="radio-no"
                                                     value="0" <?php if (!$notice['is_show']): echo "checked"; endif; ?>>否</label>
                    </td>
                </tr>
                <tr <?php if (!$notice['is_show']) {?>style="display:none"<?php }?>>
                    <td class="fb">显示平台</td>
                    <td>
                        <label for="platform_pc" class="mr35"><input type="checkbox" id="platform_pc" name="platform0" <?php if (empty($notice) || $notice['platform'] & 1): echo "checked"; endif; ?>>网页</label>
                        <label for="platform_android"><input type="checkbox" id="platform_android" name="platform1" <?php if (empty($notice) || $notice['platform'] & 2): echo "checked"; endif; ?>>ANDROID</label>
                        <label for="platform_ios"><input type="checkbox" id="platform_ios" name="platform2" <?php if (empty($notice) || $notice['platform'] & 4): echo "checked"; endif; ?>>IOS</label>
                        <label for="platform_m"><input type="checkbox" id="platform_m" name="platform3" <?php if (empty($notice) || $notice['platform'] & 8): echo "checked"; endif; ?>>M版</label>
                    </td>
                </tr>    
                <tr id="selectBet" style="display:<?php echo ($notice['category_id'] == '1' || empty($notice)) ? 'block' : 'none'; ?>">
                    <td class="fb">底部按钮</td>
                    <td>
                        <select class="selectList w150"  name="lotteryBet" style="padding-top:1px">
                            <option value="0" <?php if(empty($notice['additions'])): echo "selected"; endif;?>><?php echo '无按钮'; ?></option>
                            <?php foreach($lotteryList as $key => $value): ?>
                                <option value="<?php echo $key;?>" <?php if($notice['additions'] == $key): echo "selected"; endif;?>><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="tac">
                        <a href="javascript:;" class="btn-blue-h32 mlr15" id="submitForm">提 交</a>
                        <a href="javascript:;" class="btn-b-white mlr15" onclick="history.go(-1)">取 消</a>
                    </td>
                </tr>
            </table>
            <?php if ( ! empty($notice)): ?> <input type="hidden" value="<?php echo $notice['id']; ?>"
                                                    name="id" /><?php endif; ?>
        </form>
    </div>
</div>
<script charset="utf-8" src="/source/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="/source/kindeditor/lang/zh_CN.js"></script>
<script>
    var editor;
    KindEditor.ready(function (K) {
        editor = K.create('#edit_notice', {
            height: "400px",
            uploadJson: '/backend/Info/upload/',
            items: [
                'source', '|', 'undo', 'redo', '|', 'preview', 'cut', 'copy', 'paste',
                'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
                'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
                'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
                'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image',
                'table', 'hr', 'emoticons',
                'anchor', 'link', 'unlink', '|', 'about'
            ]
        });
    });

    $("#submitForm").click(function () {
        $.ajax({
            type: "POST",
            url: "/backend/Info/do_add_update/",
            data: $("#add_form").serialize() + "&content=" + encodeURIComponent(editor.html()),
            success: function (data) {
                var json = jQuery.parseJSON(data);
                alert(json.message);
                if (json.status == 'y') {
                    location.href = "/backend/Info/center?<?php echo http_build_query($search)?>";
                }
            }
        });
        return false;
    });

    $('#is_show').on('click', 'input:radio', function(){
        if ($(this).val() === '1') {
        	$('#is_show').next('tr').show();
        }else {
        	$('#is_show').next('tr').hide();
        }
    })

    $('#selectCat').change(function(){
        var category_id = $(this).find("option:selected").val();
        if(category_id == '1'){
            $('#selectBet').show();
        }else{
            $('#selectBet').hide();
        }
    });

</script>
