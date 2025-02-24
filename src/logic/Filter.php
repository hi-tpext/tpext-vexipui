<?php

namespace tpext\builder\logic;

use tpext\builder\common\Search;

class Filter
{
    /**
     * Undocumented function
     *
     * @param Search $search
     * @param array $searchData
     * @return array
     */
    public function getQuery($search, $searchData)
    {
        $where = [];

        $rows = $search->getRows();

        $comumn = '';

        foreach ($rows as $row) {

            $comumn = $row->getName();

            if (isset($searchData[$comumn]) && $searchData[$comumn] !== '' && $searchData[$comumn] !== []) {

                $filter = $row->getFilter() ?: '=';

                if (is_array($searchData[$comumn])) {
                    $filter = 'in';
                }
                if ($filter == 'like') {
                    $where[] = [$comumn, $filter, "%{$searchData[$comumn]}%"];
                } else {
                    $where[] = [$comumn, $filter, $searchData[$comumn]];
                }
            }
        }

        return $where;
    }
}
