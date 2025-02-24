<?php

namespace tpext\builder\displayer;

class Show extends Field
{
    protected $view = 'show';

    protected $input = false;

    protected $sublen = 0;

    protected $more = '...';

    protected $inline = false;

    /**
     * Undocumented function
     *
     * @param integer $len
     * @param string $more
     * @return $this
     */
    public function cut($len = 0, $more = '...')
    {
        $this->sublen = $len;
        $this->more = $more;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function inline($val = true)
    {
        $this->inline = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function renderValue()
    {
        if (!is_null($this->renderValue)) {
            return $this->renderValue;
        }

        $value = parent::renderValue();
        if ($this->sublen > 0 && $value) {
            $sub = $value;
            if (mb_strlen($value) > $this->sublen) {
                $sub = mb_substr($value, 0, $this->sublen);
                $this->renderValue = [$sub, $value, false];
            }
        }

        return $this->renderValue;
    }

    public function customVars()
    {
        return [
            'more' => $this->more,
            'inline' => $this->inline,
        ];
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function readonly($val = false)
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function disabled($val = false)
    {
        return $this;
    }
}
