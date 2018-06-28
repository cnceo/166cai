<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">竞彩虚拟投注</a></div>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li class="current"><a href="/backend/WorldCup/index">活动概况</a></li>
            <li><a href="/backend/WorldCup/guessingRecord">竞猜记录</a></li>
            <li><a href="/backend/WorldCup/rankList">竞猜用户</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mt10">
        			<form action="/backend/WorldCup/index" method="get" id="search_form">
	          			<table>
                            <colgroup>
                                <col width="200">
                                <col width="200">
                                <col width="340">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td>
                                        活动主题：
                                        <select name="theme_id">
                                            <?php foreach($theme as $v){?>
                                                <option value="<?php echo $v['id']?>"  <?php echo $search['theme_id'] = $v['id']?>><?php echo $v['name']?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        活动期次：
                                        <input type="text" class="ipt w108" name="issue"  value="<?php echo $search['issue'] ?>">
                                    </td>
                                    <td>
                                        活动时间：
                                        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
                                        <span class="ml8 mr8">至</span>
                                        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
                                    </td>
                                    <td>
                                        <a href="javascript:;" class="btn-blue" onclick="$('#search_form').submit();" target="_self">查询</a>
                                    </td>
                                    <td style="padding: 0 10px;">
                                        <a href="javascript:;" class="btn-blue" onclick="addIssue()">新建期次</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
          			</form>
        		</div>

        		<div class="data-table-list mt10">
                    <table>
                        <colgroup>
                            <col width="100">
                            <col width="100">
                            <col width="200">
                            <col width="200">

                        </colgroup>
                        <thead>
                            <tr>
                                <th>活动主题</th>
                                <th>活动期次</th>
                                <th>活动开始时间</th>
                                <th>活动结束时间</th>
                                <th>当期奖金</th>
                                <th>竞猜场次</th>
                                <th>用户人数</th>
                                <th>中奖人数</th>
                                <th>奖金</th>
                                <th>期次状态</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($list as $v){?>
                            <tr>
                                <td><?php echo $v['name']?></td>
                                <td><?php echo $v['issue']?></td>
                                <td><?php echo $v['start_time']?></td>
                                <td><?php echo $v['end_time']?></td>
                                <td><?php echo ParseUnit($v['money'],1)?>元</td>
                                <td style="color: blue;">
                                    <?php
                                        $plan = json_decode($v['plan'],true);
                                        $str = '';
                                        foreach ($plan as $k1=>$v1){
                                            $str .="<p>".$k1.":".$v1['mid']."   ".$v1['home']."  VS  ".$v1['away']."</p>";
                                        }
                                    ?>
                                    <span onclick="getPlan('<?php echo $str?>')">
                                        <?php   echo count($plan);?>场
                                    </span>
                                </td>
                                <td><?php echo $v['status']!= 0  ? $v['join_num'] : '---';?></td>
                                <td><?php echo $v['status']== 3  ? $v['bouns_num'] : '---';?></td>
                                <td><?php echo $v['status']== 3  ? ParseUnit($v['bouns'],1) : '---';?></td>
                                <td>
                                    <?php  if($v['status'] == 0 ){ echo '未开始';}?>
                                    <?php  if($v['status'] == 1 ){ echo '竞猜进行中';}?>
                                    <?php  if($v['status'] == 2 ){ echo '等待开奖';}?>
                                    <?php  if($v['status'] == 3 ){ echo '已派奖';}?>
                            </tr>
                        <?php } ?>
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
</div>

<!--添加和修改弹窗-->
<div class="pop-mask" style="display:none"></div>
<form id='updateForm' method='post' action=''>
    <div class="pop-dialog" id="updatePop">
        <div class="pop-in">
            <div class="pop-head">
                <h2>新增竞猜活动期次</h2>
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
                            <tr>
                                <td>竞猜名称：</td>
                                <td>
                                    <select name="theme_id"  class="ipt w184">
                                        <?php foreach($theme as $v){?>
                                            <option value="<?php echo $v['id']?>"><?php echo $v['name']?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>活动期次</td>
                                <td><?php echo $max_issue['max_issue'] + 1;?><input type="hidden"  name="issue" value="<?php echo $max_issue['max_issue'] + 1;?>"></td>
                            </tr>
                            <tr>
                                <td>当期奖金</td>
                                <td><input type="text" id="money" name="money" class="ipt w184"></td>
                            </tr>

                            <tr id="tr1">
                                <td>竞猜场次</td>
                                <td>
                                    <div id="moreGuest">
                                        <p>
                                            <input type="text" name="plan[]" class="ipt w184">
                                            <select name ="wf"  disabled>
                                                <option value="sfp" >胜负平</option>
                                            </select>
                                            <span style="padding: 0 10px;" onclick="addOneGuest()">【添加一场】</span>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>活动时间</td>
                                <td>
                                    <span class="ipt ipt-date w184"><input type="text" name='start_time' id="start_time"  class="Wdate1" /><i></i></span>
                                    <span class="ml8 mr8">至</span>
                                    <span class="ipt ipt-date w184"><input type="text" name='end_time' id="end_time"  class="Wdate1" /><i></i></span>
                                </td>
                            </tr>
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
</form>
<style>
    #conentPlan p{ line-height: 30px; font-size: 16px;}
</style>
<!--计划详情-->
<div class="pop-mask" style="display:none"></div>
    <div class="pop-dialog" id="planPop">
        <div class="pop-in">
            <div class="pop-head">
                <h2>涉及场次</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
                <div class="data-table-filter del-percent" id="conentPlan">

                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-b-white mlr15 pop-cancel">确定</a>
            </div>
        </div>
    </div>


<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(".Wdate1").focus(function(){
        dataPicker();
    });

    function addIssue() {
        popdialog("updatePop");
    }

    function getPlan(content){
        //根据ID信息查询场次信息
        $("#conentPlan").html(content)
        popdialog("planPop");
    }

    /**
     * 添加一行
     * @param obj
     */
    function addOneGuest(){
      var html = ' <p style="padding: 10px 0;"><input type="text" name="plan[]" class="ipt w184"> <select name ="wf"  disabled><option value="sfp" >胜负平</option></select></p>';
      $("#moreGuest").append(html);
    }

    $("#updateSubmit").click(function () {
       $.ajax({
           type: 'post',
           url : '/backend/WorldCup/addConfig',
           data: $("#updateForm").serialize(),
           dataType:'json',
           success:function(res){
               if(res.status == '10020' || res.status == 'n'){
                   alert(res.message);
               }else{
                   alert(res.message);
                   location.reload();
               }
           }
       });
    });

</script>