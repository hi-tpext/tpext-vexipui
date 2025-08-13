<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasTreeData;

class Tree extends Field
{
    use HasTreeData;
    protected $view = 'tree';

    protected $multiple = true;

    protected $valueType = 'array';

    protected $maxHeight = 400;

    protected $expandAll = true;

    protected $postAsString = false;

    /**
     * Undocumented variable
     *
     * @var array|string
     */
    protected $checked = [];

    protected $jsOptions = [
        'accordion' => false, //是否为手风琴模式，每次只打开一个同级树节点展开
        'floor-select' => false, //开启后，当选择存在下级的节点时，会触发节点的展开收起，无下级时才会触发选择取消事件
        'indent' => '16px', //节点缩进距离
        'no-cascaded' => true, //使父子节点能被独立勾选
        'use-y-bar' => false, //设置树是否使用纵向滚动条
        'virtual' => false, //是否开启虚拟滚动
        'link-line' => 'dashed', //是否连接线
        'key-config' => [
            'id' => 'id',
            'label' => 'text',
            'children' => 'children',
            'disabled' => 'disabled',
            'selected' => 'selected',
            'expanded' => 'expanded',
            'checked' => 'checked',
        ],
        'root-id' => '',
        'no-build-tree' => true, //直接传染树形数据，不再构建树结构
        'block-effect' => true, //是否开启块级效果
        'multiple' => false, //是否多选
    ];

    /**
     * Undocumented function
     *
     * @param array|string|int $val
     * @return $this
     */
    public function default($val = [])
    {
        $this->default = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param bool $val
     * @return $this
     */
    public function multiple($val = true)
    {
        $this->multiple = $val;
        $this->valueType = $val ? 'array' : 'string';
        return $this;
    }

    /**
     * 多选时，是否父子节点不级联
     * @param bool $val
     * @return $this
     */
    public function noCascaded($val = true)
    {
        $this->jsOptions['no-cascaded'] = $val;

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
     * @param integer|string $val
     * @return $this
     */
    public function maxHeight($val = 800)
    {
        $this->maxHeight = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function expandAll($val = true)
    {
        $this->expandAll = $val;
        return $this;
    }

    protected function treeOptions()
    {
        $value = $this->renderValue();
        $options = $this->getChildren($this->options, $value);
        return $options;
    }

    protected function getChildren($arr, $value)
    {
        $options = [];
        foreach ($arr as $data) {
            $options[] = [
                'id' => $data['id'],
                'text' => $data['text'],
                'disabled' => in_array($data['id'], $this->disabledOptions) || $this->readonly,
                'selected' => !$this->multiple && $data['id'] == $value,
                'checked' => $this->multiple && in_array($data['id'], $value),
                'expanded' => $this->expandAll ? true : false,
                'children' => $this->getChildren($data['children'] ?? [], $value),
                'checkDisabled' => !$this->multiple,
            ];
        }
        return $options;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function fieldScript()
    {
        if ($this->maxHeight) {
            $this->addStyle('max-height:' . $this->maxHeight . 'px');
            $this->jsOptions['virtual'] = true;
        }

        $fieldId = $this->getId();
        $VModel = $this->getVModel();

        if ($this->multiple) {
            $this->jsOptions['multiple'] = true;
            $this->jsOptions['checkbox'] = true;
        } else {
            $this->jsOptions['multiple'] = false;
            $this->jsOptions['checkbox'] = false;
            $this->jsOptions['floor-select'] = false;
            $this->jsOptions['block-effect'] = true;
        }

        $options = json_encode($this->treeOptions(), JSON_UNESCAPED_UNICODE);

        $multiple = $this->multiple ? 'true' : 'false';

        $script = <<<EOT

    const {$fieldId}Ref = ref(null);
    const {$fieldId}Options = ref({$options});
    const {$fieldId}Multiple = {$multiple};

    const {$fieldId}NodeChange = (data, node) => {
        if({$fieldId}Multiple) {
            {$VModel} = ({$fieldId}Ref.value.getCheckedNodeData() || []).map(item => item.id);
            {$VModel}__tmp = {$VModel}.join(',');
        } else {
            if(!node.selected) {//点击已选的会取消选择，保证始终有一个节点被选中，重新设置选中
                {$fieldId}Ref.value.selectNodeByData(data, true);
            }
            {$VModel} = data.id;
            {$VModel}__tmp = {$VModel};
        }
    };

EOT;
        $this->setupScript[] = $script;
        $this->addVueToken([
            "{$fieldId}Ref",
            "{$fieldId}Options",
            "{$fieldId}NodeChange",
        ]);

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
