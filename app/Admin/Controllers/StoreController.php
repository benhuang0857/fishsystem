<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\UserGender;

use App\Model\Store;
use App\Model\Machine;
use App\Model\FishData;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
Use Encore\Admin\Admin;
use App\Model\HandOverHistory;
use Encore\Admin\Widgets\Table;

class StoreController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '店家列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Store());

        $grid->model()->orderBy('updated_at', 'DESC');

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->equal('region', __('店家區域'))->select([
                'A' => 'A',
                'B' => 'B',
                'C' => 'C',
                'D' => 'D',
                'E' => 'E',
                'F' => 'F',
            ]);
            $filter->equal('name', __('店家名稱'));
        });

        $grid->column('name', __('店家名稱'))->expand(function () {

            $Machine = $this->Machine()->get();
            $Machines = $Machine->map(function ($Machine) {
                $mac = $Machine['mac'];
                $category = $Machine['category'];

                /*
                //fish and fish2
                $total_bet = $Machine->PlayerData->where('num', '<=', 5)->reduce(function($current, $item){
                    return $current + $item->bet;
                }, 0);
                //fish and fish2
                $total_credits = $Machine->PlayerData->where('num', '<=', 5)->reduce(function($current, $item){
                    return $current + $item->credits;
                }, 0);
                */

                //fish3
                $total_bet = $Machine->PlayerData->reduce(function($current, $item){
                    return $current + $item->bet;
                }, 0);
                //fish3
                $total_credits = $Machine->PlayerData->reduce(function($current, $item){
                    return $current + $item->credits;
                }, 0);

                $total_income = FishData::where('mac', $mac)->first()->income;
                $total_payout = FishData::where('mac', $mac)->first()->payout;
                $total_earn = ($total_income-$total_payout)*4;
                $created_at = $Machine['created_at'];
                return [$mac, $category, $total_bet, $total_credits, $total_income, $total_payout, $total_earn, $created_at];
            });
        
            return new Table(['mac','機種', '總押分', '總餘分', '總收入(幣)', '總支出(幣)', '機台營收(台幣)', '建立時間', '未交班機台營收'], $Machines->toArray());
        });

        $grid->column('region', __('店家區域'));
        $grid->column('store_machine_num', __('店家機器數量'))->display(function(){
            return $this->machine->isNotEmpty()?count($this->machine):'';
        });
        $grid->column('store_income', __('店家收入(幣)'))->display(function(){
            $store = Store::find($this->id);
            return isset($this->id) ? $store->Machine->reduce(function($current, $item){
                return $current + $item->fishData->income;
            }, 0) : '';
        });
        $grid->column('store_payout', __('店家支出(幣)'))->display(function(){
            $store = Store::find($this->id);
            return isset($this->id) ? $store->Machine->reduce(function($current, $item){
                return $current + $item->fishData->payout;
            }, 0) : '';
        });
        $grid->column('store_earn', __('店家淨利(台幣)'))->display(function(){
            $store = Store::find($this->id);
            if(isset($this->id))
            {
                $income = $store->Machine->reduce(function($current, $item){
                    return $current + $item->fishData->income;
                }, 0);
                $payout = $store->Machine->reduce(function($current, $item){
                    return $current + $item->fishData->payout;
                }, 0);

                return ($income - $payout)*4;
            }
        });
        $grid->column('h', __('未交班店家營收'));
        $grid->column('created_at', __('建立時間'));
        //$grid->column('updated_at', __('更新時間'));

        $grid->column('hand_over', __('建立交班紀錄'))->display(function(){
            $store = Store::find($this->id);
            if(isset($this->id))
            {
                $income = $store->Machine->reduce(function($current, $item){
                    return $current + $item->fishData->income;
                }, 0);
                $payout = $store->Machine->reduce(function($current, $item){
                    return $current + $item->fishData->payout;
                }, 0);
            }
            return '<a href="'.url('/admin/store/create-hand-over-histories-db/'.$this->id.'/'.$income.'/'.$payout).'">建立交班紀錄</a>';
        });

        /**
         * 自訂工具
         */
        /*
        $grid->tools(function ($tools) {
            $tools->append(new UserGender());
        });
        */

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Store::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('region', __('Region'));
        $show->field('machine_list', __('Machine list'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Store());

        $data = Machine::pluck('mac', 'id');

        $form->text('name', '店家名稱');
        $form->select('region', __('店家區域'))->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F']);
        $form->multipleSelect("machine", '店家機台Mac')->options($data);

        return $form;
    }

    public function CreateHandOverHistoriesDB($id, $income, $payout)
    {
        HandOverHistory::create([
            'store_id' => $id,
            'income' => $income,
            'payout' => $payout
        ]);

        return redirect(admin_url('store'));
    }
}
