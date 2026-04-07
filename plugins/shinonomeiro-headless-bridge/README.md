# Shinonomeiro Headless Bridge

为 `Shinonomeiro` 主题的 Headless 前端提供只读配置接口。

## 接口

- `GET /wp-json/shinonomeiro-headless/v1/site`
- `GET /wp-json/shinonomeiro-headless/v1/homepage`
- `GET /wp-json/shinonomeiro-headless/v1/posts/{id}/extras`

## 依赖

- 推荐同时安装并启用 `WPGraphQL`
- 当前主题需保留 `shinonomeiro_options` 或 `iro_options` 配置项

## 设计约束

- 只读输出，不写入主题配置
- 缺少关键依赖时在后台管理页直接报错提示
- 所有输出默认使用 UTF-8
