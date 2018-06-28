<?php $this->load->view("templates/head") ?>
<style type="text/css">
    ._red{color:#f00;font-style: normal;}
    ._normal{font-style: normal;}
</style>
<div id="app">
    <div class="path">您的位置：<a href="javascript:;">推送管理</a>&nbsp;&gt;&nbsp;<a href="/backend/Apppush/management">未注册实名推送</a></div>
    <div class="mod-tab mt20">
        <div class="mod-tab-hd">
            <ul>
                <li class="current"><a href="/backend/Apppush/management">推送管理</a></li>
                <li><a href="/backend/Apppush/effect">推送效果</a></li>
                <li><span style="color:red;margin-left: 20px;">
                        除半小时推送外，程序将在每日22点统一处理明日符合条件的推送配置信息，请提前确认保存和删除操作</span>
                    <br><span style="color:red;margin-left: 20px;">推送选择红包，尽量选择短信推送方式</span>
                </li>
            </ul>
        </div>
        <div class="mod-tab-bd">
            <ul>
                <li style="display: list-item;">
                    <div class="data-table-list mt10">
                        <table id="manage">
                            <colgroup>
                                <col width="20" />
                                <col width="10" />
                                <col width="15" />
                                <col width="15" />
                                <col width="10" />
                                <col width="10" />
                                <col width="10" />
                                <col width="35" />
                                <col width="10" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>推送主题</th>
                                    <th>当前推送方式</th>
                                    <th>第一次推送时间</th>
                                    <th>第二次推送时间</th>
                                    <th>是否选择红包</th>
                                    <th>昨日涉及人数</th>
                                    <th>操作</th>
                                    <th>当前推送内容</th>
                                    <th>状态</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lists as $list){ ?>
                                <tr>
                                    <td id="topic<?php echo  $list['id'];?>"><?php echo $list['topic']; ?></td>
                                    <td id="ptype<?php echo  $list['id'];?>"><?php echo $list['ptype']==0?'短信':'push'; ?></td>
                                    <td id="first<?php echo  $list['id'];?>"><?php echo $list['first']; ?></td>
                                    <td id="secend<?php echo  $list['id'];?>"><?php echo $list['secend']; ?></td>
                                    <td id="red<?php echo  $list['id'];?>"><?php echo $list['red']; ?></td>
                                    <td><?php echo $list['totalNum']; ?></td>
                                    <td><a class="update" data-id="<?php echo $list['id'];?>" data-rid="<?php echo $list['rid'].','.$list['red']; ?>" data-action="<?php echo $list['action'];?>" data-url="<?php echo $list['url'];?>" href="javascript:;">编辑</a></td>
                                    <td id="content<?php echo  $list['id'];?>"><?php echo $list['content']; ?></td>
                                    <td><select class="changeStatus" data-id="<?php echo $list['id'];?>" id="status<?php echo  $list['id'];?>">
                                            <option value="0" <?php echo $list['status']==0?'selected':''; ?>>关闭</option>
                                            <option value="1" <?php echo $list['status']==1?'selected':''; ?>>开启</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="tac">
	          	    <a class="btn-blue mt20 submit">保存并生效</a>
	          	</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="pop-dialog" id="alertPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div>是否确认保存配置？</div>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:closePop();" class="btn-b-white mlr15">取消</a>
			<a href="javascript:;" class="btn-b-white mlr15 config-submit">确认</a>
		</div>
	</div>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<div class="pop-dialog" id="modifyPop">
    <div class="pop-in">
        <div class="pop-head">
            <h2 id="head">推送配置</h2>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup>
                        <col width="68"/>
                        <col width="350"/>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>推送主题:</th>
                        <td id="topic"></td>
                    </tr>
                    <tr>
                        <th>推送方式：</th>
                        <td>
                            <div style="float:left;"><input type="radio" name="ptype" value="0">短信</div>
                            <div id="pushchose" style="float:left;"><input type="radio" name="ptype" value="1">push</div>
                        </td>
                    </tr>
                    <tr>
                        <th>第一次推送时间：</th>
                        <td id="first"></td>
                    </tr>
                    <tr>
                        <th>第二次推送时间：</th>
                        <td id="secend">
                            
                        </td>
                    </tr>
                    <tr id="red">
                        <th>选择红包：</th>
                        <td>
                            <select id="redpack">
                            <option value="0,">无</option>    
                            <?php foreach ($redpacks as $r){ ?>
                                <option value="<?php echo $r['id'].','.$r['use_desc'];?>"><?php echo $r['content'];?></option>    
                            <?php } ?>    
                            </select>    
                        </td>
                    </tr>
                    <tr id="pushtitle">
                        <th>推送标题：</th>
                        <td>
                            <input id="pushtitletxt" class="ipt w222" value="红包快过期啦"> 
                        </td>
                    </tr>
                    <tr>
                        <th>推送内容：</th>
                        <td>
                            <textarea id="content" style="width:300px;height: 100px;">
                                
                            </textarea>
                            <br><span id="moban">短信模板：【166彩票】亲，您的166元红包快过期了哦！<br>速来挽救，拿红包中大奖： t.cn/R9SyzIp</span>
                        </td>
                    </tr>
                    <tr id="pushAction">
                        <th>点击后续动作：</th>
                        <td>
                            <select id="pushActionSelect">
                                <option value="0">打开APP</option>
                                <option value="1" selected>打开红包页面</option>
                                <option value="2">打开指定URL</option>
                            </select>    
                        </td>
                    </tr>
                    <tr id="pushurl">
                        <th>URL地址：</th>
                        <td>
                            <input id="pushurlTxt" class="ipt w222"> 
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:void(0)" class="btn-blue-h32" id="modifySubmit">确认</a>
            <a href="javascript:closePop();" class="btn-blue-h32">取消</a>
        </div>
    </div>
</div>
<script>
    var changeArr = [];
    var id = 0;
    $(".update").click(function(){
        $("#content").val('');
        id=$(this).data('id');
        $("#topic").html($("#topic"+id).html());
        if(($("#ptype"+id).html())=='短信'){
            $("input:radio[name='ptype']").eq(0).attr("checked",true);
            $("#pushtitle").css("display","none");
             $("#pushAction").css("display","none");
              $("#pushurl").css("display","none");
        }else{
            $("input:radio[name='ptype']").eq(1).attr("checked",true);
            $("#pushtitle").css("display","");
            $("#pushAction").css("display","");
            if(($("#pushActionSelect").val())==2){
                $("#pushurl").css("display","");
            }
        }
        if($("#topic"+id).html()=='手机领红包未注册'  || $("#topic"+id).html()=='注册未实名（网页、M版）' || $("#topic"+id).html()=='实名未购彩（网页、M版）'){
            $("#pushchose").css("display","none");
        }else{
            $("#pushchose").css("display","");
        }
        var first= $("#first"+id).html();
        if(first=='半小时'){
            $("#first").html("半小时");
        }else{
            var hour1=first.substring(2,4);
            var min1=first.substring(5,7);
            $("#first").html("次日<input type='text' name='hour1' id='hour1' class='ipt' value='"+hour1+"'>时<input type='text' id='min1' name='min1' class='ipt' value='"+min1+"'>分");
        }
        var str = $("#secend"+id).html();
        var hour2=str.substring(2,4);
        var min2=str.substring(5,7);
        var hour3=str.substring(3,5);
        var min3=str.substring(6,8);
        if((str.indexOf("次日")!=-1 || str=='/') && first.indexOf("次日")==-1){
            $("#secend").html("次日<input type='text' name='hour2' id='hour2' class='ipt' value='"+hour2+"'>时<input type='text' name='min2' id='min2' class='ipt' value='"+min2+"'>分");
        }else{
            $("#secend").html("第三日<input type='text' name='hour2' id='hour2' class='ipt' value='"+hour3+"'>时<input type='text' name='min2' id='min2' class='ipt' value='"+min3+"'>分");
        }
        var head=$("#topic"+id).html();
        if(head.indexOf("实名未购彩")!=-1){
            $("#red").css("display","");
        }else{
            $("#red").css("display","none");
        }
        if($("#topic"+id).html()=='手机领红包未注册'){
            $("#moban").html("短信模板：【166彩票】亲，您的166元红包快过期了哦！<br>速来挽救，拿红包中大奖： t.cn/R9SyzIp");
        }
        if($("#topic"+id).html()=='注册未实名（网页、M版）'){
            $("#moban").html("短信模板：【166彩票】亲，您的满2减1.99红包快过期了哦！<br>1分钱实现一个梦，碰碰运气： t.cn/R9SyzIp");
        }
        if($("#topic"+id).html()=='实名未购彩（网页、M版）'){
            $("#moban").html("短信模板：【166彩票】亲，您的满2减1.99红包快过期了哦！<br>1分钱有机会砸中1000万，碰碰运气： t.cn/R9SyzIp");
        }
        if($("#topic"+id).html()=='注册未实名（安卓）' || $("#topic"+id).html()=='注册未实名（苹果）'){
            if(($("#ptype"+id).html())=='短信'){
                $("#moban").html("短信模板：【166彩票】亲，您的满2减1.99红包快过期了哦！<br>1分钱实现一个梦，碰碰运气： t.cn/R9SyzIp");
            }else{
                $("#moban").html("推送模板：亲，您的满2减1.99红包快过期了哦！<br>碰碰运气，1分钱有机会砸中1000万>>");
            }
        }
        if($("#topic"+id).html()=='实名未购彩（苹果）' || $("#topic"+id).html()=='实名未购彩（安卓）'){
            if(($("#ptype"+id).html())=='短信'){
                $("#moban").html("短信模板：【166彩票】亲，您的满2减1.99红包快过期了哦！<br>1分钱有机会砸中1000万，碰碰运气： t.cn/R9SyzIp");
            }else{
                $("#moban").html("推送模板：亲，您的满2减1.99红包快过期了哦！<br>碰碰运气，1分钱有机会砸中1000万>>");
            }
        }
        $("#content").val($("#content"+id).text());
        var action = $(this).data('action');
        var url = $(this).data('url');
        var rid = $(this).data('rid');
        $("#pushActionSelect").val(action);
        $("#pushurlTxt").val(url);
        $("#redpack").val(rid);
        popdialog("modifyPop"); 
    });
    
    $('input:radio[name="ptype"]').change( function(){
        var type=$("input[name='ptype']:checked").val();
        if(type==0){
            $("#pushtitle").css("display","none");
            $("#pushAction").css("display","none");
            $("#pushurl").css("display","none");
        }else{
            $("#pushtitle").css("display","");
            $("#pushAction").css("display","");
            if(($("#pushActionSelect").val())==2){
                $("#pushurl").css("display","");
            }
        }
        if($("#topic"+id).html()=='注册未实名（安卓）' || $("#topic"+id).html()=='注册未实名（苹果）'){
            if(type==0){
                $("#moban").html("短信模板：【166彩票】亲，您的满2减1.99红包快过期了哦！<br>1分钱实现一个梦，碰碰运气： t.cn/R9SyzIp");
            }else{
                $("#moban").html("推送模板：亲，您的满2减1.99红包快过期了哦！<br>碰碰运气，1分钱有机会砸中1000万>>");
            }
        }
        if($("#topic"+id).html()=='实名未购彩（苹果）' || $("#topic"+id).html()=='实名未购彩（安卓）'){
            if(type==0){
                $("#moban").html("短信模板：【166彩票】亲，您的满2减1.99红包快过期了哦！<br>1分钱有机会砸中1000万，碰碰运气： t.cn/R9SyzIp");
            }else{
                $("#moban").html("推送模板：亲，您的满2减1.99红包快过期了哦！<br>碰碰运气，1分钱有机会砸中1000万>>");
            }
        }
    });
    
    $('#pushActionSelect').change(function(){
        if(($("#pushActionSelect").val())==2){
            $("#pushurl").css("display","");
        }
    });
    
    $(".changeStatus").change(function(){
        id = $(this).data('id');
        var str={};
        str.id=id;
        str.topic=$("#topic"+id).html();
        if(($("#ptype"+id).html())=='短信'){
            $("input:radio[name='ptype']").eq(0).attr("checked",true);
        }else{
            $("input:radio[name='ptype']").eq(1).attr("checked",true);
        }
        str.type=$("input[name='ptype']:checked").val();
        str.content = $("#content"+id).text();
        changeArr[id]=str;
    });
    
    $("#modifySubmit").on('click',function(){
        var str={};
        str.id=id;
        str.topic=$("#topic"+id).html();
        if($("input[name='ptype']:checked").val()==0){
           $("#ptype"+id).html('短信');
        }else{
           $("#ptype"+id).html('push');
        }
        str.type=$("input[name='ptype']:checked").val();
        if(($("#first"+id).html())=='半小时'){
            $("#first"+id).html("半小时");
            str.first=30;
        }else{
            if(!$("#hour1").val() || !$("#min1").val()){
                alert("必须选择第一次的时间");
                return false;
            }
            $("#first"+id).html("次日"+$("#hour1").val()+"时"+$("#min1").val()+"分");
            str.first='1-'+$("#hour1").val()+':'+$("#min1").val();
        }
        if($("#hour2").val() && $("#min2").val()){
            var s = $("#secend"+id).html();
            var f = $("#first"+id).html();
            if((s.indexOf("次日")!=-1 || s=='/') && f.indexOf("次日")==-1){
                $("#secend"+id).html("次日"+$("#hour2").val()+"时"+$("#min2").val()+"分");
                str.secend='1-'+$("#hour2").val()+':'+$("#min2").val();
            }else{
                $("#secend"+id).html("第三日"+$("#hour2").val()+"时"+$("#min2").val()+"分");
                str.secend='2-'+$("#hour2").val()+':'+$("#min2").val();
            }
        }else{
            $("#secend"+id).html("/");
        }
        var head=$("#topic"+id).html();
        if(head.indexOf("实名未购彩")!=-1){
            str.redpack = $("#redpack").val();
            var q=str.redpack.split(",");  
            $("#red"+id).html(q[1]);
        }
        if(str.type!=0){
            str.pushtitle = $("#pushtitletxt").val();
            if(!str.pushtitle){
                alert("必须输入标题");
                return false;
            }
            str.pushAction = $("#pushActionSelect").val();
            if(str.pushAction==2){
                str.pushurl = $("#pushurlTxt").val();
            }
        }
        str.content = $("#content").val().trim();
        if(!str.content){
            alert("必须输入内容");
            return false;
        }
        $("#content"+id).html(str.content);
        changeArr[id]=str;
        closePop();
    });
    
    $(".submit").click(function(){
        popdialog("alertPop");
    });
    
    $(".config-submit").click(function(){
        for (var i = 0; i < changeArr.length; i++) {
            var a = changeArr[i];
            if(a){
                a.status = $("#status"+a.id).val();
                changeArr[i] = a;
            }
        };
        $.post('/backend/Apppush/updateManagement',{data:changeArr},function(data){
            var obj = JSON.parse(data);
            if(obj.status=='y'){
                alert("操作成功！");
                location.reload();
            }else{
                alert(obj.message);
                closePop();
            }
    });
    });
</script>


