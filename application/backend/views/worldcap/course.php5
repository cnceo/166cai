<?php $this->load->view("templates/head") ?>
<?php
    //日期转换成展示格式
    function timeFormat($time)
    {
        $time = strtotime($time);
        return date('m', $time).'月'.date('d', $time).'日 '.date('H:i', $time);
    }
?>
<style type="text/css">
    .w980 {
        width: 980px;
    }
</style>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">世界杯赛程接口</a></div>
<div class="data-table-filter mt10 w980">
    <a href="javascript:void(0);" class="btn-blue mr20 save">保存并上线</a>
</div>
<div class="data-table-list mt10 mb20">
  	<table id="manage">
        <colgroup>
            <col width="10">
            <col width="10">
            <col width="10">
            <col width="20">
            <col width="15">
            <col width="15">
            <col width="10">
            <col width="10">
            <col width="30">
        </colgroup>
        <tbody>
            <tr>
                <th>场次id</th>
                <th>期次</th>
                <th>场次类型</th>
                <th>比赛时间</th>
                <th>期次开始时间</th>
                <th>期次结束时间</th>
                <th>主队</th>
                <th>客队</th>
                <th>资讯链接</th>
            </tr>
            <?php foreach ($datas as $data){ ?>
            <tr id="<?=$data['id']?>">
                <td><?=$data['number']?></td>
                <td><?=$data['period']?></td>
                <td><?=$data['type']?></td>
                <td><?=timeFormat($data['play_time'])?></td>
                <td><?=timeFormat($data['period_start_time'])?></td>
                <td><?=timeFormat($data['period_end_time'])?></td>
                <td>
                    <div class="table-modify">
                        <p class="table-modify-txt"><?=$data['home_team']?><i></i></p>
                        <p class="table-modify-ipt">
                            <input type="text" class="ipt" id="home_team" name="home_team" value="<?=$data['home_team']?>">
                            <i></i>
                        </p>
                    </div>
                </td>
                <td>
                    <div class="table-modify">
                        <p class="table-modify-txt"><?=$data['away_team']?><i></i></p>
                        <p class="table-modify-ipt">
                            <input type="text" class="ipt" id="away_team" name="away_team" value="<?=$data['away_team']?>">
                            <i></i>
                        </p>
                    </div>
                </td>
                <td>
                    <div class="table-modify">
                        <p class="table-modify-txt"><?=$data['link']?><i></i></p>
                        <p class="table-modify-ipt">
                            <input type="text" class="ipt" id="link" name="link" value="<?=$data['link']?>">
                            <i></i>
                        </p>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script>
    $('.table-modify-txt').on('click', function(){
        $(this).hide();
        $(this).parents('.table-modify').find('.table-modify-ipt').show();
        var ipt = $(this).parents('.table-modify').find('.table-modify-ipt');
        var flage = ipt.find('input').attr('flage' ,'1') ;
    });
    $('.save').on('click', function(){
        var tableModify= $('.data-table-list').find('.table-modify');
        var table = document.getElementById("manage");
        var tbody = table.tBodies[0];
        var jsonArray = [];
        for(var k = 1, rowb; rowb = tbody.rows[k]; k++){
            var arr = {
                id: rowb.id,
                home_team: getText(rowb.cells[6]),
                away_team: getText(rowb.cells[7]),
                link: getText(rowb.cells[8])
            };
            jsonArray.push(arr);
        }
        var data = JSON.stringify(jsonArray);
        $.ajax({
            type: "post",
            url: '/backend/WorldCup/alter',
            data: {data:data},
            dataType: "json",
            success: function (returnData) {
                if(returnData.status =='y')
                {
                    layer.alert('修改成功', {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){
                            location.reload();
                        }
                    });
                }else
                {
                    layer.alert(returnData.message, {icon: 2,btn:'',title:'温馨提示',time:0});
                }
            }
        });
    });
    //获取dom文本
    var getText = function( el ){
        if($(el).find("input").attr('flage') == 1)
        {
            return $(el).find("input").val();
        }
    };

    $('.data-table-list').find('.table-modify').each(function(){
        if ($(this).find(".table-modify-txt").html() === '<i></i>') {
            var ipt = $(this).find('.table-modify-ipt');
            ipt.show();
            ipt.find('input').attr('flage' ,'1') ;
        }
    });

</script>