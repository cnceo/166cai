<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：运营管理>&nbsp;&gt;&nbsp;<a href="/backend/Operation/">反馈管理</a>&nbsp;&gt;&nbsp;<a href="">详情</a></div>
<div class="wbase mt20">
  <div class="">
    <form action ="/backend/Operatio/reply" method="post" id="add_form">
    <table>
      <colgroup>
        <col width="65" />
        <col width="919" />
      </colgroup>
      <tr>
        <td class="fb">用户名</td>
        <td>
            <?php echo $ope['name'] ?>
        </td>
      </tr>
      <tr>
        <td class="fb ptb5">反馈时间</td>
        <td>
            <?php echo $ope['created'] ?>
        </td>
      </tr>
        <tr>
            <td class="fb ptb5">反馈平台</td>
            <td>
                <?php echo ($ope['platform'] == 0) ? "网页" : ($ope['platform'] == 1 ? "Android" : ($ope['platform'] == 2 ? "IOS" : "M版")); ?>
            </td>
        </tr>
       <tr>
        <td class="fb ptb5">反馈类型</td>
        <td>
            <?php echo $this->o_type[$ope['type']] ?>
        </td>
      </tr>
      <tr>
        <td class="fb"  style="vertical-align:top">反馈内容</td>
        <td>
            <div class="richTextEdit-wrap w830 ptb10 pl15 mb10" style="border:1px solid #eee;">
                <?php echo $ope['content'] ?>
            </div>
        </td>
      </tr>
      <tr>
        <td class="fb ptb5" style="vertical-align:top">回复内容</td>
        <td>
            <?php foreach($ope['reply'] as $value): ?>
                <div class="richTextEdit-wrap  ptb10 pl15 w830 mb10" style="border:1px solid #eee">
                    <div id="reply_<?php echo $value['id'] ?>"><?php echo $value['content'] ?></div>
                    <?php echo $value['name'] ?> <?php echo $value['created'] ?>
                    <a href="id=<?php echo $value['id']?>" class="cBlue delete " data-id = "<?php echo $value['id']?>" data-content = "<?php echo $value['content'] ?>" data-name = "<?php echo $value['name'] ?>" data-created = "<?php echo $value['created'] ?>">删除</a> <!--<a  class="cBlue edit" href="<?php echo $value['id'] ?>">编辑</a>-->
                </div>
            <?php endforeach; ?>
        </td>
      </tr>
      <tr>
          <td class="fb"></td>
          <td>
              <div class="fb mt10">发表回复</div>
              <textarea  class="textarea w830"  cols="30" rows="30" name="content"></textarea>
          </td>
      </tr>
      <tr>
        <td colspan="2" class="tac">
          <a href="javascript:;" class="btn-blue-h32 mlr15" id="submitForm">提 交</a>
          <a href="javascript:;" class="btn-b-white mlr15" onclick="history.go(-1)">取 消</a>
        </td>
      </tr>
    </table>
        <?php if(!empty($ope)): ?> 
            <input type="hidden" value="<?php echo $ope['uid'];?>" name="uid" />
            <input type="hidden" value="<?php echo $ope['id'];?>" name="id" />
            <input type="hidden" value="<?php echo $ope['name'];?>" name="name" />
            <input type="hidden" value="<?php echo $ope['created'];?>" name="created" />
        <?php endif;?>
    </form>
  </div>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<!-- 审核弹窗 -->
<form id='checkForm' method='post' action=''>
    <div class="pop-dialog" id="J-dc-addAccount">
        <div class="pop-in">
            <div class="pop-head">
                <h2>编辑回复</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
                <div class="data-table-filter del-percent">
                    <table>
                        <colgroup>
                            <col width="280" />
                        </colgroup>
                        <tbody>   
                            <tr><td><textarea  class="textarea w830"  cols="30" rows="30" name="edit_content" id="edit_content"></textarea></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id='submitForm1'>提 交</a>
                <a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a>
            </div>
        </div>
    </div>
    <input type="hidden" name="reply_id" value="" id="reply_id" />
</form>
<script>

    $("#submitForm").click(function(){
        $.ajax({
           type: "POST",
           url: "/backend/Operation/reply/",
           data: $("#add_form").serialize(),
           success: function(data){
               var json = jQuery.parseJSON(data);
                alert(json.message);
                if (json.status == 'y')
                {
                    self.location=document.referrer;
                }
           }
        });
        return false;
    });
    
    $(".delete").click(function(){
        var _this = $(this);
        var id = _this.data('id'),
            content = _this.data('content'),
            name = _this.data('name'),
            created = _this.data('created');
        $.ajax({
            type: "POST",
            url: "/backend/Operation/del/",
            data: {id:id, content:content, name:name, created:created},
            success: function(data){
                var json = jQuery.parseJSON(data);
                alert(json.message);
                if (json.status == 'y')
                    {
                        _this.parent().remove();
                    }
                }
            });
        return false;    
    });
    
    $(".edit").click(function(){
        var _this = $(this);
        $("#reply_id").val(_this.attr("href"));
        $("#edit_content").html(_this.prevAll("div").html());
        popdialog("J-dc-addAccount");        
        return false;    
    });
    
    $("#submitForm1").click(function(){
        $.ajax({
            type: "POST",
            url: "/backend/Operation/edit/",
            data: $("#checkForm").serialize(),
            success: function(data){
                var json = jQuery.parseJSON(data);
                alert(json.message);
                if (json.status == 'y')
                    {
                        $("#reply_"+$("#reply_id").val()).html($("#edit_content").val());
                        closePop();
                    }
                }
            });
            return false;    
    });
        
</script>
</body>
</html>
