<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 生成 XML Sitemap - Typecho 1.2.1 专用兼容版
 */
function generateSitemap() {
    $options = \Widget_Options::alloc();
    $db = \Typecho\Db::get();
    
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    // 1. 首页
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . $options->siteUrl . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . date('c') . '</lastmod>' . PHP_EOL;
    echo '    <changefreq>daily</changefreq>' . PHP_EOL;
    echo '    <priority>1.0</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;

    // 2. 文章
    $posts = $db->fetchAll($db->select('cid', 'slug', 'created', 'type')
        ->from('table.contents')
        ->where('type = ? AND status = ?', 'post', 'publish')
        ->order('created', \Typecho\Db::SORT_DESC));
    
    foreach ($posts as $post) {
        echo '  <url>' . PHP_EOL;
        echo '    <loc>' . \Typecho\Router::url($post['type'], $post, $options->index) . '</loc>' . PHP_EOL;
        echo '    <lastmod>' . date('c', $post['created']) . '</lastmod>' . PHP_EOL;
        echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
        echo '    <priority>0.8</priority>' . PHP_EOL;
        echo '  </url>' . PHP_EOL;
    }

    // 3. 独立页面
    $pages = $db->fetchAll($db->select('cid', 'slug', 'created', 'type')
        ->from('table.contents')
        ->where('type = ? AND status = ?', 'page', 'publish')
        ->order('created', \Typecho\Db::SORT_DESC));
    
    foreach ($pages as $page) {
        echo '  <url>' . PHP_EOL;
        echo '    <loc>' . \Typecho\Router::url($page['type'], $page, $options->index) . '</loc>' . PHP_EOL;
        echo '    <lastmod>' . date('c', $page['created']) . '</lastmod>' . PHP_EOL;
        echo '    <changefreq>monthly</changefreq>' . PHP_EOL;
        echo '    <priority>0.6</priority>' . PHP_EOL;
        echo '  </url>' . PHP_EOL;
    }

    // 4. 分类
    $categories = $db->fetchAll($db->select('mid', 'slug')
        ->from('table.metas')
        ->where('type = ?', 'category'));
    
    foreach ($categories as $category) {
        echo '  <url>' . PHP_EOL;
        echo '    <loc>' . \Typecho\Router::url('category', array('slug' => $category['slug']), $options->index) . '</loc>' . PHP_EOL;
        echo '    <lastmod>' . date('c') . '</lastmod>' . PHP_EOL;
        echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
        echo '    <priority>0.7</priority>' . PHP_EOL;
        echo '  </url>' . PHP_EOL;
    }

    echo '</urlset>';
}

/**
 * 针对 Typecho 1.2.1 的底层拦截逻辑
 */
if (isset($_SERVER['REQUEST_URI'])) {
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // 兼容子目录安装的情况
    $siteUrlPath = parse_url(\Widget_Options::alloc()->siteUrl, PHP_URL_PATH);
    $relativeUrl = !empty($siteUrlPath) ? str_replace($siteUrlPath, '', $url) : $url;
    $relativeUrl = '/' . ltrim($relativeUrl, '/');

    if ($relativeUrl == '/sitemap.xml') {
        generateSitemap();
        exit;
    }
}
