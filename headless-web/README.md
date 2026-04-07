# Shinonomeiro Headless Web

基于 `Next.js + TypeScript + App Router` 的前台站点。

## 环境变量

复制 `.env.example` 为 `.env.local`：

```bash
WORDPRESS_GRAPHQL_ENDPOINT=https://example.com/graphql
WORDPRESS_REST_BASE_URL=https://example.com/wp-json
NEXT_PUBLIC_SITE_URL=https://example.com
```

## 启动

```bash
npm install
npm run dev
```

## 依赖

- WordPress 已安装并启用 `WPGraphQL`
- WordPress 已安装并启用 `plugins/shinonomeiro-headless-bridge`
