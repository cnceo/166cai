<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="">公告管理</a></div>
<div class="data-table-filter mt10" style="width:1050px">
 <form action="/backend/Notice/" method="get"  id="search_form">
  <table>
    <colgroup>
      <col width="62" />
      <col width="242" />
      <col width="62" />
      <col width="180" />
      <col width="62" />
      <col width="400" />
    </colgroup>
    <tbody>
    <tr>
      <th>标题：</th>
      <td>
          <input type="text" class="ipt w222"  name="title" value="<?php echo $search['title'] ?>"  placeholder="" />
      </td>
      <th>是否显示：</th>
      <td>
          <label for="isshow"><input id="isshow" name="isshow" value='1' type="checkbox" class="ckbox"  <?php if($search['isshow'] === '1'): echo "checked"; endif;    ?> />是</label>
              <label for="ishide"><input id="ishide" name="ishide" value='0' type="checkbox" class="ckbox"  <?php if($search['ishide'] === '0'): echo "checked"; endif;    ?> />否</label>
              </td>
      <th>创建时间：</th>
      <td>
        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
    </tr>
    <tr>
    <th>提交人：</th>
      <td>
          <input type="text" class="ipt w222"  name="name" value="<?php echo $search['name'] ?>"  placeholder="" />
      </td>
     <th>来源：</th>
      <td>
        <select class="selectList w150"  name="source">
            <option value="">全部</option>
            <option value="1" <?php if($search['source'] === '1'): echo "selected"; endif;   ?>>2345</option>
            <option value="2" <?php if($search['source'] === '2'): echo "selected"; endif;   ?>>合作方</option>
        </select>
      </td>
      <th>分类：</th>
      <td>
          <select class="selectList w150"  name="category">
              <option value="">全部</option>
                 <?php foreach($this->category as $key => $value): ?>
                      <option value="<?php echo $key;?>" <?php if($search['category'] === "{$key}"): echo "selected"; endif;?>><?php echo $value; ?></option> 
                <?php endforeach; ?>
          </select>
      </td>
     <td>
        <a href="javascript:void(0);" class="btn-blue " onclick="$('#search_form').submit();">查询</a>
      </td>
      <th></th>
        <td></td>
    </tr>

    </tbody>
  </table>
  </form>
</div>

<div class="tal mt20">
  <a href="/backend/Notice/add_update/" class="btn-blue">新建公告</a>
</div>
<div class="data-table-list table-tb-border del-percent mt10">
  <table>
    <colgroup>
      <col width="380" />
      <col width="390" />
      <col width="80" />
      <col width="80" />
      <col width="100" />
      <col width="80" />
      <col width="100" />
       <col width="150" />
    </colgroup>
    <tbody>
      <tr>
        <th>公告标题</th>
        <th>公告时间</th>
        <th>置顶</th>
        <th>权重</th>
        <th>分类</th>
        <th>提交人</th>
        <th>是否显示</th>
        <th>操作</th>
      </tr>
      <?php foreach($notices as $key => $notice):  ?>
      <tr>
       <td><?php echo $notice['title'] ?></td>
       <td><?php echo date("Y-m-d H:i:s",$notice['addTime']) ?></td>
       <td><?php echo $notice['isTop'] == 0 ? "否":"是"; ?></td>
       <td><?php echo $notice['weight']?></td>
       <td><?php echo $this->category[$notice['category']] ?></td>
       <td><?php echo $notice['username'] ?></td>
       <td><?php echo $notice['status']; ?></td>
       <td><a href="/backend/Notice/add_update/?id=<?php echo $notice['id'] ?>" class="cBlue mr10">编辑</a>
           <a href="/backend/Notice/notice_view/?id=<?php echo $notice['id']?>" class="cBlue" target="_blank">预览</a>
           <a href="/backend/Notice/setTop/?id=<?php echo $notice['id'] ?>" class="cBlue mr10 setTop">置顶</a>
       </td>
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
    
    $(".setTop").bind('click',function(){
        var _this = $(this);
        $.ajax({
            type: "get",
            url: _this.attr("href"),
            success: function(data){
                data = JSON.parse(data);
                if(data.status == 'y'){
                    var isTop = data.info.isTop == 1 ? "是" : "否";
                    _this.parent().prevAll("td").eq(4).html(isTop);
                }
                alert(data.message);
            }
        });    
        return false;
    });
    
});

</script>
</body>
</html>
