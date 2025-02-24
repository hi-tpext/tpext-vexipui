<?php

namespace tpext\builder\traits;

use tpext\builder\inface\Storage;
use tpext\builder\common\Module;

trait HasStorageDriver
{
    protected $storageDriver;

    protected $isRandName = '';

    /**
     * 设置文件上传驱动
     *
     * @param string $driverClass 驱动的类名，如：\tpext\builder\logic\LocalStorage::class
     * @return $this
     */
    public function storageDriver($driverClass = '')
    {
        if (!is_string($driverClass) && ($driverClass instanceof Storage)) {
            $driverClass = get_class($driverClass);
        }

        $this->storageDriver = $driverClass;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function randName($val = true)
    {
        $this->isRandName = $val ? 'y' : 'n';

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string 'y' or 'n'
     */
    public function isRandName()
    {
        return $this->isRandName;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getStorageDriver()
    {
        if (empty($this->storageDriver)) {
            return '';
        }

        return str_replace('\\', '-', $this->storageDriver);
    }

    /**
     * 获取上传url路径
     * 默认：'/admin/upload/upfiles'
     * @return string
     */
    public function getUploadUrl()
    {
        return Module::getInstance()->getUploadUrl();
    }
}
