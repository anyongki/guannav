<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 提交 URL 到百度站长平台
 * @param array $urls 要提交的 URL 数组
 * @param string $baiduApiToken 百度站长平台 API 提交 token
 * @return array 提交结果
 */
function submitUrlsToBaidu($urls, $baiduApiToken) {
    if (empty($urls) || empty($baiduApiToken)) {
        return ['error' => 'URLs or Baidu API Token is empty.'];
    }

    $api = 'http://data.zz.baidu.com/urls?site=' . urlencode(Helper::options()->siteUrl) . '&token=' . $baiduApiToken;
    $ch = curl_init();
    $options = array(
        CURLOPT_URL => $api,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => implode("\n", $urls),
        CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        CURLOPT_TIMEOUT => 5 // 设置超时时间为5秒
    );
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        return json_decode($result, true);
    } else {
        return ['error' => 'HTTP Error: ' . $httpCode, 'response' => $result];
    }
}

/**
 * 监听文章发布/更新事件，自动提交到百度
 */
Typecho_Plugin::factory('Widget_Contents_Post_Edit')->finishPublish = function($contents, $edit) {
    $options = Helper::options();
    $baiduApiToken = $options->baiduApiToken; // 从主题设置中获取百度 API Token

    if (!empty($baiduApiToken)) {
        $permalink = $edit->permalink;
        $urls = [$permalink];
        $result = submitUrlsToBaidu($urls, $baiduApiToken);
        
        // 可以将提交结果记录到日志或后台通知，这里简单打印
        // error_log('Baidu Push Result for ' . $permalink . ': ' . json_encode($result));
    }
};

// 监听页面发布/更新事件，自动提交到百度
Typecho_Plugin::factory('Widget_Contents_Page_Edit')->finishPublish = function($contents, $edit) {
    $options = Helper::options();
    $baiduApiToken = $options->baiduApiToken; // 从主题设置中获取百度 API Token

    if (!empty($baiduApiToken)) {
        $permalink = $edit->permalink;
        $urls = [$permalink];
        $result = submitUrlsToBaidu($urls, $baiduApiToken);
        
        // error_log('Baidu Push Result for ' . $permalink . ': ' . json_encode($result));
    }
};

?>
