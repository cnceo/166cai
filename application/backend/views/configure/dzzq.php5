<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">数据中心</a>&nbsp;&gt;&nbsp;<a href="">抓取配置</a></div>
<div class="mod-tab mt20">
  <div class="mod-tab-hd">
    <ul>
      <li><a href="/backend/Configure/">开奖号码</a></li>
      <li><a href="/backend/Configure/kjxq/">开奖详情</a></li>
      <li class="current"><a href="/backend/Configure/dzzq/">对阵抓取</a></li>
      <li><a href="/backend/Configure/bfzq/">比分抓取</a></li>
      <li><a href="/backend/Configure/lzcsg/">老足彩赛果</a></li>
      <li><a href="/backend/Configure/jlks/">快3期次更新</a></li>
    </ul>
  </div>
  <div>
  <div class="data-table-filter">
  		<form action="/backend/Configure/dzzq/" method="get"  id="search_form">
          <table>
            <tbody>
            <tr>
              <td colspan="4">
                <select class="selectList w222" id="ctype" name="ctype">
                  <?php foreach ($ctypes as $key => $ctype): ?>
                  <option value="<?php echo $key;?>" <?php if($search['ctype'] === "{$key}"): echo "selected"; endif;   ?>><?php echo $ctype;?></option>
                  <?php endforeach; ?>
                </select>
                <span style="margin-left: 120px;">
                  <a href="javascript:void(0);" class="btn-blue" id="update">修改配置</a>
                </span>
                <input type="hidden" name="selectId" id="selectId" value="" />
              </td>
            </tr>
            </tbody>
          </table>
        </form>
        </div>
        <div class="data-table-list mt10">
          <table>
            <colgroup>
              <col width="15%" />
              <col width="15%" />
              <col width="50%" />
              <col width="10%" />
            </colgroup>
            <thead>
              <tr>
                <th>彩种</th>
                <th>抓取来源</th>
                <th>抓取地址</th>
                <th>状态</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($result as $row):?>
              <tr id="<?php echo $row['id'];?>">
               <td><?php echo $ctypes[$row['ctype']];?></td>
               <td><?php echo $row['cname'];?></td>
               <td style="text-align: left; padding-left:5px;"><a href="<?php echo $row['url'];?>" target="_blank"><?php echo $row['url'];?></a></td>
               <td><?php if($row['start'] == 1): echo '开启'; else: echo '关闭'; endif;?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
  </div>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<form id='updateForm' method='post' action=''>
<div class="pop-dialog" id="updatePop">
	<div class="pop-in">
		<div class="pop-head">
			<h2>开奖信息抓取修改</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-filter del-percent">
				<table>
					<colgroup>
						<col width="68" />
		                <col width="350" />
					</colgroup>
					<tbody id="tbody">
					</tbody>
				</table>
			</div>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-blue-h32 mlr15" id="updateSubmit">确定</a>
			<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">关闭</a>
		</div>
	</div>
</div>
<input type="hidden" value="" name="updateId"  id="updateId"/>
</form>
<div class="pop-dialog" id="alertPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-filter del-percent" id="alertBody">
			</div>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">确认</a>
		</div>
	</div>
</div>
<script>
$(function(){
	$("#ctype").change(function(){
		$('#search_form').submit();
	});

	$("tr").click(function(){
		$("tr").removeClass("select");
		$(this).addClass("select");
		$("#selectId").val($(this).attr("id"));
	});

	$("#update").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的配置');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		var selected0 = selected1 = '';
		if(td.eq(3).html() == '开启')
		{
			selected1 = ' selected';
		}
		else
		{
			selected0 = ' selected';
		}
		var html  = '<tr><th>彩种：</th><td>'+td.eq(0).html()+'</td></tr>';
			html += '<tr><th>抓取来源：</th><td><input type="text" name="cname" class="ipt w222" value="'+td.eq(1).html()+'"></td></tr>';
			html += '<tr><th>抓取地址：</th><td><input type="text" name="url" class="ipt w222" value="'+td.eq(2).find('a').html()+'"></td></tr>';
			html += '<tr><th>状态：</th><td><select class="selectList w222" name="start"><option value="1" '+selected1+'>开启</option><option value="0" '+selected0+'>关闭</option></select></td></tr>';
		$("#tbody").html(html);
		$("#updateId").val(id);
		popdialog("updatePop");
	});

	$("#updateSubmit").click(function(){
        var type = $("#ctype").val();
		$.ajax({
            type: "post",
            url: '/backend/Configure/update',
            data: $("#updateForm").serialize()+ '&type ='+ type+ '&names =对阵抓取',
            success: function (data) {
                var json = jQuery.parseJSON(data);
                alert(json.message)
                if(json.status =='y')
                {
                    location.reload();
                }
            }
        });
        return false;
	});
	
	//重写提示框
	function alertPop(content){
		$("#alertBody").html(content);
		popdialog("alertPop");
	}
})
</script>
</body>
</html>