<?php

namespace tpext\builder\traits;

use tpext\builder\form\When;

trait HasWhen
{
    /**
     * Undocumented variable
     *
     * @var When[]
     */
    protected $whens = [];

    /**
     * Undocumented variable
     *
     * @var When
     */
    protected $__when__ = null;

    protected $whenWrapper = false;

    /**
     * Undocumented function
     *
     * @param string|int|array $cases 如：'1' 或 '1 + 2' 或 ['1 + 2', '2 + 3']
     * @param array|\Closure|mixed ...$toggleFields
     * @return $this
     */
    public function when($cases, ...$toggleFields)
    {
        $form = $this->getForm();
        $when = $form->createWhen($this, $cases);

        $this->__when__ = $when;
        $this->whens[] = $when;

        if (count($toggleFields)) {

            if ($toggleFields[0] instanceof \Closure) { //如果是匿名回调
                //fields包围优化
                $this->makeWhenWrapper();
                $this->with(...$toggleFields);
                return $this;
            } else {
                //无法fields包围优化，多层次when嵌套时不推荐：
                //->when('1', $field1, $field2, ...)
                //或
                //->when('1', [$field1, $field2, ...])

                if (is_array($toggleFields[0])) {
                    $toggleFields = $toggleFields[0];
                }

                foreach ($toggleFields as $field) {
                    $this->__when__->toggle($field);
                }
            }

            $form->whenEnd();
            $this->whenEnd();
            //如果此处传入[toggleFields]参数，那么就结束，后面就不要再调用with($toggleFields)方法了。否则，后面可以继续调用with($toggleFields)方法;
        } else {
            //fields包围优化
            $this->makeWhenWrapper();
        }

        return $this;
    }

    protected function makeWhenWrapper()
    {
        //创建一个fields把后面的 toggleFields装进去，解决多层when的嵌套的一些问题
        $form = $this->getForm();
        $whenWrapper = $form->fields(preg_replace('/\W/', '_', $this->getName()) . '_when_' . count($this->whens))
            ->showLabel(false)
            ->addClass('when-wrapper')
            ->size(0, 12);
        $this->__when__->toggle($whenWrapper);
        $this->whenWrapper = true;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function emptyWhens()
    {
        return empty($this->whens);
    }

    /**
     * Undocumented function
     *
     * @param array|\Closure|mixed ...$toggleFields
     * @return $this
     */
    public function with(...$toggleFields)
    {
        if (!$this->__when__) {
            throw new \LogicException('when($cases, ...$toggleFields)第二个参数[toggleFields]已传入，后续不要继续调用with');
        }

        $form = $this->getForm();

        if (count($toggleFields)) {
            if ($toggleFields[0] instanceof \Closure) {
                $toggleFields[0]($form);
            }
        }

        $form->whenEnd();
        $this->whenEnd();

        if ($this->whenWrapper) {
            $form->fieldsEnd();
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function whenEnd()
    {
        $this->__when__ = null;
        return $this;
    }

    public function whenScript()
    {
        if (count($this->whens) == 0) {
            return;
        }
        $fieldId = $this->getId();
        $VModel = $this->getVModel();
        $isArrayValue = $this->isArrayValue() ? 'true' : 'false';
        $DisplayerType = $this->getDisplayerType();
        $formId = $this->getForm()->getFormId();

        $allCaseFieldNames = [];
        $caseJudges = [];

        foreach ($this->whens as $when) {
            $caseJudges[] = $when->getCaseKey() . 'Judge()';
            $allCaseFieldNames = array_merge($allCaseFieldNames, $when->getFieldNames());
        }

        $caseJudges = implode(";\r\n\t\t\t\t", $caseJudges);
        $allCaseFieldNames = json_encode(array_values(array_unique($allCaseFieldNames)));

        $script = <<<EOT

    const {$fieldId}IsArrayValue = {$isArrayValue};
    const {$fieldId}DisplayerType = '{$DisplayerType}';
    const {$fieldId}AllCaseFieldNames = {$allCaseFieldNames};
    let {$fieldId}MatchCaseFieldNames = [];

EOT;
        $this->addSetupScript($script);

        foreach ($this->whens as $when) {
            $this->addSetupScript($when->watchForScript());
        }

        $script = <<<EOT

    const {$fieldId}Judge = () => {
        {$fieldId}MatchCaseFieldNames = [];
        {$caseJudges};
        let notMatcheFieldsNames = {$fieldId}AllCaseFieldNames.filter(name => !{$fieldId}MatchCaseFieldNames.includes(name));
        let newRules = {};
        for(let key in {$formId}Rules) {
            if(!notMatcheFieldsNames.includes(key)) {
                newRules[key] = {$formId}Rules[key];
            }
        }
        {$formId}Op.value.rules = newRules;
    };

    watch(
        () => {$VModel},
        (val, oldVal) => {
            {$fieldId}Judge();
        }
    );

    setTimeout(() => {$fieldId}Judge(), 10 * {$fieldId}AllCaseFieldNames.length);
    
EOT;
        $this->addOnMountedScript($script);
    }
}
