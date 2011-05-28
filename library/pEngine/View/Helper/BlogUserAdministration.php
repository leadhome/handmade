<?php

class pEngine_View_Helper_BlogUserAdministration extends Zend_View_Helper_Abstract
{
	/**
	 *
	 * @todo лучше сделать не хелпер а метод MethodsToUser
	 */
	public function blogUserAdministration($member_id, $blog_id)
	{
		if(isset(Zend_Auth::getInstance()->getIdentity()->id))
			$user_id = Zend_Auth::getInstance()->getIdentity()->id;
		else
			return false;

//		var_dump($member);
		$return = '';
		$links = Blog_Model_Member::getAdminLinks($user_id, $blog_id, $member_id);
		if($links)
			foreach($links as $link)
			{
				if($link == 'invite')
					$return .= '<a href="/blog/' . $blog_id . '/user/' . $member_id . '/' . $link . '">invite</a> ';
				elseif($link == 'ban')
					$return .= '<a href="/blog/' . $blog_id . '/user/' . $member_id . '/' . $link . '">ban</a> ';
				elseif($link == 'delete')
					$return .= '<a href="/blog/' . $blog_id . '/user/' . $member_id . '/' . $link . '">delete</a> ';
				elseif($link == 'adopt')
					$return .= '<a href="/blog/' . $blog_id . '/user/' . $member_id . '/' . $link . '">adopt</a> ';
			}
		else
			return false;

		return $return;
	}
}
