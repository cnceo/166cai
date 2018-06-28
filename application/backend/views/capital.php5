<?php
$this->load->view("templates/head") ;
?>
<div class="path">您的位置：报表管理&nbsp;>&nbsp;<a href="">网站成本明细</a></div>

<div class="data-table-filter mt10" style="width:1100px">
  <form action="/backend/Transactions/list_capital" method="get"  id="search_form">
  <table>
    <colgroup><col width="62" /><col width="130" /><col width="62" /><col width="400" /><col width="62" /><col width="280" /></colgroup>
    <tbody>
    <tr>
      <th>用户名：</th>
      <td><input type="text" class="ipt w120"  name="uname" value="<?php echo $search['uname'] ?>"  placeholder="用户名..." /></td>
      <th>交易编号：</th>
      <td><input type="text" class="ipt w222"  name="trade_no" value="<?php echo $search['trade_no'] ?>"/></td>
      <th>交易说明：</th>
      <td><input type="text" class="ipt w360"  name="content" value="<?php echo $search['content'] ?>"/></td>
    </tr>
    <tr>
      <th>订单类型：</th>
      <td>
        <select class="selectList w120"  name="ctype">
            <option value="">全部</option>
             <?php foreach ($ctypeArr as $key => $ctype): ?>
             <option value="<?php echo $key; ?>" <?php if($search['ctype'] === "{$key}"): echo "selected"; endif;   ?>><?php echo $ctype?></option>  
             <?php endforeach; ?>
        </select>
      </td>
      <th>交易金额：</th>
      <td>
        <input type="text" class="ipt w120" name="start_money" value='<?php echo $search['start_money'] ?>'/>
        <span class="ml8 mr8">至</span>
        <input type="text" class="ipt w120" name="end_money" value='<?php echo $search['end_money'] ?>'/>
      </td>
      <th>创建时间：</th>
      <td>
        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
        <a href="javascript:void(0);" class="btn-blue mb10" onclick="$('#search_form').submit();">查询</a>
      </td>
    </tr>
    </tbody>
  </table>
  </form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="140"/><col width="140"/><col width="140"/><col width="140"/><col width="140"/><col width="140"/><col width="140"/><col width="140"/><col width="140"/>
    </colgroup>
    <thead>
        <tr>
            <td colspan="8">
                <div class="tal">
                    <strong>支出总额</strong>
                    <span><?php echo $count['out'] > 0 ? (jine_format(substr($count['out'], 0, -2)).".".substr($count['out'], -2)) : '0.00'; ?></span>
                    <strong class="ml20">收入总额</strong>
                    <span><?php echo $count['in'] > 0 ? (jine_format(substr($count['in'], 0, -2)).".".substr($count['in'], -2)) : '0.00'; ?></span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr><th>交易编号</th><th>用户名</th><th>真实姓名</th><th>交易类型</th><th>支出（元）</th><th>收入（元）</th><th>交易时间</th><th>交易说明</th></tr>
    <?php foreach($data as $key => $val): ?>
      <tr>
      <td><?php echo $val['trade_no'] ?></td>
      <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $val['uid'] ?>" class="cBlue"><?php echo $val['uname'] ?></a></td>
      <td><?php echo $val['real_name'] ?></td>
      <td><?php echo $ctypeArr[$val['ctype']];?></td>
      <td><?php echo ($val['status'] == 2) ? m_format($val['money']) : '--'?></td>
      <td><?php echo ($val['status'] == 1) ? m_format($val['money']) : '--'?></td>
      <td><?php echo $val['created'] ?></td>
      <td><?php echo $val['content'] ?></td>
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
<div class="page mt10">
   <?php echo $pages[0] ?>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
       $(".Wdate1").focus(function(){
            dataPicker();
        });
    });
</script>
</body>
</html>
<?php 
function jine_format($str) {
	$num = strlen($str) % 3;
	$sl = substr($str, 0, $num);
	$sr = substr($str, $num);
	$arr = array_filter(array_merge(array($sl), str_split($sr, 3)));
	return implode(',', $arr);
}
 ?>