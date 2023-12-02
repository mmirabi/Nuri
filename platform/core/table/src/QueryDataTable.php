<?php

namespace Botble\Table;

use Yajra\DataTables\QueryDataTable as BaseQueryDataTable;

class QueryDataTable extends BaseQueryDataTable
{
    protected function showDebugger(array $output): array
    {
        $queryLog = $this->getConnection()->getQueryLog();
        array_walk_recursive($queryLog, function (&$item) {
            if (is_string($item) && extension_loaded('iconv')) {
                $item = iconv('iso-8859-1', 'utf-8', $item);
            }
        });

        $output['queries'] = $queryLog;
        $output['input'] = $this->request->all();

        return $output;
    }
}
