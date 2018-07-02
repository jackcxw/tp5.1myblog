<?php
/**
 * Created by PhpStorm.
 * User: 15603
 * Date: 2018/6/6
 * Time: 16:35
 */

namespace app\admin\controller;


use app\index\controller\Base;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class Test extends Base
{
    public function word(){
        $phpword = new PhpWord();

    }
}