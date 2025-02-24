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

使用`intervention/image 2.x`库，支持`Gd`和`Imagic`(推荐)两种php图片处理库
