<?php
/**
 * Class for correcting world search
 */
class pEngine_Text_Corrector {

    protected $myWord = '';

    protected $word_list = array();

    protected $word_translit = array();

    private function translitEnglish( $str )
    {
        $tr = array(
                "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
                "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
                "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
                "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
                "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
                "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
                "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
                "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
                "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
                "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
                "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
                "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
                "ы"=>"yi","ь"=>"'","э"=>"e","ю"=>"yu","я"=>"ya"
            );
            return strtr( $str, $tr );
    }

    private function searchWorlds()
    {
        $enteredWord = $this->translitEnglish( $this->myWord );
        $possibleWord = NULL;
        $shortest = -1;
        foreach( $this->word_translit as $n => $k ) {
            $levenshtein = levenshtein($enteredWord, $k);
            if ($levenshtein == 0) {
                $correct = $n;
                $shortest = 0;
                break;
            }
            if ($levenshtein <= $shortest || $shortest < 0) {
                $correct  = $n;
                $shortest = $levenshtein;
            }
        }
        return $correct;
    }

    public function correctWord( $words, $dictionary )
    {
        $this->word_translit = $dictionary;
        $this->myWord = $words;

        if ( isset( $this->word_list[ $this->myWord ] ) )
            $correct[] = $this->myWord;
        else
            $correct[] = $this->searchWorlds();
        return $correct;
    }
}
