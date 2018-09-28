<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 2018/9/27
 * Time: 下午5:52
 */

namespace App\Console\Commands;

use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Cell_IValueBinder;
use PHPExcel_Cell_DefaultValueBinder;

class CustomValueBinder extends PHPExcel_Cell_DefaultValueBinder implements PHPExcel_Cell_IValueBinder
{
    public function bindValue(PHPExcel_Cell $cell, $value = null)
    {

        if (!empty($value))
        {
            $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING2);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
}