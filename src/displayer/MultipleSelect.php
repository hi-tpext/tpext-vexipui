<?php

namespace tpext\builder\displayer;

class MultipleSelect extends Select
{
    protected $valueType = 'array';

    /**
     * Undocumented variable
     *
     * @var array|string
     */
    protected $default = [];

    /**
     * Undocumented variable
     *
     * @var array|string
     */
    protected $checked = [];

    protected $postAsString = false;

    public function created($type = '')
    {
        parent::created($type);

        $multipleOptios = [
            'multiple' => true,
            'count-limit' => 0,//多选时限制最大的可选数量，为 0 时不限制
            'max-tag-count' => 0,//在多选模式下，设置显示的最大标签数，为 0 时会动态计算以确保在一行内显示
            'option-check' => true,//设置开启被选选项打勾功能
            'tag-type' => null,//设置多选模式下标签的类型
        ];

        $this->jsOptions($multipleOptios);
    }

    /**
     * Undocumented function
     *
     * @param int $val
     * @return $this
     */
    public function limit($val)
    {
        $this->jsOptions['count-limit'] = $val;
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
        $this->default = $val;
        return $this;
    }

    protected function fieldScript()
    {
        parent::fieldScript();
        
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
