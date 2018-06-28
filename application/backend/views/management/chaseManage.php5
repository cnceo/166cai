<?php
    $this->load->view("templates/head");
    $setStatus = array(
    	'' => '全部',
    	'0' => '中奖后继续追号',
    	'1' => '中奖后停止追号'
    );
    $platforms = array(
    	'' => '全部',
    	'0' => '网页',
    	'1' => 'Android',
      '2' => 'IOS'
    );
?>
<?php if ($fromType != 'ajax'): $this->load->view("templates/head") ?>
<div class="path">您的位置：运营管理&nbsp;>&nbsp;<a href="/backend/Management/chaseManage">追号管理</a></div>
<?php endif; ?>

<div class="data-table-filter mt10" style="width:960px">
  <form action="/backend/Management/chaseManage" method="get"  id="search_form_chase">
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
      <th>下单金额：</th>
      <td>
        <input type="text" class="ipt w120" name="start_money" value='<?php echo $search['start_money'] ?>'/>
        <span class="ml8 mr8">至</span>
        <input type="text" class="ipt w120" name="end_money" value='<?php echo $search['end_money'] ?>'/>
      </td>
      <th class="tar">追号设置：</th>
      <td>
         <select class="selectList mr20"  name="setStatus">
            <?php foreach ($setStatus as $key => $val): ?>
            <option value="<?php echo $key; ?>" <?php if ($search['setStatus'] === "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?></option>
            <?php endforeach; ?>
         </select>
      </td>
      <th>彩种玩法：</th>
      <td>
         <select class="selectList"  name="lid" id="caipiao_play">
            <option value="">全部</option>
            <?php foreach ($this->caipiao_cfg as $key => $cp): ?>
            <option value="<?php echo $key; ?>" <?php if ($search['lid'] === "{$key}"): echo "selected"; endif; ?>><?php echo $cp['name'] ?></option>
            <?php endforeach; ?>
         </select>
      </td>
    </tr>
    <tr>
      <th>订单状态：</th>
      <td>
        <select class="selectList w222 mr20"  name="status" id="status">
            <option value="">全部</option>
            <?php foreach ($this->chase_manage_cfg as $key => $status): ?>
            <?php if($key == '710'){ continue;}?>
    		<option value="<?php echo $key ?>" <?php if ($search['status'] === "{$key}"): echo "selected"; endif; ?>><?php echo $status; ?></option>
			<?php endforeach; ?>
        </select>
      </td>
      <th>下单时间：</th>
      <td>
          <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
          <span class="ml8 mr8">至</span>
          <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <th>投注平台：</th>
      <td>
        <select class="selectList w98" id="platformId" name="buyPlatform">
            <?php foreach ($platforms as $key => $val):?>
            <option value="<?php echo $key;?>" <?php if ($search['buyPlatform'] === "{$key}"): echo "selected"; endif; ?>><?php echo $val;?></option>
            <?php endforeach;?>
        </select>
      </td>
    </tr>
    <tr>
    <th class="tar">注册渠道：</th>
      <td colspan="4">
        <select class="selectList w130" name="registerChannel">
      		<option value="">不限</option>
      		<?php foreach ($channels as $val):?>
      		<option value="<?php echo $val['id'];?>" <?php if($search['registerChannel'] == $val['id']): echo "selected"; endif;?>><?php echo $val['name'];?></option>
      		<?php endforeach;?>
      	</select>
      </td>
      <th>注册方式：</th>
      <td>
          <select class="selectList w130" name="reg_type">
              <option value="0" <?php if(empty($search['reg_type']) || $search['reg_type'] == '0'){ echo "selected"; }?>>不限</option>
              <option value="1" <?php if($search['reg_type'] == '1'){ echo "selected"; }?>>账号密码</option>
              <option value="3" <?php if($search['reg_type'] == '3'){ echo "selected"; }?>>微信</option>
              <option value="4" <?php if($search['reg_type'] == '4'){ echo "selected"; }?>>短信验证码</option>
          </select>
      </td>
      <td >
          <a id="searchChaseOrder" href="javascript:void(0);" class="btn-blue ml20">查询</a>
      </td>
    <tr>
    </tr>
    </tbody>
  </table>
  <input type="hidden" name="fromType" value="<?php echo $fromType ?>" id="fromType"/>
  <?php if ($fromType == 'ajax'): ?><input type="hidden" name="uid" value="<?php echo $search['uid'] ?>"/><?php endif; ?>
  </form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="135" />
      <col width="128" />
      <col width="100" />
      <col width="80" />
      <col width="130" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
      <col width="60" />
      <col width="60" />
      <col width="60" />
      <col width="60" />
      <col width="60" />
    </colgroup>
    <thead>
        <tr>
            <td colspan="12">
                <div class="tal">
                    <strong>追号总额</strong>
                    <span><?php echo m_format($tj['0']);?> 元</span>
                    <strong class="ml20">已追总额</strong>
                    <span><?php echo m_format($tj['1']);?> 元</span>
                    <strong class="ml20">中奖总额(税前)</strong>
                    <span><?php echo m_format($tj['2']);?> 元</span>
                    <strong class="ml20">中奖总额(税后)</strong>
                    <span><?php echo m_format($tj['3']);?> 元</span>
                    <strong class="ml20">用户统计</strong>
                    <span><?php echo $tj['4'];?> 人</span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr>
      <th>追号订单编号</th>
      <th>用户名</th>
      <th>彩种</th>
      <th>玩法</th>
      <th>创建时间</th>
      <th>已追期数/总期数</th>
      <th>投注总额（元）</th>
      <th>中奖总额（税后）</th>
      <th>订单状态</th>
      <th>详情</th>
      <th>投注平台</th>
      <th>注册渠道</th>
      <th>注册方式</th>
    </tr>
    <?php foreach ($orders as $key => $order): ?>
    <tr>
        <td><?php echo $order['chaseId']; ?></td>
        <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $order['uid']; ?>"
               class="cBlue"><?php echo $order['uname']; ?></a></td>
        <td><?php echo $this->caipiao_cfg[$order['lid']]['name']; ?></td>
        <td><?php if ( ! empty($this->caipiao_cfg[$order['lid']]['play'])): echo print_playtype($order['lid'], $order['playType'], $this->caipiao_cfg[$order['lid']]['play']);
            else: echo "--";  endif; ?></td>
        <td><?php echo $order['created']; ?></td>
        <td><?php echo $order['chaseIssue']; ?>/<?php echo $order['totalIssue'];?></td>
        <td><?php echo m_format($order['money']); ?></td>
        <td><?php echo m_format($order['margin']);?></td>
        <td><?php echo $this->chase_manage_cfg[$order['status']];?></td>
        <td><a href="/backend/Management/chaseDetail/?id=<?php echo $order['chaseId']; ?>" class="cBlue" target="_blank">查看</a></td>
        <td><?php echo $platforms[$order['buyPlatform']];?></td>
        <td><?php echo $channels[$order['channel']]['name'];?></td>
        <td><?php echo ($order['reg_type'] <= 2) ? '账号密码' : ($order['reg_type'] == 3 ? '微信' : '短信验证码'); ?></td>
    </tr>
<?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="11">
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

<div class="page mt10 chase_info">
   <?php echo $pages[0] ?>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
        $("#searchChaseOrder").click(function(){
      		var start = $("input[name='start_time']").val();
      		var end = $("input[name='end_time']").val();
      		if(start > end){
      			alertPop('您选择的时间段错误，请核对后操作');
      			return false;
      		}
          if ($("#fromType").val() == "ajax") {
              $("#chase_info").load("/backend/Management/chaseManage?" + $("#search_form_chase").serialize() + "&fromType=ajax");
              return false;
          }
          $('#search_form_chase').submit();

    	  });

        $('.chase_info a').click(function () {
            if ($("#fromType").val() == "ajax") {
                var _this = $(this);
                $("#chase_info").load(_this.attr("href"));
                return false;
            }
            return true;
        });
    	
       	$(".Wdate1").focus(function(){
            dataPicker();
        });    
    }); 
</script>
<?php if ($fromType != 'ajax'): ?>
    </body>
    </html>
<?php endif; ?>