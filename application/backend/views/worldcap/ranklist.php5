<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">竞猜记录</a></div>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li><a href="/backend/WorldCup/index">活动概况</a></li>
            <li><a href="/backend/WorldCup/guessingRecord">竞猜记录</a></li>
            <li class="current"><a href="/backend/WorldCup/rankList">竞猜用户</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mt10">
        			<form action="/backend/WorldCup/ranklist" method="get" id="search_form">
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
                            <col width="200">
                            <col width="200">
                            <col width="150">
                            <col width="150">
                            <col width="200">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>用户名</th>
                                <th>活动主题</th>
                                <th>活动排名</th>
                                <th>累计猜中期次</th>
                                <th>累计中奖</th>
                                <th>累计猜中场次数</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $v){?>
                            <tr>
                                <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $v['uid']?>"><?php echo $v['uname']?></a></td>
                                <td><?php echo $v['name']?></td>
                                <td><?php echo $v['rank']?></td>
                                <td><?php echo $v['issue_num']?></td>
                                <td><?php echo ParseUnit( $v['bouns'],1)?></td>
                                <td><?php echo $v['match_num']?></td>
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