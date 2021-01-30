<?php
$model = new Models_Catalog;
$catalog = array();

$model->category_id = MG::get('category')->getCategoryList($category_id); // пять - id категории
$model->category_id[] = $category_id;

$catalog = $model->getPageList($page, 5, true);

//категории:

$listCategories = MG::get('category')->getCategoryTitleList();
$arrayCategories = $model->category_id = MG::get('category')->getHierarchyCategory(0);


$categories.= "<ul id='category-tree'>";
$categories.= MG::get('category')->getCategoryListUl(0, 'admin');
$categories.= "</ul>";

$pagination = $catalog['pagination'];
unset($catalog['pagination']);

$select_categories = "<select id='category_edit_select' name='select_parent_category'>";
$select_categories.="<option selected value='0'>Все</option>";
$select_categories.=MG::get('category')->getTitleCategory($arrayCategories);
$select_categories.="</select>";

$this->select_categories = $select_categories;
$this->categories = $categories;

