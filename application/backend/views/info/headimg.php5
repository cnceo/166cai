<?php
if ($fromType != 'ajax'): $this->load->view("templates/head");
endif;
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
        <div class="path">您的位置：用户头像管理&nbsp;&gt;&nbsp;<a href="/backend/Info/headimgManage">用户头像管理</a></div>
    <?php endif; ?>
    <div class="data-table-filter mt10" style="width:960px">
        <form action="" method="get"  id="search_form">
            <table>
                <colgroup>
                    <col width="35%"/>
                    <col width="35%"/>
                    <col width="30%"/>
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
                        上传时间：
                        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1"/><i></i></span>
                        <span class="ml8 mr8">至</span>
                        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1"/><i></i></span>
                    </td>
                    <td>
                        &nbsp;&nbsp;
                        <input type="checkbox" name="forbidden" value="1" <?php if($search['forbidden']):?>checked=""<?php endif;?>>禁止上传
                        &nbsp;
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="btn-blue "
                           onclick="$('#search_form').submit();">查询</a>
                    </td>
                </tr>
                <tr>
                    <?php if($config['headimg_config'] == 0){ ?>
                    <td>
                        <a href="javascript:void(0);" class="btn-blue "
                           onclick="forbiddenAll(1);">全部禁止上传头像</a>
                    </td>
                    <?php }else{ ?>
                    <td>
                        <a href="javascript:void(0);" class="btn-red " style="width:130px;"
                           onclick="forbiddenAll(0);">解除禁止上传头像</a>
                    </td>
                    <?php } ?>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div class="data-table-list mt20">
        <table>
            <colgroup>
                <col width="5%" />
                <col width="15%" />
                <col width="15%" />
                <col width="15%" />
                <col width="20%" />
            </colgroup>
            <thead>
                <tr>
                    <th><input type="checkbox" class="_ck"></th>
                    <th>上传时间</th>
                    <th>用户名</th>
                    <th>上传头像</th>
                    <th>操作</th>
                </tr>
            </thead>
            <?php foreach ($imgs as $key => $val): ?>
            <tr>
                <td><input type="checkbox" class="ck_" value="<?php echo $val['uid']; ?>"></td>
                <td><?php echo $val['created']; ?></td>
                <td><a href="/backend/User/user_manage/?uid=<?php echo $val['uid'] ?>" class="cBlue" target="_blank"><?php echo $val['uname']; ?></td>
                <td>
                    <img src="<?php echo $val['headimgurl']; ?>" style="width:20px;height: 20px;">
                </td>
                <td>
                    <a href="javascript:;" data-src="<?php echo $val['headimgurl'] ?>" class="cBlue imgShow">查看原图</a>
                    &nbsp;&nbsp;
                    <?php if($val['headimgurl']){ ?>
                    <a href="javascript:;"  onclick="deleteImg(<?php echo $val['uid'] ?>);" class="cBlue ">删除</a>
                    <?php }else{ ?>
                    <b>已删除</b>
                    <?php } ?>
                    &nbsp;&nbsp;
                    <?php if($val['headimg_status']==1){ ?>
                    <a href="javascript:;" onclick="forbiddenUpload(<?php echo $val['uid'] ?>,2)" class="cBlue ">禁止上传</a>
                    <?php }else{ ?>
                    <a href="javascript:;" onclick="forbiddenUpload(<?php echo $val['uid'] ?>,1)" class="cBlue ">解除禁止上传</a>
                    <?php } ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <tfoot>
                <tr>
                    <td colspan="5">
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
<div class="pop-dialog" id="headimg" style="display:none;">
    <div class="pop-in pop-examine">
        <div class="pop-head">
            <h2 class="tac">提示</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <p style="text-align:center;">
            <img id="headimgurl">
            </p>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:closePop();" class="btn-blue pop-btn">确认</a>
        </div>
    </div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script type="text/javascript">
    function forbiddenAll(type){
        if(confirm("是否全部禁止上传头像操作？")){
            $.ajax({
                type: "post",
                url: "/backend/Info/updateUploadConfig",
                data: {'type':type},
                success: function(data){
                    var json = jQuery.parseJSON(data);
                    alert(json.message);
                    location.reload();
                }
            });
        }
    }
    
    function deleteImg(uid){
        if(confirm("是否删除用户上传头像？")){
            $.ajax({
                type: "post",
                url: "/backend/Info/deleteImg",
                data: {'uid':uid},
                success: function(data){
                    var json = jQuery.parseJSON(data);
                    alert(json.message);
                    location.reload();
                }
            });
        }
    }
    
    function forbiddenUpload(uid,type){
        if(confirm("是否禁止用户上传头像？")){
            $.ajax({
                type: "post",
                url: "/backend/Info/forbiddenUpload",
                data: {'uid':uid,'type':type},
                success: function(data){
                    var json = jQuery.parseJSON(data);
                    alert(json.message);
                    location.reload();
                }
            });
        }
    }
    
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
            var html = '是否删除这' + ids.length + '图片？';
            if(ids.length < 1)
            {
                alert('请先选择需要删除的图片');
                return false;
            }
            else
            {
                $("#deleteAlert").html(html);
                popdialog("dialog-deletAll");

                
            }
        });

        $('#deleteConfirm').click(function(){
            if(ids.length < 1)
            {
                alert('请先选择需要手动成功的图片');
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "/backend/Info/deleteImg",
                    data: {'uid':ids},
                    success: function(data){
                        var json = jQuery.parseJSON(data);
                        alert(json.message);
                        location.reload();
                    }
                });
            }
        });
        
        $(".imgShow").click(function(){
           var src = $(this).data('src');
           $("#headimgurl").attr('src',src); 
           popdialog("headimg");
        });
    });
</script>
<?php if ($fromType != 'ajax'): ?>
    </body>
    </html>
<?php endif; ?>