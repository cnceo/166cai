<?php
$this->load->view("templates/head") ;
?>
<div class="path">您的位置：报表管理&nbsp;>&nbsp;<a href="">调账记录</a></div>

<div class="data-table-filter mt10" style="width:1300px">
  <form action="/backend/Transactions/list_adjust" method="get"  id="search_form">
  <table>
    <colgroup>
    	<col width="64" />
    	<col width="150" />
    	<col width="64" />
    	<col width="90" />
    	<col width="90" />
    	<col width="90" />
    	<col width="150" />
    </colgroup>
    <tbody>
    <tr>
      <th>用户名：</th>
      <td><input type="text" class="ipt w120"  name="uname" value="<?php echo $search['uname'] ?>"  placeholder="用户名..." /></td>
      <th>申请时间：</th>
      <td colspan="2">
        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <th>调账原因：</th>
      <td><input type="text" class="ipt w120"  name="comment" value="<?php echo $search['comment'] ?>" /></td>
    </tr>
    <tr>
      <th>订单状态：</th>
      <td>
        <select name="status">
        	<option value="all" <?php if (empty($search['status']) || $search['status'] === 'all') {?>selected<?php }?>>全部</option>
        	<option value="0" <?php if ($search['status'] === '0') {?>selected<?php }?>>待审核</option>
        	<option value="1" <?php if ($search['status'] === '1') {?>selected<?php }?>>审核通过</option>
        	<option value="2" <?php if ($search['status'] === '2') {?>selected<?php }?>>审核失败</option>
        </select>
      </td>
      <th>调账类型：</th>
      <td colspan="2">
      	<span class="ml8 mr35">
        <select name="type">
        	<option value="all" <?php if (empty($search['type']) || $search['type'] === 'all') {?>selected<?php }?>>全部</option>
        	<option value="0" <?php if ($search['type'] === '0') {?>selected<?php }?>>加款</option>
        	<option value="1" <?php if ($search['type'] === '1') {?>selected<?php }?>>扣款</option>
        </select>
        </span>
        <span class="ml8 mr8">金额类型：</span>
        <span class="ml8 mr8">
        	<select name="ismustcost">
	        	<option value="all" <?php if (empty($search['ismustcost']) || $search['ismustcost'] === 'all') {?>selected<?php }?>>全部</option>
	        	<option value="0" <?php if ($search['ismustcost'] === '0') {?>selected<?php }?>>可提现</option>
	        	<option value="1" <?php if ($search['ismustcost'] === '1') {?>selected<?php }?>>不可提现</option>
        	</select>
        </span>
      </td>
      <th>走成本库：</th>
      <td>
        <select name="iscapital">
	        <option value="all" <?php if (empty($search['iscapital']) || $search['iscapital'] === 'all') {?>selected<?php }?>>全部</option>
	        <option value="0" <?php if ($search['iscapital'] === '0') {?>selected<?php }?>>否</option>
	        <option value="1" <?php if ($search['iscapital'] === '1') {?>selected<?php }?>>是</option>
        </select>
      </td>
      <td><a href="javascript:void(0);" class="btn-blue " onclick="$('#search_form').submit();">查询</a></td>
    </tr>
    </tbody>
  </table>
  </form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="127"/>
      <col width="125"/>
      <col width="60"/>
      <col width="55"/>
      <col width="83"/>
      <col width="80"/>
      <col width="55"/>
      <col width="80"/>
      <col width="80"/>
      <col width="80"/>
      <col width="120"/>
      <col width="125"/>
      <col width="118"/>
      <col width="118"/>
    </colgroup>
    <thead>
        <tr>
            <td colspan="14">
                <div class="tal">
                    <strong>调账人数</strong><span><?php echo $count['ucount']?></span>
                    <strong class="ml20">调账笔数</strong><span><?php echo $count['count']?></span>
                    <strong class="ml20">待审核调账订单</strong><span><?php echo $count['dcount']?></span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr>
    	<th>调账订单编号</th>
	    <th>用户名</th>
	    <th>真实姓名</th>
	    <th>操作类型</th>
	    <th>操作金额(元)</th>
	    <th>账户余额(元)</th>
	    <th>金额类型</th>
	    <th>是否走成本库</th>
	    <th>账户明细类型</th>
	    <th>订单状态</th>
	    <th>调账原因</th>
	    <th>失败原因</th>
	    <th>申请时间</th>
	    <th>审核时间</th>
    </tr>
    <?php foreach($data as $key => $val): ?>
      <tr>
      <td><?php echo $val['num'] ?></td>
      <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $val['uid'] ?>" class="cBlue"><?php echo $val['uname'] ?></a></td>
      <td><?php echo $val['real_name'] ?></td>
      <td><?php echo $typeArr[$val['type']];?></td>
      <td><?php echo m_format($val['money']);?></td>
      <td><?php echo m_format($val['umoney']);?></td>
      <td><?php echo ($val['type'] == 1) ? '--' : $ismustcostArr[$val['ismustcost']];?></td>
      <td><?php echo $iscapitalArr[$val['iscapital']];?></td>
      <td><?php echo $ctypeArr[$val['ctype']];?></td>
      <td><?php echo $statusArr[$val['status']]; ?></td>
      <td><?php echo $val['comment'] ?></td>
      <td><?php echo $val['failreason'] ?></td>
      <td><?php echo $val['created'] ?></td>
      <td><?php echo ($val['status'] == 0) ? '' : $val['review_time'] ?></td>
    </tr>
    <?php endforeach; ?>
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
<div class="page mt10"><?php echo $pages[0] ?></div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
  $(".Wdate1").focus(function(){
    dataPicker();
  }); 
</script>
</body>
</html>