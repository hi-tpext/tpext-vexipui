<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasWhen;
use tpext\builder\traits\HasTreeData;

class SelectTree extends Field
{
    use HasTreeData;
    use HasWhen;
    protected $view = 'selecttree';

    protected $multiple = false;

    protected $placeholder = '';

    protected $checked = '';

    protected $postAsString = false;

    protected $selectOptions = [];

    protected $jsOptions = [
        'clearable' => false,
        'filter' => false,
        'remote' => false,
        'key-config' => [
            'value' => 'value',
            'label' => 'label',
            'disabled' => 'disabled',
            'divided' => 'divided',
            'group' => 'group',
            'children' => 'children',
        ],
        'multiple' => false,//开启多选模式
        'count-limit' => 0,//多选时限制最大的可选数量，为 0 时不限制
        'max-tag-count' => 0,//在多选模式下，设置显示的最大标签数，为 0 时会动态计算以确保在一行内显示
    ];

    //内置树的配置
    protected $treeJsOptions = [
        'accordion' => false, //是否为手风琴模式，每次只打开一个同级树节点展开
        'floor-select' => false, //开启后，当选择存在下级的节点时，会触发节点的展开收起，无下级时才会触发选择取消事件
        'check-strictly' => false, //[多选]是否严格的遵循父子节点不互相关联的原则，设为true后父节点选中状态会影响子节点的选中状态。
        'indent' => '16px', //节点缩进距离
        'no-cascaded' => true, //使父子节点能被独立勾选
        'use-y-bar' => false, //设置树是否使用纵向滚动条
        'virtual' => true, //是否开启虚拟滚动
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
        'no-build-tree' => true,//直接传染树形数据，不再构建树结构
        'block-effect' => true, //是否开启块级效果
        'multiple' => false,
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

    /**
     * Undocumented function
     *
     * @param string|array|int $val
     * @return $this
     */
    public function default($val = '')
    {
        $this->default = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return $this
     */
    public function treeJsOptions($options)
    {
        $this->treeJsOptions = array_merge($this->treeJsOptions, $options);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getTreeJsOptions()
    {
        return $this->treeJsOptions;
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
        $this->jsOptions['multiple'] = $val;
        $this->treeJsOptions['multiple'] = $val;
        return $this;
    }

    protected function treeOptions()
    {
        $value = $this->renderValue();
        $this->selectOptions = [];
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
                'expanded' => true,
                'children' => $this->getChildren($data['children'] ?? [], $value),
                'checkDisabled' => !$this->multiple,
            ];
            $this->selectOptions[] = [
                'value' => (string) $data['id'],
                'label' => $data['text'],
                'disabled' => true,
            ];
        }
        return $options;
    }

    protected function fieldScript()
    {
        $fieldId = $this->getId();

        $treeOptions = json_encode($this->treeOptions(), JSON_UNESCAPED_UNICODE);
        $options = json_encode($this->selectOptions, JSON_UNESCAPED_UNICODE);

        $fieldId = $this->getId();
        $VModel = $this->getVModel();

        if ($this->multiple) {
            $this->treeJsOptions['multiple'] = true;
            $this->treeJsOptions['checkbox'] = true;
        } else {
            $this->treeJsOptions['multiple'] = false;
            $this->treeJsOptions['checkbox'] = false;
            $this->treeJsOptions['floor-select'] = false;
            $this->treeJsOptions['block-effect'] = true;
        }

        $multiple = $this->multiple ? 'true' : 'false';
        $treeOp = json_encode($this->treeJsOptions, JSON_UNESCAPED_UNICODE);

        $script = <<<EOT

        const {$fieldId}TreeRef = ref(null);
        const {$fieldId}Options = ref({$options});
        const {$fieldId}TreeOptions = ref({$treeOptions});
        const {$fieldId}TreeOp = ref({$treeOp});
        const {$fieldId}Multiple = {$multiple};
        const {$fieldId}Visible = ref(false);
    
        const {$fieldId}NodeChange = (data, node) => {
            if({$fieldId}Multiple) {
                {$VModel} = ({$fieldId}TreeRef.value.getCheckedNodeData() || []).map(item => item.id);
            } else {
                if(!node.selected) {//点击已选的会取消选择，保证始终有一个节点被选中，重新设置选中
                    {$fieldId}TreeRef.value.selectNodeByData(data, true);
                }
                {$VModel} = data.id;
                {$fieldId}Visible.value = false;
            }
        };

        const {$fieldId}Cancel = (value, data) => {
            let dd = findNode(data.value, {$fieldId}TreeOptions.value);
            if(dd) {
                {$fieldId}TreeRef.value.checkNodeByData(dd, false);
                setTimeout(() => {
                    {$VModel} = ({$fieldId}TreeRef.value.getCheckedNodeData() || []).map(item => item.id);
                }, 100);
            }
        };

        const findNode = (id, children) => {
            for(let i in children) {
                if(children[i].id == id) {
                    return children[i];
                } else if(children[i].children) {
                    let node = findNode(id, children[i].children);
                    if(node) {
                        return node;
                    }
                }
            }
            return null;
        };
    
EOT;
        $this->setupScript[] = $script;
        $this->addVueToken([
            "{$fieldId}TreeRef",
            "{$fieldId}Options",
            "{$fieldId}TreeOptions",
            "{$fieldId}TreeOp",
            "{$fieldId}NodeChange",
            "{$fieldId}Cancel",
            "{$fieldId}Visible",
        ]);

        $this->whenScript();
    }

    public function customVars()
    {
        return [
            'placeholder' => $this->placeholder ?: __blang('bilder_please_select') . $this->label
        ];
    }
}
