<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">信息管理</a>&nbsp;&gt;&nbsp;<a href="">banner管理</a></div>
<div class="mod-tab mt20">
  <div class="mod-tab-hd">
    <ul>
      <li class="changeLi <?php if ($action == 'tzy'): ?>current<?php endif ?>" ><a href="javascript:;">投注页banner</a></li>
      <li class="changeLi <?php if ($action == 'zcfc'): ?>current<?php endif ?>" ><a href="javascript:;">左侧浮层广告</a></li>
      <li class="changeLi <?php if ($action == 'ycfc'): ?>current<?php endif ?>" ><a href="javascript:;">右侧banner</a></li>
      <li><a href="/backend/Appconfig/info/android">资讯页悬浮窗</a></li>
      <li><a href="/backend/Appconfig/orderdetail/android">Android订单详情</a></li>
      <li><a href="/backend/Appconfig/orderdetail/ios">ios订单详情</a></li>
      <li><a href="/backend/Appconfig/jfShop">积分商城广告</a></li>
    </ul>
  </div>
  <div class="mod-tab-bd">
    <ul>
      <!-- 轮播图管理 -->
      <li  <?php if ($action == 'tzy'): ?>style="display: block;"<?php else: ?>style="display: none;"<?php endif ?> >
        <div class="data-table-list mt10">
        <form action="/backend/banner" method="post" id="tzy_form">
          <table>
            <colgroup><col width="5%" /><col width="15%" /><col width="20%" /><col width="20%" /><col width="25%" /><col width="5%" /></colgroup>
            <thead><tr><th>序号</th><th>标题</th><th>图片</th><th>链接</th><th>彩种</th><th>操作</th></tr></thead>
            <tbody id="pic-table">
            <?php for ($i = 0; $i < 5; $i++) {?>
            <tr>
              <td><input type="text" class="ipt w40 tac" name="tzy[<?php echo $i?>][priority]" readonly value="<?php echo $i+1?>"></td>
              <td><input type="text" class="ipt tac w184" name="tzy[<?php echo $i?>][title]" value="<?php echo $tzy[$i]['title']?>"></td>
              <td>
              	<div class="btn-white file">选择文件</div> <div class="btn-white upload" data-index="<?php echo $i?>">开始上传</div>
                <input type="hidden" name="tzy[<?php echo $i?>][path]" id="path_<?php echo $i?>" value="<?php echo $tzy[$i]['path']?>">
                <div id="imgdiv0" class="imgDiv"><img id="imgShow<?php echo $i?>" src="/uploads/banner/<?php echo $tzy[$i]['path']?>" width="50" height="50" /></div>
              </td>
              <td><input type="text" class="ipt tac w222" name="tzy[<?php echo $i?>][url]" value="<?php echo $tzy[$i]['url']?>"></td>
              <td>
              <?php $j = 0;foreach ($tzylocationArr as $l => $locatArr) {
                  foreach ($locatArr as $location) {
                    ?>
                  <input type="checkbox" class="location" data-type="tzy" <?php $match = explode('|', $location[1]);  if (strpos($tzy[$i]['location'], ($l . '/' . $match[0])) !== false) {?>checked<?php }?> value="<?php echo $l."/".$location[1]; ?>" name="tzy[<?php echo $i?>][location][]">
                  <?php echo $location[0]?>
                  <?php if (in_array($j, array(3, 8))) {?><br><?php }
                  $j++;
                  }
              }?>
              </td>
              <td><a href="javascript:;" class="cBlue removeTr">清空</a></td>
            </tr>
            <?php }?>
            </tbody>
          </table>
          <!-- <a href="javascript:;" class="btn-white mt20" id="add-row">添加一行</a> -->
          <div class="tac"><a class="btn-blue mt20 submit">保存并上线</a></div>
          </form>
        </div>
      </li>
      <!-- 轮播图管理结束 -->
      <!-- 首页资讯管理 开始 -->
      <li <?php if ($action == 'zcfc'): ?>style="display: block;"<?php else: ?>style="display: none;"<?php endif ?> >
        <div class="data-table-list mt10">
        <form action="/backend/banner" method="post" id="zcfc_form">
          <table>
            <colgroup><col width="5%" /><col width="15%" /><col width="20%" /><col width="20%" /><col width="25%" /><col width="5%" /></colgroup>
            <thead><tr><th>序号</th><th>标题</th><th>图片</th><th>链接</th><th>彩种</th><th>操作</th></tr></thead>
            <tbody id="pic-table">
            <?php for ($i = 0; $i < 5; $i++) {?>
            <tr>
              <td><input type="text" class="ipt w40 tac" name="zcfc[<?php echo $i?>][priority]" readonly value="<?php echo $i+1?>"></td>
              <td><input type="text" class="ipt tac w184" name="zcfc[<?php echo $i?>][title]" value="<?php echo $zcfc[$i]['title']?>"></td>
              <td>
              	<div class="btn-white file">选择文件</div> <div class="btn-white zcfcupload" data-index="<?php echo $i?>">开始上传</div>
                <input type="hidden" name="zcfc[<?php echo $i?>][path]" id="zcfcpath_<?php echo $i?>" value="<?php echo $zcfc[$i]['path']?>">
                <div id="imgdiv0" class="imgDiv"><img id="zcfcimgShow<?php echo $i?>" src="/uploads/banner/<?php echo $zcfc[$i]['path']?>" width="50" height="50" /></div>
              </td>
              <td><input type="text" class="ipt tac w222" name="zcfc[<?php echo $i?>][url]" value="<?php echo $zcfc[$i]['url']?>"></td>
              <td>
              <?php $j = 0;foreach ($zcfclocationArr as $l => $locatArr) {
                  foreach ($locatArr as $location) {?>
                  <input type="checkbox" class="location" data-type="zcfc" <?php $match = explode('|', $location[1]); if (strpos($zcfc[$i]['location'], ($l . '/' . $match[0])) !== false) {?>checked<?php }?> value="<?php echo $l."/".$location[1]; ?>" name="zcfc[<?php echo $i?>][location][]">
                  <?php echo $location[0]?>
                  <?php if (in_array($j, array(4, 9))) {?><br><?php }
                  $j++;
                  }
              }?>
              </td>
              <td><a href="javascript:;" class="cBlue removeTr">清空</a></td>
            </tr>
            <?php }?>
            </tbody>
          </table>
          <!-- <a href="javascript:;" class="btn-white mt20" id="add-row">添加一行</a> -->
          <div class="tac"><a class="btn-blue mt20 submit">保存并上线</a></div>
          </form>
        </div>
      </li>
      <!-- 首页资讯管理结束 -->
      <!-- 右侧banner管理 开始 -->
      <li <?php if ($action == 'ycfc'): ?>style="display: block;"<?php else: ?>style="display: none;"<?php endif ?> >
        <div class="data-table-list mt10">
        <form action="/backend/banner" method="post" id="ycfc_form">
          <table>
            <colgroup><col width="5%" /><col width="15%" /><col width="20%" /><col width="20%" /><col width="25%" /><col width="5%" /></colgroup>
            <thead><tr><th>序号</th><th>标题</th><th>图片</th><th>链接</th><th>彩种</th><th>操作</th></tr></thead>
            <tbody id="pic-table">
            <?php for ($i = 0; $i < 5; $i++) {?>
            <tr>
              <td><input type="text" class="ipt w40 tac" name="ycfc[<?php echo $i?>][priority]" readonly value="<?php echo $i+1?>"></td>
              <td><input type="text" class="ipt tac w184" name="ycfc[<?php echo $i?>][title]" value="<?php echo $ycfc[$i]['title']?>"></td>
              <td>
                <div class="btn-white file">选择文件</div> <div class="btn-white ycfcupload" data-index="<?php echo $i?>">开始上传</div>
                <input type="hidden" name="ycfc[<?php echo $i?>][path]" id="ycfcpath_<?php echo $i?>" value="<?php echo $ycfc[$i]['path']?>">
                <div id="imgdiv0" class="imgDiv"><img id="ycfcimgShow<?php echo $i?>" src="/uploads/banner/<?php echo $ycfc[$i]['path']?>" width="50" height="50" /></div>
              </td>
              <td><input type="text" class="ipt tac w222" name="ycfc[<?php echo $i?>][url]" value="<?php echo $ycfc[$i]['url']?>"></td>
              <td>
              <?php $j = 0; foreach ($ycfclocationArr as $l => $locatArr) {
                  foreach ($locatArr as $location) {?>
                  <input type="checkbox" class="location" data-type="ycfc" <?php $match = explode('|', $location[1]); if (strpos($ycfc[$i]['location'], ($l . '/' . $match[0])) !== false) {?>checked<?php }?> value="<?php echo $l."/".$location[1]; ?>" name="ycfc[<?php echo $i?>][location][]">
                  <?php echo $location[0]?>
                  <?php if (in_array($j, array(4, 9))) {?><br><?php }
                  $j++;
                  }
              }?>
              </td>
              <td><a href="javascript:;" class="cBlue removeTr">清空</a></td>
            </tr>
            <?php }?>
            </tbody>
          </table>
          <!-- <a href="javascript:;" class="btn-white mt20" id="add-row">添加一行</a> -->
          <div class="tac"><a class="btn-blue mt20 submit">保存并上线</a></div>
          </form>
        </div>
      </li>
      <!-- 右侧banner管理结束 -->
      <li></li>
      <li></li>
      <li></li>
    </ul>
  </div>
</div>
<div class="pop-dialog" id="confirm-submit">
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<p>是否确认修改？</p>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-blue-h32 mlr15" id="confirm-link">确认</a>
			<a href="javascript:;" class="btn-b-white" id="confirm-cancel">取消</a>
		</div>
	</div>
</div>
<!-- 上传内容请填写完整 -->
<div class="pop-dialog" id="no_complete" <?php if ($notfull) {?>style="display:block"<?php }?>>
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body tac">
			<p>上传内容请填写完整</p>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-blue-h32" onClick="return closePop();">确认</a>
		</div>
	</div>
</div>

<!-- 操作成功 -->
<div class="pop-dialog" id="success" <?php if ($saved) {?>style="display:block"<?php }?>>
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body tac">
			<p>恭喜你，操作成功！</p>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-blue-h32" onClick="return closePop();">确认</a>
		</div>
	</div>
</div>
<style>
.mod0-tab-bd li{display:none}
</style>
<script src="/source/js/webuploader.min.js"></script>
<script>
<?php if ($opennew) {?>
window.open('/backend/banner/zcfc');
<?php }?>
$("a.submit").click(function(){
	$("#confirm-link").attr('data-form', $(this).parents('form').attr('id'));
	popdialog("confirm-submit");
})
$("#confirm-link").click(function(){
	$("#"+$(this).attr('data-form')).append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
})
$("#confirm-cancel").click(function(){
	closePop();
})
// window.onload = function(){
// 	var oTab = document.getElementsByClassName('mod-tab')[0];
// 	if(oTab){
// 		var oTabHd = oTab.getElementsByClassName('mod-tab-hd')[0];
// 		var oTabBd = oTab.getElementsByClassName('mod-tab-bd')[0];
// 		var oTabHdLi = oTabHd.getElementsByTagName('ul')[0].getElementsByTagName('li');
// 		var oTabBdLi = oTabBd.getElementsByTagName('ul')[0].getElementsByTagName('li');
// 		oTabHd.addEventListener('click', function(ev){
// 			var target = ev.target;
// 			if(target.nodeName.toLowerCase() === "a"){
// 				var index;
// 				for(var i = 0; i < oTabHdLi.length; i++){
// 				  oTabHdLi[i].index = i;
// 				  oTabHdLi[i].className = "";
// 				  oTabBdLi[i].style.display = "none";
// 				}
// 				target.parentNode.className = "current";
// 				oTabBdLi[target.parentNode.index].style.display = "block";
// 			}
// 		}, false)
// 	}
// }
$(function() {

  $('.changeLi').click(function(){
    var index  = $(this).index();
    $('.mod-tab-bd ul li').hide();
    $('.mod-tab-bd ul li').eq(index).show();
    $(this).addClass('current').siblings().removeClass('current');
     //alert($(this).css('display'))
  });

	//首页资讯管理内部tab切换
	$('.mod0-tab-hd li').click(function(){
		$(this).addClass('current').siblings().removeClass('current');
		var _this=$(this).index();
		$('.mod0-tab-bd li').eq(_this).addClass('current').siblings().removeClass('current');
	})
	
	$('.location').click(function(){
		if ($(this).attr('checked')) {
			var val = $(this).val();
			var type = $(this).data('type');
			$(".location[value='"+val+"'][data-type='"+type+"']").removeAttr('checked');
			$(this).attr('checked', 'checked');
		}
	})

	var uploader = WebUploader.create({
        swf: '/caipiaoimg/v1.1/js/jUploader.swf',
        server: 'banner/upload',
        pick: '.file',
    });

    $(".upload").click(function(){
    	uploader.options.server = "/backend/banner/upload/tzy/"+$(this).data('index');
    	uploader.upload();
    })
    
    $(".zcfcupload").click(function(){
    	uploader.options.server = "/backend/banner/upload/zcfc/"+$(this).data('index');
    	uploader.upload();
    })

    $(".ycfcupload").click(function(){
        uploader.options.server = "/backend/banner/upload/ycfc/"+$(this).data('index');
        uploader.upload();
    })

    uploader.on( 'uploadSuccess', function( file, data) {
        if (data.position == 'tzy') {
        	$("#imgShow"+data.index).attr('src', '/uploads/banner/'+data.name);
            $("#path_"+data.index).val(data.name);
        }else if(data.position == 'zcfc'){
            $("#zcfcimgShow"+data.index).attr('src', '/uploads/banner/'+data.name);
            $("#zcfcpath_"+data.index).val(data.name);
        }else {
            $("#ycfcimgShow"+data.index).attr('src', '/uploads/banner/'+data.name);
            $("#ycfcpath_"+data.index).val(data.name);
        }
	});

});

$(".removeTr").click(function(){
	$(this).parents('tr').find('input').val('');
	$(this).parents('tr').find(':checkbox').removeAttr('checked');
	$(this).parents('tr').find('img').attr('src', '');
})

</script>