<?php 
	$this->load->view("templates/head");
	$status = array(
		'0' => '在售',
		'1' => '淘汰',
		'2' => '夺冠',
		'3' => '停售',
	);
?>
<div class="path">您的位置：数据中心&nbsp;&gt;&nbsp;<a href="/backend/Match/">对阵管理</a></div>
<div class="mod-tab mt20">
  <div class="mod-tab-hd">
    <ul>
      <li><a href="/backend/Match/">北京单场</a></li>
      <li><a href="/backend/Match/bdsfgg">北单胜负过关</a></li>
      <li><a href="/backend/Match/tczq">老足彩</a></li>
      <li><a href="/backend/Match/jczq">竞彩足球</a></li>
      <li><a href="/backend/Match/jclq">竞彩篮球</a></li>
      <li class="current"><a href="/backend/Match/gj">冠军彩</a></li>
      <li><a href="/backend/Match/gyj">冠亚军</a></li>
    </ul>
  </div>

          <div>
        <div class="data-table-filter">
          <table>
            <tbody>
            <tr>
              <td>
              <form action="/backend/Match/gj" method="get"  id="search_form">
                期次编号：
                <select class="selectList w130" id="issue" name="issue" onchange="$('#search_form').submit();">
                  <?php foreach ($issues as $val): ?>
                  <option value="<?php echo $val;?>" <?php if($issue === "{$val}"): echo "selected"; endif;   ?>><?php echo $val;?></option>
                  <?php endforeach; ?>
                </select>
                </form>
              </td> 
            </tr>
            </tbody>
          </table>
        </div>
        <div class="data-table-list mt10">
          <table id="tczqTable">
            <colgroup>
              <col width="8%" />
              <col width="12%" />
              <col width="10%" />
              <col width="10%" />
            </colgroup>
            <thead>
              <tr>
                <th>编号</th>
                <th>球队名</th>
                <th>赔率</th>
                <th>赛事状态</th>
              </tr>
            </thead>
            <tbody>
            <?php
            ?>
              <?php foreach ($result as $row):?>
              <tr id="<?php echo $row['mid'];?>">
               <td><?php echo $row['mid'];?></td>
               <td><?php echo $row['name'];?></td>
               <td><?php echo $row['odds'];?></td>
               <td><?php echo $status[$row['status']];?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
        <div class="pop-mask" style="display:none;width:200%"></div>
<script>
$(function(){
	$("tr").click(function(){
		$("tr").removeClass("select");
		$(this).addClass("select");
        $("#selectId").val($(this).attr("id"));
	});
})
</script>
  </div>
</div>
</body>
</html>