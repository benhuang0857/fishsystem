<?php

namespace App\Admin\Controllers;

use App\Model\JackpotHistory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class JackpotController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '彩金紀錄';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new JackpotHistory());

        $grid->model()->orderBy('datetime', 'DESC');

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

        /*卡住
        $grid->column('store_name', __('店家名稱'))->display(function(){
            return $this->Machine->store->first()->name;
        });
        
        $grid->column('store_region', __('機台區域'))->display(function(){
            return $this->Machine->store->region;
        });
        */
        $grid->column('player', __('拉彩位子'))->display(function ($player) {
            $result = (int)$player;
            $result += 1;
            return $result.'號';
        });
        $grid->column('jackpot', __('JP'))->display(function ($jackpot) {
            $result = (int)$jackpot;
            $result += 1;
            return 'JP'.$result;
        });
        $grid->column('coins', __('硬幣'));
        $grid->column('jackpot', __('彩金'))->display(function(){
            return $this->coins * 2000;
        });
        $grid->column('jackpot_convert', __('彩金金額換算'))->display(function(){
            return $this->coins * 2000 / 500;
        });
        $grid->column('datetime', __('時間'))->sortable();

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
        $show = new Show(JackpotHistory::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('server_mac', __('Server mac'));
        $show->field('serial', __('Serial'));
        $show->field('mac', __('Mac'));
        $show->field('player', __('Player'));
        $show->field('jackpot', __('Jackpot'));
        $show->field('coins', __('Coins'));
        $show->field('datetime', __('Datetime'));
        $show->field('verified', __('Verified'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new JackpotHistory());

        $form->number('server_mac', __('Server mac'));
        $form->number('serial', __('Serial'));
        $form->number('mac', __('Mac'));
        $form->number('player', __('Player'));
        $form->number('jackpot', __('Jackpot'));
        $form->number('coins', __('Coins'));
        $form->datetime('datetime', __('Datetime'))->default(date('Y-m-d H:i:s'));
        $form->number('verified', __('Verified'));

        return $form;
    }
}
