<?php
/**
 * 观影导航主题 - 首页
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php $this->options->title() ?></title>
    <meta name="description" content="<?php $this->options->description() ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>">
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
</head>
<body>
    <!-- 顶部工具栏 -->
    <div class="top-toolbar">
        <button class="theme-toggle" id="theme-toggle" title="切换深色/浅色模式">
            <svg id="sun-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
    </div>

    <div class="container-wrapper">
        <!-- 调用统一导航组件 -->
        <?php $this->need('sidebar.php'); ?>

        <!-- 主内容区域 -->
        <main class="main-content">
            <?php 
            global $tree;
            if (!empty($tree)) {
                foreach ($tree as $topCategory) {
                    if ($topCategory['isHidden']) continue; // 隐藏分类
                    ?>
                    <section class="category-section" id="<?php echo htmlspecialchars($topCategory['slug']); ?>">
                        <div class="category-header">
                            <div class="category-title-wrapper">
                                <?php if (!empty($topCategory['icon'])) { ?>
                                    <img src="<?php echo htmlspecialchars($topCategory['icon']); ?>" class="title-icon">
                                <?php } ?>
                                <h2 class="category-title"><?php echo htmlspecialchars($topCategory['name']); ?></h2>
                            </div>
                            
                            <!-- 子分类选项卡 -->
                            <div class="subcategory-tabs">
                                <?php 
                                if (!empty($topCategory['children'])) {
                                    foreach ($topCategory['children'] as $index => $subCategory) { 
                                        if ($subCategory['isHidden']) continue; // 隐藏子分类
                                        ?>
                                        <button class="subcategory-tab<?php echo $index === 0 ? ' active' : ''; ?>" data-mid="<?php echo intval($subCategory['mid']); ?>" data-slug="<?php echo $subCategory['slug']; ?>" data-limit="<?php echo intval($subCategory['limit']); ?>">
                                            <?php echo htmlspecialchars($subCategory['name']); ?>
                                        </button>
                                    <?php } 
                                } else {
                                    echo '<button class="subcategory-tab active" style="display:none;" data-mid="' . intval($topCategory['mid']) . '" data-slug="' . $topCategory['slug'] . '" data-limit="' . intval($topCategory['limit']) . '">' . htmlspecialchars($topCategory['name']) . '</button>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <!-- 卡片网格 -->
                        <div class="cards-grid" id="grid-<?php echo $topCategory['mid']; ?>">
                            <!-- 初始不显示文字，由 JS 控制延迟显示 -->
                        </div>
                    </section>
                    <?php
                }
            }
            ?>

            <!-- 友情链接板块 -->
            <?php 
            $linksStr = $this->options->links;
            if (!empty($linksStr)):
                $linksArray = explode("\n", str_replace("\r", "", $linksStr));
            ?>
            <section class="footer-links fade-in-up">
                <h3 class="links-title">观影的小伙伴们</h3>
                <div class="links-container">
                    <?php 
                    foreach ($linksArray as $linkLine) {
                        $parts = explode(",", $linkLine);
                        if (count($parts) >= 3) {
                            $icon = trim($parts[0]);
                            $name = trim($parts[1]);
                            $url = trim($parts[2]);
                            echo '<a href="' . htmlspecialchars($url) . '" target="_blank" class="link-item" style="display: flex; align-items: center; gap: 8px;">
                                    <img src="' . htmlspecialchars($icon) . '" style="width: 16px; height: 16px; object-fit: contain;">
                                    <span>' . htmlspecialchars($name) . '</span>
                                  </a>';
                        } elseif (count($parts) >= 2) {
                            $name = trim($parts[0]);
                            $url = trim($parts[1]);
                            echo '<a href="' . htmlspecialchars($url) . '" target="_blank" class="link-item">' . htmlspecialchars($name) . '</a>';
                        }
                    }
                    ?>
                </div>
            </section>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // 主题切换逻辑
        const themeToggle = document.getElementById('theme-toggle');
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');
        const htmlElement = document.documentElement;
        const mainLogo = document.getElementById('main-site-logo');
        const dynamicFavicon = document.getElementById('dynamic-favicon');
        const currentTheme = localStorage.getItem('theme') || 'dark';
        
        function updateAssets(theme) {
            if (theme === 'dark') {
                if (mainLogo) mainLogo.src = '/logo2.png';
                if (dynamicFavicon) dynamicFavicon.href = '/favicon2.ico';
            } else {
                if (mainLogo) mainLogo.src = '/logo.png';
                if (dynamicFavicon) dynamicFavicon.href = '/favicon.ico';
            }
        }

        if (currentTheme === 'dark') {
            htmlElement.setAttribute('data-theme', 'dark');
            sunIcon.style.display = 'block';
            moonIcon.style.display = 'none';
            updateAssets('dark');
        }

        themeToggle.addEventListener('click', () => {
            const isDark = htmlElement.getAttribute('data-theme') === 'dark';
            if (isDark) {
                htmlElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
                updateAssets('light');
            } else {
                htmlElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
                updateAssets('dark');
            }
        });

        // 统计点击量
        function addView(cid) {
            const formData = new URLSearchParams();
            formData.append('action', 'add_view');
            formData.append('cid', cid);
            
            // 使用 fetch 确保请求发出并能处理响应
            fetch('<?php $this->options->index(); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            }).then(res => res.json()).then(data => console.log('View recorded:', data));
        }

        // 提取图片主色调并设置发光效果
        function setCardGlow(card, iconUrl) {
            if (!iconUrl || iconUrl.includes('google.com/s2/favicons')) {
                card.style.setProperty('--glow-color', 'rgba(74, 144, 217, 0.4)');
                return;
            }
            const img = new Image();
            const isSameOrigin = iconUrl.indexOf(window.location.origin) === 0 || iconUrl.indexOf('/') === 0;
            if (isSameOrigin) {
                img.src = iconUrl;
                img.onload = function() {
                    try {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        const sampleSize = 20;
                        canvas.width = sampleSize;
                        canvas.height = sampleSize;
                        ctx.drawImage(img, 0, 0, sampleSize, sampleSize);
                        const imageData = ctx.getImageData(0, 0, sampleSize, sampleSize).data;
                        let r = 0, g = 0, b = 0, count = 0;
                        for (let i = 0; i < imageData.length; i += 4) {
                            const currR = imageData[i], currG = imageData[i+1], currB = imageData[i+2], currA = imageData[i+3];
                            if (currA > 125) {
                                const brightness = (currR * 299 + currG * 587 + currB * 114) / 1000;
                                if (brightness > 20 && brightness < 235) {
                                    r += currR; g += currG; b += currB; count++;
                                }
                            }
                        }
                        if (count > 0) {
                            r = Math.floor(r / count); g = Math.floor(g / count); b = Math.floor(b / count);
                            card.style.setProperty('--glow-color', `rgba(${r}, ${g}, ${b}, 0.6)`);
                        }
                    } catch (e) {}
                };
            } else {
                card.style.setProperty('--glow-color', 'rgba(74, 144, 217, 0.4)');
            }
        }

        // 加载分类内容
        function loadCategoryPosts(mid, gridElement, limit = 0) {
            gridElement.style.opacity = '0.5';
            gridElement.innerHTML = Array(8).fill(0).map(() => `
                <div class="card loading-skeleton" style="opacity:1; height:85px; border:none;"></div>
            `).join('');
            
            let isLoaded = false;
            setTimeout(() => {
                if (!isLoaded) {
                    const loadingText = document.createElement('div');
                    loadingText.className = 'loading-text-delayed';
                    loadingText.style.cssText = 'position:absolute; width:100%; text-align:center; padding:20px; color:var(--text-muted); font-size:14px;';
                    loadingText.innerText = '正在努力加载内容...';
                    gridElement.appendChild(loadingText);
                }
            }, 1000);
            
            const formData = new URLSearchParams();
            formData.append('action', 'category');
            formData.append('mid', mid);

            // 针对 by.qhdh.top 的 API 路径精准适配
            let apiUrl = '<?php $this->options->index(); ?>';
            if (!apiUrl.includes('index.php') && !window.location.pathname.includes('index.php')) {
                apiUrl = apiUrl.replace(/\/$/, '') + '/index.php';
            }

            fetch(apiUrl + '?v=' + Math.random(), {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(response => {
                isLoaded = true;
                gridElement.style.opacity = '1';
                gridElement.innerHTML = '';
                
                const data = response.posts || [];
                const categorySlug = response.categorySlug || '';

                if (!data || data.length === 0) {
                    gridElement.innerHTML = '<div class="no-posts">暂无内容</div>';
                    return;
                }

                // 前端截断
                const displayData = limit > 0 ? data.slice(0, limit) : data;

                displayData.forEach((article, index) => {
                    const card = document.createElement('div');
                    card.className = 'card';
                    
                    const siteUrl = '<?php $this->options->siteUrl(); ?>';
                    const pureDomain = siteUrl.replace(/^https?:\/\//, '').replace(/\/$/, '');
                    const resourceType = article.resource_type || 'web';
                    const jumpUrl = (resourceType === 'app') ? article.permalink : (article.website_url + (article.website_url.indexOf('?') !== -1 ? '&' : '?') + 'ref=' + encodeURIComponent(pureDomain));
                    
	                    let platformsHtml = '';
	                    const customIcon = article.custom_icon;
	                    const customText = article.custom_text || '';
	                    
	                    if (article.platforms && article.platforms.length > 0) {
	                        const platforms = [...article.platforms];
	                        // 排序：app 放在最后
	                        platforms.sort((a, b) => (a === 'app' ? 1 : b === 'app' ? -1 : 0));
	                        const hasApp = platforms.includes('app');
	                        
	                        platforms.forEach(platform => {
	                            const p = platform.trim();
	                            // 如果是 app 且有自定义图标，优先显示自定义图标
	                            if (p === 'app' && customIcon) {
	                                platformsHtml += `<div class="platform-icon" title="${customText}"><img src="${customIcon}" alt="custom"></div>`;
	                            }
	                            
	                            const icon = getPlatformIcon(p);
	                            let name = getPlatformName(p);
	                            // 如果是广告位且有自定义文字，显示自定义文字
	                            if (p === 'gan' && article.ad_text) name = article.ad_text;
	                            
	                            if (icon) {
	                                let isAppClass = '';
if (p === 'app') isAppClass = ' platform-app';
else if (p === 'windows') isAppClass = ' platform-windows';
else if (p === 'anzhuo') isAppClass = ' platform-android';
	                                platformsHtml += `<div class="platform-icon${isAppClass}" title="${name}"><img src="${icon}" alt="${name}"></div>`;
	                            }
	                        });
	                        
	                        // 如果没有 app 标签但有自定义图标，也显示出来
	                        if (!hasApp && customIcon) {
	                            platformsHtml += `<div class="platform-icon" title="${customText}"><img src="${customIcon}" alt="custom"></div>`;
	                        }
	                    } else if (customIcon) {
	                        // 如果没有任何平台标签但有自定义图标，直接显示
	                        platformsHtml += `<div class="platform-icon" title="${customText}"><img src="${customIcon}" alt="custom"></div>`;
	                    }

                    if (resourceType === 'post') {
                        card.innerHTML = `
                            <div class="card-main-click" data-cid="${article.cid}" data-url="${article.permalink}">
                                <div class="card-body" style="padding-left: 0;">
                                    <div class="card-title">${article.title}</div>
                                    <div class="card-description">${article.description}</div>
                                </div>
                            </div>
                            <a href="${article.permalink}" class="card-more-btn" title="阅读全文">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                            </a>
                        `;
                    } else {
                        card.innerHTML = `
                            <div class="card-main-click" data-cid="${article.cid}" data-url="${jumpUrl}">
                                <div class="card-icon-wrapper">
                                    <img src="${article.favicon}" class="card-icon" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2252%22 height=%2252%22%3E%3Crect fill=%22%23f0f0f0%22 width=%2252%22 height=%2252%22/%3E%3C/svg%3E'">
                                </div>
                                <div class="card-body">
                                    <div class="card-title">${article.title}</div>
                                    <div class="card-description">${article.description}</div>
                                </div>
                            </div>
                            <div class="card-platforms">${platformsHtml}</div>
                            <a href="${article.permalink}" class="card-more-btn" title="查看详情">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            </a>
                        `;
                    }

                    const mainClick = card.querySelector('.card-main-click');
                    mainClick.addEventListener('click', function() {
                        addView(article.cid);
                        setTimeout(() => {
                            if (resourceType === 'post') {
                                window.location.href = article.permalink;
                            } else {
                                // 恢复手机端在新标签页打开
                                if (resourceType === 'app') {
                                    window.location.href = jumpUrl;
                                } else {
                                    const win = window.open(jumpUrl, '_blank');
                                    if (win) win.opener = null;
                                }
                            }
                        }, 50);
                    });

                    gridElement.appendChild(card);
                    if (article.favicon) setCardGlow(card, article.favicon);
                    setTimeout(() => { card.classList.add('fade-in-up'); }, index * 50);
                });

                // 如果超过限制，显示“更多”按钮（UI 与普通卡片一致）
                if (limit > 0 && data.length > limit && categorySlug) {
                    const moreCard = document.createElement('div');
                    moreCard.className = 'card view-more-card';
                    
                    const categoryUrl = `<?php $this->options->siteUrl(); ?>index.php/category/${categorySlug}/`;
                    
                    moreCard.innerHTML = `
                        <div class="card-main-click" onclick="window.location.href='${categoryUrl}'">
                            <div class="card-icon-wrapper" style="background: var(--bg-secondary); display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted);"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                            </div>
                            <div class="card-body">
                                <div class="card-title">查看更多</div>
                                <div class="card-description">点击进入分类查看该分类下的全部内容</div>
                            </div>
                        </div>
                        <a href="${categoryUrl}" class="card-more-btn" title="进入分类">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    `;
                    gridElement.appendChild(moreCard);
                    setTimeout(() => moreCard.classList.add('fade-in-up'), displayData.length * 50);
                }
            })
            .catch(err => {
                gridElement.innerHTML = '<div class="no-posts">加载失败，请刷新重试</div>';
            });
        }

        // 强力滚动函数 - 恢复原始逻辑以确保在所有浏览器中都能准确跳转
        function forceScrollTo(targetY) {
            const containers = [window, document.documentElement, document.body];
            const startY = window.pageYOffset || document.documentElement.scrollTop;
            const distance = targetY - startY;
            const duration = 600;
            let start = null;

            function step(timestamp) {
                if (!start) start = timestamp;
                const progress = timestamp - start;
                const percentage = Math.min(progress / duration, 1);
                const ease = percentage < 0.5 ? 4 * percentage * percentage * percentage : 1 - Math.pow(-2 * percentage + 2, 3) / 2;
                const currentY = startY + distance * ease;
                containers.forEach(c => {
                    if (c.scrollTo) c.scrollTo(0, currentY);
                    else c.scrollTop = currentY;
                });
                if (progress < duration) window.requestAnimationFrame(step);
                else {
                    containers.forEach(c => {
                        if (c.scrollTo) c.scrollTo(0, targetY);
                        else c.scrollTop = targetY;
                    });
                }
            }
            window.requestAnimationFrame(step);
        }

        // 高亮特效函数
        function highlightSection(section) {
            if (!section) return;
            section.classList.add('highlight-target');
            setTimeout(() => { section.classList.remove('highlight-target'); }, 1500);
        }

        // 初始化加载
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.category-section').forEach(section => {
                const activeTab = section.querySelector('.subcategory-tab.active') || section.querySelector('.subcategory-tab');
                if (activeTab) {
                    activeTab.classList.add('active');
                    const mid = activeTab.getAttribute('data-mid');
                    const limit = parseInt(activeTab.getAttribute('data-limit') || 0);
                    loadCategoryPosts(mid, section.querySelector('.cards-grid'), limit);
                }
            });
        });

        // 处理子分类选项卡点击
        document.querySelectorAll('.subcategory-tab').forEach(btn => {
            btn.addEventListener('click', function() {
                const section = this.closest('.category-section');
                section.querySelectorAll('.subcategory-tab').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const mid = this.getAttribute('data-mid');
                const limit = parseInt(this.getAttribute('data-limit') || 0);
                loadCategoryPosts(mid, section.querySelector('.cards-grid'), limit);
            });
        });

        // 侧边栏子分类菜单逻辑
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
                }, 500);
            });

            // 主导航点击
            const mainLink = wrapper.querySelector('.nav-item');
            if (mainLink) {
                mainLink.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href.includes('#')) {
                        e.preventDefault();
                        const slug = decodeURIComponent(href.split('#')[1]);
                        const section = document.getElementById(slug);
                        if (section) {
                            const targetY = section.getBoundingClientRect().top + (window.pageYOffset || document.documentElement.scrollTop) - 20;
                            forceScrollTo(targetY);
                            highlightSection(section);
                            history.pushState(null, null, '#' + slug);
                        }
                    }
                });
            }

            // 子分类菜单点击
            const subLinks = wrapper.querySelectorAll('.sub-nav-link');
            subLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href.includes('#')) {
                        e.preventDefault();
                        const slug = decodeURIComponent(href.split('#')[1]);
                        const targetTab = document.querySelector(`.subcategory-tab[data-slug="${slug}"]`);
                        if (targetTab) {
                            targetTab.click();
                            const section = targetTab.closest('.category-section');
                            if (section) {
                                const targetY = section.getBoundingClientRect().top + (window.pageYOffset || document.documentElement.scrollTop) - 20;
                                forceScrollTo(targetY);
                                highlightSection(section);
                            }
                            history.pushState(null, null, '#' + slug);
                        }
                        
                        // 手机端点击后立即隐藏菜单
                        const currentMenu = this.closest('.sub-nav-menu');
                        if (currentMenu && window.innerWidth <= 768) {
                            currentMenu.style.display = 'none';
                            if (activeMenu === currentMenu) activeMenu = null;
                        }
                    }
                });
            });
        });

        // 平台图标映射
        function getPlatformIcon(p) {
            const m = {'ios':'/ico/ios.png','iosz':'/ico/ios.png','anzhuo':'/ico/anzhuo.png','app':'/ico/app.png','windows':'/ico/windows.png','mac':'/ico/mac.png','tv':'/ico/tv.png','che':'/ico/che.png','qian':'/ico/qian.png','fan':'/ico/fan.png','en':'/ico/en.png','gan':'/ico/gan.png'};
            return m[p] || '';
        }

        function getPlatformName(p) {
            const m = {'ios':'iOS端','iosz':'iOS端(需要自签名)','anzhuo':'安卓Android/鸿蒙端','app':'该软件是APP，没有网页版','windows':'Windows端','mac':'Mac端','tv':'TV端','che':'车机端','qian':'部分影视资源需要收费','fan':'繁体中文页面','en':'英语页面','gan':'有广告'};
            return m[p] || p;
        }
    </script>
    <?php $this->need('footer.php'); ?>

