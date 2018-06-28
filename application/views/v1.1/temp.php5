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
<title><?php echo $title; ?></title>
<meta content="<?php echo $description; //@Author liusijia   ?>" name="Description" />
<meta content="<?php echo $keywords; //@Author liusijia  ?>" name="Keywords" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="renderer" content="webkit">
<meta name="baidu-site-verification" content="lQnvYyQA6s" />
<link rel="shortcut icon" href="/favicon.ico"/>
</head>
<body>
<script>
  // 百度统计 
  var _hmt = _hmt || [];
  (function() {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?73920d2a63aee9065feff02106ed5b0f";
    var s = document.getElementsByTagName("script")[0]; 
    s.parentNode.insertBefore(hm, s);
  })();
</script>
</body>
</html>
