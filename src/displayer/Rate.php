<?php

namespace tpext\builder\displayer;

class Rate extends Text
{
    protected $view = 'rate';

    protected $valueType = 'float';
    protected $size = [2, 4];
    protected $texts = ['极差', '差', '一般', '好', '极好'];
    protected $allowHalf = true;
    protected $default = 1;

    protected $jsOptions = [
        'min' => 1,
        'max' => 5,
        'marker-only' => true,
    ];

    /**
     * @param array $val
     * @return $this
     */
    public function texts($val)
    {
        $this->texts = $val;

        return $this;
    }

    /**
     * @param bool $val
     * @return $this
     */
    public function allowHalf($val = true)
    {
        $this->allowHalf = $val;
        return $this;
    }

    public function renderValue()
    {
        if (!is_null($this->renderValue)) {
            return $this->renderValue;
        }

        $value = parent::renderValue();

        $this->renderValue = ($value ?: 0) * 10;

        return $this->renderValue;
    }

    protected function fieldScript()
    {
        $markers = [];
        $this->jsOptions['max'] = $this->jsOptions['min'] + count($this->texts) - 1;
        $step = $this->allowHalf ? 0.5 : 1;

        for ($i = $this->jsOptions['min']; $i <= $this->jsOptions['max']; $i += $step) {
            $markers[$i * 10] = [
                'label' => (int) $i == $i ? $i : '',
                'text' => $this->texts[(int) $i - 1],
            ];
        }

        $this->jsOptions['min'] *= 10;
        $this->jsOptions['max'] *= 10;

        $fieldId = $this->getId();
        $VModel = $this->getVModel();
        $markers = json_encode($markers, JSON_UNESCAPED_UNICODE);

        $script = <<<EOT

    const {$fieldId}Markers = ref({$markers});
    const {$fieldId}Star = ref(VexipIcon.Star);
    const {$fieldId}Text = ref('');
    
EOT;
        $this->setupScript[] = $script;

        $this->addVueToken([
            "{$fieldId}Markers",
            "{$fieldId}Star",
            "{$fieldId}Text",
        ]);

        $script = <<<EOT

        watch(
            () => {$VModel},
            (newValue, oldValue) => {
                {$fieldId}Text.value = {$fieldId}Markers.value[{$VModel}] ? {$fieldId}Markers.value[{$VModel}].text + ':' + ({$VModel}/10) : {$VModel}/10;
            },
            {
                immediate: true
            }
        );
    
EOT;
        $this->onMountedScript[] = $script;

        $script = <<<EOT

        {$VModel} = ({$VModel} || 0) / 10;
    
EOT;
        $this->convertScript[] = $script;
    }
}
