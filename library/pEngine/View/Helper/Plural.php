<?php
class pEngine_View_Helper_Plural
{
	/**
	 * Super Plural.
	 *
	 * @param <type> $num
	 * @param array $titles as $titles('фирма', 'фирмы', 'фирм')
	 * @return <type>
	 */
	public function plural($number, $titles)
	{
		$cases = array (2, 0, 1, 1, 1, 2);
		return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
	}
}
