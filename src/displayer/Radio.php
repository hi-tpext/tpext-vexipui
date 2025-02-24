<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasWhen;
use tpext\builder\traits\HasOptions;

class Radio extends Field
{
    use HasOptions;
    use HasWhen;

    protected $view = 'radio';

    protected $checked = '';

    protected $disabledOptions = [];

    protected $blockStyle = false;

    /**
     *
     * text-color 按钮形式的 checkbox 激活时的文本颜色
     * fill       复选框组子项组件类型，需配合 options 属性使用
     * @var array
     */
    protected $jsOptions = [
        'vertical' => false,
        'shape' => 'default',
    ];

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function inline($val = true)
    {
        $this->jsOptions['vertical'] = !$val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function blockStyle($val = true)
    {
        $this->jsOptions['shape'] = $val ? 'button' : 'default';
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|array $val
     * @return $this
     */
    public function disabledOptions($val)
    {
        $this->disabledOptions = is_array($val) ? $val : explode(',', $val);
        return $this;
    }

    /**
     * Undocumented function
     * @deprecated 和 disabledOptions 功能相同
     * @param string|array $val
     * @return $this
     */
    public function readonlyOptions($val)
    {
        $this->disabledOptions = is_array($val) ? $val : explode(',', $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function yesOrNo()
    {
        $this->options = [1 => __blang('bilder_option_yes'), 0 => __blang('bilder_option_no')];
        return $this;
    }

    protected function radioOptions()
    {
        $options = [];

        foreach ($this->options as $key => $label) {
            $options[] = [
                'value' => (string)$key,
                'label' => $label,
                'disabled' => in_array($key, $this->disabledOptions) || $this->readonly,
            ];
        }

        return $options;
    }

    protected function fieldScript()
    {
        $fieldId = $this->getId();
        $options = [];

        $options = json_encode($this->inTable ? [] : $this->radioOptions(), JSON_UNESCAPED_UNICODE);

        $script = <<<EOT

    const {$fieldId}Options = ref({$options});

EOT;
        $this->setupScript[] = $script;
        $this->addVueToken([
            "{$fieldId}Options",
        ]);

        $this->whenScript();
    }

    /**
     * Undocumented function
     * 
     * @return array
     */
    public function fieldInfo()
    {
        $info = parent::fieldInfo();
        $info['options'] = $this->radioOptions();

        return $info;
    }
}
