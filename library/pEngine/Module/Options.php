<?php
class pEngine_Module_Options
{
	// тест
	public static function getx()
	{
		return pEngine_Module_Options::getModel('lsdhb_llwkdbh_sdf');
	}

	// делает из template_module_model => Template__Module__Model
	public static function getModel($s)
	{
		$words = split("__", $s);

		for ($i=0; $i<count($words); $i++)
			$words[$i] = ucFirst($words[$i]);

		if (count($words) == 3)
			{
			$pod_words = split("_", $words[2]);
			for ($i=0; $i<count($pod_words); $i++)
				$pod_words[$i] = ucFirst($pod_words[$i]);
			$words[2] = implode("", $pod_words);
			
			return implode("_", $words);
			}
		else
			return '';
	}
	
	//получить список layouts
	public static function getLayouts()
	{
		$layouts = Doctrine_Query::create()
			->from('Template_Model_Layout')
			->execute();
		return $layouts;	
	}

	// получить список позиций
	public static function getPositions($module_id, $layout_id)
	{
		//получаем имя модуля по id layout
		$module = Doctrine_Query::create()
			->from('Template_Model_LayoutInComponent')
			->where('layout_id = ?', $layout_id)
			->execute();
		$module = $module[0]->Template_Model_Component->name;

		
		//получаем имя лейаута по id
		$layout = Doctrine_Query::create()
			->from('Template_Model_Layout')
			->where('id = ?', $layout_id)
			->execute();
		$layout = $layout[0]->name;
		
		// парсим лейаут на позиции
		$fname = APPLICATION_PATH.'/templates/'.$module.'/'.$layout.'.phtml';
		$f = fopen($fname, "r");
		$txt = fread($f, filesize($fname));
		fclose($f);
		$txt = str_replace('<?=', '', $txt);
		$txt = str_replace('?>', '', $txt);
		$parts = split('\)\-\>', $txt);
		$res = array();
		for ($i=1; $i<count($parts); $i++)
		{	
			preg_match_all("/[a-zA-Z0-9]+/", $parts[$i], $a);
			$res[] = $a[0][0];
		}	

		return $res; //$positions;
	}

	// получить список модулей из БД
	public static function getModules()
	{
		$mods = Doctrine_Query::create()
			->from('Template_Model_Module')
			->execute();
		return $mods;
	}

	// считать и рассериализовать форму
	public static function getForm($idModule)
	{
		$form = Doctrine_Query::create()
			->from('Template_Model_Module')
			->where('id = ?', $idModule)
			->execute();
		
		return unserialize($form[0]->params);
	}

	// сохранить форму
	public static function saveFormInModule($module, $form)
	{
		$q = Doctrine_Query::create()
			->update('Template_Model_Module m')
			->set('m.params', '?', $form )
			->where('m.id = ?', $module)
			->execute();
	}

	// Получаем свойства модуля в конкретном виде и конкретной позиции
	public static function getOptions($module, $layout, $position)
	{
		$q = Doctrine_Query::create()
			->from('Template_Model_ModuleInLayout')
			->andWhere('layout_id = ?', $layout)
			->andWhere('module_id = ?', $module)
			->andWhere('position =?', $position)
			->execute();
		
		if ($q->count() == 0)
			return '';
		else
			return unserialize($q[0] -> params);
	}

	// сохраняем свойства модуля
	public static function saveOptions($module, $layout, $position, $post)
	{
		$q = Doctrine_Query::create()
			->from('Template_Model_ModuleInLayout')
			->andWhere('layout_id = ?', $layout)
			->andWhere('module_id = ?', $module)
			->andWhere('position =?', $position)
			->execute();

		if ($q->count() == 0)
		{
			// если свойств еще нет - делаем новую запись
			$option = new Template_Model_ModuleInLayout();
			$option->layout_id = $layout;
			$option->module_id = $module;
			$option->params = serialize($post);
			$option->order = 1;
			$option->position = $position;
			$option->enabled = 1;
			$option->save();
		}
		else
		{	
			// а если есть - апдейтим
			$q = Doctrine_Query::create()
			->update('Template_Model_ModuleInLayout')
			->andWhere('layout_id = ?', $layout)
			->andWhere('module_id = ?', $module)
			->andWhere('position =?', $position)
			->set('params', '?', serialize($post) )
			->execute();
		}
	}

	// отобразить форму (на входе рассериализованный массив элементов)
	public static function showForm($elements)
	{
		//сколько элементов
		$n = count($elements['label']);
		if ($n != 0)
		{
			$form = new Zend_Form;
			// перебираем элементы
			for ($i=0; $i<$n; $i++)
			{
				// подпись поля формы
				$params = array('label' => $elements['label'][$i]);

				// если есть опции - парсим и передаем в multioptions
				if ($elements['vibor'][$i] == 'opt')
				{
					$params['multioptions'] = array();
					$ops = split(";", $elements['options_array'][$i]);
					foreach ($ops as $op)
					{
						// разбивка опции на name и value
						$o = split( "=", $op);
						if (count($o) == 2)
							$params['multioptions'][$o[1]] = $o[0];
					}
				}

				// если надо - берем опции из базы
				$model = $elements['model'][$i];
				if ($elements['vibor'][$i] == 'db' and count(split("__", $model)) == 3 )
				{
					$multioptinos = array();

					$model = pEngine_Module_Options::getModel($model);

					$q = Doctrine_Query::create()
						->select($elements['field1'][$i])
						->select($elements['field2'][$i])
						->from($model)
						->execute();
					$options = $q->toArray();
					
					foreach ($options as $option)
						$multioptions[$option[$elements['field1'][$i]]] = $option[$elements['field2'][$i]];

					$params['multioptions'] = $multioptions;
				}
				
				// добавляем элемент формы к объекту Zend_Form
				$form->addElement($elements['type'][$i], $elements['name'][$i], $params);
			}
			return $form;
		}
		else
			return ""; 
	}

	// Добавляет к объекту Zend_Form метод отправки и Submit
	public static function addOptionsElement($form)
	{
		if ($form != "")
		{
			$form->addElement('submit', 'submit', array(
	            'ignore' => true,
    	        'label' =>  'Сохранить'
    	        ));
			$form->setMethod("POST");
		}
		return $form;
	}

	// заполняем в форму значения из базы
	public static function fillOptions($form, $params)
	{
		foreach ($params as $name => $value)
			$form->$name->setValue($value);
		return $form;
	}

}
?>
