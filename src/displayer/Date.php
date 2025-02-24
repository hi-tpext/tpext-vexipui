<?php

namespace tpext\builder\displayer;

class Date extends DateTime
{
    protected $timespan = 'Y-m-d';

    protected $type = 'date';

    public function created($type = '')
    {
        parent::created($type);
        $this->jsOptions['format'] = 'yMd';
        $this->jsOptions['value-format'] = 'yyyy-MM-dd';
    }

    /**
     * Undocumented function
     * @param string $val yyyy-MM-dd
     * @return $this
     */
    public function format($val)
    {
        $val = str_replace(['Y', 'D'], ['y', 'd'], $val);
        $val = str_replace(['h', 'H', 'm', 's', ':'], '', $val);
        $this->jsOptions['value-format'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function timespan($val = 'Y-m-d')
    {
        $this->timespan = $val;
        return $this;
    }
}
