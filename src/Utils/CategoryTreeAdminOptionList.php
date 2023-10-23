<?php

namespace App\Utils;

use App\Twig\AppExtension;
use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminOptionList extends CategoryTreeAbstract
{
    public $html_1 = "<ul class='fa-ul text-left'>";
    public $html_2 = "<li><i class='fa-li fa fa-arrow-right'></i>";
    public $html_3 = "<a href='";
    public $html_4 = "'> Edit";
    public $html_5 = '</a> <a onclick="return confirm(\'Are you sure?\')" href="';
    public $html_6 = '">Delete';
    public $html_7 = "</a>";
    public $html_8 = "</li>";
    public $html_9 = "</ul>";


    public function getCategoryList(array $categoriesArray, int $repeat = 0)
    {

        foreach ($categoriesArray as $value)
        {
            $this->categoryList[] = ['name' => str_repeat("-", $repeat).$value['name'], 'id' => $value['id']];
            if(!empty($value['children']))
            {
                $repeat += 2;
                $this->getCategoryList($value['children'], $repeat);
                $repeat -= 2;
            }
        }
        return $this->categoryList;
    }

}