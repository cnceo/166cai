<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="javascript:;">用户成长管理</a>&nbsp;&gt;&nbsp;<a href="/backend/Growth/exchangeLog">兑换记录</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd">
	    <ul>
		    <li ><a href="/backend/Growth/pointMonitor">积分监测</a></li>
		    <li class="current"><a href="/backend/Growth/exchangeLog">兑换记录</a></li>
		    <li ><a href="/backend/Growth/stockManage">库存管理</a></li>
		    <li ><a href="/backend/Growth/pointDetail">积分明细</a></li>
	    </ul>
  	</div>
</div>
    <div class="data-table-filter mt10">
        <form action="/backend/Growth/exchangeLog" method="get" id="search_form">
            <table>
                <colgroup>
                    <col width="50"/>
                    <col width="140"/>
                    <col width="50"/>
                    <col width="200"/>
                    <col width="60"/>
                    <col width="410"/>
                </colgroup>
                <tbody>
                <tr>
                    <th>关键字：</th>
                    <td>
                       <input type="text" class="ipt w130" name="name" value="<?php echo $search['name'] ?>" placeholder="用户名..."/>
                    </td>
                    <th>红包类型：</th>
                    <td>
                        <select class="selectList w130" name="p_type">
                        	<option value="">不限</option>
                        	<option value="3">购彩红包</option>
                        	<option value="2">充值红包</option>
                        	<option value="1">彩金红包</option>
                        </select>
                    </td>
                    <th>领取时间：</th>
                    <td>
	                    <span class="ipt ipt-date w184">
	                        <input type="text" name='get_time_s'  value="<?php echo $search['get_time_s'] ?>" class="Wdate1"/><i></i>
	                    </span>
	                    <span class="ml8 mr8">至</span>
	                    <span class="ipt ipt-date w184">
	                        <input type="text" name='get_time_e' value="<?php echo $search['get_time_e'] ?>" class="Wdate1"/><i></i>
	                    </span>
                    </td>
                </tr>
                <tr>
                    <th>红包金额：</th>
                    <td>
                        <input type="text" class="ipt w130" name="money" value="<?php echo $search['money'] ?>"/>
                    </td>
                    <th>使用状态：</th>
                    <td>
                        <select class="selectList w130" name="use_status">
                            <option value="">不限</option>
                            <option value="1">未使用</option>
                            <option value="2">已使用</option>
                        </select>
                    </td>
                    <th>生效日：</th>
                    <td>
                        <span class="ipt ipt-date w184">
                        	<input type="text" name='valid_start' value="<?php echo $search['valid_start_s'] ?>" class="Wdate1"/><i></i>
                        </span>
                        <span class="ml8 mr8">至</span>
                        <span class="ipt ipt-date w184">
                        	<input type="text" name='valid_start_end' value="<?php echo $search['valid_start_e'] ?>" class="Wdate1"/><i></i>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>使用时间：</th>
                    <td colspan="3">
                        <span class="ipt ipt-date w184">
                        	<input type="text" name='use_time_s' value="<?php echo $search['use_time_s'] ?>" class="Wdate1"/><i></i>
                        </span>
                        <span class="ml8 mr8">至</span>
                        <span class="ipt ipt-date w184">
                        	<input type="text" name='use_time_e' value="<?php echo $search['use_time_e'] ?>" class="Wdate1"/><i></i>
                        </span>
                    </td>
                    <th>到期日：</th>
                    <td colspan="">
                        <span class="ipt ipt-date w184">
                        	<input type="text" name='valid_end_s' value="<?php echo $search['valid_end_s'] ?>" class="Wdate1"/><i></i>
                        </span>
                        <span class="ml8 mr8">至</span>
                        <span class="ipt ipt-date w184">
                        	<input type="text" name='valid_end_e' value="<?php echo $search['valid_end_e'] ?>" class="Wdate1"/><i></i>
                        </span>
                        <a id="searchTransction" href="javascript:void(0);" class="btn-blue ml35" >查询</a>
                    </td>
                </tr>

                </tbody>
            </table>
        </form>
    </div>

    <div class="data-table-list mt20">
        <table>
            <colgroup>
                <col width="90"/>
                <col width="100"/>
                <col width="78"/>
                <col width="80"/>
                <col width="78"/>
                <col width="85"/>
                <col width="112"/>
                <col width="120"/>
                <col width="90"/>
                <col width="110"/>
                <col width="110"/>
            </colgroup>
            <thead>
            <tr>
                <td colspan="11">
                    <div class="tal">
                        <strong>总人数</strong>
                        <span><?php echo $count['u']; ?> </span>
                        <strong class="ml20">消耗积分总额</strong>
                        <span><?php echo $count['v']; ?></span>
                        <strong class="ml20">红包总金额</strong>
                        <span><?php echo m_format($count['m']); ?> 元</span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>用户名</th>
                <th>领取时间</th>
                <th>红包类型</th>
                <th>红包金额</th>
                <th>消耗积分</th>
                <th>使用状态</th>
                <th>红包使用时间</th>
                <th>使用条件</th>
                <th>适用彩种</th>
                <th>生效日</th>
                <th>到期日</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($res as $key => $v): ?>
            <tr>
 				<td><a href="/backend/User/user_manage/?uid=<?php echo $v['uid'] ;?>" target="_blank"><?php echo $v['uname'] ;?></a></td>
				<td><?php echo $v['get_time']; ?></td>
				<td><?php echo $v['p_type']==1 ? '彩金红包' : '充值红包';?></td>
				<td><?php echo  m_format($v['money']);?>&nbsp;元</td>
				<td><?php echo $v['value'];?></td>
				<td><?php echo $v['status']==1?'未使用':'已使用';?></td>
				<td><?php echo $v['use_time'];?></td>
				<td><?php echo $v['use_desc'];?></td>
				<td><?php echo $v['c_name'];?></td>
				<td><?php echo $v['valid_start'];?></td>
				<td><?php echo $v['valid_end'];?></td>
			</tr>           		
            <?php endforeach ?>
            </tbody>
            <tfoot>

            <tr>
                <td colspan="9">
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
    <div class="page mt10 transactions_info">
        <?php echo $pages[0] ?>
    </div>
    <script src="/source/date/WdatePicker.js"></script>
    <script src="/caipiaoimg/src/layer/layer.js"></script>
    <script>
        var caipiao_cfg = jQuery.parseJSON('<?php echo json_encode($this->caipiao_cfg) ?>');
        //比较时间
        function compareDate(d1,d2)
        {
          return ((new Date(d1.replace(/-/g,"\/"))) > (new Date(d2.replace(/-/g,"\/"))));
        }
        $(function () {
        	$("#searchTransction").click(function(){
                //验证时间
                if(compareDate($('input[name=get_time_s]').val(),$('input[name=get_time_e]').val()))
                {
        　　　　　　layer.alert('你选择的时间段错误', {icon: 2,btn:'',title:'温馨提示',time:0});
        　　　　　　return false; 
                } 
                if(compareDate($('input[name=use_time_s]').val(),$('input[name=use_time_e]').val()))
                {
        　　　　　　layer.alert('你选择的时间段错误', {icon: 2,btn:'',title:'温馨提示',time:0});
        　　　　　　return false; 
                } 
                if(compareDate($('input[name=valid_end_s]').val(),$('input[name=valid_end_e]').val()))
                {
        　　　　　　layer.alert('你选择的时间段错误', {icon: 2,btn:'',title:'温馨提示',time:0});
        　　　　　　return false; 
                } 
                $('#search_form').submit();
          	});
        	
            $("#caipiao_play").bind('change', function () {
                play = caipiao_cfg[$(this).val()]['play'];
                if ($("#play_type").length > 0) {
                    $("#play_type").remove();
                }
                if (play != undefined) {
                    html = ' <select class="selectList w130"  name="playType" id="play_type">';
                    html += '<option value="0">全部</option>';
                    for (var key in play) {
                        html += '<option value="' + key + '">' + play[key]['name'] + '</option>';
                    }
                    html += '</select>';
                    $("#caipiao_play").after(html);
                }
            });
            $(".Wdate1").focus(function () {
                dataPicker();
            });

            $('#search_form').submit(function () {
                if ($("#fromType").val() == "ajax") {
                    $("#transactions_info").load("/backend/Transactions/index?" + $("#search_form").serialize() + "&fromType=ajax");
                    return false;
                }
                return true;
            });
            $('.transactions_info a').click(function () {
                if ($("#fromType").val() == "ajax") {
                    var _this = $(this);
                    $("#transactions_info").load(_this.attr("href"));
                    return false;
                }
                return true;
            });
        });

    </script>
<?php if ($fromType != 'ajax'): ?>
    </body>
    </html>
<?php endif; ?>