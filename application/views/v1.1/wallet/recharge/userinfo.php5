<div class="user-info">
    用户名：<span><?php echo $this->uname;?></span>账户余额：<span><em><?php echo number_format(ParseUnit($this->uinfo['money'], 1), 2); ?></em> 元</span>
</div>