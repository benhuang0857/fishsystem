<?php

namespace App\Admin\Controllers;

use App\Model\HandOverHistory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class HandOverHistoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '交班紀錄';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new HandOverHistory());

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
        });

        $grid->actions(function ($actions) {

            // 去掉删除
            $actions->disableDelete();
        
            // 去掉编辑
            $actions->disableEdit();
        });
        
        $grid->column('store.region', __('店家區域'));
        $grid->column('store.name', __('店家名稱'));
        $grid->column('income', __('收入'));
        $grid->column('payout', __('支出'));
        $grid->column('created_at', __('匯出時間'));

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
        $show = new Show(HandOverHistory::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('store_id', __('Store id'));
        $show->field('income', __('Income'));
        $show->field('payout', __('Payout'));
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
        $form = new Form(new HandOverHistory());

        $form->number('store_id', __('Store id'));
        $form->number('income', __('Income'));
        $form->number('payout', __('Payout'));

        return $form;
    }
}
