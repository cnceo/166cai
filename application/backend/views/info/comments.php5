<?php
if ($fromType != 'ajax'): $this->load->view("templates/head");
endif;

$categoryUrls = array(
    1 => 'csxw',
    2 => 'ssq',
    3 => 'qtfc',
    4 => 'dlt',
    5 => 'qttc',
    6 => 'jczq',
    7 => 'sfc',
    8 => 'jclq',
    9 => 'zjtjzq',
    10 => 'zjtjlq',
);

?>
<style>
    .emojione {
         display: inline-block;
         width: 1.1em;
         height: auto;
         line-height: normal;
         vertical-align: text-top;
         font-size: inherit;
    }
</style>
<div class="frame-container" style="margin-left:0;padding-left: 0px;">
    <?php
    if ($fromType != 'ajax'):
        ?>
        <div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="/backend/Info/comments">评论管理</a></div>
    <?php endif; ?>
    <div class="data-table-filter mt10" style="width:960px">
        <form action="" method="get"  id="search_form">
            <table>
                <colgroup>
                    <col width="35%"/>
                    <col width="25%"/>
                    <col width="40%"/>
                </colgroup>
                <tbody>
                <tr>
                    <td>
                        用户名：
                        &nbsp;&nbsp;
                        <input type="text" class="ipt w184" name="uname"
                               value="<?php echo $search['uname'] ?>" placeholder=""/>
                    </td>
                    <td>
                        标题：
                        &nbsp;&nbsp;
                        <input type="text" class="ipt w184" name="title"
                               value="<?php echo $search['title'] ?>" placeholder=""/>
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
                        <input type="checkbox" name="number" value="1" <?php if($search['number']):?>checked=""<?php endif;?>>含数字
                        &nbsp;
                        <input type="checkbox" name="word" value="1" <?php if($search['word']):?>checked=""<?php endif;?>>含字母
                        &nbsp;
                        <input type="checkbox" name="chinesenumer" value="1" <?php if($search['chinesenumer']):?>checked=""<?php endif;?>>含中文数字
                        &nbsp;
                        <input type="checkbox" name="delete" value="1" <?php if($search['delete']):?>checked=""<?php endif;?>>未删除
                        &nbsp;
                        <input type="checkbox" name="uncomment" value="1" <?php if($search['uncomment']):?>checked=""<?php endif;?>>禁言用户
                        &nbsp;
                        <input type="checkbox" name="replied" value="1" <?php if($search['replied']):?>checked=""<?php endif;?>>已回复
                        &nbsp;
                        <input type="checkbox" name="replyadmin" value="1" <?php if($search['replyadmin']):?>checked=""<?php endif;?>>用户回复166彩票（待反馈）
                    </td>
                    <td>
                        &nbsp;&nbsp;&nbsp;
                        审核状态：
                        <select class="selectList w150" name="status" style="padding: 1px 5px;">
                            <?php foreach ($checkStatus as $key => $value): ?>
                                <option
                                    value="<?php echo $key; ?>" <?php if ($search['status'] == $key): echo "selected"; endif; ?>><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="btn-blue "
                           onclick="$('#search_form').submit();">查询</a>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php if ($fromType == 'ajax'): ?><input type="hidden" name="uid"
                       value="<?php echo $search['uid'] ?>"/><?php endif; ?>
        </form>
    </div>
    <div class="data-table-list mt20">
        <table>
            <colgroup>
                <col width="5%" />
                <col width="10%" />
                <col width="14%" />
                <col width="5%" />
                <col width="10%" />
                <col width="12%" />
                <col width="8%" />
                <col width="7%" />
                <col width="10%" />
                <col width="10%" />
                <col width="14%" />
            </colgroup>
            <thead>
                <tr>
                    <th><input type="checkbox" class="_ck"></th>
                    <th>用户名</th>
                    <th>评论</th>
                    <th>楼层</th>
                    <th>评论时间</th>
                    <th>标题</th>
                    <th>URL</th>
                    <th>审核状态</th>
                    <th>敏感词</th>
                    <th>操作</th>
                    <?php if($search['replied']): ?>
                        <th>官方回复内容</th>
                    <?php else: ?>
                        <th>原评论</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <?php if(!empty($comments)): ?>
            <?php if($search['replied']): ?>
                <?php foreach ($comments as $key => $val): ?>
                    <?php $cid = ($val['tid'] > 0) ? $val['tid'] : $val['pid']; ?>
                    <tr>
                        <td><?php if($val['delete_flag'] == '0'):?><input type="checkbox" class="ck_" value="<?php echo $val['newsId'] . '-' . $cid;?>"><?php endif;?></td>
                        <td><a href="/backend/User/user_manage/?uid=<?php echo $val['tuid'] ?>" class="cBlue" target="_blank"><?php echo $val['tuname']; ?></a></td>
                        <td><?php echo emoji4img($val['tcontent']); ?></td>
                        <td><?php echo $val['tfloor']; ?></td>
                        <td><?php echo $val['tcreated']; ?></td>
                        <td><?php echo $val['title']; ?></td>
                        <td><a href="http://888.166cai.cn/info/<?php echo $categoryUrls[$val['category_id']]?>/<?php echo $val['newsId'] ?>" target="_blank" class="cBlue">/info/<?php echo $categoryUrls[$val['category_id']]?>/<?php echo $val['newsId'] ?></a></td>
                        <td><?php echo $checkStatus[$val['tstatus'] + 1]; ?></td>
                        <td><?php echo $val['tsensitives']; ?></td>
                        <td data-index="<?php echo $val['newsId'] . '-' . $cid;?>">
                            <?php if($val['tdelete_flag'] == '0'): ?>
                                <?php if($val['tstatus'] == 2): ?>
                                <a href="javascript:;" class="cBlue handleSucc">手动成功</a>
                                <?php endif; ?>
                                <a href="javascript:;" class="cBlue deleteIt">删除</a>
                            <?php else: ?>
                                已删除
                            <?php endif;?>
                            <?php if($val['tuncomment'] == 0): ?>
                            <a href="javascript:;" data-uid="<?php echo $val['tuid'] ?>" data-status="1" class="cBlue handlecomment">禁言</a>
                            <?php else: ?>
                            <a href="javascript:;" data-uid="<?php echo $val['tuid'] ?>" data-status="0" class="cBlue handlecomment">解除禁言</a>
                            <?php endif;?>
                            <?php if($val['tstatus'] == '1' && $val['tdelete_flag'] == '0'): ?>
                            <a href="javascript:;" data-id="<?php echo $cid ?>" data-type="1" class="cBlue replycomment">回复</a>
                            <?php endif;?>
                        </td>
                        <td><?php echo emoji4img($val['content']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($comments as $key => $val): ?>
                    <tr>
                        <td><?php if($val['delete_flag'] == '0'):?><input type="checkbox" class="ck_" value="<?php echo $val['newsId'] . '-' . $val['id'];?>"><?php endif;?></td>
                        <td><a href="/backend/User/user_manage/?uid=<?php echo $val['uid'] ?>" class="cBlue" target="_blank"><?php echo $val['uname']; ?></a></td>
                        <td><?php echo emoji4img($val['content']); ?></td>
                        <td><?php echo $val['floor']; ?></td>
                        <td><?php echo $val['created']; ?></td>
                        <td><?php echo $val['title']; ?></td>
                        <td><a href="http://888.166cai.cn/info/<?php echo $categoryUrls[$val['category_id']]?>/<?php echo $val['newsId'] ?>" target="_blank" class="cBlue">/info/<?php echo $categoryUrls[$val['category_id']]?>/<?php echo $val['newsId'] ?></a></td>
                        <td><?php echo $checkStatus[$val['status'] + 1]; ?></td>
                        <td><?php echo $val['sensitives']; ?></td>
                        <td data-index="<?php echo $val['newsId'] . '-' . $val['id'];?>">
                            <?php if($val['delete_flag'] == '0'): ?>
                                <?php if($val['status'] == 2): ?>
                                <a href="javascript:;" class="cBlue handleSucc">手动成功</a>
                                <?php endif; ?>
                                <a href="javascript:;" class="cBlue deleteIt">删除</a>
                            <?php else: ?>
                                已删除
                            <?php endif;?>
                            <?php if($val['uncomment'] == 0): ?>
                            <a href="javascript:;" data-uid="<?php echo $val['uid'] ?>" data-status="1" class="cBlue handlecomment">禁言</a>
                            <?php else: ?>
                            <a href="javascript:;" data-uid="<?php echo $val['uid'] ?>" data-status="0" class="cBlue handlecomment">解除禁言</a>
                            <?php endif;?>
                            <?php if($val['status'] == '1' && $val['delete_flag'] == '0'): ?>
                            <a href="javascript:;" data-id="<?php echo $val['id'] ?>" data-type="1" class="cBlue replycomment">回复</a>
                            <?php endif;?>
                        </td>
                        <td><?php echo emoji4img($val['tcontent']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php endif; ?>
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
    <div class="page mt10 info_comments">
        <?php echo $pages[0];?>
    </div>
    <a href="javascript:;" class="btn-blue" id="deletAll">批量删除</a>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<!-- 批量删除弹框 -->
<div class="pop-dialog" id="dialog-deletAll" style='display:none;'>
    <div class="pop-in">
        <div class="pop-body">
            <p id="deleteAlert" style="text-align:center;font-size:20px;font-weight:bolder"></p>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:void(0);" class="btn-blue-h32 mlr15" id="deleteConfirm">确认</a>
            <a href="javascript:closePop();" class="btn-b-white mlr15">取消</a><br/><br/>
        </div>
    </div>
</div>
<!-- 手动成功弹框 -->
<div class="pop-dialog" id="dialog-handleSucc" style='display:none;'>
    <div class="pop-in">
        <div class="pop-body">
            <p id="handleAlert" style="text-align:center;font-size:20px;font-weight:bolder">请确认手动成功</p>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:void(0);" class="btn-blue-h32 mlr15" id="handleConfirm">确认</a>
            <a href="javascript:closePop();" class="btn-b-white mlr15">取消</a><br/><br/>
        </div>
    </div>
</div>
<!-- 回复评论弹框 -->
<div class="pop-dialog" id="dialog-reply" style="display:none;">
    <div class="pop-in">
        <div class="pop-head">
            <h2>回复评论</h2>
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
                            <th id="replytouname3"></th>
                            <td id="replytocontent3" style="white-space:inherit"></td>
                        </tr>
                        <tr>
                            <th id="replytouname2"></th>
                            <td id="replytocontent2" style="white-space:inherit"></td>
                        </tr>
                        <tr>
                            <th id="replytouname"></th>
                            <td id="replytocontent" style="white-space:inherit"></td>
                        </tr>
                        <tr>
                            <th class="ptb5 vat">回复：</th>
                            <td>
                                <textarea class="textarea w360" rows="10" cols="30" id="replyval" name=""></textarea>
                                <input type="hidden" name="replynewsId" value=""/>
                                <input type="hidden" name="replyId" value=""/>
                            </td>
                            <input type="hidden" name="replytime" val="">
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15" id="handleReply">确认</a>
            <a href="javascript:closePop();" class="btn-blue-h32 mlr15">取消</a>
        </div>
    </div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script type="text/javascript">
    $(function(){

        $(".Wdate1").focus(function () {
            dataPicker();
        });

        // 全选
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

        // 批量删除
        var ids;
        $("#deletAll").click(function(){
            ids = [];
            $(".ck_").each(function(){
                if(this.checked)
                {
                    ids.push($(this).val());
                }
            })
            var html = '是否删除这' + ids.length + '条评论？';
            if(ids.length < 1)
            {
                alert('请先选择需要删除的评论');
                return false;
            }
            else
            {
                $("#deleteAlert").html(html);
                popdialog("dialog-deletAll");

                
            }
        });

        // 单条删除
        $(".deleteIt").click(function(){
            ids = [];
            ids.push($(this).parent('td').data('index'));
            var html = '是否删除评论“' + $(this).parent('td').parent('tr').find('td').eq(2).html() + '”？';
            if(ids.length < 1)
            {
                alert('请先选择需要删除的评论');
                return false;
            }
            else
            {
                $("#deleteAlert").html(html);
                popdialog("dialog-deletAll");   
            }
        });

        // 确认删除
        $('#deleteConfirm').click(function(){
            if(ids.length < 1)
            {
                alert('请先选择需要删除的评论');
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "/backend/Info/delComments/",
                    data: {'ids':ids},
                    success: function(data){
                        var json = jQuery.parseJSON(data);
                        alert(json.message);
                        location.reload();
                    }
                });
            }
        });

        // 手动成功
        $(".handleSucc").click(function(){
            ids = [];
            ids.push($(this).parent('td').data('index'));
            var html = '是否将评论“' + $(this).parent('td').parent('tr').find('td').eq(2).html() + '”置为成功？';
            if(ids.length < 1)
            {
                alert('请先选择需要手动成功的评论');
                return false;
            }
            else
            {
                $("#handleAlert").html(html);
                popdialog("dialog-handleSucc");
            }
        });

        $('#handleConfirm').click(function(){
            if(ids.length < 1)
            {
                alert('请先选择需要手动成功的评论');
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "/backend/Info/handleCommSucc/",
                    data: {'ids':ids},
                    success: function(data){
                        var json = jQuery.parseJSON(data);
                        alert(json.message);
                        location.reload();
                    }
                });
            }
        });
        
        $(".handlecomment").click(function(){
            var uid=$(this).data('uid');
            var status=$(this).data('status');
            $.ajax({
                    type: "post",
                    url: "/backend/Info/handlecomment",
                    data: {'uid':uid,'status':status},
                    success: function(data){
                        var json = jQuery.parseJSON(data);
                        alert(json.message);
                        location.reload();
                    }
                });
        });

        // 回复
        $(".replycomment").click(function(){
            var id = $(this).data('id');
            var type = $(this).data('type');
            $.ajax({
                    type: "post",
                    url: "/backend/Info/getComment",
                    data: {'id':id, 'type':type},
                    success: function(data){
                        var json = jQuery.parseJSON(data);
                        if(json.status == '1'){
                            if(json.up.uname){
                                $('#replytouname2').text(json.up.uname);
                                $('#replytocontent2').html(json.up.content); 
                            }
                            if(json.up2.uname){
                                $('#replytouname3').text(json.up2.uname);
                                $('#replytocontent3').html(json.up2.content); 
                            }
                            $('#replytouname').text(json.data.uname);
                            $('#replytocontent').html(json.data.content);
                            $('input[name="replynewsId"]').val(json.data.newsId);
                            $('input[name="replyId"]').val(json.data.commentId);
                            $('input[name="replytime"]').val(json.data.created);
                            popdialog("dialog-reply"); 
                        }else{
                           alert(json.message); 
                        }
                    }
                });
        });

        $("#handleReply").click(function(){
            var id = $('input[name="replynewsId"]').val();
            var content = $('#replyval').val();
            var commentId = $('input[name="replyId"]').val();
            var uname = $("#replytouname").text();
            var time = $('input[name="replytime"]').val();
            $.ajax({
                    type: "post",
                    url: "/backend/Info/postComment",
                    data: {id:id,content:content,commentId:commentId,uname:uname,time:time},
                    success: function(data){
                        var json = jQuery.parseJSON(data);
                        if(json.status == '1'){
                            alert(json.msg); 
                            location.reload();
                        }else{
                            alert(json.msg); 
                        }
                    }
                });
        });

        // 点击切换
        $('input[name="replied"]').change(function() { 
            $('input[name="replyadmin"]').attr("checked", false);
        });

        $('input[name="replyadmin"]').change(function() { 
            $('input[name="replied"]').attr("checked", false);
        });
    });
</script>
<?php if ($fromType != 'ajax'): ?>
    </body>
    </html>
<?php endif; ?>