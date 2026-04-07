# Headless Next.js 接入说明

## 本次新增内容

- `plugins/shinonomeiro-headless-bridge`
  - 提供站点配置、首页配置、文章扩展只读 REST 接口
- `headless-web`
  - 基于 `Next.js + TypeScript + App Router` 的前台站点

## WordPress 后台需要完成的工作

1. 安装并启用 `WPGraphQL`
2. 将 `plugins/shinonomeiro-headless-bridge` 目录部署到 `wp-content/plugins`
3. 在后台启用 `Shinonomeiro Headless Bridge`
4. 检查并整理：
   - 主导航菜单
   - 站点图标
   - 首页模块顺序
   - 静态首页内容页
   - 文章特色图
5. 如果需要在新前台继续使用 Yoast SEO 或 ACF，再按需安装：
   - `Yoast SEO`
   - `Advanced Custom Fields`
   - `WPGraphQL for ACF`

## 环境变量

`headless-web/.env.local`

```bash
WORDPRESS_GRAPHQL_ENDPOINT=https://your-site.com/graphql
WORDPRESS_REST_BASE_URL=https://your-site.com/wp-json
NEXT_PUBLIC_SITE_URL=https://your-headless-site.com
```

## 本地启动

```bash
cd headless-web
npm install
npm run dev
```

## 核心验证清单

- `GET /wp-json/shinonomeiro-headless/v1/site` 返回 200
- `GET /wp-json/shinonomeiro-headless/v1/homepage` 返回 200
- `GET /wp-json/shinonomeiro-headless/v1/posts/{id}/extras` 返回 200
- `WORDPRESS_GRAPHQL_ENDPOINT` 可访问
- 首页可正常展示静态页模块和最新文章
- 内容页可正常展示正文、目录、AI 摘要、上一篇/下一篇
- 分类、标签、作者、搜索、归档路由可访问
- `sitemap.xml` 与 `robots.txt` 可访问

## 已知边界

- 旧主题的 PJAX、全量动画系统、本地主题小挂件未迁移
- 特殊短代码首版未全部组件化
- 评论系统仍保留在 WordPress 侧，首版前端未接评论提交
