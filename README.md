# Typecho 导航主题

一个仿硬核指南风格的卡片式导航展示主题，为 Typecho 博客系统设计。支持自定义图标、平台标识、网站简介和自动获取网站图标等功能。

## 功能特性

### 前台展示
- **卡片式布局**：现代化的卡片网格展示，支持响应式设计
- **深色调设计**：专业的深色主题，减少眼睛疲劳
- **自动获取 Favicon**：通过 API 自动获取目标网站的图标
- **阿里图标支持**：卡片右上角可自定义阿里 Iconfont 图标
- **平台标识**：显示网站支持的平台（iOS、Android、Web 等）
- **网站简介**：在卡片中展示网站的简短描述
- **左侧导航栏**：分类导航，支持图标显示

### 后台功能
- **自定义字段**：支持为每篇文章添加自定义字段
- **网站 URL 输入**：输入网站地址用于自动获取图标
- **一键获取图标**：后台按钮自动获取并预览网站 Favicon
- **图标代码输入**：支持手动输入阿里 Iconfont 代码
- **平台标识设置**：灵活设置网站支持的平台
- **简介编辑**：编辑网站简介文本

## 安装步骤

### 1. 下载主题

将主题文件夹放到 Typecho 的 `usr/themes/` 目录下：

```bash
cd /path/to/typecho/usr/themes/
git clone https://github.com/yourusername/typecho-nav-theme.git
# 或手动上传文件夹
```

### 2. 激活主题

在 Typecho 后台：
1. 进入 **设置 → 外观**
2. 找到 **导航主题**
3. 点击 **启用**

### 3. 配置主题

#### 创建分类

为了更好地组织网站，建议创建以下分类：

- **动漫** - 动漫相关网站
- **漫画** - 漫画相关网站
- **下载** - 下载资源网站
- **神器** - 实用工具网站
- **影视** - 影视相关网站
- **音乐** - 音乐相关网站
- **阅读** - 阅读相关网站
- **游戏** - 游戏相关网站
- **娱乐** - 娱乐相关网站

#### 添加网站

在后台新建文章时：

1. **输入网站名称**：作为文章标题
2. **输入网站 URL**：在自定义字段中输入完整的网站地址
3. **自动获取图标**：点击"自动获取图标"按钮，系统会自动获取网站的 Favicon
4. **设置阿里图标**：（可选）输入阿里 Iconfont 的 class 名称
5. **设置平台标识**：输入网站支持的平台，用逗号分隔（如：iOS, Android, Web）
6. **编写简介**：在简介字段中描述网站的用途和特点
7. **选择分类**：选择合适的分类
8. **发布文章**

### 4. 配置阿里 Iconfont

如果要使用阿里 Iconfont 图标，需要在主题中配置字体文件：

1. 在 [Iconfont](https://www.iconfont.cn/) 上创建或选择图标库
2. 获取字体文件的 CDN 链接
3. 编辑 `index.php` 中的 link 标签，替换为您的 Iconfont CDN 链接：

```html
<link rel="stylesheet" href="https://at.alicdn.com/t/your_font_id.css">
```

## 文件结构

```
typecho-nav-theme/
├── theme.php           # 主题配置文件
├── index.php           # 主模板文件
├── functions.php       # 主题功能函数
├── admin-fields.php    # 后台自定义字段管理
├── style.css           # 样式表
└── README.md           # 本文件
```

## 自定义字段说明

主题使用以下自定义字段来存储网站信息：

| 字段名 | 说明 | 示例 |
|--------|------|------|
| `website_url` | 网站完整 URL | https://example.com |
| `auto_favicon` | 自动获取的 Favicon URL | https://www.google.com/s2/favicons?... |
| `favicon` | 手动设置的 Favicon URL | /usr/uploads/favicon.png |
| `iconfont_code` | 阿里 Iconfont 图标代码 | icon-star |
| `platforms` | 平台标识（逗号分隔） | iOS, Android, Web |
| `description` | 网站简介 | 这是一个很棒的网站 |

## API 接口

### 获取 Favicon API

**端点**：`/?action=get_favicon`

**参数**：
- `url` (string) - 目标网站的 URL

**请求示例**：
```
GET /?action=get_favicon&url=https://example.com
```

**响应示例**：
```json
{
  "success": true,
  "favicon": "https://www.google.com/s2/favicons?sz=128&domain=example.com",
  "url": "https://example.com"
}
```

## 样式自定义

主题使用 CSS 变量来管理颜色和间距，您可以在 `style.css` 中修改以下变量来自定义主题外观：

```css
:root {
    /* 颜色变量 */
    --color-bg: #1a1a1a;              /* 背景色 */
    --color-card: #2d2d2d;            /* 卡片背景色 */
    --color-text: #e8e8e8;            /* 文字色 */
    --color-accent: #4a9eff;          /* 强调色 */
    
    /* 其他变量... */
}
```

## 响应式设计

主题在以下断点处进行了优化：

- **桌面**（> 768px）：3 列卡片网格
- **平板**（768px - 480px）：2 列卡片网格
- **手机**（< 480px）：1 列卡片网格

## 浏览器兼容性

- Chrome/Edge：完全支持
- Firefox：完全支持
- Safari：完全支持
- IE 11：不支持（使用现代 CSS 特性）

## 常见问题

### Q: 如何修改卡片的颜色？
A: 编辑 `style.css` 中的 CSS 变量，例如修改 `--color-accent` 来改变强调色。

### Q: 自动获取 Favicon 不工作？
A: 确保：
1. 网站 URL 格式正确（包含 http:// 或 https://）
2. 目标网站可以正常访问
3. 服务器允许外部 HTTP 请求

### Q: 如何添加更多分类？
A: 在 Typecho 后台 **管理 → 分类** 中添加新分类，然后在发布文章时选择对应分类即可。

### Q: 可以修改左侧导航栏的宽度吗？
A: 可以，编辑 `style.css` 中的 `.sidebar { width: 200px; }` 来修改宽度。

## 许可证

MIT License

## 更新日志

### v1.0.0 (2024-01-05)
- 初始版本发布
- 支持卡片式展示
- 集成自动获取 Favicon 功能
- 支持阿里图标和平台标识

## 支持

如有问题或建议，请提交 Issue 或 Pull Request。

## 作者

Your Name

---

**最后更新**：2024-01-05
