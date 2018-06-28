<?php $this->load->view("templates/head") ?>
        <div class="path">您的位置：系统管理&nbsp;&gt;&nbsp;<a href="/backend/Account/">帐号管理</a></div>
        <div class="tal mt20">
            <a href="/backend/Account/add" class="btn-blue">新建帐号</a>
        </div>
        <div class="data-table-list table-tb-border del-percent mt10">
            <table>
                <colgroup>
                    <col width="100" />
                    <col width="168" />
                    <col width="145" />
                    <col width="145" />
                    <col width="150" />
                    <col width="100" />
                    <col width="100" />
                    <col width="120" />
                   <col width="150" />
                </colgroup>
                <tbody>

                    <tr>
                        <th>账号ID</th>
                        <th>账号名</th>
                        <th>账号身份</th>
                        <th>创建时间</th>
                        <th>创建人</th>
                        <th>帐号状态</th>
                        <th>权限</th>
                        <th>备注</th>
                    </tr>
                    <?php if (!empty($accounts)): ?>
                        <?php foreach ($accounts as $account): ?>
                            <tr>
                                <td><?php echo $account['id']; ?></td>
                                <td><?php echo $account['name']; ?></td>
                                <td><?php echo $user_capacity_cfg['user_role'][$account['role']]; ?></td>
                                <td><?php echo date("Y-m-d H:i:s", $account['addTime']); ?></td>
                                <td><?php echo $account['createName']; ?></td>
                                <td><a href="javascript:void(0);" onclick='change_status(<?php echo $account['id']; ?>,this)' class="cBlue">
                                        <?php echo $user_capacity_cfg['status_capacity'][$account['status']]; ?>
                                    </a></td>
                                <td><a href="/backend/Account/add?id=<?php echo $account['id']; ?>" class="cBlue mr10">编辑</a> <a href="/backend/Account/add?id=<?php echo $account['id']; ?>&preview=1" class="cBlue mr10">预览</a></td>
                                <td><?php echo $account['mark']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <script type="text/javascript">
        //修改用户状态
        function change_status(id,_this)
        {
            $.ajax({
                type: "post",
                url: '/backend/Account/change_status',
                data: {'id': id},
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    if (json.status == 'y')
                    {
                        $(_this).html(json.info);
                    }
                    alert(json.message);
                }
            });
            return false;
        }
        </script>
    </body>
</html>