<?php

namespace tpext\builder\toolbar;

use tpext\builder\common\Builder;

class DropdownBtns extends Bar
{
    protected $view = 'dropdownbtns';

    protected $items = [];

    protected $checkbox = false;

    /**
     * Undocumented function
     *
     * @param array $items
     * @return $this
     */
    public function items($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function checkbox($val = true)
    {
        $this->checkbox = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    public function isEmpty()
    {
        return empty($this->items);
    }

    protected function buttonScript()
    {
        $btnId = $this->getId();
        $table = $this->tableId;
        $keys = json_encode(array_keys($this->getItems()));

        $script = <<<EOT

    let {$btnId}Name = '{$this->name}';
    let {$btnId}PostUrl = '{$this->href}';
    let {$btnId}ChooseColumnTimer = null;

    const {$btnId}BtnType = ref('{$this->type}' || '');
    const {$btnId}Checked = ref({$keys});

    const {$btnId}ItemClick = (label) => {
        if({$btnId}Name == 'exports') {
            {$table}ExportData({$btnId}PostUrl, label);
        }
    };

    const {$btnId}Change = (value) => {
        if({$btnId}Name == 'choose_columns') {
            if (value.length == 0) {
                VxpMessage.error(__blang.builder_show_at_least_one_field);
            }
            if({$btnId}ChooseColumnTimer) {
                clearTimeout({$btnId}ChooseColumnTimer);
                {$btnId}ChooseColumnTimer = null;
            }
            {$btnId}ChooseColumnTimer = setTimeout(() => {
                {$table}UseChooseColumns.value = value;
                {$table}Ref.value.refresh();//刷新表格，将会触发表格的重新布局及数据渲染
            }, 500);
        }
    };

    const {$btnId}Op = ref({
        'outside-close' : true,
        'trigger' : 'hover',
    });

EOT;
        $this->setupScript[] = $script;

        Builder::getInstance()->addVueToken(["{$btnId}Op", "{$btnId}Checked", "{$btnId}ItemClick", "{$btnId}BtnType", "{$btnId}Change"]);

        return $script;
    }

    public function beforRender()
    {
        $this->buttonScript();

        return parent::beforRender();
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $vars = $this->commonVars();

        $actions = [];

        $items = $this->getItems();

        foreach ($items as $key => $it) {
            if (is_string($it)) {
                $it = ['label' => $it];
            }
            $data = array_merge(
                [
                    'key' => $key,
                    'label' => '',
                    'icon' => '',
                    'attr' => '',
                    'class' => '',
                ],
                $it
            );

            $actions[$key] = $data;
        }

        $vars = array_merge($vars, [
            'items' => $actions,
            'checkbox' => $this->checkbox,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
