<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ' - '); ?><?php $this->options->title(); ?></title>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>">
    <link rel="icon" id="dynamic-favicon" href="/favicon.ico" type="image/x-icon">
    <!-- Fancybox 图片灯箱 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
    <?php $this->header(); ?>
    <style>
        body { background-color: var(--bg-color); color: var(--text-color); transition: all 0.3s ease; }
        .post-container { max-width: 1250px; margin: 40px auto; padding: 0 20px; }
        .post-main-card, .post-content-section { 
            background: var(--card-bg); 
            padding: 30px; 
            border-radius: 20px; 
            border: 1px solid var(--border-color); 
            margin-bottom: 30px; 
            box-shadow: 0 4px 12px var(--shadow-color);
            width: 100%;
            box-sizing: border-box;
        }
        .comments-section {
            background: var(--card-bg); 
            padding: 30px; 
            border-radius: 20px; 
            border: 1px solid var(--border-color); 
            margin-bottom: 30px; 
            box-shadow: 0 4px 12px var(--shadow-color);
            width: 100% !important;
            max-width: 500px !important; /* 强制评论区宽度缩小一半 */
            margin-left: auto !important;
            margin-right: auto !important;
            box-sizing: border-box;
        }
        
        /* 修复夜间模式按钮颜色反转 */
        [data-theme="dark"] .btn-visit,
        [data-theme="dark"] .btn-download,
        [data-theme="dark"] .resource-actions a {
            filter: none !important;
            background-color: #007bff !important; /* 保持品牌蓝 */
            color: #fff !important;
        }
        .resource-header { display: flex; gap: 30px; align-items: flex-start; margin-bottom: 25px; }
        .post-logo-wrapper { width: 120px; height: 120px; flex-shrink: 0; border-radius: 15px; overflow: hidden; border: 1px solid var(--border-color); background: #fff; display: flex; align-items: center; justify-content: center; }
        .post-logo { width: 90%; height: 90%; object-fit: contain; }
        .resource-info { flex: 1; }
        .resource-title { font-size: 28px; font-weight: 700; margin-bottom: 10px; color: var(--text-color); }
        .resource-meta { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
        .resource-actions { display: flex; gap: 15px; align-items: center; }
        .article-header { text-align: center; margin-bottom: 30px; }
        .article-title { font-size: 32px; font-weight: 700; margin-bottom: 15px; color: var(--text-color); }
        .article-meta { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; align-items: center; }
        .meta-label { color: var(--text-muted); opacity: 0.8; }
        .meta-value { color: var(--accent-color); font-weight: 600; }
        .meta-value a { color: var(--accent-color); text-decoration: none; transition: opacity 0.2s; }
        .meta-value a:hover { opacity: 0.8; text-decoration: underline; }
        .btn-visit { background: var(--accent-color); color: #fff; padding: 10px 25px; border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-size: 15px; }
        .btn-visit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px var(--glow-color); }
        .btn-like { background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-color); padding: 9px 20px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-like:hover { background: var(--border-color); }
        .btn-like.active { color: #e74c3c; border-color: #e74c3c; background: rgba(231, 76, 60, 0.1); }
        .btn-like.active svg { fill: #e74c3c; }
        .meta-platforms { display: inline-flex; align-items: center; gap: 8px; margin-left: 5px; vertical-align: middle; }
        .meta-platform-icon { height: 18px; width: auto; filter: var(--platform-icon-filter); opacity: 0.8; transition: opacity 0.2s; }
        .meta-platform-icon:hover { opacity: 1; }
        .post-content-divider { height: 1px; background: var(--border-color); margin: 25px 0; opacity: 0.5; }
        .post-content { line-height: 1.8; color: var(--text-color); font-size: 16px; }
        .post-content a { color: var(--accent-color); text-decoration: none; border-bottom: 1px solid transparent; transition: all 0.3s ease; font-weight: 500; }
        .post-content a:hover { border-bottom-color: var(--accent-color); opacity: 0.8; }
        .section-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; padding-left: 10px; border-left: 4px solid var(--accent-color); color: var(--text-color); }
        .related-posts { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
        .related-card { background: var(--bg-color); border: 1px solid var(--border-color); padding: 15px; border-radius: 12px; text-decoration: none; display: flex; align-items: center; gap: 10px; transition: all 0.3s; }
        .related-card:hover { transform: translateY(-3px); border-color: var(--accent-color); box-shadow: 0 4px 12px var(--shadow-color); }
        .related-icon { width: 32px; height: 32px; border-radius: 6px; object-fit: contain; }
        .related-title { font-size: 14px; font-weight: 600; color: var(--text-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        @media (max-width: 768px) {
            .resource-header { flex-direction: column; align-items: center; text-align: center; }
            .resource-actions { justify-content: center; width: 100%; display: flex; gap: 10px; }
            .resource-actions .btn-visit { flex: 1; width: 100%; padding: 10px 0; }
            .article-title { font-size: 24px; }
        }

        /* 下载对话框样式 */
        .dl-modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(5px); }
        .dl-modal-content { background-color: var(--card-bg); margin: 15% auto; padding: 25px; border: 1px solid var(--border-color); width: 90%; max-width: 500px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: relative; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .dl-modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .dl-modal-title { font-size: 20px; font-weight: 700; color: var(--text-color); }
        .dl-modal-close { cursor: pointer; color: var(--text-muted); transition: color 0.2s; }
        .dl-modal-close:hover { color: var(--text-color); }
        .dl-link-list { display: flex; flex-direction: column; gap: 12px; }
        .dl-link-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 18px; background: var(--bg-color); border-radius: 12px; border: 1px solid var(--border-color); transition: all 0.2s; }
        .dl-link-row:hover { border-color: var(--accent-color); transform: translateX(5px); }
        .dl-platform-name { font-weight: 600; color: var(--text-color); display: flex; align-items: center; gap: 10px; }
        .dl-platform-url { font-size: 12px; color: var(--text-muted); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .btn-dl-action { background: var(--accent-color); color: #fff; padding: 6px 15px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; transition: opacity 0.2s; }
        .btn-dl-action:hover { opacity: 0.9; }
        .no-dl-links { text-align: center; padding: 20px; color: var(--text-muted); }
        
        /* 灯箱样式自定义 */
        .post-content img { cursor: zoom-in; transition: opacity 0.3s; max-width: 100%; height: auto; }
        .post-content img:hover { opacity: 0.9; }
    </style>
    <script>
        // 立即执行：精准对齐首页夜间模式逻辑
        (function() {
            const currentTheme = localStorage.getItem('theme') || 'dark';
            if (currentTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <script>
        // 侧边栏子分类菜单逻辑 - 全站同步
        document.addEventListener('DOMContentLoaded', function() {
            let menuTimer = null;
            let activeMenu = null;

            document.querySelectorAll('.nav-item-wrapper').forEach(wrapper => {
                wrapper.addEventListener('mouseenter', function() {
                    if (menuTimer) { clearTimeout(menuTimer); menuTimer = null; }
                    const currentMenu = this.querySelector('.sub-nav-menu');
                    if (activeMenu && activeMenu !== currentMenu) activeMenu.style.display = 'none';
                    if (!currentMenu) return;
                    activeMenu = currentMenu;
                    currentMenu.style.display = 'flex';
                    
                    // 强制贴合逻辑
                    if (window.innerWidth <= 768) {
                        currentMenu.style.setProperty('left', '120px', 'important');
                    } else {
                        currentMenu.style.left = '120px';
                    }
                    
                    const rect = this.getBoundingClientRect();
                    const menuHeight = currentMenu.offsetHeight;
                    const windowHeight = window.innerHeight;
                    if (rect.top + menuHeight > windowHeight) {
                        currentMenu.style.top = 'auto';
                        currentMenu.style.bottom = (windowHeight - rect.bottom) + 'px';
                    } else {
                        currentMenu.style.top = rect.top + 'px';
                        currentMenu.style.bottom = 'auto';
                    }
                });

                wrapper.addEventListener('mouseleave', function() {
                    const currentMenu = this.querySelector('.sub-nav-menu');
                    if (!currentMenu) return;
                    menuTimer = setTimeout(() => {
                        currentMenu.style.display = 'none';
                        if (activeMenu === currentMenu) activeMenu = null;
                    }, 300);
                });

                const subMenu = wrapper.querySelector('.sub-nav-menu');
                if (subMenu) {
                    subMenu.addEventListener('mouseenter', function() {
                        if (menuTimer) { clearTimeout(menuTimer); menuTimer = null; }
                    });
                    subMenu.addEventListener('mouseleave', function() {
                        menuTimer = setTimeout(() => {
                            this.style.display = 'none';
                            if (activeMenu === this) activeMenu = null;
                        }, 300);
                    });
                }
            });
        });
    </script>
</head>
<?php 
$bodyClass = 'post-page';
$resource_type = $this->fields->resource_type;
if ($resource_type === 'post' || $resource_type === 'app') {
    $bodyClass .= ' hide-sidebar-mobile';
}
?>
<body class="<?php echo $bodyClass; ?>">
    <div class="top-toolbar">
        <button class="theme-toggle" id="theme-toggle" title="切换深色/浅色模式">
            <svg id="sun-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
    </div>

    <div class="container-wrapper">
        <?php $this->need('sidebar.php'); ?>

        <main class="main-content fade-in-up">
            <div class="post-container">
                <div style="margin-bottom: 20px;">
                    <a href="<?php $this->options->siteUrl(); ?>" style="color: var(--text-muted); text-decoration: none; font-size: 14px;">← 返回导航首页</a>
                </div>

                <article class="post-main-card">
                    <?php $resource_type = $this->fields->resource_type; ?>
                    
                    <?php if ($resource_type === 'post'): ?>
                        <header class="article-header">
                            <h1 class="article-title"><?php $this->title(); ?></h1>
                            <div class="article-meta">
                                <span><span class="meta-label">发布于：</span><span class="meta-value"><?php $this->date('Y-m-d'); ?></span></span>
                                <span><span class="meta-label">浏览：</span><span class="meta-value"><?php echo get_post_view($this); ?> 次</span></span>
                                <span><span class="meta-label">分类：</span><span class="meta-value"><?php $this->category(' '); ?></span></span>
                                <?php if($this->user->pass('administrator', true)): ?>
                                    <span style="margin-left: 15px;"><a href="<?php $this->options->adminUrl('write-post.php?cid=' . $this->cid); ?>" target="_blank" style="color: #6c757d; text-decoration: none; font-weight: 600;">[编辑文章]</a></span>
                                <?php endif; ?>
                            </div>
                        </header>
                    <?php else: ?>
                        <header class="resource-header">
                            <div class="post-logo-wrapper">
                                <?php 
                                $favicon = $this->fields->favicon;
                                if (empty($favicon)) $favicon = "https://api.xinac.net/icon/?url=" . urlencode($this->fields->website_url);
                                ?>
                                <img src="<?php echo $favicon; ?>" alt="<?php $this->title(); ?>" class="post-logo">
                            </div>
                            <div class="resource-info">
                                <h1 class="resource-title"><?php $this->title(); ?></h1>
                                <div class="resource-meta">
                                    <span><span class="meta-label">分类：</span><span class="meta-value"><?php $this->category(' '); ?></span></span>
                                    <span><span class="meta-label">发布于：</span><span class="meta-value"><?php $this->date('Y-m-d'); ?></span></span>
                                    <span><span class="meta-label">浏览：</span><span class="meta-value"><?php echo get_post_view($this); ?> 次</span></span>
                                    
                                    <span class="meta-platforms">
                                        <?php 
                                        $platforms = parsePlatforms($this->fields->platforms);
                                        $customIcon = $this->fields->custom_icon;
                                        $customText = $this->fields->custom_text ?: '';
                                        $hasApp = in_array('app', $platforms);

                                        if (!empty($platforms)):
                                            foreach ($platforms as $p):
                                                if ($p === 'app' && !empty($customIcon)):
                                        ?>
                                            <img src="<?php echo $customIcon; ?>" class="meta-platform-icon" title="<?php echo $customText; ?>">
                                        <?php 
                                                endif;
                                                $pIcon = getPlatformIcon($p);
                                                if ($pIcon):
                                        ?>
                                            <img src="<?php echo $pIcon; ?>" class="meta-platform-icon" title="<?php echo ($p === 'gan' && !empty($this->fields->ad_text)) ? $this->fields->ad_text : getPlatformName($p); ?>">
                                        <?php 
                                                endif;
                                            endforeach;
                                        endif; 

                                        if (!empty($customIcon) && !$hasApp):
                                        ?>
                                            <img src="<?php echo $customIcon; ?>" class="meta-platform-icon" title="<?php echo $customText; ?>">
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="resource-actions">
                                    <?php 
                                    // 增强版字段读取：如果 fields 读不到，尝试直接从数据库读
                                    $website_url = $this->fields->website_url;
                                    if (empty($website_url)) {
                                        $db = Typecho_Db::get();
                                        $field_row = $db->fetchRow($db->select('str_value')->from('table.fields')->where('cid = ? AND name = ?', $this->cid, 'website_url'));
                                        $website_url = $field_row ? $field_row['str_value'] : '';
                                    }
                                    
                                    $site_url = $this->options->siteUrl;
                                    $pure_domain = preg_replace('/^https?:\/\//', '', rtrim($site_url, '/'));
                                    $jump_url = $website_url . (strpos($website_url, '?') !== false ? '&' : '?') . 'ref=' . urlencode($pure_domain);
                                    
                                    // 统一按钮样式
                                    $common_style = 'background: var(--bg-secondary); color: var(--text-color); border: 1px solid var(--border-color);';
                                    $primary_style = 'background: var(--accent-color); color: #fff; border: none;';
                                    ?>
                                    <?php if ($resource_type === 'app'): ?>
                                        <a href="javascript:void(0)" class="btn-visit" id="btn-download-trigger" style="<?php echo $primary_style; ?>">立即下载</a>
                                        <?php if (!empty($website_url) && $website_url !== '#'): ?>
                                            <a href="<?php echo $jump_url; ?>" target="_blank" rel="nofollow" class="btn-visit" style="<?php echo $common_style; ?>" onclick="addView(<?php echo $this->cid; ?>)">访问网站</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if (!empty($website_url) && $website_url !== '#'): ?>
                                            <a href="<?php echo $jump_url; ?>" target="_blank" rel="nofollow" class="btn-visit" style="<?php echo $primary_style; ?>" onclick="addView(<?php echo $this->cid; ?>)">立即访问</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <button class="btn-like" id="btn-like" data-cid="<?php $this->cid(); ?>" style="height: 42px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                        <span id="like-count"><?php echo get_like_count($this->cid); ?></span>
                                    </button>
                                    <?php if($this->user->pass('administrator', true)): ?>
                                        <a href="<?php $this->options->adminUrl('write-post.php?cid=' . $this->cid); ?>" target="_blank" class="btn-visit" style="background: #6c757d; color: #fff; border: none; height: 42px; padding: 0 15px; font-size: 14px;">编辑</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </header>
                    <?php endif; ?>

                    <div class="post-content-divider"></div>
                    <div class="post-content">
                        <?php $this->content(); ?>
                    </div>
                </article>

                <?php 
                $hasCategory = !empty($this->categories);
                if ($hasCategory): 
                ?>
                <section class="post-content-section">
                    <h3 class="section-title">相关推荐</h3>
                    <div class="related-posts">
                        <?php 
                        try {
                            $db = Typecho_Db::get();
                            $prefix = $db->getPrefix();
                            $mid = $this->categories[0]['mid'];
                            
                            if ($mid > 0) {
                                $sql = "SELECT p.cid, p.title, f.str_value as favicon, f1.str_value as website_url 
                                        FROM {$prefix}contents p 
                                        INNER JOIN {$prefix}relationships r ON p.cid = r.cid 
                                        LEFT JOIN {$prefix}fields f ON p.cid = f.cid AND f.name = 'favicon'
                                        LEFT JOIN {$prefix}fields f1 ON p.cid = f1.cid AND f1.name = 'website_url'
                                        WHERE r.mid = {$mid} AND p.cid != {$this->cid} AND p.type = 'post' AND p.status = 'publish'
                                        ORDER BY p.created DESC LIMIT 6";
                                $related = $db->fetchAll($sql);
                                if ($related):
                                    foreach ($related as $item):
                                        $relFavicon = $item['favicon'] ?: ($item['website_url'] ? "https://api.xinac.net/icon/?url=" . urlencode($item['website_url']) : "/favicon.ico");
                                ?>
                                    <a href="<?php echo rtrim($this->options->index, '/') . '/archives/' . $item['cid'] . '/'; ?>" class="related-card">
                                        <img src="<?php echo $relFavicon; ?>" class="related-icon">
                                        <span class="related-title"><?php echo $item['title']; ?></span>
                                    </a>
                                <?php endforeach; else: ?>
                                    <p style="color:var(--text-muted); font-size:14px;">暂无相关推荐</p>
                                <?php endif;
                            } else {
                                echo '<p style="color:var(--text-muted); font-size:14px;">暂无相关推荐</p>';
                            }
                        } catch (Exception $e) {
                            echo '<p style="color:var(--text-muted); font-size:14px;">暂无相关推荐</p>';
                        }
                        ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php $this->need('comments.php'); ?>
            </div>
        </main>
    </div>

    <!-- 下载对话框 -->
    <div id="download-modal" class="dl-modal">
        <div class="dl-modal-content">
            <div class="dl-modal-header">
                <div class="dl-modal-title">选择下载平台</div>
                <div class="dl-modal-close" id="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </div>
            </div>
            <div class="dl-link-list">
                <?php 
                $dlLinksJson = $this->fields->download_links;
                $dlLinks = json_decode($dlLinksJson, true);
                if (!empty($dlLinks)):
                    foreach ($dlLinks as $link):
                ?>
                    <div class="dl-link-row">
                        <div class="dl-platform-info">
                            <div class="dl-platform-name"><?php echo htmlspecialchars($link['platform']); ?></div>
                            <div class="dl-platform-url"><?php echo htmlspecialchars($link['url']); ?></div>
                        </div>
                        <?php if (!empty($link['url']) && $link['url'] !== '#'): ?>
                        <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" class="btn-dl-action" onclick="addView(<?php echo $this->cid; ?>)">下载</a>
                        <?php endif; ?>
                    </div>
                <?php 
                    endforeach;
                else:
                ?>
                    <div class="no-dl-links">暂无下载链接</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        // 初始化 Fancybox
        Fancybox.bind("[data-fancybox]", {
            // 自定义选项
        });
        
        // 为文章内容中的图片自动添加 Fancybox
        document.querySelectorAll('.post-content img').forEach(img => {
            const wrapper = document.createElement('a');
            wrapper.href = img.src;
            wrapper.setAttribute('data-fancybox', 'gallery');
            img.parentNode.insertBefore(wrapper, img);
            wrapper.appendChild(img);
        });

        // 夜间模式切换
        const themeToggle = document.getElementById('theme-toggle');
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');
        const dynamicFavicon = document.getElementById('dynamic-favicon');

        function updateIcons(theme) {
            if (theme === 'dark') {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
                if (dynamicFavicon) dynamicFavicon.href = '/favicon2.ico';
            } else {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
                if (dynamicFavicon) dynamicFavicon.href = '/favicon.ico';
            }
        }

        updateIcons(localStorage.getItem('theme') || 'dark');

        themeToggle.addEventListener('click', () => {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const newTheme = isDark ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcons(newTheme);
        });

        // 点赞功能
        document.getElementById('btn-like').addEventListener('click', function() {
            const btn = this;
            const cid = btn.getAttribute('data-cid');
            const countSpan = document.getElementById('like-count');
            
            if (btn.classList.contains('active')) return;

            const formData = new FormData();
            formData.append('action', 'like');
            formData.append('cid', cid);

            fetch('<?php $this->options->index(); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    countSpan.innerText = data.count;
                    btn.classList.add('active');
                }
            });
        });

        function addView(cid) {
            const formData = new FormData();
            formData.append('action', 'add_view');
            formData.append('cid', cid);
            fetch('<?php $this->options->index(); ?>', { method: 'POST', body: formData });
        }

        // 下载弹窗逻辑
        const dlModal = document.getElementById('download-modal');
        const dlTrigger = document.getElementById('btn-download-trigger');
        const modalClose = document.getElementById('modal-close');

        if (dlTrigger) {
            dlTrigger.onclick = function() {
                dlModal.style.display = "block";
                document.body.style.overflow = "hidden";
            }
        }

        if (modalClose) {
            modalClose.onclick = function() {
                dlModal.style.display = "none";
                document.body.style.overflow = "auto";
            }
        }

        window.onclick = function(event) {
            if (event.target == dlModal) {
                dlModal.style.display = "none";
                document.body.style.overflow = "auto";
            }
        }
    </script>
    <?php $this->need('footer.php'); ?>
</body>
</html>
