<?php

namespace tpext\builder\traits;

trait HasDom
{
    protected $class = '';

    protected $attr = '';

    protected $style = '';

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    function class($val)
    {
        $this->class = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function attr($val)
    {
        $this->attr = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function style($val)
    {
        $this->style = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addClass($val)
    {
        $this->class .= ' ' . $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addAttr($val)
    {
        $this->attr .= ' ' . $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addStyle($val)
    {
        $this->style .= $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getAttr()
    {
        return $this->attr;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    public function getAttrWithStyle()
    {
        return implode(' ', array_unique(explode(' ', $this->attr))) . (empty($this->style) ? '' : ' style="' . $this->style . '"');
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getClass()
    {
        $arr = explode(' ', $this->class);

        return ' ' . implode(' ', array_unique($arr));
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @return string
     */
    public function getAttrByName($name)
    {
        if (!$this->attr || !stristr($this->attr, $name . '=')) {
            return '';
        }

        if (preg_match('/\b' . $name . '=[\'\"]([^\'\"]*?)[\'\"]\s?/is', $this->attr, $mch)) {
            return $mch[1];
        }

        return '';
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @return string
     */
    public function getStyleByName($name)
    {
        if (!$this->style || !stristr($this->style, $name)) {
            return '';
        }

        $arr = explode(':', $this->style);
        foreach ($arr as $k => $v) {
            if (strtolower($v) == strtolower($name)) {
                return trim(explode(';', $arr[$k + 1] ?? '')[0]);
            }
        }

        return '';
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @return bool
     */
    public function hasClass($name)
    {
        return $this->class && stristr(' ' . $this->class, ' ' . $name);
    }
}
