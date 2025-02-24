<?php

namespace tpext\builder\traits;

trait TreeModel
{
    /**
     * 默认查询条件
     *
     * @var array
     */
    protected $treeScope = []; //如 [['enable', 'eq', 1]]

    /**
     * 根一级的id
     *
     * @var integer
     */
    protected $treeRootId = 0;

    /**
     * 树 Text 字段 如 'name'
     *
     * @var string
     */
    protected $treeTextField = 'name';

    /**
     * 树 id 字段
     *
     * @var string
     */
    protected $treeIdField = 'id';

    /**
     * 树 上级id字段 如 parend_id pid
     *
     * @var string
     */
    protected $treeParentIdField = 'parent_id';

    /**
     * 排序字段　如 sort
     *
     * @var string
     */
    protected $treeSortField = '';

    /**
     * 多维结构数据
     *
     * @var array
     */
    protected $treeData = [];

    /**
     * 一维结构数据
     *
     * @var array
     */
    protected $lineData = [];

    /**
     * 一维结构key:value数据
     *
     * @var array
     */
    protected $optionsData = [];

    protected $lineType = 0;

    protected $except = [];

    protected $allTreeData;

    protected $asTreeList = true;

    protected $treeCacheKey = ''; //避免和 thinkorm-model中的属性[cacheKey]冲突

    /**
     * Undocumented variable
     *
     * @var int|boolean
     */
    protected $chacheTime = false; //按需调整时间，并自行处理修改后清除缓存

    /**
     * 获取是否显示为树行
     *
     * @return boolean
     */
    public function asTreeList()
    {
        return $this->asTreeList;
    }

    public function getTreeCacheKey()
    {
        return $this->treeCacheKey ?: 'tree_data_' . $this->getName();
    }

    /**
     * 设置是否显示为树行
     *
     * @param boolean $asTreeList
     * @return $this;
     */
    public function setAsTreeList($asTreeList = true)
    {
        $this->asTreeList = $asTreeList;

        return $this;
    }

    /**
     * 初始化
     *
     * @return void
     */
    protected function treeInit()
    {
        //如
        // $this->treeScope = [['enable', 'eq', 1]];
        // $this->treeTextField = 'title';
        // $this->treeIdField = 'id';
        // $this->treeParentIdField = 'pid';
        // $this->treeSortField ='sort';
    }

    /**
     * 重新初始化参数
     *
     * @param array $initData
     * @return $this
     */
    public function reInit($initData)
    {
        foreach ($initData as $key => $value) {
            $this->setOption($key, $value);
        }

        return $this;
    }

    /**
     * 判断这个$key 是不是我的成员属性，如果是，则设置
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setOption($key, $value)
    {
        //得到所有的成员属性
        $keys = array_keys(get_class_vars(__CLASS__));

        if (in_array($key, $keys)) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @return array|string
     */
    public function getOption($key = '')
    {
        return $this->$key ?? '';
    }

    /**
     * 获取1维结构数据 text当&nbsp;(适合放列表页面)
     *
     * @param array $except
     * @return array
     */
    public function getLineData($except = [])
    {
        $this->treeInit();
        $this->lineType = 1;
        $this->except = is_array($except) ? $except : [$except];

        if (!empty($this->lineData)) {
            return $this->lineData;
        }

        $this->lineData = [];
        $this->builderData();

        return $this->lineData;
    }

    /**
     * 获取1维结构数据 (适合放列select)
     *
     * @param array $except
     * @return array
     */
    public function getOptionsData($except = [])
    {
        $this->treeInit();
        $this->lineType = 2;
        $this->except = is_array($except) ? $except : [$except];

        if (!empty($this->optionsData)) {
            return $this->optionsData;
        }

        if (empty($this->lineData)) {
            $this->lineData = [];
            $this->builderData();
        }

        $this->optionsData = [];
        foreach ($this->lineData as $d) {
            $this->optionsData[$d['__id__']] = $d['__text__'];
        }

        return $this->optionsData;
    }

    /**
     * 获取多维结构数据
     *
     * @param array $except
     * @return array
     */
    public function getTreeData($except = [])
    {
        $this->treeInit();
        $this->lineType = 0;
        $this->except = is_array($except) ? $except : [$except];

        if (!empty($this->treeData)) {
            return $this->treeData;
        }

        $this->treeData = [];
        $this->builderData();

        return $this->treeData;
    }

    public function getAllData()
    {
        $this->treeInit();

        if ($this->chacheTime !== false) {
            $this->allTreeData = $this->where($this->treeScope)->order($this->treeSortField)->cache($this->getTreeCacheKey(), $this->chacheTime)->select();
        } else {
            $this->allTreeData = $this->where($this->treeScope)->order($this->treeSortField)->select();
        }

        return $this->allTreeData;
    }

    protected function builderData()
    {
        $this->getAllData();

        $roots = [];

        foreach ($this->allTreeData as $k => $d) {

            if ('' . $d[$this->treeParentIdField] !== '' . $this->treeRootId) {
                continue;
            }

            if (!empty($this->except) && in_array($d[$this->treeIdField], $this->except)) {
                continue;
            }

            $roots[] = $d;

            unset($this->allTreeData[$k]);
        }

        unset($d);

        foreach ($roots as $d) {

            $this->treeData[] = $d;
            $this->lineData[] = $d;

            $d['__deep__'] = 0;
            $d['__id__'] = $d[$this->treeIdField];
            $d['__text__'] = $d[$this->treeTextField];
            $d['__children__'] = $this->getChildrenData($d[$this->treeIdField]);
        }
    }

    /**
     * Undocumented function
     *
     * @param integer $pid
     * @param integer $deep
     * @return array
     */
    protected function getChildrenData($pid, $deep = 1)
    {

        $data = [];
        $deep += 1;

        foreach ($this->allTreeData as $k => $d) {

            if (!empty($this->except) && in_array($d[$this->treeIdField], $this->except)) {
                continue;
            }

            if ('' . $d[$this->treeParentIdField] === '' . $pid) {

                $data[] = $d;

                unset($this->allTreeData[$k]);
            }
        }

        $children = [];

        foreach ($data as $d) {
            $d['__deep__'] = $deep;
            $d['__id__'] = $d[$this->treeIdField];

            if ($this->lineType) {
                if ($this->lineType == 1) {
                    $d['__text__'] = str_repeat('&nbsp;', ($deep - 1) * 5) . '├─' . $d[$this->treeTextField];
                } else {
                    $d['__text__'] = str_repeat('──', ($deep - 1)) . '├─' . $d[$this->treeTextField];
                }

                $this->lineData[] = $d;
                $this->getChildrenData($d[$this->treeIdField], $deep);
                continue;
            }

            $d['__text__'] = $d[$this->treeTextField];
            $d['__children__'] = $this->getChildrenData($d[$this->treeIdField], $deep);

            $children[] = $d;
        }

        return $children;
    }
}
