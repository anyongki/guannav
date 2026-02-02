<?php
/**
 * 后台自定义字段管理
 * 这个文件应该被包含在 Typecho 的后台编辑页面中
 * 或通过插件系统加载
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 在文章编辑页面添加自定义字段元框
 */
function addPostMetaBox() {
    global $post;
    
    if (!isset($post)) return;
    
    $postId = $post->cid;
    
    // 获取现有的字段值
    $favicon = getPostMeta($postId, 'favicon', '');
    $autoFavicon = getPostMeta($postId, 'auto_favicon', '');
    $iconfontCode = getPostMeta($postId, 'iconfont_code', '');
    $platforms = getPostMeta($postId, 'platforms', '');
    $description = getPostMeta($postId, 'description', '');
    $websiteUrl = getPostMeta($postId, 'website_url', '');
    $downloadResources = getPostMeta($postId, 'download_resources', '');
    
    ?>
    <div class="typecho-post-option" id="meta-box-nav-fields">
        <h3>导航主题字段</h3>
        
        <!-- 网站 URL -->
        <div class="typecho-option">
            <label class="typecho-label" for="website_url">网站 URL</label>
            <input type="url" id="website_url" name="website_url" class="typecho-input" value="<?php echo htmlspecialchars($websiteUrl); ?>" placeholder="https://example.com" />
            <p class="description">输入网站完整 URL，用于自动获取 Favicon</p>
        </div>
        
        <!-- 自动获取 Favicon -->
        <div class="typecho-option">
            <label class="typecho-label">网站图标</label>
            <div style="margin-bottom: 10px;">
                <button type="button" id="auto-favicon-btn" class="btn primary">自动获取图标</button>
                <span id="favicon-status" style="margin-left: 10px; color: #999;"></span>
            </div>
            
            <div style="margin-bottom: 10px;">
                <img id="favicon-preview" src="<?php echo htmlspecialchars($autoFavicon ?: $favicon); ?>" alt="Favicon Preview" style="width: 48px; height: 48px; border-radius: 4px; display: <?php echo (!empty($autoFavicon) || !empty($favicon)) ? 'block' : 'none'; ?>;" />
            </div>
            
            <input type="hidden" id="auto_favicon" name="auto_favicon" value="<?php echo htmlspecialchars($autoFavicon); ?>" />
            <input type="hidden" id="favicon" name="favicon" value="<?php echo htmlspecialchars($favicon); ?>" />
        </div>
        
        <!-- 阿里图标代码 -->
        <div class="typecho-option">
            <label class="typecho-label" for="iconfont_code">阿里图标代码</label>
            <input type="text" id="iconfont_code" name="iconfont_code" class="typecho-input" value="<?php echo htmlspecialchars($iconfontCode); ?>" placeholder="例如: icon-star" />
            <p class="description">输入阿里图标的 class 名称，如 icon-star、icon-heart 等</p>
        </div>
        
        <!-- 平台标识 -->
        <div class="typecho-option">
            <label class="typecho-label" for="platforms">平台标识</label>
            <input type="text" id="platforms" name="platforms" class="typecho-input" value="<?php echo htmlspecialchars($platforms); ?>" placeholder="例如: iOS, Android, Web" />
            <p class="description">用逗号分隔多个平台，如：iOS, Android, Web</p>
        </div>
        
        <!-- 网站简介 -->
        <div class="typecho-option">
            <label class="typecho-label" for="description">网站简介</label>
            <textarea id="description" name="description" class="typecho-textarea" rows="3" placeholder="输入网站简介，最多 100 字"><?php echo htmlspecialchars($description); ?></textarea>
            <p class="description">简短描述这个网站的用途和特点</p>
        </div>

        <!-- 下载资源配置 -->
        <div class="typecho-option">
            <label class="typecho-label" for="download_resources">下载资源配置</label>
            <textarea id="download_resources" name="download_resources" class="typecho-textarea" rows="5" placeholder="格式：平台标题,下载链接 (每行一个)"><?php echo htmlspecialchars($downloadResources); ?></textarea>
            <p class="description">格式：平台标题,下载链接 (每行一个)。例如：<br>安卓版,https://example.com/android.apk<br>iOS版,https://example.com/ios</p>
        </div>
    </div>
    
    <script>
    (function() {
        const btn = document.getElementById('auto-favicon-btn');
        const urlInput = document.getElementById('website_url');
        const faviconInput = document.getElementById('auto_favicon');
        const preview = document.getElementById('favicon-preview');
        const status = document.getElementById('favicon-status');
        
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const url = urlInput.value.trim();
                if (!url) {
                    status.textContent = '请先输入网站 URL';
                    status.style.color = '#d9534f';
                    return;
                }
                
                btn.disabled = true;
                status.textContent = '获取中...';
                status.style.color = '#999';
                
                // 调用后台 API 获取 Favicon
                fetch('<?php echo $GLOBALS['options']->siteUrl(); ?>?action=get_favicon&url=' + encodeURIComponent(url))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.favicon) {
                            faviconInput.value = data.favicon;
                            preview.src = data.favicon;
                            preview.style.display = 'block';
                            status.textContent = '✓ 获取成功';
                            status.style.color = '#5cb85c';
                        } else {
                            status.textContent = '✗ 获取失败';
                            status.style.color = '#d9534f';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        status.textContent = '✗ 请求失败';
                        status.style.color = '#d9534f';
                    })
                    .finally(() => {
                        btn.disabled = false;
                    });
            });
        }
    })();
    </script>
    <?php
}

/**
 * 保存文章时处理自定义字段
 */
function savePostMeta($post) {
    if (!isset($_POST['website_url'])) return;
    
    $postId = $post->cid;
    
    // 保存所有自定义字段
    setPostMeta($postId, 'website_url', $_POST['website_url']);
    setPostMeta($postId, 'auto_favicon', $_POST['auto_favicon'] ?? '');
    setPostMeta($postId, 'favicon', $_POST['favicon'] ?? '');
    setPostMeta($postId, 'iconfont_code', $_POST['iconfont_code'] ?? '');
    setPostMeta($postId, 'platforms', $_POST['platforms'] ?? '');
    setPostMeta($postId, 'description', $_POST['description'] ?? '');
    setPostMeta($postId, 'download_resources', $_POST['download_resources'] ?? '');
}

/**
 * 在 Post 类中添加 getField 方法
 */
if (!method_exists('Widget_Archive', 'getField')) {
    function getFieldMethod($fieldName, $default = '') {
        global $db;
        
        // 获取当前文章 ID
        $postId = $this->cid;
        
        $sql = $db->select('value')->from('table.metas')
            ->where('post = ?', $postId)
            ->where('name = ?', $fieldName)
            ->limit(1);
        
        $result = $db->fetchRow($sql);
        
        return $result ? $result['value'] : $default;
    }
}

?>
