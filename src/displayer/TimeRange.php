<?php

namespace tpext\builder\displayer;

class TimeRange extends Time
{
    protected $size = [2, 3];

    protected $separator = ',';

    public function created($type = '')
    {
        parent::created($type);
        $this->jsOptions['range'] = true;
    }

    /**
     * Undocumented function
     * ','
     * @param string $val
     * @return $this
     */
    public function separator($val = ',')
    {
        $this->separator = $val;
        return $this;
    }

    public function renderValue()
    {
        if (!is_null($this->renderValue)) {
            return $this->renderValue;
        }

        $value = parent::renderValue();

        $this->renderValue = explode($this->separator, $value);

        return $this->renderValue;
    }

    protected function fieldScript()
    {
        parent::fieldScript();
        
        $VModel = $this->getVModel();

        $script = <<<EOT

        if (Array.isArray({$VModel})) {
            {$VModel} = {$VModel}.join('{$this->separator}');
        }

EOT;
        $this->convertScript[] = $script;
    }
}
