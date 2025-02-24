<?php

namespace tpext\builder\displayer;

class Time extends Field
{
    protected $view = 'time';

    protected $size = [2, 2];

    protected $placeholder = '';

    /**
     *
     * clearable 是否显示清除按钮
     * @var array
     */
    protected $jsOptions = [
        'clearable' => true,
        'format' => 'HH:mm:ss',
        'separator' => ':',
        'transfer' => true, //渲染至 <body>
        // 'min' => '',//可选范围
        // 'max' => '',
    ];

    /**
     * Undocumented function
     * HH:mm:ss
     * @param string $val
     * @return $this
     */
    public function format($val)
    {
        $this->jsOptions['format'] = $val;
        return $this;
    }

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

    public function customVars()
    {
        return [
            'placeholder' => $this->placeholder ?: __blang('bilder_please_select') . $this->label
        ];
    }
}
