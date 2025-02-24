<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasStorageDriver;
use tpext\builder\traits\HasImageDriver;

class WangEditor extends Field
{
    use HasStorageDriver;
    use HasImageDriver;

    protected $view = 'wangeditor';
    protected $js = [
        '/assets/tpextvexipui/js/wangEditor/wangEditor.min.js',
    ];

    protected $jsOptions = [
        'uploadImgMaxSize' => 20 * 1024 * 1024,
        'uploadImgMaxLength' => 10,
        'uploadImgTimeout' => 30000,
        'uploadFileName' => 'file',
        'zIndex' => 99
    ];

    protected function fieldScript()
    {
        if ($this->readonly) {
            return;
        }

        $fieldId = $this->getId();
        $VModel = $this->getVModel();

        if (!isset($this->jsOptions['uploadImgServer']) || empty($this->jsOptions['uploadImgServer'])) {

            $token = $this->getCsrfToken();

            $this->jsOptions['uploadImgServer'] = (string)url($this->getUploadUrl(), [
                'utype' => 'wangeditor',
                'token' => $token,
                'driver' => $this->getStorageDriver(),
                'is_rand_name' => $this->isRandName(),
                'image_driver' => $this->getImageDriver(),
                'image_commonds' => $this->getImageCommands()
            ]);
        }

        $this->jsOptions['uploadImgParams'] = [];

        $configs = json_encode($this->jsOptions);

        $script = <<<EOT

    var E = window.wangEditor;
    var editor = new E('#{$fieldId}-div');
    editor.customConfig = {$configs};
    editor.customConfig.uploadImgHooks = {
        customInsert: function (insertImg, result, editor) {
            // 图片上传并返回结果，自定义插入图片的事件（而不是编辑器自动插入图片！！！）
            // insertImg 是插入图片的函数，editor 是编辑器对象，result 是服务器端返回的结果
            // 举例：假如上传图片成功后，服务器端返回的是 {url:'....'} 这种格式，即可这样插入图片：
            var url = result.url;
            insertImg(url);
            // result 必须是一个 JSON 格式字符串！！！否则报错
        }
    };
    // 设置内容
    editor.customConfig.onchange = function (html) {
        {$VModel} = html;
    }
    editor.create();

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
