<?php

namespace tpext\builder\displayer;

class RangeSlider extends Field
{
    protected $view = 'rangeslider';

    protected $size = [2, 6];

    protected $jsOptions = [
        'min' => 0,
        'max' => 100,
        'step' => 1,
        'vertical' => false,
        'range' => false,
    ];

    public function renderValue()
    {
        if (!is_null($this->renderValue)) {
            return $this->renderValue;
        }

        $value = [];
        if (!empty($this->value)) {
            $value = is_array($this->value) ? $this->value : explode(',', $this->value);
        } else if (!empty($this->default)) {
            $value = is_array($this->default) ? $this->default : explode(',', $this->default);
        }

        if (count($value) == 0) {
            $this->renderValue = '';
        } else if (count($value) == 1) {
            $this->renderValue = $value[0];
        } else {
            $this->renderValue = $value;
        }

        return $this->renderValue;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    protected function vBindOp()
    {
        $value = $this->renderValue();

        if (is_array($value)) {
            $this->jsOptions['range'] = true;
        }
        return parent::vBindOp();
    }

    protected function fieldScript()
    {
        $VModel = $this->getVModel();

        $script = <<<EOT

        if (Array.isArray({$VModel})) {
            {$VModel} = {$VModel}.join(',');
        }

EOT;
        $this->convertScript[] = $script;
    }
}
