<?php

namespace tpext\builder\displayer;

class Loads extends Load
{
    /**
     * Undocumented function
     * '、'
     * @param string $val
     * @return $this
     */
    public function separator($val = '、')
    {
        $this->separator = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function default($val = [])
    {
        $this->default = $val;
        return $this;
    }
}
