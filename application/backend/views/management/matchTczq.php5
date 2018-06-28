<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/manageMatch/">对阵管理</a></div>
<div class="mod-tab mt20">
  <div class="mod-tab-hd">
    <ul>
      <li><a href="/backend/Management/manageMatch/?type=bjdc">北京单场</a></li>
      <li><a href="/backend/Management/manageMatch/?type=bdsfgg">北单胜负过关</a></li>
      <li class="current"><a href="/backend/Management/manageMatch/?type=tczq">老足彩</a></li>
      <li><a href="/backend/Management/manageMatch/?type=jczq">竞彩足球</a></li>
      <li><a href="/backend/Management/manageMatch/?type=jclq">竞彩篮球</a></li>
    </ul>
  </div>
  <div>
        <div class="data-table-filter">
          <table>
            <tbody>
            <tr>
              <td colspan="9">
              <form action="/backend/Management/manageMatch/?type=tczq" method="post"  id="search_form">
              对阵类型：
                <select class="selectList w184" id="ctype" name="ctype" onchange="$('#search_form').submit();">
                  <?php foreach ($ctypes as $ctype => $val): ?>
                  <option value="<?php echo $ctype;?>" <?php if($search['ctype'] === "{$ctype}"): echo "selected"; endif;   ?>><?php echo $val;?></option>
                  <?php endforeach; ?>
                </select>
                期次编号：
                
                <select class="selectList w130" id="mid" name="mid" onchange="$('#search_form').submit();">
                  <?php foreach ($mids as $mid): ?>
                  <option value="<?php echo $mid;?>" <?php if($search['mid'] === "{$mid}"): echo "selected"; endif;   ?>><?php echo $mid;?></option>
                  <?php endforeach; ?>
                </select>
                <span style="margin-left: 120px;">
                <input type="hidden" name="selectId" id="selectId" value="" />
                </span>
                </form>
              </td> 
            </tr>
            </tbody>
          </table>
        </div>
        <?php if($search['ctype'] == 1):?>
        <div class="data-table-list mt10">
          <table>
            <colgroup>
              <col width="12%" />
              <col width="12%" />
              <col width="14%" />
              <col width="14%" />
              <col width="12%" />
              <col width="12%" />
              <col width="12%" />
              <col width="12%" />
            </colgroup>
            <thead>
              <tr>
                <th>场次</th>
                <th>赛事</th>
                <th>主队</th>
                <th>客队</th>
                <th>比赛时间</th>
                <th>半场比分</th>
                <th>全场比分</th>
                <th>赛果</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($result as $row):?>
              <tr id="<?php echo $row['id'];?>">
               <td><?php echo $row['mname'];?></td>
               <td><?php echo $row['league'];?></td>
               <td><?php echo $row['home'];?></td>
               <td><?php echo $row['away'];?></td>
               <td><?php echo $row['begin_date'];?></td>
               <td><?php if($row['status'] != 51): echo $row['half_score']; else: echo '----'; endif;?></td>
               <td><?php if($row['status'] != 51): echo $row['full_score']; else: echo '----'; endif;?></td>
               <td><?php if($row['status'] != 51): echo $row['result1']; else: echo '----'; endif;?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
        <div class="pop-mask" style="display:none;width:200%"></div>

        <?php elseif ($search['ctype'] == 2):?>
        <div class="data-table-list mt10">
          <table>
            <colgroup>
              <col width="8%" />
              <col width="12%" />
              <col width="12%" />
              <col width="12%" />
              <col width="14%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
            </colgroup>
            <thead>
              <tr>
                <th>场次</th>
                <th>赛事</th>
                <th>主队</th>
                <th>客队</th>
                <th>比赛时间</th>
                <th>半场比分</th>
                <th>全场比分</th>
                <th>赛果</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($result as $row):?>
              <tr id="<?php echo $row['id'];?>">
               <td><?php echo $row['mname'];?></td>
               <td><?php echo $row['league'];?></td>
               <td><?php echo $row['home'];?></td>
               <td><?php echo $row['away'];?></td>
               <td><?php echo $row['begin_date'];?></td>
               <td><?php if($row['status'] != 51): echo $row['half_score']; else: echo '----'; endif;?></td>
               <td><?php if($row['status'] != 51): echo $row['full_score']; else: echo '----'; endif;?></td>
               <td><div style="border-bottom: 1px dashed #ccc;"><?php if($row['status'] != 51): echo $row['result1']; else: echo '----'; endif;?></div>
                <div><?php if($row['status'] != 51): echo $row['result2']; else: echo '----'; endif;?></div></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
        <div class="pop-mask" style="display:none;width:200%"></div>

        <?php else:?>
        <div class="data-table-list mt10">
          <table>
            <colgroup>
              <col width="10%" />
              <col width="12%" />
              <col width="12%" />
              <col width="12%" />
              <col width="12%" />
              <col width="12%" />
            </colgroup>
            <thead>
              <tr>
                <th>场次</th>
                <th>赛事</th>
                <th>对阵</th>
                <th>比赛时间</th>
                <th>全场比分</th>
                <th>赛果</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($result as $row):?>
              <tr id="<?php echo $row['id'];?>">
               <td><?php echo $row['mname'];?></td>
               <td><?php echo $row['league'];?></td>
               <td>
               	  <div style="border-bottom: 1px dashed #ccc;"><?php echo $row['home'];?></div>
                  <div><?php echo $row['away'];?></div>
               </td>
               <td><?php echo $row['begin_date'];?></td>
               <td><?php if($row['status'] != 51): echo $row['full_score']; else: echo '----'; endif;?></td>
               <td>
               	  <div style="border-bottom: 1px dashed #ccc;"><?php if($row['status'] != 51): echo $row['result1']; else: echo '----'; endif;?></div>
                  <div><?php if($row['status'] != 51): echo $row['result2']; else: echo '----'; endif;?></div>
               </td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
<script>
$(function(){
	$("tr").click(function(){
		$("tr").removeClass("select");
		$(this).addClass("select");
		$("#selectId").val($(this).attr("id"));
	});

	function alertPop(content){
		$("#alertBody").html(content);
		popdialog("alertPop");
	}
})
</script>
        <?php endif;?>
  </div>
</div>
</body>
</html>