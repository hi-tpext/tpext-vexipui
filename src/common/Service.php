<?php

namespace tpext\builder\common;

use think\Service as BaseService;

/**
 * for tp6 . webman等命令行cli模式，每次请求结束时清空Builder
 */
class Service extends BaseService
{
    public function boot()
    {
        $this->app->event->listen('HttpEnd', function () {
            Builder::destroyInstance();
        });
    }
}
