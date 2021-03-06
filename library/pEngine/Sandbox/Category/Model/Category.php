<?php

/**
 * pEngine_Category_Model_Category
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class pEngine_Category_Model_Category
{

	/**
	 * Adding category.
	 *
	 * @param int $parent_id
	 * @param array $data
	 * @return <type>
	 */
	public function addCategory($parent_id, $data)
	{
		$category_root = Doctrine_Core::getTable('Category_Model_Category')->find($parent_id);
		$child = new Category_Model_Category();
		if(isset($data['name'])) $child->name = $data['name'];
		if(isset($data['title'])) $child->title = $data['title'];
		if(isset($data['description'])) $child->description = $data['description'];
		$child->getNode()->insertAsLastChildOf($category_root);

		$this->createRouters($child->level);

		return;
	}

	/**
	 * Editing category.
	 *
	 * @param array $formdata
	 */
	public function editCategory($category_id, $formdata)
	{
		$update = Doctrine_Query::create()
			->update('Category_Model_Category')
			->where('id = ?', $formdata['category_id'])
			->set('name', '?', $formdata['name'])
			->set('title', '?', $formdata['title'])
			->set('description', '?', $formdata['description'])
			->execute();

		return true;
	}

	public function getAttr($category_id)
	{
		$attr = Doctrine_Query::create()
			->from('Category_Model_Category')
			->where('id = ?', $category_id)
			->limit(1)
			->fetchArray();
		$attr[0]['_description'] = $attr[0]['description'];
		return $attr[0];
	}

	/**
	 * Create routers if it not exists.
	 *
	 * @param int $level level in nested set
	 */
	protected function createRouters($level)
	{
		$max_level = Doctrine_Core::getTable('Router_Model_Req')->findOneByName('cat'.$level);

		if(isset($max_level->id)){
			return false;
		}else{
			//КАТЕГОРИИ
			//делаем роут
			$url = '';
			for($i=1; $i<$level+1; $i++){
				$url .= '/:cat'.$i;
			}

			$route = new Router_Model_Route();
			$route->module = 'spravka';
			$route->controller = 'index';
			$route->action = 'category';
			$route->url = $url;
			$route->save();

			for($i=1; $i<$level+1; $i++){
				$req = new Router_Model_Req();
				$req->route_id = $route->id;
				$req->name = 'cat'.$i;
				$req->value = '\w+';
				$req->save();
			}

			//ФИРМЫ
			//делаем роут
			$url = '';
			for($i=1; $i<$level+1; $i++){
				$url .= '/:cat'.$i;
			}
			$url .= '/:company_id';

			$route = new Router_Model_Route();
			$route->module = 'spravka';
			$route->controller = 'index';
			$route->action = 'company';
			$route->url = $url;
			$route->save();

			//деалем параметры
			$req = new Router_Model_Req();
			$req->route_id = $route->id;
			$req->name = 'company_id';
			$req->value = '\d+';
			$req->save();

			for($i=1; $i<$level+1; $i++){
				$req = new Router_Model_Req();
				$req->route_id = $route->id;
				$req->name = 'cat'.$i;
				$req->value = '\w+';
				$req->save();
			}
		}

		return true;
	}

	/**
	 * Moving categories
	 *
	 * @param int $from
	 * @param int $to
	 */
	public function moveCategory($from, $to)
	{
		$categoryTable = Doctrine_Core::getTable('Category_Model_Category');

		$category = $categoryTable->findOneById($to);

		$treeObject = $categoryTable->getTree();

		$childCategory = $categoryTable->findOneById($from);
		$childCategory->getNode()->moveAsLastChildOf($category);

		return;
	}

	/**
	 * Delete array of categories.
	 *
	 * @param array $array
	 */
	public function deleteCategory($array)
	{
		foreach($array as $id){
			$category = Doctrine_Core::getTable('Category_Model_Category')->findOneById($id);
			$category->getNode()->delete();
		}

		return;
	}

	public function getCategories($level = 0, $parent_id = 0)
	{
		if($level > 0 && $parent_id >= 0) {
			$parent = Doctrine_Core::getTable('Category_Model_Category')->find($parent_id);
			$categories = Doctrine_Query::create()
				->from('Category_Model_Category')
				->where('level = ?', $level)
				->andWhere('lft > ?', $parent->lft)
				->andWhere('rgt < ?', $parent->rgt)
				->orderBy('lft')
				->useResultCache(true)
				->setResultCacheLifeSpan(0)
				->execute();
		} else {
			$categories = Doctrine_Query::create()
				->from('Category_Model_Category')
				->where('level > 0')
				->orderBy('lft')
				->useResultCache(true)
				->setResultCacheLifeSpan(0)
				->execute();
		}

		if(count($categories))
			return $categories;
		return false;
	}

	/**
	 * Returns ul-li list.
	 *
	 * @param int $min_level
	 * @return string $string
	 */
	public function getCategoriesUlLi($min_level = 0)
	{
		$string = '<ul>';
		$prev_level = 0;
		$categories = Doctrine_Query::create()
			->from('pEngine_Category_Model_Category')
			->where('level > ?', $min_level-1)
			->orderBy('lft')
			->execute();
		foreach($categories as $c){
			if($c->level > $prev_level){
				$string .= '<ul><li>' . $c->title;
			}elseif($c->level < $prev_level){
				$multiplier = $prev_level - $c->level;
				$string .= str_repeat('</ul>', $multiplier) . '<li>' . $c->title;
			}elseif($c->level == $prev_level){
				$string .= '<li>' . $c->title;
			}
			$prev_level = $c->level;
		}
		$string .= '</ul>';

		return $string;
	}

	public static function getCategoriesList()
	{
		$tree = self::getCategoriesArray();
		return $tree;
	}


	/**
	 * Returns navigation for current magazine.
	 *
	 * @param string $magazine_name
	 * @return Zend_Navigation | false
	 */
	public function getNavigation()
	{
		$pages = array();

		$categories = Doctrine_Core::getTable('Category_Model_Category')->getTree()->fetchTree()->toArray();

		$categoryRoot = array_shift($categories);
		$pages = $this->recursiveNavigation($categories, $categoryRoot['level']+1, $categoryRoot['lft'], $categoryRoot['rgt']);

		$container = new Zend_Navigation($pages);
		return $container;
	}

	protected function recursiveNavigation($categories, $level, $l, $r)
	{
		$pages = array();
		foreach($categories AS $category){
			$cat = 'cat' . ($category['level']);
			$levels[$cat] = $category['name'];

			$params = array(
//				'magazine_name' => $levels['magazine_name']
				);

			for($i = 1; $i<$category['level']+1; ++$i){
				$params['cat' . $i] = $levels['cat' . $i];
			}

			if($category['level']==$level && $category['lft']>$l && $category['rgt']<$r){

				$category['label'] = $category['title'];
				$router_name = 'spravkaindexcategory';
				for($i = 1; $i<$category['level']+1; $i++){
					$router_name .= 'cat'.$i;
				}
				$category['route'] =  $router_name;
				$category['params'] = $params;

				$category['pages']=$this->recursiveNavigation($categories, $level+1, $category['lft'], $category['rgt']);
				$pages[] = $category;
			}

		}
		return $pages;
	}
}
