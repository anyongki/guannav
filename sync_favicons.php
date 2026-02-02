<?php
/**
 * 图标本地化同步脚本 - 终极冗余版
 */

define('__TYPECHO_ROOT_DIR__', dirname(__FILE__));
require_once __TYPECHO_ROOT_DIR__ . '/config.inc.php';

// 1. 修复权限
$uploadDir = __TYPECHO_ROOT_DIR__ . '/usr/uploads/favicons/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}
@chmod($uploadDir, 0777);

// 2. 批量同步
$db = Typecho_Db::get();
$posts = $db->fetchAll($db->select()->from('table.contents')->where('type = ?', 'post'));

echo "<h2>图标同步工具 v3.0 (多接口冗余版)</h2>";
echo "开始同步 " . count($posts) . " 个网站的图标...<br><br>";

foreach ($posts as $post) {
    $cid = $post['cid'];
    $fields = $db->fetchAll($db->select()->from('table.fields')->where('cid = ?', $cid));
    
    $favicon = '';
    $websiteUrl = '';
    
    foreach ($fields as $field) {
        if ($field['name'] == 'favicon') $favicon = $field['str_value'];
        if ($field['name'] == 'website_url') $websiteUrl = $field['str_value'];
    }
    
    if (strpos($favicon, '/usr/uploads/favicons/') !== false) {
        echo "[$cid] {$post['title']} : <span style='color:blue;'>已本地化，跳过</span><br>";
        continue;
    }

    if (empty($websiteUrl)) {
        echo "[$cid] {$post['title']} : <span style='color:gray;'>无网址，跳过</span><br>";
        continue;
    }

    $host = parse_url($websiteUrl, PHP_URL_HOST);
    if (!$host) {
        echo "[$cid] {$post['title']} : <span style='color:gray;'>非标准域名，跳过</span><br>";
        continue;
    }

    // 接口列表
    $apis = array(
        "https://api.uomg.com/api/get.favicon?url=" . $websiteUrl,
        "https://api.iowen.cn/favicon/{$host}.png",
        "https://www.google.com/s2/favicons?sz=128&domain=" . $host
    );

    $success = false;
    echo "正在处理 [$cid] {$post['title']} ... ";

    foreach ($apis as $api) {
        $ch = curl_init($api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        // 检查是否是有效的图片数据
        if ($data && $httpCode == 200 && strpos($contentType, 'image') !== false) {
            $filename = 'fav-' . $cid . '.png';
            $savePath = $uploadDir . $filename;
            $publicPath = '/usr/uploads/favicons/' . $filename;
            
            if (file_put_contents($savePath, $data)) {
                @chmod($savePath, 0666);
                $db->query($db->update('table.fields')
                    ->rows(array('str_value' => $publicPath))
                    ->where('cid = ?', $cid)
                    ->where('name = ?', 'favicon'));
                echo "<span style='color:green;'>成功 (接口: " . parse_url($api, PHP_URL_HOST) . ")</span><br>";
                $success = true;
                break;
            }
        }
    }

    if (!$success) {
        echo "<span style='color:red;'>所有接口均失败</span><br>";
    }
}

echo "<br>同步完成！请刷新前台查看效果。为了安全，请立即删除此文件。";
?>
