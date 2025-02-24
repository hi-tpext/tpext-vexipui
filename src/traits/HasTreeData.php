<?php

namespace tpext\builder\traits;

use think\Collection;

trait HasTreeData
{
    protected $options = [];

    protected $idKey = 'id';

    protected $disabledOptions = [];

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Undocumented function
     *
     * @param string|array $val
     * @return $this
     */
    public function disabledOptions($val)
    {
        $this->disabledOptions = $val;
        return $this;
    }

    /**
     * Undocumented function
     * 
     * @param array|Collection|\IteratorAggregate $options [[id,text,children],...]
     * @return $this
     */
    public function options($options)
    {
        if ($options instanceof Collection || $options instanceof \IteratorAggregate) {
            return $this->optionsData($options);
        }
        $this->options = $this->convertOptions($options);
        return $this;
    }


    /**
     * 数组 id 字段转字符串
     *
     * @param array $options
     * @return array
     */
    protected function convertOptions($options)
    {
        $func = function ($options) use (&$func) {
            $result = [];
            foreach ($options as $k => $v) {
                $v[$this->idKey] = (string)$v[$this->idKey];
                if (!empty($v['children'])) {
                    $v['children'] = $func($v['children']);
                }
                $result[] = $v;
            }
            return $result;
        };

        $options = $func($options);

        return $options;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection|\IteratorAggregate $treeData
     * @param string $textField
     * @param string $idField
     * @param string $pidField
     * 
     * @return $this
     */
    public function optionsData($treeData, $textField = '', $idField = 'id', $pidField = 'parent_id')
    {
        if (empty($idField)) {
            $idField = $this->idKey;
        }
        if (empty($pidField)) {
            $pidField = 'parent_id';
        }

        $tree = [];

        preg_match_all('/\{([\w\.]+)\}/', $textField, $matches);

        $needReplace = isset($matches[1]) && count($matches[1]) > 0;

        $key = '';

        foreach ($treeData as $k => $li) {

            if (!isset($li[$pidField])) { //没有pidField的表也可以做只有一层的树形
                $li[$pidField] = $li['pid'] ?? 0;
            }

            if ($li[$pidField] !== 0 && $li[$pidField] !== '') {
                continue;
            }

            unset($treeData[$k]);

            $key = (string)$li[$idField];

            if ($needReplace) {

                $keys = [];
                $replace = [];

                foreach ($matches[1] as $match) {
                    $arr = explode('.', $match);
                    if (count($arr) == 1) {

                        $keys[] = '{' . $arr[0] . '}';
                        $replace[] = isset($li[$arr[0]]) ? $li[$arr[0]] : '-';
                    } else if (count($arr) == 2) {

                        $keys[] = '{' . $arr[0] . '.' . $arr[1] . '}';
                        $replace[] = isset($li[$arr[0]]) && isset($li[$arr[0]][$arr[1]]) ? $li[$arr[0]][$arr[1]] : '-';
                    } else {
                        //最多支持两层 xx 或 xx.yy
                    }
                }
                $tree[] = [
                    'id' => $key,
                    'text' => str_replace($keys, $replace, $textField),
                    'disabled' => in_array($key, $this->disabledOptions) || $this->isReadonly() || $this->isDisabled(),
                    'children' => isset($li['children']) ? $li['children'] : $this->getChildren($treeData, $key, $textField, $idField, $pidField),
                ];
            } else {

                $tree[] = [
                    'id' => $key,
                    'text' => $li[$textField],
                    'disabled' => in_array($key, $this->disabledOptions) || $this->isReadonly() || $this->isDisabled(),
                    'children' => isset($li['children']) ? $li['children'] : $this->getChildren($treeData, $key, $textField, $idField, $pidField),
                ];
            }
        }

        $this->options = $tree;
        return $this;
    }

    protected function getChildren($treeData, $pid, $textField = 'name', $idField = 'id', $pidField = 'parent_id')
    {
        $children = [];

        preg_match_all('/\{([\w\.]+)\}/', $textField, $matches);

        $needReplace = isset($matches[1]) && count($matches[1]) > 0;

        $key = '';

        foreach ($treeData as $k => $li) {

            if ('' . $li[$pidField] === '' . $pid) {

                unset($treeData[$k]);

                $key = (string)$li[$idField];

                if ($needReplace) {

                    $keys = [];
                    $replace = [];

                    foreach ($matches[1] as $match) {
                        $arr = explode('.', $match);
                        if (count($arr) == 1) {

                            $keys[] = '{' . $arr[0] . '}';
                            $replace[] = isset($li[$arr[0]]) ? $li[$arr[0]] : '-';
                        } else if (count($arr) == 2) {

                            $keys[] = '{' . $arr[0] . '.' . $arr[1] . '}';
                            $replace[] = isset($li[$arr[0]]) && isset($li[$arr[0]][$arr[1]]) ? $li[$arr[0]][$arr[1]] : '-';
                        } else {
                            //最多支持两层 xx 或 xx.yy
                        }
                    }

                    $children[] = [
                        'id' => $key,
                        'text' => str_replace($keys, $replace, $textField),
                        'disabled' => in_array($key, $this->disabledOptions) || $this->isReadonly() || $this->isDisabled(),
                        'children' => isset($li['children']) ? $li['children'] : $this->getChildren($treeData, $key, $textField, $idField, $pidField),
                    ];
                } else {

                    $children[] = [
                        'id' => $key,
                        'text' => $li[$textField],
                        'disabled' => in_array($key, $this->disabledOptions) || $this->isReadonly() || $this->isDisabled(),
                        'children' => isset($li['children']) ? $li['children'] : $this->getChildren($treeData, $key, $textField, $idField, $pidField),
                    ];
                }
            }
        }

        return $children;
    }
}
