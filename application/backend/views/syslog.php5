<?php $this->load->view("templates/head") ?>
<?php date_default_timezone_set('Asia/Shanghai');?>
  <div class="path">您的位置：系统管理&nbsp;&gt;&nbsp;<a href="">系统日志</a></div>
<div class="wbase mt10">
  <div class="data-table-filter" style="width:1050px">
      <form action="/backend/Syslog/" method="get" id="search_form">
    <table>
      <colgroup>
        <col width="62" />
        <col width="232" />
        <col width="62" />
        <col width="232" />
        <col width="62" />
        <col width="400" />
      </colgroup>
      <tbody>
      <tr>
        <th>操作内容：</th>
        <td>
            <input type="text" class="ipt w222" placeholder="请输入关键字"  name="mark" value="<?php echo $search['mark'] ?>"/>
        </td>
        <th>操作模块：</th>
        <td>
          <select class="selectList w222"  name="lmod">
              <option value="">全部</option>
          <?php  foreach($mods as $key => $mod): ?>
              <option value="<?php echo $key; ?>" <?php if($search['lmod'] === "{$key}"): echo "selected"; endif; ?>><?php echo $mod; ?></option>
          <?php endforeach; ?>
          </select>
        </td>
        <th>操作时间：</th>
        <td>
         <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
          <span class="ml8 mr8">至</span>
         <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>"  class="Wdate1" /><i></i></span>
        </td>
      </tr>
      <tr>
        <th>操作人：</th>
        <td>
            <input type="text" class="ipt w222"   name="name" value="<?php echo $search['name'] ?>"/>
        </td>
        <td colspan="4" class="tar">
          <a href="javascript:void(0);" class="btn-blue mr35" onclick="$('#search_form').submit();">查询</a>
        </td>
      </tr>
      </tbody>
    </table>
      </form>
  </div>
  <div class="data-table-list table-tb-border del-percent mt10">
    <table>
      <colgroup>
        <col width="220" />
        <col width="820" />
        <col width="150" />
        <col width="100" />
      </colgroup>
      <tbody>
        <tr>
          <th>操作模块</th>
          <th>操作内容</th>
          <th>操作时间</th>
          <th>操作人</th>
        </tr>
        <?php foreach ($logs as $key => $log):?>
        <tr>
         <td><div class="tal pl15"><?php  echo $mods[$log['lmod']] ?></div></td>
         <td><div class="tal pl15"><?php  echo $log['mark'] ?></div></td>
         <td><?php  echo date("Y-m-d H:i:s",$log['addTime']); ?></td>
         <td><?php  echo $log['userName'] ?></td>
        </tr>
        <?php endforeach; ?>
   
      </tbody>
      <tfoot>
        <tr>
          <td colspan="14">
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