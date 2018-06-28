<?php $this->load->view("templates/head") ?>
<style>
.isorder {
	display:none;
	background-color: #f1f1f1;
}
.isorder span {
	color:#aaa;
}
.editing {
	display:none;
}
</style>
<div class="path">您的位置：<a href="">信息管理</a>&nbsp;&gt;&nbsp;<a href="">首页管理</a></div>
<div class="mod-tab mt20">
  <div class="mod-tab-hd">
    <ul>
      <li <?php if ($action == 'banner') {?> class="current" <?php }?>><a href="javascript:;">轮播图管理</a></li>
      <li <?php if ($action == 'zixun') {?> class="current" <?php }?>><a href="javascript:;">首页资讯管理</a></li>
      <li><a href="/backend/Shouye/zhongjiang">中奖墙管理</a></li>
    </ul>
  </div>
  <div class="mod-tab-bd">
    <ul>
      <!-- 轮播图管理 -->
      <li <?php if ($action == 'banner') {?> style="display: block;" <?php }?>>
        <div class="data-table-filter mt10">
            <input type="checkbox" id="showorder"><label for="showorder">显示预约banner</label>
        </div>
        <div class="data-table-list mt10">
            <form action="/backend/Shouye" method="post" id="banner_form">
              <table>
                <colgroup><col width="3%" /><col width="5%" /><col width="20%" /><col width="25%" /><col width="7%" /><col width="28%" /><col width="24%" /><col width="4%" /></colgroup>
                <thead>
                  <tr><th>序号</th><th>权重</th><th>标题（长度建议在<span class="cRed">10-15</span>个字之间）</th><th>图片</th><th>背景色值</th><th>链接</th><th>上线时间</th><th>操作</th></tr>
                </thead>
                <tbody id="pic-table">
                <?php foreach ($banner as $i => $bn) {?>
                    <tr class="<?php if ($bn['isorder']) {?>isorder<?php }?>">
                      <td><?php echo $i+1?></td>
                      <td><input type="text" class="ipt w40 tac" name="banner[<?php echo $i?>][priority]"  value="<?php echo $bn['priority']?>"><br> <?php if ($bn['isorder']) {echo '<span>预约</span>';}?></td>
                      <td><input type="text" class="ipt tac w184" name="banner[<?php echo $i?>][title]" value="<?php echo $bn['title']?>"></td>
                      <td>
                      	<div class="btn-white file">选择文件</div>
                        <div class="btn-white upload" data-index="<?php echo $i?>">开始上传</div>
                        <input type="hidden" name="banner[<?php echo $i?>][path]" id="path_<?php echo $i?>" value="<?php echo $bn['path']?>">
                        <div id="imgdiv0" class="imgDiv"><img id="imgShow<?php echo $i?>" src="/uploads/shouyebanner/<?php echo $bn['path']?>" width="50" height="50" /></div>
                      </td>
                      <td><input type="text" class="ipt tac w60" name="banner[<?php echo $i?>][bgcolor]" value="<?php echo $bn['bgcolor']?>"></td>
                      <td><input type="text" class="ipt tac w264" name="banner[<?php echo $i?>][url]" value="<?php echo $bn['url']?>"></td>
                      <td>
                      <?php if (isset($bn) && !$bn['isorder']) {?>
      				<input type="hidden" name='banner[<?php echo $i?>][start_time]' value="<?php echo $bn['start_time'] ?>">
      				上线：<span class="ipt ipt-date w184"><input type="text" name='banner[<?php echo $i?>][start_time]' value="<?php echo $bn['start_time'] ?>" class="Wdate1" disabled><i></i></span><br>
                    <?php } else {?>
                                                                      上线：<span class="ipt ipt-date w184"><input type="text" name='banner[<?php echo $i?>][start_time]' value="<?php echo $bn['start_time'] ?>" class="Wdate1"><i></i></span><br>
                    <?php }?>
                                                                       下线：<span class="ipt ipt-date w184"><input type="text" name='banner[<?php echo $i?>][end_time]' value="<?php echo $bn['end_time'] ?>" class="Wdate1" /><i></i></span>
                      </td>
                      <td><a href="javascript:;" class="cBlue removeTr">清空</a><br><a href="javascript:;" class="cBlue copyTr">复制</a></td>
                    </tr>
                <?php }
                if (empty($banner)) $i = -1;
                for ($j = $i + 1; $j < $i + 6; $j++) {?>
                    <tr class="editing">
                      <td><?php echo $j+1?></td>
                      <td><input type="text" class="ipt w40 tac" name="banner[<?php echo $j?>][priority]"></td>
                      <td><input type="text" class="ipt tac w184" name="banner[<?php echo $j?>][title]"></td>
                      <td>
                      	<div class="btn-white file">选择文件</div>
                        <div class="btn-white upload" data-index="<?php echo $j?>">开始上传</div>
                        <input type="hidden" name="banner[<?php echo $j?>][path]" id="path_<?php echo $j?>">
                        <div id="imgdiv0" class="imgDiv"><img id="imgShow<?php echo $j?>" width="50" height="50" /></div>
                      </td>
                      <td><input type="text" class="ipt tac w60" name="banner[<?php echo $j?>][bgcolor]"></td>
                      <td><input type="text" class="ipt tac w264" name="banner[<?php echo $j?>][url]"></td>
                      <td>
                                                                      上线：<span class="ipt ipt-date w184"><input type="text" name='banner[<?php echo $j?>][start_time]' class="Wdate1"><i></i></span><br>
                                                                       下线：<span class="ipt ipt-date w184"><input type="text" name='banner[<?php echo $j?>][end_time]' class="Wdate1" /><i></i></span>
                      </td>
                      <td><a href="javascript:;" class="cBlue removeTr">清空</a><br><a href="javascript:;" class="cBlue pasteTr">粘贴</a></td>
                    </tr>
                <?php }?>
                </tbody>
              </table>
              <!-- <a href="javascript:;" class="btn-white mt20" id="add-row">添加一行</a> -->
              <p class="mt20">备注：<span class="cRed">标题长度建议在10-15个字之间，前台不展示。<br>&nbsp;&nbsp;&nbsp;图片尺寸1000*320px</span></p>
              <div class="tac">
              <a class="btn-blue mt20 submit">保存并上线</a>
              </div>
              </form>
        </div>
      </li>
      <!-- 轮播图管理结束 -->
      <!-- 首页资讯管理 开始 -->
      <li <?php if ($action == 'zixun') {?> style="display: block;" <?php }?>>
        <div class="data-table-list mt10"><!-- 分类表格开始 -->
        <form action="/backend/Shouye/index/zixun" method="post" id="zxtype_form">
          <table>
            <colgroup>
              <col width="5%" />
              <col width="15%" />
              <col width="30%" />
              <col width="5%" />
              <col width="15%" />
              <col width="30%" />
            </colgroup>
            <thead>
              <tr>
                <th>数字彩</th>
                <th>分类名（仅可输入<span class="cRed">2</span>个字）</th>
                <th>URL链接</th>
                <th>竞技彩</th>
                <th>分类名</th>
                <th>URL链接</th>
              </tr>
            </thead>
            <tbody>
            <?php for ($i = 1; $i <= 4; $i++) {?>
              <tr>
                <td><?php echo $i?></td>
                <td><input type="text" class="ipt tac w84" name="numtype[<?php echo $i?>][title]" value="<?php echo $numtype[$i]['title']?>"></td>
                <td><input type="text" class="ipt tac w264" name="numtype[<?php echo $i?>][url]" value="<?php echo $numtype[$i]['url']?>"></td>
                <td><?php echo $i?></td>
                <td><input type="text" class="ipt tac w84" name="jctype[<?php echo $i?>][title]" value="<?php echo $jctype[$i]['title']?>"></td>
                <td><input type="text" class="ipt tac w264" name="jctype[<?php echo $i?>][url]" value="<?php echo $jctype[$i]['url']?>"></td>
              </tr>
            <?php }?>
            </tbody>
          </table>
          <div class="tac">
            <a class="btn-blue mt20 submit">保存并上线</a>
          </div>
        </form>
        </div><!-- 分类表格结束 -->
        <div class="mt20"><!-- 标题表格开始 -->
          <div class="mod0-tab-hd">
            <ul class="clearfix">
              <li class="current"><a href="javascript:;">数字彩</a></li>
              <li><a href="javascript:;" class="nobdr">竞技彩</a></li>
            </ul>
          </div>
          <div class="mod0-tab-bd">
            <ul>
              <li class="current"><!-- 数字彩开始 -->
                <div class="data-table-list mt10">
                <form action="/backend/Shouye/index/zixun" method="post" id="zxnum_form">
                  <table>
                    <colgroup>
                      <col width="10%" />
                      <col width="30%" />
                      <col width="50%" />
                      <col width="10%" />
                    </colgroup>
                    <thead>
                      <tr>
                        <th>位置</th>
                        <th>标题（长度建议在<span class="cRed">9-10</span>个字之间）</th>
                        <th>URL链接</th>
                        <th>标红</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>热门1</td>
                        <td><input type="text" class="ipt tac w222" name="numrm[1][title]" value="<?php echo $numrm[1]['title']?>"></td>
                        <td><input type="text" class="ipt tac w264" name="numrm[1][url]" value="<?php echo $numrm[1]['url']?>"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>热门2</td>
                        <td><input type="text" class="ipt tac w222" name="numrm[2][title]" value="<?php echo $numrm[2]['title']?>"></td>
                        <td><input type="text" class="ipt tac w264" name="numrm[2][url]" value="<?php echo $numrm[2]['url']?>"></td>
                        <td></td>
                      </tr>
                      <?php foreach ($numtype as $p => $type) {
                      	$tpStr = "num".$p;
                      	$tpArr = $$tpStr;
                      	for ($i = 1; $i <= 3; $i++) {?>
                      		<tr>
		                        <td><?php echo $type['title'].$i?></td>
		                        <td><input type="text" class="ipt tac w222" name="<?php echo $tpStr?>[<?php echo $i?>][title]" value="<?php echo isset($tpArr[$i]) ? $tpArr[$i]['title'] : ''?>"></td>
		                        <td><input type="text" class="ipt tac w264" name="<?php echo $tpStr?>[<?php echo $i?>][url]" value="<?php echo isset($tpArr[$i]) ? $tpArr[$i]['url'] : ''?>"></td>
		                        <td><input type="checkbox" class="vam" name="<?php echo $tpStr?>[<?php echo $i?>][redflag]" <?php if ($tpArr[$i]['redflag'] == 1) {?>checked<?php }?>></td>
		                      </tr>
					  <?php }
					  }?>
                    </tbody>
                  </table>
                  <div class="tac mt20 mb20">
                  	<a type="submit" class="btn-blue mt20 submit">保存并预览</a>
                    <a href="/backend/Shouye/onlinezx/num" class="btn-blue mt20">上线</a>
                  </div>
                </form>
                </div><!-- 数字彩结束 -->
              </li>
              <li><!-- 竞技彩开始 -->
                <div class="data-table-list mt10">
                <form action="/backend/Shouye/index/zixun" method="post" id="zxjc_form">
                  <table>
                    <colgroup>
                      <col width="10%" />
                      <col width="30%" />
                      <col width="50%" />
                      <col width="10%" />
                    </colgroup>
                    <thead>
                      <tr>
                        <th>位置</th>
                        <th>标题（长度建议在<span class="cRed">9-10</span>个字之间）</th>
                        <th>URL链接</th>
                        <th>标红</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>热门1</td>
                        <td><input type="text" class="ipt tac w222" name="jcrm[1][title]" value="<?php echo $jcrm[1]['title']?>"></td>
                        <td><input type="text" class="ipt tac w264" name="jcrm[1][url]" value="<?php echo $jcrm[1]['url']?>"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>热门2</td>
                        <td><input type="text" class="ipt tac w222" name="jcrm[2][title]" value="<?php echo $jcrm[2]['title']?>"></td>
                        <td><input type="text" class="ipt tac w264" name="jcrm[2][url]" value="<?php echo $jcrm[2]['url']?>"></td>
                        <td></td>
                      </tr>
                      <?php foreach ($jctype as $p => $type) {
                      	$tpStr = "jc".$p;
                      	$tpArr = $$tpStr;
                      	for ($i = 1; $i <= 3; $i++) {?>
                      		<tr>
		                        <td><?php echo $type['title'].$i?></td>
		                        <td><input type="text" class="ipt tac w222" name="<?php echo $tpStr?>[<?php echo $i?>][title]" value="<?php echo isset($tpArr[$i]) ? $tpArr[$i]['title'] : ''?>"></td>
		                        <td><input type="text" class="ipt tac w264" name="<?php echo $tpStr?>[<?php echo $i?>][url]" value="<?php echo isset($tpArr[$i]) ? $tpArr[$i]['url'] : ''?>"></td>
		                        <td><input type="checkbox" class="vam" name="<?php echo $tpStr?>[<?php echo $i?>][redflag]" <?php if ($tpArr[$i]['redflag'] == 1) {?>checked<?php }?>></td>
		                    </tr>
					  <?php }
					  }?>
                    </tbody>
                  </table>
                  <div class="tac mt20 mb20">
                    <a type="submit" class="btn-blue mt20 submit">保存并预览</a>
                    <a href="/backend/Shouye/onlinezx/jc" class="btn-blue mt20">上线</a>
                  </div>
                </form>
                </div><!-- 竞技彩结束 -->
              </li>
            </ul>
          </div>
        </div><!-- 标题表格结束 -->
      </li>
      <!-- 首页资讯管理结束 -->
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
<script  src="/source/date/WdatePicker.js"></script>
<script>
<?php if ($opennew) {?>
window.open('/backend/Shouye/zixun');
<?php }?>
<?php if ($notfull) {?>
alert('请将方案内容填写完整！');
<?php }?>
$("a.submit").click(function(){
	var formid = $(this).parents('form').attr('id');
	if (formid === 'banner_form') {
		var notime = false, dataArr = {}
		$('#banner_form tbody tr').each(function(){
			var index = $(this).find('td:eq(0)').html(), priority = $(this).find('td:eq(1) input').val(), 
			start = $(this).find('td:eq(6) input:first').val(), end = $(this).find('td:eq(6) input:last').val();
			if (priority && (!start || !end)) {
				alert('请设置上下线时间！');
				notime = true;
				return false
			}
			if (priority !== '') {
				if (!(priority in dataArr)) dataArr[priority] = [];
				var startval = (new Date(start)).valueOf(), endval = (new Date(end)).valueOf();
				if (startval >= endval) {
					alert('下线时间不可大于上线时间！');
					notime = true;
					return false
				}
				dataArr[priority].push([startval, endval, index]);
			}
		})
		if (notime) return;
		var repeat = [];
		$.each (dataArr, function(k, data) {
			if (data.length > 0) {
				$.each(data, function(k0, val0){
					$.each(data, function(k1, val1){
						if (k0 != k1 && ((val0[0] < val1[0] && val0[1] > val1[0]) || (val0[0] >= val1[0] && val1[1] > val0[0]) || (val0[0] == val1[0] && val1[1] == val0[0]))) {
							if (repeat.indexOf(val0[2]) == -1) repeat.push(val0[2])
							if (repeat.indexOf(val1[2]) == -1) repeat.push(val1[2])
						}
					})
				})
				if (repeat.length > 0) {
					alert('方案'+repeat.join('、')+'上线时间冲突')
        			return false;
        		}
			}
		})
		if (repeat.length > 0) return ;
	}
	$("#confirm-link").attr('data-form', formid);
	popdialog("confirm-submit");
})
$("#confirm-link").click(function(){
	$("#"+$(this).attr('data-form')).append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").submit();
})
$("#confirm-cancel").click(function(){
	closePop();
})
$(function(){
    $(".Wdate1").focus(function(){
        dataPicker();
    });
});
window.onload = function(){
	var oTab = document.getElementsByClassName('mod-tab')[0];
	if(oTab){
		var oTabHd = oTab.getElementsByClassName('mod-tab-hd')[0];
		var oTabBd = oTab.getElementsByClassName('mod-tab-bd')[0];
		var oTabHdLi = oTabHd.getElementsByTagName('ul')[0].children;
		var oTabBdLi = oTabBd.getElementsByTagName('ul')[0].children;
		oTabHd.addEventListener('click', function(ev){
			var target = ev.target;
			if(target.nodeName.toLowerCase() === "a"){
				var index;
				for(var i = 0; i < oTabHdLi.length; i++){
				  oTabHdLi[i].index = i;
				  oTabHdLi[i].className = "";
				  oTabBdLi[i].style.display = "none";
				}
				target.parentNode.className = "current";
				oTabBdLi[target.parentNode.index].style.display = "block";
			}
		}, false)
	}
}
$(function() {
	//首页资讯管理内部tab切换
	$('.mod0-tab-hd li').click(function(){
		$(this).addClass('current').siblings().removeClass('current');
		var _this=$(this).index();
		$('.mod0-tab-bd li').eq(_this).addClass('current').siblings().removeClass('current');
	})

	var uploader = WebUploader.create({
        swf: '/caipiaoimg/v1.1/js/jUploader.swf',
        server: 'Shouye/upload',
        pick: '.file',
    });

    $(".upload").click(function(){
    	uploader.options.server = "/backend/Shouye/upload/shouyebanner/"+$(this).data('index');
    	uploader.upload();
    })

    uploader.on( 'uploadSuccess', function( file, data) {
      	$("#imgShow"+data.index).attr('src', '/uploads/shouyebanner/'+data.name);
        $("#path_"+data.index).val(data.name);
	});

});

$(".removeTr").click(function(){
	$(this).parents('tr').find('input').val('').filter('[disabled=disabled]').removeAttr('disabled');
	$(this).parents('tr').find('img').attr('src', '');
})

$(".copyTr").click(function(){
	var tr = $(this).parents('tr'), priority = tr.find('td:eq(1) input').val(), title = tr.find('td:eq(2) input').val(),
	path = tr.find('td:eq(3) input[name^=banner]').val(), bgcolor = tr.find('td:eq(4) input').val(),
	url = tr.find('td:eq(5) input').val();
	window.localStorage['banner'] = priority+'|'+title+'|'+path+'|'+bgcolor+'|'+url;
})

$(".pasteTr").click(function(){
	var arr = window.localStorage['banner'].split('|'), tr = $(this).parents('tr');
	tr.find('td:eq(1) input').val(arr[0]);
	tr.find('td:eq(2) input').val(arr[1]);
	tr.find('td:eq(3) input[name^=banner]').val(arr[2]);
	tr.find('td:eq(3) img').attr('src', '/uploads/shouyebanner/'+arr[2]);
	tr.find('td:eq(4) input').val(arr[3]);
	tr.find('td:eq(5) input').val(arr[4]);
})

$("#showorder").click(function(){
	$('#banner_form tbody tr').show();
	if(!$(this).attr('checked')) $('.isorder, .editing').hide();	
})
</script>