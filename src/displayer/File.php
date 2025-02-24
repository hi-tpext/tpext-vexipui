<?php

namespace tpext\builder\displayer;

class File extends MultipleFile
{
    public function created($type = '')
    {
        parent::created($type);
        $this->jsOptions['fileNumLimit'] = 1;
        $this->jsOptions['multiple'] = false;
    }
}
