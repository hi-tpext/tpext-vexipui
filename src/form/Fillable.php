<?php

namespace tpext\builder\form;

use tpext\builder\inface\Renderable;

interface Fillable extends Renderable
{
    /**
     * Undocumented function
     *
     * @param array|\think\Model $data
     * @return $this
     */
    public function fill($data = []);
}
