<?php if($fromType != 'ajax'): $this->load->view("templates/head") ?>
<div class="path">您的位置：审核管理&nbsp;>&nbsp;<a href="">派奖审核</a></div>
<?php endif; ?>
<div class="data-table-filter mt10" style="width:1100px">
    <form action="/backend/Order/check_list" method="get" id="search_form_check">
  <table>
    <colgroup>
      <col width="62" />
      <col width="140" />
      <col width="82" />
      <col width="284" />
      <col width="62" />
      <col width="400 "/>
    </colgroup>
    <tbody>
    <tr>
      <th>关键字：</th>
      <td>
          <input type="text" class="ipt w120" placeholder="用户名..."  name="name" value="<?php echo $search['name']; ?>"/>
      </td>
     <th>订单编号：</th>
      <td>
            <input type="text" class="ipt w264"  name="orderId" value="<?php echo $search['orderId']; ?>"/>
      </td>
      <th>彩种玩法：</th>
      <td>
        <select class="selectList w130 mr20 "  name="lid" id="caipiao_play">
            <option value="">全部</option>
            <?php foreach ($this->caipiao_cfg as $key => $cp): ?>
            <option value="<?php echo $key; ?>" <?php if($search['lid'] === "{$key}"): echo "selected"; endif;    ?>><?php echo $cp['name'] ?></option>   
            <?php endforeach; ?>
        </select>
      </td>
    </tr>
      <th>期次：</th>
      <td>
            <input type="text" class="ipt w120"  name="issue" value="<?php echo $search['issue']; ?>"/>
      </td>
      <th>税前中奖金额：</th>
      <td>
        <input type="text" class="ipt w120" name="start_money" value='<?php echo $search['start_money'] ?>'/>
        <span class="">至</span>
        <input type="text" class="ipt w120" name="end_money" value='<?php echo $search['end_money'] ?>'/>
      </td>
     <th>获奖时间：</th>
      <td>
      <span class="ipt ipt-date w184"><input type="text" name='start_w_time' value="<?php echo $search['start_w_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_w_time' value="<?php echo $search['end_w_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <td colspan="2">
        <a href="javascript:void(0);" class="btn-blue fl ml20" onclick="$('#search_form_check').submit();">查询</a>
      </td>
    </tr>
    </tbody>
  </table>
        <input type="hidden" name="fromType" value="<?php echo $fromType  ?>"  id="fromType" />
    </form>
</div>
<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="134" />
      <col width="100" />
       <col width="80" />
      <col width="80" />
      <col width="70" />
      <col width="80" />
      <col width="80" />
      <col width="80" />
      <col width="90" />
    <col width="120" />
      <col width="180" />
    </colgroup>
    <thead>
        <tr>
            <td colspan="11">
                <div class="tal">
                    <strong >订单总额</strong>
                    <span><?php echo m_format($tj[0]) ?> 元</span>
                    <strong class="ml20">中奖总额(税前)</strong>
                    <span><?php echo m_format($tj[2]) ?> 元</span>
                    <strong class="ml20">中奖总额(税后)</strong>
                    <span><?php echo m_format($tj[1]) ?> 元</span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr>
      <th>订单编号</th>
      <th>用户名</th>
      <th>真实姓名</th>
      <th>彩种</th>
      <th>玩法</th>
      <th>期次</th>
      <th>订单金额</th>
      <th>中奖金额税前</th>
      <th>中奖金额税后</th>
     <th>开奖时间</th>
      <th>操作</th>
    </tr>
    <?php  foreach($checks as $key => $check): ?>
    <tr>
      <?php if($check['orderType']!=4){ ?>
      <td><a target="_blank" href="/backend/Management/orderDetail/?id=<?php echo $check['orderId']; ?>" class="cBlue"><?php echo $check['orderId']  ?></a></td>
      <?php }else{ ?>
      <td><a target="_blank" href="/backend/Management/unitedOrderDetail/?id=<?php echo $check['orderId']; ?>" class="cBlue"><?php echo $check['orderId']  ?></a></td>
      <?php } ?>
      <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $check['uid'] ?>" class="cBlue"><?php echo $check['uname']  ?></a></td>
      <td><?php echo $check['real_name']  ?></td>
      <td><?php echo $this->caipiao_cfg[$check['lid']]['name'] ?></td>
      <td><?php if(!empty($this->caipiao_cfg[$check['lid']]['play'])): echo print_playtype($check['lid'], $check['playType'], $this->caipiao_cfg[$check['lid']]['play']); else: echo $this->caipiao_cfg[$check['lid']]['name'];  endif;?></td>
      <td><?php echo $check['issue']  ?></td>
      <td><?php echo m_format($check['money']) ?></td>
      <td><?php echo m_format($check['bonus']) ?></td>
      <td><?php echo m_format($check['margin']) ?></td>
      <td><?php if(intval($check['time']) > 0): echo date("Y-m-d H:i:s", $check['time'] ); endif?></td>
      <td><?php if($fromType != 'ajax'): ?><a href="" class="cBlue dc-success " data-id="<?php  echo $check['orderId']  ?>" >执行派奖</a><?php if($check['orderType'] != 4): ?><a href="" class="cBlue dc-user-success mlr10" data-id="<?php  echo $check['orderId']  ?>">用户已领</a><a href="" class="cBlue dc-fail mlr10" data-id="<?php  echo $check['orderId']  ?>">派奖失败</a><?php endif; ?><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>

      <tr>
        <td colspan="11">
          <div class="stat">
            <span >本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
            <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
            <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
          </div>
        </td>
      </tr>
    </tfoot>
  </table>
</div>
<div class="page mt10 order_check">
<?php echo $pages[0] ?>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<!-- 审核弹窗 -->
<form id='checkForm' method='post' action=''>
<div class="pop-dialog" id="J-dc-addAccount">
    <div class="pop-in">
        <div class="pop-head">
            <h2 id="pop_name"></h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup>
                        <col width="80" />
                        <col width="180" />
                        <col width="80" />
                        <col width="180" />
                    </colgroup>
                    <tbody id="tbody">

                    </tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15" id='submitForm'>提 交</a>
            <a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a>
        </div>
    </div>
</div>
    <input type="hidden" value="" name="hid_order_id"  id="hid_order_id"/>
    <input type="hidden" value="" name="hid_status"  id="hid_status"/>
</form>
<script  src="/source/date/WdatePicker.js"></script>
<script>
 var caipiao_cfg =jQuery.parseJSON('<?php echo  json_encode($this->caipiao_cfg) ?>');
 var play_type = '<?php echo $search['playType'] ?>';
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
                html = ' <select class="selectList w130"  name="playType" id="play_type">';
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

        $(".dc-success,.dc-fail,.dc-user-success").click(function(){
            var _this = $(this);
            if(_this.hasClass("dc-success"))
            {
                info = "请确认已完成用户信息审核并执行派奖";
                title = '执行派奖';
                tip = "单注奖金超过1w或者单注奖金未超过1w，但奖金总额大于5w，需核实用户信息后派奖。";
                $("#hid_status").val(3);
            }
            else if(_this.hasClass("dc-user-success"))
            {
                info = " 请确认已将中奖彩票 送达至用户手中";
                title = '用户已领';
                tip = "单注奖金超过1w，用户自行领奖。";
                $("#hid_status").val(5);
            }
            else
            {
                title = '派奖失败';
                info = "确认该用户弃奖，并将订单信息状态改为派奖失败？";
                tip = "";
                $("#hid_status").val(4);
            }
            var td = _this.parent("td").siblings("td");
            html = '<tr><td colspan=4>'+tip+'</td></tr><tr class="fb"><td colspan=4 >'+info+'</td></tr>';
            html += "<tr><th>订单编号：</th><td>"+td.eq(0).html()+"</td><th></th><td></td></tr>";
            html += "<tr><th>用户名：</th><td>"+td.eq(1).html()+"</td><th>订单金额：</th><td>"+td.eq(6).html()+"</td></tr>";
            html += "<tr><th>彩种：</th><td>"+td.eq(3).html()+"</td><th>期次：</th><td>"+td.eq(5).html()+"</td></tr>";
            html += "<tr><th>玩法：</th><td>"+td.eq(4).html()+"</td><th>开奖时间：</th><td>"+td.eq(9).html()+"</td></tr>";
            html += "<tr><th>中奖金额(税前)：</th><td>"+td.eq(7).html()+"</td><th>中奖金额(税后)：</th><td>"+td.eq(8).html()+"</td></tr>";
            $("#tbody").html(html);
            $("#pop_name").html(title);
            $("#hid_order_id").val(_this.attr("data-id"));
            popdialog("J-dc-addAccount");
            return false;
        });
        $("#submitForm").click(function(){
            $.ajax({
                type: "post",
                url: '/backend/Order/check',
                data: $("#checkForm").serialize(),
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    alert(json.message)
                    if(json.status =='y')
                    {
                        location.reload();
                    }
                }
            });
            return false;
        });
        
        $('#search_form_check').submit(function(){
            if($("#fromType").val() == "ajax")
            {
                $("#big_order").load("/backend/Order/check_list?"+$("#search_form_check").serialize()+"&fromType=ajax");
                return false;
            }
            return true;
        });
        $('.order_check a').click(function(){
            if($("#fromType").val() == "ajax")
            {
                var _this = $(this);
                $("#big_order").load(_this.attr("href"));
                return false;
            }
            return true;
        });
        $("#caipiao_play").change();
        if($("#play_type").length > 0 && play_type != '')
        {
            $("#play_type").val(play_type);
        }
    });
    
    
    
</script>
<?php if($fromType != 'ajax'): ?>
</body>
</html>
<?php endif; ?>