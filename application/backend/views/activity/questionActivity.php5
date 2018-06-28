<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">答题活动</a></div>
<div class="data-table-filter mt10" style="width:1100px">
    <a href="javascript:void(0);" class="btn-blue mr20 addQuestion">新建活动</a>
</div>
<div class="data-table-list mt20">
  	<table>
        <colgroup>
            <col width="10">
            <col width="15">
            <col width="15">
            <col width="20">
            <col width="15">
            <col width="15">
            <col width="10">
            <col width="10">
        </colgroup>
        <thead>
            <tr>
                <td colspan="8">
                    <div class="tal">
                        <strong>参与总人数</strong>
                        <span><?php echo $all['count']; ?></span>
                        <strong class="ml20">派发红包总数</strong>
                        <span><?php echo $all['allred']; ?></span>
                        <strong class="ml20">使用红包总数</strong>
                        <span><?php echo $all['allusered']; ?></span>
                    </div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>活动期次</th>
                <th>派发红包数</th>
                <th>使用红包数</th>
                <th>红包明细(红包类型:派发数/使用数)</th>
                <th>活动开始时间</th>
                <th>活动结束时间</th>
                <th>查看活动</th>
                <th>结束活动</th>
            </tr>
            <?php foreach ($datas as $k =>$data){ ?>
            <tr>
                <td><?php echo $data['id'] ?></td>
                <td><?php echo $data['qall'] ?></td>
                <td><?php echo $data['quse'] ?></td>
                <td>
                    <?php foreach ($data['extra'] as $extra){
                    echo $extra['use_desc'].$extra['p_name'].'：'.$extra['count'].'/'.$extra['usecount'].'<br>';
                    } ?>
                </td>
                <td><?php echo $data['start_time'] ?></td>
                <td><?php echo $data['end_time'] ?></td>
                <td><a href="javascript:;" class="btn openQuestion" data-id="<?php echo $data['id']?>">查看</a></td>
                <td><?php if($data['status'] == 1){?><a href="javascript:;" class="btn closeQuestion" data-id="<?php echo $data['id']?>">结束</a><?php } ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8">
                    <div class="stat">
                        <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
                        <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
                        <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="page mt10 order_info">
    <?php echo $pages[0] ?>
</div>
<?php foreach ($datas as $k =>$data){ ?>
<div class="pop-dialog" id="dialog-show<?php echo $data['id']?>" style="display:none; width:650px;">
    <div class="pop-in">
        <div class="pop-head">
            <h2>查看活动</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup>
                        <col width="60">
                        <col width="240">
                    </colgroup>
                    <tbody>
                        <tr>
                            <td>答题期次：</td>
                            <td>
                                <?php echo $data['id']?>
                            </td>
                        </tr>
                        <tr>
                            <td>答题链接：</td>
                            <td>
                                <?php echo $data['questionUrl']?>
                            </td>
                        </tr>
                        <tr>
                            <td>开始时间：</td>
                            <td>
                                <?php echo $data['start_time']?>
                            </td>
                        </tr>
                        <tr>
                            <td>结束时间：</td>
                            <td>
                                <?php echo $data['end_time']?>
                            </td>
                        </tr>
                        <tr>
                            <td>活动文案：</td>
                            <td>
                                <?php $wenan = explode('*', $data['titleDesc']);
                                  echo $wenan['0'].'<br>'.$wenan[1];
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>活动规则：</td>
                            <td>
                                <?php echo $data['rule']?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="tab-radio-bd">
                    <ul>
                        <li style="display: block;">
                            <table>
                                <thead>
                                    <tr>
                                        <th width="30%">答对题数</th>
                                        <th width="40%">红包奖励</th>
                                        <th width="30%">红包生效期</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['extras'] as $e){?>
                                    <tr>
                                        <td><?php echo $e['min']?>至<?php echo $e['max']?></td>
                                        <td><?php echo $e['p_name']?></td>
                                        <td><?php echo $e['ridTime']?></td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:closePop();" class="btn-blue-h32 mlr15">确认</a>
        </div>
    </div>
</div>
<?php } ?>
<div class="pop-dialog" id="dialog-create" style="display:none; width:650px;">
    <div class="pop-in">
        <div class="pop-head">
            <h2>新建活动</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup>
                        <col width="60">
                        <col width="240">
                    </colgroup>
                    <tbody>
                        <tr>
                            <td>答题链接：</td>
                            <td>
                                <input type="text" class="ipt w300p" style="width: 400px;" name="url" placeholder="" value="">
                            </td>
                        </tr>
                        <tr>
                            <td>活动时间：</td>
                            <td>
                                <span class="ipt ipt-date w150"><input type="text" class="Wdate1" name="startTime" value="<?php echo date('Y-m-d 00:00:00'); ?>"><i></i></span>
                                至
                                <span class="ipt ipt-date w150"><input type="text" class="Wdate1" name="endTime" value="<?php echo date("Y-m-d 23:59:59"); ?>"><i></i></span>
                            </td>
                        </tr>
                        <tr>
                            <td>活动文案：</td>
                            <td>
                                <input type="text" class="ipt w300p" style="width: 400px;" name="ldes" placeholder="不超过20个字符" value="">
                                <br><br>
                                <input type="text" class="ipt w300p" style="width: 400px;" name="rdes" placeholder="不超过20个字符" value="">
                            </td>
                        </tr>
                        <tr>
                            <td>活动规则：</td>
                            <td>
                                <div class="btn-white file">选择文件</div>
                                <div class="btn-white uploadTxt">开始上传</div>
                                <input type="hidden" id="ruleDetail" name="rule" value="">
                                <div id="imgdiv" class="imgDiv ruleName">未选择任何文件</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="tab-radio-bd" id="tab-create">
                    <ul>
                        <li style="display: block;">
                            <table id="config-table">
                                <thead>
                                    <tr>
                                        <th width="30%">答对题数</th>
                                        <th width="30%">红包奖励</th>
                                        <th width="30%">红包生效期</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <a href="javascript:;" class="btn-white">添加一行</a>
                                        </td>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <tr class="hidden" data-id="1">
                                        <td>
                                            <input type="text" class="ipt w60" value="" name="getMin">
                                            &nbsp; 至 &nbsp;
                                            <input type="text" class="ipt w60" value="" name="getMax">
                                        </td>
                                        <td>
                                            <a href="javascript:;" class="btn-blue choseHongbao">选择红包</a>
                                        </td>
                                        <td>
                                            
                                        </td>
                                        <td>
                                            <a href="javascript:;" class="tab-radio-del">×</a>
                                        </td>
                                    </tr>
                                    <tr id="chose1" data-id="1">
                                        <td>
                                            <input type="text" class="ipt w60" value="" name="getMin">
                                            &nbsp; 至 &nbsp;
                                            <input type="text" class="ipt w60" value="" name="getMax">
                                        </td>
                                        <td>
                                            <a href="javascript:;" class="btn-blue choseHongbao">选择红包</a>
                                        </td>
                                        <td>
                                            
                                        </td>
                                        <td>
                                            <a href="javascript:;" class="tab-radio-del">×</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirmPop">确认</a>
            <a href="javascript:closePop();" class="btn-b-white">取消</a>
        </div>
    </div>
</div>
<div class="pop-dialog" id="dialog-hongbao" style="display:none; width:650px;">
    <div class="pop-in">
        <div class="pop-head">
            <h2>选择红包</h2>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <label for="" class="mr20"><input type="radio" checked="checked" name="hongbaotype" value="1" class="selecthongbaotype">不派发红包</label>
                            </td>
                            <td>
                                <label for="" class="mr20"><input type="radio" name="hongbaotype" value="2" class="selecthongbaotype">彩金红包</label>
                                <input type="text" class="ipt w60" value="" name="caijinhongbao">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="" class="mr20"><input type="radio" name="hongbaotype" value="3" class="selecthongbaotype">购彩红包</label>
                            </td>
                            <td>
                                <label for="" class="mr20"><input type="radio" name="hongbaotype" value="4" class="selecthongbaotype">充值红包</label>
                            </td>
                        </tr>
                        <tr>
                            <td id="hongbaolist"> 
                                
                            </td>
                        </tr>
                        <tr>
                            <td id="hongbaotime" style="display:none;">红包生效期：<span class="ipt ipt-date w150"><input type="text" class="Wdate1" name="hongbaoStartTime" value="<?php echo date('Y-m-d 00:00:00'); ?>"><i></i></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirmHongbao">确认</a>
            <a href="javascript:closePops();" class="btn-b-white">取消</a>
        </div>
    </div>
</div>
<div class="pop-dialog" id="dialog-confirm" style="display:none; width:650px;">
    <div class="pop-in">
        <div class="pop-head">
            <h2>新建活动确认</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup>
                        <col width="60">
                        <col width="240">
                    </colgroup>
                    <tbody>
                        <tr>
                            <td>答题链接：</td>
                            <td id="confirm_url">
                                
                            </td>
                        </tr>
                        <tr>
                            <td>开始时间：</td>
                            <td id="confirm_start">
                                
                            </td>
                        </tr>
                        <tr>
                            <td>结束时间：</td>
                            <td id="confirm_end">
                                
                            </td>
                        </tr>
                        <tr>
                            <td>活动文案：</td>
                            <td id="confirm_des">
                                
                            </td>
                        </tr>
                        <tr>
                            <td>活动规则：</td>
                            <td id="confirm_rule">
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="tab-radio-bd">
                    <ul>
                        <li style="display: block;">
                            <table>
                                <thead>
                                    <tr>
                                        <th width="30%">答对题数</th>
                                        <th width="40%">红包奖励</th>
                                        <th width="30%">红包生效期</th>
                                    </tr>
                                </thead>
                                <tbody id="confirm_hongbao">
                                    
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15" id="submitconfig">确认</a>
            <a href="javascript:closePop();" class="btn-b-white">取消</a>
        </div>
    </div>
</div>
<div class="pop-dialog" id="closeconfirmPop" style="width:200px;">
        <div class="pop-in">
            <div class="pop-body">
                <div class="data-table-list">
                    <table>
                        <div id="showAlert" style="text-align:center;font-size:20px;font-weight:bolder"></div>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirmPopSubmit" data-id="0">确认</a>
                <a href="javascript:closePop();" class="btn-b-white mlr15 pop-cancel">取消</a>
            </div>
        </div>
    </div>
<script  src="/source/js/webuploader.min.js"></script>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    var goucaihongbao = <?php echo json_encode($goucai); ?>;
    var chongzhihongbao = <?php echo json_encode($chongzhi); ?>;
    var closePops = function closePops(){
        $("#dialog-hongbao").css("display","none");
    }
    var hongbaoid = 0;  
    $(function(){
        // 时间控件
        $(".Wdate1").focus(function(){
            dataPicker();
        });
        
        var uploader = WebUploader.create({
            swf: '/caipiaoimg/v1.1/js/jUploader.swf',
            pick: '.file',
        });
        
        $(".addQuestion").click(function(){
            popdialog("dialog-create");
            return false;
        });
        
        // 新增一行
        $('#tab-create').on('click', '.btn-white', function(){
            var tbody = $(this).parents('table').find('tbody');
            var innerTr = tbody.find('tr')[0].innerHTML;
            var id = $("#tab-create .hidden").data('id');
            var id = id+1;
            $("#tab-create .hidden").data('id',id);
            tbody.append('<tr id="chose'+id+'" data-id="'+id+'">' + innerTr + '</tr>');
        });

        // 删除一行
        $('#tab-create').on('click', '.tab-radio-del', function(){
            $(this).parents('tr').remove();
        });
        
        $(".uploadTxt").click(function(){
            uploader.options.server = "/backend/Activity/uplaodTxtFile/" + $(this).data('index') + "/rule";
            var files = uploader.getFiles();
            var index = files.length - 1;
            // 分割文件名
            if(!(/^\w+\.\w+$/.test(files[index].name))){
                alert('文件名只能包含字母和数字！');
                uploader.removeFile(files);
                return false;
            }
            uploader.upload();
        });
        
        uploader.on( 'uploadSuccess', function( file, data) {
            if(data.type == 'rule'){
                if(data.name.length>20){
                  data.name=data.name.substring(0,20)+"...";
                }
                $("#ruleDetail").val(data.txt);
                $(".ruleName").html(data.name);
                $("#confirm_rule").html(data.txt);
            }else{
                $("#imgShow").attr('src', data.path + data.name);
                $('#path').val('/uploads/banner/' + data.name)
            } 
        });
        
        $('#tab-create').on('click', '.choseHongbao', function(){
            hongbaoid = $(this).parents('tr').data('id');
            $("#dialog-hongbao").css({
                display: "inline",
                marginTop: -($("#dialog-hongbao").outerHeight()) / 2,
                marginLeft: -($("#dialog-hongbao").outerWidth()) / 2
            });
            return false;
        });
       
       $('#dialog-hongbao').on('click', '.selecthongbaotype', function(){
            var type = $(this).val();
            var html = '';
            if(type == '3'){
                html+= '<select class="selectList w110" name="chosehongbao">';
                for(var i=0;i<goucaihongbao.length;i++){
                    html+= "<option value='"+goucaihongbao[i].rid+"'>"+goucaihongbao[i].name+"</option>";
                }
                html+= '</select>';
                $("#hongbaotime").css("display",'block');
            }else if(type == '4'){
                html+= '<select class="selectList w110" name="chosehongbao">';
                for(var i=0;i<chongzhihongbao.length;i++){
                    html+= "<option value='"+chongzhihongbao[i].rid+"'>"+chongzhihongbao[i].name+"</option>";
                }
                html+= '</select>';
                $("#hongbaotime").css("display",'block');
            }else{
                $("#hongbaotime").css("display",'none');
            }
            $("#hongbaolist").html(html);
        });
        
        $("#confirmHongbao").click(function(){
            var type = $('#dialog-hongbao input[name="hongbaotype"]:checked').val();
            var hongbao = '<input type="hidden" data-name="-" name="rid" value="-">'+'-';
            var hongbaotime = '<input type="hidden" name="ridTime" value="-">'+'-';
            if(type == 2){
                var caijinhongbao = $('#dialog-hongbao input[name="caijinhongbao"]').val();
                var re = /^[1-9]+[0-9]*]*$/
                if(!re.test(caijinhongbao)){
                    alert("彩金红包金额输入错误");
                    return false;
                }
                hongbao = '<input type="hidden" data-name="'+caijinhongbao+'元彩金红包" name="rid" value="'+caijinhongbao+'">'+caijinhongbao+"元彩金红包";
            }
            if(type == 3 || type == 4){
                hongbao = '<input type="hidden" name="rid" data-name="'+$(".selectList").find("option:selected").text()+'" value="'+$(".selectList").val()+'">'+$(".selectList").find("option:selected").text();
                hongbaotime =  '<input type="hidden" name="ridTime" value="'+$('#dialog-hongbao input[name="hongbaoStartTime"]').val()+'">'+$('#dialog-hongbao input[name="hongbaoStartTime"]').val();
            }
            $("#chose"+hongbaoid+" td:eq(1)").html(hongbao);
            $("#chose"+hongbaoid+" td:eq(2)").html(hongbaotime);
            closePops();
        });
        
        var selectTag = true;
        var url,rule,startTime,endTime,ldes,rdes,plan;
        $("#confirmPop").click(function(){
            plan = {min:{},max:{},rid:{},ridTime:{}};
            url = $('#dialog-create input[name="url"]').val();
            if(!url){
                alert("请填写答题链接");
                return false;
            }
            $("#confirm_url").html(url);
            startTime = $('#dialog-create input[name="startTime"]').val();
            endTime = $('#dialog-create input[name="endTime"]').val();
            if(!startTime || !endTime || (startTime>endTime)){
                alert("活动时间设置错误");
                return false;
            }
            $("#confirm_start").html(startTime);
            $("#confirm_end").html(endTime);
            ldes = $('#dialog-create input[name="ldes"]').val();
            rdes = $('#dialog-create input[name="rdes"]').val();
            if(!ldes || !rdes){
                alert("请填写活动文案");
                return false;
            }
            if(ldes.length > 20 || rdes.length > 20){
                alert("活动文案不超过20个字符");
                return false;
            }
            $("#confirm_des").html(ldes+"<br>"+rdes);
            rule = $('#dialog-create input[name="rule"]').val();
            if(rule == ''){
                alert("上传规则不能为空");
                return false;
            }
            // 配置信息
            var getMin = 0;
            var getMax = 0;
            var max = 0;
            var rid = '';
            var ridTime = '';
            var ridName = '';
            var configArry = $('#config-table').find('tbody tr');
            for (var i = 1; i < configArry.length; i++) 
            {
                getMin = parseInt($.trim($(configArry[i]).find('input[name="getMin"]').val()));
                getMax = parseInt($.trim($(configArry[i]).find('input[name="getMax"]').val()));
                rid = $.trim($(configArry[i]).find('input[name="rid"]').val());
                ridName = $.trim($(configArry[i]).find('input[name="rid"]').data('name'));
                ridTime = $.trim($(configArry[i]).find('input[name="ridTime"]').val());
                if(getMin === '' || getMax === '' || rid == '' || ridTime == '')
                {
                    alert("答题奖励内容不能有空");
                    return false;
                }
                var reg = /^[0-9]\d*$/;
                if(!reg.test(getMin))
                {
                    alert("答题范围格式错误");
                    return false;
                }
                if(!reg.test(getMax) && getMax!='*')
                {
                    alert("答题范围格式错误");
                    return false;
                }
                if(getMin > getMax)
                {
                    alert("答题范围格式错误");
                    return false;
                }
                if(getMin <= max && max!=0)
                {
                    alert("答题范围格式错误");
                    return false;
                }else{
                    if((getMin-max)>1){
                        alert("答题范围格式错误");
                        return false;
                    }
                    max = getMax;
                }
                plan.min[i]=getMin;
                plan.max[i]=getMax;
                plan.rid[i]=rid;
                plan.ridTime[i]=ridTime;
                if(ridTime != '-' && ridTime < startTime){
                    alert("红包有效期需要设置在活动开始时间之后");
                    return false;
                }
                $("#confirm_hongbao").append('<tr><td>'+getMin+'至'+getMax+'</td><td>'+ridName+'</td><td>'+ridTime+'</td></tr>');
            };
            popdialog("dialog-confirm");
            return false;
        });
        $("#submitconfig").click(function(){
            if(selectTag){
                selectTag = true;
                $.ajax({
                    type: 'post',
                    url: '/backend/Activity/createQuestionActivity',
                    data: {url:url,startTime:startTime,endTime:endTime,ldes:ldes,rdes:rdes,rule:rule,plan:plan},
                    success: function (response) {
                        var response = $.parseJSON(response);
                        if(response.status == 'y')
                        {
                            selectTag = true;
                            closePop();
                            alert(response.message);
                            window.location.reload();
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
        
        $(".openQuestion").click(function(){
            var id =$(this).data('id');
            popdialog("dialog-show"+id);
        });
        
        $(".closeQuestion").click(function(){
            var id =$(this).data('id');
            $("#confirmPopSubmit").data('id',id);
            $("#showAlert").html("结束第"+id+"期活动？");
            popdialog("closeconfirmPop");
        });
        $("#confirmPopSubmit").click(function(){
            var id =$(this).data('id');
                $.ajax({
                    type: 'get',
                    url: '/backend/Activity/closeQuestionActivity/'+id,
                    success: function (response) {
                        var response = $.parseJSON(response);
                        if(response.status == 'y')
                        {
                            alert(response.message);
                            window.location.reload();
                        }else{
                            alert(response.message);
                        }
                    },
                    error: function () {
                        selectTag = true;
                        alert('网络异常，请稍后再试');
                    }
                });
        });
    });
</script>
</body>
</html>