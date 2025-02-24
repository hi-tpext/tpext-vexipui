<?php

namespace tpext\builder\common;

class SizeAdapter extends Widget
{
    protected static $instance = null;
    /**
     * 创建自身
     *
     * @param mixed $arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        if (!static::$instance) {
            static::$instance = self::makeWidget('SizeAdapter', $arguments);
        }

        return static::$instance;
    }

    /**
     * Undocumented function
     *
     * @param string $cloSize
     * @return string
     */
    public function adjustColSize($cloSize = '')
    {
        if (is_numeric($cloSize)) {
            $col = $cloSize;
            if ($col == 0) {
                //
            } else if ($col == 12) {
                $cloSize .= " col-xl-12 col-lg-12 col-sm-12 col-xs-12";
            } else if ($col <= 3) {
                $cloSize .= " col-xl-{$col} col-lg-{$col} col-sm-4 col-xs-12";
            } else if ($col <= 4) {
                $cloSize .= " col-xl-{$col} col-lg-{$col} col-sm-6 col-xs-12";
            } else {
                $cloSize .= " col-xl-{$col} col-lg-{$col} col-sm-12 col-xs-12";
            }
        } else {
            if (preg_match('/^(\d{1,2})\s*/', $cloSize, $mch)) {
                $col = $mch[1];
                if ($col == 0) {
                    //
                } else if ($col == 12) {
                    if (!strstr($cloSize, 'col-xl-')) {
                        $cloSize .= ' col-xl-12';
                    }
                    if (!strstr($cloSize, 'col-lg-')) {
                        $cloSize .= ' col-lg-12';
                    }
                    if (!strstr($cloSize, 'col-sm-')) {
                        $cloSize .= ' col-sm-12';
                    }
                    if (!strstr($cloSize, 'col-xs-')) {
                        $cloSize .= ' col-xs-12';
                    }
                } else if ($col <= 3) {
                    if (!strstr($cloSize, 'col-xl-')) {
                        $cloSize .= ' col-xl-' . $col;
                    }
                    if (!strstr($cloSize, 'col-lg-')) {
                        $cloSize .= ' col-lg-' . $col;
                    }
                    if (!strstr($cloSize, 'col-sm-')) {
                        $cloSize .= ' col-sm-4';
                    }
                    if (!strstr($cloSize, 'col-xs-')) {
                        $cloSize .= ' col-xs-12';
                    }
                } else if ($col <= 4) {
                    if (!strstr($cloSize, 'col-xl-')) {
                        $cloSize .= ' col-xl-' . $col;
                    }
                    if (!strstr($cloSize, 'col-lg-')) {
                        $cloSize .= ' col-lg-' . $col;
                    }
                    if (!strstr($cloSize, 'col-sm-')) {
                        $cloSize .= ' col-sm-6';
                    }
                    if (!strstr($cloSize, 'col-xs-')) {
                        $cloSize .= ' col-xs-12';
                    }
                } else {
                    if (!strstr($cloSize, 'col-xl-')) {
                        $cloSize .= ' col-xl-' . $col;
                    }
                    if (!strstr($cloSize, 'col-lg-')) {
                        $cloSize .= ' col-lg-' . $col;
                    }
                    if (!strstr($cloSize, 'col-sm-')) {
                        $cloSize .= ' col-sm-12';
                    }
                    if (!strstr($cloSize, 'col-xs-')) {
                        $cloSize .= ' col-xs-12';
                    }
                }
            } else {
                if (!strstr($cloSize, 'col-xl-')) {
                    $cloSize .= ' col-xl-2';
                }
                if (!strstr($cloSize, 'col-lg-')) {
                    $cloSize .= ' col-lg-2';
                }
                if (!strstr($cloSize, 'col-md-')) {
                    $cloSize .= ' col-md-2';
                }
                if (!strstr($cloSize, 'col-sm-')) {
                    $cloSize .= ' col-sm-6';
                }
                if (!strstr($cloSize, 'col-xs-')) {
                    $cloSize .= ' col-xs-12';
                }
            }
        }
        return $cloSize;
    }

    /**
     * Undocumented function
     *
     * @param array $size
     * @return array
     */
    public function adjustDisplayerSize($size = [2, 8])
    {
        $label = $element = '';

        if (is_numeric($size[0])) {
            $label = $size[0];
            if ($size[0] == 0) {
                //
            } else if ($size[0] == 12) {
                $size[0] .= " col-xl-12 col-lg-12 col-sm-12 col-xs-12";
            } else if ($size[0] <= 3) {
                $size[0] .= " col-xl-{$size[0]} col-lg-{$size[0]} col-sm-3 col-xs-12";
            } else if ($size[0] <= 4) {
                $size[0] .= " col-xl-{$size[0]} col-lg-{$size[0]} col-sm-4 col-xs-12";
            } else {
                $size[0] .= " col-xl-{$size[0]} col-lg-{$size[0]} col-sm-12 col-xs-12";
            }
        } else {
            if (preg_match('/^([\d]{1,2})\s*/', $size[0], $mch)) {
                $label = intval($mch[1]);
                if (!strstr($size[0], 'col-xl-')) {
                    $size[0] .= ' col-xl-' . $label;
                }
                if (!strstr($size[0], 'col-lg-')) {
                    $size[0] .= ' col-lg-' . $label;
                }
                if (!strstr($size[0], 'col-sm-')) {
                    $size[0] .= ' col-sm-4';
                }
                if (!strstr($size[0], 'col-xs-')) {
                    $size[0] .= ' col-xs-12';
                }
            } else {
                if (!strstr($size[0], 'col-xl-')) {
                    $size[0] .= ' col-xl-2';
                }
                if (!strstr($size[0], 'col-lg-')) {
                    $size[0] .= ' col-lg-2';
                }
                if (!strstr($size[0], 'col-md-')) {
                    $size[0] .= ' col-md-2';
                }
                if (!strstr($size[0], 'col-sm-')) {
                    $size[0] .= ' col-sm-4';
                }
                if (!strstr($size[0], 'col-xs-')) {
                    $size[0] .= ' col-xs-12';
                }
            }
        }

        if (is_numeric($size[1])) {
            $element = $size[1];

            if ($element == 0) {
                //
            } else if ($element == 12) {
                $size[1] .= " col-xl-12 col-lg-12 col-sm-12 col-xs-12";
            } else {
                if (is_numeric($label)) {
                    if ($label == 0) {
                        $size[1] .= " col-xl-{$size[1]} col-lg-{$size[1]} col-sm-12 col-xs-12";
                    } else if ($label <= 3) {
                        $size[1] .= " col-xl-{$size[1]} col-lg-{$size[1]} col-sm-9 col-xs-12";
                    } else if ($label <= 4) {
                        $size[1] .= " col-xl-{$size[1]} col-lg-{$size[1]} col-sm-8 col-xs-12";
                    } else {
                        $size[1] .= " col-xl-{$size[1]} col-lg-{$size[1]} col-sm-12 col-xs-12";
                    }
                } else {
                    if ($element <= 4) {
                        $size[1] .= " col-xl-{$size[1]} col-lg-{$size[1]} col-sm-8 col-xs-12";
                    } else {
                        $size[1] .= " col-xl-{$size[1]} col-lg-{$size[1]} col-sm-12 col-xs-12";
                    }
                }
            }
        } else {

            if (!preg_match('/^([\d]{1,2})\s*/', $size[1], $mch)) {
                if (!strstr($size[1], 'col-md-')) {
                    $size[1] .= ' col-md-8';
                }
            }
            if (!strstr($size[1], 'col-xl-')) {
                $size[1] .= ' col-xl-8';
            }
            if (!strstr($size[1], 'col-lg-')) {
                $size[1] .= ' col-lg-8';
            }
            if (!strstr($size[1], 'col-sm-')) {
                $size[1] .= ' col-sm-8';
            }
            if (!strstr($size[1], 'col-xs-')) {
                $size[1] .= ' col-xs-12';
            }
        }
        return $size;
    }

    /**
     * Undocumented function
     *
     * @param string $class
     * @return string
     */
    public function getColSizeAttrFromColClass($class)
    {
        if (empty($class)) {
            return '';
        }
        if (is_numeric($class)) {
            return ':md="' . ($class * 2) . '"';
        }
        if (preg_match('/^\d/', $class)) {
            $class = 'col-md-' . $class;
        }

        $attr = [];
        preg_match_all('/\bcol\-(xs|sm|md|lg|xl)\-(\d+)\b/i', $class, $matches);

        if (isset($matches[1]) && count($matches[1]) > 0) {
            foreach ($matches[1] as $key => $size) {
                $attr[] = ':' . $size . '="' . ($matches[2][$key] * 2) . '"';
            }
        }
        return implode(' ', $attr);
    }
}
