<?php

namespace CrudBundle\View\ViewModel;

use CrudBundle\View\ViewModel;

final class TableRowViewModel extends ViewModel
{
    public function getVariable($name, $default = null)
    {
        $row = parent::getVariable('row', []);

        if (isset($row[$name])) {
            return $row[$name];
        }

        return parent::getVariable($name, $default);
    }
}
