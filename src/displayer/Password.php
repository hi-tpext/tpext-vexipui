<?php

namespace tpext\builder\displayer;

class Password extends Text
{
    /**
     *
     * maxlength 　最大输入长度
     * @var array
     */
    protected $jsOptions = [
        'maxlength' => '',
        'plain-password' => true,//设置是否显示查看明文密码的按钮
        'autocomplete' => 'new-password',
    ];

    protected $view = 'password';
}
