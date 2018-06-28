<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：财务对账&nbsp;&gt;&nbsp;<a href="/backend/Statistics/partner?searchType=day">合作商对账</a>&nbsp;&gt;&nbsp;存入押金记录</div>
<div class="mod-tab mt20">
  <div>
        <div class="data-table-filter">
        	<form action="/backend/Statistics/partnerLog" method="get"  id="search_form">
            	<table>
                	<tbody>
                    <tr>
                    	<td>
                                        日期：
                    	<input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="ipt ipt-date w150 Wdate1" /><i></i>
                                        至
                        <input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="ipt ipt-date w150 Wdate1" /><i></i>
                                        合作商：
                        <select class="partnerType w122" id="" name="name">
                            <?php foreach ($partners as  $key => $partner): ?>
                            <option <?php if($search['name'] == $key):?>selected<?php endif;?> value="<?php echo $key;?>"><?php echo $partner;?></option>
                            <?php endforeach; ?>
                        </select>
                 		<a href="javascript:;" id="search" class="btn-blue">查询</a>
           				</td>
                	</tr>
               		</tbody>
    			</table>
     			<input type="hidden" name="searchType" value=""/>
   			</form>
   		</div>
        <div class="data-table-list mt10">
          <table>
            <colgroup>
              <col width="10%" />
              <col width="15%" />
              <col width="15%" />
              <col width="60%" />
            </colgroup>
            <thead>
              <tr>
                <th>合作商</th>
                <th>日期</th>
                <th>存入押金</th>
                <th>备注</th>
              </tr>
            </thead>
            <tbody>
            <?php if($list):?>
            <?php foreach ($list as $value):?>
            <tr>
               <td><?php echo $value['name'];?></td>
               <td><?php echo $value['date'];?></td>
               <td><?php echo m_format($value['money']); ?></td>
               <td><?php echo $value['content']; ?></td>
            </tr>
            <?php endforeach;?>
            <?php else:?>
            <tr>
               <td colspan="4">暂无数据</td>
              </tr>
            <?php endif;?>
            </tbody>
          </table>
        </div>
        <div class="page mt10">
         <?php echo $pages[0]; ?>
    	</div>
  </div>
</div>
<script src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
    	$("#search").click(function(){
            var start = $("input[name='start_time']").val();
            var end = $("input[name='end_time']").val();
            if(start > end)
            {
                alertPop('您选择的时间段错误，请核对后操作');
                return false;
            }
            $('#search_form').submit();
        });
        $(".Wdate1").focus(function(){
            dataPicker();
        });

    });

</script>
</body>
</html>