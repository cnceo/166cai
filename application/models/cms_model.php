<?php

class Cms_Model extends MY_Model {

    const CODE_SUCCESS = 1;

    const CATE_ALL = 0;
    const CATE_NOTICE = 1;
    const CATE_BLOG = 2;
    const CATE_NEWS = 3;
    const CATE_ACTIVITY = 4;
    const CATE_ADV = 5;

    private static $CN_NAMES = array(
        0 => '全部',
        1 => '公告',
        2 => '博文',
        3 => '新闻',
        4 => '活动',
        5 => '广告',
    );

    public function __construct() {
        parent::__construct();
    }

    public function getCategoryNames() {
        return self::$CN_NAMES;
    }

    public function getCategoryName($categoryId) {
        $cnName = '未知';
        if (isset(self::$CN_NAMES[$categoryId])) {
            $cnName = self::$CN_NAMES[$categoryId];
        }

        return $cnName;
    }

    public function getArticles($options = array()) {
        $articles = array();
        $articleResponse = $this->tools->get($this->cmsApi . 'content/fragment/list', $options);
        if ($articleResponse['code'] == self::CODE_SUCCESS) {
            $articles = $articleResponse['data'];
        }

        return $articles;
    }

    public function getArticleDetail($articleId) {
        $article = array();
        $articleResponse = $this->tools->get($this->cmsApi . 'content/fragment/detail', array(
            'fragmentId' => $articleId,
        ));
        if ($articleResponse['code'] == self::CODE_SUCCESS) {
            $article = $articleResponse['data'];
        }

        return $article;
    }

    public function getTags($options) {
        $tags = array();
        $tagResponse = $this->tools->get($this->cmsApi . 'content/fragment/tag/list', $options);
        if ($tagResponse['code'] == self::CODE_SUCCESS) {
            $tags = $tagResponse['data'];
        }

        return $tags;
    }


}
