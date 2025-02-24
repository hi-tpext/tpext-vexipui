<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasStorageDriver;
use tpext\builder\traits\HasImageDriver;


class Tinymce extends Field
{
    use HasStorageDriver;
    use HasImageDriver;

    protected $view = 'tinymce';
    protected $js = [
        '/assets/buildertinymce/tinymce.min.js',
    ];

    protected $jsOptions = [
        'language' => 'zh_CN',
        'directionality' => 'ltl',
        'browser_spellcheck' => true,
        'contextmenu' => false,
        'height' => 600,
        'plugins' => [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste imagetools wordcount",
            "code",
        ],
        'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code",
    ];

    protected function fieldScript()
    {
        if ($this->readonly) {
            return;
        }

        if (!class_exists('\\tpext\\builder\\tinymce\\common\\Resource')) {
            $this->js = [];
            $this->setupScript[] = 'layer.alert("未安装tinymce资源包！<pre>composer require ichynul/builder-tinymce</pre>");';
            return;
        }

        if (!isset($this->jsOptions['images_upload_url']) || empty($this->jsOptions['images_upload_url'])) {

            $token = $this->getCsrfToken();

            $this->jsOptions['images_upload_url'] = (string)url($this->getUploadUrl(), [
                'utype' => 'tinymce',
                'token' => $token,
                'driver' => $this->getStorageDriver(),
                'is_rand_name' => $this->isRandName(),
                'image_driver' => $this->getImageDriver(),
                'image_commonds' => $this->getImageCommands()
            ]);
        }

        $fieldId = $this->getId();
        $VModel = $this->getVModel();

        $this->jsOptions['selector'] = "#{$fieldId}";
        $configs = json_encode($this->jsOptions);

        $script = <<<EOT

    tinymce.init({$configs});
    tinymce.activeEditor.on('change', function () {
        {$VModel} = tinymce.get('{$fieldId}').getContent();
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
