<?php
if ($fromType != 'ajax'): $this->load->view("templates/head");
endif;
?>
<div class="frame-container" style="margin-left:0;padding-left: 0px;">
    <?php
    if ($fromType != 'ajax'):
        ?>
        <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/manageUnited/">合买管理</a></div>
        <div class="mod-tab-hd mt20">
            <ul>
                <li class="current"><a href="/backend/Management/manageUnited/">合买管理</a></li>
                <li><a href="/backend/Management/managePlanner/">合买红人</a></li>
            </ul>
        </div>
    <?php endif; ?>
    <div class="data-table-filter mt10" style="width:960px">
        <form action="" method="get"  id="search_form_unitedorder">
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
                            <input type="text" class="ipt w222"  name="united_name" value="<?php echo $search['name']; ?>"  placeholder="用户名/合买订单编号/合买认购编号" />
                        </td>
                        <th>彩种玩法：</th>
                        <td>
                            <select class="selectList w120 mr20"  name="united_lid" id="united_caipiao_play">
                                <option value="">全部</option>
                                <?php foreach ($lottery as $key => $val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ($search['lid'] === "{$key}"): echo "selected";
                                    endif;
                                    ?>><?php echo $val; ?></option>
                                        <?php endforeach; ?>
                            </select>
                        </td>
                        <th class = "tar">投注平台：</th>
                        <td>
                            <select class="selectList w98" id="united_platformId" name="united_buyPlatform">
                                <?php foreach ($platforms as $key => $val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ((string) $key === ($search['buyPlatform'])): echo "selected";
                                    endif;
                                    ?>><?php echo $val; ?></option>
                                        <?php endforeach; ?>
                            </select>
                        </td>
                         <?php if ($fromType != 'ajax'){ ?>
                        <th>佣金比例：</th>
                        <td>
                            <select class="selectList w98" id="united_proportion" name="united_proportion">
                                <?php foreach ($proportion as $key => $val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ((string) $key === ($search['proportion'])): echo "selected";
                                    endif;
                                    ?>><?php echo $val; ?></option>
                                        <?php endforeach; ?>
                            </select>
                        </td>
                         <?php } ?>
                    </tr>
                    <tr>
                        <th>期次场次：</th>
                        <td>
                            <input type="text" class="ipt w222" name="united_issue" value='<?php echo $search['issue']; ?>' placeholder="期次场次" />
                        </td>
                        <th>订单金额：</th>
                        <td>
                            <input type="text" class="ipt w120" name="united_start_money" value='<?php echo $search['start_money']; ?>'/>
                            <span class="ml8 mr8">至</span>
                            <input type="text" class="ipt w120" name="united_end_money" value='<?php echo $search['end_money']; ?>'>
                        </td>
                        <th class="tar">注册渠道：</th>
                        <td>
                            <select class="selectList w130" name="united_channel">
                                <option value="">不限</option>
                                <?php foreach ($channels as $key=>$val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ($key == ($search['channel'])): echo "selected";
                                    endif;
                                    ?>><?php echo $val; ?></option>
                                        <?php endforeach; ?>
                            </select>
                        </td>
                        <?php if ($fromType != 'ajax'){ ?>
                        <th class="tar">保底设置：</th>
                        <td>
                            <select class="selectList w130" name="united_guarantee">
                                <option value="0" <?php
                                if (0 == ($search['guarantee'])): echo "selected";
                                endif;
                                ?>>不限</option>
                                <option value="1" <?php
                                if (1 == ($search['guarantee'])): echo "selected";
                                endif;
                                ?>>有保底</option>
                                <option value="2" <?php
                                if (2 == ($search['guarantee'])): echo "selected";
                                endif;
                                ?>>无保底</option>
                            </select>
                        </td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <th>订单状态：</th>
                        <td>
                            <select class="selectList w222 mr20"  name="united_status" id="united_status">
                                <?php foreach ($status as $key => $val): ?>
                                <?php if($fromType == 'ajax' && ($key==997 || $key==998)){ ?>
                                <?php }else{ ?>
                                    <option  value="<?php echo $key ?>" <?php
                                    if ((string) $key === ($search['status'])): echo "selected";
                                    endif;
                                    ?>><?php echo $val; ?></option>
                                <?php } ?>
                                         <?php endforeach; ?>
                            </select>
                        </td>
                        <?php if ($fromType == 'ajax'){ ?>
                            <th>购买方式：</th>
                            <td>
                                <select class="selectList w130" name="orderType">
                                    <option value="-1">不限</option>
                                    <option value="1" <?php
                                    if (1 == ($search['orderType'])): echo "selected";
                                    endif;
                                    ?>>发起合买</option>
                                    <option value="2" <?php
                                    if (2 == ($search['orderType'])): echo "selected";
                                    endif;
                                    ?>>参与合买</option>
                                    <option value="3" <?php
                                    if (3 == ($search['orderType'])): echo "selected";
                                    endif;
                                    ?>>定制跟单</option>
                                </select>
                            </td>
                        <?php } ?>  
                        <th>发起时间：</th>
                        <td>
                            <span class="ipt ipt-date w184"><input type="text" name='united_start_time' value="<?php echo $search['start_time'] ? $search['start_time'] : $searchTime['start_time']; ?>" class="Wdate1" /><i></i></span>
                            <span class="ml8 mr8">至</span>
                            <span class="ipt ipt-date w184"><input type="text" name='united_end_time' value="<?php echo $search['end_time'] ? $search['end_time'] : $searchTime['end_time']; ?>" class="Wdate1" /><i></i></span>
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
                        <th></th>
                        <td>
                            <?php if ($fromType != 'ajax'){ ?>
                            <input type="checkbox" name="webGurantee" value="1" <?php
                            if ($search['webGurantee'] == '1'): echo "checked";
                            endif;
                            ?>>网站已保底
                            <?php } ?>
                            <a id="united_searchOrder" href="javascript:void(0);" class="btn-blue mr20" onclick="">查询</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="fromType" value="<?php echo $fromType ?>"  id="fromType" />
            <?php if ($fromType == 'ajax'): ?><input type="hidden" name="uid"
                       value="<?php echo $search['uid'] ?>"/><?php endif; ?>
        </form>
    </div>
    <div class="data-table-list mt20">
        <table>
            <colgroup>
                <col width="40" />
                <?php if ($fromType != 'ajax') { ?> <col width="40" /> <?php } else { ?> <col width="80" /> <?php } ?>
                <col width="100" />
                <col width="80" />
                <col width="80" />
                <col width="70" />
                <col width="110" />
                <col class="filter-item" width="100" />
                <col width="100" />
                <col class="filter-item" width="100" />
                <col width="100" />
                <col width="60" />
                <col width="60" />
                <col width="60" />
            </colgroup>
            <tr>
                <td <?php if ($fromType != 'ajax') { ?> colspan="15" <?php } else { ?> colspan="16" <?php } ?>>
                    <div class="tal">
                        <?php if ($fromType != 'ajax') { ?> 
                        <strong>等待满员方案</strong>
                        <span><?php echo (int)$count['notFull']; ?> 个</span>
                        <strong class="ml10">已满员方案</strong>
                        <span><?php echo (int)$count['full']; ?> 个</span>
                        <?php } ?>
                        <strong class="ml10">订单总额</strong>
                        <span><?php echo m_format((int)$count['totalMoney']); ?> 元</span>
                        <strong class="ml10">出票总额</strong>
                        <span><?php echo m_format((int)$count['drawMoney']); ?> 元</span>
                        <?php if ($fromType != 'ajax') { ?> 
                        <strong class="ml10">中奖总额（税前）</strong>
                        <span><?php echo m_format((int)$count['bonus']); ?> 元</span>
                        <?php } ?>
                        <strong class="ml10">中奖总额（税后）</strong>
                        <span><?php echo m_format((int)$count['margin']); ?> 元</span>
                        <?php if ($fromType != 'ajax') { ?> 
                        <strong class="ml10">用户统计</strong>
                        <span><?php echo (int)$count['countUid']; ?> 人</span>
                        <?php } ?>
                        <?php if ($fromType != 'ajax'): ?>
                            <a class="btn-red ml20" id="setTop" data-type="1">方案置顶</a>
                            <a class="btn-white ml10" id="delTop" data-type="0">取消置顶</a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>
        <table id="tablesorter" class="tablesorter">
            <colgroup>
                <?php if ($fromType != 'ajax') { ?> <col width="20" /><?php } ?>
                <?php if ($fromType != 'ajax') { ?> <col width="40" /> <?php } else { ?> <col width="80" /> <?php } ?>
                <col width="150" />
                <col width="80" />
                <col width="80" />
                <col width="70" />
                <col width="140" />
                <col class="filter-item" width="100" />
                <col width="110" />
                <col class="filter-item" width="100"/>
                <col width="100" />
                <col width="60" />
                <col width="60" />
                <col width="60" />
                <col width="60" />
            </colgroup>
            <?php if ($fromType != 'ajax'){ ?>
            <thead>
                <tr>
                    <th></th>
                    <th>置顶</th>
                    <th>合买订单编号</th>
                    <th>发起人</th>
                    <th>彩种</th>
                    <th>期次场次</th>
                    <th>发起时间</th>
                    <th>合买方案金额</th>
                    <th>中奖金额（税后）</th>
                    <th>进度+保底</th>
                    <th>订单状态</th>
                    <th>详情</th>
                    <th>投注平台</th>
                    <th>注册渠道</th>
                    <th>注册方式</th>
                </tr>
            </thead>
            <?php foreach ($orders as $key => $order): ?>
                <tr id="orders">
                    <td><?php if(!in_array($order['status'], array(0,20,600,610,620,1000,2000))){ ?><?php if($order['buyTotalMoney']<$order['money']){?><input name="order" type="radio" value="<?php echo $order['id']; ?>"><?php } ?><?php } ?></td>
                    <td>
                        <?php if ($order['isTop'] > 0) { ?>
                            <?php echo '<font style="color:red;">顶</font>'; ?>
                        <?php }?>
                    </td>
                    <td><?php echo $order['orderId'] ?></td>
                    <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $order['uid'] ?>"
                           class="cBlue"><?php echo $order['uname'] ?></a></td>
                    <td><?php echo $this->caipiao_cfg[$order['lid']]['name'] ?></td>
                    <td><?php echo $order['issue'] ?></td>
                    <td><?php echo $order['created'] ?></td>
                    <td><?php echo m_format($order['money']) ?></td>
                    <td><?php echo m_format($order['orderMargin']) ?></td>
                    <td><?php echo (round($order['buyTotalMoney'] / $order['money'], 4) * 100) . '%+' . (floor($order['guaranteeAmount'] * 100 / $order['money'])) . '%'; ?></td>
                    <td><?php if($order['status']!=600 && $order['status']!=40 && $order['status']!=2000){ ?><?php echo $this->caipiao_status_cfg[$order['status']][1]; ?><?php }elseif($order['status']==40){ echo '等待出票';} elseif($order['status']==600){ ?>方案撤单<?php }else{ ?><?php echo $this->caipiao_ms_cfg[$order['status']][$order['my_status']][0]; ?><?php } ?></td>
                    <td><a target="_blank" class="cBlue" href="/backend/Management/unitedOrderDetail/?id=<?php echo $order['orderId'] ?>">查看</a></td>
                    <td><?php echo ($order['buyPlatform'] == 0) ? "网页" : ($order['buyPlatform'] == 1 ? "Android" : ($order['buyPlatform'] == 2 ? "IOS" : "M版")); ?></td>
                    <td><?php echo $channels[$order['channel']]; ?></td>
                    <td><?php echo ($order['reg_type'] <= 2) ? '账号密码' : ($order['reg_type'] == 3 ? '微信' : '短信验证码'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php }else{ ?>
            <thead>
                <tr>
                    <th>合买订单编号</th>
                    <th>合买方案金额</th>
                    <th>发起人</th>
                    <th>彩种</th>
                    <th>期次场次</th>
                    <th>购买方式</th>
                    <th>认购编号</th>
                    <th>认购时间</th>
                    <th>认购金额</th>
                    <th>中奖金额（税后）</th>
                    <th>订单状态</th>
                    <th>详情</th>
                    <th>投注平台</th>
                    <th>注册渠道</th>
                    <th>注册方式</th>
                </tr>
            </thead>
            <?php foreach ($orders as $key => $order): ?>
                <tr id="orders">
                    <td><?php echo $order['orderId'] ?></td>
                    <td><?php echo m_format($order['money']) ?></td>
                    <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $order['puid'] ?>"
                           class="cBlue"><?php echo $order['uname'] ?></a></td>
                    <td><?php echo $this->caipiao_cfg[$order['lid']]['name'] ?></td>
                    <td><?php echo $order['issue'] ?></td>
                    <td><?php echo ($order['orderType'] == 1) ? "发起合买" : (($order['subOrderType'] == 1) ? '定制跟单' : '参与合买'); ?></td>
                    <td><?php echo $order['subscribeId']; ?></td>
                    <td><?php echo $order['created'] ?></td>
                    <td><?php echo m_format($order['buyMoney']); ?></td>
                    <td><?php echo m_format($order['margin']); ?></td>
                    <td><?php if($order['status']!=600 && $order['status']!=40 && $order['status']!=2000){ ?><?php echo $this->caipiao_status_cfg[$order['status']][1]; ?><?php }elseif($order['status']==40){ echo '等待出票';} elseif($order['status']==600){ ?>方案撤单<?php }else{ ?><?php echo $this->caipiao_ms_cfg[$order['status']][$order['my_status']][0]; ?><?php } ?></td>
                    <td><a target="_blank" class="cBlue" href="/backend/Management/unitedOrderDetail/?id=<?php echo $order['orderId'] ?>">查看</a></td>
                    <td><?php echo ($order['buyPlatform'] == 0) ? "网页" : ($order['buyPlatform'] == 1 ? "Android" : ($order['buyPlatform'] == 2 ? "IOS" : "M版")); ?></td>
                    <td><?php echo $channels[$order['channel']]; ?></td>
                    <td><?php echo ($order['reg_type'] <= 2) ? '账号密码' : ($order['reg_type'] == 3 ? '微信' : '短信验证码'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php } ?>
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
    <div class="page mt10 united_order">
        <?php echo $pages[0];?>
    </div>
    <div class="pop-dialog" id="alertPop">
        <div class="pop-in">
            <div class="pop-head">
                <h2>提示</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
                <div class="data-table-filter del-percent" id="alertBody" style="text-align: center">
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-b-white mlr15 pop-cancel">确认</a>
            </div>
        </div>
    </div>
    <div class="pop-dialog" id="forceDistributionPop" style="width:200px;">
        <div class="pop-in">
            <div class="pop-body">
                <div class="data-table-list">
                    <table>
                        <div id="showAlert" style="text-align:center;font-size:20px;font-weight:bolder"></div>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="forceDistSubmit" data-type="0">确认</a>
                <a href="javascript:;" class="btn-b-white mlr15 pop-cancel" id="forceDistCancel">取消</a>
            </div>
        </div>
    </div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<!-- 字段排序js -->
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tablesorter.css?v=7">
<script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery.tablesorter.js"></script>
<script>
    var united_caipiao_cfg = jQuery.parseJSON('<?php echo json_encode($this->caipiao_cfg) ?>');
    var united_mystatus_cfg = jQuery.parseJSON('<?php echo json_encode($this->caipiao_ms_cfg) ?>');
    var united_play_type = '<?php echo $search['playType'] ?>';
    var united_s_my_status = '<?php echo $search['my_status'] ?>';
    $(function () {
        <?php if ($fromType != 'ajax'): ?>
        $.tablesorter.addParser({
           id: "jdbd", 
           is: function(s){
              return false;
           },
           format: function(s){
               var num = s.match(/(\d+)\%\+(\d+)/);
              return parseInt(num[1], 10)+parseInt(num[2], 10);
           },
           type: "numeric" 
        });
           $('#tablesorter').tablesorter({headers: {0: {sorter: false}, 1: {sorter: false}, 2: {sorter: false}, 3: {sorter: false}, 4: {sorter: false}, 5: {sorter: false}, 6: {sorter: false}, 8: {sorter: false}, 9 : {sorter:'jdbd'}, 10: {sorter: false}, 11: {sorter: false}, 12: {sorter: false}, 13: {sorter: false}}});
           
        <?php endif; ?>
        $("#united_caipiao_play").bind('change', function () {
            if ($("#united_play_type").length > 0 || $(this).val() == 0)
            {
                $("#united_play_type").remove();
                if ($(this).val() == 0)
                    return;
            }
            united_play = united_caipiao_cfg[$(this).val()]['play'];
            if (united_play != undefined)
            {
                united_html = ' <select class="selectList w120"  name="united_playType" id="united_play_type">';
                united_html += '<option value="">全部</option>';
                for (var key in united_play)
                {
                    united_html += '<option value="' + key + '">' + united_play[key]['name'] + '</option>';
                }
                united_html += '</select>';
                $("#united_caipiao_play").after(united_html);
            }
        });

        $("#united_searchOrder").click(function () {
            var start = $("input[name='united_start_time']").val();
            var end = $("input[name='united_end_time']").val();
            if (start > end) {
                alertPop('您选择的时间段错误，请核对后操作');
                return false;
            }
            if ($("#fromType").val() == "ajax")
            {
                $("#united_order").load("/backend/Management/manageUnited?" + $("#search_form_unitedorder").serialize() + "&fromType=ajax");
                return false;
            }
            $('#search_form_unitedorder').submit();
        });

        $(".Wdate1").focus(function () {
            dataPicker();
        });

        $('#search_form_unitedorder').submit(function () {
            if ($("#fromType").val() == "ajax")
            {
                $("#united_order").load("/backend/Management/manageUnited?" + $("#search_form_unitedorder").serialize() + "&fromType=ajax");
                return false;
            }
            return true;
        });
        $('.united_order a').click(function () {
            if ($("#fromType").val() == "ajax")
            {
                var _this = $(this);
                $("#united_order").load(_this.attr("href"));
                return false;
            }
            return true;
        });

        $("#united_status").bind("change", function () {
            if ($(this).val() == '2000')
            {
                united_mstatus = united_mystatus_cfg[$(this).val()];
                if (united_mstatus != undefined)
                {
                    $("#united_my_stauts").remove();
                    united_html = ' <select class="selectList w120"  name="united_my_stauts" id="united_my_stauts">';
                    united_html += '<option value="">全部</option>';
                    for (var key in united_mstatus)
                    {
                        if(key<=3){
                        united_html += '<option value="' + key + '">' + united_mstatus[key][0] + '</option>';
                        }
                    }
                    united_html += '</select>';
                    $("#united_status").after(united_html);
                }
            } 
            else
            {
                if ($("#united_my_stauts").length > 0)
                {
                    $("#united_my_stauts").remove();
                }
            }
        });

        $("#united_caipiao_play").change();
        if ($("#united_play_type").length > 0 && united_play_type != '')
        {
            $("#united_play_type").val(united_play_type);
        }
        $("#united_status").change();
        if ($("#united_my_stauts").length > 0)
        {
            $("#united_my_stauts").val(united_s_my_status);
        }
        $('#setTop,#delTop').click(function () {
            var top = $(this).attr('data-type');
            var id = 0;
            $("#orders input[name=order]").each(function () {
                if ($(this).attr("checked")) {
                    id = $(this).val();
                }
            });
            $.ajax({
                type: "post",
                url: '/backend/Management/updateTop?judge=1',
                data: {top:top, id:id},
                dataType: "json",
                success: function (data) {
                    if (data.status == 'success') {
                        if (data.message == 'success') {
                            location.reload();
                            return false;
                        }
                        $("#showAlert").html("是否要"+(top != 1 ? '取消' : '')+"置顶方案？");
                        $("#forceDistSubmit").attr("data-type", top)
                        popdialog("forceDistributionPop");
                    } else {
                        alertPop(data.message);
                        return false;
                    }
                }
            });
        });

        $('#forceDistSubmit').click(function () {
            $('#forceDistributionPop').hide();
            var top = $(this).attr('data-type');
            var id = 0;
            $("#orders input[name=order]").each(function () {
                if ($(this).attr("checked")) {
                    id = $(this).val();
                }
            });
            $.ajax({
                type: "post",
                url: '/backend/Management/updateTop?judge=0',
                data: {top:top,id:id},
                dataType: "json",
                success: function (data) {
                    if (data.status == 'success') {
                        alertPop('恭喜你,操作成功!');
                        location.reload();
                    } else {
                        alertPop(data.message);
                        return false;
                    }
                }
            });
        });
    });
    function alertPop(content) {
        $("#alertBody").html(content);
        popdialog("alertPop");
    }

</script>
<?php if ($fromType != 'ajax'): ?>
    </body>
    </html>
<?php endif; ?>