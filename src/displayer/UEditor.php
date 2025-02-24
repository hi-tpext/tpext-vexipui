<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasStorageDriver;
use tpext\builder\traits\HasImageDriver;

class UEditor extends Field
{
    use HasStorageDriver;
    use HasImageDriver;

    protected $view = 'ueditor';

    protected $js = [
        '/assets/builderueditor/ueditor.all.min.js',
    ];

    protected $configJsPath = '/assets/builderueditor/ueditor.config.js';

    protected $uploadUrl = '';

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function configJsPath($val)
    {
        $this->configJsPath = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function uploadUrl($val)
    {
        $this->uploadUrl = $val;
        return $this;
    }

    protected function fieldScript()
    {
        if ($this->readonly) {
            return;
        }

        if (!class_exists('\\tpext\\builder\\ueditor\\common\\Resource')) {
            $this->js = [];
            $this->onMountedScript[] = 'VxpConfirm.open({ content: "未安装ueditor资源包！composer require ichynul/builder-ueditor", cancelable: false });';
            return;
        }

        if (empty($this->uploadUrl)) {
            $token = $this->getCsrfToken();
            $this->uploadUrl = (string)url($this->getUploadUrl(), [
                'utype' => 'ueditor',
                'token' => $token,
                'driver' => $this->getStorageDriver(),
                'is_rand_name' => $this->isRandName(),
                'image_driver' => $this->getImageDriver(),
                'image_commonds' => $this->getImageCommands()
            ]);
        }

        $fieldId = $this->getId();
        $VModel = $this->getVModel();

        $this->js = array_merge([$this->configJsPath], $this->js);

        $script = <<<EOT

    window.UEDITOR_CONFIG.serverUrl = '{$this->uploadUrl}';
    var ue = UE.getEditor('{$fieldId}');
    // 当内容发生变化时，这个函数会被调用
    ue.addListener('contentChange', () => {
        {$VModel} = ue.getContent(); // 获取编辑器的内容
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
