<?php

namespace App\Admin\Controllers;

use App\Model\Store;
use App\Model\Machine;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

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

        $grid->column('name', __('店家名稱'));
        $grid->column('region', __('店家區域'));
        //$grid->column('machine_list', __('Machine list'));
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

        $data = Machine::pluck('mac', 'mac');

        $form->text('name', '店家名稱');
        $form->select('region', __('店家區域'))->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F']);
        $form->multipleSelect("machine_list", '店家機台Mac')->options($data);

        return $form;
    }
}
