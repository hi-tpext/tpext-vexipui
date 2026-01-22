<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasWhen;
use tpext\builder\traits\HasTreeData;

class Cascader extends Field
{
    use HasTreeData;
    use HasWhen;
    protected $view = 'cascader';

    protected $size = [2, 6];

    protected $placeholder = '';

    protected $jsOptions = [
        'clearable' => true,
        'merge-tags' => false,
        'multiple' => false,
        'separator' => '/',
        'key-config' => [
            'value' => 'id',
            'label' => 'text',
            'children' => 'children',
            'disabled' => 'disabled',
            'hasChild' => 'has_child'
        ]
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

    protected function fieldScript()
    {
        $fieldId = $this->getId();

        $options = json_encode($this->options, JSON_UNESCAPED_UNICODE);

        $script = <<<EOT

    const {$fieldId}Options = ref({$options});

EOT;
        $this->setupScript[] = $script;
        $this->addVueToken([
            "{$fieldId}Options",
        ]);

        $this->whenScript();
    }

    public function customVars()
    {
        return [
            'placeholder' => $this->placeholder ?: __blang('builder_please_select') . $this->label
        ];
    }
}
