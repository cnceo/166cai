<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="/backend/Info/center">资讯中心管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/Info/crawl">资讯抓取配置</a></li>
            <li><a href="/backend/Info/center">资讯管理</a></li>
            <li><a href="/backend/Info/nba">NBA伤病管理</a></li>
            <li class="current"><a href="/backend/Info/banner">banner图管理</a></li>
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
            <tr>
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
<script>
$(function() {

	$(".submit").click(function(){
		$("form").submit();
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