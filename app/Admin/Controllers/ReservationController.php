<?php

namespace App\Admin\Controllers;

use App\Models\Reservation;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ReservationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Reservation';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Reservation());

        $grid->column('id', __('Id'))->sortable();
        $grid->column('product_id', __('Product id'));
        $grid->column('user_id', __('User id'));
        $grid->column('customer_name', __('Customer name'));
        $grid->column('people_count', __('People count'));
        $grid->column('reservation_date', __('Reservation date'));
        $grid->column('reservation_time', __('Reservation time'));
        $grid->column('created_at', __('Created at'))->sortable();
        $grid->column('updated_at', __('Updated at'))->sortable();

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
        $show = new Show(Reservation::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('product_id', __('Product id'));
        $show->field('user_id', __('User id'));
        $show->field('customer_name', __('Customer name'));
        $show->field('people_count', __('People count'));
        $show->field('reservation_date', __('Reservation date'));
        $show->field('reservation_time', __('Reservation time'));
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
        $form = new Form(new Reservation());

        $form->number('product_id', __('Product id'));
        $form->number('user_id', __('User id'));
        $form->text('customer_name', __('Customer name'));
        $form->number('people_count', __('People count'));
        $form->date('reservation_date', __('Reservation date'))->default(date('Y-m-d'));
        $form->time('reservation_time', __('Reservation time'))->default(date('H:i:s'));

        return $form;
    }
}
