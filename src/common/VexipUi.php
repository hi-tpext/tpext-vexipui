<?php

namespace tpext\builder\common;

use tpext\think\App;
use think\facade\Lang;
use tpext\common\ExtLoader;
use tpext\common\Module as baseModule;

class VexipUi extends baseModule
{
    protected $version = '1.1.2';

    protected $name = 'tpext.vexipui';

    protected $title = 'vexipui ui生成';

    protected $description = 'vexipui后台ui生成';

    protected $root = __DIR__ . '/../../';

    protected $assets = 'assets';

    protected $modules = [
        'admin' => ['upload', 'import', 'attachment'],
        'index' => ['file'],
    ];

    /**
     * 版本列表，列出所有存在过的版本，即使没有升级脚本也要列出
     * 版本号 => 升级脚本
     *
     * @var array
     */
    protected $versions = [
        '1.0.1' => '',
        '1.0.2' => '',
        '1.0.3' => '1.0.3.sql',
        '1.0.4' => '',
        '1.0.5' => '',
    ];

    /**扩展基本信息完**/

    //所有视图的基础路径
    protected $viewsPath = '';

    /**
     * 通用文件上传url，不需要经过url()的
     * 默认 '/admin/upload/upfiles'
     * @var string
     */
    protected $uploadUrl = '';

    /**
     * 导入页面url，不需要经过url()的
     * 默认 '/admin/import/page'
     * @var string
     */
    protected $importUrl = '';

    /**
     * 文件上传：选择文件列表页url
     *默认 '/admin/attachment/index'
     * @var string
     */
    protected $chooseUrl = '';

    /**
     * 存储驱动类列表
     *
     * @var array
     */
    protected $storageDrivers = [\tpext\builder\logic\LocalStorage::class => '本地'];

    /**
     * Undocumented function
     *
     * @param string $newPath
     * @return $this
     */
    public function setViewsPath($newPath)
    {
        $this->viewsPath = $newPath;

        return $this;
    }

    /**
     * 设置所有上传的目标url
     *
     * @param string $newUrl 不需要经过url()的
     * @return $this
     */
    public function setUploadUrl($newUrl)
    {
        $this->uploadUrl = (string)$newUrl;

        return $this;
    }

    /**
     * 设置所有上传的目标url
     *
     * @param string $newUrl 不需要经过url()的
     * @return $this
     */
    public function setImportUrl($newUrl)
    {
        $this->importUrl = (string)$newUrl;

        return $this;
    }

    /**
     * 设置选择文件列表页url
     *
     * @param string $newUrl 不需要经过url()的
     * @return $this
     */
    public function setChooseUrl($newUrl)
    {
        $this->chooseUrl = (string)$newUrl;

        return $this;
    }

    /**
     * 添加存储驱动类
     *
     * @param string $class 驱动类名
     * @param string $title 驱动名称
     * @return $this
     */
    public function addStorageDriver($class, $title)
    {
        $this->storageDrivers[$class] = $title;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getViewsPath()
    {
        ExtLoader::trigger('tpext_builder_get_views_path');

        //如果未设置新的视图路径,则设置为默认路径 root/src/view/
        if (empty($this->viewsPath)) {
            $this->viewsPath = $this->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', '']);
        }

        return $this->viewsPath;
    }

    /**
     * 获取上传url路径
     * 默认：'/admin/upload/upfiles'
     * @return string
     */
    public function getUploadUrl()
    {
        ExtLoader::trigger('tpext_builder_get_upload_url');
        return $this->uploadUrl ?: '/admin/upload/upfiles';
    }

    /**
     * 获取导入页面url路径
     * 默认：'/admin/import/page'
     * @return string
     */
    public function getImportUrl()
    {
        ExtLoader::trigger('tpext_builder_get_import_url');
        return $this->importUrl ?: '/admin/import/page';
    }

    /**
     * 获取上传url路径
     * 默认：'/admin/attachment/index'
     * @return string
     */
    public function getChooseUrl()
    {
        ExtLoader::trigger('tpext_builder_get_choose_url');
        return $this->chooseUrl ?: '/admin/attachment/index';
    }

    /**
     * 获取存储驱动类列表
     *
     * @return array
     */
    public function getStorageDrivers()
    {
        //可以监听此事件，调用addStorageDriver($class, $title)添加驱动
        ExtLoader::trigger('tpext_builder_find_storage_drivers');
        return $this->storageDrivers;
    }

    public function loaded()
    {
        Lang::load(Module::getInstance()->getRoot() . 'src' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . App::getDefaultLang() . '.php');
    }
}
