# tpext-vexipui

使用 vue3 + vexipui 实现的 tpext-builder 替代

vexipui：<https://www.vexipui.com>

## 文档

<https://gxzrnxb27j.k.topthink.com/@tpext-docs/tpextbuilder-UIshengcheng.html>

### 集成富文本编辑器

* ckeditor
* editor.md (mdeditor && mdreader)
* tinymce
* ueditor
* wangEditor (也是默认编辑器 : 调用`$form->editor()`时默认使用它)

#### 已内置 wangEditor 资源，其余编辑器资源较占空间未内置，需额外安装资源包

其他的按需要安装

comnpser：

`composer require ichynul/builder-ckeditor`

`composer require ichynul/builder-mdeditor`

`composer require ichynul/builder-tinymce`

`composer require ichynul/builder-ueditor`

或到后台-[扩展管理]-页面下载插件安装

#### 图片压缩／水印

使用`intervention/image 2.x / 3.x`库，支持`Gd`和`Imagic`(推荐)两种php图片处理库

#### 2025-09-04 更新

支持`xlswriter`导出

安装php扩展`xlswriter`后不依赖`PhpSpreadsheet`或`PHPExcel`也可以导出`xlsx`格式文件，如果同时存在，也优先使用`xlswriter`

导出菜单默认移除`xls`格式，支持[csv/xlsx]
