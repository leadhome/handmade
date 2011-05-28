<?php

class pEngine_View_Helper_GetAvatar extends Zend_View_Helper_Abstract
{
	public function getAvatar($user_id = null, $size = "")
	{
		$user = Doctrine_Core::getTable('User_Model_User')->findOneById($user_id);
		if ($user->avatar)
		{
			if (!file_exists('images/user/' . $user->id . '/avatar/' . $size . $user->avatar))
				return $this->defaultAvatar($user_id, $size);
			else
				return '/images/user/' . $user->id . '/avatar/' . $size . $user->avatar;
			exit;
		}
		else
		{
			return $this->defaultAvatar($user_id, $size);
		}
	}
	
	protected function defaultAvatar($user_id = null, $size = "")
	{
		$sex = Doctrine_Query::create()
			->from('Field_Model_Value f')
			->where('f.field_id = ?', 5)
			->addWhere('f.user_id = ?', $user_id)
			->execute();
		switch ($sex[0]->value)
		{
			case 1:
			default:
				if (!file_exists('images/user/default/' . $size . 'default.jpg'))
				{
					$image = new pEngine_Image('ImageMagic', '/images/user/default/default.jpg');
					$image->resizeImage('smart', $size, $size);
					$image->saveImage('/images/user/default/' . $size . 'default.jpg', 100);
				}
				return '/images/user/default/' . $size . 'default.jpg';
				break;
			case 0:
				if (!file_exists('images/user/default/' . $size . 'default_women.jpg'))
				{
					$image = new pEngine_Image('ImageMagic', '/images/user/default/default_women.jpg');
					$image->resizeImage('smart', $size, $size);
					$image->saveImage('/images/user/default/' . $size . 'default_women.jpg', 100);
				}
				return '/images/user/default/' . $size . 'default_women.jpg';
				break;
		}
	}	
}
