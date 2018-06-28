<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">竞猜记录</a></div>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li ><a href="/backend/WorldCup/">活动概况</a></li>
            <li class="current"><a href="/backend/WorldCup/guessingRecord">竞猜记录</a></li>
            <li><a href="/backend/WorldCup/rankList">竞猜用户</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mt10">
        			<form action="/backend/WorldCup/guessingRecord" method="get" id="search_form">
	          			<table>
                            <colgroup>
                                <col width="200">
                                <col width="200">
                                <col width="340">
                            </colgroup>
                            <tbody>
                            <tr>
                                <td>
                                    用户信息：
                                    <input type="text" class="ipt w108" name="uname"  value="<?php echo $search['uname'] ?>">
                                </td>
                                <td>
                                    活动主题：
                                    <select name="theme_id">
                                        <?php foreach($theme as $v){?>
                                            <option value="<?php echo $v['id']?>"  <?php echo $search['theme_id'] = $v['id']?>><?php echo $v['name']?></option>
                                        <?php } ?>
                                    </select>
                                </td>

                                <td>
                                    活动时间：
                                    <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
                                    <span class="ml8 mr8">至</span>
                                    <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
                                </td>

                            </tr>
                                <tr>
                                    <td>
                                        订单状态：
                                        <select name="status">
                                        	<option value="" <?php echo $search['status'] == '' ? 'selected' : ''?>>全部</option>
                                            <option value="0" <?php echo $search['status'] === '0' ? 'selected' : ''?>>等待开奖</option>
                                            <option value="1" <?php echo $search['status'] == 1 ? 'selected' : ''?>>未中奖</option>
                                            <option value="2" <?php echo $search['status'] == 2 ? 'selected' : ''?>>已中奖</option>
                                        </select>
                                    </td>

                                    <td>
                                        活动期次：
                                        <input type="text" class="ipt w108" name="issue"  value="<?php echo $search['issue'] ?>">
                                    </td>

                                    <td>
                                        竞猜平台：
                                        <select name="platform">
                                        	<option value="" <?php echo $search['platform'] == '' ? 'selected' : ''?>>全部</option>
                                            <option value="0" <?php echo $search['platform'] === '0' ? 'selected' : ''?>>网站</option>
                                            <option value="1" <?php echo $search['platform'] == 1 ? 'selected' : ''?>>APP</option>
                                            <option value="2" <?php echo $search['platform'] == 2 ? 'selected' : ''?>>iOS</option>
                                            <option value="3" <?php echo $search['platform'] == 3 ? 'selected' : ''?>>M版</option>
                                        </select>
                                    </td>

                                    <td>
                                        <a href="javascript:;" class="btn-blue" onclick="$('#search_form').submit();" target="_self">查询</a>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
          			</form>
        		</div>

        		<div class="data-table-list mt10">
                    <table>
                        <colgroup>
                            <col width="200">
                            <col width="100">
                            <col width="100">
                            <col width="150">
                            <col width="150">
                            <col width="100">

                        </colgroup>
                        <thead>
                            <tr>
                                <th>订单编号</th>
                                <th>活动主题</th>
                                <th>活动期次</th>
                                <th>用户名</th>
                                <th>竞猜时间</th>
                                <th>订单状态</th>
                                <th>中奖金额</th>
                                <th>竞猜平台</th>
                                <th>竞彩方案</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $v){?>
                            <tr>
                                <td><?php echo $v['orderId']?></td>
                                <td><?php echo $v['name']?></td>
                                <td><?php echo $v['issue']?></td>
                                <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $v['uid']?>"><?php echo $v['uname']?></a></td>
                                <td><?php echo $v['created']?></td>
                                <td>
                                    <?php
                                        if($v['status'] == 0){ echo '等待开奖';}
                                        if($v['status'] == 1){ echo '未中奖';}
                                        if($v['status'] == 2){ echo '已中奖';}
                                    ?>
                                </td>
                                <td><?php if($v['status'] == 2):?><?php echo number_format(ParseUnit($v['bouns'],1), 2)?><?php elseif ($v['status'] == 1):?>0.00<?php else :?>---<?php endif;?></td>
                                <td>
                                    <?php
                                        if(0 == $v['platform']){ echo '网站';}
                                        if(1 == $v['platform']){ echo 'APP';}
                                        if(2 == $v['platform']){ echo 'iOS';}
                                        if(3 == $v['platform']){ echo 'M版';}
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    //场次
                                    $code = array(
                                        '1' => 'code1',
                                        '2' => 'code2',
                                        '3' => 'code3',
                                        '4' => 'code4'
                                    );
                                    //结果
                                    $spf = array(
                                             '3' => '胜',
                                             '1' => '平',
                                             '0' => '负',
                                    );
                                    $plan = json_decode($v['plan'],true);
                                    $str = '';
                                    $spf_str = '';
                                    foreach ($plan as $k1=>$v1){
                                        if($v[$code[$k1]] == 0){
                                            $spf_str =  '客胜';
                                        }
                                        if($v[$code[$k1]] == 1){
                                            $spf_str =  '平';
                                        }
                                        if($v[$code[$k1]] == 3){
                                            $spf_str =  '主胜';
                                        }
                                        $str .="<p>".$k1.":".$v1['mid']."   ".$v1['home']."  VS  ".$v1['away']."   <span style=\'float: right;color:blue;\'>".$spf_str."</span></p>";
                                    }
                                    ?>
                                    <a href="javascript:void ;" onclick="getPlan('<?php echo $str?>')">查看详情</a>
                                </td>
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

<style>
    #conentPlan p{ line-height: 30px; font-size: 16px;}
</style>
<!--计划详情-->
<div class="pop-mask" style="display:none"></div>
    <div class="pop-dialog" id="planPop">
        <div class="pop-in">
            <div class="pop-head">
                <h2>查看详情</h2>
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
               if(res.code == '10020'){
                   alert(res.msg);
               }else{
                   alert(res.msg);
                   location.reload();
               }
           }
       });
    });

</script>