<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="/backend/betStation/index">投注站管理</a>&nbsp;&gt;&nbsp;<a href="/backend/betStation/index">投注站管理</a></div>
<div class="data-table-filter mt10">
    <form action="/backend/betStation/index" method="post"  id="searchdata">
    <table>
        <colgroup>
            <col width="30%" />
            <col width="30%" />
            <col width="30%" />
            <col width="10%" />
        </colgroup>
        <tbody>
        <tr>
            <td>
                合作商：
                <select name="partnerId" id="partnerId" class="selectList w140">
                    <option value="">不限</option>
                    <?php foreach ($partnerId as $key => $val):?>
                        <option value="<?php echo $val['id'];?>"
                            <?php if($val['id'] == $search['partnerId']  ): echo "selected"; endif;?>><?php echo $val['name'];?></option>
                    <?php endforeach;?>
                </select>
            </td>
            <td>
                编号：<input type="text" class="ipt w140" id = "shopId" name = "shopId" value = "<?php echo $search['shopId'] ?>"/>
            </td>
            <td>
                彩种：<select name="lid" id="lid" class="selectList w140">
                    <option value="">不限</option>
                    <?php foreach ($lids as $l => $lid):?>
                        <option value="<?php echo $l?>"
                            <?php if($l == $search['lid']  ): echo "selected"; endif;?>><?php echo $lid['cname'];?></option>
                    <?php endforeach;?>
                </select>
            </td>
            <td>
                <a href="javascript:void(0);" class="btn-blue ml25" id="search">查询</a>
            </td>
        </tr>
        </tbody>
    </table>
    </form>
</div>
<div class="data-table-list mt10">
    <table>
        <colgroup>
            <col width="10%" />
            <col width="10%" />
            <col width="10%" />
            <col width="10%" />
            <col width="10%" />
            <col width="10%" />
            <col width="10%" />
            <col width="24%" />
            <col width="8%" />
            <col width="8%" />
        </colgroup>
        <thead>
        <tr>
            <th>合作商</th>
            <th>编号</th>
            <th>名称</th>
            <th>电话</th>
            <th>QQ</th>
            <th>微信</th>
            <th>彩种</th>
            <th>地址</th>
            <th>状态</th>
            <th>详情</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($datas as $data):?>
        <tr>
            <td><?php echo $data['partner_name'];?></td>
            <td><?php echo $data['shopNum'];?></td>
            <td><?php echo empty($data['cname']) ? '--' : $data['cname'];?></td>
            <td><?php echo empty($data['phone']) ? '--' : $data['phone'];?></td>
            <td><?php echo empty($data['qq']) ? '--' : $data['qq'];?></td>
            <td><?php echo empty($data['webchat']) ? '--' : $data['webchat'];?></td>
            <td><?php echo empty($data['lid']) ? '--' : $lids[$data['lid']]['cname'];?></td>
            <td><?php echo empty($data['address']) ? '--' : $data['address'];?></td>
            <td><?php switch ($data['status'])
                {
                case 0:
                      echo "待审核";
                      break;
                  case 10:
                      echo "审核未通过";
                      break;
                  case 20:
                      echo "审核通过";
                      break;
                  case 30:
                      echo "已上架";
                      break;
                  case 40:
                      echo "审核通过";
                      break;
                }
                  ?></td>
            <td><a href="/backend/betStation/BetStationDetail?id=<?php echo $data['id'];?>" class="cBlue" target="_blank">查看</a></td>
        </tr>
        <?php endforeach;?>
        </tbody>
    </table>

</div>
<div class="stat mt10">
    <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
    <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
    <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
</div>
<div class="page mt10 order_info">
    <?php echo $pages[0] ?>
</div>
<!--#include virtual="../include/pagination.htm" -->
<script  src="/source/date/WdatePicker.js"></script>
<script type = "text/javascript">
    $(function(){

        $("#search").click(function(){
            var start = $("input[name='start_time']").val();
            var end = $("input[name='end_time']").val();
            if(start > end){
                alertPop('您选择的时间段错误，请核对后操作');
                return false;
            }
            $('#searchdata').submit();
        });

        $(".Wdate1").focus(function(){
            dataPicker();
        });

        $('#searchdata').submit(function(){
            if($("#fromType").val() == "ajax")
            {
                $("#order_info").load("/backend/betStation/index?"+$("#searchdata").serialize()+"&fromType=ajax");
                return false;
            }
            return true;
        });

    });
</script>