<?php

namespace App\Admin\Controllers;

use App\Model\PlayerData;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PlayerDataController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'PlayerData';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PlayerData());

        $grid->column('id', __('Id'));
        $grid->column('mac', __('Mac'));
        $grid->column('num', __('Num'));
        $grid->column('bet', __('Bet'));
        $grid->column('credits', __('Credits'));
        $grid->column('created_time', __('Created time'));
        $grid->column('update_time', __('Update time'));

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
        $show = new Show(PlayerData::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('mac', __('Mac'));
        $show->field('num', __('Num'));
        $show->field('bet', __('Bet'));
        $show->field('credits', __('Credits'));
        $show->field('created_time', __('Created time'));
        $show->field('update_time', __('Update time'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new PlayerData());

        $form->number('mac', __('Mac'));
        $form->number('num', __('Num'));
        $form->number('bet', __('Bet'));
        $form->number('credits', __('Credits'));
        $form->datetime('created_time', __('Created time'))->default(date('Y-m-d H:i:s'));
        $form->datetime('update_time', __('Update time'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
