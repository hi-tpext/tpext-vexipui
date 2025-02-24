<?php

namespace tpext\builder\displayer;

class Month extends DateTime
{
    protected $type = 'month';

    protected $size = [2, 2];

    protected $timespan = '';

    public function created($type = '')
    {
        parent::created($type);
        $this->jsOptions['format'] = 'yM';
        $this->jsOptions['value-format'] = 'yyyy-MM';
    }

    /**
     * Undocumented function
     * @param string $val yyyy-MM
     * @return $this
     */
    public function format($val)
    {
        $val = str_replace('Y', 'y', $val);
        $val = str_replace(['D', 'd', 'h', 'H', 'm', 's', ':'], '', $val);
        $this->jsOptions['value-format'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function timespan($val = 'm')
    {
        $this->timespan = $val;
        return $this;
    }
}
