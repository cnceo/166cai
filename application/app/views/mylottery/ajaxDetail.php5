<?php if(!empty($orders)):?>
    <?php foreach ($orders as $order): ?>
        <li <?php echo "onclick=\"window.location.href='" . $order['tradeDetailUrl'] . "'\""; ?>\"">
            <p><?php echo wallet_ctype($order['ctype'], $order['additions']); ?>
                <b><span style="color:<?php echo $order['balance'] > 0 ? 'red' : 'green'; ?>"><?php echo $order['balance']; ?></span></b>
            </p>

            <p>
                <time><?php echo date('m-d h:m', strtotime($order['created'])); ?></time>
                <s>余额<?php echo number_format(ParseUnit($order['umoney'], 1), 2); ?></s>
            </p>
        </li>
    <?php endforeach; ?>
<?php endif;?>