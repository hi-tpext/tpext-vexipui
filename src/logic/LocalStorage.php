<?php

namespace tpext\builder\logic;

use tpext\builder\inface\Storage;
use tpext\builder\common\model\Attachment;

class LocalStorage implements Storage
{
    /**
     * Undocumented function
     *
     * @param Attachment $attachment
     * @return string url
     */
    public function process($attachment)
    {
        return $attachment['url'];
    }
}
