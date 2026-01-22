<?php

namespace tpext\builder\displayer;

class Load extends Field
{
    protected $view = 'load';
    protected $input = false;
    protected $ajax = [];
    protected $separator = '、';
    protected $size = [2, 10];
    public $loadingText = '加载中...';
    protected $inline = true;

    public function created($type = '')
    {
        $this->loadingText = __blang('builder_loading');
        parent::created($type);
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
     * @param string $val 加载中...|&nbsp;
     * @return $this
     */
    public function loadingText($val = '&nbsp;')
    {
        $this->loadingText = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $textField text|name
     * @return $this
     */
    public function dataUrl($url, $textField = '')
    {
        $this->ajax = [
            'url' => (string)$url,
            'text' => $textField,
        ];

        return $this;
    }

    public function renderValue()
    {
        if (!is_null($this->renderValue)) {
            return $this->renderValue;
        }

        $value = parent::renderValue();

        $this->renderValue = [$value, $this->loadingText];

        return $this->renderValue;
    }

    protected function fieldScript()
    {
        $fieldId = $this->getId();
        $VModel = $this->getVModel();
        $url = $this->ajax['url'];
        $text = $this->ajax['text'] ?: '_';
        $separator = $this->separator ?: '、';
        $formMode = $this->formMode;
        $inTable = $this->inTable ? 'true' : 'false';

        $script = <<<EOT

    const {$fieldId}InTable = {$inTable};
    const {$fieldId}AjaxCache = {};

    const {$fieldId}LoadData = (selected) => {
        let params = {
            q: '',
            page: 1,
            selected : selected,
            ele_id : '{$fieldId}',
            prev_ele_id : '',
            idField : '',
            textField : '{$text}' == '_' ? null : '{$text}'
        };

        return new Promise((resolve, reject) => {
            axios({
                method: 'get',
                url: '{$url}',
                responseType: 'json',
                params: params,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                timeout: 60000,
            }).then(res => {
                let list = res.data.data || res.data || [];
                let texts = [];
                list.forEach(d => {
                    texts.push(d.__text__ || d['{$text}'] || d.text);
                });

                let result = texts.length ? texts.join('{$separator}') : __blang.builder_value_is_empty;
                return resolve(result);
            })
            .catch(e => {
                console.log(e);
                VxpMessage.error(__blang.builder_network_error + (e.message || JSON.stringify(e)));
            });
        });
    };

    if('{$formMode}' == 'form' && !{$fieldId}InTable) {
        if({$VModel}[0] !== '') {
            {$fieldId}LoadData({$VModel}).then(result => {
                {$VModel}[1] = result;
            }).catch(e => {
                {$VModel}[1] = __blang.builder_loading_error;
            });
        } else {
            {$VModel} = __blang.builder_value_is_empty;
        }
    }

EOT;
        $this->onMountedScript[] = $script;
    }

    /**
     * 在列表中时初始化脚本
     * @return string
     */
    public function getInitRowScript()
    {
        $fieldName = $this->getName();
        $fieldId = $this->getId();

        $script = <<<EOT
        
        if(row.{$fieldName}[0] !== '') {
            if({$fieldId}AjaxCache[row.{$fieldName}[0]]) {
                row.{$fieldName}[1] = {$fieldId}AjaxCache[row.{$fieldName}[0]];
            }
            else {
                {$fieldId}LoadData(row.{$fieldName}).then(result => {
                    row.{$fieldName}[1] = result;
                    {$fieldId}AjaxCache[row.{$fieldName}[0]] = result;
                }).catch(e => {
                    row.{$fieldName}[1] = __blang.builder_loading_error;
                });    
            }
        } else {
            row.{$fieldName} = __blang.builder_value_is_empty;
        }
EOT;

        return $script;
    }

    public function customVars()
    {
        return [
            'inline' => $this->inline,
        ];
    }
}
