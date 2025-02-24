<?php

namespace tpext\builder\displayer;

class Tags extends Field
{
    protected $view = 'tags';
    protected $placeholder = '';

    protected $jsOptions = [
        'border' => true,
        'type' => 'primary',
    ];

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

    protected function fieldScript()
    {
        $fieldId = $this->getId();
        $VModel = $this->getVModel();
        $fieldName = $this->getName();

        $table = '';
        $eventKey = '';
        $url = $this->autoPost['url'] ?? '';
        $refresh = isset($this->autoPost['refresh']) && $this->autoPost['refresh'] ? 'true' : 'false';

        if ($this->formMode == 'table') {
            $table = $this->getForm()->getTableId();
            $eventKey = $table . preg_replace('/\W/', '_', $fieldName) . 'Change';
        }

        $script = <<<EOT

    const {$fieldId}TagIndex = reactive({});
    const {$fieldId}InputVisible = reactive({});
    const {$fieldId}InputValue1 = ref('');
    const {$fieldId}InputValue2 = ref('');
    const {$fieldId}Input1Ref = {};
    const {$fieldId}Input2Ref = {};
    const {$fieldId}Plus = ref(VexipIcon.Plus);

    let {$fieldId}Row = null;//如果是在items中，保持当前行的实例

    const {$fieldId}AddInput1Ref = (el, row) => {
        if(!el) {
            return;
        }
        const key = row ? '{$fieldId}input1_row_' + row.__pk__ : '{$fieldId}input1_row_0';
        {$fieldId}Input1Ref[key] = el;
    };
    const {$fieldId}AddInput2Ref = (el, row) => {
        if(!el) {
            return;
        }
        const key = row ? '{$fieldId}input2_row_' + row.__pk__ : '{$fieldId}input2_row_0';
        {$fieldId}Input2Ref[key] = el;
    };

    const {$fieldId}HandleClose = (row, index) => {
        {$fieldId}Row = row;
        let arr = {$VModel}.split(',').filter(x => x.trim());
        arr.splice(index, 1);
        {$VModel} = arr.join(',');
        if({$fieldId}Row) {
            {$fieldId}Input1Ref['{$fieldId}input1_row_' + {$fieldId}Row.__pk__] = null;
        } else {
            {$fieldId}Input1Ref['{$fieldId}input1_row_0'] = null;
        }
    };

    const {$fieldId}HandleEdit = (row, tag, index) => {
        {$fieldId}Row = row;
        if({$fieldId}Row) {
            {$fieldId}TagIndex['index_row_' + {$fieldId}Row.__pk__] = index;
        } else {
            {$fieldId}TagIndex['index_row_0'] = index;
        }
        {$fieldId}InputValue1.value = tag;
        nextTick(() => {
            if({$fieldId}Row) {
                {$fieldId}Input1Ref['{$fieldId}input1_row_' + {$fieldId}Row.__pk__].focus();
            } else {
                {$fieldId}Input1Ref['{$fieldId}input1_row_0'].focus();
            }
        });
    };

    const {$fieldId}ShowInput = (row) => {
        {$fieldId}Row = row;
        if({$fieldId}Row) {
            {$fieldId}InputVisible['input2_row_' + {$fieldId}Row.__pk__] = true;
        } else {
            {$fieldId}InputVisible['input2_row_0'] = true;
        }
        nextTick(() => {
            if({$fieldId}Row) {
                {$fieldId}Input2Ref['{$fieldId}input2_row_' + {$fieldId}Row.__pk__].focus();
            } else {
                {$fieldId}Input2Ref['{$fieldId}input2_row_0'].focus();
            }
        })
    };

    const {$fieldId}InputConfirm1 = (row, index) => {
        {$fieldId}Row = row;
        let arr = {$VModel}.split(',').filter(x => x.trim());
        if(arr[index] == {$fieldId}InputValue1.value.trim()) {
            {$fieldId}InputValue1.value = '';
            {$fieldId}TagIndex[{$fieldId}Row ? 'index_row_' + {$fieldId}Row.__pk__ : 'index_row_0'] = -1;
            return;
        }
        arr[index] = {$fieldId}InputValue1.value.trim();
        {$VModel} = arr.join(',');
        if({$fieldId}Row) {
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
            {$fieldId}TagIndex['index_row_' + {$fieldId}Row.__pk__] = -1;
        } else {
            {$fieldId}TagIndex['index_row_0'] = -1;
        }
        {$fieldId}InputValue1.value = '';
    };

    const {$fieldId}InputConfirm2 = (row) => {
        {$fieldId}Row = row;
        console.log({$fieldId}InputValue2.value)
        if(!{$fieldId}InputValue2.value.trim()) {
            {$fieldId}InputValue2.value = '';
            {$fieldId}InputVisible[{$fieldId}Row ? 'input2_row_' + {$fieldId}Row.__pk__ : 'input2_row_0'] = false;
            return;
        }
        {$VModel} += ',' + {$fieldId}InputValue2.value.trim();
        if({$fieldId}Row) {
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
            {$fieldId}InputVisible['input2_row_' + {$fieldId}Row.__pk__] = false;
        } else {
            {$fieldId}InputVisible['input2_row_0'] = false;
        }
        {$fieldId}InputValue2.value = '';
    };

EOT;
        $this->setupScript[] = $script;
        $this->addVueToken([
            "{$fieldId}TagIndex",
            "{$fieldId}InputVisible",
            "{$fieldId}InputValue1",
            "{$fieldId}InputValue2",
            "{$fieldId}HandleClose",
            "{$fieldId}HandleEdit",
            "{$fieldId}ShowInput",
            "{$fieldId}InputConfirm1",
            "{$fieldId}InputConfirm2",
            "{$fieldId}AddInput1Ref",
            "{$fieldId}AddInput2Ref",
            "{$fieldId}Plus",
        ]);
    }

    public function customVars()
    {
        return [
            'editText' => __blang('bilder_page_edit_text'),
            'placeholder' => $this->placeholder ?: __blang('bilder_please_enter') . $this->label
        ];
    }
}
