<?php

namespace tpext\builder\form;

use tpext\builder\displayer\Field;
use tpext\builder\displayer\Fields;
use tpext\builder\common\Form;
use tpext\builder\common\Search;
use tpext\builder\common\Builder;

class When
{
    /**
     * Undocumented variable
     *
     * @var Field
     */
    protected $watchFor = null;

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $cases = '';

    /**
     * Undocumented variable
     *
     * @var Field[]
     */
    protected $fields = [];

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $fieldNames = [];

    /**
     * 
     * @var bool|null
     */
    protected $matchCase = null;

    protected $caseKey = '';

    /**
     * Undocumented variable
     *
     * @var Form|Search
     */
    protected $form;

    /**
     * Undocumented function
     *
     * @param Field $watchFor
     * @param string|int|array $cases
     * @return $this
     */
    public function watch($watchFor, $cases)
    {
        $this->watchFor = $watchFor;
        if (!is_array($cases)) {
            $cases = [(string)$cases];
        }
        $this->cases = $cases;
        $this->caseKey = preg_replace('/[^\w\-]/', '-', $this->watchFor->getName()) . md5(json_encode($this->cases));
        //
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Field $field
     * @return $this
     */
    public function toggle($field)
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Undocumented function
     * 
     * @param mixed $field
     * @return $this
     */
    protected function collectFiledName($field)
    {
        if ($field instanceof Fields) {
            $rows = $field->getContent()->getRows();
            foreach ($rows as $row) {
                $this->collectFiledName($row->getDisplayer());
            }
        } else {
            $this->fieldNames[] = $field->getName();
        }
        return $this;
    }

    /**
     * Undocumented function
     * 
     * @return string
     */
    public function watchForScript()
    {
        $fieldId = $this->watchFor->getId();
        $VModel = $this->watchFor->getVModel();
        $cases = json_encode($this->cases);
        $fieldNames = json_encode(array_values(array_unique($this->fieldNames)));
        $caseKey = $this->caseKey;

        $script = <<<EOT

    const {$caseKey}Cases = {$cases};
    const {$caseKey}MatchCase = ref(false);
    const {$caseKey}FieldNames = {$fieldNames};

    const {$caseKey}IsMatchCase = () => {
        if({$fieldId}DisplayerType == 'Checkbox' || {$fieldId}DisplayerType == 'Transfer' || {$fieldId}DisplayerType == 'MultipleSelect' || {$fieldId}IsArrayValue) {
            let val = {$VModel};
            let cases = [];
            let m = 0;
            let v = '';
            if(!Array.isArray(val)) {
                val = ('' + val).split(',');
            }
            for(let i in {$caseKey}Cases) {
                cases = ('' + {$caseKey}Cases[i]).split('+');
                if(val.length != cases.length) {
                    continue;
                }
                m = 0;
                for(let j in val) {
                    v = ('' + val[j]).trim();
                    if(cases.find(item => v == ('' + item).trim()) !== undefined) {
                        m += 1;
                    }
                }
                if(m > 0 && m == val.length) {
                    return true;
                }
            }
            return false;
        }
        else {
            let val = ('' + {$VModel}).trim();
            return {$caseKey}Cases.find(item => val == ('' + item).trim()) !== undefined;
        }
    };

    const {$caseKey}Judge = () => {
        {$caseKey}MatchCase.value = {$caseKey}IsMatchCase();
        if({$caseKey}MatchCase.value) {
            {$fieldId}MatchCaseFieldNames = {$fieldId}MatchCaseFieldNames.concat({$caseKey}FieldNames);
        }
    };
    
    EOT;

        Builder::getInstance()->addVueToken([
            "{$caseKey}MatchCase"
        ]);

        foreach ($this->fields as $field) {
            $field->getWrapper()->addAttr('v-show="' . $caseKey . 'MatchCase"');
        }

        return $script;
    }

    /**
     * Undocumented function
     *
     * @param Form|Search $val
     * @return $this
     */
    public function setForm($val)
    {
        $this->form = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Form|Search
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Undocumented function
     *
     * @return string|int|array
     */
    public function getCases()
    {
        return $this->cases;
    }

    /**
     * Undocumented function
     * @return string
     */
    public function getCaseKey()
    {
        return $this->caseKey;
    }

    /**
     * Undocumented function
     * @return array
     */
    public function getFieldNames()
    {
        if (empty($this->fieldNames)) {
            foreach ($this->fields as $field) {
                $this->collectFiledName($field);
            }
            $this->fieldNames = array_unique($this->fieldNames);
        }
        return $this->fieldNames;
    }
}
