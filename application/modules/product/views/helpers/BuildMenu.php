<?php

class Product_View_Helper_BuildMenu extends Zend_View_Helper_Abstract {
    public function BuildMenu($categories,$parent = NULL) {
		foreach($categories as $category) {
			if($category['parent_id'] == $parent) {
				$menu[] = array(
									'category_id'=>$category['category_id'],
									'title' => $category['title'],
									'parent_id' => $category['parent_id'],
									'childrens' => $this->BuildMenu($categories,$category['category_id'])
								);
			}
		}
		return $menu;
	}
}
