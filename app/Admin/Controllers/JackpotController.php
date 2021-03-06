<?php

namespace App\Admin\Controllers;

use App\Model\JackpotHistory;
use App\Model\Store;
use App\Model\Machine;
use App\Model\StoreMachine;
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
            $stores = Store::pluck('name', 'name');
            $categories = Machine::pluck('category', 'category');

            $filter->disableIdFilter();
            $filter->equal('Machine.Store.name', __('店家名稱'))->select($stores);
            $filter->like('Machine.category', __('機台種類'))->select($categories);
            $filter->between('datetime', __('時間'))->datetime();
        });

        $grid->column('store_name', __('店家名稱'))->display(function(){          
            return isset($this->Machine->store) ? $this->Machine->store->first()->name : '';
        });

        $grid->column('store_region', __('店家區域'))->display(function(){
            return isset($this->Machine->store) ? $this->Machine->store->first()->region : '';
        });

        //$grid->column('Machine.category', __('機台種類'))->sortable();
        $grid->column('category', __('機台種類'))->display(function(){return isset($this->Machine->category) ? $this->machine->category:'';})->sortable();

        $grid->column('player', __('座位'))->display(function ($player) {
            $result = (int)$player;
            $result += 1;
            return $result.'號';
        });
        $grid->column('jackpot', __('JP名稱'))->display(function ($jackpot) {
            $result1 = (int)$jackpot;
            $result1 += 1;
            return 'JP'.$result1;
        })->sortable();
        //$grid->column('coins', __('硬幣'));
        $grid->column('jackpotnum', __('彩金分數'))->display(function(){
            return $this->coins * 2000;
        })->sortable();
        $grid->column('jackpot_convert', __('彩金金額'))->display(function(){
            return $this->coins * 2000 / 500;
        })->sortable();
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
