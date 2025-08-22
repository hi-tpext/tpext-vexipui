<?php

namespace tpext\builder\toolbar;

use tpext\builder\common\Builder;

class LinkBtn extends Bar
{
    protected $view = 'linkbtn';

    protected $postChecked = '';

    protected $openChecked = '';

    protected $confirm = true;

    /**
     * Undocumented function
     *
     * @param string $url
     * @param boolean|string $confirm
     * @return $this
     */
    public function postChecked($url, $confirm = true)
    {
        $this->postChecked = (string)$url;
        $this->confirm = $confirm ? $confirm : 0;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return $this
     */
    public function openChecked($url)
    {
        $this->openChecked = $url;

        return $this;
    }

    protected function operateCheckedScript()
    {
        $btnId = $this->getId();
        $table = $this->tableId;

        $label = $this->label;

        if (!$label) {
            $label = $this->getAttrByName('title') ?: $this->getAttrByName('data-title');
        }

        $script = <<<EOT

    const {$btnId}Click = () => {
        if({$table}SelectedSize.value == 0) {
            VxpMessage.warning(__blang.bilder_no_data_was_selected);
            return false;
        }
    
        let {$table}Selected = {$table}Ref.value.getSelected();
        let ids = {$table}Selected.map(x => { return x.__pk__  }).join(',');

        let openCheckedUrl = '{$this->openChecked}';
        let postCheckedUrl = '{$this->postChecked}';

        if(openCheckedUrl) {
            {$btnId}OpenChecked(ids, openCheckedUrl);
            return false;
        }
        let confirm = '{$this->confirm}';
        if (confirm && confirm != '0' && confirm != 'false') {
            if (confirm == '1') {
                let text = '{$this->label}'.trim() || __blang.bilder_this;
                confirm = __blang.bilder_confirm_to_do_batch_operation + ' [' + text + '] ' + __blang.bilder_action_operation + ' ?';
            }
            VxpConfirm.open({
                title : __blang.bilder_operation_tips,
                content: confirm,
                confirmText : __blang.bilder_button_ok,
                cancelText : __blang.bilder_button_cancel,
                confirmType: 'warning',
            }).then((res) => {
                if(res) {
                    {$btnId}PostChecked(postCheckedUrl, ids);
                }
            });
            return false;
        }
        {$btnId}PostChecked(postCheckedUrl, ids);
    };

    const {$btnId}PostChecked = (url, ids) => {
        let data = {ids};
        {$table}SendData(url, data, 1);
    };

    const {$btnId}OpenChecked = (url, ids) => {
        window.refreshTable = () => {
            {$table}Refresh();
        };
        layerOpenLink(url + (/.+\?.*/.test(url) ? '&ids=' : '?ids=') + ids, '{$label}', '{$this->layerSize}');
    };

    const {$btnId}Op = ref({
        'size' : 'small',
        'simple' : true,
        'type' : '{$this->type}',
        'class' : ['{$this->class}', { 'btn-disabled' : {$table}MultipleToolbarDisabled}]
        // 'disabled' : {$table}MultipleToolbarDisabled,
    });

EOT;
        $this->setupScript[] = $script;
        Builder::getInstance()->addVueToken(["{$btnId}Op", "{$btnId}Click"]);

        return $script;
    }

    protected function buttonScript()
    {
        $btnId = $this->getId();
        $table = $this->tableId;

        $href = '';

        $label = $this->label;

        if (!$label) {
            $label = $this->getAttrByName('title') ?: $this->getAttrByName('data-title');
        }

        if ($this->useLayer) {
            $href = empty($this->__href__) ? $this->href : $this->__href__;
        }

        $script = <<<EOT

    const {$btnId}Click = () => {
        let name = '{$this->name}';
        let href = '{$href}';

        if(name == 'refresh') {
            {$table}Refresh();
        } else if(name =='toggle_search') {
            {$table}ToggleSearch();
        } else if(name == 'export') {
            {$table}ExportData({$btnId}PostUrl, data.itemData);
        } else if(href) {
            window.refreshTable = () => {
                {$table}Refresh();
            };
            layerOpenLink(href, '{$label}', '{$this->layerSize}');
        } else {
            //自行处理按钮事件
            {$this->onClick}
        }
    };

    const {$btnId}Op = ref({
        'size' : 'small',
        'simple' : true,
        'type' : '{$this->type}',
        'class' : '{$this->class}',
    });

EOT;
        $this->setupScript[] = $script;
        Builder::getInstance()->addVueToken(["{$btnId}Op", "{$btnId}Click"]);

        return $script;
    }

    public function beforRender()
    {
        if ($this->postChecked) {
            if (Builder::checkUrl($this->postChecked)) {
                $this->operateCheckedScript();
            } else {
                $this->hidden = true;
            }
        } else if ($this->openChecked) {
            if (Builder::checkUrl($this->openChecked)) {
                $this->operateCheckedScript();
            } else {
                $this->hidden = true;
            }
        } else {
            $this->buttonScript();
        }

        return parent::beforRender();
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        if ($this->hidden) {
            return '';
        }
        $vars = $this->commonVars();

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
