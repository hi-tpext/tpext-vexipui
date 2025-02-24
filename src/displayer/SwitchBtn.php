<?php

namespace tpext\builder\displayer;

class SwitchBtn extends Field
{
    protected $view = 'switchbtn';
    protected $pair = ['on' => 1, 'off' => 0];

    protected $default = 0;

    /**
     *
     * @var array
     */
    protected $jsOptions = [
        'open-text' => '',
        'close-text' => '',
        'rectangle' => false, //设置开发是否为矩形
    ];

    /**
     * Undocumented function
     * @example 1 (1, 0) / ('yes', 'no') / ('on', 'off') etc...
     * @param string|mixed $on
     * @param string|mixed $off
     * @return $this
     */
    public function pair($on = 1, $off = 0)
    {
        $this->pair = ['on' => $on, 'off' => $off];

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getPair()
    {
        return $this->pair;
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

        $this->renderValue = $value == $this->pair['on'] ? true : false; //值只支持 boolean 类型，所以需要转换一下

        return $this->renderValue;
    }

    protected function fieldScript()
    {
        $onVal = is_string($this->pair['on']) ? "'" . $this->pair['on'] . "'" : $this->pair['on'];
        $offVal = is_string($this->pair['off']) ? "'" . $this->pair['off'] . "'" : $this->pair['off'];
        $VModel = $this->getVModel();

        $script = <<<EOT
        
        {$VModel} = {$VModel} ? {$onVal} : {$offVal};

EOT;
        $this->convertScript[] = $script;
    }
}
