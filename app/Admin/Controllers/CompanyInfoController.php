<?php

namespace App\Admin\Controllers;

use App\Models\CompanyInfo;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CompanyInfoController extends AdminController
{
    protected function title()
    {
        return "会社情報";
    }

    protected function grid()
    {
        $grid = new Grid(new CompanyInfo());

        $grid->column('id', __('ID'))->sortable();
        $grid->column('company_name', __('会社名'));
        $grid->column('postal_code', __('郵便番号'));
        $grid->column('address', __('所在地'));
        $grid->column('established_date', __('設立日'));
        $grid->column('representative', __('代表者'));
        $grid->column('business_content', __('事業内容'));
        $grid->column('email', __('メールアドレス'));
        $grid->column('phone_number', __('電話番号'));
        $grid->column('created_at', __('作成日時'));
        $grid->column('updated_at', __('更新日時'));

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(CompanyInfo::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('company_name', __('会社名'));
        $show->field('postal_code', __('郵便番号'));
        $show->field('address', __('所在地'));
        $show->field('established_date', __('設立日'));
        $show->field('representative', __('代表者'));
        $show->field('business_content', __('事業内容'));
        $show->field('email', __('メールアドレス'));
        $show->field('phone_number', __('電話番号'));
        $show->field('created_at', __('作成日時'));
        $show->field('updated_at', __('更新日時'));

        return $show;
    }

    protected function form()
    {
        $form = new Form(new CompanyInfo());

        $form->text('company_name', __('会社名'))->required();
        $form->text('postal_code', __('郵便番号'));
        $form->text('address', __('所在地'))->required();
        $form->date('established_date', __('設立日'))->required();
        $form->text('representative', __('代表者'))->required();
        $form->textarea('business_content', __('事業内容'))->required();
        $form->email('email', __('メールアドレス'))->required();
        $form->mobile('phone_number', __('電話番号'))->required();

        return $form;
    }
}
