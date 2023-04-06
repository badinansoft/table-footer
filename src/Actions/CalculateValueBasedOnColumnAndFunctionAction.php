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
        $result = $this->$function($query,$function,$column);
        return $result;
    }

    private function sum($query,$function,$column) {
        return array_sum(array_values(array_column($query->get()->toArray(),$column)));
    }

    private function min($query,$function,$column) {
        return min(array_column($query->get()->toArray(),$column));
    }

    private function max($query,$function,$column) {
        return max(array_column($query->get()->toArray(),$column));
    }

    private function avg($query,$function,$column) {
        return $this->sum($query,$function,$column) / $query->get()->count();
    }

    private function count($query,$function,$column) {
        return $query->get()->count();
    }
}
