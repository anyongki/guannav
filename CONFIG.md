# Typecho 导航主题 - 配置指南

本文档详细说明了主题的所有可配置选项和自定义方法。

## 目录

1. [CSS 变量配置](#css-变量配置)
2. [PHP 配置](#php-配置)
3. [HTML 结构定制](#html-结构定制)
4. [性能优化](#性能优化)
5. [安全配置](#安全配置)

## CSS 变量配置

所有颜色、间距、圆角等样式都通过 CSS 变量定义，可以在 `style.css` 的 `:root` 部分修改。

### 颜色变量

```css
:root {
    /* 背景色 */
    --color-bg: #1a1a1a;              /* 页面背景色 */
    --color-bg-secondary: #2a2a2a;    /* 次级背景色（侧边栏） */
    --color-card: #2d2d2d;            /* 卡片背景色 */
    
    /* 文字色 */
    --color-text: #e8e8e8;            /* 主文字色 */
    --color-text-secondary: #b0b0b0;  /* 次级文字色 */
    
    /* 强调色 */
    --color-accent: #4a9eff;          /* 主强调色 */
    --color-accent-hover: #3a8eef;    /* 强调色悬停状态 */
    
    /* 边框和其他 */
    --color-border: #404040;          /* 边框色 */
    --color-success: #4ade80;         /* 成功色 */
    --color-warning: #facc15;         /* 警告色 */
    --color-error: #ef4444;           /* 错误色 */
}
```

### 修改颜色示例

**改为浅色主题：**

```css
:root {
    --color-bg: #ffffff;
    --color-bg-secondary: #f5f5f5;
    --color-card: #ffffff;
    --color-text: #1a1a1a;
    --color-text-secondary: #666666;
    --color-accent: #0066cc;
    --color-border: #e0e0e0;
}
```

**改为紫色主题：**

```css
:root {
    --color-accent: #a78bfa;
    --color-accent-hover: #9370db;
}
```

### 间距变量

```css
:root {
    --spacing-xs: 0.25rem;    /* 4px */
    --spacing-sm: 0.5rem;     /* 8px */
    --spacing-md: 1rem;       /* 16px */
    --spacing-lg: 1.5rem;     /* 24px */
    --spacing-xl: 2rem;       /* 32px */
    --spacing-2xl: 3rem;      /* 48px */
}
```

### 圆角变量

```css
:root {
    --radius-sm: 0.375rem;    /* 6px */
    --radius-md: 0.5rem;      /* 8px */
    --radius-lg: 0.75rem;     /* 12px */
    --radius-xl: 1rem;        /* 16px */
}
```

**修改所有圆角：**

```css
:root {
    --radius-sm: 0;           /* 方角 */
    --radius-md: 0;
    --radius-lg: 0;
    --radius-xl: 0;
}
```

### 阴影变量

```css
:root {
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15);
}
```

### 过渡变量

```css
:root {
    --transition-fast: 150ms ease-in-out;
    --transition-normal: 300ms ease-in-out;
    --transition-slow: 500ms ease-in-out;
}
```

## PHP 配置

### 主题配置文件（theme.php）

主题配置文件包含主题的基本信息：

```php
<?php
/**
 * 导航主题 - Typecho 卡片式导航展示主题
 * 
 * @package Typecho Nav Theme
 * @author Your Name
 * @version 1.0.0
 * @link https://example.com
 * @description 一个仿硬核指南风格的卡片式导航主题
 */
?>
```

**可修改的信息：**
- `@author` - 作者名称
- `@version` - 版本号
- `@link` - 主题链接
- `@description` - 主题描述

### 函数配置（functions.php）

#### 修改 Favicon API

在 `functions.php` 中的 `getFavicon()` 函数：

```php
function getFavicon($url) {
    // 默认使用 Google Favicon API
    return 'https://www.google.com/s2/favicons?sz=128&domain=' . urlencode($domain);
    
    // 如果要使用其他 API，可以修改为：
    // return 'https://icon.horse/icon/' . urlencode($domain);
}
```

**可用的 Favicon API：**

1. **Google Favicon API**（推荐）
   ```
   https://www.google.com/s2/favicons?sz=128&domain=example.com
   ```

2. **Icon.horse**
   ```
   https://icon.horse/icon/example.com
   ```

3. **Favicone.com**
   ```
   https://favicone.com/example.com
   ```

## HTML 结构定制

### 修改卡片布局

编辑 `index.php` 中的卡片部分：

```php
<article class="card">
    <!-- 卡片头部 -->
    <div class="card-header">
        <!-- 修改图标位置 -->
        <!-- 修改右上角图标 -->
    </div>
    
    <!-- 卡片正文 -->
    <div class="card-body">
        <!-- 修改标题 -->
        <!-- 修改描述 -->
    </div>
    
    <!-- 卡片底部 -->
    <div class="card-footer">
        <!-- 修改平台标识 -->
        <!-- 修改链接按钮 -->
    </div>
</article>
```

### 添加新的自定义字段

1. **在 `index.php` 中添加字段显示：**

```php
<?php 
$customField = $this->getField('custom_field_name');
if ($customField) {
    echo '<div class="custom-field">' . htmlspecialchars($customField) . '</div>';
}
?>
```

2. **在 `admin-fields.php` 中添加字段编辑框：**

```php
<div class="typecho-option">
    <label class="typecho-label" for="custom_field">自定义字段</label>
    <input type="text" id="custom_field" name="custom_field" class="typecho-input" value="<?php echo htmlspecialchars($customField); ?>" />
    <p class="description">字段描述</p>
</div>
```

3. **在 `admin-fields.php` 的 `savePostFields()` 中保存字段：**

```php
self::setPostMeta($postId, 'custom_field', $_POST['custom_field'] ?? '');
```

## 性能优化

### 1. 缓存 Favicon

在 `api.php` 中添加缓存逻辑：

```php
private static function getFaviconWithCache($url) {
    $cacheKey = 'favicon_' . md5($url);
    
    // 检查缓存
    $cached = apcu_fetch($cacheKey);
    if ($cached !== false) {
        return $cached;
    }
    
    // 获取 Favicon
    $favicon = self::getFavicon($url);
    
    // 缓存 1 小时
    apcu_store($cacheKey, $favicon, 3600);
    
    return $favicon;
}
```

### 2. 压缩 CSS

使用在线工具压缩 `style.css`，或使用以下工具：

```bash
# 使用 cssnano
npx cssnano style.css -o style.min.css
```

然后在 `index.php` 中引用压缩版本：

```html
<link rel="stylesheet" href="<?php $this->options->themeUrl('style.min.css'); ?>">
```

### 3. 延迟加载图片

修改 `index.php` 中的图片标签：

```html
<img src="<?php echo $favicon; ?>" 
     alt="icon" 
     class="card-icon"
     loading="lazy">
```

## 安全配置

### 1. 防止 XSS 攻击

始终使用 `htmlspecialchars()` 转义用户输入：

```php
echo '<div>' . htmlspecialchars($userInput) . '</div>';
```

### 2. 验证 URL

在 `api.php` 中已实现 URL 验证：

```php
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    // URL 无效
}
```

### 3. 限制 API 请求

在 `api.php` 中添加速率限制：

```php
// 检查 IP 是否超过请求限制
$ip = $_SERVER['REMOTE_ADDR'];
$key = 'favicon_requests_' . $ip;
$count = apcu_fetch($key);

if ($count > 100) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many requests']);
    exit;
}

apcu_store($key, ($count ?? 0) + 1, 3600);
```

### 4. CORS 安全

在 `api.php` 中配置 CORS：

```php
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN'] ?? '*');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Max-Age: 3600');
```

## 高级定制

### 1. 添加搜索功能

在 `index.php` 中添加搜索表单：

```html
<div class="search-box">
    <form method="get" action="<?php $this->options->siteUrl(); ?>">
        <input type="text" name="s" placeholder="搜索网站...">
        <button type="submit">搜索</button>
    </form>
</div>
```

### 2. 添加排序功能

在 `index.php` 中添加排序选项：

```html
<div class="sort-options">
    <a href="?orderby=date">最新</a>
    <a href="?orderby=title">按名称</a>
</div>
```

### 3. 添加标签云

在 `index.php` 中添加标签显示：

```php
<?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&desc=1&limit=30')->to($tags); ?>
<?php while($tags->next()): ?>
    <a href="<?php $tags->permalink(); ?>" class="tag"><?php $tags->name(); ?></a>
<?php endwhile; ?>
```

## 常见配置问题

### Q: 如何改变卡片网格的列数？

A: 编辑 `style.css` 中的 `.cards-grid`：

```css
/* 3 列 */
.cards-grid {
    grid-template-columns: repeat(3, 1fr);
}

/* 4 列 */
.cards-grid {
    grid-template-columns: repeat(4, 1fr);
}

/* 自适应（推荐） */
.cards-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
}
```

### Q: 如何隐藏某些元素？

A: 使用 CSS `display: none`：

```css
.card-platforms {
    display: none;  /* 隐藏平台标识 */
}

.sidebar {
    display: none;  /* 隐藏左侧导航栏 */
}
```

### Q: 如何修改字体？

A: 在 `style.css` 中修改 `body` 的 `font-family`：

```css
body {
    font-family: 'Segoe UI', 'Microsoft YaHei', sans-serif;
}
```

或在 `index.php` 的 `<head>` 中添加 Google Fonts：

```html
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;700&display=swap" rel="stylesheet">
```

然后在 `style.css` 中使用：

```css
body {
    font-family: 'Noto Sans SC', sans-serif;
}
```

---

**最后更新**：2024-01-05
