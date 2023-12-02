<?php

namespace Botble\Table\Columns;

use BackedEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Enum;
use Botble\Table\Contracts\FormattedColumn;

class EnumColumn extends Column implements FormattedColumn
{
    public static function make(array|string $data = [], string $name = ''): static
    {
        return parent::make($data, $name)
            ->alignCenter()
            ->width(100)
            ->renderUsing(function (EnumColumn $column, $value) {
                return $column->editedFormat($value);
            });
    }

    public function editedFormat($value): string
    {
        if (! $value instanceof Enum && ! $value instanceof BackedEnum) {
            return '';
        }

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        $table = $this->getTable();

        if ($table->isExportingToExcel() || $table->isExportingToCSV()) {
            return $value->getValue();
        }

        $value = $value->toHtml() ?: $value->getValue();

        return BaseHelper::clean($value);
    }
}
