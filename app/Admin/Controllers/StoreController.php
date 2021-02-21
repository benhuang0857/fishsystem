<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\UserGender;

use App\Model\Store;
use App\Model\Machine;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
Use Admin;
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
        $this->permission($grid);

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
                $total_bet = $Machine->PlayerData->reduce(function($current, $item){
                    return $current + $item->bet;
                }, 0);
                $total_credits = $Machine->PlayerData->reduce(function($current, $item){
                    return $current + $item->credits;
                }, 0);
                $total_earn = $total_credits/500;
                $created_at = $Machine['created_at'];
                return [$mac, $category, $total_bet, $total_credits, $total_earn, $created_at];
            });
        
            return new Table(['mac','機種', '總累積投幣數', '總累積分數', '總營業額','建立時間'], $Machines->toArray());
        });

        $grid->column('region', __('店家區域'));
        $grid->column('store_machine_num', __('店家機器數量'))->display(function(){
            return $this->machine->isNotEmpty()?count($this->machine):'';
        });
        $grid->column('store_income', __('店家收入'))->display(function(){
            $income = 0;
            $this->Machine->each(function ($machine) use (&$income) {
                $machine->fishData->each(function ($fish) use (&$income) {
                    $income += $fish->income;
                });
            });
            return $income;
        });
        $grid->column('store_payout', __('店家支出'))->display(function(){
            $payout = 0;
            $this->Machine->each(function ($machine) use (&$payout) {
                $machine->fishData->each(function ($fish) use (&$payout) {
                    $payout += $fish->payout;
                });
            });
            return $payout;
        });
        $grid->column('store_earn', __('店家淨利'))->display(function(){
            $payout = 0;
            $income = 0;
            $this->Machine->each(function ($machine) use (&$payout, &$income) {
                $machine->fishData->each(function ($fish) use (&$payout, &$income) {
                    $payout += $fish->payout;
                    $income += $fish->income;
                });
            });
            return $income - $payout;
        });
        $grid->column('created_at', __('建立時間'));
        $grid->column('updated_at', __('更新時間'));

        $grid->column('hand_over', __('建立交班紀錄'))->display(function(){
            return '<a href="'.url('/admin/store/create-hand-over-histories-db/'.$this->id).'">建立交班紀錄</a>';
        })->totalRow('<a href="'.url('/admin/store/create-hand-over-histories-db/all').'">建立所有交班紀錄</a>');

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

    public function CreateHandOverHistoriesDB($id)
    {
        // 店家
        $store = Store::find($id);
        // 該店家最後一筆交班資料
        $last_handover = $store->handOverRecords->sortByDesc('id')->first();
        // 是否為第一筆資料
        $is_first_handover = is_null($last_handover);
        // 過早交班驗證
        if (!$is_first_handover) {
            // 過早交班，該店家最後交班時間低於 6 小時
            $is_too_early = 60 * 60 * 6 > time() - $last_handover->updated_at->timestamp;
            if ($is_too_early) {
                return redirect(admin_url('store'));
            }
        }
        // 該店家擁有之機器
        $machines = $store->Machine;
        // 該班總收入
        $income = 0;
        // 該班總支出
        $payout = 0;
        // 針對該店家所有機器的魚機資料
        $fish_data = $machines->each(function ($machine) use ($is_first_handover, $last_handover, &$income, &$payout) {
            if ($is_first_handover) {
                // 該店的第一次交班紀錄，紀錄目前所有資料
                $fish_data = $machine->fishData;
            } else {
                // 篩選魚機紀錄，該紀錄更新時間大於最後一筆交班時間
                $fish_data = $machine->fishData()->where('update_time', '>', $last_handover->updated_at)->get();
            }
            // 計算加總魚機收支狀況
            $fish_data->each(function ($fish) use (&$income, &$payout) {
                $income += $fish->income;
                $payout += $fish->payout;
            });
        });
        HandOverHistory::create([
            'store_id' => $id,
            'income' => $income,
            'payout' => $payout
        ]);
        return redirect(admin_url('store'));
    }

    public function CreateAllHandOverHistoriesDB()
    {
        // 只有管理員能夠交班全部
        if (Admin::user()->isAdministrator()) {
            $stores = Store::all();
            $stores->each(function ($store) {
                $this->CreateHandOverHistoriesDB($store->id);
            });
            return redirect(admin_url('store'));
        }
    }
    
    /**
     * 權限控制
     */
    public function permission($grid)
    {
        if (Admin::user()->isAdministrator()) {
            $grid;
        } else {
            $grid->model()->where('id', 1);
        }
    }
}
