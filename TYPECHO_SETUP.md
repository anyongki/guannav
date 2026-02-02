# Typecho 导航主题 - Typecho 特定设置指南

本文档说明如何在 Typecho 中正确安装和配置导航主题。

## 重要提示

**本主题需要配合一个辅助插件使用，才能在后台添加自定义字段。**

## 安装步骤

### 第一步：上传主题文件

1. 将 `typecho-nav-theme-php` 文件夹上传到 Typecho 的 `/usr/themes/` 目录
2. 最终路径应为：`/usr/themes/typecho-nav-theme-php/`

### 第二步：启用主题

1. 登录 Typecho 后台
2. 进入 **设置 → 外观**
3. 找到 **导航主题**
4. 点击 **启用**

### 第三步：安装辅助插件（重要！）

**为什么需要插件？**
- Typecho 的自定义字段需要通过插件来管理
- 插件提供后台编辑界面，用于添加网站信息
- 插件处理 Favicon 自动获取功能

**安装步骤：**

1. 在主题文件夹中找到 `NavThemeHelper.php` 文件
2. 创建插件目录：`/usr/plugins/NavThemeHelper/`
3. 将 `NavThemeHelper.php` 复制到 `/usr/plugins/NavThemeHelper/Plugin.php`
4. 在后台 **管理 → 插件** 中找到 **导航主题辅助**
5. 点击 **启用**

### 第四步：配置 Iconfont（可选）

1. 访问 [Iconfont](https://www.iconfont.cn/)
2. 创建项目并添加图标
3. 获取 CDN 链接
4. 编辑 `/usr/themes/typecho-nav-theme-php/index.php`
5. 找到第 14 行的 Iconfont link 标签：
   ```html
   <link rel="stylesheet" href="https://at.alicdn.com/t/font_3320918_9rvzqvq8rrg.css">
   ```
6. 替换为您的 CDN 链接
7. 保存文件

## 使用流程

### 1. 创建分类

在后台 **管理 → 分类** 中创建分类：
- 动漫
- 漫画
- 下载
- 神器
- 影视
- 音乐
- 阅读
- 游戏
- 娱乐

### 2. 添加网站

1. 进入 **管理 → 写文章**
2. 填写文章标题（网站名称）
3. 选择分类
4. 向下滚动找到 **导航主题字段** 部分
5. 填写以下字段：
   - **网站 URL**：网站的完整 URL（必填）
   - **阿里图标代码**：如 `icon-star`（可选）
   - **平台标识**：如 `iOS, Android, Web`（可选）
   - **网站简介**：网站描述（可选）
6. 点击 **自动获取图标** 按钮获取网站图标
7. 点击 **发布** 保存

### 3. 查看前台效果

访问网站首页，您应该看到卡片式的导航展示。

## Typecho 特定的函数

本主题使用以下 Typecho 特定的函数：

| 函数 | 说明 |
|------|------|
| `$this->options->title()` | 获取网站标题 |
| `$this->options->description()` | 获取网站描述 |
| `$this->options->siteUrl()` | 获取网站 URL |
| `$this->options->themeUrl()` | 获取主题 URL |
| `$this->header()` | 输出 header 钩子 |
| `$this->footer()` | 输出 footer 钩子 |
| `$this->archiveTitle()` | 获取存档标题 |
| `$this->is()` | 判断当前页面类型 |
| `$this->have()` | 判断是否有文章 |
| `$this->next()` | 获取下一篇文章 |
| `$this->title()` | 获取文章标题 |
| `$this->content` | 获取文章内容 |
| `$this->permalink()` | 获取文章链接 |
| `$this->getField()` | 获取自定义字段 |
| `$this->pageNav()` | 输出分页导航 |
| `$this->widget()` | 调用 Widget |

## 自定义字段存储

主题的自定义字段存储在 Typecho 的 `typecho_metas` 表中：

```sql
-- 查询某篇文章的所有自定义字段
SELECT * FROM typecho_metas 
WHERE post = {文章ID} AND type = 'post';
```

自定义字段包括：
- `website_url` - 网站 URL
- `auto_favicon` - 自动获取的 Favicon
- `favicon` - 手动设置的 Favicon
- `iconfont_code` - 阿里图标代码
- `platforms` - 平台标识
- `description` - 网站简介

## 常见问题

### Q: 为什么后台没有显示自定义字段？

A: 确保：
1. ✓ 辅助插件已启用
2. ✓ 您在编辑文章（不是新建）
3. ✓ 浏览器缓存已清除
4. ✓ 刷新页面

### Q: 自动获取 Favicon 不工作？

A: 检查以下几点：
1. ✓ URL 格式是否正确（如 https://example.com）
2. ✓ 目标网站是否可以正常访问
3. ✓ 服务器是否允许外部 HTTP 请求
4. ✓ 检查浏览器控制台是否有错误信息

### Q: 如何修改主题颜色？

A: 编辑 `/usr/themes/typecho-nav-theme-php/style.css`，修改 `:root` 中的 CSS 变量。

### Q: 如何添加更多自定义字段？

A: 
1. 编辑 `NavThemeHelper.php` 中的 `addPostFields()` 方法，添加新的输入框
2. 编辑 `savePostFields()` 方法，添加字段保存逻辑
3. 编辑 `index.php`，在合适的位置显示新字段

### Q: 主题支持哪些 Typecho 版本？

A: 本主题支持 Typecho 1.0 及以上版本。

### Q: 如何卸载主题？

A: 
1. 在后台 **设置 → 外观** 中切换到其他主题
2. 删除 `/usr/themes/typecho-nav-theme-php/` 文件夹
3. 在后台 **管理 → 插件** 中禁用 **导航主题辅助** 插件
4. 删除 `/usr/plugins/NavThemeHelper/` 文件夹

## 文件权限

确保以下文件夹有正确的权限：

```bash
# 主题文件夹权限
chmod -R 755 /usr/themes/typecho-nav-theme-php/

# 插件文件夹权限
chmod -R 755 /usr/plugins/NavThemeHelper/

# 确保 Typecho 可以写入 metas 表
# 这通常由 Typecho 自动处理
```

## 数据库要求

- 需要访问 `typecho_metas` 表
- 需要访问 `typecho_posts` 表
- 需要访问 `typecho_categories` 表

这些表都是 Typecho 的标准表，无需额外配置。

## 性能优化

### 1. 启用缓存

在 Typecho 配置中启用缓存可以提高性能：

```php
// config.inc.php
define('__TYPECHO_CACHE_ADAPTER__', 'File');
```

### 2. 优化数据库查询

主题已经优化了数据库查询，使用了 Typecho 的标准查询方法。

### 3. 图片优化

- 使用 `loading="lazy"` 属性进行图片延迟加载
- Favicon 通过 CDN 提供，自动缓存

## 安全性

### 1. XSS 防护

所有用户输入都使用 `htmlspecialchars()` 进行转义。

### 2. SQL 注入防护

所有数据库查询都使用参数化查询。

### 3. CSRF 防护

Typecho 的内置 CSRF 防护机制已启用。

## 技术支持

如有问题，请：
1. 查看本文档
2. 查看 `README.md` 和其他文档
3. 检查 Typecho 错误日志
4. 提交 Issue 或联系作者

## 更新日志

### v1.0.0 (2024-01-05)
- ✨ 初始版本发布
- ✨ 完全兼容 Typecho 1.0+
- ✨ 包含辅助插件
- ✨ 完整的文档支持

---

**最后更新**：2024-01-05
