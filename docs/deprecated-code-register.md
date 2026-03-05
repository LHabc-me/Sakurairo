# 废弃代码登记表（PR3）

更新日期：2026-03-05  
适用范围：Shinonomeiro 主题主线（当前 `INT_VERSION=20.0.10`）

## 登记表

| ID | 废弃项 | 位置 | 当前状态 | 兼容策略 | 标记废弃版本 | 计划移除版本 |
| --- | --- | --- | --- | --- | --- | --- |
| DEP-001 | 旧配置键 `IRO_LEGACY_OPTIONS_KEY` 单次导入链路（含 `iro_migrate_legacy_options` 管理入口） | `inc/modules/legacy-options-import.php`（由 `functions.php` 加载） | Phase2 已完成子项：复核并移除无引用 `iro_legacy_import_done` 写入（20.0.10-dev / 2026-03-05）；迁移链路继续保留兼容，默认不主动触发 | 保留一次性迁移能力，新增文档提醒用户迁移后清理旧键 | v1.2.78 | v1.4.0 |
| DEP-002 | `iro_act` GET 路由式后台动作（如 `del_exist_theme`） | `inc/modules/legacy-actions.php::iro_action_operator` | Phase2 已完成子项：`bangumi/mal/steam_library/playlist/gallery_init/gallery_webp` 已新增 `admin-post + nonce` 入口，后台入口链接已切换；`del_exist_theme` GET 删除逻辑改为兼容重定向到 `admin-post` 实现 | 保留 `iro_act` 兼容回退入口，待观察后整体下线 | v1.2.78 | v1.3.0 |
| DEP-003 | `GBsubstr`（`mbstring` 缺失时的摘要降级路径） | `functions.php::GBsubstr` | 兼容保留 | 下个小版本起在后台提示 `mbstring` 依赖，先告警再移除降级 | v1.2.78 | v1.5.0 |
| DEP-004 | 友情链接提交中“无法 `wp_insert_link` 时退化为发待审文章”分支 | `functions.php::sakurairo_link_submission_handler` | 兼容保留 | 统计线上命中率；若接近 0 则在大版本移除降级分支 | v1.2.78 | v1.4.0 |

## Phase1 完成记录（2026-03-05）

| 项目 | 完成内容 | 版本 | 日期 |
| --- | --- | --- | --- |
| DEP-001 子项 | 移除 `iro_run_one_time_legacy_import` 中无引用的 `iro_legacy_import_done` 写入 | 20.0.10-dev | 2026-03-05 |
| DEP-002 子项 | `del_exist_theme` 切换为 `admin-post + nonce` 主入口（GET 兼容回退在后续阶段清理） | 20.0.10-dev | 2026-03-05 |

## Phase2 完成记录（2026-03-05）

| 项目 | 完成内容 | 版本 | 日期 |
| --- | --- | --- | --- |
| DEP-001 子项 | 在 `inc/modules/legacy-options-import.php` 复核并移除残留的 `iro_legacy_import_done` 写入 | 20.0.10-dev | 2026-03-05 |
| DEP-002 子项 | 新增 `action=iro_legacy_action`（`admin-post + nonce`）统一入口，`cache_settings` 与 `theme-options` 链接切换到新入口 | 20.0.10-dev | 2026-03-05 |
| DEP-002 子项 | `legacy-actions` 中 `iro_act=del_exist_theme` 删除实现改为兼容重定向到 `admin_post_iro_delete_duplicate_theme` | 20.0.10-dev | 2026-03-05 |

## Phase2 遗留候选

1. DEP-002：保留 `iro_act` 兼容回退分发（`iro_action_operator`），待统计命中后整体下线。
2. DEP-003：`GBsubstr` 降级路径仍保留，需先补告警与环境要求提示。
3. DEP-004：友情链接提交降级分支仍保留，需先采样确认线上命中率。

## 本次拆分加载顺序与回滚

1. 加载顺序
- `functions.php` 在定义 `IRO_*` 常量与 `iro_get_options_store()` 后加载 `inc/modules/legacy-options-import.php`。
- `functions.php` 在原 `iro_action_operator` 所在位置改为加载 `inc/modules/legacy-actions.php`，并保持模块内执行入口。

2. 回滚方式
- 代码回滚：直接回滚对应提交（或 `git revert <commit>`）即可恢复原实现。
- 运行回滚：若需临时停用旧配置导入触发，仅保持默认（不定义 `IRO_ENABLE_LEGACY_IMPORT` 且不触发 `iro_migrate_legacy_options=1`）。

## 版本化移除计划

1. v1.2.78（本次）
- 建立登记表并冻结新增历史兼容入口。
- 新增 CI 基线，保证后续移除动作有最小化回归检查。

2. v1.2.79 - v1.2.80
- 为 DEP-002/DEP-003 增加可观测告警（后台 notice 或 error log），采集命中情况。
- 在 release note 明确“将在后续版本移除”的具体项。

3. v1.3.0
- 移除 DEP-002（GET 路由式后台动作）并提供替代入口。

4. v1.4.0
- 移除 DEP-001/DEP-004（旧配置导入链路与友情链接提交降级分支）。

5. v1.5.0
- 移除 DEP-003（`GBsubstr` 降级路径），主题最低环境要求提升为启用 `mbstring`。
