<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Machine;
use App\Model\Store;
use App\Model\StoreMachine;
use Validator;

class MachineController extends Controller
{
    public function getMachine(Request $request)
    {
        $rules = [
            'mac' => 'required',
            'player' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 400);
        }
        $mac = $request->mac;
        $player = $request->player + 1;
        
        $Machine = new Machine;
        $Store = new Store;
        $StoreMachine = new StoreMachine;
        $Machine = Machine::where('mac', '=', $mac)->first();
        $sid = $StoreMachine->where('machine_id', '=', $Machine->id)->first()->store_id;
        $name = $Store->find($sid)->name;
        $region = $Store->find($sid)->region;

        echo header("Content-type:text/html;charset=utf-8");
        echo $region.'區'.$name.$Machine->category.'-'.$player.'號玩家';
    }
}