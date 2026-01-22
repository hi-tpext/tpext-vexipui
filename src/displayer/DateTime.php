<?php

namespace tpext\builder\displayer;

class DateTime extends Field
{
    protected $view = 'datetime';
    protected $size = [2, 3];

    protected $timespan = 'Y-m-d H:i:s';

    protected $type = 'datetime';

    protected $placeholder = '';

    /**
     *
     * clearable 是否显示清除按钮
     * @var array
     */
    protected $jsOptions = [
        'clearable' => true,
        'format' => 'yMd Hms',
        'value-format' => 'yyyy-MM-dd HH:mm:ss',
        'week-start' => 1,
        'date-separator' => '-',
        'time-separator' => ':',
        'transfer' => true, //渲染至 <body>
        // 'min' => '',//可选范围
        // 'max' => '',
    ];

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function placeholder($val)
    {
        $this->placeholder = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function timespan($val = 'Y-m-d H:i:s')
    {
        $this->timespan = $val;
        return $this;
    }

    /**
     * Undocumented function
     * @param string $val yyyy-MM-dd HH:mm:ss
     * @return $this
     */
    public function format($val)
    {
        $val = str_replace(['Y', 'D'], ['y', 'd'], $val);
        $this->jsOptions['value-format'] = $val;
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

        /**
         * 数字格式时间戳自动转为日期格式
         * 但要避免没有`-/`分割的时间格式被转换，如：20200630 => 1970-08-23 03:17:10
         * 解决办法，截取前字符串4位，如果大于2099或小于1900则认为是时间戳，否则认为是`-/`分割的时间
         * 如果值是数字但可以确定值不是时间戳，可主动使用->timespan('')清空格式避免自动转换。
         */
        if ($this->timespan && is_numeric($value) && $value > 0) {

            $char4 = substr((string) $value, 0, 4);

            if ($char4 < 1900 || $char4 > 2099) //1900~2099区间不会误判
            {
                $value = date($this->timespan, $value);
            }
        }

        $this->renderValue = $value;

        return $this->renderValue;
    }

    public function customVars()
    {
        return [
            'type' => $this->type,
            'name' => $this->getName() . '__tmp',
            'placeholder' => $this->placeholder ?: __blang('builder_please_select') . $this->label
        ];
    }

    protected function fieldScript()
    {
        $VModel = $this->getVModel();

        $script = <<<EOT

        watch(
            () => {$VModel}__tmp,
            (newValue, oldValue) => {
                if(!newValue) {
                    {$VModel} = null;
                    console.log({$VModel});
                }
            }
        );

EOT;
        $this->onMountedScript[] = $script;
    }
}
