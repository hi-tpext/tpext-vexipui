<?php

namespace tpext\builder\displayer;

use tpext\builder\common\Builder;

class Button extends Field
{
    protected $view = 'button';

    protected $input = false;

    /**
     * circle	boolean	false	是否圆形按钮
     * ghost	boolean	false	是否幽灵按钮
     * loading	boolean	false	是否加载中状态
     * button-type	'button' | 'submit' | 'reset'	'button'	对应按钮原生 type 属性
     * simple	boolean	false	开启后，按钮将变为浅色系的简约风格
     * size	'small' | 'default' | 'large'  --	定义按钮尺寸
     * type	primary、info、success、warning 和 error	--	展示按钮不同的状态，不设置时为默认样式
     *
     * @var array
     */
    protected $jsOptions = [
        'size' => 'default',
        'type' => 'default',
        'button-type' => 'button',
        'loading' => false,
        'circle' => false,
        'simple' => true,
        'ghost' => false,
    ];

    protected $onClick = '';

    protected $size = [0, 12];

    protected $showLabel = false;

    public function created($type = '')
    {
        parent::created($type);
        $this->name = '__button' . mt_rand(100, 999);
    }

    /**
     * Undocumented function
     *
     * @param string $val primary | success | warning | error | info | default
     * @return $this
     */
    public function type($val)
    {
        $this->jsOptions['type'] = str_replace('btn-', '', $val);
        if ($this->jsOptions['type'] == 'danger') {
            $this->jsOptions['type']  = 'error';
        }
        if (!in_array($this->jsOptions['type'], ['primary', 'info', 'success', 'warning', 'error'])) {
            $this->jsOptions['type'] = 'default';
            $this->jsOptions['class'] = 'vxp-button--' . $this->jsOptions['type'];
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function nativeType($val)
    {
        $this->jsOptions['native-type'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function buttonSize($val)
    {
        $this->jsOptions['size'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function loading($val = true)
    {
        $this->jsOptions['loading'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function closeLayer()
    {
        $script = <<<EOT
        layerCloseWindow();
EOT;
        $this->onClick = $script;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $script
     * @return $this
     */
    public function onClick($script)
    {
        $this->onClick = $script; //处理按钮事件
        return $this;
    }

    protected function fieldScript()
    {
        $btnId = $this->getId();

        $script = <<<EOT

    const {$btnId}Click = () => {
        {$this->onClick}
    };

EOT;
        $this->setupScript[] = $script;
        Builder::getInstance()->addVueToken(["{$btnId}Click"]);
    }
}
