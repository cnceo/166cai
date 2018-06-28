<?php
$this->load->view("templates/head");

function bonusComputed($status) {
    $isComputed = ($status >= 1000);

    return $isComputed;
}

$statusArr = array(
    0 => '等待出票',
    10 => '创建',
    20 => '过期未付款',
    21 => '过期已付款',
    30 => '付款失败',
    40 => '已付款',
    200 => '未满足条件',
    240 => '出票中',
    500 => '出票成功',
    600 => '出票失败',
    1000 => '未中',
    2000 => '中奖'
);
?>
<div class="path">您的位置：运营管理&nbsp;>&nbsp;<a href="/backend/Management/manageOrder">订单管理</a>&nbsp;>&nbsp;<a
        href="">详情</a>
</div>
<div class="data-table-log mt20">
    <table>
        <colgroup>
            <col width="100"/>
            <col width="200"/>
            <col width="100"/>
            <col width="200"/>
            <col width="100"/>
            <col width="117"/>
            <col/>
        </colgroup>
        <tbody>
            <tr class="title">
                <th colspan="8">订单信息</th>
            </tr>
            <?php if ($order): ?>
                <tr>
                    <th>订单编号：</th>
                    <td><?php echo $order['orderId'] ?></td>
                    <th>购买方式：</th>
                    <td>合买</td>
                    <th>投注方式：</th>
                    <td><?php
                        if ($order['betTnum'] > 1): echo "复式投注";
                        else: echo "单式投注";
                        endif;
                        ?></td>
                    <th>用户名：</th>
                    <td><?php echo $order['userName'] ?></td>
                </tr>
                <tr>
                    <th>彩种：</th>
                    <td><?php echo $this->caipiao_cfg[$order['lid']]['name'] ?></td>
                    <th>期次：</th>
                    <td><?php echo $order['issue'] ?></td>
                    <th>订单状态：</th>
                    <td>
                        <?php if($order['status']!=600 && $order['status']!=40 && $order['status']!=2000){ ?><?php echo $this->caipiao_status_cfg[$order['status']][1]; ?><?php }elseif($order['status']==40){ echo '等待出票';} elseif($order['status']==600){ ?>方案撤单<?php }else{ ?><?php echo $this->caipiao_ms_cfg[$order['status']][$order['my_status']][0]; ?><?php } ?>
                        <?php if(($order['cstate'] & 256) != 0):?>(<a href="javascript:;" class="cBlue" id="sendmail" data-val="<?php echo $order['orderId'];?>">补发邮件</a>)<?php endif;?>
                    </td>
                    <th>创建时间：</th>
                    <td><?php echo $order['created'] ?></td>
                </tr>
                <tr>
                    <th>投注倍数：</th>
                    <td><?php echo $order['multi'] ?></td>
                    <th>投注金额：</th>
                    <td><?php echo m_format($order['money']) ?></td>
                    <th>开奖号码：</th>
                    <td><?php echo $order['awardNum'] ?></td>
                    <th>税前奖金：</th>
                    <td>
                        <?php
                        echo (!in_array($order['status'], array('1000')) && empty($order['bonus'])) ? "--" : m_format($order['bonus']);
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>税后奖金：</th>
                    <td>
                        <?php
                        echo (!in_array($order['status'], array('1000')) && empty($order['margin'])) ? "--" : m_format($order['margin']);
                        ?>
                    </td>
                    <th>奖金核对：</th>
                    <td><?php echo $order['consistencyInfo'] ?></td>
                    <th>出票订单：</th>
                    <td><?php echo $order['messageId'] ?></td>
                    <th>投注平台：</th>
                    <td><?php echo ($order['buyPlatform'] == 0) ? "网页" : ($order['buyPlatform'] == 1 ? "Android" : ($order['buyPlatform'] == 2 ? "IOS" : "M版")); ?> </td>
                </tr>
                <tr>
                    <?php if (($order['activity_ids'] & 4) == 4): ?>
                        <th>加奖金额：</th>
                        <?php if (($order['activity_status'] & 4) == 4): ?>
                            <td><?php echo number_format(ParseUnit($order['add_money'], 1), 2); ?> </td>
                        <?php else: ?>
                            <td>-- </td>
                        <?php endif; ?>
                    <?php endif; ?>
                    <th>玩法：</th>
                    <td><?php echo $order['playTypeName']; ?></td>
                    <th>认购进度：</th>
                    <td><?php echo round($order['buyTotalMoney']/$order['money']*100,2).'%('.number_format($order['buyTotalMoney']/100,2).'元)'; ?></td>
                    <th>承诺保底：</th>
                    <td><?php echo round($order['guaranteeAmount']/$order['money']*100,2).'%('.number_format($order['guaranteeAmount']/100,2).'元)'; ?></td>
                    <th>实际保底：</th>
                    <td><?php echo number_format($order['actualguranteeAmount']/100,2).'元'; ?></td>
                </tr>
                <tr>
                    <th>网站保底：</th>
                    <td><?php echo round($order['webguranteeAmount']/$order['money']*100,2).'%('.number_format($order['webguranteeAmount']/100,2).'元)'; ?></td>
                    <th>保密设置：</th>
                    <td><?php if ($order['openStatus'] == 0) { ?>
                            <?php echo '公开'; ?>
                        <?php } elseif ($order['openStatus'] == 1) { ?>
                            <?php echo '仅对跟单者公开'; ?>
                        <?php } else { ?>
                            <?php echo '截止后公开'; ?>
                        <?php } ?>    
                    </td>
                    <th>盈利佣金：</th>
                    <td><?php echo $order['commissionRate'].'%' ?><?php echo $order['status']==2000?'('.number_format($order['commission']/100,2).'元)':''; ?></td>
                    <th>战绩奖励：</th>
                    <td><?php echo $order['points']?></td>
                </tr>
                <tr>
                    <th>是否软删除：</th>
                    <td><?php echo ($order['is_hide'] & 1) ? '是' : '否'; ?></td>
                    <th>合买宣言：</th>
                    <td><?php echo $order['introduction']; ?></td>
                </tr>
            <?php endif; ?>
            <tr class="hr">
                <td colspan="6">
                    <div class="hr-dashed"></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="tab-nav mt20">
    <ul class="clearfix">
        <li class="active load_info"><a
                href="javascript:stab('orderDetail', '/backend/Management/orderDetail/?id=<?php echo $order['id']; ?>');"><span>投注内容</span></a>
        </li>
        <li class="load_info"><a
                href="javascript:stab('splitDetail', 'Order/splitDetail/orderId=<?php echo $order['orderId']; ?>')"><span>出票明细</span></a>
        </li>
        <li class="load_info"><a
                href="javascript:stab('unitedDetail', 'Order/unitedDetail/orderId=<?php echo $order['orderId']; ?>')"><span>参与用户</span></a>
        </li>
        <li class="load_info"><a
                href="javascript:stab('lsDetail', '')"><span>乐善奖明细</span></a>
        </li>
        <li>
            <div style="margin-left:200px;">
                <?php if(!in_array($order['status'],array(610,620,600))){ ?>
                <button id="cancelOrder" class="btn-blue">撤单</button>
                <?php } ?>
            </div>
        </li>
    </ul>
</div>
<div class="tab-content">
    <div class="item" style="display:block;" id="orderDetail" has_load='true'>
        <div class="data-table-log bd-width-item">
            <table>
                <colgroup>
                    <col width="112"/>
                    <col width="262"/>
                    <col width="112"/>
                    <col width="209"/>
                    <col width="112"/>
                    <col/>
                </colgroup>
                <tbody>
                    <tr class="title">
                        <th colspan="6">出票信息</th>
                    </tr>
<?php if ($order): ?>
                        <tr>
                            <th>出票商ID：</th>
                            <td><?php echo $order['ticket_seller'] ?></td>
                            <th>票号：</th>
                            <td><?php echo $order['ticket_id'] ?></td>
                            <th>出票时间：</th>
                            <td><?php echo $order['ticket_time'] ?></td>
                        </tr>
                        <tr>
                            <th align="top">投注内容：</th>
                            <td colspan="5">
                                <textarea class="textarea w830" rows="10" cols="30" id=""
                                          name=""><?php echo $order['codes'] ?></textarea>
                            </td>
                        </tr>
<?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="item" style="display: none" id="split_detail" has_load="false">
        <div class="data-table-list mt20">
            <table>
                <colgroup>
                    <col width="5%"/>
                    <col width="10%"/>
                    <col width="15%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col/>
                </colgroup>
                <tbody>
                    <tr>
                        <th>序号</th>
                        <th>子订单号</th>
                        <th>出票内容</th>
                        <th>出票商</th>
                        <th>出票状态</th>
                        <th>出票时间</th>
                        <th>拆票金额</th>
                        <th>中奖注数</th>
                        <th>计奖奖金</th>
                        <th>计奖税后</th>
                        <th>出票金额</th>
                        <th>票商税前奖金</th>
                        <th>票商税后奖金</th>
                    </tr>
                    <?php if ($subOrders): ?>
                        <?php $i = 0; ?>
    <?php foreach ($subOrders as $subOrder): ?>
                            <tr>
                                <td><?php echo ++$i; ?></td>
                                <td style="<?php if ($subOrder['error_num'] > 0): ?>color:red;<?php endif; ?>"><?php echo $subOrder['subId']; ?></td>
                                <td><?php echo $subOrder['content']; ?></td>
                                <td><?php echo $subOrder['ticketSeller']; ?></td>
                                <td style="<?php if ($subOrder['error_num'] > 0): ?>color:red;<?php endif; ?>"><?php echo $statusArr[$subOrder['status']]; ?><?php
                                    if ($subOrder['cancelFlag'] && $subOrder['status'] == 600) {
                                        echo '(人工)';
                                    }
                                    ?><?php
                                    if ($subOrder['error_num'] > 0) {
                                        echo '(' . $subOrder['error_num'] . ')';
                                    }
                                    ?></td>
                                <td><?php echo $subOrder['ticketTime']; ?></td>
                                <td><?php echo m_format($subOrder['money']) . '元'; ?></td>
                                <td><?php echo bonusComputed($subOrder['status']) ? $subOrder['stakeNum'] : '--'; ?></td>
                                <td><?php echo ($subOrder['status'] == 1000 || $subOrder['status'] == 2000) ? m_format($subOrder['bonus']) . '元' : '--'; ?></td>
                                <td><?php echo ($subOrder['status'] == 1000 || $subOrder['status'] == 2000) ? m_format($subOrder['margin']) . '元' : '--'; ?></td>
                                <td><?php echo ($order['lid'] == 21406 ? $subOrder['ticketMoney'] <= '2' : $subOrder['ticketMoney'] == '0') ? "--" : m_format($subOrder['ticketMoney']) . '元'; ?></td>
                                <td><?php echo bonusComputed($subOrder['status']) ? (($order['lid'] == 21406 ? $subOrder['cpstate'] <= '2' : $subOrder['cpstate'] == 0) ? "--" : (m_format($subOrder['ticketBonus']) . '元')) : '--'; ?></td>
                                <td><?php echo bonusComputed($subOrder['status']) ? (($order['lid'] == 21406 ? $subOrder['cpstate'] <= '2' : $subOrder['cpstate'] == 0) ? "--" : (m_format($subOrder['ticketMargin']) . '元')) : '--'; ?></td>
                            </tr>
                        <?php endforeach; ?>
<?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="item" style="display: none" id="unitedDetail" has_load="true">

        <div class="data-table-list mt20">
            <table>
                <colgroup>
                    <col width="5%"/>
                    <col width="10%"/>
                    <col width="15%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                </colgroup>
                <tbody>
                    <tr>
                        <td colspan="3">
                            <div style="float:left;">
                                已有<?php echo $order['popularity']; ?>人次  共认购了<?php echo number_format($order['buyTotalMoney']/100,2); ?>元
                            </div>
                        </td>
                        <td colspan="6">
                            <div style="float:right;">
                                <form method="post" action="/backend/Management/unitedOrderDetail/?id=<?php echo $order['orderId'] ?>">
                                    参与方式：
                                    <select class="selectList w98" id="subOrderType" name="subOrderType" style="vertical-align:middle">
                                        <option value="-1">不限</option>
                                        <?php foreach ($subOrderTypes as $key => $val): ?>   
                                        <option value="<?php echo $key; ?>" <?php
                                        if ((string) $key === ($searchData['subOrderType'])): echo "selected";
                                        endif;
                                        ?>><?php echo $val; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input value="<?php echo $searchData['username']; ?>" id="username" name="username" placeholder="用户名"  class="ipt w120" style="vertical-align:middle"><button class="btn-blue ml10 mr10" type="submit">搜索</button>
                                </form>
                            </div>  
                        </td>
                    </tr>
                    <tr>
                        <th>序号</th>
                        <th>认购编号</th>
                        <th>用户名</th>
                        <th>认购时间</th>
                        <th>认购金额(元)</th>
                        <th>税后奖金(元)</th>
                        <th>投注平台</th>
                        <th>参与方式</th>
                        <th>出票邮件</th>
                    </tr>
<?php foreach ($unitOrders as $key => $unitOrder): ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo $unitOrder['subscribeId']; ?></td>
                            <td><?php if($unitOrder['uid']!=0){ ?><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $unitOrder['uid'] ?>"
                            class="cBlue"><?php echo $unitOrder['uname'] ?></a><?php } ?><?php if($unitOrder['uid']==0) { echo '166彩票(保底)';}?></td>
                            <td><?php echo $unitOrder['created']; ?></td>
                            <td><?php echo number_format($unitOrder['buyMoney']/100,2); ?></td>
                            <td><?php echo ($unitOrder['status'] == 1000 || $unitOrder['status'] == 2000) ? number_format($unitOrder['margin']/100, 2): '--'; ?></td>
                            <td><?php echo ($unitOrder['buyPlatform'] == 0) ? "网页" : ($unitOrder['buyPlatform'] == 1 ? "Android" : ($unitOrder['buyPlatform'] == 2 ? "IOS" : "M版")); ?></td>
                            <td><?php echo ($unitOrder['orderType'] == 2 && $unitOrder['subOrderType'] == 1) ? '定制跟单' : '自己参与'; ?></td>
                            <td><?php if ($unitOrder['cstate'] & 128) {?><a href="javascript:;" onclick="sendJoinedEmail('<?php echo $unitOrder['subscribeId']?>')">补发邮件</a><?php }?></td>
                        </tr>
<?php endforeach; ?>
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
                </tbody>
            </table>
        </div>
        <div class="page mt10 united_order">
        <?php echo $pages[0];?>
    </div>
    </div>
    <!-- 大乐透乐善码 -->
    <div class="item" style="display: none" id="lsDetail" has_load="false">
        <div class="data-table-list mt20">
            <table>
                <colgroup>
                    <col width="40%"/>
                    <col width="30%"/>
                    <col width="30%"/>
                    <col/>
                </colgroup>
                <tbody>
                    <tr>
                        <th>订单方案</th>
                        <th>对应乐善码</th>
                        <th>奖金</th>
                    </tr>
                    <?php if ($lsDetail['detail']): ?>
                        <?php foreach ($lsDetail['detail'] as $tickets): ?>
                        <tr>
                            <td><?php echo $tickets['codes']; ?></td>
                            <td><?php echo $tickets['awardNum']; ?></td>
                            <td><?php echo m_format($tickets['margin']) . '元'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="pop-dialog" id="forceDistributionPop" style="width:200">
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
</div>
<script>
    var method='<?php echo $_SERVER['REQUEST_METHOD']?>';
    var p='<?php echo intval($this->input->get("p"));?>';
    $(function () {
        $(".tab-nav li").bind("click", function () {
            var i = $(this).index();
            $(this).addClass('active').siblings().removeClass('active');
            $(this).parents(".tab-nav").next(".tab-content:eq(0)").find(".item").eq(i).show().siblings().hide();
        });
        if(method=='POST' || p>0){
            $(".tab-nav li:eq(2)").addClass('active').siblings().removeClass('active');
            $(".tab-nav li:eq(2)").parents(".tab-nav").next(".tab-content:eq(0)").find(".item").eq(2).show().siblings().hide();
        }
        $("#cancelOrder").click(function () {
            $("#showAlert").html("撤单则整个方案撤单，是否继续撤单？");
            popdialog("forceDistributionPop");
        });
        $("#forceDistSubmit").click(function () {
            $('#forceDistributionPop').hide();
            var id = '<?php echo $order['orderId']; ?>';
            $.ajax({
                type: "post",
                url: '/backend/Management/cancelOrder',
                data: {id:id},
                dataType: "json",
                success: function (data) {
                    if (data.status == 'success') {
                        $.ajax({
                            type: "post",
                            url: '/api/order/cancelOrder',
                            data: {id:id, type:1},
                            dataType: "json",
                            success: function (data) {
                                if (data.status == 'success') {
                                    alertPop('撤单成功!');
                                    location.reload();
                                    return false;
                                } else {
                                    alertPop(data.message);
                                    return false;
                                }
                            }
                        });
                    } else {
                        alertPop(data.message);
                        return false;
                    }
                }
            });
        });
        
        $('#sendmail').click(function(){
            var orderId = $(this).attr("data-val");
            $.ajax({
                type: 'post',
                url:  '/backend/Management/supplyEmail',
                data: {orderId:orderId,united:1},
                dataType : 'json',
                success: function(response) {
                    if(response.status == 'y'){
                        alert(response.message);
                    }else{
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('网络异常，请稍后再试');
                }
            });
        });
    });
    function stab(ele, url) {
        if ($("#" + ele).attr("has_load") == 'false') {
            if(url){
                $("#" + ele).load("/backend/" + url + "&fromType=ajax", function () {
                    console.log("/backend/" + url + "&fromType=ajax");
                    $("#" + ele).attr("has_load", 'true')
                });
            }else{
                $("#" + ele).attr("has_load", 'true')
            }
        }
    }

    function alertPop(content) {
        $("#alertBody").html(content);
        popdialog("alertPop");
    }

    function sendJoinedEmail(subid) {
        if (confirm('确认给该用户补发邮件吗？')) {
            $.post('/backend/Management/sendJoinedEmail', {suborderId:subid}, function(){
                alert('补发成功！');
            })
        }
    }
</script>
</body>
</html>