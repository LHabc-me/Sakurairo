# GitHub 自动发版

本文档定义 GitHub 管理下的主题自动发版流程。

## 目标

- 为主题生成独立 Release 包
- 在 GitHub Release 保留可下载资产，便于回滚
- 保证主题版本与 Git tag 一致

## 发布分支

- 正式发版分支固定为 `release`
- 只有推送或合并到 `release` 才会自动发正式版
- `main` 不直接自动发正式版

## 新增流水线

### 1. Release Assets

文件：`.github/workflows/release-assets.yml`

触发条件：

- 推送 `release` 分支
- 手动触发

行为：

- 自动将当前版本执行 `patch + 1`
- 执行 `tools/release/build_release_artifacts.py`
- 构建主题 ZIP
- 生成 `manifest.json`
- 上传到 GitHub Release

### 2. 版本一致性校验

文件：`tools/release/check_release_version.py`

校验项：

- `style.css` 中主题版本
- Git tag 去掉前缀 `v` 后的版本号

规则：

- 二者必须一致
- 任一不一致，工作流直接失败

### 3. 自动递增版本

文件：`tools/release/bump_release_version.py`

规则：

- 读取当前主题版本，例如 `1.2.87`
- 自动执行 `patch + 1`
- 新版本变为 `1.2.88`
- 同步更新 `style.css`

## 推荐发版流程

1. 在 `main` 完成日常开发
2. 将准备发布的提交合并或推送到 `release`
3. GitHub Actions 自动：
   - 当前版本执行 `patch + 1`
   - 提交版本变更到 `release`
   - 创建对应 tag，例如 `v1.2.88`
   - 校验 tag / 主题版本一致
   - 构建 Release 资产
   - 上传主题 ZIP / manifest

## 版本策略

- Release tag 采用 `vX.Y.Z`
- `release` 每次发版自动执行 `patch + 1`

## 注意事项

- 主题部署包会排除：
  - `.git`
  - `.github`
  - `docs`
  - `tools`
- 当前自动化只覆盖 GitHub Release 发版，不包含服务器部署
