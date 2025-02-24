<?php

namespace tpext\builder\inface;

use tpext\builder\common\model\Attachment;

interface Image
{
    /**
     * Undocumented function
     *
     * @param Attachment $attachment
     * @param array $args
     * @return string url
     */
    public function process($attachment, $args);
}
