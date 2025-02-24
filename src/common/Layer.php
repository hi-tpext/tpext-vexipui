<?php

namespace tpext\builder\common;

use tpext\builder\common\Module;
use tpext\think\View;

class Layer extends Widget
{
    /**
     * Undocumented variable
     *
     * @var View
     */
    protected $viewShow;

    public function getViewShow()
    {
        return $this->viewShow;
    }

    public function close($success = true, $msg = '操作成功')
    {
        if (request()->isAjax()) {
            return json([
                'code' => $success ? 1 : 0,
                'msg' => $msg,
                'layer_close' => 1,
            ]);
        }

        $view = Module::getInstance()->getViewsPath() . 'layer' . DIRECTORY_SEPARATOR . 'close.html';

        $vars = [
            'success' => $success ? 1 : 0,
            'msg' => $msg,
        ];

        $this->viewShow = new View($view);

        $this->viewShow->assign($vars);

        return $this->viewShow;
    }

    public function closeGo($success = true, $msg = '操作成功', $url = '')
    {
        if (request()->isAjax()) {
            return json([
                'code' => $success ? 1 : 0,
                'msg' => $msg,
                'layer_close_go' => (string) $url,
            ]);
        }

        $view = Module::getInstance()->getViewsPath() . 'layer' . DIRECTORY_SEPARATOR . 'closego.html';

        $vars = [
            'success' => $success ? 1 : 0,
            'msg' => $msg,
            'url' => (string) $url,
        ];

        $this->viewShow = new View($view);

        $this->viewShow->assign($vars);

        return $this->viewShow;
    }

    public function closeRefresh($success = true, $msg = '操作成功')
    {
        if (request()->isAjax()) {
            return json([
                'code' => $success ? 1 : 0,
                'msg' => $msg,
                'layer_close_refresh' => 1,
            ]);
        }

        $view = Module::getInstance()->getViewsPath() . 'layer' . DIRECTORY_SEPARATOR . 'closerefresh.html';

        $vars = [
            'success' => $success ? 1 : 0,
            'msg' => $msg,
        ];

        $this->viewShow = new View($view);

        $this->viewShow->assign($vars);

        return $this->viewShow;
    }
}
