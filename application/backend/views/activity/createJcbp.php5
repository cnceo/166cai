<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">竞彩活动</a></div>
<div class="mod-tab mod-tab-s mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li class="current"><a href="/backend/Activity/createJcbp">竞彩不中包赔</a></li>
    	</ul>
  	</div>
</div>
<div class="mod-tab mt20 mb20">
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mb10">
        			<form>
	          			<table>
	            			<colgroup>
	              				<col width="220">
	              				<col width="220">
                                                <col width="220">
	            			</colgroup>
		            		<tbody>
                                            <tr>
                                                <td>
                                                <h1>创建活动</h1>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                <span>活动期次：<?php echo $id['id']+1?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                活动时间: 
                                                <input type="text" class="ipt w150 ipt-date Wdate1" id="start" name="start" value="">
                                                <span>至</span>
                                                <input type="text" class="ipt w150 ipt-date Wdate1" id="end" name="end" value="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                单用户参与活动金额: 
                                                <input type="text" class="ipt w108" id="money" name="money" placeholder="金额" value="">
                                                赔付形式：
                                                <select class="selectList w115" name="paystatus" id="paystatus">
                                                    <option value="1">满3减2购彩红包</option>
                                                    <option value="2">充10送3充值红包</option>
                                                </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    选择彩种：
                                                    <select class="selectList w115" id="lid" name="lid">
                                                        <option value="42">竞彩足球</option>
                                                        <option value="43">竞彩篮球</option>
                                                    </select>
                                                    过关方式:
                                                    <select class="selectList w115" id="type" name="type">
                                                        <option value="0">2串1</option>
                                                        <option value="1">单关</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="jczqc1">
                                                <td>
                                                    场次1选择 ：
                                                    <input type="text" class="ipt w108" id="jczqissue1" name="jczqissue1" placeholder="场次1" value="">
                                                    玩法选择 : 
                                                    <select class="selectList w115" id="jczqplaytype1" name="jczqplaytype1">
                                                        <option value="0">胜平负</option>
                                                        <option value="1">让球胜平负</option>
                                                    </select>
                                                    方案选择 :
                                                    <select class="selectList w115" id="jczqchose1" name="jczqchose1">
                                                        <option value="3">胜</option>
                                                        <option value="1">平</option>
                                                        <option value="0">负</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="jczqc2">
                                                <td>
                                                    场次2选择 ：
                                                    <input type="text" class="ipt w108" id="jczqissue2" name="jczqissue2" placeholder="场次2" value="">
                                                    玩法选择 : 
                                                    <select class="selectList w115" id="jczqplaytype2" name="jczqplaytype2">
                                                        <option value="0">胜平负</option>
                                                        <option value="1">让球胜平负</option>
                                                    </select>
                                                    方案选择 :
                                                    <select class="selectList w115" id="jczqchose2" name="jczqchose2">
                                                        <option value="3">胜</option>
                                                        <option value="1">平</option>
                                                        <option value="0">负</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="jclqc1" style="display:none;">
                                                <td>
                                                    场次1选择 ：
                                                    <input type="text" class="ipt w108" id="jclqissue1" name="jclqissue1" placeholder="场次1" value="">
                                                    玩法选择 : 
                                                    <select class="selectList w115" id="jclqplaytype1" name="jclqplaytype1">
                                                        <option value="0">胜负</option>
                                                        <option value="1">让分胜负</option>
                                                    </select>
                                                    方案选择 :
                                                    <select class="selectList w115" id="jclqchose1" name="jclqchose1">
                                                        <option value="3">胜</option>
                                                        <option value="0">负</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="jclqc2" style="display:none;">
                                                <td>
                                                    场次2选择 ：
                                                    <input type="text" class="ipt w108" id="jclqissue2" name="jclqissue2" placeholder="场次2" value="">
                                                    玩法选择 : 
                                                    <select class="selectList w115" id="jclqplaytype2" name="jclqplaytype2">
                                                        <option value="0">胜负</option>
                                                        <option value="1">让分胜负</option>
                                                    </select>
                                                    方案选择 :
                                                    <select class="selectList w115" id="jclqchose2" name="jclqchose2">
                                                        <option value="3">胜</option>
                                                        <option value="0">负</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="javascript:;" class="btn-blue">创建活动</a>
                                                </td>
                                            </tr>
		            		</tbody>
	          			</table>
          			</form>
        		</div>
      		</li>
    	</ul>
  	</div>
      	<!-- 创建活动 start -->
  	<div class="pop-dialog" id="dialog-createJc" style="display:none;">
		<div class="pop-in">
			<div class="pop-head">
				<h2>竞彩不中全赔活动确认</h2>
				<span class="pop-close" title="关闭">关闭</span>
			</div>
                        <div class="pop-body">
                            <div class="data-table-filter del-percent">
                                <table>
                                    <colgroup>
                                        <col width="175">
                                        <col width="175">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <td><span>活动期次：<?php echo $id['id']+1?></span></td>
                                        </tr>
                                        <tr>
                                            <td>活动时间 : <span id="poptime"></span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                单用户参与金额 : <span id="popmoney"></span>元</td>
                                                <td>赔付红包 : <span id="poppay"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>彩种 : <span id="popcai"></span></td>
                                            <td>过关方式 : <span id="poptype"></span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                场次及方案确认：<br>
                                                <span id="popissue"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
			<div class="pop-foot tac">
				<a href="javascript:closePop();" class="btn-b-white">取消</a>
				<a href="javascript:;" class="btn-blue-h32 mlr15" id="confirmJc">创建活动</a>
			</div>
		</div>
	</div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
        // 时间控件
        $(".Wdate1").focus(function(){
            dataPicker();
        });
        
        $("#lid").on('change',function(){
            change();
        });
        $("#type").on('change',function(){
            change();
        });
        
        var change=function(){
            var lid =$("#lid").val();
            var type = $("#type").val();
            if(lid==42){
                $("#jclqc1").css("display","none");
                $("#jclqc2").css("display","none");
                $("#jczqc1").css("display","");
                if(type == 0){
                    $("#jczqc2").css("display","");
                }else{
                    $("#jczqc2").css("display","none");
                }
            }else{
                $("#jczqc1").css("display","none");
                $("#jczqc2").css("display","none");
                $("#jclqc1").css("display","");
                if(type == 0){
                    $("#jclqc2").css("display","");
                }else{
                    $("#jclqc2").css("display","none");
                }
            }
        };
        
        
        $(".btn-blue").on('click',function(){
            var start =  $("#start").val();
            var end =  $("#end").val();
            var money =  $("#money").val();
            var paystatus =  $("#paystatus").val();
            var lid = $("#lid").val();
            var type = $("#type").val();
            if(lid==42){
                var issue1= $("#jczqissue1").val();
                var playtype1 = $("#jczqplaytype1").val();
                var chose1 = $("#jczqchose1").val();
                var issue2= $("#jczqissue2").val();
                var playtype2 = $("#jczqplaytype2").val();
                var chose2 = $("#jczqchose2").val();
            }else{
                var issue1= $("#jclqissue1").val();
                var playtype1 = $("#jclqplaytype1").val();
                var chose1 = $("#jclqchose1").val();
                var issue2= $("#jclqissue2").val();
                var playtype2 = $("#jclqplaytype2").val();
                var chose2 = $("#jclqchose2").val();
            }
            if(!start || !end){
                alert("必选选择活动时间");
                return false;
            }
            var re = /^[0-9]+$/ ;
            if(!re.test(money)){
                alert("金额必须是正整数");
                return false;
            }
            if(money%2!=0){
                alert("金额必须是正偶数");
                return false;
            }
            if(!issue1){
                alert("场次1必须选择");
                return false;
            }
            if(type==0){
                if(!issue2){
                    alert("场次2必须选择");
                    return false;
                }
            }
            $("#poptime").html(start+"--"+end);
            $("#popmoney").html(money);
            if(lid==42){
                $("#popcai").html("竞彩足球");
            }else{
                $("#popcai").html("竞彩篮球");
            }
            if(type==0){
                $("#poptype").html("2串1");
            }else{
                $("#poptype").html("单关");
            }
            if(paystatus==1){
                $("#poppay").html("满3减2购彩红包")
            }else{
                $("#poppay").html("充10送3充值红包")
            }
            $.ajax({
                type: 'post',
                url: '/backend/Activity/getJcinfo',
                data: {lid:lid,type:type,issue1:issue1,playtype1:playtype1,chose1:chose1,issue2:issue2,playtype2:playtype2,chose2:chose2},
                success: function (response) {
                    var response = $.parseJSON(response);
                    $("#popissue").html(response.message);
                    popdialog("dialog-createJc");
                    return false;
                },
                error: function () {
                    alert('网络异常，请稍后再试');
                }
            });
        });
        
        var selectTag = true;
	$("#confirmJc").click(function(){
            var start =  $("#start").val();
            var end =  $("#end").val();
            var money =  $("#money").val();
            var paystatus =  $("#paystatus").val();
            var lid = $("#lid").val();
            var type = $("#type").val();
            if(lid==42){
                var issue1= $("#jczqissue1").val();
                var playtype1 = $("#jczqplaytype1").val();
                var chose1 = $("#jczqchose1").val();
                var issue2= $("#jczqissue2").val();
                var playtype2 = $("#jczqplaytype2").val();
                var chose2 = $("#jczqchose2").val();
            }else{
                var issue1= $("#jclqissue1").val();
                var playtype1 = $("#jclqplaytype1").val();
                var chose1 = $("#jclqchose1").val();
                var issue2= $("#jclqissue2").val();
                var playtype2 = $("#jclqplaytype2").val();
                var chose2 = $("#jclqchose2").val();
            }

    	if(selectTag){

    		selectTag = false;

    		$.ajax({
                type: 'post',
                url: '/backend/Activity/createJcbp',
                data: {start:start,end:end,money:money,paystatus:paystatus,lid:lid,type:type,issue1:issue1,playtype1:playtype1,chose1:chose1,issue2:issue2,playtype2:playtype2,chose2:chose2},

                success: function (response) {
                    var response = $.parseJSON(response);
                    if(response.status == 'y')
                    {
                        selectTag = true;
                        closePop();
                        alert(response.message);
                        window.location.href="/backend/Activity/newManageJc";
                    }else{
                        selectTag = true;
                        alert(response.message);
                    }
                },
                error: function () {
                    selectTag = true;
                    alert('网络异常，请稍后再试');
                }
            });
    	}

	});
    });
</script>

