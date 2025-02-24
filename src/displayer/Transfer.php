<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;
use tpext\builder\traits\HasWhen;

class Transfer extends Field
{
    use HasOptions;
    use HasWhen;

    protected $view = 'transfer';

    protected $valueType = 'array';

    protected $default = [];

    protected $checked = [];

    protected $disabledOptions = [];

    protected $postAsString = false;

    protected $jsOptions = [
        'source-title' => '未勾选',
        'target-title' => '已选择',
        'empty-text' => '暂无数据',
        'key-config' => [
            'value' => 'key',
            'label' => 'text',
            'disabled' => 'disabled'
        ],
        'filter' => false,
    ];

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function default($val = [])
    {
        $this->default = $val;
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
     * @param string|array $val
     * @return $this
     */
    public function disabledOptions($val)
    {
        $this->disabledOptions = is_array($val) ? $val : explode(',', $val);
        return $this;
    }

    protected function fieldScript()
    {
        $fieldId = $this->getId();

        $options = [];

        foreach ($this->options as $key => $label) {
            $options[] = [
                'key' => (string) $key,
                'text' => $label,
                'disabled' => in_array($key, $this->disabledOptions),
            ];
        }

        $options = json_encode($options, JSON_UNESCAPED_UNICODE);

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
}
