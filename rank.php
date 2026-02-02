<?php
/**
 * 观影导航主题 - 排行榜
 * Template Name: 排行榜
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>点击量排行榜 - <?php $this->options->title() ?></title>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>">
    <link rel="icon" id="dynamic-favicon" href="/favicon.ico" type="image/x-icon">
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
        /* 排行榜页面特有布局优化 */
        .rank-single-column { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .rank-page-title { margin-bottom: 30px; font-size: 24px; font-weight: 600; }
        
        .rank-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 20px; }
        @media (max-width: 1024px) { .rank-grid { grid-template-columns: 1fr; } }
        
        .rank-column { background: var(--bg-content); border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid var(--border-color); transition: all 0.3s ease; }
        .rank-column-title { display: flex; align-items: center; gap: 10px; font-size: 18px; font-weight: 600; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color); }
        
        .rank-item { opacity: 0; transform: translateY(10px); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); display: flex; align-items: center; padding: 4px 10px; margin-bottom: 4px; background: var(--bg-secondary); border-radius: 12px; text-decoration: none; color: inherit; border: 1px solid transparent; }
        .rank-item.show { opacity: 1; transform: translateY(0); }
        .rank-item:hover { background: var(--bg-hover); transform: translateX(6px); border-color: var(--accent-color, #007bff); }
        
        .rank-num { width: 24px; height: 24px; background: var(--border-color); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; margin-right: 8px; flex-shrink: 0; color: var(--text-muted); }
        .rank-item:nth-child(1) .rank-num { background: #FFD700; color: #fff; box-shadow: 0 2px 8px rgba(255,215,0,0.3); }
        .rank-item:nth-child(2) .rank-num { background: #C0C0C0; color: #fff; box-shadow: 0 2px 8px rgba(192,192,192,0.3); }
        .rank-item:nth-child(3) .rank-num { background: #CD7F32; color: #fff; box-shadow: 0 2px 8px rgba(205,127,50,0.3); }
        
        .rank-icon { width: 60px; height: 60px; border-radius: 8px; margin-right: 10px; object-fit: contain; background: #fff; padding: 4px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
        [data-theme="dark"] .rank-icon { background: #2a2a2a; }
        
        .rank-info { flex: 1; min-width: 0; }
        .rank-name { display: block; font-size: 15px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
        .rank-meta { font-size: 12px; color: var(--text-muted); }
        .rank-count { font-weight: 700; color: var(--accent-color, #007bff); }

        /* 侧边栏悬停下拉框样式补全 */
        .nav-item-wrapper { position: relative; }
        .sub-nav-menu { 
            position: absolute; left: 100%; top: 0; background: var(--bg-content); 
            border: 1px solid var(--border-color); border-radius: 12px; padding: 8px; 
            min-width: 160px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            opacity: 0; visibility: hidden; transform: translateX(10px); 
            transition: all 0.3s ease; z-index: 1000; margin-left: 10px;
        }
        .nav-item-wrapper:hover .sub-nav-menu { opacity: 1; visibility: visible; transform: translateX(0); }
        .sub-nav-link { 
            display: block; padding: 8px 12px; border-radius: 8px; color: var(--text-main); 
            text-decoration: none; font-size: 13px; transition: all 0.2s; 
        }
        .sub-nav-link:hover { background: var(--bg-hover); color: var(--accent-color, #007bff); }
    </style>
</head>
<body class="rank-page">
    <!-- 顶部工具栏 (完全对齐首页) -->
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
        <main class="main-content fade-in-up">
            <div class="rank-page-container rank-single-column">
                <h1 class="rank-page-title">点击量排行榜</h1>
                <div class="rank-grid">
                    <div class="rank-column">
                        <h2 class="rank-column-title">今日热榜</h2>
                        <div class="rank-list" id="rank-day"></div>
                    </div>
                    <div class="rank-column">
                        <h2 class="rank-column-title">本周排行</h2>
                        <div class="rank-list" id="rank-week"></div>
                    </div>
                    <div class="rank-column">
                        <h2 class="rank-column-title">全站总榜</h2>
                        <div class="rank-list" id="rank-total"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // 1. 夜间模式逻辑 (完全对齐首页)
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

        // 2. 排行榜数据抓取
        function fetchRank(type, elementId) {
            const container = document.getElementById(elementId);
            container.innerHTML = '<div style="text-align:center; padding:40px; color:var(--text-muted); font-size:14px;">加载中...</div>';
            
            const formData = new URLSearchParams();
            formData.append('action', 'get_rank');
            formData.append('type', type);
            
            fetch('<?php $this->options->index(); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';
                if (!data || !data.length) {
                    container.innerHTML = '<div style="text-align:center; padding:40px; color:var(--text-muted); font-size:14px;">暂无数据</div>';
                    return;
                }
                data.forEach((item, index) => {
                    const a = document.createElement('a');
                    a.className = 'rank-item';
                    a.href = item.permalink;
                    a.target = '_blank';
                    a.innerHTML = `
                        <div class="rank-num">${index + 1}</div>
                        <img src="${item.favicon}" class="rank-icon" onerror="this.src='/favicon.ico'">
                        <div class="rank-info">
                            <span class="rank-name">${item.title}</span>
                            <div class="rank-meta"><span class="rank-count">${item.views}</span> 次点击</div>
                        </div>
                    `;
                    container.appendChild(a);
                    setTimeout(() => a.classList.add('show'), index * 50);
                });
            })
            .catch(() => {
                container.innerHTML = '<div style="text-align:center; padding:40px; color:var(--text-muted); font-size:14px;">加载失败</div>';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchRank('day', 'rank-day');
            fetchRank('week', 'rank-week');
            fetchRank('total', 'rank-total');
        });
    </script>
    <?php $this->need('footer.php'); ?>