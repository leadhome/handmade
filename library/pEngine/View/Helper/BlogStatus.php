<?php

class pEngine_View_Helper_BlogStatus extends Zend_View_Helper_Abstract
{
	public function blogStatus($blog, $user_id = null)
	{
		if(!isset($user_id))
		{
			if(isset(Zend_Auth::getInstance()->getIdentity()->id))
				$user_id = Zend_Auth::getInstance()->getIdentity()->id;
			else
				return null;
		}

        
		$status = Blog_Model_Member::retMembership($blog->id, $user_id, true);
		if(Zend_Auth::getInstance()->getIdentity())
		{
			if($status)
			{
				if($status->role == 'banned')
					return 'You\'re banned on this blog.';
				elseif($status->role == 'invited')
					return '<a href="/blog/'.$blog->id.'/join">Adopt invite.</a>';
				else
					if($status->adopted)
						return '<a href="/blog/'.$blog->id.'/leave">Leave this blog.</a>';
					else
						return 'Please, wait adopting of adminitsration.';
			}
			else
			{
				if($blog->access)
					return '<a href="/blog/'.$blog->id.'/join">Send a request.</a>';
				else
					return '<a href="/blog/'.$blog->id.'/join">Join this blog.</a>';
			}
		}

	}
}
