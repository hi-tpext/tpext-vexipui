<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Matche extends Raw
{
    use HasOptions;

    protected $input = false;

    public function renderValue()
    {
        if (!is_null($this->renderValue)) {
            return $this->renderValue;
        }

        $value = !($this->value === '' || $this->value === null) ? $this->value : $this->default;

        if (isset($this->options[$value])) {
            $value = $this->options[$value];
        } else if (isset($this->options['__default__'])) {
            $value = $this->options['__default__'];
        }

        if (!empty($this->to)) {
            $this->renderValue = $this->parseToValue($value);
        } else {
            $this->renderValue = $value;
        }

        return $this->renderValue;
    }

    public function yesOrNo()
    {
        $this->options = [1 => __blang('bilder_option_yes'), 0 => __blang('bilder_option_no')];
        return $this;
    }
}
