<?php $this->load->view("templates/head") ?>
<style type="text/css">
    ._red{color:#f00;font-style: normal;}
    ._normal{font-style: normal;}
</style>
<div id="app">
    <div class="path">您的位置：<a href="javascript:;">推送管理</a>&nbsp;&gt;&nbsp;<a href="/backend/Apppush/management">未注册实名推送</a></div>
    <div class="mod-tab mt20">
        <div class="mod-tab-hd">
            <ul>
                <li><a href="/backend/Apppush/management">推送管理</a></li>
                <li class="current"><a href="/backend/Apppush/effect">推送效果</a></li>
                <li><span style="color:red;margin-left: 20px;">
                        除半小时推送外，程序将在每日22点统一处理明日符合条件的推送配置信息，请提前确认保存和删除操作</span>
                    <br><span style="color:red;margin-left: 20px;">推送选择红包，尽量选择短信推送方式</span>
                </li>
            </ul>
        </div>
        <div class="data-table-filter mt10" style="width:960px">
            <form action="" method="get"  id="search_form">
                <table>
                    <colgroup>
                        <col width="50" />
                        <col width="150" />
                        <col width="50" />
                        <col width="150" />
                        <col width="50" />
                        <col width="150" />
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>推送主题：</th>
                            <td>
                                <select class="selectList w130" name="topic">
                                    <option value="0">全部</option>
                                    <?php foreach ($lists as $key => $list) { ?>
                                        <option  value="<?php echo $list['id'] ?>" <?php
                                        if ($list['id'] === ($search['topic'])): echo "selected";
                                        endif;
                                        ?>><?php echo $list['topic']; ?></option>
                                             <?php } ?>
                                </select>
                            </td>
                            <th>推送方式：</th>
                            <td>
                                <select class="selectList w130" name="type">
                                    <option value="-1" <?php
                                    if (-1 == ($search['type'])): echo "selected";
                                    endif;
                                    ?>>全部</option>
                                    <option value="0" <?php
                                    if ('0' === ($search['type'])): echo "selected";
                                    endif;
                                    ?>>短信</option>
                                    <option value="1" <?php
                                    if ('1' === ($search['type'])): echo "selected";
                                    endif;
                                    ?>>push</option>
                                </select>
                            </td>
                            <th>是否红包：</th>
                            <td>
                                <select class="selectList w130" name="redpack">
                                    <option value="0" <?php
                                    if (0 == ($search['redpack'])): echo "selected";
                                    endif;
                                    ?>>全部</option>
                                    <option value="1" <?php
                                    if (1 == ($search['redpack'])): echo "selected";
                                    endif;
                                    ?>>是</option>
                                    <option value="2" <?php
                                    if (2 == ($search['redpack'])): echo "selected";
                                    endif;
                                    ?>>否</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>创建周期：</th>
                            <td>
                                <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ? $search['start_time'] : $searchTime['start_time']; ?>" class="Wdate1" /><i></i></span>
                                <span class="ml8 mr8">至</span>
                                <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ? $search['end_time'] : $searchTime['end_time']; ?>" class="Wdate1" /><i></i></span>
                            </td>
                            <th></th>
                            <td><a href="javascript:void(0);" class="btn-blue mr20" id="query">查询</a></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
        <div class="data-table-list mt20">
        <table>
            <colgroup>
                <col width="40" />
                <col width="100" />
                <col width="80" />
                <col width="80" />
                <col width="70" />
                <col width="110" />
                <col width="100" />
                <col width="100" />
                <col width="100" />
                <col width="100" />
                <col width="60" />
                <col width="60" />
                <col width="60" />
            </colgroup>
            <tr>
                <td  colspan="13">
                    <div class="tal">
                        <strong>累计涉及人数</strong>
                        <span><?php echo $sum['totalNum']; ?></span>
                        <strong>累计注册人数</strong>
                        <span><?php echo $sum['regNum']; ?></span>
                        <strong>累计实名人数</strong>
                        <span><?php echo $sum['authNum']; ?></span>
                        <strong>累计充值人数</strong>
                        <span><?php echo $sum['recNum']; ?></span>                        
                    </div>
                </td>
            </tr>
        </table>
        <table id="tablesorter" class="tablesorter">
            <colgroup>
                <col width="50" />
                <col width="100" />
                <col width="40" />
                <col width="70" />
                <col width="70" />
                <col width="50" />
                <col width="60" />
                <col width="60" />
                <col width="60" />
                <col width="60" />
                <col width="60" />
                <col width="120" />
                <col width="100" />
            </colgroup>
            <thead>
                <tr>
                    <th>创建日期</th>
                    <th>推送主题</th>
                    <th>推送方式</th>
                    <th>第一次推送时间</th>
                    <th>第二次推送时间</th>
                    <th>是否选择红包</th>
                    <th>涉及人数</th>
                    <th>注册人数</th>
                    <th>实名人数</th>
                    <th>充值人数</th>
                    <th>操作</th>
                    <th>推送内容</th>
                    <th>推送地址</th>
                </tr>
            </thead>
            <?php foreach ($effects as $key => $list): ?>
                <tr>
                    <td><?php echo $list['pdate']; ?></td>
                    <td><?php echo $list['topic']; ?></td>
                    <td><?php echo $list['ptype']==0?'短信':'push'; ?></td>
                        <?php
                        $time = json_decode($list['config'], true);
                        if ($time[0]['time'] == 30) {
                            $first = '半小时';
                        } else {
                            $t = explode('-', $time[0]['time']);
                            $first = '次日' . $t[1];
                        }
                        if (isset($time[1])) {
                            $t = explode('-', $time[1]['time']);
                            if ($t[0] == 1) {
                                $second = '次日' . $t[1];
                            } else {
                                $second = '第三日' . $t[1];
                            }
                        } else {
                            $second = '/';
                        }
                        ?>
                    <td><?php echo $first; ?></td>
                    <td><?php echo $second; ?></td>
                    <td><?php echo $list['rid']>0?$list['rname']:'/'; ?></td>
                    <td><?php echo $list['totalNum']; ?></td>
                    <td><?php echo (strstr($list['topic'],'注册未实名')|| strstr($list['topic'],'实名未购彩'))?'-':$list['regNum']; ?></td>
                    <td><?php echo ($list['topic']=='手机领红包未注册' || strstr($list['topic'],'实名未购彩'))?'-':$list['authNum']; ?></td>
                    <td><?php echo ($list['topic']=='手机领红包未注册' || strstr($list['topic'],'注册未实名'))?'-':$list['recNum']; ?></td>
                    <td><a href="javascript:void(0);" data-id="<?php echo $list['id']; ?>" data-topic="<?php echo $list['topic']; ?>" class="detail">详情</a> &nbsp;<a href="javascript:void(0);" data-id="<?php echo $list['id']; ?>" data-pdate="<?php echo $list['pdate']; ?>" class="export">导出</a></td>
                    <td><?php echo $list['content']; ?></td>
                    <td><?php  if($list['action']==0){echo '打开app';}elseif($list['action']==1){echo '打开红包页面';}else{ echo '打开url:'.$list['url']; } ?></td>
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
        </table>
    </div>
    <div class="page mt10">
        <?php echo $pages[0];?>
    </div>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<div class="pop-dialog" id="detail">
    <div class="pop-in">
        <div class="pop-head">
            <h2>推送效果详情</h2>
        </div>
        <div class="pop-body">
            主题:<span id="head"></span>
            <div class="data-table-list mt10">
                <table>
                    <colgroup>
                        <col width="30"/>
                        <col width="30"/>
                        <col width="30"/>
                        <col width="30"/>
                        <col width="30"/>
                        <col width="30"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>创建日期</th>
                            <th>推送时间</th>
                            <th>涉及人数</th>
                            <th>注册人数</th>
                            <th>实名人数</th>
                            <th>充值人数</th>
                        </tr>
                    </thead>
                    <tbody id="listdetail">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:closePop();" class="btn-blue-h32">确定</a>
        </div>
    </div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(".Wdate1").focus(function () {
        dataPicker();
    });
    
     $("#query").click(function () {
            var start = $("input[name='start_time']").val();
            var end = $("input[name='end_time']").val();
            if ((start && end) && (start > end)) {
                alert('起始时间不能晚于结束时间');
                return false;
            }
            $('#search_form').submit();
        });
        
     $(".detail").click(function(){
        var id =$(this).data('id');
        var topic = $(this).data('topic');
        $("#head").html(topic);
        $("#listdetail").html("");
        $.post('/backend/Apppush/getEffectDetail',{id:id},function(data){
            $("#listdetail").html("");
            if(data.status=='y'){
                for (var i = 0; i < data.message.length; i++) {
                    var a = data.message[i];
                    $("#listdetail").append("<tr><td>"+a.pdate+"</td><td>"+a.time+"</td><td>"+a.totalNum+"</td><td>"+a.regNum+"</td><td>"+a.authNum+"</td><td>"+a.recNum+"</td></tr>");
                }
                popdialog("detail"); 
            }
        },'json');
    });
    
    $(".export").on('click',function(){
        var id =$(this).data('id');
        var pdate = $(this).data('pdate');
        location.href="/backend/Apppush/export?id="+id+"&pdate="+pdate;
    }); 
</script>    


