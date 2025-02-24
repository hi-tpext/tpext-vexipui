<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Matches extends Raw
{
    use HasOptions;

    protected $input = false;

    protected $separator = '、';

    /**
     * Undocumented function
     * ','
     * @param string $val
     * @return $this
     */
    public function separator($val = '、')
    {
        $this->separator = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $type
     */
    public function created($type = '')
    {
        parent::created($type);
        $this->separator = __blang('bilder_default_separator');
    }

    public function renderValue()
    {
        if (!is_null($this->renderValue)) {
            return $this->renderValue;
        }

        $value = !($this->value === '' || $this->value === null || $this->value === []) ? $this->value : $this->default;

        $values = is_array($value) ? $value : explode(',', $value);

        $texts = [];

        foreach ($values as $val) {
            if (isset($this->options[$val])) {
                $texts[] = $this->options[$val];
            }
        }

        $value = implode($this->separator, $texts);

        if (!empty($this->to)) {
            $this->renderValue = $this->parseToValue($value);
        } else {
            $this->renderValue = $value;
        }

        return $this->renderValue;
    }
}
