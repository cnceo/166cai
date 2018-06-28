<?php if (!$this->is_ajax): ?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<?php
/* --- seo优化 --- @Author liusijia --- start --- */
$this->config->load('seo');
$seo = $this->config->item('seo');
$set_data = $seo[$this->con][$this->act];
$title = str_replace(array('#cnName#', '*date*', '#pageNumber#','#pageTitle#','#issue#', '#param0#', '#param1#'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : ''), (($pageNumber > 1) ? '-第' . $pageNumber . '页' : ''),(!empty($pageTitle)?$pageTitle:''),(!empty($issue)?$issue:''), $param0, $param1), $set_data['title']);
$keywords = ($pageNumber > 1) ? '' : str_replace(array('#cnName#', '*date*', '#issue#', '#param0#', '#param1#'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : ''), (!empty($issue)?$issue:''), $param0, $param1), $set_data['keywords']);
$description = ($pageNumber > 1) ? '' : str_replace(array('#cnName#', '*date*', '#issue#', '#param0#', '#param1#'), array($cnName, (!empty($date) ? date('Y-m-d', $date) : ''), (!empty($issue)?$issue:''), $param0, $param1), $set_data['description']);
/* --- seo优化 --- @Author liusijia --- end --- */
?>
<title><?php echo $title?$title:$s_title; ?></title>
<meta content="<?php echo $description;  ?>" name="Description" />
<meta content="<?php echo $keywords; //@Author liusijia  ?>" name="Keywords" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="renderer" content="webkit">
<meta name="baidu-site-verification" content="lQnvYyQA6s" />
<base href="<?php echo $baseUrl; ?>" <?php if (in_array($this->con, array('notice', 'help', 'hall'))): ?> target="_blank" <?php endif;?>>
<!--[if lte IE 6]></base><![endif]-->
<link rel="shortcut icon" href="/favicon.ico"/>
<link rel="bookmark" href="/favicon.ico"/>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>"/>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/user.min.css');?>"/>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/jifen.min.css');?>">
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
<?php if (!$htype): ?>
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-public.min.css');?>" rel="stylesheet" />
<?php endif; ?>
</head>
<body>
<script type="text/javascript">
    var baseUrl = '<?php echo $baseUrl; ?>';
    var uri = '<?php echo str_replace(array('<', '>', 'script'), '', $_SERVER['REQUEST_URI']);?>';
    var version = 'v1.1';
    var G = {
        baseUrl: baseUrl
    }
    var visitor = {userNickname:'<?php echo empty($this->uid) ? '未登录用户' : $this->uinfo['uname']?>'};
    window.easemobim = window.easemobim || {};
    easemobim.config = {visitor: visitor};
</script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js'); ?>" type="text/javascript" ></script>
<!--top begin-->
<?php if (empty($this->uid)): 
    $this->load->view('v1.1/elements/common/header_topbar_notlogin'); 
else: 
    $this->load->view('v1.1/elements/common/header_topbar'); 
endif; ?>
<!--top end-->
<?php endif; ?>
