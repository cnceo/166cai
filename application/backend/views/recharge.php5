<?php
$this->load->view("templates/head") ;
$platforms = array(
    '1' => '网页',
    '2' => 'Android',
    '3' => 'IOS',
    '4' => 'M版'
);
$payTypeName = array(
  'llpayWeb'  =>  '连连快捷',
  'llpaySdk'    =>  '连连SDK',
  'payWeix'     =>  '中信微信',
  'sumpayWap'   =>  '统统付Wap',
  'sumpayWeb'   =>  '统统付快捷',
  'yeepay'      =>  '易宝',
  'yeepayCredit'=>  '易宝信用卡',
  'yeepayKuaij' =>  '易宝快捷',
  'yeepayMPay'  =>  '易宝Wap',
  'yeepayWangy' =>  '易宝网银',
  'yeepayWeix'  =>  '易宝微信', 
  'zxwxSdk'     =>  '中信微信SDK', 
  'payZfb'      =>  '全付通支付宝',
  'wzPay'       =>  '微众银行支付宝',
  'wftWxSdk'    =>  '全付通微信SDK',
  'wftWx'       =>  '全付通微信PC',
  'jdPay'       =>  '京东支付',
  'umPay'       =>  '联动快捷',
  'xzZfbWap'    =>  '现在支付宝H5',  
  'hjZfbPay'    =>  '汇聚无限支付宝',
  'wftZfbWap'   =>  '兴业支付宝H5',
  'hjWxWap'     =>  '微信H5-兴业银行',
  'hjZfbWap'    =>  '支付宝H5-鸿粤浦发银行',
  'payXmZfb'    =>  '厦门银行支付宝',
  //'xzpay'  =>  '现在支付宝H5',
  //'wftpay' => '兴业支付宝H5',
  //'hjpay'  => '汇聚无限支付宝',
  'wftWxWap'    =>  '微信H5-鸿粤浦发银行',
  'payPaZfb'    =>  '平安银行支付宝',
  'pfWxWap'     =>  '浦发白名单',
  'payYlyZf'=> '银联云支付',
    'yzpayh' => '盈中平安银行支付宝',
  'tomatoZfbWap' => '番茄支付支付宝h5',
  'ulineWxWap' => '上海银行微信h5',
  'hjZfbSh' => '支付宝H5-上海银行',
  'yzWxWap' => '盈中平安银行微信h5',
  'jdSdk'       =>  '京东SDK',
  'wftwxzx'     => '微信扫码-长沙中信银行渠道',
  'wftzfbzx'     => '支付宝扫码-长沙中信银行渠道',
  'tomatoWxWap' => '番茄支付微信h5',
);
$payTypes = array(
  'llpayWeb'  =>  '连连快捷',
  'llpaySdk'    =>  '连连SDK',
  'payWeix'     =>  '中信微信',
  'sumpayWap'   =>  '统统付Wap',
  'sumpayWeb'   =>  '统统付快捷',
  'yeepay'      =>  '易宝',
  'yeepayCredit'=>  '易宝信用卡',
  'yeepayKuaij' =>  '易宝快捷',
  'yeepayMPay'  =>  '易宝Wap',
  'yeepayWangy' =>  '易宝网银',
  'yeepayWeix'  =>  '易宝微信', 
  'zxwxSdk'     =>  '中信微信SDK', 
  'payZfb'      =>  '全付通支付宝',
  'wzPay'       =>  '微众银行支付宝',
  'wftWxSdk'    =>  '全付通微信SDK',
  'wftWx'       =>  '全付通微信PC',
  'jdPay'       =>  '京东支付',
  'umPay'       =>  '联动快捷',
  'xzZfbWap'    =>  '现在支付宝H5',  
  'hjZfbPay'    =>  '汇聚无限支付宝',
  'wftZfbWap'   =>  '兴业支付宝H5',
  'xzpay'       =>  '现在支付宝H5',
  'wftpay'      =>  '兴业支付宝H5',
  'hjpay'       =>  '汇聚无限支付宝',
  'hjWxWap'     =>  '微信H5-兴业银行',
  'hjZfbWap'    =>  '支付宝H5-鸿粤浦发银行',
  'wftWxWap'    =>  '微信H5-鸿粤浦发银行',
  'payPaZfb'    =>  '平安银行支付宝',
  'payXmZfb'    =>  '厦门银行支付宝',
  'pfWxWap'     =>  '浦发白名单',
  'payYlyZf'    => '银联云支付',
    'yzpayh'    => '盈中平安银行支付宝',
 'tomatoZfbWap' => '番茄支付支付宝h5',
 'ulineWxWap' => '上海银行微信h5',
 'hjZfbSh' => '支付宝H5-上海银行',
    'yzWxWap' => '盈中平安银行微信h5',
  'jdSdk'       =>  '京东SDK',
  'wftwxzx'     => '微信扫码-长沙中信银行渠道',
  'wftzfbzx'    => '支付宝扫码-长沙中信银行渠道',
  'tomatoWxWap' => '番茄支付微信h5',
);
?>
<div class="path">您的位置：<a href="">报表系统</a>&nbsp;>&nbsp;<a href="">充值记录</a></div>
<div class="data-table-filter mt10"style="width:1100px" >
  <form action="/backend/Transactions/list_recharge" method="get"  id="search_form">
  <table>
    <colgroup>
      <col width="62" />
      <col width="130" />
      <col width="62" />
      <col width="400" />
      <col width="62" />
      <col width="120" />
      <col width="62" />
      <col width="140" />
    </colgroup>
    <tbody>
    <tr>
      <th>充值信息：</th>
      <td>
          <input type="text" class="ipt w120"  name="name" value="<?php echo $search['name'] ?>"  placeholder="用户名/订单编号" />
      </td>
      <th>创建时间：</th>
      <td>
        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <th>充值方式：</th>
      <td>
        <select class="selectList w130"  name="rtype" id="rtype">
            <option value="">全部</option>
            <?php foreach ($payTypeName as $key => $payType): ?>
            <option value="<?php echo $key; ?>" <?php if($search['rtype'] === "{$key}"): echo "selected"; endif;   ?>><?php echo $payType ?></option>  
            <?php endforeach; ?>
        </select>
      </td>
      <th class="tar">注册渠道：</th>
      <td>
        <select class="selectList w130" name="registerChannel">
      		<option value="">不限</option>
      		<?php foreach ($channels as $val):?>
      		<option value="<?php echo $val['id'];?>" <?php if($search['registerChannel'] == $val['id']): echo "selected"; endif;?>><?php echo $val['name'];?></option>
      		<?php endforeach;?>
      	</select>
      </td>
    </tr>
    <tr>
      <th>订单状态：</th>
      <td>
        <select class="selectList w120"  name="mark">
            <option value="">全部</option>
             <?php foreach ($this->r_s_cfg as $key => $rs): ?>
             <option value="<?php echo $key; ?>" <?php if($search['mark'] === "{$key}"): echo "selected"; endif;   ?>><?php echo $rs?></option>  
             <?php endforeach; ?>
        </select>
      </td>
      <th>交易时间：</th>
      <td>
      <span class="ipt ipt-date w184"><input type="text" name='start_r_time' value="<?php echo $search['start_r_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_r_time' value="<?php echo $search['end_r_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <th>充值金额：</th>
      <td colspan="3">
        <input type="text" class="ipt w130" name="start_money" value='<?php echo $search['start_money'] ?>'/>
        <span class="ml8 mr8">至</span>
        <input type="text" class="ipt w130" name="end_money" value='<?php echo $search['end_money'] ?>'/>
        <a href="javascript:void(0);" class="btn-blue mb10" onclick="$('#search_form').submit();">查询</a>
      </td>
    </tr>
    <tr>
        <th>充值平台：</th>
        <td>
            <select class="selectList w98" id="platformId" name="platform">
                <option value="">不限</option>
                <?php foreach ($platforms as $key => $val):?>
                    <option value="<?php echo $key;?>"
                        <?php if($key == ($search['platform'] + 1) ): echo "selected"; endif;?>><?php echo $val;?></option>
                <?php endforeach;?>
            </select>
        </td>
        <th class="tar">注册方式：</th>
        <td colspan="4">
            <select class="selectList w130" name="reg_type">
                <option value="0" <?php if(empty($search['reg_type']) || $search['reg_type'] == '0'){ echo "selected"; }?>>不限</option>
                <option value="1" <?php if($search['reg_type'] == '1'){ echo "selected"; }?>>账号密码</option>
                <option value="3" <?php if($search['reg_type'] == '3'){ echo "selected"; }?>>微信</option>
                <option value="4" <?php if($search['reg_type'] == '4'){ echo "selected"; }?>>短信验证码</option>
            </select>
        </td>
    </tr>
    </tbody>
  </table>
  </form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="50" />
      <col width="134" />
      <col width="100" />
      <col width="78" />
      <col width="80" />
      <!-- <col width="150" /> -->
      <col width="80" />
      <col width="112" />
      <col width="120" />
      <col width="120" />
      <col width="75" />
      <col width="75" />
      <col width="75" />
      <col width="75" />
      <col width="75" />
      <col width="75" />
    </colgroup>
    <thead>
        <tr>
            <td colspan="15">
                <div class="tal">
                    <strong>充值人数</strong>
                    <span><?php echo intval($tj[0]); ?></span>
                    <strong class="ml20">充值笔数</strong>
                    <span><?php echo  intval($pages[3]); ?></span>
                    <strong class="ml20">充值成功总额</strong>
                    <span><?php echo m_format($tj[1]); ?></span>
                    <strong class="ml20">成功订单数</strong>
                    <span><?php echo intval($tj[3]); ?></span>
                    <strong class="ml20">待付款订单数</strong>
                    <span><?php echo intval($tj[2]); ?></span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr>
      <th><input type="checkbox" name="ckallbox"  class="ckbox" value="1"></th>
      <th>订单编号</th>
      <th>用户名</th>
      <th>真实姓名</th>
      <th>充值方式</th>
      <!-- <th>充值网关</th> -->
      <th>充值金额（元）</th>
      <th>账户余额（元）</th>
      <th>创建时间</th>
      <th>交易时间</th>
      <th>订单状态</th>
      <th>注册渠道</th>
      <th>注册方式</th>
      <th>充值平台</th>
      <th>补单</th>
      <th>退款</th>
    </tr>
    <?php foreach($trans as $key => $mon): ?>
      <tr>
      <td><?php if(date('Y-m-d', strtotime($mon['created'])) > date('Y-m-d', strtotime("-90 day"))):?><input type="checkbox" name="ckbox[]"  class="ckbox" value="<?php echo $mon['trade_no']?>"><?php endif;?></td>
      <td><?php echo $mon['trade_no'] ?></td>
      <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $mon['uid'] ?>" class="cBlue"><?php echo $mon['uname'] ?></a></td>
      <td><?php echo $mon['real_name'] ?></td>
      <td><?php echo $payTypes[$mon['additions']];?></td>
      <!-- <td><?php echo $this->pay_cfg[$mon['pay_type']]['child'][$mon['bank']][0];  ?></td> -->
      <td><?php echo m_format($mon['money']) ?></td>
      <td><?php echo m_format($mon['umoney']) ?></td>
      <td><?php echo $mon['created'] ?></td>
      <td><?php echo $mon['recharge_over_time'] ?></td>
      <td><?php echo $this->r_s_cfg[$mon['mark']] ;?></td>
      <td><?php echo $channels[$mon['userChannel']]['name'];?></td>
      <td><?php echo ($mon['reg_type'] <= 2) ? '账号密码' : ($mon['reg_type'] == 3 ? '微信' : '短信验证码'); ?></td>
      <td><?php echo $platforms[$mon['platform'] + 1];?></td>
      <td><?php if(date('Y-m-d', strtotime($mon['created'])) > date('Y-m-d', strtotime("-90 day"))):?><a href="javascript:void(0);" class="cBlue selectP" data-val="<?php echo $mon['trade_no'] ?>">补单查询</a><?php endif;?></td>
      <td><?php if($mon['mark'] == '1' && (in_array($mon['additions'], array('payWeix', 'zxwxSdk')))):?><a href="javascript:void(0);" class="cBlue refundP" data-val="<?php echo $mon['trade_no'] ?>|<?php echo $payTypes[$mon['additions']];?>|<?php echo $this->r_s_cfg[$mon['mark']] ;?>|<?php echo m_format($mon['money']) ?>">退款</a><?php endif;?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="14">
            <div class="tal ptb10 c999">
                <a href="javascript:void(0);" class="btn-blue mr10" id="supplement" data-type="1" style="width:90px;">补单</a>
            </div>
        </td>
      </tr>
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
<!-- 弹出层 -->
<div class="pop-mask" style="display:none;width:200%"></div>
<!-- 交易查询 start -->
<div class="pop-dialog" id="selectPop">
  <div class="pop-in">
    <div class="pop-head">
      <h2>交易查询结果</h2>
      <span class="pop-close" title="关闭">关闭</span>
    </div>
    <div class="pop-body">
      <div class="data-table-filter del-percent">
        <table>
          <colgroup>
            <col width="68" />
                    <col width="150" />
          </colgroup>
          <tbody id="tbody">
          </tbody>
        </table>
      </div>
    </div>
    <div class="pop-foot tac">
      <a href="javascript:;" class="btn-blue-h32 pop-cancel">关闭</a>
    </div>
  </div>
</div>
<!-- 交易查询 end -->
<!-- 退款 start -->
<div class="pop-dialog" id="refundPop">
  <div class="pop-in">
    <div class="pop-head">
      <h2>退款操作</h2>
      <span class="pop-close" title="关闭">关闭</span>
    </div>
    <div class="pop-body">
      <div class="data-table-filter del-percent">
        <table>
          <colgroup>
            <col width="68" />
                    <col width="150" />
          </colgroup>
          <tbody id="tbody1">
          </tbody> 
        </table>
      </div>
    </div>
    <div class="pop-foot tac">
      <a href="javascript:;" class="btn-blue-h32 mlr15" id='submitRefund'>确 认</a><a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取 消</a>
    </div>
  </div>
</div>
<!-- 退款 end -->
<!-- 批量补单 start -->
<div class="pop-dialog" id="supplementPop">
  <div class="pop-in">
    <div class="pop-head">
      <h2>补单结果查询</h2>
    </div>
    <div class="pop-body">
      <div class="data-table-filter del-percent">
        <table>
          <colgroup>
            <col width="68" />
                    <col width="150" />
          </colgroup>
          <tbody id="tbody2">
          	<tr><td colspan="2">补单操作进行中，等待补单结果反馈......</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="pop-foot tac">
      <a href="javascript:;" class="btn-blue-h32 mlr15" id='supplementSubmit'>停止补单进程</a>
    </div>
  </div>
</div>
<!-- 批量补单 end -->
<script  src="/source/date/WdatePicker.js"></script>
<script>
	checkAll($("input[name='ckallbox']"), $("input[name='ckbox[]']"));
    $(function(){
        $(".Wdate1").focus(function(){
            dataPicker();
        });
        $(".selectP").click(function(){
            $_this = $(this);
        	$.ajax({
                type: 'post',
                url:  '/backend/Transactions/orderSelect',
                data: {},
                success: function(response) {
                    var response = $.parseJSON(response);
                    if(response.status == 'n'){
                        alert(response.message);
                    }else{
                    	var trade_no = $_this.attr("data-val");
                        $.ajax({
                            type: "get",
                            url: '/api/recharge/orderSelect/' + trade_no,
                            success: function (data) {
                              var html = '';
                              var data = jQuery.parseJSON(data);
                              if(data.code == '0'){
                            	  html += '<tr><td colspan="2">以下为第三方支付机构交易结果。</td></tr>';
                                  html += '<tr><th>支付方式:</th><td>' + data.ptype + '</td></tr>';
                                  html += '<tr><th>商户编号:</th><td>' + data.mer_id + '</td></tr>';
                                  html += '<tr><th>订单状态:</th><td>' + data.pstatus + '</td></tr>';
                                  html += '<tr><th>支付金额:</th><td>' + data.pmoney + '</td></tr>';
                                  html += '<tr><th>支付时间:</th><td>' + data.ptime + '</td></tr>';
                                  html += '<tr><th>银行名称:</th><td>' + data.pbank + '</td></tr>';
                                  if(data.isDone == '1')
                                  {
                                	  html += '<tr><td colspan="2">已对此单进行补单操作。</td></tr>';
                                  }
                              }else{  
                            	  html += '<tr><td colspan="2">' + data.msg + '</td></tr>';
                              }
                              $("#tbody").html(html);
                              popdialog("selectPop");
                            }
                        });
                    }
                },
                error: function () {
                    alert('网络异常，请稍后再试');
                }
            });
          });

        $("#supplement").click(function(){
             var s = '';
             s = getCheckVal("ckbox[]");
             if(!s){
                 return false;
             }
             popdialog("supplementPop");
          	 $.ajax({
             	type: "post",
                url: '/backend/Transactions/supplementOrder',
                     data: {'ids': s},
                     success: function (data) {
                         var json = jQuery.parseJSON(data);
                         if(json.status =='y'){
                        	 var html = '';
                        	 html += '<tr><td colspan="2">'+json.message+'</td></tr>';
                        	 $("#tbody2").html(html);
                        	 $("#supplementSubmit").html('确定');
                             //location.reload();
                         }else{
                        	 closePop();
                             alert(json.message);
                         }
                     }
                 });
             
             return false;
         });

        $("#supplementSubmit").click(function(){
        	location.reload();
        });
        
        $(".refundP").click(function(){
        	$_this = $(this);
      	   $.ajax({
                 type: 'post',
                 url:  '/backend/Transactions/refundCheck',
                 data: {},
                 success: function(response) {
                     var response = $.parseJSON(response);
                     if(response.status == 'n'){
                         alert(response.message);
                     }else{
                    	 var dataStr = $_this.attr("data-val");
                         var data = dataStr.split("|");
                         var html = '';
                         html += '<tr><td colspan="2" style="color:red;">切记，请在退款前先对用户余额进行调账扣款！</td></tr>';
                         html += '<tr><th>支付方式:</th><td>' + data[1] + '</td></tr>';
                         html += '<tr><th>订单状态:</th><td>' + data[2] + '</td></tr>';
                         html += '<tr><th>支付金额:</th><td>' + data[3] + '元</td></tr>';
                         html += '<tr><th>退款金额:</th><td><input type="text" class="ipt w184" name="money"><input type="hidden" name="trade_no" value="' + data[0] + '"></td></tr>';
                         $("#tbody1").html(html);
                         popdialog("refundPop");
                     }
                 },
                 error: function () {
                     alert('网络异常，请稍后再试');
                 }
             });
          });
    });
    $('#submitRefund').click(function(){
        $.ajax({
            type: 'post',
            url: '/api/pay/orderRefund/',
            data: {trade_no:$('input[name="trade_no"]').val(), money:$('input[name="money"]').val()},
            dataType : 'json',
            success: function(response) {
            	if(response.code == '0'){
            		alert(response.msg);
            		$.ajax({
                        type: 'post',
                        url: '/backend/Transactions/refundLog',
                        data: {trade_no:$('input[name="trade_no"]').val(), money:$('input[name="money"]').val()},
                        success: function(data) {
                        	location.reload();
                        }
                    });
                }else{
                	alert(response.msg);
                }
            },
            error: function () {
                alert('网络异常，请稍后再试');
            }
        });
    }); 
</script>
</body>
</html>