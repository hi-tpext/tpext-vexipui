<?php

use tpext\common\ExtLoader;
use think\facade\Request;
use think\facade\Lang;

$classMap = [
    'tpext\\builder\\common\\Module'
];

ExtLoader::addClassMap($classMap);

if (!function_exists('csrf_token')) {
    /**
     * 生成表单令牌
     * @param string $name 令牌名称
     * @param mixed  $type 令牌生成方法
     * @return string
     */
    function csrf_token($name = '__token__', $type = 'md5')
    {
        $token = Request::token($name, $type);

        return $token;
    }
}

if (!function_exists('__blang')) {
    function __blang(string $name = null, array $vars = [], string $range = '')
    {
        return Lang::get($name, $vars, $range);
    }
}

if (!function_exists('class_basename')) {
    /**
     * 获取类名(不包含命名空间)
     *
     * @param mixed $class 类名
     * @return string
     */
    function class_basename($class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        
        return basename(str_replace('\\', '/', $class));
    }
}
