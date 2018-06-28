<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">信息管理</a>&nbsp;&gt;&nbsp;<a href="">banner管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
          <li><a href="/backend/banner">投注页banner</a></li>
          <li><a href="/backend/banner/index/zcfc">左侧浮层广告</a></li>
          <li><a href="/backend/banner/index/ycfc">右侧banner</a></li>
          <li><a href="/backend/Appconfig/info/android">资讯页悬浮窗</a></li>
          <li><a href="/backend/Appconfig/orderdetail/android">Android订单详情</a></li>
          <li><a href="/backend/Appconfig/orderdetail/ios">ios订单详情</a></li>
          <li class="current"><a href="/backend/Appconfig/jfShop">积分商城广告</a></li>
        </ul>
    </div>
    <div class="mod-tab-bd">
    <ul>
      <li style="display: block">
      <form action="" method="post" id="banner_form">
      <div class="data-table-list mt10">
          <table>
            <colgroup>
              <col width="5%" />
              <col width="20%" />
              <col width="26%" />
              <col width="24%" />
              <col width="5%" />
            </colgroup>
            <thead>
              <tr>
                <th>序号</th>
                <th>标题（长度建议在<span class="cRed">10-15</span>个字之间）</th>
                <th>图片</th>
                <th>链接</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody id="pic-table">
            <?php for ($i = 0; $i < 5; $i++) {?>
            <tr class='sub_tr'>
              <td><input type="text" class="ipt w40 tac" name="banner[<?php echo $i?>][priority]"  value="<?php echo $banner[$i]['priority']?>"></td>
              <td>
                <input type="text" class="ipt tac w184" name="banner[<?php echo $i?>][title]" value="<?php echo $banner[$i]['title']?>">
              </td>
              <td>
                <div class="btn-white file">选择文件</div>
                <div class="btn-white upload" data-index="<?php echo $i?>">开始上传</div>
                <input type="hidden" name="banner[<?php echo $i?>][path]" id="path_<?php echo $i?>" value="<?php echo $banner[$i]['path']?>">
                <div id="imgdiv0" class="imgDiv"><img id="imgShow<?php echo $i?>" src="/uploads/infobanner/<?php echo $banner[$i]['path']?>" width="50" height="50" /></div>
              </td>
              <td>
                <input type="text" class="ipt tac w222" name="banner[<?php echo $i?>][url]" value="<?php echo $banner[$i]['url']?>">
              </td>
              <td>
                <a href="javascript:;" class="cBlue removeTr">清空</a>
              </td>
            </tr>
            <?php }?>
            </tbody>
          </table>
          
          <!-- <a href="javascript:;" class="btn-white mt20" id="add-row">添加一行</a> -->
          <div class="tac">
          <a class="btn-blue mt20 submit">保存并上线</a>
          </div>
          </div>
       </form>
       </li>
       </ul>
    </div>
</div>
<script src="/source/js/webuploader.min.js"></script>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script>
$(function() {

  $(".submit").click(function(){
    layer.confirm('是否确认保存并上线？', {
      btn: ['确定'] ,
      'title' : '温馨提示',
      'btnAlign': 'c'
    }, function(){
      layer.closeAll();
      //验证是否有一条完整信息
      var tag = false;
      $('.sub_tr').each(function(index){
        var inputObj = $('.sub_tr').eq(index).find('input');
        var count = 0;
            inputObj.each(function(){
                if($(this).val()!='')
                {
                  count++;
                }
            });
            if(count==4)
            {
              tag = true;
              //return;
            }
      });
      if(tag===false)
      {
        layer.alert('上线内容请填写完整', {icon: 2,btn:'',title:'温馨提示',time:0});
        return;
      }
      layer.load(0, {shade: [0.5, '#393D49']});
      $.ajax({
          type: "post",
          url: "/backend/Appconfig/jfShop",
          data: $('#banner_form').serialize(),
          success: function(data)
          {
            var json = jQuery.parseJSON(data);
            layer.closeAll();
            if(json.status == 'SUCCESSS')
            {
              layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();}});
            }else{
              layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
            }
          }
      });

    });
  })

  var uploader = WebUploader.create({
        swf: '/caipiaoimg/v1.1/js/jUploader.swf',
        pick: '.file',
    });

  $(".upload").click(function(){
    uploader.options.server = "/backend/Info/uploadbanner/"+$(this).data('index');
      uploader.upload();
    })

    uploader.on( 'uploadSuccess', function( file, data) {
        $("#imgShow"+data.index).attr('src', '/uploads/infobanner/'+data.name);
        $("#path_"+data.index).val(data.name);
  });

});
$(".removeTr").click(function(){
  $(this).parents('tr').find('input').val('');
  $(this).parents('tr').find('img').attr('src', '');
})
</script>