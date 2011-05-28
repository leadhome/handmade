<?php
/**
 * Autocuting posts without user cut.
 *
 * @author Validmir Loginov
 */
class pEngine_View_Helper_BlogAutocutPost extends Zend_View_Helper_Abstract
{
	const LENGHT_CUT = 2500;
	const LENGHT_MAX = 3500;
	
	public function BlogAutocutPost($post_id)
	{
		$post = Doctrine_Core::getTable('Blog_Model_Post')->findOneById($post_id);
		if(isset($post->content))
		{
			$read_more = '...<br><a href=\'/blog/post/'.$post_id.'\'>Read More</a>';
			$content = trim(strip_tags($post->content));
			if(strlen($content) > self::LENGHT_MAX)
			{
				$content = substr($content, 0, strrpos(substr($content, 0, self::LENGHT_CUT), '.')).$read_more;
				return $content;
			}
			return $post->content;
		}
	}
}