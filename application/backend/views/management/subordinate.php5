<!--表单筛选 begin-->
  <div class="filter-oper">
  <form action="/backend/Order/rebateDetailList" method="get"  id="sch_form">
    <table class="data-table-filter">
      <colgroup>
	<col width="280">
	<col width="100">
	<col width="100">
	<col width="100">
      </colgroup>
      <tbody>
	<tr>
	  <td>
	   申请时间：<span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
	  </td>
	  <td>
	    <input type="text" class="ipt w95" name="userName" value="<?php echo $search['userName'] ?>" placeholder="用户名">
	  </td>
	  <td>
	    <input type="hidden" name="fromType" value="<?php echo $fromType  ?>"  id="fromType" />
	  	<input type="hidden" name="id" value="<?php echo $rebate['id']; ?>"/>
	    <a href="javascript:;" class="btn-blue" onclick="$('#sch_form').submit();">查询</a>
	  </td>
	</tr>
      </tbody>
    </table>
</form>
      <!--表单筛选 end-->
      <!--表格 begin-->
      <table class="data-table-list">
	<colgroup>
	    <col width="110">
	    <col width="140">
	    <col width="220">
	    <col width="140">
	    <col width="200">
	    <col width="170">
	    <col width="170">
	    <col width="170">
	</colgroup>
	<thead>
	  <tr>
	    <th>代理编号</th>
	    <th>用户名</th>
	    <th>真实姓名</th>
	    <th>手机号码</th>
	    <th>申请时间</th>
	    <th>用户销量(元)</th>
	    <th>累积收入(元)</th>
	    <th>详情</th>
	  </tr>
	</thead>
	<tbody>
	  <?php foreach($lists as $key => $value): ?>
	  <tr>
	    <td><?php echo $value['id'];?></td>
	    <td><a class="cBlue" href="/backend/User/user_manage/?uid=<?php  echo $value['uid'];?>"><?php echo $value['uname'];?></a></td>
	    <td><?php echo $value['real_name'];?></td>
	    <td><?php echo $value['phone'];?></td>
	    <td><?php echo $value['created'];?></td>
	    <td><?php echo m_format($value['total_sale']);?></td>
	    <td><?php echo m_format($value['total_income']);?></td>
	    <td><a class="cBlue" href="/backend/Management/rebateDetail?id=<?php  echo $value['id'];?>">查看</a></td>
	  </tr>
	  <?php endforeach;?>
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
      <!--表格 end-->
</div>
<div class="page mt10 abnomal">
   <?php echo $pages[0] ?>
</div>
<script>
$(function(){
	$('.abnomal a').click(function(){
        if($("#fromType").val() == "ajax")
        {
            var _this = $(this);
            $("#subordinate").load(_this.attr("href"));
            return false;
        }
        return true;
    });
	$('#sch_form').submit(function(){
        if($("#fromType").val() == "ajax")
        {
            $("#subordinate").load("/backend/Management/subordinate?"+$("#sch_form").serialize());
            return false;
        }
        return true;
    });
    $(".Wdate1").focus(function(){
        dataPicker();
    });
});
</script>