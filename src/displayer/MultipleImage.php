<?php

namespace tpext\builder\displayer;

class MultipleImage extends MultipleFile
{
    protected $__default = '/assets/tpextvexipui/images/default.png';

    public function created($type = '')
    {
        parent::created($type);
        $this->jsOptions['fileSingleSizeLimit'] = 2 * 1024 * 1024;
        $this->jsOptions['isImage'] = true;
        $this->image();
    }
}
