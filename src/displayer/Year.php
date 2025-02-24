<?php

namespace tpext\builder\displayer;

class Year extends DateTime
{
    protected $type = 'year';

    protected $size = [2, 2];
    protected $timespan = '';

    public function created($type = '')
    {
        parent::created($type);
        $this->jsOptions['format'] = 'y';
        $this->jsOptions['value-format'] = 'yyyy';
    }

    /**
     * Undocumented function
     * @param string $val yyyy
     * @return $this
     */
    public function format($val)
    {
        $val = str_replace('Y', 'y', $val);
        $val = str_replace(['M', 'D', 'd', 'h', 'H', 'm', 's', ':', '-'], '', $val);
        $this->jsOptions['value-format'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function timespan($val = 'Y')
    {
        $this->timespan = $val;
        return $this;
    }
}
