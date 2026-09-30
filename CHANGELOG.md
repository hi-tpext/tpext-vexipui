# CHANGELOG

## 2026-09

- 新增 `inface\ReleaseAble` 接口与 `traits\HasDestroyOnce` 幂等标记，统一组件销毁契约：`destroy()` 入口检查 `__destroyed__` 防止多归属路径重复销毁。
- 销毁流程补漏：Form 补销毁 tab/step；Table 补销毁 toolbar/actionbar；Tab/Step 补销毁 rows['content']；Toolbar 补销毁 elms；Fields/Items/table FieldsContent 新增 `destroy()`；Tree 同步契约。
- 数组属性销毁后复位为空数组，对象引用置 null。

## 2026-06

- 支持国际化
