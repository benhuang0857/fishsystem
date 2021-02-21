<?php

namespace App\Admin\Controllers;

use App\Model\PlayerData;
use App\Model\Store;
use App\Model\Machine;
use App\Model\StoreMachine;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class PlayerDataController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '玩家即時資訊';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */

     /*
    protected function grid()
    {
        $grid = new Grid(new PlayerData());

        $grid->model()->where('num', '<=', 5);
        $grid->disableCreateButton();

        $grid->disableActions();

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->equal('Machine.Store.region', __('店家區域'))->select([
                'A' => 'A',
                'B' => 'B',
                'C' => 'C',
                'D' => 'D',
                'E' => 'E',
                'F' => 'F',
            ]);
            $filter->equal('Machine.Store.name', __('店家名稱'));
            $filter->like('Machine.category', __('機台種類'));
            $filter->equal('mac', __('機台身分證'));
        });

        $grid->column('store_name', __('店家名稱'))->display(function(){          
            return isset($this->Machine->store) ? $this->Machine->store->first()->name : '';
        });

        $grid->column('store_region', __('店家區域'))->display(function(){
            return isset($this->Machine->store) ? $this->Machine->store->first()->region : '';
        });

        $grid->column('Machine.category', __('機台種類'));
        $grid->column('num', __('座位號碼'))->display(function ($num) {
            $result = (int)$num;
            $result += 1;
            return $result.'號';
        });;
        $grid->column('bet', __('押分'));
        $grid->column('credits', __('餘分'));
        $grid->column('update_time', __('更新時間'));

        return $grid;
    }
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

        $grid->column('mac', __('Mac'))->expand(function () {

            $PlayerData = $this->PlayerData()->where('num', '<=', 5)->get();
            //$PlayerData = $this->PlayerData()->get(); //for fish3
            $PlayerDatas = $PlayerData->map(function ($PlayerData) {
                $num = $PlayerData['num'] + 1;
                $bet = $PlayerData['bet'];
                $credits = $PlayerData['credits'];
                $update_time = $PlayerData['update_time'];
                return [$num, $bet, $credits, $update_time];
            });
        
            return new Table(['座位號碼','押分', '餘分', '更新時間'], $PlayerDatas->toArray());
        });

        $grid->column('category', __('機台種類'));

        $grid->column('store_name', __('店家名稱'))->display(function(){
            return $this->store->isNotEmpty()?$this->store->first()->name:'';
        });
        $grid->column('store_region', __('機台區域'))->display(function(){
            return $this->store->isNotEmpty()?$this->store->first()->region:'';
        });

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
