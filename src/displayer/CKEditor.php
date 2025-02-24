<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasStorageDriver;
use tpext\builder\traits\HasImageDriver;

class CKEditor extends Field
{
    use HasStorageDriver;
    use HasImageDriver;

    protected $view = 'ckeditor';

    protected $js = [
        '/assets/builderckeditor/ckeditor.js',
    ];

    protected $jsOptions = [
        'language' => 'zh-cn',
        'uiColor' => '#eeeeee',
        'height' => 600,
        'image_previewText' => ' ',
    ];

    protected function fieldScript()
    {
        if ($this->readonly) {
            return;
        }
        if (!class_exists('\\tpext\\builder\\ckeditor\\common\\Resource')) {
            $this->js = [];
            $this->onMountedScript[] = 'VxpConfirm.open({ content: "未安装ckeditor资源包！composer require ichynul/builder-ckeditor", cancelable: false });';
            return;
        }
        // 配置可放在config.js中
        // 成功返回格式{"uploaded":1,"fileName":"图片名称","url":"图片访问路径"}
        // 失败返回格式{"uploaded":0,"error":{"message":"失败原因"}}

        if (!isset($this->jsOptions['filebrowserImageUploadUrl']) || empty($this->jsOptions['filebrowserImageUploadUrl'])) {

            $token = $this->getCsrfToken();

            $this->jsOptions['filebrowserImageUploadUrl'] = (string)url($this->getUploadUrl(), [
                'utype' => 'ckeditor',
                'token' => $token,
                'driver' => $this->getStorageDriver(),
                'is_rand_name' => $this->isRandName(),
                'image_driver' => $this->getImageDriver(),
                'image_commonds' => $this->getImageCommands()
            ]);
        }

        $VModel = $this->getVModel();

        $configs = json_encode($this->jsOptions);

        // 配置可放在config.js中

        $script = <<<EOT
        
    var editor = CKEDITOR.replace('{$this->name}', {$configs});
    // 当编辑器内容发生改变时，这个函数会被调用
    editor.on('change', function(event) {
        {$VModel} = editor.getData();
    });

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
