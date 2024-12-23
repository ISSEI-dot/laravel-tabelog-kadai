<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\Category;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Extensions\Tools\CsvImport;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Illuminate\Http\Request;

class ProductController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product());

        $grid->column('id', __('Id'))->sortable();
        $grid->column('name', __('店舗名'));
        $grid->column('description', __('店舗説明'));
        $grid->column('price', __('価格（下限）'))->sortable();
        $grid->column('price_max', __('価格（上限）'))->sortable();
        $grid->column('category.name', __('分類'));
        $grid->column('opening_time', __('開店時間'));
        $grid->column('closing_time', __('閉店時間'));
        $grid->column('regular_holiday', __('定休日'));
        $grid->column('image', __('画像'))->image();
        $grid->column('recommend_flag', __('おすすめフラグ'));
        $grid->column('postal_code', __('郵便番号')); 
        $grid->column('address', __('住所')); 
        $grid->column('phone_number', __('電話番号')); 
        $grid->column('created_at', __('登録日'))->sortable();
        $grid->column('updated_at', __('更新日'))->sortable();

        $grid->filter(function($filter) {
            $filter->like('name', '店舗名');
            $filter->like('description', '店舗説明');
            $filter->between('price', '金額');
            $filter->in('category_id', 'カテゴリー')->multipleSelect(Category::all()->pluck('name', 'id'));
            $filter->equal('recommend_flag', 'おすすめフラグ')->select(['0' => 'false', '1' => 'true']);
            $filter->like('postal_code', '郵便番号'); 
            $filter->like('address', '住所'); 
            $filter->like('phone_number', '電話番号'); 
        });

        $grid->tools(function ($tools) {
            $tools->append(new CsvImport());
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
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('店舗名'));
        $show->field('description', __('店舗説明'));
        $show->field('price', __('金額'));
        $show->field('category.name', __('分類'));
        $show->field('image', __('画像'))->image();
        $show->field('recommend_flag', __('おすすめフラグ'));
        $show->field('postal_code', __('郵便番号')); 
        $show->field('address', __('住所')); 
        $show->field('phone_number', __('電話番号'));
        $show->field('created_at', __('登録日'));
        $show->field('updated_at', __('更新日'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product());

        $form->text('name', __('店舗名'));
        $form->textarea('description', __('店舗の説明'));
        $form->number('price', __('価格（下限）'));
        $form->number('price_max', __('価格（上限）'))->required()->min(0);
        $form->select('category_id', __('分類'))->options(Category::all()->pluck('name', 'id'));
        $form->time('opening_time', __('開店時間'))->required();
        $form->time('closing_time', __('閉店時間'))->required();
        $form->text('regular_holiday', __('定休日'))->placeholder('例: 月曜日');
        $form->image('image', __('画像'));
        $form->switch('recommend_flag', __('おすすめフラグ'));
        $form->text('postal_code', __('郵便番号'))->required(); 
        $form->text('address', __('住所'))->required(); 
        $form->text('phone_number', __('電話番号'))->required();

        // バリデーション例
        $form->saving(function (Form $form) {
        if ($form->price > $form->price_max) {
            throw new \Exception('下限価格は上限価格以下である必要があります。');
        }
        if ($form->opening_time >= $form->closing_time) {
            throw new \Exception('開店時間は閉店時間より前である必要があります。');
        }
        });

        return $form;
    }

    public function csvImport(Request $request)
     {
         $file = $request->file('file');
         $lexer_config = new LexerConfig();
         $lexer = new Lexer($lexer_config);
 
         $interpreter = new Interpreter();
         $interpreter->unstrict();
 
         $rows = array();
         $interpreter->addObserver(function (array $row) use (&$rows) {
             $rows[] = $row;
         });
 
         $lexer->parse($file, $interpreter);

         foreach ($rows as $key => $value) {
             if (count($value) == 10) {
                 Product::create([
                     'name' => $value[0],
                     'description' => $value[1],
                     'price' => $value[2],
                     'category_id' => $value[3],
                     'image' => $value[4],
                     'recommend_flag' => $value[5],
                     'carriage_flag' => $value[6],
                     'postal_code' => $value[7],
                     'address' => $value[8],
                     'phone_number' => $value[9],
                 ]);
             }
         }
 
         return response()->json(
             ['data' => '成功'],
             200,
             [],
             JSON_UNESCAPED_UNICODE
         );
     }
}
