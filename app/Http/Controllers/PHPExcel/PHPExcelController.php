<?php
/**
 * Created by PhpStorm.
 * User: HSF
 * Date: 2016/9/18
 * Time: 12:07
 */
namespace App\Http\Controllers\PHPExcel;
use App\Http\Controllers\Controller;
use Excel;
class PHPExcelController extends Controller{

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     *
     * Excel文件导出功能
     */
    public function export(Excel $excel){

        $cellData = [
            ['学号','姓名','成绩'],
            ['10001','AAAAA','99'],
            ['10002','BBBBB','92'],
            ['10003','CCCCC','95'],
            ['10004','DDDDD','89'],
            ['10005','EEEEE','96'],
        ];
        Excel::create(iconv('UTF-8', 'GBK', '测试Excel导出'),function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->store('xls')->download('xlsx');   // 导出：download('xlsx') 或 export('xlsx)
    }

}