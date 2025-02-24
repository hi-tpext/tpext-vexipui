<?php

namespace tpext\builder\displayer;

class Raw extends Field
{
    protected $view = 'raw';

    protected $input = false;
    
    protected $inline = false;

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function inline($val = true)
    {
        $this->inline = $val;
        return $this;
    }

    public function customVars()
    {
        return [
            'inline' => $this->inline,
        ];
    }
}
