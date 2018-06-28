<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：信息管理>&nbsp;&gt;&nbsp;<a href="/backend/Notice/">公告管理</a>&nbsp;&gt;&nbsp;<a href="">公告编辑</a></div>
<div class="wbase mt20">
  <div class="data-table-log">
    <form action ="/backend/Notice/do_add_update/" method="post" id="add_form">
    <table>
      <colgroup>
        <col width="65" />
        <col width="919" />
      </colgroup>
      <tr>
        <td class="fb">公告标题</td>
        <td>
            <input type="text" class="ipt w910" name="title" value="<?php echo $notice['title'] ?>">
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
         <?php foreach ($this->category as $key => $value): ?>
             <label><input type="radio" class="radio" name="category" value="<?php echo $key; ?>" <?php if($notice['category'] == $key): echo "checked"; endif; ?>><?php echo $value;?></label>
         <?php endforeach; ?>
        </td>
      </tr>
      <tr>
        <td class="fb">公告内容</td>
        <td>
          <div class="richTextEdit-wrap w910">
            <textarea name="" class="textarea w910" id="edit_notice" cols="30" rows="10" name="content"><?php echo $notice['content'] ?></textarea>
          </div>
        </td>
      </tr>
      <tr>
        <td class="fb">是否显示</td>
        <td>
            <label for="radio-yes" class="mr35"><input type="radio" class="radio" name="status" value="1" <?php if($notice['status']==1): echo "checked"; endif; ?>>是</label>
          <label for="radio-no"><input type="radio" class="radio" name="status"  value="0" <?php if($notice['status']==0): echo "checked"; endif; ?>>否</label>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="tac">
          <a href="javascript:;" class="btn-blue-h32 mlr15" id="submitForm">提 交</a>
          <a href="javascript:;" class="btn-b-white mlr15" onclick="history.go(-1)">取 消</a>
        </td>
      </tr>
    </table>
        <?php if(!empty($notice)): ?> <input type="hidden" value="<?php echo $notice['id'];?>" name="id" /><?php endif;?>
    </form>
  </div>
</div>
<script  charset="utf-8" src="/source/kindeditor/kindeditor-all-min.js"></script>
<script   charset="utf-8" src="/source/kindeditor/lang/zh_CN.js"></script>
<script>
        var editor;
        KindEditor.ready(function(K) {
                editor = K.create('#edit_notice', {
                    height:"400px",
                    uploadJson : '/backend/Notice/upload/',
                    items:[
                    'source', '|', 'undo', 'redo', '|', 'preview','cut', 'copy', 'paste',
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
        
        $("#submitForm").click(function(){
            $.ajax({
               type: "POST",
               url: "/backend/Notice/do_add_update/",
               data: $("#add_form").serialize()+"&content=" + encodeURIComponent(editor.html()),
               success: function(data){
                   var json = jQuery.parseJSON(data);
                    alert(json.message);
                    if (json.status == 'y')
                    {
                        location.href="/backend/Notice/";
                    }
               }
            });
            return false;
        });
        
</script>
</body>
</html>
