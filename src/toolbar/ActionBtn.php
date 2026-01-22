<?php

namespace tpext\builder\toolbar;

use think\Model;
use tpext\builder\common\Builder;

class ActionBtn extends Bar
{
    protected $view = 'actionbtn';

    protected $mapClass = [];

    protected $matchClass = [];

    protected $postRowid = '';

    protected $extClass = '';

    protected $onClick = '';

    protected $data = [];

    protected $dataId = 0;

    protected $confirm = true;

    protected $initPostRowidScript = false;

    protected $parsedLabel = null;

    /**
     * Undocumented function
     *
     * @param array|Model|\ArrayAccess $data
     * @return $this
     */
    public function data($data = [])
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function parseUrl()
    {
        $data = $this->data;

        if (empty($this->href) || empty($data)) {
            return $this;
        }

        preg_match_all('/__data\.([\w\.]+)__/i', $this->href, $matches);

        $keys = ['__data.pk__'];
        $replace = [$this->dataId];
        $arr = [];

        if (isset($matches[1]) && count($matches[1]) > 0) {
            foreach ($matches[1] as $match) {
                $arr = explode('.', $match);

                if (count($arr) == 1) {

                    $keys[] = '__data.' . $arr[0] . '__';
                    $replace[] = isset($data[$arr[0]]) ? $data[$arr[0]] : '';
                } else if (count($arr) == 2) {

                    $keys[] = '__data.' . $arr[0] . '.' . $arr[1] . '__';
                    $replace[] = isset($data[$arr[0]]) && isset($data[$arr[0]][$arr[1]]) ? $data[$arr[0]][$arr[1]] : '';
                } else {
                    //最多支持两层 xx 或 xx.yy
                }
            }

            $this->__href__ = str_replace($keys, $replace, $this->href);
        } else {
            $this->__href__ = $this->href;
        }

        return $this;
    }

    public function parseLabel()
    {
        if (!is_null($this->parsedLabel)) {
            return $this->parsedLabel;
        }

        $data = $this->data;

        $label = $this->label;

        if ($label instanceof \Closure) {
            $this->parsedLabel = $label($data);
            return $this->parsedLabel;
        }

        if (empty($label)) {
            $this->parsedLabel = '';
            return $this->parsedLabel;
        }
        if (empty($data)) {
            $this->parsedLabel = $label;
            return $this->parsedLabel;
        }

        preg_match_all('/\{([\w\.]+)\}/', $label, $matches);

        $keys = [];
        $replace = [];
        $arr = [];

        if (isset($matches[1]) && count($matches[1]) > 0) {
            foreach ($matches[1] as $match) {
                $arr = explode('.', $match);

                if (count($arr) == 1) {

                    $keys[] = '{' . $arr[0] . '}';
                    $replace[] = isset($data[$arr[0]]) ? $data[$arr[0]] : '';
                } else if (count($arr) == 2) {

                    $keys[] = '{' . $arr[0] . '.' . $arr[1] . '}';
                    $replace[] = isset($data[$arr[0]]) && isset($data[$arr[0]][$arr[1]]) ? $data[$arr[0]][$arr[1]] : '-';
                } else {
                    //最多支持两层 xx 或 xx.yy
                }
            }

            $val = str_replace($keys, $replace, $label);
        } else {
            $val = $label;
        }

        $this->parsedLabel = $val;

        return $this->parsedLabel;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function parseMapClass()
    {
        $data = $this->data;
        $this->matchClass = [];

        $values = $class = $field = $logic = $val = $match = null;

        foreach ($this->mapClass as $class => $mp) {
            if (is_array($mp)) { // 'enable' => ['hidden' => [1, 'status']],
                $values = $mp[0];

                if (!is_array($values)) {
                    $values = [$values];
                }

                $field = $mp[1];
                $logic = $mp[2] ?? ''; //in_array|not_in_array|eq|gt|lt|egt|elt|strstr|not_strstr

                if (strstr($field, '.')) {

                    $arr = explode('.', $field);

                    if (isset($data[$arr[0]]) && isset($data[$arr[0]][$arr[1]])) {

                        $val = $data[$arr[0]][$arr[1]];
                    } else {
                        continue;
                    }
                } else {

                    if (!isset($data[$field])) {
                        continue;
                    }

                    $val = $data[$field];
                }

                $match = false;
                if ($logic == 'not_in_array') {
                    $match = !in_array($val, $values);
                } else if ($logic == 'eq' || $logic == '==') {
                    $match = $val == $values[0];
                } else if ($logic == 'gt' || $logic == '>') {
                    $match = is_numeric($values[0]) && $val > $values[0];
                } else if ($logic == 'lt' || $logic == '<') {
                    $match = is_numeric($values[0]) && $val < $values[0];
                } else if ($logic == 'egt' || $logic == '>=') {
                    $match = is_numeric($values[0]) && $val >= $values[0];
                } else if ($logic == 'elt' || $logic == '<=') {
                    $match = is_numeric($values[0]) && $val <= $values[0];
                } else if ($logic == 'strpos' || $logic == 'strstr') {
                    $match = strstr($val, $values[0]);
                } else if ($logic == 'not_strpos' || $logic == 'not_strstr' || $logic == '!strpos' || $logic == '!strstr') {
                    $match = !strstr($val, $values[0]);
                } else //default in_array
                {
                    $match = in_array($val, $values);
                }
                if ($match) {
                    $this->matchClass[] = $class;
                }
            } else if ($mp instanceof \Closure) {
                // 'delete' => ['hidden' => function ($data) {
                //     return $data['pay_status'] > 1;
                // }],
                if (!empty($data)) {//判断一下，避免空数据导致报错
                    $match = $mp($data);
                    if ($match) {
                        $this->matchClass[] = $class;
                    }
                }
            } else { // 'enable' => ['hidden' => '__hi_en__'],
                if (isset($data[$mp]) && $data[$mp]) {
                    $this->matchClass[] = $class;
                }
            }
        }

        return $this->matchClass;
    }

    /**
     * Undocumented function
     *
     * @param array $mapData
     * @return $this
     */
    public function mapClass($mapData)
    {
        if (!empty($mapData) && isset($mapData[$this->name])) {
            $this->mapClass = $mapData[$this->name];
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function dataId($val)
    {
        $this->dataId = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param boolean|string $confirm
     * @return $this
     */
    public function postRowid($url, $confirm = true)
    {
        $this->postRowid = (string)$url;
        $this->confirm = $confirm ? $confirm : 0;

        return $this;
    }

    protected function postRowidScript()
    {
        $btnId = $this->getId();
        $table = $this->tableId;

        $label = $this->label;

        if (!$label) {
            $label = $this->getAttrByName('title') ?: $this->getAttrByName('data-title');
        }

        $script = <<<EOT

    const {$btnId}Click = (row) => {
        let ids = row.__pk__;
        let postRowidUrl = '{$this->postRowid}';

        let confirm = '{$this->confirm}';
        if (confirm && confirm != '0' && confirm != 'false') {
            if (confirm == '1') {
                let text = '{$label}'.trim() || __blang.builder_this;
                confirm = __blang.builder_confirm_to_do_operation + ' [' + text + '] ' + __blang.builder_action_operation + ' ?';
            }
            VxpConfirm.open({
                title : __blang.builder_operation_tips,
                content: confirm,
                confirmText : __blang.builder_button_ok,
                cancelText : __blang.builder_button_cancel,
                confirmType: 'warning',
            }).then((res) => {
                if(res) {
                    {$btnId}PostRowid(postRowidUrl, ids);
                }
            });
            return false;
        }
        {$btnId}PostRowid(postRowidUrl, ids);
    };

    const {$btnId}PostRowid = (url, ids) => {
        let data = {ids};
        {$table}SendData(url, data, 1);
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

    protected function buttonScript()
    {
        $btnId = $this->getId();
        $table = $this->tableId;

        $label = $this->label;

        if (!$label) {
            $label = $this->getAttrByName('title') ?: $this->getAttrByName('data-title');
        }

        $script = <<<EOT

    const {$btnId}Click = (row) => {
        if(row.__action__.{$btnId}.href) {
            window.refreshTable = () => {
                {$table}Refresh();
            };
            layerOpenLink(row.__action__.{$btnId}.href, '{$label}', '{$this->layerSize}');
        }
        else {
            // console.log(row);
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
        if ($this->postRowid) {
            if (Builder::checkUrl($this->postRowid)) {
                $this->postRowidScript();
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

        $label = $this->parseLabel();

        $vars = array_merge($this->commonVars(), [
            'dataId' => $this->dataId,
            'label' => $this->icon && $label ? '&nbsp;' . $label : '',
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
