<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasStorageDriver;
use tpext\builder\traits\HasImageDriver;

class MDReader extends Field
{
    use HasStorageDriver;
    use HasImageDriver;

    protected $view = 'mdeditor';

    protected $input = false;

    protected $js = [
        '/assets/buildermdeditor/lib/marked.min.js',
        '/assets/buildermdeditor/lib/prettify.min.js',
        '/assets/buildermdeditor/lib/raphael.min.js',
        '/assets/buildermdeditor/lib/underscore.min.js',
        '/assets/buildermdeditor/lib/sequence-diagram.min.js',
        '/assets/buildermdeditor/lib/flowchart.min.js',
        '/assets/buildermdeditor/lib/jquery.flowchart.min.js',
        '/assets/buildermdeditor/editormd.min.js',
    ];

    protected $css = [
        '/assets/buildermdeditor/css/editormd.min.css',
    ];

    /*模板样式里面有一个css会影响editor.md的图标,这里重设下*/
    protected $stylesheet =
    '.editormd .divider {
            width: auto;
        }
        .editormd .divider:before,
        .editormd .divider:after {
            margin: 0px;

        }
        ';

    protected $jsOptions = [
        'width' => '100%',
        'htmlDecode' => "style,script,iframe", // you can filter tags decode
        'emoji' => true,
        'taskList' => true,
        'tex' => true, // 默认不解析
        'flowChart' => true, // 默认不解析
        'sequenceDiagram' => true, // 默认不解析
    ];

    protected function fieldScript()
    {
        if (!class_exists('\\tpext\\builder\\mdeditor\\common\\Resource')) {
            $this->js = [];
            $this->css = [];
            $this->onMountedScript[] = 'Modal.alert({ message: () => <div style="color: #fa9841">未安装mdeditor资源包！！<pre>composer require ichynul/builder-mdeditor</pre></div> });';
            return;
        }

        /**
         * 上传的后台只需要返回一个 JSON 数据，结构如下：
         * {
         *      success : 0 | 1,           // 0 表示上传失败，1 表示上传成功
         *      message : "提示的信息，上传成功或上传失败及错误信息等。",
         *      url     : "图片地址"        // 上传成功时才返回
         *  }
         */
        if (!isset($this->jsOptions['imageUploadURL']) || empty($this->jsOptions['imageUploadURL'])) {

            $token = $this->getCsrfToken();

            $this->jsOptions['imageUploadURL'] = (string)url($this->getUploadUrl(), [
                'utype' => 'editormd',
                'token' => $token,
                'driver' => $this->getStorageDriver(),
                'is_rand_name' => $this->isRandName(),
                'image_driver' => $this->getImageDriver(),
                'image_commonds' => $this->getImageCommands()
            ]);
        }

        $fieldId = $this->getId();

        $configs = json_encode($this->jsOptions);

        $script = <<<EOT

    var mdreader = editormd.markdownToHTML("{$fieldId}-div", {$configs});

EOT;
        $this->onMountedScript[] = $script;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    protected function vBindOp()
    {
        return $this;
    }
}
