<?php
/**
 * This file is part of the Table Footer package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @license MIT License
 * @author Shahab Zebari
 * @orginization BadinanSoft
 */


namespace Badinansoft\TableFooter\Http\Controller;

use App\Http\Controllers\Controller;
use Badinansoft\TableFooter\Actions\CalculateValueBasedOnColumnAndFunctionAction;
use Badinansoft\TableFooter\Http\Request\CalculationRequest;
use Illuminate\Http\JsonResponse;

class CalculateController extends Controller
{
    /**
     * @param CalculationRequest $request
     * @return JsonResponse
     */
    public function calculate(CalculationRequest $request): JsonResponse
    {
        $result = [];
        if ($request->get('calculate')!==null) {
            $action = new CalculateValueBasedOnColumnAndFunctionAction();
            foreach ($request->get('calculate') as $value){
                $value = json_decode($value,true);
                if (isset($value['indexName']) && isset($value['calculate'])){
                    $result[$value['indexName']] = $action($request->toQuery(),$value['indexName'],$value['calculate']);
                }
            }
        }
        return response()->json($result);
    }
}
