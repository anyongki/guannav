<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 导航主题函数文件 - 终极全功能修复版
 */

/**
 * 0. 数据库自动升级逻辑
 */
function initThemeDatabase() {
    $db = \Typecho\Db::get();
    $prefix = $db->getPrefix();
    $table = $prefix . 'contents';
    try { $db->query("ALTER TABLE `{$table}` ADD COLUMN `views_day` INT(10) UNSIGNED DEFAULT 0"); } catch (Exception $e) {}
    try { $db->query("ALTER TABLE `{$table}` ADD COLUMN `views_week` INT(10) UNSIGNED DEFAULT 0"); } catch (Exception $e) {}
    try { $db->query("ALTER TABLE `{$table}` ADD COLUMN `views_last_update` INT(10) UNSIGNED DEFAULT 0"); } catch (Exception $e) {}
}
initThemeDatabase();

/**
 * 1. AJAX 处理逻辑
 */
function handleThemeAjax() {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $db = \Typecho\Db::get();
        $prefix = $db->getPrefix();
        
        if ($action === 'fetch_favicon') {
            ob_clean();
            $url = $_POST['url'];
            $path = saveFaviconLocalFinal($url);
            header('Content-Type: application/json');
            echo json_encode($path ? array('success' => true, 'path' => $path) : array('success' => false));
            exit;
        }
        
        if ($action === 'generate_favicon') {
            ob_clean();
            $title = $_POST['title'];
            $url = $_POST['url'];
            $path = generateTextIcon($title, $url);
            header('Content-Type: application/json');
            echo json_encode($path ? array('success' => true, 'path' => $path) : array('success' => false));
            exit;
        }

        if ($action === 'category') {
            ob_clean();
            $mid = intval($_POST['mid']);
            try {
                $category = $db->fetchRow($db->select('slug')->from('table.metas')->where('mid = ?', $mid));
                $categorySlug = $category ? $category['slug'] : '';
                $sql = "SELECT p.cid, p.title, p.slug, p.text, f1.str_value as website_url, f2.str_value as favicon, f3.str_value as description, f4.str_value as platforms, f5.str_value as sort_order, f6.str_value as ad_text, f7.str_value as custom_icon, f8.str_value as custom_text, f9.str_value as resource_type FROM {$prefix}contents p INNER JOIN {$prefix}relationships r ON p.cid = r.cid LEFT JOIN {$prefix}fields f1 ON p.cid = f1.cid AND f1.name = 'website_url' LEFT JOIN {$prefix}fields f2 ON p.cid = f2.cid AND f2.name = 'favicon' LEFT JOIN {$prefix}fields f3 ON p.cid = f3.cid AND f3.name = 'description' LEFT JOIN {$prefix}fields f4 ON p.cid = f4.cid AND f4.name = 'platforms' LEFT JOIN {$prefix}fields f5 ON p.cid = f5.cid AND f5.name = 'sort_order' LEFT JOIN {$prefix}fields f6 ON p.cid = f6.cid AND f6.name = 'ad_text' LEFT JOIN {$prefix}fields f7 ON p.cid = f7.cid AND f7.name = 'custom_icon' LEFT JOIN {$prefix}fields f8 ON p.cid = f8.cid AND f8.name = 'custom_text' LEFT JOIN {$prefix}fields f9 ON p.cid = f9.cid AND f9.name = 'resource_type' WHERE r.mid = {$mid} AND p.type = 'post' AND p.status = 'publish' ORDER BY CAST(f5.str_value AS UNSIGNED) DESC, p.created DESC";
                $posts = $db->fetchAll($sql);
                $result = array();
                foreach ($posts as $post) {
                    $favicon = $post['favicon'] ?: ($post['website_url'] ? "https://api.xinac.net/icon/?url=" . urlencode($post['website_url'] ) : "/favicon.ico");
                    $result[] = array('title' => $post['title'], 'favicon' => $favicon, 'description' => $post['description'] ?: mb_substr(strip_tags($post['text']), 0, 50), 'website_url' => $post['website_url'] ?: '#', 'platforms' => parsePlatforms($post['platforms']), 'ad_text' => $post['ad_text'] ?: '有广告', 'custom_icon' => $post['custom_icon'] ?: '', 'custom_text' => $post['custom_text'] ?: '', 'cid' => $post['cid'], 'permalink' => rtrim(Helper::options()->index, '/') . '/archives/' . $post['cid'] . '/', 'resource_type' => $post['resource_type'] ?: 'web');
                }
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array('posts' => $result, 'categorySlug' => $categorySlug), JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) { echo json_encode(array('posts' => array(), 'categorySlug' => '')); }
            exit;
        }

        if ($action === 'get_rank') {
            ob_clean();
            $type = $_POST['type'] ?: 'day';
            try {
                $now = time();
                $todayStart = strtotime(date('Y-m-d 00:00:00', $now));
                $weekStart = strtotime('this week monday', $now);
                $filterSql = " AND p.cid IN (SELECT cid FROM {$prefix}fields WHERE name = 'resource_type' AND str_value IN ('web', 'app'))";
                if ($type === 'day') $sql = "SELECT p.cid, p.title, p.views_day as views, f1.str_value as favicon, f2.str_value as website_url FROM {$prefix}contents p LEFT JOIN {$prefix}fields f1 ON p.cid = f1.cid AND f1.name = 'favicon' LEFT JOIN {$prefix}fields f2 ON p.cid = f2.cid AND f2.name = 'website_url' WHERE p.type = 'post' AND p.status = 'publish' AND p.views_day > 0 AND p.views_last_update >= {$todayStart} {$filterSql} ORDER BY p.views_day DESC LIMIT 10";
                elseif ($type === 'week') $sql = "SELECT p.cid, p.title, p.views_week as views, f1.str_value as favicon, f2.str_value as website_url FROM {$prefix}contents p LEFT JOIN {$prefix}fields f1 ON p.cid = f1.cid AND f1.name = 'favicon' LEFT JOIN {$prefix}fields f2 ON p.cid = f2.cid AND f2.name = 'website_url' WHERE p.type = 'post' AND p.status = 'publish' AND p.views_week > 0 AND p.views_last_update >= {$weekStart} {$filterSql} ORDER BY p.views_week DESC LIMIT 10";
                else $sql = "SELECT p.cid, p.title, p.views, f1.str_value as favicon, f2.str_value as website_url FROM {$prefix}contents p LEFT JOIN {$prefix}fields f1 ON p.cid = f1.cid AND f1.name = 'favicon' LEFT JOIN {$prefix}fields f2 ON p.cid = f2.cid AND f2.name = 'website_url' WHERE p.type = 'post' AND p.status = 'publish' {$filterSql} ORDER BY p.views DESC LIMIT 10";
                $results = $db->fetchAll($sql);
                $data = array();
                foreach ($results as $row) {
                    $favicon = $row['favicon'] ?: ($row['website_url'] ? "https://api.xinac.net/icon/?url=" . urlencode($row['website_url'] ) : "/favicon.ico");
                    $data[] = array('title' => $row['title'], 'permalink' => rtrim(Helper::options()->index, '/') . '/archives/' . $row['cid'] . '/', 'views' => (int)$row['views'], 'favicon' => $favicon);
                }
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) { echo json_encode(array()); }
            exit;
        }

        if ($action === 'add_view') {
            ob_clean();
            $cid = intval($_POST['cid']);
            if ($cid > 0) {
                $now = time();
                $post = $db->fetchRow($db->select('views', 'views_day', 'views_week', 'views_last_update')->from('table.contents')->where('cid = ?', $cid));
                if ($post) {
                    $views = intval($post['views']) + 1;
                    $lastUpdate = intval($post['views_last_update']);
                    $viewsDay = (date('Ymd', $lastUpdate) != date('Ymd', $now)) ? 1 : intval($post['views_day']) + 1;
                    $viewsWeek = (date('oW', $lastUpdate) != date('oW', $now)) ? 1 : intval($post['views_week']) + 1;
                    $db->query($db->update('table.contents')->rows(array('views' => $views, 'views_day' => $viewsDay, 'views_week' => $viewsWeek, 'views_last_update' => $now))->where('cid = ?', $cid));
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'success', 'day' => $viewsDay, 'week' => $viewsWeek));
                }
            }
            exit;
        }

        if ($action === 'like') {
            ob_clean();
            $cid = intval($_POST['cid']);
            $row = $db->fetchRow($db->select('str_value')->from('table.fields')->where('cid = ? AND name = ?', $cid, 'likes'));
            $newLikes = ($row ? intval($row['str_value']) : 0) + 1;
            if ($row) $db->query($db->update('table.fields')->rows(array('str_value' => $newLikes))->where('cid = ? AND name = ?', $cid, 'likes'));
            else $db->query($db->insert('table.fields')->rows(array('cid' => $cid, 'name' => 'likes', 'type' => 'str', 'str_value' => $newLikes, 'int_value' => 0, 'float_value' => 0)));
            echo json_encode(array('success' => true, 'count' => $newLikes));
            exit;
        }
    }
}
handleThemeAjax();

/**
 * 2. 前台核心函数
 */
$tree = array();
function buildCategoryTree() {
    global $tree;
    $tree = array();
    try {
        $db = \Typecho\Db::get();
        $categories = $db->fetchAll($db->select()->from('table.metas')->where('type = ?', 'category')->order('order', \Typecho\Db::SORT_ASC));
        foreach ($categories as $cat) {
            if ($cat['parent'] == 0) {
                $description = $cat['description'] ?? '';
                $icon = (!empty($description) && (strpos($description, 'http' ) === 0 || strpos($description, '/') === 0)) ? $description : '';
                $limit = preg_match('/(\d+)/', $description, $matches) ? intval($matches[1]) : 21;
                $tree[$cat['mid']] = array('mid' => $cat['mid'], 'name' => $cat['name'], 'slug' => $cat['slug'], 'parent' => $cat['parent'], 'icon' => $icon, 'isHidden' => (strpos($description, '隐藏') !== false), 'limit' => $limit, 'children' => array());
            }
        }
        foreach ($categories as $cat) {
            if ($cat['parent'] != 0 && isset($tree[$cat['parent']])) {
                $description = $cat['description'] ?? '';
                $limit = preg_match('/(\d+)/', $description, $matches) ? intval($matches[1]) : 21;
                $tree[$cat['parent']]['children'][] = array('mid' => $cat['mid'], 'name' => $cat['name'], 'slug' => $cat['slug'], 'parent' => $cat['parent'], 'isHidden' => (strpos($description, '隐藏') !== false), 'limit' => $limit);
            }
        }
    } catch (Exception $e) {}
}
buildCategoryTree();

/**
 * 3. 图标抓取与生成逻辑
 */
function saveFaviconLocalFinal($websiteUrl) {
    if (empty($websiteUrl)) return '';
    $host = parse_url($websiteUrl, PHP_URL_HOST) ?: 'site-' . time();
    $uploadDir = __TYPECHO_ROOT_DIR__ . '/usr/uploads/favicons/';
    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);
    $apiUrl = "https://api.xinac.net/icon/?url=" . urlencode($websiteUrl );
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_TIMEOUT => 15, CURLOPT_USERAGENT => 'Mozilla/5.0']);
    $data = curl_exec($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    if ($data) {
        $ext = (strpos($contentType, 'icon') !== false) ? 'ico' : ((strpos($contentType, 'jpeg') !== false) ? 'jpg' : ((strpos($contentType, 'svg') !== false) ? 'svg' : 'png'));
        $filename = $host . '.' . $ext;
        if (@file_put_contents($uploadDir . $filename, $data)) return '/usr/uploads/favicons/' . $filename;
    }
    return ''; 
}

function generateTextIcon($title, $url) {
    $char = mb_substr($title, 0, 1, 'utf-8') ?: '?';
    $host = parse_url($url, PHP_URL_HOST) ?: 'gen-' . time();
    $uploadDir = __TYPECHO_ROOT_DIR__ . '/usr/uploads/favicons/';
    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);
    $size = 200; $img = imagecreatetruecolor($size, $size);
    $bgColors = [[52, 152, 219], [231, 76, 60], [46, 204, 113], [155, 89, 182], [241, 196, 15], [52, 73, 94]];
    $bg = $bgColors[array_rand($bgColors)];
    imagefill($img, 0, 0, imagecolorallocate($img, $bg[0], $bg[1], $bg[2]));
    $fontPath = __DIR__ . '/font.ttf'; $white = imagecolorallocate($img, 255, 255, 255);
    if (file_exists($fontPath)) {
        $fontSize = $size * 0.4; $bbox = imagettfbbox($fontSize, 0, $fontPath, $char);
        imagettftext($img, $fontSize, 0, ($size-($bbox[2]-$bbox[0]))/2, ($size-($bbox[1]-$bbox[7]))/2-$bbox[7], $white, $fontPath, $char);
    } else { imagestring($img, 5, $size/2-10, $size/2-10, $char, $white); }
    $filename = $host . '-' . date('Ymd') . '-gen.png';
    imagepng($img, $uploadDir . $filename); imagedestroy($img);
    return '/usr/uploads/favicons/' . $filename;
}

/**
 * 4. 工具函数
 */
function get_like_count($cid) {
    $db = \Typecho\Db::get();
    $row = $db->fetchRow($db->select('str_value')->from('table.fields')->where('cid = ? AND name = ?', $cid, 'likes'));
    return $row ? intval($row['str_value']) : 0;
}

function get_post_view($archive) {
    $cid = $archive->cid; $db = \Typecho\Db::get();
    $post = $db->fetchRow($db->select('views', 'views_day', 'views_week', 'views_last_update')->from('table.contents')->where('cid = ?', $cid));
    $views = intval($post['views']);
    if ($archive->is('single')) {
        $views++; $now = time(); $lastUpdate = intval($post['views_last_update']);
        $viewsDay = (date('Ymd', $lastUpdate) != date('Ymd', $now)) ? 1 : intval($post['views_day']) + 1;
        $viewsWeek = (date('oW', $lastUpdate) != date('oW', $now)) ? 1 : intval($post['views_week']) + 1;
        $db->query($db->update('table.contents')->rows(array('views' => $views, 'views_day' => $viewsDay, 'views_week' => $viewsWeek, 'views_last_update' => $now))->where('cid = ?', $cid));
    }
    return $views;
}

function parsePlatforms($data) {
    if (empty($data)) return array();
    if (is_array($data)) return $data;
    if (is_string($data)) {
        if (strpos($data, '[') === 0) return json_decode($data, true) ?: array();
        return array_filter(array_map('trim', explode(',', $data)));
    }
    return array();
}

function getPlatformIcon($p) {
    $m = array(
        'ios'=>'/ico/ios.png',
        'iosz'=>'/ico/ios.png',
        'anzhuo'=>'/ico/anzhuo.png',
        'app'=>'/ico/app.png',
        'windows'=>'/ico/windows.png',
        'mac'=>'/ico/mac.png',
        'tv'=>'/ico/tv.png',
        'che'=>'/ico/che.png',
        'qian'=>'/ico/qian.png',
        'fan'=>'/ico/fan.png',
        'en'=>'/ico/en.png',
        'gan'=>'/ico/gan.png'
    );
    return isset($m[$p]) ? $m[$p] : '';
}

function getPlatformName($p) {
    $m = array(
        'ios'=>'iOS端',
        'iosz'=>'iOS端(需要自签名)',
        'anzhuo'=>'安卓Android/鸿蒙端',
        'app'=>'该软件是APP，没有网页版',
        'windows'=>'Windows端',
        'mac'=>'MacOS端',
        'tv'=>'TV端',
        'che'=>'车机端',
        'qian'=>'部分影视资源需要收费',
        'fan'=>'繁体中文页面',
        'en'=>'英语页面',
        'gan'=>'有广告'
    );
    return isset($m[$p]) ? $m[$p] : '';
}

/**
 * 5. 后台自定义字段
 */
function themeFields($layout) {
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Select('resource_type', array('web' => '网站', 'app' => 'APP', 'post' => '文章'), 'web', _t('资源类型')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('website_url', NULL, NULL, _t('网站 URL')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('favicon', NULL, NULL, _t('网站图标')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Textarea('description', NULL, NULL, _t('网站简介')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('sort_order', NULL, '0', _t('排序权重')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Checkbox('platforms', array(
        'ios'=>'iOS',
        'iosz'=>'iOS(自签)',
        'anzhuo'=>'Android',
        'app'=>'APP',
        'windows'=>'Windows',
        'mac'=>'Mac',
        'tv'=>'TV',
        'che'=>'车机',
        'qian'=>'收费',
        'fan'=>'繁体',
        'en'=>'英语',
        'gan'=>'有广告'
    ), array(), _t('支持平台')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('ad_text', NULL, '有广告', _t('有广告悬停文字')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('custom_icon', NULL, NULL, _t('自定义图标')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('custom_text', NULL, NULL, _t('自定义文字')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Hidden('download_links', NULL, NULL, _t('下载链接数据')));

    echo '<style>
        .favicon-btn-container{display:flex;gap:10px;margin:5px 0 15px}
        .favicon-btn{cursor:pointer;padding:0 15px;height:32px;color:#fff;border:none;border-radius:4px;font-size:13px}
        #btn-fetch-favicon{background:#467b96}
        #btn-gen-favicon{background:#6c757d}
        .download-links-manager { background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px; padding: 15px; margin-top: 10px; }
        .download-link-item { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .dl-platform { width: 120px; }
        .dl-url { flex: 1; }
        .btn-del-link { color: #d9534f; cursor: pointer; font-weight: bold; }
        .btn-add-link { margin-top: 10px; background: #5cb85c; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; }
    </style>';

    echo '<script>(function(){
        function init(){
            var f = document.querySelector("input[name=\'fields[favicon]\']"),
                u = document.querySelector("input[name=\'fields[website_url]\']"),
                t = document.querySelector("input[name=\'title\']"),
                dlInput = document.querySelector("input[name=\'fields[download_links]\']");
            
            if(dlInput && !document.querySelector(".download-links-manager")){
                var manager = document.createElement("div");
                manager.className = "download-links-manager";
                manager.innerHTML = "<h4>下载链接管理</h4><div id=\'dl-list\'></div><button type=\'button\' class=\'btn-add-link\' id=\'btn-add-dl\'>+ 添加链接</button>";
                dlInput.parentNode.insertBefore(manager, dlInput.nextSibling);
                
                var dlList = document.getElementById("dl-list");
                var btnAdd = document.getElementById("btn-add-dl");
                
                function renderLinks(){
                    dlList.innerHTML = "";
                    var data = [];
                    try { data = JSON.parse(dlInput.value || "[]"); } catch(e) {}
                    data.forEach(function(item, index){ addLinkRow(item.platform, item.url, index); });
                }
                
                function addLinkRow(platform, url, index){
                    var row = document.createElement("div");
                    row.className = "download-link-item";
                    row.innerHTML = \'<input type="text" class="text dl-platform" placeholder="平台(如ios)" value="\' + platform + \'">\' +
                                    \'<input type="text" class="text dl-url" placeholder="下载链接" value="\' + url + \'">\' +
                                    \'<span class="btn-del-link" title="删除">×</span>\';
                    
                    row.querySelector(".btn-del-link").onclick = function(){
                        row.remove();
                        updateData();
                    };
                    
                    row.querySelectorAll("input").forEach(function(input) {
                        input.oninput = updateData;
                    });
                    
                    dlList.appendChild(row);
                }
                
                function updateData() {
                    var links = [];
                    document.querySelectorAll(".download-link-item").forEach(function(row) {
                        var p = row.querySelector(".dl-platform").value.trim();
                        var u = row.querySelector(".dl-url").value.trim();
                        if (p || u) links.push({platform: p, url: u});
                    });
                    dlInput.value = JSON.stringify(links);
                }
                
                btnAdd.onclick = function() {
                    addLinkRow("", "", dlList.children.length);
                    updateData();
                };
                
                renderLinks();
            }

            if(f && u && !document.getElementById("btn-fetch-favicon")){
                var c = document.createElement("div");
                c.className = "favicon-btn-container";
                c.innerHTML = \'<button id="btn-fetch-favicon" type="button" class="favicon-btn">一键获取图标</button>\' +
                              \'<button id="btn-gen-favicon" type="button" class="favicon-btn">生成文字图标</button>\';
                f.parentNode.insertBefore(c, f.nextSibling);
                document.getElementById("btn-fetch-favicon").onclick = function(e){
                    e.preventDefault(); var url = u.value.trim(); if(!url){ alert("请先输入网站 URL"); return; }
                    this.innerText = "获取中..."; this.disabled = true;
                    var fd = new FormData(); fd.append("action", "fetch_favicon"); fd.append("url", url);
                    fetch(window.location.href, {method: "POST", body: fd})
                        .then(r => r.json()).then(d => { if(d.success){ f.value = d.path; this.innerText = "获取成功"; } else alert("获取失败"); })
                        .catch(() => alert("请求出错")).finally(() => { this.innerText = "一键获取图标"; this.disabled = false; });
                };
                document.getElementById("btn-gen-favicon").onclick = function(e){
                    e.preventDefault(); var title = t.value.trim(); if(!title){ alert("请先输入文章标题"); return; }
                    this.innerText = "生成中..."; this.disabled = true;
                    var fd = new FormData(); fd.append("action", "generate_favicon"); fd.append("title", title); fd.append("url", u.value);
                    fetch(window.location.href, {method: "POST", body: fd})
                        .then(r => r.json()).then(d => { if(d.success){ f.value = d.path; this.innerText = "生成成功"; } else alert("生成失败"); })
                        .catch(() => alert("请求出错")).finally(() => { this.innerText = "生成文字图标"; this.disabled = false; });
                };
            }
        }
        setTimeout(init, 500);
    })()</script>';
}

/**
 * 6. 主题配置项
 */
function themeConfig($form) {
    $logoUrl = new Typecho_Widget_Helper_Form_Element_Text('logoUrl', NULL, NULL, _t('Logo 地址'), _t('在这里输入站点的 Logo 图片 URL'));
    $form->addInput($logoUrl);

    $bottomNav = new Typecho_Widget_Helper_Form_Element_Textarea('bottomNav', NULL, NULL, _t('左下角导航'), _t('每行一个，格式：图标URL,名称,链接'));
    $form->addInput($bottomNav);

    $links = new Typecho_Widget_Helper_Form_Element_Textarea('links', NULL, NULL, _t('友情链接'), _t('每行一个，格式：图标URL,名称,链接 或 名称,链接'));
    $form->addInput($links);

    $analysisCode = new Typecho_Widget_Helper_Form_Element_Textarea('analysisCode', NULL, NULL, _t('统计代码'), _t('在这里粘贴您的统计代码（如百度统计、Google Analytics）。'));
    $form->addInput($analysisCode);

    $customJs = new Typecho_Widget_Helper_Form_Element_Textarea('customJs', NULL, NULL, _t('自定义 JS 代码'), _t('在这里粘贴您的自定义 JS 代码。'));
    $form->addInput($customJs);

    // 公告设置
    $noticeContent = new Typecho_Widget_Helper_Form_Element_Textarea('noticeContent', NULL, _t("观影导航试运行中\n等待主域名备案"), _t('公告内容'), _t('在这里输入公告内容，支持换行。'));
    $form->addInput($noticeContent);

    $noticeFrequency = new Typecho_Widget_Helper_Form_Element_Radio('noticeFrequency', array('once' => _t('每天显示一次'), 'always' => _t('始终显示')), 'once', _t('公告显示频率'));
    $form->addInput($noticeFrequency);

    $noticeScope = new Typecho_Widget_Helper_Form_Element_Radio('noticeScope', array('all' => _t('全站显示'), 'index' => _t('仅首页显示')), 'all', _t('公告显示范围'));
    $form->addInput($noticeScope);
}

/**
 * 7. 输出自定义代码逻辑（含升级版公告脚本）
 */
function theme_header_custom_code() {
    $options = \Typecho\Widget::widget('Widget_Options');
    if (!empty($options->analysisCode)) {
        echo $options->analysisCode . "\n";
    }
    // 智能兼容输出：支持 Meta 标签、带标签的 JS 和纯 JS 代码
    if (!empty($options->customJs)) {
        $lines = explode("\n", $options->customJs);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            if (strpos($line, '<') !== 0) {
                echo '<script>' . $line . '</script>' . "\n";
            } else {
                echo $line . "\n";
            }
        }
    }
}

function theme_footer_custom_code() {
    $options = \Typecho\Widget::widget('Widget_Options');
    
    // 修改点：此处不再输出自定义 JS
    
    // 2. 输出升级版公告脚本
    if (!empty($options->noticeContent)) {
        // 增加显示范围判断
        $scope = $options->noticeScope ? $options->noticeScope : 'all';
        if ($scope === 'index' && !\Typecho\Widget::widget('Widget_Archive')->is('index')) {
            return;
        }

        $content = str_replace(array("\r\n", "\r", "\n"), "\\n", addslashes($options->noticeContent));
        $frequency = $options->noticeFrequency ? $options->noticeFrequency : 'once';
        
        echo '<script>
        (function() {
            const config = {
                content: "' . $content . '",
                frequency: "' . $frequency . '",
                duration: 16000
            };

            const storageKey = "manus_notice_last_shown";
            const now = new Date().getTime();
            if (config.frequency === "once") {
                const lastShown = localStorage.getItem(storageKey);
                if (lastShown && (now - lastShown < 86400000)) return;
            }

            const style = document.createElement("style");
            style.textContent = `
                @keyframes glassSlideIn {
                    from { opacity: 0; transform: translateX(50px); filter: blur(10px); }
                    to { opacity: 1; transform: translateX(0); filter: blur(0); }
                }
                .ultra-glass-notice {
                    position: fixed;
                    bottom: 40px;
                    right: 30px;
                    z-index: 2147483647;
                    width: 280px;
                    padding: 20px;
                    /* 增加背景深色透明度，确保白天文字清晰 */
                    background: rgba(0, 0, 0, 0.4); 
                    backdrop-filter: blur(25px) saturate(180%);
                    -webkit-backdrop-filter: blur(25px) saturate(180%);
                    border: 1px solid rgba(255, 255, 255, 0.15);
                    border-radius: 20px;
                    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
                    font-family: -apple-system, "SF Pro Text", sans-serif;
                    animation: glassSlideIn 1.s cubic-bezier(0.23, 1, 0.32, 1) forwards;
                    color: #ffffff;
                    /* 增强文字阴影对比度 */
                    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
                }
                .glass-content-title {
                    font-size: 14px;
                    font-weight: 600;
                    margin-bottom: 6px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    color: #ffffff;
                }
                .glass-content-text {
                    font-size: 13px;
                    line-height: 1.5;
                    color: rgba(255, 255, 255, 0.9);
                    white-space: pre-line;
                }
                .glass-close-btn {
                    position: absolute;
                    top: 12px;
                    right: 12px;
                    width: 20px;
                    height: 20px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.1);
                    transition: background 0.3s;
                }
                .glass-close-btn:hover {
                    background: rgba(255, 255, 255, 0.2);
                }
                .glass-close-btn::before, .glass-close-btn::after {
                    content: "";
                    position: absolute;
                    width: 10px;
                    height: 1px;
                    background: rgba(255, 255, 255, 0.8);
                }
                .glass-close-btn::before { transform: rotate(45deg); }
                .glass-close-btn::after { transform: rotate(-45deg); }
                .ultra-glass-notice::before {
                    content: "";
                    position: absolute;
                    top: 0; left: 0; right: 0; bottom: 0;
                    border-radius: 20px;
                    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
                    pointer-events: none;
                }
            `;
            document.head.appendChild(style);

            const notice = document.createElement("div");
            notice.className = "ultra-glass-notice";
            notice.innerHTML = `
                <div class="glass-close-btn" id="glass-close-btn"></div>
                <div class="glass-content-title">
                    <span style="color: #00ffcc;">●</span> 运行状态
                </div>
                <div class="glass-content-text">${config.content}</div>
            `;
            document.body.appendChild(notice);

            const closeNotice = () => {
                notice.style.transition = "all 1.2s ease";
                notice.style.opacity = "0";
                notice.style.transform = "translateX(100px)";
                setTimeout(() => {
                    if(notice.parentNode) document.body.removeChild(notice);
                }, 1200);
            };

            document.getElementById("glass-close-btn").onclick = closeNotice;
            localStorage.setItem(storageKey, now);
            setTimeout(closeNotice, config.duration);
        })();
        </script>' . "\n";
    }
}

// 注册钩子到 Typecho 头部和底部
\Typecho\Plugin::factory('Widget_Archive')->header = 'theme_header_custom_code';
\Typecho\Plugin::factory('Widget_Archive')->footer = 'theme_footer_custom_code';
