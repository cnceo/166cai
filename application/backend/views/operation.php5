<?php
$this->load->view("templates/head") ;
$platforms = array(
    '1' => '网页',
    '2' => 'Android',
    '3' => 'IOS',
    '4' => 'M版'
);
?>
<div class="path">您的位置：审核管理&nbsp;&gt;&nbsp;<a href="">用户反馈</a></div>
<div class="data-table-filter mt10" style="width:956px">
 <form action="/backend/Operation/" method="get"  id="search_form">
  <table style = "width : 1160px;">
    <colgroup>
      <col width="62" />
      <col width="140" />
      <col width="62" />
      <col width="400" />
      <col width="62" />
      <col width="232" />
    </colgroup>
    <tbody>
    <tr>
      <th>用户名：</th>
      <td>
          <input type="text" class="ipt w130"  name="name" value="<?php echo $search['name'] ?>"  placeholder="" />
      </td>
      <th>反馈时间：</th>
      <td>
        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <th>反馈类型：</th>
      <td>
        <select class="selectList w222"  name="type">
            <option value="">全部</option>
            <?php foreach ($this->o_type as $key => $type): ?>
              <option value="<?php echo $key ?>" <?php if($search['type'] === "{$key}"): echo "selected"; endif;    ?>><?php echo $type; ?></option>   
            <?php endforeach; ?>
        </select>
      </td>
        <th class = "tar">反馈平台：</th>
        <td>
            <select class="selectList w98" id="platformId" name="platform">
                <option value="">不限</option>
                <?php foreach ($platforms as $key => $val):?>
                    <option value="<?php echo $key;?>"
                        <?php if($key == ($search['platform'] + 1) ): echo "selected"; endif;?>><?php echo $val;?></option>
                <?php endforeach;?>
            </select>
        </td>
    </tr>
    <tr>
    <th>回复人：</th>
      <td>
          <input type="text" class="ipt w130"  name="reply_name" value="<?php echo $search['reply_name'] ?>"  placeholder="" />
      </td>
      <th>回复时间：</th>
      <td>
          <span class="ipt ipt-date w184"><input type="text" name='reply_s_time' value="<?php echo $search['reply_s_time'] ?>" class="Wdate1" /><i></i></span>
          <span class="ml8 mr8">至</span>
          <span class="ipt ipt-date w184"><input type="text" name='reply_e_time' value="<?php echo $search['reply_e_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <th>反馈内容：</th>
      <td>
          <input type="text" class="ipt w222"  name="content" value="<?php echo $search['content'] ?>"  placeholder="" />
      </td>
        <td>
            <a href="javascript:void(0);" class="btn-blue fr"  onclick="$('#search_form').submit();">查询</a>
        </td>
    </tr>

    </tbody>
  </table>
  </form>
</div>

<div class="data-table-list table-tb-border del-percent mt10">
  <table id="Table">
    <colgroup>
      <col width="100" />
      <col width="210" />
      <col width="150" />
      <col width="100" />
      <col width="150" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
    </colgroup>
      <thead>
      <tr>
        <th>用户名</th>
        <th>反馈内容</th>
        <th>反馈时间</th>
        <th>反馈类型</th>
        <th>回复时间</th>
        <th>回复人</th>
        <th>反馈平台</th>
        <th>操作</th>
        <th>手机型号</th>
        <th>系统版本</th>
        <th>软件版本</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach($opes as $key => $op):?>
      <tr>
       <td><a href="/backend/User/user_manage/?uid=<?php echo $op['uid']; ?>" class="cBlue" target="_blank"><?php echo $op['name'] ?></a></td>
       <td><div class="text-overflow w390"><?php echo htmlentities($op['content'], ENT_QUOTES, "UTF-8");?></div></td>
       <td><?php echo $op['created'] ?></td>
       <td><?php echo $this->o_type[$op['type']]?></td>
       <td><?php echo $op['rcreated'] ?></td>
       <td><?php echo $op['rname'] ?></td>
       <td><?php echo ($op['platform'] == 0) ? "网页" : ($op['platform'] == 1 ? "Android" : ($op['platform'] == 2 ? "IOS" : "M版")); ?></td>
       <td><a href="/backend/Operation/detail?id=<?php echo $op['id'] ?>" class="cBlue mr10">查看</a></td>
       <td><?php echo $op['model']; ?></td>
       <td><?php echo $op['system']; ?></td>
       <td><?php echo $op['version']; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="7">
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
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tablesorter.css?v=7">
<script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery.tablesorter.js"></script>
<style>
    .data-table-list thead tr { border: 1px solid #e5e5e5;}
    .data-table-list thead th { border:none !important}
</style>
<script>
$(function(){
    $(".Wdate1").focus(function(){
        dataPicker();
    });
    $('#Table').tablesorter(
        {headers:{0:{sorter:false},1:{sorter:false},3:{sorter:false},5:{sorter:false},6:{sorter:false},7:{sorter:false},8:{sorter:false},9:{sorter:false},10:{sorter:false}},cssHeader: "border:1px;"}
    );
});

</script>
</body>
</html>
