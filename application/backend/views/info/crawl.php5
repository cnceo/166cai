<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="/backend/Info/center">资讯中心管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li class="current"><a href="/backend/Info/crawl">资讯抓取配置</a></li>
            <li><a href="/backend/Info/center">资讯管理</a></li>
            <li><a href="/backend/Info/nba">NBA伤病管理</a></li>
            <li><a href="/backend/Info/banner">banner图管理</a></li>
        </ul>
    </div>
    <div class="mod-tab-bd">
        <ul>
            <li style="display: block">
                <div class="data-table-filter">
                    <table>
                        <tbody>
                        <tr>
                            <td colspan="14">
                                <select class="selectList w150" id="category_sel" name="">
                                    <?php foreach ($categoryList as $category): 
                                    if ($category['id'] < 9) {?>
                                        <option <?php if ($categoryId == $category['id']) {?>selected<?php }?> value="<?php echo $category['id'] ?>"><?php echo $category['name']; ?></option>
                                    <?php }
                                    endforeach; ?>
                                </select>
                                <a href="javascript:;" class="btn-blue ml20" id="modify">修改配置</a>
                                <input type="hidden" name="selectId" id="selectId" value="" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="data-table-list mt10">
                    <table>
                        <colgroup>
                            <col width="20%"/>
                            <col width="20%"/>
                            <col width="20%"/>
                            <col width="20%"/>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>分类</th>
                            <th>抓取来源</th>
                            <th>抓取地址</th>
                            <th>状态</th>
                        </tr>
                        </thead>
                        <tbody id="modify-infor">
                        <?php foreach ($configList as $config): ?>
                            <tr id="<?php echo $config['id'];?>" data-source="<?php echo $config['source'];?>">
                                <td><?php echo $config['category'] ?></td>
                                <td><?php echo $config['name'] ?></td>
                                <td><?php echo $config['url'] ?></td>
                                <td><?php echo $config['is_open'] ? '开启' : '关闭' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </li>
        </ul>
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
var sources = $.parseJSON('<?php echo json_encode($sourceList)?>');
$("tr").click(function(){
	$("tr").removeClass("select");
	$(this).addClass("select");
	$("#selectId").val($(this).attr("id"));
});
window.onload = function(){
	var oTab = document.getElementsByClassName('mod-tab')[0];
	if(oTab){
		var oTabHd = oTab.getElementsByClassName('mod-tab-hd')[0];
		var oTabBd = oTab.getElementsByClassName('mod-tab-bd')[0];
		var oTabHdLi = oTabHd.getElementsByTagName('ul')[0].getElementsByTagName('li');
		var oTabBdLi = oTabBd.getElementsByTagName('ul')[0].getElementsByTagName('li');
		oTabHd.addEventListener('click', function(ev){
		var index;
		for(var i = 0; i < oTabHdLi.length; i++){
		  oTabHdLi[i].index = i;
		  oTabHdLi[i].className = "";
		  oTabBdLi[i].style.display = "none";
		}
		var target = ev.target;
		if(target.nodeName.toLowerCase() !== "li"){
		  target.parentNode.className = "current";
		  oTabBdLi[target.parentNode.index].style.display = "block";
		}
		}, false)
	}
}
$("#modify").click(function(){
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
	var html  = '<tr><th>彩种：</th><td>'+td.eq(0).html()+'</td></tr><tr><th>抓取来源：</th><td><select name="source">';
		for (i in sources) {
			html += '<option value="'+sources[i].id+'" ';
			if (sources[i].id == $("#"+id).data('source')) {
				html += 'selected';
			}
			html += '>'+sources[i].name+'</option>';
		}
		html += '</select></td></tr><tr><th>抓取地址：</th><td><input type="text" name="url" class="ipt w222" value="'+td.eq(2).html()+'"></td></tr>';
		html += '<tr><th>状态：</th><td><select class="selectList w222" name="is_open"><option value="1" '+selected1+'>开启</option><option value="0" '+selected0+'>关闭</option></select></td></tr>';
	$("#tbody").html(html);
	$("#updateId").val(id);
	popdialog("updatePop");
});
$("#category_sel").change(function(){
	location.href = "/backend/Info/crawl?cid="+$(this).val();
})
$("#updateSubmit").click(function(){
	$.ajax({
        type: "post",
        url: '/backend/Info/crawl',
        data: $("#updateForm").serialize(),
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
</script>