<?php

namespace tpext\builder\displayer;

class Color extends Text
{
    protected $view = 'color';

    protected $size = [2, 3];

    /**
     *
     * alpha 	  是否启用alpha选择
     * visible    设置颜色控制面板的显示状态
     * @var array
     */
    protected $jsOptions = [
        'alpha' => false,
        'visible' => false,
        'format' => 'hex',
        'show-label' => true,
    ];

    /**
     * Undocumented function
     * rgb|hsl|hsv|hsla|hex
     * 
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
     * @return $this
     */
    public function rgb()
    {
        $this->jsOptions['format'] = 'rgb';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function hsl()
    {
        $this->jsOptions['format'] = 'hsl';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function hsv()
    {
        $this->jsOptions['format'] = 'hsv';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function hex()
    {
        $this->jsOptions['format'] = 'hex';
        return $this;
    }

    public function alpha($val = true)
    {
        $this->jsOptions['alpha'] = $val;

        return $this;
    }
}
