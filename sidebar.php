<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?php $this->options->siteUrl(); ?>" class="site-logo-link">
            <img src="/logo.png" alt="<?php $this->options->title(); ?>" class="site-logo" id="main-site-logo">
        </a>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-main">
            <?php 
            buildCategoryTree();
            global $tree;
            if (!empty($tree)) {
                foreach ($tree as $category) {
                    if ($category['isHidden']) continue; // 隐藏分类
                    $iconHtml = !empty($category['icon']) ? '<img src="' . htmlspecialchars($category['icon']) . '" class="nav-icon">' : '';
                    
                    echo '<div class="nav-item-wrapper">';
                    echo '<a href="' . (Helper::options()->siteUrl) . '#' . htmlspecialchars($category['slug']) . '" class="nav-item">' . $iconHtml . '<span>' . htmlspecialchars($category['name']) . '</span></a>';
                    
                    // 子分类悬停菜单
                    if (!empty($category['children'])) {
                        echo '<div class="sub-nav-menu">';
                        foreach ($category['children'] as $sub) {
                            if ($sub['isHidden']) continue;
                            echo '<a href="' . (Helper::options()->siteUrl) . '#' . htmlspecialchars($sub['slug']) . '" class="sub-nav-link">' . htmlspecialchars($sub['name']) . '</a>';
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                }
            } ?>
        </div>
        
        <div class="nav-bottom">
            <?php 
            $bottomNavStr = $this->options->bottomNav;
            $bottomNavArray = !empty($bottomNavStr) ? explode("\n", str_replace("\r", "", $bottomNavStr)) : [];
            ?>

            <!-- 电脑端专用：自定义导航 (通过 CSS 控制显示，HTML 结构唯一) -->
            <div class="nav-bottom-custom pc-only-nav">
                <?php 
                foreach ($bottomNavArray as $navLine) {
                    $parts = explode(",", $navLine);
                    if (count($parts) >= 3) {
                        $icon = trim($parts[0]);
                        $name = trim($parts[1]);
                        $url = trim($parts[2]);
                        echo '<a href="' . htmlspecialchars($url) . '" class="nav-bottom-item">
                                <img src="' . htmlspecialchars($icon) . '" class="nav-bottom-icon">
                                <span>' . htmlspecialchars($name) . '</span>
                              </a>';
                    }
                }
                ?>
            </div>

            <!-- 手机端专用：更多按钮 (通过 CSS 控制显示) -->
            <div class="nav-item mobile-only-more" id="mobile-more-btn">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="1"></circle>
                    <circle cx="19" cy="12" r="1"></circle>
                    <circle cx="5" cy="12" r="1"></circle>
                </svg>
                <span>更多</span>
            </div>
        </div>

        <!-- 手机端专用：更多向右展开菜单 -->
        <div id="more-side-menu" class="more-side-menu">
            <div class="more-nav-list">
                <?php 
                foreach ($bottomNavArray as $navLine) {
                    $parts = explode(",", $navLine);
                    if (count($parts) >= 3) {
                        $icon = trim($parts[0]);
                        $name = trim($parts[1]);
                        $url = trim($parts[2]);
                        echo '<a href="' . htmlspecialchars($url) . '" class="more-nav-item">
                                <img src="' . htmlspecialchars($icon) . '" class="more-nav-icon">
                                <span>' . htmlspecialchars($name) . '</span>
                              </a>';
                    }
                }
                ?>
            </div>
        </div>

        <script>
        (function() {
            // 增强版点击逻辑：支持触摸和点击，并确保在 DOM 加载后立即绑定
            function initMoreBtn() {
                const moreBtn = document.getElementById('mobile-more-btn');
                const moreMenu = document.getElementById('more-side-menu');

                if (moreBtn && moreMenu) {
                    const toggleMenu = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        moreMenu.classList.toggle('active');
                    };

                    moreBtn.addEventListener('click', toggleMenu);
                    moreBtn.addEventListener('touchend', toggleMenu);

                    document.addEventListener('click', function(event) {
                        if (!moreMenu.contains(event.target) && event.target !== moreBtn && !moreBtn.contains(event.target)) {
                            moreMenu.classList.remove('active');
                        }
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initMoreBtn);
            } else {
                initMoreBtn();
            }
        })();
        </script>
    </nav>
</aside>
