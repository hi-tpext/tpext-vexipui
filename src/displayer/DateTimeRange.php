<?php

namespace tpext\builder\displayer;

class DateTimeRange extends DateTime
{
    protected $size = [2, 5];

    protected $separator = ',';

    public function created($type = '')
    {
        parent::created($type);
        $this->jsOptions['range'] = true;
        $this->jsOptions['start-placeholder'] = __blang('builder_date_range_from');
        $this->jsOptions['end-placeholder'] = __blang('builder_date_range_to');
    }

    /**
     * Undocumented function
     * ','
     * @param string $val
     * @return $this
     */
    public function separator($val = ',')
    {
        $this->separator = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function renderValue()
    {
        if (!is_null($this->renderValue)) {
            return $this->renderValue;
        }
        $value = parent::renderValue();

        $arr = explode($this->separator, $value);

        /**
         * 数字格式时间戳自动转为日期格式
         * 但要避免没有`-/`分割的时间格式被转换，如：20200630 => 1970-08-23 03:17:10
         * 解决办法，截取前字符串4位，如果大于2099或小于1900则认为是时间戳，否则认为是`-/`分割的时间
         * 如果值是数字但可以确定值不是时间戳，可主动使用->timespan('')清空格式避免自动转换。
         */
        if ($this->timespan && isset($arr[0]) && is_numeric($arr[0]) && $arr[0] > 0) {
            $char4 = substr((string) $arr[0], 0, 4);

            if ($char4 < 1900 || $char4 > 2099) //1900~2099区间不会误判
            {
                $arr[0] = date($this->timespan, $arr[0]);
            }
        }

        if ($this->timespan && isset($arr[1]) && is_numeric($arr[1]) && $arr[1] > 0) {
            $char4 = substr((string) $arr[1], 0, 4);

            if ($char4 < 1900 || $char4 > 2099) //1900~2099区间不会误判
            {
                $arr[1] = date($this->timespan, $arr[1]);
            }
        }

        $this->renderValue = $arr;

        return $this->renderValue;
    }

    protected function fieldScript()
    {
        parent::fieldScript();
        
        $VModel = $this->getVModel();

        $script = <<<EOT

        if (Array.isArray({$VModel})) {
            {$VModel} = {$VModel}.join('{$this->separator}');
        }

EOT;
        $this->convertScript[] = $script;
    }
}
