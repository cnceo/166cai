<?php if($fromType != 'ajax'): $this->load->view("templates/head") ?>
<div class="path">您的位置：报表管理&nbsp;>&nbsp;<a href="">订单管理</a></div>
<?php endif; ?>
<div class="data-table-filter mt10" style="width:960px">
  <form action="/backend/Order/" method="get"  id="search_form_order">
  <table>
    <colgroup>
      <col width="62" />
      <col width="232" />
      <col width="62" />
      <col width="400" />
      <col width="62" />
      <col width="100" />
    </colgroup>
    <tbody>
    <tr>
      <th>关键字：</th>
      <td>
          <input type="text" class="ipt w222"  name="name" value="<?php echo $search['name'] ?>"  placeholder="用户名/订单" />
      </td>
      <th>彩种玩法：</th>
      <td>
        <select class="selectList w120 mr20"  name="lid" id="caipiao_play">
            <option value="">全部</option>
            <?php foreach ($this->caipiao_cfg as $key => $cp): ?>
            <option value="<?php echo $key; ?>" <?php if($search['lid'] === "{$key}"): echo "selected"; endif;    ?>><?php echo $cp['name'] ?></option>   
            <?php endforeach; ?>
        </select>
      </td>
    </tr>
    <tr>
        <th>期次：</th>
        <td>
            <input type="text" class="ipt w222" name="issue" value='<?php echo $search['issue'] ?>' placeholder="期次..." />
        </td>
     <th>下单金额：</th>
      <td>
        <input type="text" class="ipt w120" name="start_money" value='<?php echo $search['start_money'] ?>'/>
        <span class="ml8 mr8">至</span>
        <input type="text" class="ipt w120" name="end_money" value='<?php echo $search['end_money'] ?>'/>
      </td>
    </tr>
    <tr>
      <th>订单状态：</th>
      <td>
        <select class="selectList w222 mr20"  name="status" id="status">
            <option value="">全部</option>
            <?php foreach ($this->caipiao_status_cfg as $key => $status): ?>
            <option value="<?php echo $key ?>" <?php if($search['status'] === "{$key}"): echo "selected"; endif;    ?>><?php echo $status[0]; ?></option>   
            <?php endforeach; ?>
            <option value="success"  <?php if($search['status'] === "success"): echo "selected"; endif;?>>已出票</option>
        </select>
      </td>
      <th>下单时间：</th>
      <td>
          <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
          <span class="ml8 mr8">至</span>
          <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <td >
          <a href="javascript:void(0);" class="btn-blue mr20" onclick="$('#search_form_order').submit();">查询</a>
      </td>
    </tr>
    </tbody>
  </table>
      <input type="hidden" name="fromType" value="<?php echo $fromType  ?>"  id="fromType" />
      <?php if($fromType == 'ajax'):?><input type="hidden" name="uid" value="<?php echo $search['uid']?>"/><?php endif; ?>
  </form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="135" />
      <col width="128" />
      <col width="105" />
      <col width="78" />
      <col width="85" />
      <col width="132" />
      <col width="100" />
      <col width="100" />
      <col width="120" />
      <col width="80" />
    </colgroup>
    <thead>
        <tr>
            <td colspan="10">
                <div class="tal">
                    <strong>订单总额</strong>
                    <span><?php echo m_format($tj[0]); ?> 元</span>
                    <strong class="ml20">中奖总额(税前)</strong>
                    <span><?php echo  m_format($tj[2]); ?> 元</span>
                    <strong class="ml20">中奖总额(税后)</strong>
                    <span><?php echo  m_format($tj[1]); ?> 元</span>
                    <strong class="ml20">用户统计</strong>
                    <span><?php echo  $tj[3]; ?> 人</span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr>
      <th>订单编号</th>
      <th>用户名</th>
      <th>彩种</th>
      <th>玩法</th>
      <th>期次</th>
      <th>创建时间</th>
      <th>订单金额（元）</th>
      <th>中奖金额（税后）</th>
      <th>订单状态</th>
      <th>详情</th>
    </tr>
    <?php foreach($orders as $key => $order): ?>
      <tr>
      <td><?php echo $order['orderId'] ?></td>
      <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $order['uid'] ?>" class="cBlue"><?php echo $order['userName'] ?></a></td>
      <td><?php echo $this->caipiao_cfg[$order['lid']]['name'] ?></td>
      <td><?php if(!empty($this->caipiao_cfg[$order['lid']]['play'])): echo print_playtype($order['lid'], $order['playType'], $this->caipiao_cfg[$order['lid']]['play']); else: echo "--";  endif;?></td>
      <td><?php echo $order['issue'] ?></td>
      <td><?php echo $order['created'] ?></td>
      <td><?php echo m_format($order['money']) ?></td>
      <td><?php echo m_format($order['margin']) ?></td>
      <td><?php if($order['status'] == '2000'): echo $this->caipiao_ms_cfg['2000'][$order['my_status']][0]; else: echo $this->caipiao_status_cfg[$order['status']][0]; endif;?></td>
      <td><a href="/backend/Order/order_detail/?id=<?php echo $order['orderId']; ?>" class="cBlue" target="_blank">查看</a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="10">
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
<div class="page mt10 order_info">
   <?php echo $pages[0] ?>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    var caipiao_cfg =jQuery.parseJSON('<?php echo  json_encode($this->caipiao_cfg) ?>');
    var mystatus_cfg = jQuery.parseJSON('<?php echo  json_encode($this->caipiao_ms_cfg) ?>');
    var play_type = '<?php echo $search['playType'] ?>';
    var s_my_status = '<?php echo $search['my_status'] ?>';
    $(function(){
        $("#caipiao_play").bind('change', function(){
            if($("#play_type").length > 0  || $(this).val()==0)
            {
                $("#play_type").remove();
                 if($(this).val()==0)
                    return ;
            }
            play = caipiao_cfg[$(this).val()]['play'];
            if(play != undefined)
            {
                html = ' <select class="selectList w120"  name="playType" id="play_type">';
                html += '<option value="">全部</option>';
                for (var key in play) 
                {
                   html += '<option value="'+key+'">'+play[key]['name']+'</option>'; 
                }
                html +='</select>';
                $("#caipiao_play").after(html);
            }
        });
        
       $(".Wdate1").focus(function(){
            dataPicker();
        });   
        
        $('#search_form_order').submit(function(){
            if($("#fromType").val() == "ajax")
            {
                $("#order_info").load("/backend/Order/index?"+$("#search_form_order").serialize()+"&fromType=ajax");
                return false;
            }
            return true;
        });
        $('.order_info a').click(function(){
            if($("#fromType").val() == "ajax")
            {
                var _this = $(this);
                $("#order_info").load(_this.attr("href"));
                return false;
            }
            return true;
        });
        
        $("#status").bind("change",function(){
            if($(this).val() == '2000')
            {
                mstatus = mystatus_cfg[$(this).val()];
                if(mstatus != undefined)
                {
                    html = ' <select class="selectList w120"  name="my_status" id="my_stauts">';
                    html += '<option value="">全部</option>';
                    for (var key in mstatus) 
                    {
                        html += '<option value="'+key+'">'+mstatus[key][0]+'</option>'; 
                    }
                    html +='</select>';
                    $("#status").after(html);
                }
            }
            else
            {
                if($("#my_stauts").length > 0)
                {
                        $("#my_stauts").remove();
                }
            }
        });
        
        $("#caipiao_play").change();
        if($("#play_type").length > 0 && play_type != '')
        {
            $("#play_type").val(play_type);
        }
        $("#status").change();
        if($("#my_stauts").length > 0)
        {
            $("#my_stauts").val(s_my_status);
        }
    });

    
</script>
<?php if($fromType != 'ajax'): ?>
</body>
</html>
<?php endif; ?>