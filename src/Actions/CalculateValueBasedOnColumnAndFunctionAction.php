<?php
/**
 * This file is part of the Table Footer package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @license MIT License
 * @author Shahab Zebari
 * @orginization BadinanSoft
 */



namespace Badinansoft\TableFooter\Actions;


class CalculateValueBasedOnColumnAndFunctionAction
{
    /**
     * @param $query
     * @param $column
     * @param $function
     * @return string
     */
    public function __invoke($query,$column,$function):string
    {
        return number_format($query->{$function}($column) )?? 0;
    }

}
