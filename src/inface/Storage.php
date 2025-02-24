<?php

namespace tpext\builder\inface;

use tpext\builder\common\model\Attachment;

interface Storage
{
    /**
     * Undocumented function
     *
     * @param Attachment $attachment
     * @return string url
     */
    public function process($attachment);
}
