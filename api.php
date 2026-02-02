<?php
/**
 * 导航主题 API 处理
 * 处理 Favicon 获取等 API 请求
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 获取网站 Favicon
 * 支持多种获取方式
 */
class NavTheme_API {
    
    /**
     * 获取网站的 Favicon
     * 
     * @param string $url 网站 URL
     * @return string Favicon URL
     */
    public static function getFavicon($url) {
        if (empty($url)) return '';
        
        // 确保 URL 有协议
        if (strpos($url, 'http') !== 0) {
            $url = 'https://' . $url;
        }
        
        $parsed = parse_url($url);
        $domain = $parsed['host'] ?? '';
        
        if (empty($domain)) return '';
        
        // 使用 Google Favicon API（最稳定）
        return 'https://www.google.com/s2/favicons?sz=128&domain=' . urlencode($domain);
    }
    
    /**
     * 处理 API 请求
     */
    public static function handleRequest() {
        // 检查是否是 Favicon 请求
        if (isset($_GET['action']) && $_GET['action'] === 'get_favicon') {
            self::handleGetFavicon();
        }
    }
    
    /**
     * 处理获取 Favicon 的请求
     */
    private static function handleGetFavicon() {
        // 设置响应头
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        
        // 获取 URL 参数
        $url = isset($_GET['url']) ? trim($_GET['url']) : '';
        
        // 验证 URL
        if (empty($url)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'URL 参数缺失',
                'code' => 'MISSING_URL'
            ]);
            exit;
        }
        
        // 验证 URL 格式
        if (!self::isValidUrl($url)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'URL 格式不正确',
                'code' => 'INVALID_URL'
            ]);
            exit;
        }
        
        // 获取 Favicon
        $favicon = self::getFavicon($url);
        
        if (empty($favicon)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => '无法获取 Favicon',
                'code' => 'FAVICON_NOT_FOUND'
            ]);
            exit;
        }
        
        // 返回成功响应
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'favicon' => $favicon,
            'url' => $url,
            'timestamp' => time()
        ]);
        exit;
    }
    
    /**
     * 验证 URL 是否有效
     * 
     * @param string $url URL 地址
     * @return bool 是否有效
     */
    private static function isValidUrl($url) {
        // 基本的 URL 验证
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        }
        
        // 如果没有协议，尝试添加 https://
        if (strpos($url, 'http') !== 0) {
            $testUrl = 'https://' . $url;
            if (filter_var($testUrl, FILTER_VALIDATE_URL)) {
                return true;
            }
        }
        
        return false;
    }
}

// 在主题加载时处理 API 请求
NavTheme_API::handleRequest();

?>
