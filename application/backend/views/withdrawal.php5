<?php
$this->load->view("templates/head") ;
$platforms = array(
    '1' => '网页',
    '2' => 'Android',
    '3' => 'IOS',
    '4' => 'M版'
);
?>
<div class="path">您的位置：报表管理&nbsp;>&nbsp;<a href="">提款记录</a></div>
<div class="data-table-filter mt10" style="width:1100px">
  <form action="/backend/Transactions/list_withdraw" method="get"  id="search_form">
  <table >
    <colgroup>
      <col width="62" />
      <col width="130" />
      <col width="62" />
      <col width="150" />
      <col width="62" />
      <col width="105" />
      <col width="62" />
      <col width="280" />
    </colgroup>
    <tbody>
    <tr>
      <th>提款用户：</th>
      <td>
          <input type="text" class="ipt w120"  name="name" value="<?php echo $search['name'] ?>"  placeholder="用户名..." />
      </td>
      <th>&nbsp;订单编号：</th>
      <td>
        <span class="ipt ipt-date w164"><input type="text" class="ipt w140"  style=" " name="trade_no" value="<?php echo $search['trade_no'] ?>"  placeholder="订单编号..." /><i></i></span>
      </td>
      <th>&nbsp;提款平台：</th>
      <td>
          <select class="selectList w98" id="platformId" name="platform">
              <option value="">不限</option>
              <?php foreach ($platforms as $key => $val):?>
                  <option value="<?php echo $key;?>"
                      <?php if($key == ($search['platform'] + 1) ): echo "selected"; endif;?>><?php echo $val;?></option>
              <?php endforeach;?>
          </select>
      </td>
      <th>创建时间：</th>
      <td>
        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
    </tr>
    <tr>
      <th>订单状态：</th>
      <td>
        <select class="selectList w120"  name="ctype">
            <option value="">全部</option>
             <?php foreach ($this->w_s_cfg as $key => $rs): ?>
             <option value="<?php echo $key; ?>" <?php if($search['ctype'] === "{$key}"): echo "selected"; endif;   ?>><?php echo $rs?></option>  
             <?php endforeach; ?>
        </select>
      </td>
      <th >&nbsp;提款金额：</th>
      <td colspan="3" >
        <input type="text" class="ipt w130" name="start_money" value='<?php echo $search['start_money'] ?>'/>
        <span class="ml8 mr8">至</span>
        <input type="text" class="ipt w130" name="end_money" value='<?php echo $search['end_money'] ?>'/>
      </td>
      <th>提款渠道：</th>
      <td colspan="3">
        <select class="selectList w120"  name="channel">
            <option value="">全部</option>
             <option value="tonglian" <?php if($search['channel'] == "tonglian"): echo "selected"; endif;   ?>>通联支付</option>  
             <option value="lianlian" <?php if($search['channel'] == "lianlian"): echo "selected"; endif;   ?>>连连支付</option>  
             <option value="xianfeng" <?php if($search['channel'] == "xianfeng"): echo "selected"; endif;   ?>>先锋代付</option>  
        </select>
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
      <col width="134" />
      <col width="100" />
      <col width="78" />
      <col width="140" />
      <col width="140" />
      <col width="100" />
      <col width="100" />
      <col width="75" />
      <col width="120" />
      <col width="120" />
      <col width="120" />
      <col width="120" />
      <col width="120" />
      <col width="120" />
      <col width="80" />
    </colgroup>
    <thead>
        <tr>
            <td colspan="15">
                <div class="tal">
                    <strong>提款人数</strong>
                    <span><?php echo intval($tj[0]); ?></span>
                    <strong class="ml20">提款笔数</strong>
                    <span><?php echo intval($pages[3]); ?></span>
                    <strong class="ml20">提款成功总额</strong>
                    <span><?php echo m_format($tj[1]); ?></span>
                    <strong class="ml20">成功单数</strong>
                    <span><?php echo intval($tj[2]); ?></span>
                    <strong class="ml20">失败单数</strong>
                    <span><?php echo intval($tj[3]); ?></span>
                    <strong class="ml20">待审核单数</strong>
                    <span><?php echo intval($tj[4]) ; ?></span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr>
      <th>订单编号</th>
      <th>用户名</th>
       <th>真实姓名</th>
      <th>提款账户银行</th>
      <th>银行卡号</th>
      <th>提款金额（元）</th>
      <th>账户余额（元）</th>
      <th>订单状态</th>
      <th>申请时间</th>
      <th>已操作打款时间</th>
      <th>成功时间</th>
      <th>失败时间</th>
	  <th>失败原因</th>
      <th>提款平台</th>
      <th>提款通道</th>
    </tr>
    <?php foreach($trans as $key => $mon): ?>
      <tr>
      <td><?php echo $mon['trade_no'] ?></td>
      <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $mon['uid'] ?>" class="cBlue"><?php echo $mon['uname'] ?></a></td>
       <td><?php echo $mon['real_name'] ?></td>
      <td><?php echo $this->pay_cfg['chinabank']['child'][$mon['bank_name']][0];?></td>
      <td><?php echo $mon['bank_id'];?></td>
      <td><?php echo m_format($mon['money']) ?></td>
      <td><?php echo m_format($mon['umoney']) ?></td>
      <td><?php echo ($mon['status']==0)?'提款申请':$this->w_s_cfg[$mon['status']] ;?></td>
      <td><?php echo $mon['created']; ?></td>
      <td><?php if($mon['start_check'] != '0000-00-00 00:00:00'): echo $mon['start_check'] ; endif;?></td>
      <td><?php if($mon['succ_time'] != '0000-00-00 00:00:00'): echo $mon['succ_time'] ; endif;?></td>
      <td><?php if($mon['fail_time'] != '0000-00-00 00:00:00'): echo $mon['fail_time'] ; endif;?></td>
	  <td><?php echo $mon['content'];?></td>
      <td><?php echo ($mon['platform'] == 0) ? "网页" : ($mon['platform'] == 1 ? "Android" : ($mon['platform'] == 2 ? "IOS" : "M版"));?></td>
      <td><?php if($mon['withdraw_channel']=='tonglian'){echo '通联支付';} if($mon['withdraw_channel']=='lianlian'){echo '连连支付';}if($mon['withdraw_channel']=='xianfeng'){echo '先锋代付';} if(!$mon['withdraw_channel']){echo '人工';}?></td>
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
    var pay_cfg =jQuery.parseJSON('<?php echo  json_encode($this->pay_cfg) ?>');
    var rtype_1 = '<?php echo $search['rtype1'] ?>';
    $(function(){
        $("#rtype").bind('change', function(){
            if($("#rtype1").length > 0 || $(this).val()==0)
            {
                $("#rtype1").remove();
                if($(this).val()==0)
                    return ;
            }
            rtype1 = pay_cfg[$(this).val()]['child'];
            if(rtype1 != undefined)
            {
                html = ' <select class="selectList w222"  name="rtype1" id="rtype1">';
                html += '<option value="">全部</option>';
                if(rtype1['default'] == undefined)
                {
                    for (var key in rtype1) 
                    {
                       html += '<option value="'+key+'">'+rtype1[key][0]+'</option>'; 
                    }
                }
                html +='</select>';
                $("#rtype").after(html);
            }
        });
        
       $(".Wdate1").focus(function(){
            dataPicker();
        });
        $("#rtype").change();
        if($("#rtype1").length > 0 && rtype_1 != '')
        {
            $("#rtype1").val(rtype_1);
        }
    });
    
</script>
</body>
</html>