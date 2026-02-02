<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的资源'),
            'search'    =>  _t('包含关键字 %s 的资源'),
            'tag'       =>  _t('标签 %s 下的资源'),
            'author'    =>  _t('%s 发布的资源')
        ), '', ' - '); ?><?php $this->options->title(); ?></title>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>?v=<?php echo time(); ?>">
    <link rel="icon" id="dynamic-favicon" href="/favicon.ico" type="image/x-icon">
    <!-- Fancybox 图片灯箱 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
    <?php $this->header(); ?>
    <script>
        (function() {
            const currentTheme = localStorage.getItem('theme') || 'dark';
            if (currentTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <style>
        /* 核心布局适配 */
        body { background-color: var(--bg-color); color: var(--text-color); transition: all 0.3s ease; }
        .archive-header { margin-bottom: 30px; padding: 20px 0; border-bottom: 1px solid var(--border-color); }
        .archive-title { font-size: 24px; font-weight: 700; color: var(--text-color); display: flex; align-items: center; gap: 10px; }
        .archive-title::before { content: ""; width: 4px; height: 24px; background: var(--accent-color); border-radius: 2px; }
        
        /* 强制覆盖卡片网格样式，确保与首页一致 */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
            padding: 10px;
        }

        /* 集成首页核心卡片样式 */
        .site-card {
            position: relative;
            display: flex;
            align-items: center;
            height: 85px;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            overflow: visible;
            opacity: 0; /* 配合动画 */
        }

        .site-card:hover {
            transform: translateY(-6px) scale(1.02);
            border-color: var(--glow-color);
            box-shadow: 0 0 15px var(--glow-color), 0 0 30px var(--glow-color), 0 10px 20px rgba(0,0,0,0.1);
            z-index: 10;
        }

        .card-main-click {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            width: 100%;
            height: 100%;
        }

        .card-icon-wrapper {
            flex-shrink: 0;
            width: 52px;
            height: 52px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-icon {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .site-card:hover .card-icon {
            transform: scale(1.1);
        }

        .card-body {
            flex: 1;
            min-width: 0;
            padding-right: 5px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-description {
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            opacity: 0.9;
        }

        .card-platforms {
            position: absolute;
            top: 5px;
            right: 8px;
            display: flex;
            gap: 5px;
            align-items: center;
            z-index: 2;
        }

        .card-more-btn {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            color: #bbb;
            border-radius: 50%;
            opacity: 0;
            transition: all 0.2s ease;
            z-index: 5;
        }

        [data-theme="dark"] .card-more-btn {
            background: #333;
            color: #777;
        }

        .site-card:hover .card-more-btn {
            opacity: 1;
        }

        .platform-icon {
            height: 14px;
            width: auto;
            display: flex;
            align-items: center;
        }

        .platform-icon img {
            height: 100%;
            width: auto;
            filter: var(--platform-icon-filter);
            opacity: var(--platform-icon-opacity);
            transition: all 0.2s ease;
        }

        .site-card:hover .platform-icon img {
            opacity: 0.9;
        }

        .platform-icon:hover img {
            opacity: 1;
            filter: none;
            transform: scale(1.3);
        }

        .main-content { padding: 80px 40px 40px; }
        .no-posts { grid-column: 1 / -1; text-align: center; padding: 50px; color: var(--text-muted); font-size: 16px; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }

        @media (max-width: 768px) {
            .main-content { padding: 70px 20px 20px; }
        }
    </style>
</head>
<body class="hide-sidebar-mobile">
    <div class="top-toolbar">
        <button class="theme-toggle" id="theme-toggle" title="切换深色/浅色模式">
            <svg id="sun-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
    </div>

    <div class="container-wrapper">
        <?php $this->need('sidebar.php'); ?>

        <main class="main-content">
            <div class="archive-header">
                <h1 class="archive-title">
                    <?php $this->archiveTitle(array(
                        'category'  =>  _t('%s'),
                        'search'    =>  _t('搜索: %s'),
                        'tag'       =>  _t('标签: %s'),
                        'author'    =>  _t('作者: %s')
                    ), '', ''); ?>
                </h1>
            </div>

            <div class="cards-grid" id="archive-grid">
                <div class="no-posts">正在加载资源...</div>
            </div>
        </main>
    </div>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');
        const htmlElement = document.documentElement;
        
        function updateAssets(theme) {
            const dynamicFavicon = document.getElementById('dynamic-favicon');
            const mainLogo = document.getElementById('main-site-logo');
            if (theme === 'dark') {
                if (mainLogo) mainLogo.src = '/logo2.png';
                if (dynamicFavicon) dynamicFavicon.href = '/favicon2.ico';
            } else {
                if (mainLogo) mainLogo.src = '/logo.png';
                if (dynamicFavicon) dynamicFavicon.href = '/favicon.ico';
            }
        }

        const currentTheme = localStorage.getItem('theme') || 'dark';
        if (currentTheme === 'dark') {
            htmlElement.setAttribute('data-theme', 'dark');
            if (sunIcon) sunIcon.style.display = 'block';
            if (moonIcon) moonIcon.style.display = 'none';
            updateAssets('dark');
        }

        themeToggle?.addEventListener('click', () => {
            const isDark = htmlElement.getAttribute('data-theme') === 'dark';
            if (isDark) {
                htmlElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                if (sunIcon) sunIcon.style.display = 'none';
                if (moonIcon) moonIcon.style.display = 'block';
                updateAssets('light');
            } else {
                htmlElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                if (sunIcon) sunIcon.style.display = 'block';
                if (moonIcon) moonIcon.style.display = 'none';
                updateAssets('dark');
            }
        });

        function loadArchivePosts() {
            const grid = document.getElementById('archive-grid');
            const categoryMid = "<?php echo $this->is('category') ? $this->categories[0]['mid'] : ''; ?>";

            if (!categoryMid) {
                grid.innerHTML = '<div class="no-posts">无法识别分类 ID</div>';
                return;
            }

            const formData = new FormData();
            formData.append('action', 'category');
            formData.append('mid', categoryMid);

            fetch('<?php $this->options->index(); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(response => {
                grid.innerHTML = ''; 
                const data = response.posts || (Array.isArray(response) ? response : []);
                if (data.length === 0) {
                    grid.innerHTML = '<div class="no-posts">该分类下暂无资源</div>';
                    return;
                }
                renderCards(data, grid);
            })
            .catch(err => {
                console.error(err);
                grid.innerHTML = '<div class="no-posts">加载失败，请刷新重试</div>';
            });
        }

        function renderCards(posts, grid) {
            posts.forEach((article, index) => {
                const card = document.createElement('div');
                card.className = 'card site-card'; // 同时包含 card 类名以适配首页样式
                const siteUrl = '<?php $this->options->siteUrl(); ?>';
                const pureDomain = siteUrl.replace(/^https?:\/\//, '').replace(/\/$/, '');
                const resourceType = article.resource_type || 'web';
                const jumpUrl = (resourceType === 'app') ? article.permalink : (article.website_url + (article.website_url.indexOf('?') !== -1 ? '&' : '?') + 'ref=' + encodeURIComponent(pureDomain));

                let platformsHtml = '';
	                const customIcon = article.custom_icon;
	                const customText = article.custom_text || '';
	                if (article.platforms && Array.isArray(article.platforms) && article.platforms.length > 0) {
	                    const platforms = [...article.platforms];
	                    platforms.sort((a, b) => (a === 'app' ? 1 : b === 'app' ? -1 : 0));
	                    const hasApp = platforms.includes('app');
	                    
	                    platforms.forEach(platform => {
	                        const p = platform.trim();
	                        if (p === 'app' && customIcon) {
	                            platformsHtml += `<div class="platform-icon" title="${customText}"><img src="${customIcon}" alt="custom"></div>`;
	                        }
	                        
	                        const icon = getPlatformIcon(p);
	                        let name = getPlatformName(p);
	                        if (p === 'gan' && article.ad_text) name = article.ad_text;
	                        
			                        if (icon) {
			                            platformsHtml += `<div class="platform-icon platform-${p}" title="${name}"><img src="${icon}" alt="${name}"></div>`;
			                        }
	                    });
	                    
	                    if (!hasApp && customIcon) {
	                        platformsHtml += `<div class="platform-icon" title="${customText}"><img src="${customIcon}" alt="custom"></div>`;
	                    }
	                } else if (customIcon) {
	                    platformsHtml += `<div class="platform-icon" title="${customText}"><img src="${customIcon}" alt="custom"></div>`;
	                }

                card.innerHTML = `
                    <div class="card-main-click" data-url="${(resourceType === 'post' || resourceType === 'app') ? article.permalink : jumpUrl}">
                        <div class="card-icon-wrapper">
                            <img src="${article.favicon || 'https://api.xinac.net/icon/?url=' + article.website_url}" class="card-icon">
                        </div>
                        <div class="card-body">
                            <div class="card-title">${article.title}</div>
                            <div class="card-description">${article.description || ''}</div>
                        </div>
                    </div>
                    <div class="card-platforms">${platformsHtml}</div>
                    <a href="${article.permalink}" class="card-more-btn" title="查看详情">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    </a>
                `;
                
                card.querySelector('.card-main-click').addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    if (resourceType === 'post' || resourceType === 'app') {
                        window.location.href = url;
                    } else {
                        window.open(url, '_blank');
                    }
                });

                grid.appendChild(card);
                setTimeout(() => card.classList.add('fade-in-up'), index * 50);
            });
        }

        function getPlatformIcon(p) {
            const m = {'ios':'/ico/ios.png','iosz':'/ico/ios.png','anzhuo':'/ico/anzhuo.png','app':'/ico/app.png','windows':'/ico/windows.png','mac':'/ico/mac.png','tv':'/ico/tv.png','che':'/ico/che.png','qian':'/ico/qian.png','fan':'/ico/fan.png','en':'/ico/en.png','gan':'/ico/gan.png'};
            return m[p] || '';
        }
        function getPlatformName(p) {
            const m = {'ios':'iOS端','iosz':'iOS端(需要自签名)','anzhuo':'安卓Android/鸿蒙端','app':'该软件是APP，没有网页版','windows':'Windows端','mac':'MacOS端','tv':'TV端','che':'车机端','qian':'部分影视资源需要收费','fan':'繁体中文页面','en':'英语页面','gan':'有广告'};
            return m[p] || p;
        }

        document.addEventListener('DOMContentLoaded', loadArchivePosts);
    </script>
    <?php $this->need('footer.php'); ?>