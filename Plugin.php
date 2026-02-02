<?php
/**
 * 导航主题辅助插件
 * 用于在 Typecho 中注册和管理自定义字段
 * 
 * 使用方法：将此文件放在 usr/plugins/NavThemeHelper/ 目录下
 * 并在后台插件管理中启用
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class NavThemeHelper_Plugin implements Typecho_Plugin_Interface {
    
    /**
     * 激活插件
     */
    public static function activate() {
        // 注册钩子
        Typecho_Plugin::factory(__FILE__)->on('admin_post_edit', array(__CLASS__, 'addPostFields'));
        Typecho_Plugin::factory(__FILE__)->on('admin_post_insert', array(__CLASS__, 'savePostFields'));
        Typecho_Plugin::factory(__FILE__)->on('admin_post_update', array(__CLASS__, 'savePostFields'));
        
        return _t('导航主题辅助插件已激活');
    }
    
    /**
     * 禁用插件
     */
    public static function deactivate() {
        return _t('导航主题辅助插件已禁用');
    }
    
    /**
     * 获取插件配置
     */
    public static function config(Typecho_Widget_Helper_Form $form) {
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
            'favicon_api',
            null,
            'https://www.google.com/s2/favicons?sz=128&domain=',
            _t('Favicon API 地址'),
            _t('用于获取网站图标的 API 地址')
        ));
    }
    
    /**
     * 个人用户的配置面板
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}
    
    /**
     * 在文章编辑页面添加自定义字段
     */
    public static function addPostFields() {
        global $post;
        
        if (!isset($post)) return;
        
        $postId = $post->cid;
        
        // 获取现有的字段值
        $websiteUrl = self::getPostMeta($postId, 'website_url', '');
        $autoFavicon = self::getPostMeta($postId, 'auto_favicon', '');
        $favicon = self::getPostMeta($postId, 'favicon', '');
        $iconfontCode = self::getPostMeta($postId, 'iconfont_code', '');
        $platforms = self::getPostMeta($postId, 'platforms', '');
        $description = self::getPostMeta($postId, 'description', '');
        
        echo '<div class="typecho-post-option" id="meta-box-nav-fields">';
        echo '<h3>导航主题字段</h3>';
        
        // 网站 URL
        echo '<div class="typecho-option">';
        echo '<label class="typecho-label" for="website_url">网站 URL</label>';
        echo '<input type="url" id="website_url" name="website_url" class="typecho-input" value="' . htmlspecialchars($websiteUrl) . '" placeholder="https://example.com" />';
        echo '<p class="description">输入网站完整 URL，用于自动获取 Favicon</p>';
        echo '</div>';
        
        // 自动获取 Favicon
        echo '<div class="typecho-option">';
        echo '<label class="typecho-label">网站图标</label>';
        echo '<div style="margin-bottom: 10px;">';
        echo '<button type="button" id="auto-favicon-btn" class="btn primary">自动获取图标</button>';
        echo '<span id="favicon-status" style="margin-left: 10px; color: #999;"></span>';
        echo '</div>';
        
        $faviconUrl = !empty($autoFavicon) ? $autoFavicon : $favicon;
        echo '<div style="margin-bottom: 10px;">';
        echo '<img id="favicon-preview" src="' . htmlspecialchars($faviconUrl) . '" alt="Favicon Preview" style="width: 48px; height: 48px; border-radius: 4px; display: ' . (!empty($faviconUrl) ? 'block' : 'none') . ';" />';
        echo '</div>';
        
        echo '<input type="hidden" id="auto_favicon" name="auto_favicon" value="' . htmlspecialchars($autoFavicon) . '" />';
        echo '<input type="hidden" id="favicon" name="favicon" value="' . htmlspecialchars($favicon) . '" />';
        echo '</div>';
        
        // 阿里图标代码
        echo '<div class="typecho-option">';
        echo '<label class="typecho-label" for="iconfont_code">阿里图标代码</label>';
        echo '<input type="text" id="iconfont_code" name="iconfont_code" class="typecho-input" value="' . htmlspecialchars($iconfontCode) . '" placeholder="例如: icon-star" />';
        echo '<p class="description">输入阿里图标的 class 名称，如 icon-star、icon-heart 等</p>';
        echo '</div>';
        
        // 平台标识
        echo '<div class="typecho-option">';
        echo '<label class="typecho-label" for="platforms">平台标识</label>';
        echo '<input type="text" id="platforms" name="platforms" class="typecho-input" value="' . htmlspecialchars($platforms) . '" placeholder="例如: iOS, Android, Web" />';
        echo '<p class="description">用逗号分隔多个平台，如：iOS, Android, Web</p>';
        echo '</div>';
        
        // 网站简介
        echo '<div class="typecho-option">';
        echo '<label class="typecho-label" for="description">网站简介</label>';
        echo '<textarea id="description" name="description" class="typecho-textarea" rows="3" placeholder="输入网站简介，最多 100 字">' . htmlspecialchars($description) . '</textarea>';
        echo '<p class="description">简短描述这个网站的用途和特点</p>';
        echo '</div>';
        
        echo '</div>';
        
        // 添加 JavaScript
        echo '<script>
        (function() {
            const btn = document.getElementById("auto-favicon-btn");
            const urlInput = document.getElementById("website_url");
            const faviconInput = document.getElementById("auto_favicon");
            const preview = document.getElementById("favicon-preview");
            const status = document.getElementById("favicon-status");
            
            if (btn) {
                btn.addEventListener("click", function(e) {
                    e.preventDefault();
                    
                    const url = urlInput.value.trim();
                    if (!url) {
                        status.textContent = "请先输入网站 URL";
                        status.style.color = "#d9534f";
                        return;
                    }
                    
                    btn.disabled = true;
                    status.textContent = "获取中...";
                    status.style.color = "#999";
                    
                    // 调用后台 API 获取 Favicon
                    fetch("' . Typecho_Common::url('index.php', '') . '?action=get_favicon&url=" + encodeURIComponent(url))
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.favicon) {
                                faviconInput.value = data.favicon;
                                preview.src = data.favicon;
                                preview.style.display = "block";
                                status.textContent = "✓ 获取成功";
                                status.style.color = "#5cb85c";
                            } else {
                                status.textContent = "✗ 获取失败";
                                status.style.color = "#d9534f";
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            status.textContent = "✗ 请求失败";
                            status.style.color = "#d9534f";
                        })
                        .finally(() => {
                            btn.disabled = false;
                        });
                });
            }
        })();
        </script>';
    }
    
    /**
     * 保存文章时处理自定义字段
     */
    public static function savePostFields($post) {
        if (!isset($_POST['website_url'])) return;
        
        $postId = $post->cid;
        
        // 保存所有自定义字段
        self::setPostMeta($postId, 'website_url', $_POST['website_url']);
        self::setPostMeta($postId, 'auto_favicon', $_POST['auto_favicon'] ?? '');
        self::setPostMeta($postId, 'favicon', $_POST['favicon'] ?? '');
        self::setPostMeta($postId, 'iconfont_code', $_POST['iconfont_code'] ?? '');
        self::setPostMeta($postId, 'platforms', $_POST['platforms'] ?? '');
        self::setPostMeta($postId, 'description', $_POST['description'] ?? '');
    }
    
    /**
     * 获取文章的自定义字段值
     */
    private static function getPostMeta($postId, $fieldName, $default = '') {
        global $db;
        
        try {
            $result = $db->fetchRow($db->select('value')->from('table.metas')->where('post = ' . $postId)->where('name = "' . $fieldName . '"')->limit(1));
            return $result ? $result['value'] : $default;
        } catch (Exception $e) {
            return $default;
        }
    }
    
    /**
     * 保存文章的自定义字段值
     */
    private static function setPostMeta($postId, $fieldName, $value) {
        global $db;
        
        try {
            // 检查是否已存在
            $result = $db->fetchRow($db->select('mid')->from('table.metas')->where('post = ' . $postId)->where('name = "' . $fieldName . '"')->limit(1));
            
            if ($result) {
                // 更新
                $db->query($db->update('table.metas')->rows(array('value' => $value))->where('mid = ' . $result['mid']));
            } else {
                // 插入
                $db->query($db->insert('table.metas')->rows(array(
                    'post' => $postId,
                    'name' => $fieldName,
                    'value' => $value,
                    'type' => 'post'
                )));
            }
        } catch (Exception $e) {
            // 忽略错误
        }
    }
}
?>
