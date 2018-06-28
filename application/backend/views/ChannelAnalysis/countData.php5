<?php $this->load->view("templates/head") ?>
<!-- 引入组件库 -->
<style type="text/css">
	.mod-tab-bd ul li{display: block;}
    .tip-bg-color{background: #f4f4f4 !important}
</style>
<?php
$platform = array(
    '1' => '网页',
    '2' => 'Android',
    '3' => 'iOS',
    '4' => 'M版'
);
$settleMode = array('1'=>'CPA', '2'=>'CPS');
?>
<div id="app">
    <div class="path">您的位置：<a href="javascript:;">渠道分析</a>&nbsp;&gt;&nbsp;<a href="/backend/ChannelAnalysis/countData">渠道数据</a></div>
    <div class="mod-tab mt20">
        <div class="mod-tab-hd">
            <ul>
              <li><a href="/backend/ChannelAnalysis/manage">渠道管理</a></li>
              <li class="current"><a href="/backend/ChannelAnalysis/countData">渠道数据</a></li>
              <li><a href="/backend/ChannelAnalysis/scoreAndRet">渠道评分及扣减</a></li>
            </ul>
        </div>
        <div class="mod-tab-bd">
            <ul>
                <li>
                    <div class="data-table-filter">
                    <form  name="searchForm" >
                        <table width="100%">
                            <tbody>
                                <tr>
                                    <td>
                                        平台：
                                        <select class="selectList w98" id="" name="platform">
                                        <?php foreach($platform as $k => $v): ?>
                                            <option value="<?php echo $k; ?>" <?php if($k == $search['platform']): ?>selected="selected"<?php endif;?> ><?php echo $v; ?></option>
                                        <?php endforeach;?>
                                        </select>
                                    </td> 
                                    <td>
                                        渠道：
                                        <span class=" w108"><input type="text" class="ipt" name='channel' value="<?php echo $search['channel']; ?>"><i></i></span>
                                    </td>
                                    <td>
                                        查询时间：
                                        <span class="ipt-date w184"><input type="text" class="ipt Wdate1" name="start_time" value="<?php echo $search['start_time']; ?>"><i></i></span>
                                        <span class="ml8 mr8">至</span>
                                        <span class="ipt-date w184"><input type="text" class="ipt Wdate1" name="end_time" value="<?php echo $search['end_time']; ?>" ><i></i></span>
                                    </td>
                                </tr>
                                <tr>
                                   <td>
                                        模式：
                                        <select class="selectList w98" id="" name="settle_mode">
                                        <?php foreach($settleMode as $k => $v): ?>
                                            <option value="<?php echo $k; ?>" <?php if($k == $search['settle_mode']): ?>selected="selected"<?php endif;?> ><?php echo $v; ?></option>
                                        <?php endforeach;?>
                                        </select>
                                    </td>
                                    <td></td>
                                    <td>
                                        <a href="javascript:;" class="btn-blue" id="doSearch">查询</a>
                                        <a href="javascript:;" class="btn-blue ml20" id='doExport'>导出</a>
                                        <a href="javascript:;" class="btn-blue ml20" id='doBillExport'>导出对账单</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                    </div>
                    <div class="data-table-list mt10">
                        <table>
                            <caption class="capwhite">
                                <b>真实新增：<span><?php echo $add_actives;?>个</span></b>
                                <b>真实注册：<span><?php echo $reg_nums;?>个</span></b>
                                <b>真实实名：<span><?php echo $real_nums;?>个</span></b>
                                <b>真实渠道购彩(不含时限)：<span><?php echo m_format($total_amounts);?>元</span></b>
                                <b>结算渠道购彩：<span><?php echo m_format($balance_amounts);?>元</span></b>
                                <b>真实分成：<span><?php echo m_format($actual_divisions);?>元</span></b>
                                <b>结算分成：<span><?php echo m_format($balance_yjs);?>元</span></b>
                            </caption>
                        </table> 
                    </div>
                    <div class="cp-table">
                        <div class="cp-table-main">
                            <div class="cp-table-thead">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>日期</th>
                                            <th>渠道名称</th>
                                            <?php if(in_array($search['platform'], array(2, 3))){ ?>
                                                <th>真实新增</th>
                                                <th class="tip-bg-color" id="_jsxz">结算新增</th>
                                            <?php } ?>
                                            <th>真实注册</th>
                                            <th>注册率</th>
                                            <th class="tip-bg-color" id="_jszc">结算注册</th>
                                            <th>真实实名</th>
                                            <th>实名率</th>
                                            <th class="tip-bg-color" id="_jssm">结算实名</th>
                                            <th>注册成本</th>
                                            <th>实名成本</th>
                                            <th>真实分成</th>
                                            <th class="tip-bg-color" id="_jsfc">结算分成</th>
                                            <th>新用户购彩人数</th>
                                            <th>新用户购彩率</th>
                                            <?php if(in_array($search['platform'], array(2, 3))){ ?>
                                                <th>新用户购彩人数/激活</th>
                                            <?php } ?>
                                            <th>真实新增购彩成本</th>
                                            <th class="tip-bg-color" id="_jsxzgccb">结算新增购彩成本</th>
                                            <th>真实渠道购彩<br>(不含时限)</th>
                                            <th>真实渠道购彩<br>(含时限)</th>
                                            <th class="tip-bg-color" id="_jsqdgc">结算渠道购彩</th>
                                            <?php if($search['settle_mode'] == '1'){ ?>
                                                <th>单价</th>
                                            <?php }elseif($search['settle_mode'] == '2'){ ?>
                                                <th>分成比例</th>
                                            <?php }else{ ?>
                                                <th>单价/分成比例</th>
                                            <?php } ?>
                                            <th>扣减比例</th>
                                            <th>新用户购彩总额</th>
                                            <th>日均新用户购彩</th>
                                            <th>渠道购彩总人数</th>
                                            <th>次日购彩留存</th>
                                            <?php if(in_array($search['platform'], array(2, 3))){ ?>
                                                <th>渠道活跃</th>
                                            <?php } ?>
                                            <th>注册时限</th>
                                            <?php if(in_array($search['platform'], array(2, 3))){ ?>
                                                <th id="_hjxs">核减系数得分</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="cp-table-overflow">
                                <div class="cp-table-tbody">
                                    <table>
                                        <tbody>
                                            <?php foreach ($result as $v):?>
                                                <tr>
                                                    <td><?php echo date('Y-m-d', strtotime($v['date']));?></td>
                                                    <td><?php echo $v['name'];?></td>
                                                    <?php if(in_array($search['platform'], array(2, 3))){ ?>
                                                        <td><?php echo $v['add_active'];?></td>
                                                        <td class="tip-bg-color"><?php echo $v['balance_active'];?></td>
                                                    <?php } ?>
                                                    <td><?php echo $v['reg_num'];?></td>
                                                    <td><?php echo $v['per_reg'].'%';?></td>
                                                    <td class="tip-bg-color"><?php echo $v['balance_reg'];?></td>
                                                    <td><?php echo $v['real_num'];?></td>
                                                    <td><?php echo $v['per_real'].'%';?></td>
                                                    <td class="tip-bg-color"><?php echo $v['balance_real'];?></td>
                                                    <td><?php echo m_format($v['balance_reg_amount']);?></td>
                                                    <td><?php echo m_format($v['balance_real_amount']);?></td>
                                                    <td><?php echo m_format($v['actual_division']);?></td>
                                                    <td class="tip-bg-color"><?php echo m_format($v['balance_yj']);?></td>
                                                    <td><?php echo $v['active_lottery_num'];?></td>
                                                    <td><?php echo $v['per_curr_lottery'].'%';?></td>
                                                    <?php if(in_array($search['platform'], array(2, 3))){ ?>
                                                        <td><?php echo $v['curr_lottery_divided_active'].'%';?></td>
                                                    <?php } ?>
                                                    <td><?php echo m_format($v['balance_lottery_amount']);?></td>
                                                    <td class="tip-bg-color"><?php echo m_format($v['balance_lottery_money']);?></td>
                                                    <td><?php echo m_format($v['total_amount']);?></td>
                                                    <td><?php echo m_format($v['lottery_total_amount']);?></td>
                                                    <td class="tip-bg-color"><?php echo m_format($v['balance_amount']);?></td>
                                                    <td><?php echo ($search['settle_mode'] == '2' ? $v['unit_price'].'%' : m_format($v['unit_price']));?></td>
                                                    <td><?php echo $v['ret_ratio'];?></td>
                                                    <td><?php echo m_format($v['curr_lottery_total_amount']);?></td>
                                                    <td><?php echo m_format($v['avg_curr_lottery_amount']);?></td>
                                                    <td><?php echo $v['lottery_num'];?></td>
                                                    <td><?php echo $v['per_next_lottery_num'].'%';?></td>
                                                    <?php if(in_array($search['platform'], array(2, 3))){ ?>
                                                        <td><?php echo $v['active_num'];?></td>
                                                    <?php } ?>
                                                    <td><?php echo $v['reg_time'];?></td>
                                                    <?php if(in_array($search['platform'], array(2, 3))){ ?>
                                                        <td><?php echo $v['redu_coeff_score'];?></td>
                                                    <?php } ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div>
                        <tr >
                            <td colspan="12">
                                <div class="stat">
                                    <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
                                    <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
                                    <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
                                </div>
                            </td>
                        </tr>
                    </div>
                    <div class="page mt10 order_info" >
                        <?php echo $pages[0] ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script  src="/source/date/WdatePicker.js"></script>
<script type="text/javascript">
$(function(){
    //日历
    $(".Wdate1").focus(function(){WdatePicker({dateFmt: "yyyy-MM-dd HH:mm:ss"});});
    var export_url = '<?php echo $this->config->item('base_url')?>'+'/backend/ChannelAnalysis/exportCountData';
    var export_bill_url = '<?php echo $this->config->item('base_url')?>'+'/backend/ChannelAnalysis/writeExcel';
    var platform = $('select[name=platform]').val();
    var start_time = $('input[name=start_time]').val();
    var end_time = $('input[name=end_time]').val();
    var settle_mode = $('select[name=settle_mode]').val();
    var channel = $('input[name=channel]').val();
    //初始化导出链接
    $('#doExport').attr('href','//'+export_url+'?platform='+platform+'&start_time='+start_time+'&end_time='+end_time+'&settle_mode='+settle_mode+'&channel='+channel);
    $('#doBillExport').attr('href','//'+export_bill_url+'?platform='+platform+'&start_time='+start_time+'&end_time='+end_time+'&settle_mode='+settle_mode+'&channel='+channel);
    $('#doSearch').click(function(){
        $('form[name=searchForm]').submit();
    });
});
</script>
<!-- 字段排序js -->
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tablesorter.css?v=7">
<script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery.tablesorter.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#tablesorter").tablesorter({headers:{0:{sorter:false},1:{sorter:false}}}); 
    //文案显示
    $("th#_hjxs").hover(function(){
        var that = this;
        layer.tips('<p>核减系数得分=（A*系数a+B*系数b+C*系数c）/10</p><p>A激活且购彩/激活=（当日激活且购彩人数/当日激活）对应得分</p><p>B日均新用户购彩= （当日注册在当日购彩总额/当日购彩人数）对应得分</p><p>C次日购彩留存=（当日注册在次日购彩人数/当日注册人数）对应得分</p><p>注：系数a、系数b、系数c在【渠道评分及扣减】中设置；</p>', that, {tips: [1, '#3595CC'], time: 0, area: ['430px', '130px']});
    }, function(){
        layer.closeAll();
    });
    $("th#_jsxz").hover(function(){
        var that = this;
        layer.tips('<p>结算新增=真实新增x核减系数得分x扣减比例</p>', that, {tips: [1, '#3595CC'], time: 0, area: ['330px', '40px']});
    }, function(){
        layer.closeAll();
    });
    $("th#_jszc").hover(function(){
        var that = this;
        layer.tips('<p>结算注册=真实注册x扣减比例</p>', that, {tips: [1, '#3595CC'], time: 0, area: ['330px', '40px']});
    }, function(){
        layer.closeAll();
    });
    $("th#_jssm").hover(function(){
        var that = this;
        layer.tips('<p>结算实名=真实实名x扣减比例</p>', that, {tips: [1, '#3595CC'], time: 0, area: ['330px', '40px']});
    }, function(){
        layer.closeAll();
    });
    $("th#_jsfc").hover(function(){
        var that = this;
        layer.tips('<p>①CPA:真实分成x扣减比例x核减系数得分；②CPS:真实分成x扣减比例</p>', that, {tips: [1, '#3595CC'], time: 0, area: ['330px', '70px']});
    }, function(){
        layer.closeAll();
    });
    $("th#_jsxzgccb").hover(function(){
        var that = this;
        layer.tips('<p>结算新增购彩成本=结算分成÷（当日）新用户购彩人数</p>', that, {tips: [1, '#3595CC'], time: 0, area: ['330px', '40px']});
    }, function(){
        layer.closeAll();
    });
    $("th#_jsqdgc").hover(function(){
        var that = this;
        layer.tips('<p>结算渠道购彩=真实渠道购彩（注册时限范围内）x扣减比例</p>', that, {tips: [1, '#3595CC'], time: 0, area: ['330px', '70px']});
    }, function(){
        layer.closeAll();
    });
});

window.onload = function () {
    var h = document.documentElement.clientHeight || document.body.clientHeight;
    var h1 = $(".cp-table-overflow").offset().top;
    $('.cp-table').ScrollTable({
        width: [108, 198, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108, 108],
        overflowHeight: h - h1 - 20,
        leftFixed: 2
    })
}
</script>
</body>
</html>