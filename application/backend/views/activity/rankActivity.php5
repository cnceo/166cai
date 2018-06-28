<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">中奖排行榜活动</a></div>
<div class="data-table-filter mt10" style="width:1100px">
    <form action="/backend/Activity/rankActivity" method="get" id="search_form">
        <table>
            <colgroup>
                <col width="62">
                <col width="140">
                <col width="62">
                <col width="400">
                <col width="62">
                <col width="232">
            </colgroup>
            <tbody>
                <tr>
                    <th>彩种系列：</th>
                    <td>
                        <select class="selectList w120" name="plid">
                            <?php foreach ($plidArr as $key => $val):?>
                            <option value="<?php echo $key;?>" <?php if($search['plid'] == $key): echo "selected"; endif;?>><?php echo $val;?></option>
                            <?php endforeach;?>   
                        </select>
                    </td>
                    <th>活动状态：</th>
                    <td>
                        <select class="selectList w120" name="status">
                            <?php foreach ($statusArr as $key => $val):?>
                            <option value="<?php echo $key;?>" <?php if($search['status'] == $key): echo "selected"; endif;?>><?php echo $val;?></option>
                            <?php endforeach;?>   
                        </select>
                    </td>
                    <th>派奖状态：</th>
                    <td>
                        <select class="selectList w120" name="cstate">
                            <?php foreach ($cstateArr as $key => $val):?>
                            <option value="<?php echo $key;?>" <?php if($search['cstate'] == $key): echo "selected"; endif;?>><?php echo $val;?></option>
                            <?php endforeach;?>   
                        </select>
                    </td>
                    <th></th>
                    <td><a href="javascript:void(0);" class="btn-blue mr20 " onclick="$('#search_form').submit();">查询</a></td>
                    <th></th>
                    <td><a href="javascript:void(0);" class="btn-blue mr20 addRank">新建活动</a></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<div class="data-table-list mt20">
  	<table>
        <colgroup>
            <col width="5">
            <col width="10">
            <col width="10">
            <col width="10">
            <col width="7">
            <col width="10">
            <col width="10">
            <col width="10">
            <col width="7">
            <col width="7">
            <col width="7">
            <col width="7">
        </colgroup>
        <thead>
            <tr>
                <td colspan="12">
                    <div class="tal">
                        <strong>活动期次</strong>
                        <span><?php echo $total['totalIssue']; ?> 次</span>
                        <strong class="ml20">加奖总额</strong>
                        <span><?php echo number_format(ParseUnit($total['totalAdd'], 1), 2) ?> 元</span>
                        <strong class="ml20">用户统计</strong>
                        <span><?php echo $total['totalNum']; ?> 人</span>
                    </div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>活动期次</th>
                <th>活动开始时间</th>
                <th>活动结束时间</th>
                <th>涉及彩种</th>
                <th>用户统计</th>
                <th>订单总额（元）</th>
                <th>中奖总额（税后）</th>
                <th>加奖总额（元）</th>
                <th>活动状态</th>
                <th>派奖状态</th>
                <th>操作</th>
                <th>榜单</th>
            </tr>
            <?php if(!empty($result)):?>
            <?php foreach ($result as $items):?>
            <tr>
                <td><?php echo $items['issue']; ?></td>
                <td><?php echo $items['start_time']; ?></td>
                <td><?php echo $items['end_time']; ?></td>
                <td><?php echo $items['lids']; ?></td>
                <td><?php echo $items['totalNum'];; ?></td>
                <td><?php echo ParseUnit($items['totalMoney'], 1); ?></td>
                <td><?php echo ParseUnit($items['totalMargin'],1); ?></td>
                <td><?php echo ParseUnit($items['totalAdd'],1); ?></td>
                <td><?php echo $items['statusMsg']; ?></td>
                <td><?php echo $items['cstateMsg']; ?></td>
                <td><a href="/backend/Activity/rankConfigDetail/<?php echo $items['plid'] ?>/<?php echo $items['issue'] ?>" target="_blank">查看配置</a></td>
                <td><a href="/backend/Activity/rankListDetail?plid=<?php echo $items['plid'] ?>&pissue=<?php echo $items['issue'] ?>" target="_blank">查看榜单</a></td>
            </tr>
            <?php endforeach;?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9">
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
<!-- 创建活动 start -->
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
                            <td>彩种系列：</td>
                            <td>
                                <label for="" class="mr20"><input type="radio" name="plid" value="1" class="selectPlid" checked>11选5系列</label>
                                <label for="" class="mr20"><input type="radio" name="plid" value="2" class="selectPlid">快3系列</label>
                                <label for="" class="mr20"><input type="radio" name="plid" value="3" class="selectPlid">竞彩系列</label>
                            </td>
                        </tr>
                        <tr>
                            <td>参与彩种：</td>
                            <td id="getLids" data-index="1">
                                <label for="" class="mr20"><input type="checkbox" value="21407" name="lids">新11选5</label>
                                <label for="" class="mr20"><input type="checkbox" value="21408" name="lids">惊喜11选5</label>
                                <label for="" class="mr20"><input type="checkbox" value="21406" name="lids">山东11选5</label>
                                <label for="" class="mr20"><input type="checkbox" value="21421" name="lids">乐11选5</label>
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
                            <td>上传素材：</td>
                            <td>
                                <div class="btn-white file">选择文件</div>
                                <div class="btn-white upload">开始上传</div>
                                <input type="hidden" id="path" name="imgUrl" value="">
                                <div id="imgdiv" class="imgDiv"><img id="imgShow" src="" width="100" height="50" /></div>
                            </td>
                            <td>
                                尺寸720*310
                            </td>
                        </tr>
                        <tr>
                            <td>上传规则：</td>
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
                                        <th width="70%">名次</th>
                                        <th width="30%">彩金奖励（元）</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">
                                            <a href="javascript:;" class="btn-white">添加一行</a>
                                        </td>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <tr class="hidden">
                                        <td>
                                            <input type="text" class="ipt w60" value="" name="getMin">
                                            &nbsp; 至 &nbsp;
                                            <input type="text" class="ipt w60" value="" name="getMax">
                                        </td>
                                        <td>
                                            <input type="text" class="ipt w60" value="" name="getVal">
                                            <a href="javascript:;" class="tab-radio-del">×</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" class="ipt w60" value="" name="getMin">
                                            &nbsp; 至 &nbsp;
                                            <input type="text" class="ipt w60" value="" name="getMax">
                                        </td>
                                        <td>
                                            <input type="text" class="ipt w60" value="" name="getVal">
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
<!-- 创建活动 end -->
<script src="/source/js/webuploader.min.js"></script>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
        // 时间控件
        $(".Wdate1").focus(function(){
            dataPicker();
        });

        var uploader = WebUploader.create({
            swf: '/caipiaoimg/v1.1/js/jUploader.swf',
            pick: '.file',
        });

        $(".upload").click(function(){
            uploader.options.server = "/backend/Activity/uploadbanner/" + $(this).data('index');
            var files = uploader.getFiles();
            var index = files.length - 1;
            // 分割文件名
            if(!(/^\w+\.\w+$/.test(files[index].name))){
                alert('文件名只能包含字母和数字！');
                uploader.removeFile(files);
                return false;
            }
            uploader.upload();
        })

        $(".uploadTxt").click(function(){
            var platform = $('input[name="platform"]').val();
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
        })

        uploader.on( 'uploadSuccess', function( file, data) {
            if(data.type == 'rule'){
                $("#ruleDetail").val(data.txt);
                $(".ruleName").html(data.name);
            }else{
                $("#imgShow").attr('src', data.path + data.name);
                $('#path').val('/uploads/banner/' + data.name)
            } 
        });

        // 新建
        $(".addRank").click(function(){
            popdialog("dialog-create");
            return false;
        });

        // 切换彩种系列
        $(".selectPlid").click(function(){
            var plid = $(this).val();
            var cplid = $("#getLids").data('index');
            if(plid != cplid){
                var html = '';
                if(plid == '1'){
                    html += '<label for="" class="mr20"><input type="checkbox" value="21407" name="lids">新11选5</label>';
                    html += '<label for="" class="mr20"><input type="checkbox" value="21408" name="lids">惊喜11选5</label>';
                    html += '<label for="" class="mr20"><input type="checkbox" value="21406" name="lids">山东11选5</label>';
                    html += '<label for="" class="mr20"><input type="checkbox" value="21421" name="lids">乐11选5</label>';
                }else if(plid == '2'){
                    html += '<label for="" class="mr20"><input type="checkbox" value="57" name="lids">红快3</label>';
                    html += '<label for="" class="mr20"><input type="checkbox" value="56" name="lids">易快3</label>';
                    html += '<label for="" class="mr20"><input type="checkbox" value="53" name="lids">经典快3</label>';
                }else{
                    html += '<label for="" class="mr20"><input type="checkbox" value="42" name="lids">竞彩足球</label>';
                    html += '<label for="" class="mr20"><input type="checkbox" value="43" name="lids">竞彩篮球</label>';
                }
                $("#getLids").html(html);
                $("#getLids").data('index', plid);
            }
        });

        // 新增一行
        $('#tab-create').on('click', '.btn-white', function(){
            var tbody = $(this).parents('table').find('tbody');
            var innerTr = tbody.find('tr')[0].innerHTML;
            tbody.append('<tr>' + innerTr + '</tr>');
        })

        // 删除一行
        $('#tab-create').on('click', '.tab-radio-del', function(){
            $(this).parents('tr').remove();
        })

        // 确认创建弹窗
        var selectTag = true;
        $("#confirmPop").click(function(){
            // 填充内容
            var plid = $('#dialog-create input[name="plid"]:checked').val();
            // 涉及彩种
            var lidArr = [];
            $('#dialog-create input[name="lids"]:checked').each(function(){ 
                lidArr.push($(this).val());  
            }); 
            var lids = lidArr.join(",");
            var startTime = $('#dialog-create input[name="startTime"]').val();
            var endTime = $('#dialog-create input[name="endTime"]').val();
            var imgUrl = $('#dialog-create input[name="imgUrl"]').val();
            var rule = $('#dialog-create input[name="rule"]').val();

            // 字段检查
            if(lids == ''){
                alert("涉及彩种不能为空");
                return false;
            }

            if(imgUrl == ''){
                alert("上传素材不能为空");
                return false;
            }

            if(rule == ''){
                alert("上传规则不能为空");
                return false;
            }

            // 配置信息
            var plan = '';
            var getMin = '';
            var getMax = '';
            var getVal = '';
            var maxval = 0;
            var configArry = $('#config-table').find('tbody tr');
            for (var i = 1; i < configArry.length; i++) 
            {
                var tpl = '';
                getMin = $.trim($(configArry[i]).find('input[name="getMin"]').val());
                getMax = $.trim($(configArry[i]).find('input[name="getMax"]').val());
                getVal = $.trim($(configArry[i]).find('input[name="getVal"]').val());

                if(getMin == '' || getMax == '' || getVal == '')
                {
                    alert("名次内容为空");
                    return false;
                }
                var reg = /^[0-9]\d*$/;

                if(!reg.test(getMin) || !reg.test(getMax) || !reg.test(getVal))
                {
                    alert("名次格式错误");
                    return false;
                }

                tpl = getMin + ',' + getMax + ',' + getVal;
                if(plan != '')
                {
                  plan = plan + '|' + tpl;
                }else{
                  plan = tpl;
                }
                maxval = getMax;
            };

            if(maxval > 1000){
                alert("最大名次配置为1000名");
                return false;
            }
            if(selectTag){
                selectTag = true;
                $.ajax({
                    type: 'post',
                    url: '/backend/Activity/createCheckRank',
                    data: {plid:plid,lids:lids,startTime:startTime,endTime:endTime,imgUrl:imgUrl,rule:rule,plan:plan},

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
    });
</script>
</body>
</html>