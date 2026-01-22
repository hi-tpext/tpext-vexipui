<?php

namespace tpext\builder\admin\controller;

use think\Controller;
use think\facade\Session;
use tpext\builder\common\Module;
use tpext\builder\common\Builder;

/**
 * Undocumented class
 * @title 导入
 */
class Import extends Controller
{
    /**
     * Undocumented function
     *
     * @title 上传文件弹窗
     * @return mixed
     */
    public function page()
    {
        $acceptedExts = input('acceptedExts', '');
        $fileSize = input('fileSize', '');
        $pageToken = input('pageToken', '');
        $successUrl = input('successUrl', '');
        $driver = input('driver', '');

        if (request()->isPost()) {
            $successUrl = urldecode($successUrl);
            $file = input('file');
            return redirect($successUrl . '?fileurl=' . urlencode($file));
        }

        if ($fileSize == '' || empty($pageToken) || empty($successUrl)) {
            $this->error(__blang('builder_parameter_error'));
        }

        $importpagetoken = Session::get('importpagetoken');

        $_pageToken = md5($importpagetoken . $acceptedExts . $fileSize);

        if ($_pageToken != $pageToken) {
            $this->error(__blang('builder_validate_failed'));
        }

        $config = Module::getInstance()->getConfig();

        if ($fileSize == 0 || $fileSize == '' || $fileSize > $config['max_size']) {
            $fileSize = $config['max_size'];
        }

        if ($acceptedExts == '*' || $acceptedExts == '*/*' || empty($acceptedExts)) {

            $acceptedExts = $config['allow_suffix'];
        }

        $builder = Builder::getInstance();

        $form = $builder->form();
        $form->file('file', __blang('builder_action_upload_file'))->required()
            ->storageDriver($driver)
            ->extTypes($acceptedExts)->jsOptions(['fileSingleSizeLimit' => (int)$fileSize * 1024 * 1024]);

        $form->ajax(false);
        $form->btnSubmit('开始导入');

        return $builder;
    }

    /**
     * Undocumented function
     *
     * @title 上传成功
     * @return mixed
     */
    public function afterSuccess()
    {
        $builder = Builder::getInstance(__blang('builder_operation_tips'));

        $fileurl = input('fileurl');

        $script = <<<EOT
        <p>文件上传成功，但未做后续处理：{$fileurl}</p>
        <pre>
        //指定你的处理action，如 url('afterSuccess')
        \$table->getToolbar()->btnImport(url('afterSuccess'));

        //请在你的控制器实现导入逻辑
        public function afterSuccess()
        {
            \$fileurl = input('fileurl');
            if (is_file('.' . \$fileurl)) {
                // 导入逻辑...
                //....
                
                //返回成功提示并刷新列表页面
                \$builder = Builder::getInstance();
                return \$builder->layer()->closeRefresh(1, '导入成功：' . \$fileurl);
            }

            \$builder = Builder::getInstance('出错了');
            \$builder->content()->display('&lt;p&gt;' . '未能读取文件:' . \$fileurl . '&lt;/p&gt;');
            return \$builder->render();
        }
        </pre>

EOT;
        $builder->content()->display($script);
        return $builder->render();
    }
}
