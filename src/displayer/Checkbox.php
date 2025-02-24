<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasWhen;
use tpext\builder\traits\HasOptions;

class Checkbox extends Field
{
    use HasOptions;
    use HasWhen;

    protected $view = 'checkbox';

    protected $valueType = 'array';

    protected $checkallBtn = '';

    protected $default = [];

    protected $value = [];

    protected $checked = [];

    protected $disabledOptions = [];

    protected $postAsString = false;

    /**
     * @var array
     */
    protected $jsOptions = [
        'vertical' => false,
        'border' => false,//不支持像 Radio:shape一样的属性，blockStyle使用此属性代替
        // 'min' => 0,//不支持
        // 'max' => 9999,
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
        $this->jsOptions['border'] = $val ? true : false;
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
     * @param string $val
     * @return $this
     */
    public function checkallBtn($val = '全选')
    {
        $this->checkallBtn = $val;
        return $this;
    }

    /**
     * 提交时是否把数组转成字符串
     * 
     * @param boolean $val
     * @return $this
     */
    public function postAsString($val = true)
    {
        $this->postAsString = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function default($val = [])
    {
        $this->default = is_array($val) ? $val : explode(',', $val);
        return $this;
    }

    protected function checkboxOptions()
    {
        $options = [];

        foreach ($this->options as $key => $label) {
            $options[] = [
                'value' => (string) $key,
                'label' => $label,
                'disabled' => in_array($key, $this->disabledOptions) || $this->readonly,
            ];
        }

        return $options;
    }

    protected function fieldScript()
    {
        $fieldId = $this->getId();

        $options = json_encode($this->inTable ? [] : $this->checkboxOptions(), JSON_UNESCAPED_UNICODE);

        $script = <<<EOT

    const {$fieldId}Options = ref({$options});

EOT;
        $this->setupScript[] = $script;
        $this->addVueToken([
            "{$fieldId}Options",
        ]);

        $this->whenScript();

        if ($this->postAsString) {
            $VModel = $this->getVModel();
            
            $script = <<<EOT

        if (Array.isArray({$VModel})) {
            {$VModel} = {$VModel}.join(',');
        }

EOT;
            $this->convertScript[] = $script;
        }
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function customVars()
    {
        return [
            'checkallBtn' => $this->checkallBtn,
        ];
    }

    /**
     * Undocumented function
     * 
     * @return array
     */
    public function fieldInfo()
    {
        $info = parent::fieldInfo();
        $info['options'] = $this->checkboxOptions();

        return $info;
    }
}
