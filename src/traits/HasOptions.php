<?php

namespace tpext\builder\traits;

use think\Collection;
use tpext\builder\common\Builder;

trait HasOptions
{
    protected $options = [];
    /**
     * Undocumented function
     * ['key1' => $value1, 'key2' => $value2, ...]
     * @param array|Collection|\IteratorAggregate $options
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
        $arr = [];
        
        foreach ($options as $k => $v) {
            $arr[(string)$k] = $v;
        }

        return $arr;
    }

    /**
     * Undocumented function
     *
     * @param array $texts
     * @return $this
     */
    public function texts($texts)
    {
        $options = [];
        foreach ($texts as $text) {
            $options[(string)$text] = $text;
        }

        $this->options = $options;

        return $this;
    }

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
     * @param array|Collection|\IteratorAggregate $optionsData
     * @param string $textField
     * @param string $idField
     * @return $this
     */
    public function optionsData($optionsData, $textField = '', $idField = 'id')
    {
        $count = count($optionsData);

        if ($count > 2000) {
            Builder::getInstance()->notify('optionsData数据量过多(超过2000)，请使用其他方式以优化性能！', 'error');
            return $this;
        }

        if ($count > 200) {
            Builder::getInstance()->notify('optionsData数据量过多(超过200)，建议使用其他方式以优化性能！', 'warning');
        }

        $options = [];
        $keys = [];
        $replace = [];
        $arr = [];

        preg_match_all('/\{([\w\.]+)\}/', $textField, $matches);

        $needReplace = isset($matches[1]) && count($matches[1]) > 0;

        $key = '';
        foreach ($optionsData as $li) {
            if (empty($idField)) {
                $idField = $li->getPk();
            }
            if (empty($textField)) {
                $textField = isset($li['name']) ? 'name' : 'title';
            }

            $key = (string)$li[$idField];

            if ($needReplace) {

                $keys = [];
                $replace = [];

                foreach ($matches[1] as $match) {
                    $arr = explode('.', $match);
                    if (count($arr) == 1) {

                        $keys[] = '{' . $arr[0] . '}';
                        $replace[] = isset($li[$arr[0]]) ? $li[$arr[0]] : '';
                    } else if (count($arr) == 2) {

                        $keys[] = '{' . $arr[0] . '.' . $arr[1] . '}';
                        $replace[] = isset($li[$arr[0]]) && isset($li[$arr[0]][$arr[1]]) ? $li[$arr[0]][$arr[1]] : '-';
                    } else {
                        //最多支持两层 xx 或 xx.yy
                    }
                }

                $options[$key] = str_replace($keys, $replace, $textField);
            } else {
                $options[$key] = $li[$textField] ?? '-';
            }
        }
        $this->options = $options;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return $this
     */
    public function beforOptions($options)
    {
        $this->options = $options + $this->options;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return $this
     */
    public function afterOptions($options)
    {
        $this->options = $this->options + $options;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return $this
     */
    public function mergeOptions($options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @return $this
     */
    public function forget($key)
    {
        unset($this->options[$key]);

        return $this;
    }
}
