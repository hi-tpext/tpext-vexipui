<?php

namespace tpext\builder\displayer;

class Number extends Field
{
    protected $view = 'number';

    protected $rules = 'number';

    protected $size = [2, 2];

    protected $placeholder = '';

    protected $default = 0;

    /**
     *
     * step 　  步长
     * unit 	数值的单位。在设置单位时，加减按钮将不可用
     * precision 	数值的精度。小数点后保留几位
     * max 	最大值
     * min 	最小值
     * controls-position 	控制按钮位置。'right'
     * @var array
     */
    protected $jsOptions = [
        'step' => 1,
        'unit' => '',
        'precision' => 0,//小数点后保留几位
        // 'max' => '',
        'min' => 0,
    ];

    /**
     * Undocumented function
     *
     * @param int $val
     * @return $this
     */
    public function min($val)
    {
        $this->jsOptions['min'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param int $val
     * @return $this
     */
    public function max($val)
    {
        $this->jsOptions['max'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param int $val
     * @return $this
     */
    public function step($val)
    {
        $this->jsOptions['step'] = $val;
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

    public function customVars()
    {
        return [
            'placeholder' => $this->placeholder ?: __blang('bilder_please_enter') . $this->label
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

    const {$fieldId}Change = (row, e) => {
        if('{$table}') {
            if({$eventKey}Timer) {
                clearTimeout({$eventKey}Timer);
                {$eventKey}Timer = null;
            }
            {$eventKey}Timer = setTimeout(() => {
                let params = {
                    id: row.__pk__,
                    name: '{$fieldName}',
                    value: {$VModel},
                };
                {$table}SendData('{$url}', params, $refresh);
            }, 600);
        }
    };

EOT;
        $this->setupScript[] = $script;

        $this->addVueToken([
            "{$fieldId}Change",
        ]);
    }
}
