<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">竞彩活动</a></div>
<div class="mod-tab mod-tab-s mt20 mb20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/Activity/newActivityJc">不中包赔</a></li>
            <li class="current"><a href="/backend/Activity/activityJj">竞彩加奖</a></li>
        </ul>
    </div>
</div>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li><a href="/backend/Activity/activityJj">活动概览</a></li>
      		<li class="current"><a href="/backend/Activity/manageJj">活动管理</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter">
          			<table>
            			<tbody>
              				<tr>
                				<td>
                  					<a href="javascript:;" class="btn-blue" id="createJj">新建活动</a>
                				</td>
                        <td style="padding-left:10px;">
                            <a href="javascript:;" class="btn-blue" id="jzCast">投注栏配置</a>
                        </td>
              				</tr>
            			</tbody>
          			</table>
        		</div>
        		<div>
                    活动期次：<span><?php echo $total['activityNum']; ?></span>次&nbsp;&nbsp;加奖总额：<span><?php echo ParseUnit($total['totalMoney'], 1); ?></span>元&nbsp;&nbsp;用户统计：<span><?php echo $total['totalPeople']; ?></span>人
                </div>
        		<div class="data-table-list mt10">
                    <table>
                        <colgroup>
                            <col width="50">
                            <col width="100">
                            <col width="100">
                            <col width="70">
                            <col width="70">
                            <col width="70">
                            <col width="70">
                            <col width="70">
                            <col width="70">
                            <col width="70">
                            <col width="50">
                            <col width="50">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>活动期次</th>
                                <th>活动开始时间</th>
                                <th>活动结束时间</th>
                                <th>彩种</th>
                                <th>加奖玩法</th>
                                <th>加奖形式</th>
                                <th>用户统计</th>
                                <th>订单总额（元）</th>
                                <th>中奖总额（税后）</th>
                                <th>加奖总额（元）</th>
                                <th>活动状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($result)):?>
                            <?php foreach ($result as $items):?>
                            <tr>
                                <td><?php echo $items['id'];?></td>
                                <td><?php echo $items['startTime'];?></td>
                                <td><?php echo $items['endTime'];?></td>
                                <td><?php echo $items['lname'];?></td>
                                <td><?php echo $items['playTypeName'];?></td>
                                <td><?php echo $items['ctypeName'];?></td>
                                <td><?php echo $items['num'];?></td>
                                <td><?php echo ParseUnit($items['money'], 1);?></td>
                                <td><?php echo ParseUnit($items['margin'], 1);?></td>
                                <td><?php echo ParseUnit($items['add_money'], 1);?></td>
                                <td><?php echo $items['status'];?></td>
                                <td><a href="/backend/Activity/JjDetail/<?php echo $items['id'];?>" class="cBlue" target="_blank">查看</a></td>
                            </tr>
                            <?php endforeach;?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="11">
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
      		</li>
    	</ul>
  	</div>
    <div class="page mt10 order_info">
      <?php echo $pages[0] ?>
    </div>
  	<!-- 创建活动 start -->
  	<div class="pop-dialog" id="dialog-createJj" style="display:none;">
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
                                <td>彩&nbsp;&nbsp;种：</td>
                                <td>
                                    <label for="" class="mr20"><input type="radio" name="lid" value="42" checked>竞足</label>
                                    <label for=""><input type="radio" name="lid" value="43">竞蓝</label>
                                </td>
                            </tr>
                            <tr>
                                <td>加奖平台：</td>
                                <td>
                                    <label for="" class="mr20"><input type="checkbox" value="web" name="buyPlatform" plat-data="网页端">网页端</label>
                                    <label for="" class="mr20"><input type="checkbox" value="app" name="buyPlatform" plat-data="移动端">移动端</label>
                                    <label for="" class="mr20"><input type="checkbox"  value="m" name="buyPlatform" plat-data="M版">M版</label>
                                </td>
                            </tr>
                            <tr>
                                <td>加奖玩法：</td>
                                <td>
                                    <label for="" class="mr20"><input type="radio" name="playType" type-data="单关" value="0" checked>单关</label>
                                    <label for="" class="mr20"><input type="radio" name="playType" type-data="2串1" value="1">2串1</label>
                                    <!-- <label for=""><input type="radio" name="jiajiangwanfa">不限</label> -->
                                </td>
                            </tr>
                            <tr>
                                <td>活动时间：</td>
                                <td>
                                    <span class="ipt ipt-date w150"><input type="text" class="Wdate1" name="startTime" value="<?php echo date('Y-m-d H:i:s'); ?>"><i></i></span>
                                    至
                                    <span class="ipt ipt-date w150"><input type="text" class="Wdate1" name="endTime" value="<?php echo date("Y-m-d H:i:s",strtotime("+1 month")); ?>"><i></i></span>
                                </td>
                            </tr>
                            <tr>
                                <td>加奖形式：</td>
                                <td>
                                    <div class="tab-radio-hd">
                                        <label for="anMoney" class="current mr20"><input type="radio" id="anMoney" checked name="ctype" value="0">按金额加奖</label>
                                        <!-- <label for="anProportion"><input type="radio" id="anProportion" name="ctype" value="1">按比例加奖</label> -->
                                    </div>
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
                                            <th width="70%">订单税后中奖金额（元）</th>
                                            <th width="30%">加奖金额（元）</th>
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
                                                &lt; 奖金 ≤
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
                                                &lt; 奖金 ≤
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
                <a href="javascript:;" class="btn-b-white" id="confirmCancel">取消</a>
            </div>
        </div>
	</div>
	<!-- 创建活动 end -->
    <!-- 弹出层配置 start -->
    <div class="pop-dialog" id="dialog-jzCast" style="display:none;">
        <div class="pop-in">
            <div class="pop-head">
                <h2>投注栏hover配置</h2>
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
                                <td>投注页：</td>
                                <td>
                                    <label for="" class="mr20"><input type="radio" name="lname" value="JCZQ" class="choseHover current" checked>竞足</label>
                                    <label for="" class="mr20"><input type="radio" name="lname" value="JCLQ" class="choseHover">竞篮</label>
                                </td>
                            </tr>
                            <tr>
                                <td>加奖时间：</td>
                                <td>
                                    <span class="ipt ipt-date w150"><input type="text" class="Wdate1" name="cstartTime" value="<?php echo ($hoverInfo['startTime'])?$hoverInfo['startTime']:date('Y-m-d H:i:s'); ?>"><i></i></span>
                                    至
                                    <span class="ipt ipt-date w150"><input type="text" class="Wdate1" name="cendTime" value="<?php echo ($hoverInfo['endTime'])?$hoverInfo['endTime']:date("Y-m-d H:i:s",strtotime("+1 month")); ?>"><i></i></span>
                                </td>
                            </tr>
                            <tr>
                                <td>加奖平台：</td>
                                <td>
                                    <label for="" class="mr20"><input type="checkbox" value="1" name="buyPlatform" <?php echo ($hoverInfo['platform'] & 1) ? 'checked' : ''; ?> >网页端</label>
                                    <label for="" class="mr20"><input type="checkbox" value="2" name="buyPlatform" <?php echo ($hoverInfo['platform'] & 2) ? 'checked' : ''; ?> >移动端</label>
                                    <label for="" class="mr20"><input type="checkbox"  value="4" name="buyPlatform" <?php echo ($hoverInfo['platform'] & 4) ? 'checked' : ''; ?> >M版</label>
                                </td>
                            </tr>
                            <tr>
                                <td>加奖玩法：</td>
                                <td>
                                    <label for="" class="mr20"><input type="checkbox" name="playType" type-data="单关" value="0" <?php echo (in_array('0', explode(',', $hoverInfo['playType']))) ? 'checked' : ''; ?> >单关</label>
                                    <label for="" class="mr20"><input type="checkbox" name="playType" type-data="2串1" value="1" <?php echo (in_array('1', explode(',', $hoverInfo['playType']))) ? 'checked' : ''; ?> >2串1</label>
                                </td>
                            </tr>
                            <tr>
                                <td>加奖slogan：</td>
                                <td>
                                    <input type="text" class="ipt w264" value="<?php echo $hoverInfo['slogan']?$hoverInfo['slogan']:''; ?>" name="slogan">
                                </td>
                            </tr>
                            <tr>
                                <td>加奖比例：</td>
                                <td>
                                    <div class="tab-radio-hd">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="tab-radio-bd" id="tab-config">
                        <ul>
                            <li style="display: block;">
                                <table id="hover-table" class="data-table-list" style="width:420px">
                                    <thead>
                                        <tr>
                                            <th width="50%">竞彩单关、2串1奖金分布</th>
                                            <th width="25%">单关加奖金额</th>
                                            <th width="25%">2串1加奖金额</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">
                                                <a href="javascript:;" class="btn-white">添加一行</a>
                                            </td>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <tr class="hidden">
                                            <td>
                                                <input type="text" class="ipt w60" value="" name="cgetMin">
                                                &lt; 奖金 ≤
                                                <input type="text" class="ipt w60" value="" name="cgetMax">
                                            </td>
                                            <td>
                                                <input type="text" class="ipt w60" value="" name="cgetVal1">
                                            </td>
                                            <td>
                                                <input type="text" class="ipt w60" value="" name="cgetVal2">
                                                <a href="javascript:;" class="tab-radio-del">×</a>
                                            </td>
                                        </tr>
                                        <?php if(!empty($hoverInfo['params'])):?>
                                        <?php $params = json_decode($hoverInfo['params'], true); foreach ($params as $hover):?>
                                        <tr>
                                            <td>
                                                <input type="text" class="ipt w60" value="<?php echo $hover['min']; ?>" name="cgetMin">
                                                &lt; 奖金 ≤
                                                <input type="text" class="ipt w60" value="<?php echo $hover['max']; ?>" name="cgetMax">
                                            </td>
                                            <td>
                                                <input type="text" class="ipt w60" value="<?php echo $hover['dg']; ?>" name="cgetVal1">
                                            </td>
                                            <td>
                                                <input type="text" class="ipt w60" value="<?php echo $hover['2c1']; ?>" name="cgetVal2">
                                                <a href="javascript:;" class="tab-radio-del">×</a>
                                            </td>
                                        </tr>
                                        <?php endforeach;?>
                                        <?php else: ?>
                                        <tr>
                                            <td>
                                                <input type="text" class="ipt w60" value="" name="cgetMin">
                                                &lt; 奖金 ≤
                                                <input type="text" class="ipt w60" value="" name="cgetMax">
                                            </td>
                                            <td>
                                                <input type="text" class="ipt w60" value="" name="cgetVal1">
                                            </td>
                                            <td>
                                                <input type="text" class="ipt w60" value="" name="cgetVal2">
                                                <a href="javascript:;" class="tab-radio-del">×</a>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirmCast">确认</a>
                <a href="javascript:;" class="btn-b-white" id="cancelCast">取消</a>
            </div>
        </div>
    </div>
    <!-- 弹出层配置 end -->
    <!-- 确认弹出层 -->
    <div class="pop-mask" style="display:none;width:200%"></div>
        <div class="pop-dialog" id="confirm-submit">
        <div class="pop-in">
            <div class="pop-head">
                <h2>确认页</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
                <div class="data-table-filter del-percent">
                    彩种：<span id="lotteryNmae">竞彩足球</span>
                </div>
                <div class="data-table-filter del-percent">
                    加奖平台：<span id="jjcPlat"></span>
                </div>
                <div class="data-table-filter del-percent">
                    加奖玩法：<span id="jjcType">按金额加奖</span>
                </div>
                <div class="data-table-filter del-percent">
                    加奖形式：<span id="jjPlayType"></span>
                </div>
                <div class="data-table-filter del-percent">
                    活动时间：<span id="start-time"></span>至<span id="end-time"></span>
                </div>
                <div>
                    <table class="data-table-list">
                        <colgroup>
                            <col width="200">
                            <col width="100">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>订单中奖金额（元）</th>
                                <th>加奖金额（元）</th>
                            </tr>
                        </thead>
                        <tbody class="showJjPlan">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirmJj">确认</a>
                <a href="javascript:;" class="btn-b-white mlr15" id="confirm-cancel">取消</a>
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

	// 新建活动弹框 
    $("#createJj").click(function(){
        popdialog("dialog-createJj");
		return false;
	});

    $("#confirmCancel").click(function(){
        $("#dialog-createJj").hide();
        $(".pop-mask").hide();
        return false;
    });
  

    // 确认创建弹窗
    $("#confirmPop").click(function(){
        // 填充内容
        var lid = $('input[name="lid"]:checked').val();
        var playType = $('input[name="playType"]:checked').attr('type-data');
        var startTime = $('input[name="startTime"]').val();
        var endTime = $('input[name="endTime"]').val();
        var ctype = $('input[name="ctype"]:checked').val();

        // 平台
        var platform = [];
        var platformName = [];
        $('input[name="buyPlatform"]:checked').each(function(){ 
            platform.push($(this).val());  
            platformName.push($(this).attr('plat-data')); 
        }); 
        var buyPlatform = platform.join(",");

        if(buyPlatform == ''){
            alert("加奖平台不能为空");
            return false;
        }

        // 彩种
        if(lid == '42'){
            $('#lotteryNmae').html('竞彩足球');
        }else{
            $('#lotteryNmae').html('竞彩篮球');
        }   

        $('#jjcPlat').html(platformName.join(","));
        $('#jjPlayType').html(playType);
        $('#start-time').html(startTime);
        $('#end-time').html(endTime);

        var configArry = $('#config-table').find('tbody tr');
        var tpl = '';
        for (var i = 1; i < configArry.length; i++) 
        {
            getMin = $.trim($(configArry[i]).find('input[name="getMin"]').val());
            getMax = $.trim($(configArry[i]).find('input[name="getMax"]').val());
            getVal = $.trim($(configArry[i]).find('input[name="getVal"]').val());

            if(getMin == '' || getMax == '' || getVal == '')
            {
                alert("加奖内容为空");
                return false;
            }
            var reg = /^[0-9]\d*$/;

            if(!reg.test(getMin) || !reg.test(getVal))
            {
                alert("加奖内容格式错误");
                return false;
            }
            if(getMin == '0' && isNaN(getMax) && getMax != '*')
            {
                alert("加奖内容格式错误");
                return false;
            }
            if(getMax == "*")
            {
                tpl = tpl + "<tr><td>奖金>" + getMin + "</td><td>" + getVal + "</td></tr>";
            }
            else
            {
                tpl = tpl + "<tr><td>" + getMin + "<奖金≤" + getMax + "</td><td>" + getVal + "</td></tr>"; 
            } 
        };
        $('.showJjPlan').html(tpl);
        $("#dialog-createJj").hide();
        $("#confirm-submit").css({
            marginTop: -$("#confirm-submit").outerHeight() / 2,
            marginLeft: -$("#confirm-submit").outerWidth() / 2
        });
        $("#confirm-submit").show();

        return false;
    });

    $("#confirm-cancel").click(function(){
        $("#confirm-submit").hide();
        $("#dialog-createJj").show();
        return false;
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

	// 创建活动
	var selectTag = true;
	$("#confirmJj").click(function(){

        var lid = $('input[name="lid"]:checked').val();
        var playType = $('input[name="playType"]:checked').val();
        var startTime = $('input[name="startTime"]').val();
    	var endTime = $('input[name="endTime"]').val();
    	var ctype = $('input[name="ctype"]:checked').val();
        // 平台
        var platform = [];
        $('input[name="buyPlatform"]:checked').each(function(){ 
            platform.push($(this).val());  
        }); 
        var buyPlatform = platform.join(",");

        if(buyPlatform == ''){
            alert("加奖平台不能为空");
            return false;
        }

        // 获取加奖信息
        var plan = '';
        var getMin = '';
        var getMax = '';
        var getVal = '';
        var configArry = $('#config-table').find('tbody tr');
        for (var i = 1; i < configArry.length; i++) 
        {
            var tpl = '';
            getMin = $.trim($(configArry[i]).find('input[name="getMin"]').val());
            getMax = $.trim($(configArry[i]).find('input[name="getMax"]').val());
            getVal = $.trim($(configArry[i]).find('input[name="getVal"]').val());
     
            if(getMin == '' || getMax == '' || getVal == '')
            {
              alert("加奖内容为空");
              return false;
            }

            tpl = getMin + ',' + getMax + ',' + getVal;
            if(plan != '')
            {
              plan = plan + '|' + tpl;
            }else{
              plan = tpl;
            }
        
        };

    	if(selectTag){

    		selectTag = false;

    		$.ajax({
                type: 'post',
                url: '/backend/Activity/createJj',
                data: {lid:lid,playType:playType,startTime:startTime,endTime:endTime,ctype:ctype,plan:plan,buyPlatform:buyPlatform},

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
    
    // 投注栏配置
    $("#jzCast").click(function(){
        popdialog("dialog-jzCast");
        return false;
    });

    // 新增一行
    $('#tab-config').on('click', '.btn-white', function(){
        var tbody = $(this).parents('table').find('tbody');
        var innerTr = tbody.find('tr')[0].innerHTML;
        tbody.append('<tr>' + innerTr + '</tr>');
    })

    // 删除一行
    $('#tab-config').on('click', '.tab-radio-del', function(){
        $(this).parents('tr').remove();
    })

    // 投注栏配置取消
    $("#cancelCast").click(function(){
        $("#dialog-jzCast").hide();
        $(".pop-mask").hide();
        return false;
    });

    // 投注栏配置确认
    var castTag = true;
    $("#confirmCast").click(function(){

        var lname = $('input[name="lname"]:checked').val();
        var startTime = $('input[name="cstartTime"]').val();
        var endTime = $('input[name="cendTime"]').val();
        var slogan = $('input[name="slogan"]').val();

        // 玩法
        var playType = '';
        var playTypeArr = [];
        $('#dialog-jzCast input[name="playType"]:checked').each(function(){
            playTypeArr.push($(this).val());
        });
        playType = playTypeArr.join(",");

        var platform = 0;
        $('#dialog-jzCast input[name="buyPlatform"]:checked').each(function(){
            platform += parseInt($(this).val());
        });

        if(slogan == ''){
            alert("slogan内容不能为空");
            return false;
        }

        // 获取加奖信息
        var plan = '';
        var getMin = '';
        var getMax = '';
        var getVal = '';
        var configArry = $('#hover-table').find('tbody tr');

        for (var i = 1; i < configArry.length; i++) 
        {
            var tpl = '';
            getMin = $.trim($(configArry[i]).find('input[name="cgetMin"]').val());
            getMax = $.trim($(configArry[i]).find('input[name="cgetMax"]').val());
            getVal1 = $.trim($(configArry[i]).find('input[name="cgetVal1"]').val());
            getVal2 = $.trim($(configArry[i]).find('input[name="cgetVal2"]').val());
     
            if(getMin == '' || getMax == '' || getVal1 == '' || getVal2 == '')
            {
              alert("加奖内容为空");
              return false;
            }

            var reg = /^[0-9]\d*$/;

            if(!reg.test(getMin) || !reg.test(getVal1) || !reg.test(getVal2))
            {
                alert("加奖内容格式错误");
                return false;
            }
            if(getMin == '0' && isNaN(getMax) && getMax != '*')
            {
                alert("加奖内容格式错误");
                return false;
            }

            tpl = getMin + ',' + getMax + ',' + getVal1 + ',' + getVal2;
            if(plan != '')
            {
              plan = plan + '|' + tpl;
            }else{
              plan = tpl;
            }
        
        };

        if(castTag){

            castTag = false;

            $.ajax({
                type: 'post',
                url: '/backend/Activity/hoverJj',
                data: {lname:lname,startTime:startTime,endTime:endTime,slogan:slogan,plan:plan,platform:platform,playType:playType},

                success: function (response) {
                    var response = $.parseJSON(response);
                    if(response.status == 1)
                    {
                        castTag = true;
                        closePop();
                        alert(response.message);
                        window.location.reload();
                    }else{
                        castTag = true;
                        alert(response.message);
                    }
                },
                error: function () {
                    castTag = true;
                    alert('网络异常，请稍后再试');
                }
            });
        }

    });
    
    // hover切换
    $(".choseHover").click(function(){      
        if(!$(this).hasClass("current")){
            $(".choseHover").removeClass("current");
            $(this).addClass("current");
            var lname = $(this).val();
            $.ajax({
                type: 'post',
                url: '/backend/Activity/getJjHoverInfo',
                data: {lname:lname},

                success: function (response) {
                    var response = $.parseJSON(response);
                    if(response.status == 1){
                        $('input[name="cstartTime"]').val(response.data.startTime);
                        $('input[name="cendTime"]').val(response.data.endTime);
                        $('input[name="slogan"]').val(response.data.slogan);
                        // 替换平台
                        $('#dialog-jzCast input[name="buyPlatform"]').each(function(){
                            var plat = $(this).val();
                            if(plat & response.data.platform){
                                $(this).prop('checked', true);
                            }else{
                                $(this).prop('checked', false);
                            }
                        })
                        // 替换玩法
                        $('#dialog-jzCast input[name="playType"]').each(function(){
                            var play = $(this).val();
                            if(play == response.data.playType){
                                $(this).prop('checked', true);
                            }else{
                                $(this).prop('checked', false);
                            }
                        })
                        // 替换元素
                        var params = response.data.params;
                        $('#hover-table').find('tbody tr:gt(0)').remove();
                        for (var i = 0; i < params.length; i++) {
                            var tpl = '<tr><td><input type="text" class="ipt w60" value="' + params[i]['min'] + '" name="cgetMin">&lt; 奖金 ≤ <input type="text" class="ipt w60" value="' + params[i]['max'] + '" name="cgetMax"></td><td><input type="text" class="ipt w60" value="' + params[i]['dg'] + '" name="cgetVal1"></td><td><input type="text" class="ipt w60" value="' + params[i]['2c1'] + '" name="cgetVal2"><a href="javascript:;" class="tab-radio-del">×</a></td></tr>';
                            $('#hover-table').find('tbody').append(tpl);
                        }
                    }
                    else
                    {
                    	alert(response.message);
                    }
                },
                error: function () {
                    alert('网络异常，请稍后再试');
                }
            });
        }        
    });

});
</script>