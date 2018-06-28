<script>
window.webkit.messageHandlers.jumpWeb.postMessage({
    url: '<?php echo $payData['payUrl']?>'
});

location.href = "<?php echo $payData['url']?>"
</script>