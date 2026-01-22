<?php

namespace tpext\builder\displayer;

class Text extends Field
{
    protected $view = 'text';

    protected $after = '';

    protected $befor = '';

    protected $prefix = '';

    protected $suffix = '';

    protected $placeholder = '';

    /**
     *
     * maxlength 　    最大输入长度
     * @var array
     */
    protected $jsOptions = [
        'max-length' => '',
        'clearable' => true,
    ];

    /**
     * Undocumented function
     *
     * @param string $val from | table | search
     * @return $this
     */
    public function setFormMode($val)
    {
        parent::setFormMode($val);
        if ($val == 'table') {
            $this->jsOptions['clearable'] = false;
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $val
     * @return $this
     */
    public function maxlength($val = 0)
    {
        $this->jsOptions['max-length'] = $val;
        return $this;
    }

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

    /**
     * Undocumented function
     *
     * @param string $html
     * @return $this
     */
    public function befor($html)
    {
        $this->befor = $html;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $html
     * @return $this
     */
    public function after($html)
    {
        $this->after = $html;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $html
     * @return $this
     */
    public function beforSymbol($html)
    {
        $this->prefix = $html;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $html
     * @return $this
     */
    public function afterSymbol($html)
    {
        $this->suffix = $html;
        return $this;
    }

    public function customVars()
    {
        return [
            'befor' => $this->befor,
            'after' => $this->after,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'placeholder' => $this->placeholder ?: __blang('builder_please_enter') . $this->label
        ];
    }

    protected function fieldScript()
    {
        //Blur事件处理 table中的 autoPost 
        $fieldId = $this->getId();
        $VModel = $this->getVModel();
        $fieldName = $this->getName();
        $url = $this->autoPost['url'] ?? '';
        $refresh = isset($this->autoPost['refresh']) && $this->autoPost['refresh'] ? 'true' : 'false';

        $table = '';
        $eventKey = '';
        if ($this->formMode == 'table') {
            $table = $this->getForm()->getTableId();
            $eventKey = $table . preg_replace('/\W/', '_', $fieldName) . 'Change';
        }

        $script = <<<EOT

    const {$fieldId}Blur = (row, e) => {
        if('{$table}') {
            if({$eventKey}Timer) {
                clearTimeout({$eventKey}Timer);
                {$eventKey}Timer = null;
            }
            {$eventKey}Timer = setTimeout(() => {
                if({$table}ActiveRowChanged['{$fieldName}']!== undefined) {
                    {$table}ActiveRowChanged['{$fieldName}'] = undefined;
                    let params = {
                        id: row.__pk__,
                        name: '{$fieldName}',
                        value: {$VModel},
                    };
                    {$table}SendData('{$url}', params, $refresh);
                }
            }, 500);
        }
    };

    //输入过程中不改变模型值，需要监听事件
    const {$fieldId}Change = (value) => {
        if('{$table}') {
            {$table}ActiveRowChanged['{$fieldName}'] = value;
        }
    };

    //输入后需要失去焦点才更新值，监听事件强制改变
    const {$fieldId}Input = (row, value) => {
        {$VModel} = value;
    };

EOT;
        $this->setupScript[] = $script;

        $this->addVueToken([
            "{$fieldId}Blur",
            "{$fieldId}Change",
            "{$fieldId}Input",
        ]);
    }
}
