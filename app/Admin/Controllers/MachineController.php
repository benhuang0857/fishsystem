<?php

namespace App\Admin\Controllers;

use App\Model\Machine;
use App\Model\FishData;
use App\Model\JackpotHistory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Admin;

class MachineController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '機台列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Machine());

        $grid->model()->orderBy('updated_at', 'DESC');

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->equal('Store.region', __('店家區域'))->select([
                'A' => 'A',
                'B' => 'B',
                'C' => 'C',
                'D' => 'D',
                'E' => 'E',
                'F' => 'F',
            ]);
            $filter->equal('Store.name', __('店家名稱'));
            $filter->like('category', __('機台種類'));
            $filter->equal('mac', __('機台身分證'));
        });

        $grid->column('mac', __('Mac'))->totalRow('合計');
        $grid->column('store_name', __('店家名稱'))->display(function(){
            return $this->store->isNotEmpty()?$this->store->first()->name:'';
        });
        $grid->column('store_region', __('機台區域'))->display(function(){
            return $this->store->isNotEmpty()?$this->store->first()->region:'';
        });
        $grid->column('category', __('機台種類'));
        $grid->column('FishData.coin_ratio', __('投幣比率'));
        $grid->column('FishData.player_count', __('機台座位數'));
        $grid->column('FishData.income', __('機台收入'));
        $grid->column('FishData.payout', __('機台支出'));

        $open = '點開';
        $grid->column($open, '彩金紀錄')->modal('彩金狀態', function () {
            $Jackpot = $this;
            $Jackpots = $Jackpot->JackpotHistory()->get()->map(function ($Jackpot) {
                $seatNum = $Jackpot['player'] + 1;
                $JP = $Jackpot['jackpot'];
                $coins = $Jackpot['coins'];
                $convert = $Jackpot['coins']*2000/500;
                $datetime = $Jackpot['datetime'];

                return [$seatNum, $JP, $coins, $convert, $datetime];
            });
            return new Table(['拉彩位子', 'JP', '彩金', '換算金額', '時間'], $Jackpots->toArray());
        });
        $grid->column('created_at', __('建立時間'));
        $grid->column('updated_at', __('更新時間'));

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
        $show = new Show(Machine::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('mac', __('Mac'));
        $show->field('category', __('Category'));
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
        $form = new Form(new Machine());

        $data = FishData::pluck('mac', 'mac');

        $form->select('mac', __('機台Mac'))->options($data);
        $form->text('category', __('機台種類'));
        

        return $form;
    }
}
