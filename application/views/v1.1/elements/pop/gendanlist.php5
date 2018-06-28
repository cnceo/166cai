<div class="pub-pop pop-pay pop-id" style="width: 574px;display:block" >
    <div class="pop-in">
        <div class="pop-head">
            <h2>定制人数</h2>
            <span class="pop-close" title="关闭">×</span>
        </div>
        <div class="pop-body">
            <table class="mod-tableA">
                <thead>
                    <tr>
                        <th width="8%">序号</th>
                        <th width="22%">用户名</th>
                        <th width="30%">定制时间</th>
                        <th width="20%">每次认购金额</th>
                        <th width="20%">每次认购比例</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $k=>$user){ ?>
                    <tr>
                        <td><?php  echo $k+1; ?></td>
                        <td><?php echo uname_cut($user['uname'],2, 3);?></td>
                        <td><?php echo $user['effectTime']; ?></td>
                        <td><?php echo $user['followType']==0?($user['buyMoney']/100).'元':'-';?></td>
                        <td><?php echo $user['followType']==0?'-':$user['buyMoneyRate'].'%';?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
           <?php if(!empty($users)){ echo $pages; }?>
        </div>
    </div>
</div>