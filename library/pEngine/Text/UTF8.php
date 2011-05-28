<?php
/**
 * A package of PHP functions to manipulate strings encoded in UTF-8.
 * The powerful solution/contribution for UTF-8 support in your CMF/CMS, written on PHP.
 * This package IMHO better then PHP UTF-8, http://sourceforge.net/projects/phputf8.
 *
 * Поддержка UTF-8 в PHP 5.
 * Набор (сборник, библиотека, склад, репозиторий) PHP функций для разработчиков веб-сайтов, использующих кодировку UTF-8
 *
 * Преимущества использования этого класса:
 *   * Совместимость с интерфейсом стандартных PHP функций, работающих с однобайтовыми кодировками
 *   * Возможность работы без PHP расширений ICONV и MBSTRING (если они есть, то активно используются)
 *   * Полезные функции, отсутствующие в ICONV и MBSTRING
 *   * Высокая производительность, надёжность и качественный код
 *   * PHP >= 5.1.x
 *
 * Символы UTF-8 получаются из Unicode следующим образом:
 *   2^7   0x00000000 — 0x0000007F  0xxxxxxx
 *   2^11  0x00000080 — 0x000007FF  110xxxxx 10xxxxxx
 *   2^16  0x00000800 — 0x0000FFFF  1110xxxx 10xxxxxx 10xxxxxx
 *   2^21  0x00010000 — 0x001FFFFF  11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
 *
 * 1-4 bytes length: 2^7 + 2^11 + 2^16 + 2^21 = 2 164 864
 *
 * Если бы я был повелителем мира, то оставил бы только 2 кодировки: UTF-8 и UTF-32 ;)
 *
 * TODO?
 *   http://www.unicode.org/reports/
 *   http://www.unicode.org/reports/tr10/      Unicode Collation Algorithm
 *   http://www.unicode.org/Public/UCA/5.1.0/
 *   http://www.unicode.org/reports/tr6/       A Standard Compression Scheme for Unicode
 *
 * @link     http://www.unicode.org/
 * @link     http://ru.wikipedia.org/wiki/UTF8
 * @link     http://www.fileformat.info/info/unicode/char/search.htm  Unicode Character Search
 *
 * @license  http://creativecommons.org/licenses/by-sa/3.0/
 * @author   Nasibullin Rinat: http://orangetie.ru/, http://rin-nas.moikrug.ru/
 * @charset  UTF-8
 * @version  2.1.2
 */
class pEngine_Text_UTF8
{
	/**
	 * Cимвол в UTF-8 без использоватния флага /u
	 * @deprecated  Используйте взамен точку (".") и флаг /u, это работает быстрее
	 * @var string
	 */
	public static $char_re = '  [\x09\x0A\x0D\x20-\x7E]           # ASCII strict
                              # [\x00-\x7F]                       # ASCII non-strict (including control chars)
                              | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
                              |  \xE0[\xA0-\xBF][\x80-\xBF]       # excluding overlongs
                              | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
                              |  \xED[\x80-\x9F][\x80-\xBF]       # excluding surrogates
                              |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
                              | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
                              |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
                             ';

	/*
	Combining diactrical marks (Unicode 5.1).
	http://www.unicode.org/charts/PDF/U0300.pdf
	http://www.unicode.org/charts/PDF/U1DC0.pdf
	http://www.unicode.org/charts/PDF/UFE20.pdf
	Например, русские буквы Ё (U+0401) и Й (U+0419) существуют в виде монолитных символов,
	хотя могут быть представлены и набором базового символа с последующим диакритическим знаком,
	то есть в составной форме (Decomposed): (U+0415 U+0308), (U+0418 U+0306).
	*/
	#public static $diactrical_re = '\p{M}'; #alternative, but only with /u flag
	public static $diactrical_re = '  \xcc[\x80-\xb9]|\xcd[\x80-\xaf]  #UNICODE range: U+0300 - U+036F (for letters)
                                    | \xe2\x83[\x90-\xbf]              #UNICODE range: U+20D0 - U+20FF (for symbols)
                                    | \xe1\xb7[\x80-\xbf]              #UNICODE range: U+1DC0 - U+1DFF (supplement)
                                    | \xef\xb8[\xa0-\xaf]              #UNICODE range: U+FE20 - U+FE2F (combining half marks)
                                   ';

	public static $html_special_chars_table = array(
		'&quot;' => "\x22",  #U+0022 ["] &#34; quotation mark = APL quote
		'&amp;'  => "\x26",  #U+0026 [&] &#38; ampersand
		'&lt;'   => "\x3c",  #U+003C [<] &#60; less-than sign
		'&gt;'   => "\x3e",  #U+003E [>] &#62; greater-than sign
	);

	/**
	 * @link http://www.fileformat.info/format/w3c/entitytest.htm?sort=Unicode%20Character  HTML Entity Browser Test Page
	 * @var  array
	 */
	public static $html_entity_table = array(
		#Latin-1 Entities:
		'&nbsp;'   => "\xc2\xa0",  #U+00A0 [ ] no-break space = non-breaking space
		'&iexcl;'  => "\xc2\xa1",  #U+00A1 [¡] inverted exclamation mark
		'&cent;'   => "\xc2\xa2",  #U+00A2 [¢] cent sign
		'&pound;'  => "\xc2\xa3",  #U+00A3 [£] pound sign
		'&curren;' => "\xc2\xa4",  #U+00A4 [¤] currency sign
		'&yen;'    => "\xc2\xa5",  #U+00A5 [¥] yen sign = yuan sign
		'&brvbar;' => "\xc2\xa6",  #U+00A6 [¦] broken bar = broken vertical bar
		'&sect;'   => "\xc2\xa7",  #U+00A7 [§] section sign
		'&uml;'    => "\xc2\xa8",  #U+00A8 [¨] diaeresis = spacing diaeresis
		'&copy;'   => "\xc2\xa9",  #U+00A9 [©] copyright sign
		'&ordf;'   => "\xc2\xaa",  #U+00AA [ª] feminine ordinal indicator
		'&laquo;'  => "\xc2\xab",  #U+00AB [«] left-pointing double angle quotation mark = left pointing guillemet
		'&not;'    => "\xc2\xac",  #U+00AC [¬] not sign
		'&shy;'    => "\xc2\xad",  #U+00AD [ ] soft hyphen = discretionary hyphen
		'&reg;'    => "\xc2\xae",  #U+00AE [®] registered sign = registered trade mark sign
		'&macr;'   => "\xc2\xaf",  #U+00AF [¯] macron = spacing macron = overline = APL overbar
		'&deg;'    => "\xc2\xb0",  #U+00B0 [°] degree sign
		'&plusmn;' => "\xc2\xb1",  #U+00B1 [±] plus-minus sign = plus-or-minus sign
		'&sup2;'   => "\xc2\xb2",  #U+00B2 [²] superscript two = superscript digit two = squared
		'&sup3;'   => "\xc2\xb3",  #U+00B3 [³] superscript three = superscript digit three = cubed
		'&acute;'  => "\xc2\xb4",  #U+00B4 [´] acute accent = spacing acute
		'&micro;'  => "\xc2\xb5",  #U+00B5 [µ] micro sign
		'&para;'   => "\xc2\xb6",  #U+00B6 [¶] pilcrow sign = paragraph sign
		'&middot;' => "\xc2\xb7",  #U+00B7 [·] middle dot = Georgian comma = Greek middle dot
		'&cedil;'  => "\xc2\xb8",  #U+00B8 [¸] cedilla = spacing cedilla
		'&sup1;'   => "\xc2\xb9",  #U+00B9 [¹] superscript one = superscript digit one
		'&ordm;'   => "\xc2\xba",  #U+00BA [º] masculine ordinal indicator
		'&raquo;'  => "\xc2\xbb",  #U+00BB [»] right-pointing double angle quotation mark = right pointing guillemet
		'&frac14;' => "\xc2\xbc",  #U+00BC [¼] vulgar fraction one quarter = fraction one quarter
		'&frac12;' => "\xc2\xbd",  #U+00BD [½] vulgar fraction one half = fraction one half
		'&frac34;' => "\xc2\xbe",  #U+00BE [¾] vulgar fraction three quarters = fraction three quarters
		'&iquest;' => "\xc2\xbf",  #U+00BF [¿] inverted question mark = turned question mark
		#Latin capital letter
		'&Agrave;' => "\xc3\x80",  #Latin capital letter A with grave = Latin capital letter A grave
		'&Aacute;' => "\xc3\x81",  #Latin capital letter A with acute
		'&Acirc;'  => "\xc3\x82",  #Latin capital letter A with circumflex
		'&Atilde;' => "\xc3\x83",  #Latin capital letter A with tilde
		'&Auml;'   => "\xc3\x84",  #Latin capital letter A with diaeresis
		'&Aring;'  => "\xc3\x85",  #Latin capital letter A with ring above = Latin capital letter A ring
		'&AElig;'  => "\xc3\x86",  #Latin capital letter AE = Latin capital ligature AE
		'&Ccedil;' => "\xc3\x87",  #Latin capital letter C with cedilla
		'&Egrave;' => "\xc3\x88",  #Latin capital letter E with grave
		'&Eacute;' => "\xc3\x89",  #Latin capital letter E with acute
		'&Ecirc;'  => "\xc3\x8a",  #Latin capital letter E with circumflex
		'&Euml;'   => "\xc3\x8b",  #Latin capital letter E with diaeresis
		'&Igrave;' => "\xc3\x8c",  #Latin capital letter I with grave
		'&Iacute;' => "\xc3\x8d",  #Latin capital letter I with acute
		'&Icirc;'  => "\xc3\x8e",  #Latin capital letter I with circumflex
		'&Iuml;'   => "\xc3\x8f",  #Latin capital letter I with diaeresis
		'&ETH;'    => "\xc3\x90",  #Latin capital letter ETH
		'&Ntilde;' => "\xc3\x91",  #Latin capital letter N with tilde
		'&Ograve;' => "\xc3\x92",  #Latin capital letter O with grave
		'&Oacute;' => "\xc3\x93",  #Latin capital letter O with acute
		'&Ocirc;'  => "\xc3\x94",  #Latin capital letter O with circumflex
		'&Otilde;' => "\xc3\x95",  #Latin capital letter O with tilde
		'&Ouml;'   => "\xc3\x96",  #Latin capital letter O with diaeresis
		'&times;'  => "\xc3\x97",  #U+00D7 [×] multiplication sign
		'&Oslash;' => "\xc3\x98",  #Latin capital letter O with stroke = Latin capital letter O slash
		'&Ugrave;' => "\xc3\x99",  #Latin capital letter U with grave
		'&Uacute;' => "\xc3\x9a",  #Latin capital letter U with acute
		'&Ucirc;'  => "\xc3\x9b",  #Latin capital letter U with circumflex
		'&Uuml;'   => "\xc3\x9c",  #Latin capital letter U with diaeresis
		'&Yacute;' => "\xc3\x9d",  #Latin capital letter Y with acute
		'&THORN;'  => "\xc3\x9e",  #Latin capital letter THORN
		#Latin small letter
		'&szlig;'  => "\xc3\x9f",  #Latin small letter sharp s = ess-zed
		'&agrave;' => "\xc3\xa0",  #Latin small letter a with grave = Latin small letter a grave
		'&aacute;' => "\xc3\xa1",  #Latin small letter a with acute
		'&acirc;'  => "\xc3\xa2",  #Latin small letter a with circumflex
		'&atilde;' => "\xc3\xa3",  #Latin small letter a with tilde
		'&auml;'   => "\xc3\xa4",  #Latin small letter a with diaeresis
		'&aring;'  => "\xc3\xa5",  #Latin small letter a with ring above = Latin small letter a ring
		'&aelig;'  => "\xc3\xa6",  #Latin small letter ae = Latin small ligature ae
		'&ccedil;' => "\xc3\xa7",  #Latin small letter c with cedilla
		'&egrave;' => "\xc3\xa8",  #Latin small letter e with grave
		'&eacute;' => "\xc3\xa9",  #Latin small letter e with acute
		'&ecirc;'  => "\xc3\xaa",  #Latin small letter e with circumflex
		'&euml;'   => "\xc3\xab",  #Latin small letter e with diaeresis
		'&igrave;' => "\xc3\xac",  #Latin small letter i with grave
		'&iacute;' => "\xc3\xad",  #Latin small letter i with acute
		'&icirc;'  => "\xc3\xae",  #Latin small letter i with circumflex
		'&iuml;'   => "\xc3\xaf",  #Latin small letter i with diaeresis
		'&eth;'    => "\xc3\xb0",  #Latin small letter eth
		'&ntilde;' => "\xc3\xb1",  #Latin small letter n with tilde
		'&ograve;' => "\xc3\xb2",  #Latin small letter o with grave
		'&oacute;' => "\xc3\xb3",  #Latin small letter o with acute
		'&ocirc;'  => "\xc3\xb4",  #Latin small letter o with circumflex
		'&otilde;' => "\xc3\xb5",  #Latin small letter o with tilde
		'&ouml;'   => "\xc3\xb6",  #Latin small letter o with diaeresis
		'&divide;' => "\xc3\xb7",  #U+00F7 [÷] division sign
		'&oslash;' => "\xc3\xb8",  #Latin small letter o with stroke = Latin small letter o slash
		'&ugrave;' => "\xc3\xb9",  #Latin small letter u with grave
		'&uacute;' => "\xc3\xba",  #Latin small letter u with acute
		'&ucirc;'  => "\xc3\xbb",  #Latin small letter u with circumflex
		'&uuml;'   => "\xc3\xbc",  #Latin small letter u with diaeresis
		'&yacute;' => "\xc3\xbd",  #Latin small letter y with acute
		'&thorn;'  => "\xc3\xbe",  #Latin small letter thorn
		'&yuml;'   => "\xc3\xbf",  #Latin small letter y with diaeresis
		#Symbols and Greek Letters:
		'&fnof;'    => "\xc6\x92",  #U+0192 [ƒ] Latin small f with hook = function = florin
		'&Alpha;'   => "\xce\x91",  #Greek capital letter alpha
		'&Beta;'    => "\xce\x92",  #Greek capital letter beta
		'&Gamma;'   => "\xce\x93",  #Greek capital letter gamma
		'&Delta;'   => "\xce\x94",  #Greek capital letter delta
		'&Epsilon;' => "\xce\x95",  #Greek capital letter epsilon
		'&Zeta;'    => "\xce\x96",  #Greek capital letter zeta
		'&Eta;'     => "\xce\x97",  #Greek capital letter eta
		'&Theta;'   => "\xce\x98",  #Greek capital letter theta
		'&Iota;'    => "\xce\x99",  #Greek capital letter iota
		'&Kappa;'   => "\xce\x9a",  #Greek capital letter kappa
		'&Lambda;'  => "\xce\x9b",  #Greek capital letter lambda
		'&Mu;'      => "\xce\x9c",  #Greek capital letter mu
		'&Nu;'      => "\xce\x9d",  #Greek capital letter nu
		'&Xi;'      => "\xce\x9e",  #Greek capital letter xi
		'&Omicron;' => "\xce\x9f",  #Greek capital letter omicron
		'&Pi;'      => "\xce\xa0",  #Greek capital letter pi
		'&Rho;'     => "\xce\xa1",  #Greek capital letter rho
		'&Sigma;'   => "\xce\xa3",  #Greek capital letter sigma
		'&Tau;'     => "\xce\xa4",  #Greek capital letter tau
		'&Upsilon;' => "\xce\xa5",  #Greek capital letter upsilon
		'&Phi;'     => "\xce\xa6",  #Greek capital letter phi
		'&Chi;'     => "\xce\xa7",  #Greek capital letter chi
		'&Psi;'     => "\xce\xa8",  #Greek capital letter psi
		'&Omega;'   => "\xce\xa9",  #Greek capital letter omega
		'&alpha;'   => "\xce\xb1",  #Greek small letter alpha
		'&beta;'    => "\xce\xb2",  #Greek small letter beta
		'&gamma;'   => "\xce\xb3",  #Greek small letter gamma
		'&delta;'   => "\xce\xb4",  #Greek small letter delta
		'&epsilon;' => "\xce\xb5",  #Greek small letter epsilon
		'&zeta;'    => "\xce\xb6",  #Greek small letter zeta
		'&eta;'     => "\xce\xb7",  #Greek small letter eta
		'&theta;'   => "\xce\xb8",  #Greek small letter theta
		'&iota;'    => "\xce\xb9",  #Greek small letter iota
		'&kappa;'   => "\xce\xba",  #Greek small letter kappa
		'&lambda;'  => "\xce\xbb",  #Greek small letter lambda
		'&mu;'      => "\xce\xbc",  #Greek small letter mu
		'&nu;'      => "\xce\xbd",  #Greek small letter nu
		'&xi;'      => "\xce\xbe",  #Greek small letter xi
		'&omicron;' => "\xce\xbf",  #Greek small letter omicron
		'&pi;'      => "\xcf\x80",  #Greek small letter pi
		'&rho;'     => "\xcf\x81",  #Greek small letter rho
		'&sigmaf;'  => "\xcf\x82",  #Greek small letter final sigma
		'&sigma;'   => "\xcf\x83",  #Greek small letter sigma
		'&tau;'     => "\xcf\x84",  #Greek small letter tau
		'&upsilon;' => "\xcf\x85",  #Greek small letter upsilon
		'&phi;'     => "\xcf\x86",  #Greek small letter phi
		'&chi;'     => "\xcf\x87",  #Greek small letter chi
		'&psi;'     => "\xcf\x88",  #Greek small letter psi
		'&omega;'   => "\xcf\x89",  #Greek small letter omega
		'&thetasym;'=> "\xcf\x91",  #Greek small letter theta symbol
		'&upsih;'   => "\xcf\x92",  #Greek upsilon with hook symbol
		'&piv;'     => "\xcf\x96",  #U+03D6 [ϖ] Greek pi symbol

		'&bull;'    => "\xe2\x80\xa2",  #U+2022 [•] bullet = black small circle
		'&hellip;'  => "\xe2\x80\xa6",  #U+2026 […] horizontal ellipsis = three dot leader
		'&prime;'   => "\xe2\x80\xb2",  #U+2032 [′] prime = minutes = feet (для обозначения минут и футов)
		'&Prime;'   => "\xe2\x80\xb3",  #U+2033 [″] double prime = seconds = inches (для обозначения секунд и дюймов).
		'&oline;'   => "\xe2\x80\xbe",  #U+203E [‾] overline = spacing overscore
		'&frasl;'   => "\xe2\x81\x84",  #U+2044 [⁄] fraction slash
		'&weierp;'  => "\xe2\x84\x98",  #U+2118 [℘] script capital P = power set = Weierstrass p
		'&image;'   => "\xe2\x84\x91",  #U+2111 [ℑ] blackletter capital I = imaginary part
		'&real;'    => "\xe2\x84\x9c",  #U+211C [ℜ] blackletter capital R = real part symbol
		'&trade;'   => "\xe2\x84\xa2",  #U+2122 [™] trade mark sign
		'&alefsym;' => "\xe2\x84\xb5",  #U+2135 [ℵ] alef symbol = first transfinite cardinal
		'&larr;'    => "\xe2\x86\x90",  #U+2190 [←] leftwards arrow
		'&uarr;'    => "\xe2\x86\x91",  #U+2191 [↑] upwards arrow
		'&rarr;'    => "\xe2\x86\x92",  #U+2192 [→] rightwards arrow
		'&darr;'    => "\xe2\x86\x93",  #U+2193 [↓] downwards arrow
		'&harr;'    => "\xe2\x86\x94",  #U+2194 [↔] left right arrow
		'&crarr;'   => "\xe2\x86\xb5",  #U+21B5 [↵] downwards arrow with corner leftwards = carriage return
		'&lArr;'    => "\xe2\x87\x90",  #U+21D0 [⇐] leftwards double arrow
		'&uArr;'    => "\xe2\x87\x91",  #U+21D1 [⇑] upwards double arrow
		'&rArr;'    => "\xe2\x87\x92",  #U+21D2 [⇒] rightwards double arrow
		'&dArr;'    => "\xe2\x87\x93",  #U+21D3 [⇓] downwards double arrow
		'&hArr;'    => "\xe2\x87\x94",  #U+21D4 [⇔] left right double arrow
		'&forall;'  => "\xe2\x88\x80",  #U+2200 [∀] for all
		'&part;'    => "\xe2\x88\x82",  #U+2202 [∂] partial differential
		'&exist;'   => "\xe2\x88\x83",  #U+2203 [∃] there exists
		'&empty;'   => "\xe2\x88\x85",  #U+2205 [∅] empty set = null set = diameter
		'&nabla;'   => "\xe2\x88\x87",  #U+2207 [∇] nabla = backward difference
		'&isin;'    => "\xe2\x88\x88",  #U+2208 [∈] element of
		'&notin;'   => "\xe2\x88\x89",  #U+2209 [∉] not an element of
		'&ni;'      => "\xe2\x88\x8b",  #U+220B [∋] contains as member
		'&prod;'    => "\xe2\x88\x8f",  #U+220F [∏] n-ary product = product sign
		'&sum;'     => "\xe2\x88\x91",  #U+2211 [∑] n-ary sumation
		'&minus;'   => "\xe2\x88\x92",  #U+2212 [−] minus sign
		'&lowast;'  => "\xe2\x88\x97",  #U+2217 [∗] asterisk operator
		'&radic;'   => "\xe2\x88\x9a",  #U+221A [√] square root = radical sign
		'&prop;'    => "\xe2\x88\x9d",  #U+221D [∝] proportional to
		'&infin;'   => "\xe2\x88\x9e",  #U+221E [∞] infinity
		'&ang;'     => "\xe2\x88\xa0",  #U+2220 [∠] angle
		'&and;'     => "\xe2\x88\xa7",  #U+2227 [∧] logical and = wedge
		'&or;'      => "\xe2\x88\xa8",  #U+2228 [∨] logical or = vee
		'&cap;'     => "\xe2\x88\xa9",  #U+2229 [∩] intersection = cap
		'&cup;'     => "\xe2\x88\xaa",  #U+222A [∪] union = cup
		'&int;'     => "\xe2\x88\xab",  #U+222B [∫] integral
		'&there4;'  => "\xe2\x88\xb4",  #U+2234 [∴] therefore
		'&sim;'     => "\xe2\x88\xbc",  #U+223C [∼] tilde operator = varies with = similar to
		'&cong;'    => "\xe2\x89\x85",  #U+2245 [≅] approximately equal to
		'&asymp;'   => "\xe2\x89\x88",  #U+2248 [≈] almost equal to = asymptotic to
		'&ne;'      => "\xe2\x89\xa0",  #U+2260 [≠] not equal to
		'&equiv;'   => "\xe2\x89\xa1",  #U+2261 [≡] identical to
		'&le;'      => "\xe2\x89\xa4",  #U+2264 [≤] less-than or equal to
		'&ge;'      => "\xe2\x89\xa5",  #U+2265 [≥] greater-than or equal to
		'&sub;'     => "\xe2\x8a\x82",  #U+2282 [⊂] subset of
		'&sup;'     => "\xe2\x8a\x83",  #U+2283 [⊃] superset of
		'&nsub;'    => "\xe2\x8a\x84",  #U+2284 [⊄] not a subset of
		'&sube;'    => "\xe2\x8a\x86",  #U+2286 [⊆] subset of or equal to
		'&supe;'    => "\xe2\x8a\x87",  #U+2287 [⊇] superset of or equal to
		'&oplus;'   => "\xe2\x8a\x95",  #U+2295 [⊕] circled plus = direct sum
		'&otimes;'  => "\xe2\x8a\x97",  #U+2297 [⊗] circled times = vector product
		'&perp;'    => "\xe2\x8a\xa5",  #U+22A5 [⊥] up tack = orthogonal to = perpendicular
		'&sdot;'    => "\xe2\x8b\x85",  #U+22C5 [⋅] dot operator
		'&lceil;'   => "\xe2\x8c\x88",  #U+2308 [⌈] left ceiling = APL upstile
		'&rceil;'   => "\xe2\x8c\x89",  #U+2309 [⌉] right ceiling
		'&lfloor;'  => "\xe2\x8c\x8a",  #U+230A [⌊] left floor = APL downstile
		'&rfloor;'  => "\xe2\x8c\x8b",  #U+230B [⌋] right floor
		'&lang;'    => "\xe2\x8c\xa9",  #U+2329 [〈] left-pointing angle bracket = bra
		'&rang;'    => "\xe2\x8c\xaa",  #U+232A [〉] right-pointing angle bracket = ket
		'&loz;'     => "\xe2\x97\x8a",  #U+25CA [◊] lozenge
		'&spades;'  => "\xe2\x99\xa0",  #U+2660 [♠] black spade suit
		'&clubs;'   => "\xe2\x99\xa3",  #U+2663 [♣] black club suit = shamrock
		'&hearts;'  => "\xe2\x99\xa5",  #U+2665 [♥] black heart suit = valentine
		'&diams;'   => "\xe2\x99\xa6",  #U+2666 [♦] black diamond suit
		#Other Special Characters:
		'&OElig;'  => "\xc5\x92",  #U+0152 [Œ] Latin capital ligature OE
		'&oelig;'  => "\xc5\x93",  #U+0153 [œ] Latin small ligature oe
		'&Scaron;' => "\xc5\xa0",  #U+0160 [Š] Latin capital letter S with caron
		'&scaron;' => "\xc5\xa1",  #U+0161 [š] Latin small letter s with caron
		'&Yuml;'   => "\xc5\xb8",  #U+0178 [Ÿ] Latin capital letter Y with diaeresis
		'&circ;'   => "\xcb\x86",  #U+02C6 [ˆ] modifier letter circumflex accent
		'&tilde;'  => "\xcb\x9c",  #U+02DC [˜] small tilde
		'&ensp;'   => "\xe2\x80\x82",  #U+2002 [ ] en space
		'&emsp;'   => "\xe2\x80\x83",  #U+2003 [ ] em space
		'&thinsp;' => "\xe2\x80\x89",  #U+2009 [ ] thin space
		'&zwnj;'   => "\xe2\x80\x8c",  #U+200C [‌] zero width non-joiner
		'&zwj;'    => "\xe2\x80\x8d",  #U+200D [‍] zero width joiner
		'&lrm;'    => "\xe2\x80\x8e",  #U+200E [‎] left-to-right mark
		'&rlm;'    => "\xe2\x80\x8f",  #U+200F [‏] right-to-left mark
		'&ndash;'  => "\xe2\x80\x93",  #U+2013 [–] en dash
		'&mdash;'  => "\xe2\x80\x94",  #U+2014 [—] em dash
		'&lsquo;'  => "\xe2\x80\x98",  #U+2018 [‘] left single quotation mark
		'&rsquo;'  => "\xe2\x80\x99",  #U+2019 [’] right single quotation mark (and apostrophe!)
		'&sbquo;'  => "\xe2\x80\x9a",  #U+201A [‚] single low-9 quotation mark
		'&ldquo;'  => "\xe2\x80\x9c",  #U+201C [“] left double quotation mark
		'&rdquo;'  => "\xe2\x80\x9d",  #U+201D [”] right double quotation mark
		'&bdquo;'  => "\xe2\x80\x9e",  #U+201E [„] double low-9 quotation mark
		'&dagger;' => "\xe2\x80\xa0",  #U+2020 [†] dagger
		'&Dagger;' => "\xe2\x80\xa1",  #U+2021 [‡] double dagger
		'&permil;' => "\xe2\x80\xb0",  #U+2030 [‰] per mille sign
		'&lsaquo;' => "\xe2\x80\xb9",  #U+2039 [‹] single left-pointing angle quotation mark
		'&rsaquo;' => "\xe2\x80\xba",  #U+203A [›] single right-pointing angle quotation mark
		'&euro;'   => "\xe2\x82\xac",  #U+20AC [€] euro sign
	);

	/**
	 * This table contains the data on how cp1259 characters map into Unicode (UTF-8).
	 * The cp1259 map describes standart tatarish cyrillic charset and based on the cp1251 table.
	 * cp1259 - это устаревшая однобайтовая кодировка татарского языка, которая включает в себя все русские буквы из cp1251.
	 *
	 * koi8-r -> UNICODE table:
	 *   http://tools.ietf.org/html/rfc1489
	 *   http://ru.wikipedia.org/wiki/КОИ-8
	 *
	 * @link  http://search.cpan.org/CPAN/authors/id/A/AM/AMICHAUER/Lingua-TT-Yanalif-0.08.tar.gz
	 * @link  http://www.unicode.org/charts/PDF/U0400.pdf
	 */
	public static $cp1259_table = array(
		#от 0x00 до 0x7F (ASCII) байты сохраняются как есть
		"\x80" => "\xd3\x98",      #U+04d8 CYRILLIC CAPITAL LETTER SCHWA
		"\x81" => "\xd0\x83",      #U+0403 CYRILLIC CAPITAL LETTER GJE
		"\x82" => "\xe2\x80\x9a",  #U+201a SINGLE LOW-9 QUOTATION MARK
		"\x83" => "\xd1\x93",      #U+0453 CYRILLIC SMALL LETTER GJE
		"\x84" => "\xe2\x80\x9e",  #U+201e DOUBLE LOW-9 QUOTATION MARK
		"\x85" => "\xe2\x80\xa6",  #U+2026 HORIZONTAL ELLIPSIS
		"\x86" => "\xe2\x80\xa0",  #U+2020 DAGGER
		"\x87" => "\xe2\x80\xa1",  #U+2021 DOUBLE DAGGER
		"\x88" => "\xe2\x82\xac",  #U+20ac EURO SIGN
		"\x89" => "\xe2\x80\xb0",  #U+2030 PER MILLE SIGN
		"\x8a" => "\xd3\xa8",      #U+04e8 CYRILLIC CAPITAL LETTER BARRED O
		"\x8b" => "\xe2\x80\xb9",  #U+2039 SINGLE LEFT-POINTING ANGLE QUOTATION MARK
		"\x8c" => "\xd2\xae",      #U+04ae CYRILLIC CAPITAL LETTER STRAIGHT U
		"\x8d" => "\xd2\x96",      #U+0496 CYRILLIC CAPITAL LETTER ZHE WITH DESCENDER
		"\x8e" => "\xd2\xa2",      #U+04a2 CYRILLIC CAPITAL LETTER EN WITH HOOK
		"\x8f" => "\xd2\xba",      #U+04ba CYRILLIC CAPITAL LETTER SHHA
		"\x90" => "\xd3\x99",      #U+04d9 CYRILLIC SMALL LETTER SCHWA
		"\x91" => "\xe2\x80\x98",  #U+2018 LEFT SINGLE QUOTATION MARK
		"\x92" => "\xe2\x80\x99",  #U+2019 RIGHT SINGLE QUOTATION MARK
		"\x93" => "\xe2\x80\x9c",  #U+201c LEFT DOUBLE QUOTATION MARK
		"\x94" => "\xe2\x80\x9d",  #U+201d RIGHT DOUBLE QUOTATION MARK
		"\x95" => "\xe2\x80\xa2",  #U+2022 BULLET
		"\x96" => "\xe2\x80\x93",  #U+2013 EN DASH
		"\x97" => "\xe2\x80\x94",  #U+2014 EM DASH
		#"\x98"                    #UNDEFINED
		"\x99" => "\xe2\x84\xa2",  #U+2122 TRADE MARK SIGN
		"\x9a" => "\xd3\xa9",      #U+04e9 CYRILLIC SMALL LETTER BARRED O
		"\x9b" => "\xe2\x80\xba",  #U+203a SINGLE RIGHT-POINTING ANGLE QUOTATION MARK
		"\x9c" => "\xd2\xaf",      #U+04af CYRILLIC SMALL LETTER STRAIGHT U
		"\x9d" => "\xd2\x97",      #U+0497 CYRILLIC SMALL LETTER ZHE WITH DESCENDER
		"\x9e" => "\xd2\xa3",      #U+04a3 CYRILLIC SMALL LETTER EN WITH HOOK
		"\x9f" => "\xd2\xbb",      #U+04bb CYRILLIC SMALL LETTER SHHA
		"\xa0" => "\xc2\xa0",      #U+00a0 NO-BREAK SPACE
		"\xa1" => "\xd0\x8e",      #U+040e CYRILLIC CAPITAL LETTER SHORT U
		"\xa2" => "\xd1\x9e",      #U+045e CYRILLIC SMALL LETTER SHORT U
		"\xa3" => "\xd0\x88",      #U+0408 CYRILLIC CAPITAL LETTER JE
		"\xa4" => "\xc2\xa4",      #U+00a4 CURRENCY SIGN
		"\xa5" => "\xd2\x90",      #U+0490 CYRILLIC CAPITAL LETTER GHE WITH UPTURN
		"\xa6" => "\xc2\xa6",      #U+00a6 BROKEN BAR
		"\xa7" => "\xc2\xa7",      #U+00a7 SECTION SIGN
		"\xa8" => "\xd0\x81",      #U+0401 CYRILLIC CAPITAL LETTER IO
		"\xa9" => "\xc2\xa9",      #U+00a9 COPYRIGHT SIGN
		"\xaa" => "\xd0\x84",      #U+0404 CYRILLIC CAPITAL LETTER UKRAINIAN IE
		"\xab" => "\xc2\xab",      #U+00ab LEFT-POINTING DOUBLE ANGLE QUOTATION MARK
		"\xac" => "\xc2\xac",      #U+00ac NOT SIGN
		"\xad" => "\xc2\xad",      #U+00ad SOFT HYPHEN
		"\xae" => "\xc2\xae",      #U+00ae REGISTERED SIGN
		"\xaf" => "\xd0\x87",      #U+0407 CYRILLIC CAPITAL LETTER YI
		"\xb0" => "\xc2\xb0",      #U+00b0 DEGREE SIGN
		"\xb1" => "\xc2\xb1",      #U+00b1 PLUS-MINUS SIGN
		"\xb2" => "\xd0\x86",      #U+0406 CYRILLIC CAPITAL LETTER BYELORUSSIAN-UKRAINIAN I
		"\xb3" => "\xd1\x96",      #U+0456 CYRILLIC SMALL LETTER BYELORUSSIAN-UKRAINIAN I
		"\xb4" => "\xd2\x91",      #U+0491 CYRILLIC SMALL LETTER GHE WITH UPTURN
		"\xb5" => "\xc2\xb5",      #U+00b5 MICRO SIGN
		"\xb6" => "\xc2\xb6",      #U+00b6 PILCROW SIGN
		"\xb7" => "\xc2\xb7",      #U+00b7 MIDDLE DOT
		"\xb8" => "\xd1\x91",      #U+0451 CYRILLIC SMALL LETTER IO
		"\xb9" => "\xe2\x84\x96",  #U+2116 NUMERO SIGN
		"\xba" => "\xd1\x94",      #U+0454 CYRILLIC SMALL LETTER UKRAINIAN IE
		"\xbb" => "\xc2\xbb",      #U+00bb RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK
		"\xbc" => "\xd1\x98",      #U+0458 CYRILLIC SMALL LETTER JE
		"\xbd" => "\xd0\x85",      #U+0405 CYRILLIC CAPITAL LETTER DZE
		"\xbe" => "\xd1\x95",      #U+0455 CYRILLIC SMALL LETTER DZE
		"\xbf" => "\xd1\x97",      #U+0457 CYRILLIC SMALL LETTER YI
		"\xc0" => "\xd0\x90",      #U+0410 CYRILLIC CAPITAL LETTER A
		"\xc1" => "\xd0\x91",      #U+0411 CYRILLIC CAPITAL LETTER BE
		"\xc2" => "\xd0\x92",      #U+0412 CYRILLIC CAPITAL LETTER VE
		"\xc3" => "\xd0\x93",      #U+0413 CYRILLIC CAPITAL LETTER GHE
		"\xc4" => "\xd0\x94",      #U+0414 CYRILLIC CAPITAL LETTER DE
		"\xc5" => "\xd0\x95",      #U+0415 CYRILLIC CAPITAL LETTER IE
		"\xc6" => "\xd0\x96",      #U+0416 CYRILLIC CAPITAL LETTER ZHE
		"\xc7" => "\xd0\x97",      #U+0417 CYRILLIC CAPITAL LETTER ZE
		"\xc8" => "\xd0\x98",      #U+0418 CYRILLIC CAPITAL LETTER I
		"\xc9" => "\xd0\x99",      #U+0419 CYRILLIC CAPITAL LETTER SHORT I
		"\xca" => "\xd0\x9a",      #U+041a CYRILLIC CAPITAL LETTER KA
		"\xcb" => "\xd0\x9b",      #U+041b CYRILLIC CAPITAL LETTER EL
		"\xcc" => "\xd0\x9c",      #U+041c CYRILLIC CAPITAL LETTER EM
		"\xcd" => "\xd0\x9d",      #U+041d CYRILLIC CAPITAL LETTER EN
		"\xce" => "\xd0\x9e",      #U+041e CYRILLIC CAPITAL LETTER O
		"\xcf" => "\xd0\x9f",      #U+041f CYRILLIC CAPITAL LETTER PE
		"\xd0" => "\xd0\xa0",      #U+0420 CYRILLIC CAPITAL LETTER ER
		"\xd1" => "\xd0\xa1",      #U+0421 CYRILLIC CAPITAL LETTER ES
		"\xd2" => "\xd0\xa2",      #U+0422 CYRILLIC CAPITAL LETTER TE
		"\xd3" => "\xd0\xa3",      #U+0423 CYRILLIC CAPITAL LETTER U
		"\xd4" => "\xd0\xa4",      #U+0424 CYRILLIC CAPITAL LETTER EF
		"\xd5" => "\xd0\xa5",      #U+0425 CYRILLIC CAPITAL LETTER HA
		"\xd6" => "\xd0\xa6",      #U+0426 CYRILLIC CAPITAL LETTER TSE
		"\xd7" => "\xd0\xa7",      #U+0427 CYRILLIC CAPITAL LETTER CHE
		"\xd8" => "\xd0\xa8",      #U+0428 CYRILLIC CAPITAL LETTER SHA
		"\xd9" => "\xd0\xa9",      #U+0429 CYRILLIC CAPITAL LETTER SHCHA
		"\xda" => "\xd0\xaa",      #U+042a CYRILLIC CAPITAL LETTER HARD SIGN
		"\xdb" => "\xd0\xab",      #U+042b CYRILLIC CAPITAL LETTER YERU
		"\xdc" => "\xd0\xac",      #U+042c CYRILLIC CAPITAL LETTER SOFT SIGN
		"\xdd" => "\xd0\xad",      #U+042d CYRILLIC CAPITAL LETTER E
		"\xde" => "\xd0\xae",      #U+042e CYRILLIC CAPITAL LETTER YU
		"\xdf" => "\xd0\xaf",      #U+042f CYRILLIC CAPITAL LETTER YA
		"\xe0" => "\xd0\xb0",      #U+0430 CYRILLIC SMALL LETTER A
		"\xe1" => "\xd0\xb1",      #U+0431 CYRILLIC SMALL LETTER BE
		"\xe2" => "\xd0\xb2",      #U+0432 CYRILLIC SMALL LETTER VE
		"\xe3" => "\xd0\xb3",      #U+0433 CYRILLIC SMALL LETTER GHE
		"\xe4" => "\xd0\xb4",      #U+0434 CYRILLIC SMALL LETTER DE
		"\xe5" => "\xd0\xb5",      #U+0435 CYRILLIC SMALL LETTER IE
		"\xe6" => "\xd0\xb6",      #U+0436 CYRILLIC SMALL LETTER ZHE
		"\xe7" => "\xd0\xb7",      #U+0437 CYRILLIC SMALL LETTER ZE
		"\xe8" => "\xd0\xb8",      #U+0438 CYRILLIC SMALL LETTER I
		"\xe9" => "\xd0\xb9",      #U+0439 CYRILLIC SMALL LETTER SHORT I
		"\xea" => "\xd0\xba",      #U+043a CYRILLIC SMALL LETTER KA
		"\xeb" => "\xd0\xbb",      #U+043b CYRILLIC SMALL LETTER EL
		"\xec" => "\xd0\xbc",      #U+043c CYRILLIC SMALL LETTER EM
		"\xed" => "\xd0\xbd",      #U+043d CYRILLIC SMALL LETTER EN
		"\xee" => "\xd0\xbe",      #U+043e CYRILLIC SMALL LETTER O
		"\xef" => "\xd0\xbf",      #U+043f CYRILLIC SMALL LETTER PE
		"\xf0" => "\xd1\x80",      #U+0440 CYRILLIC SMALL LETTER ER
		"\xf1" => "\xd1\x81",      #U+0441 CYRILLIC SMALL LETTER ES
		"\xf2" => "\xd1\x82",      #U+0442 CYRILLIC SMALL LETTER TE
		"\xf3" => "\xd1\x83",      #U+0443 CYRILLIC SMALL LETTER U
		"\xf4" => "\xd1\x84",      #U+0444 CYRILLIC SMALL LETTER EF
		"\xf5" => "\xd1\x85",      #U+0445 CYRILLIC SMALL LETTER HA
		"\xf6" => "\xd1\x86",      #U+0446 CYRILLIC SMALL LETTER TSE
		"\xf7" => "\xd1\x87",      #U+0447 CYRILLIC SMALL LETTER CHE
		"\xf8" => "\xd1\x88",      #U+0448 CYRILLIC SMALL LETTER SHA
		"\xf9" => "\xd1\x89",      #U+0449 CYRILLIC SMALL LETTER SHCHA
		"\xfa" => "\xd1\x8a",      #U+044a CYRILLIC SMALL LETTER HARD SIGN
		"\xfb" => "\xd1\x8b",      #U+044b CYRILLIC SMALL LETTER YERU
		"\xfc" => "\xd1\x8c",      #U+044c CYRILLIC SMALL LETTER SOFT SIGN
		"\xfd" => "\xd1\x8d",      #U+044d CYRILLIC SMALL LETTER E
		"\xfe" => "\xd1\x8e",      #U+044e CYRILLIC SMALL LETTER YU
		"\xff" => "\xd1\x8f",      #U+044f CYRILLIC SMALL LETTER YA
	);

	#таблица конвертации регистра
	public static $convert_case_table = array(
		#en (английский латиница)
		#CASE_UPPER => case_lower
		"\x41" => "\x61", #A a
		"\x42" => "\x62", #B b
		"\x43" => "\x63", #C c
		"\x44" => "\x64", #D d
		"\x45" => "\x65", #E e
		"\x46" => "\x66", #F f
		"\x47" => "\x67", #G g
		"\x48" => "\x68", #H h
		"\x49" => "\x69", #I i
		"\x4a" => "\x6a", #J j
		"\x4b" => "\x6b", #K k
		"\x4c" => "\x6c", #L l
		"\x4d" => "\x6d", #M m
		"\x4e" => "\x6e", #N n
		"\x4f" => "\x6f", #O o
		"\x50" => "\x70", #P p
		"\x51" => "\x71", #Q q
		"\x52" => "\x72", #R r
		"\x53" => "\x73", #S s
		"\x54" => "\x74", #T t
		"\x55" => "\x75", #U u
		"\x56" => "\x76", #V v
		"\x57" => "\x77", #W w
		"\x58" => "\x78", #X x
		"\x59" => "\x79", #Y y
		"\x5a" => "\x7a", #Z z

		#ru (русский кириллица)
		#CASE_UPPER => case_lower
		"\xd0\x81" => "\xd1\x91", #Ё ё
		"\xd0\x90" => "\xd0\xb0", #А а
		"\xd0\x91" => "\xd0\xb1", #Б б
		"\xd0\x92" => "\xd0\xb2", #В в
		"\xd0\x93" => "\xd0\xb3", #Г г
		"\xd0\x94" => "\xd0\xb4", #Д д
		"\xd0\x95" => "\xd0\xb5", #Е е
		"\xd0\x96" => "\xd0\xb6", #Ж ж
		"\xd0\x97" => "\xd0\xb7", #З з
		"\xd0\x98" => "\xd0\xb8", #И и
		"\xd0\x99" => "\xd0\xb9", #Й й
		"\xd0\x9a" => "\xd0\xba", #К к
		"\xd0\x9b" => "\xd0\xbb", #Л л
		"\xd0\x9c" => "\xd0\xbc", #М м
		"\xd0\x9d" => "\xd0\xbd", #Н н
		"\xd0\x9e" => "\xd0\xbe", #О о
		"\xd0\x9f" => "\xd0\xbf", #П п

		#CASE_UPPER => case_lower
		"\xd0\xa0" => "\xd1\x80", #Р р
		"\xd0\xa1" => "\xd1\x81", #С с
		"\xd0\xa2" => "\xd1\x82", #Т т
		"\xd0\xa3" => "\xd1\x83", #У у
		"\xd0\xa4" => "\xd1\x84", #Ф ф
		"\xd0\xa5" => "\xd1\x85", #Х х
		"\xd0\xa6" => "\xd1\x86", #Ц ц
		"\xd0\xa7" => "\xd1\x87", #Ч ч
		"\xd0\xa8" => "\xd1\x88", #Ш ш
		"\xd0\xa9" => "\xd1\x89", #Щ щ
		"\xd0\xaa" => "\xd1\x8a", #Ъ ъ
		"\xd0\xab" => "\xd1\x8b", #Ы ы
		"\xd0\xac" => "\xd1\x8c", #Ь ь
		"\xd0\xad" => "\xd1\x8d", #Э э
		"\xd0\xae" => "\xd1\x8e", #Ю ю
		"\xd0\xaf" => "\xd1\x8f", #Я я

		#tt (татарский, башкирский кириллица)
		#CASE_UPPER => case_lower
		"\xd2\x96" => "\xd2\x97", #Ж ж с хвостиком    &#1174; => &#1175;
		"\xd2\xa2" => "\xd2\xa3", #Н н с хвостиком    &#1186; => &#1187;
		"\xd2\xae" => "\xd2\xaf", #Y y                &#1198; => &#1199;
		"\xd2\xba" => "\xd2\xbb", #h h мягкое         &#1210; => &#1211;
		"\xd3\x98" => "\xd3\x99", #Э э                &#1240; => &#1241;
		"\xd3\xa8" => "\xd3\xa9", #О o перечеркнутое  &#1256; => &#1257;

		#uk (украинский кириллица)
		#CASE_UPPER => case_lower
		"\xd2\x90" => "\xd2\x91",  #г с хвостиком
		"\xd0\x84" => "\xd1\x94",  #э зеркальное отражение
		"\xd0\x86" => "\xd1\x96",  #и с одной точкой
		"\xd0\x87" => "\xd1\x97",  #и с двумя точками

		#be (белорусский кириллица)
		#CASE_UPPER => case_lower
		"\xd0\x8e" => "\xd1\x9e",  #у с подковой над буквой

		#tr,de,es (турецкий, немецкий, испанский, французский латиница)
		#CASE_UPPER => case_lower
		"\xc3\x84" => "\xc3\xa4", #a умляут          &#196; => &#228;  (турецкий)
		"\xc3\x87" => "\xc3\xa7", #c с хвостиком     &#199; => &#231;  (турецкий, французский)
		"\xc3\x91" => "\xc3\xb1", #n с тильдой       &#209; => &#241;  (турецкий, испанский)
		"\xc3\x96" => "\xc3\xb6", #o умляут          &#214; => &#246;  (турецкий)
		"\xc3\x9c" => "\xc3\xbc", #u умляут          &#220; => &#252;  (турецкий, французский)
		"\xc4\x9e" => "\xc4\x9f", #g умляут          &#286; => &#287;  (турецкий)
		"\xc4\xb0" => "\xc4\xb1", #i c точкой и без  &#304; => &#305;  (турецкий)
		"\xc5\x9e" => "\xc5\x9f", #s с хвостиком     &#350; => &#351;  (турецкий)

		#hr (хорватский латиница)
		#CASE_UPPER => case_lower
		"\xc4\x8c" => "\xc4\x8d",  #c с подковой над буквой
		"\xc4\x86" => "\xc4\x87",  #c с ударением
		"\xc4\x90" => "\xc4\x91",  #d перечеркнутое
		"\xc5\xa0" => "\xc5\xa1",  #s с подковой над буквой
		"\xc5\xbd" => "\xc5\xbe",  #z с подковой над буквой

		#fr (французский латиница)
		#CASE_UPPER => case_lower
		"\xc3\x80" => "\xc3\xa0",  #a с ударением в др. сторону
		"\xc3\x82" => "\xc3\xa2",  #a с крышкой
		"\xc3\x86" => "\xc3\xa6",  #ae совмещенное
		"\xc3\x88" => "\xc3\xa8",  #e с ударением в др. сторону
		"\xc3\x89" => "\xc3\xa9",  #e с ударением
		"\xc3\x8a" => "\xc3\xaa",  #e с крышкой
		"\xc3\x8b" => "\xc3\xab",  #ё
		"\xc3\x8e" => "\xc3\xae",  #i с крышкой
		"\xc3\x8f" => "\xc3\xaf",  #i умляут
		"\xc3\x94" => "\xc3\xb4",  #o с крышкой
		"\xc5\x92" => "\xc5\x93",  #ce совмещенное
		"\xc3\x99" => "\xc3\xb9",  #u с ударением в др. сторону
		"\xc3\x9b" => "\xc3\xbb",  #u с крышкой
		"\xc5\xb8" => "\xc3\xbf",  #y умляут

		#xx (другой язык)
		#CASE_UPPER => case_lower
		#"" => "",  #

	);

	#Unicode Character Database 5.2.0
	#autogenerated by unicode_blocks_txt2php() PHP function at 2010-06-16 14:46:00, 197 blocks total
	public static $unicode_blocks = array(
		'Basic Latin' => array(
			0 => 0x0000,
			1 => 0x007F,
			2 => 0,
		),
		'Latin-1 Supplement' => array(
			0 => 0x0080,
			1 => 0x00FF,
			2 => 1,
		),
		'Latin Extended-A' => array(
			0 => 0x0100,
			1 => 0x017F,
			2 => 2,
		),
		'Latin Extended-B' => array(
			0 => 0x0180,
			1 => 0x024F,
			2 => 3,
		),
		'IPA Extensions' => array(
			0 => 0x0250,
			1 => 0x02AF,
			2 => 4,
		),
		'Spacing Modifier Letters' => array(
			0 => 0x02B0,
			1 => 0x02FF,
			2 => 5,
		),
		'Combining Diacritical Marks' => array(
			0 => 0x0300,
			1 => 0x036F,
			2 => 6,
		),
		'Greek and Coptic' => array(
			0 => 0x0370,
			1 => 0x03FF,
			2 => 7,
		),
		'Cyrillic' => array(
			0 => 0x0400,
			1 => 0x04FF,
			2 => 8,
		),
		'Cyrillic Supplement' => array(
			0 => 0x0500,
			1 => 0x052F,
			2 => 9,
		),
		'Armenian' => array(
			0 => 0x0530,
			1 => 0x058F,
			2 => 10,
		),
		'Hebrew' => array(
			0 => 0x0590,
			1 => 0x05FF,
			2 => 11,
		),
		'Arabic' => array(
			0 => 0x0600,
			1 => 0x06FF,
			2 => 12,
		),
		'Syriac' => array(
			0 => 0x0700,
			1 => 0x074F,
			2 => 13,
		),
		'Arabic Supplement' => array(
			0 => 0x0750,
			1 => 0x077F,
			2 => 14,
		),
		'Thaana' => array(
			0 => 0x0780,
			1 => 0x07BF,
			2 => 15,
		),
		'NKo' => array(
			0 => 0x07C0,
			1 => 0x07FF,
			2 => 16,
		),
		'Samaritan' => array(
			0 => 0x0800,
			1 => 0x083F,
			2 => 17,
		),
		'Devanagari' => array(
			0 => 0x0900,
			1 => 0x097F,
			2 => 18,
		),
		'Bengali' => array(
			0 => 0x0980,
			1 => 0x09FF,
			2 => 19,
		),
		'Gurmukhi' => array(
			0 => 0x0A00,
			1 => 0x0A7F,
			2 => 20,
		),
		'Gujarati' => array(
			0 => 0x0A80,
			1 => 0x0AFF,
			2 => 21,
		),
		'Oriya' => array(
			0 => 0x0B00,
			1 => 0x0B7F,
			2 => 22,
		),
		'Tamil' => array(
			0 => 0x0B80,
			1 => 0x0BFF,
			2 => 23,
		),
		'Telugu' => array(
			0 => 0x0C00,
			1 => 0x0C7F,
			2 => 24,
		),
		'Kannada' => array(
			0 => 0x0C80,
			1 => 0x0CFF,
			2 => 25,
		),
		'Malayalam' => array(
			0 => 0x0D00,
			1 => 0x0D7F,
			2 => 26,
		),
		'Sinhala' => array(
			0 => 0x0D80,
			1 => 0x0DFF,
			2 => 27,
		),
		'Thai' => array(
			0 => 0x0E00,
			1 => 0x0E7F,
			2 => 28,
		),
		'Lao' => array(
			0 => 0x0E80,
			1 => 0x0EFF,
			2 => 29,
		),
		'Tibetan' => array(
			0 => 0x0F00,
			1 => 0x0FFF,
			2 => 30,
		),
		'Myanmar' => array(
			0 => 0x1000,
			1 => 0x109F,
			2 => 31,
		),
		'Georgian' => array(
			0 => 0x10A0,
			1 => 0x10FF,
			2 => 32,
		),
		'Hangul Jamo' => array(
			0 => 0x1100,
			1 => 0x11FF,
			2 => 33,
		),
		'Ethiopic' => array(
			0 => 0x1200,
			1 => 0x137F,
			2 => 34,
		),
		'Ethiopic Supplement' => array(
			0 => 0x1380,
			1 => 0x139F,
			2 => 35,
		),
		'Cherokee' => array(
			0 => 0x13A0,
			1 => 0x13FF,
			2 => 36,
		),
		'Unified Canadian Aboriginal Syllabics' => array(
			0 => 0x1400,
			1 => 0x167F,
			2 => 37,
		),
		'Ogham' => array(
			0 => 0x1680,
			1 => 0x169F,
			2 => 38,
		),
		'Runic' => array(
			0 => 0x16A0,
			1 => 0x16FF,
			2 => 39,
		),
		'Tagalog' => array(
			0 => 0x1700,
			1 => 0x171F,
			2 => 40,
		),
		'Hanunoo' => array(
			0 => 0x1720,
			1 => 0x173F,
			2 => 41,
		),
		'Buhid' => array(
			0 => 0x1740,
			1 => 0x175F,
			2 => 42,
		),
		'Tagbanwa' => array(
			0 => 0x1760,
			1 => 0x177F,
			2 => 43,
		),
		'Khmer' => array(
			0 => 0x1780,
			1 => 0x17FF,
			2 => 44,
		),
		'Mongolian' => array(
			0 => 0x1800,
			1 => 0x18AF,
			2 => 45,
		),
		'Unified Canadian Aboriginal Syllabics Extended' => array(
			0 => 0x18B0,
			1 => 0x18FF,
			2 => 46,
		),
		'Limbu' => array(
			0 => 0x1900,
			1 => 0x194F,
			2 => 47,
		),
		'Tai Le' => array(
			0 => 0x1950,
			1 => 0x197F,
			2 => 48,
		),
		'New Tai Lue' => array(
			0 => 0x1980,
			1 => 0x19DF,
			2 => 49,
		),
		'Khmer Symbols' => array(
			0 => 0x19E0,
			1 => 0x19FF,
			2 => 50,
		),
		'Buginese' => array(
			0 => 0x1A00,
			1 => 0x1A1F,
			2 => 51,
		),
		'Tai Tham' => array(
			0 => 0x1A20,
			1 => 0x1AAF,
			2 => 52,
		),
		'Balinese' => array(
			0 => 0x1B00,
			1 => 0x1B7F,
			2 => 53,
		),
		'Sundanese' => array(
			0 => 0x1B80,
			1 => 0x1BBF,
			2 => 54,
		),
		'Lepcha' => array(
			0 => 0x1C00,
			1 => 0x1C4F,
			2 => 55,
		),
		'Ol Chiki' => array(
			0 => 0x1C50,
			1 => 0x1C7F,
			2 => 56,
		),
		'Vedic Extensions' => array(
			0 => 0x1CD0,
			1 => 0x1CFF,
			2 => 57,
		),
		'Phonetic Extensions' => array(
			0 => 0x1D00,
			1 => 0x1D7F,
			2 => 58,
		),
		'Phonetic Extensions Supplement' => array(
			0 => 0x1D80,
			1 => 0x1DBF,
			2 => 59,
		),
		'Combining Diacritical Marks Supplement' => array(
			0 => 0x1DC0,
			1 => 0x1DFF,
			2 => 60,
		),
		'Latin Extended Additional' => array(
			0 => 0x1E00,
			1 => 0x1EFF,
			2 => 61,
		),
		'Greek Extended' => array(
			0 => 0x1F00,
			1 => 0x1FFF,
			2 => 62,
		),
		'General Punctuation' => array(
			0 => 0x2000,
			1 => 0x206F,
			2 => 63,
		),
		'Superscripts and Subscripts' => array(
			0 => 0x2070,
			1 => 0x209F,
			2 => 64,
		),
		'Currency Symbols' => array(
			0 => 0x20A0,
			1 => 0x20CF,
			2 => 65,
		),
		'Combining Diacritical Marks for Symbols' => array(
			0 => 0x20D0,
			1 => 0x20FF,
			2 => 66,
		),
		'Letterlike Symbols' => array(
			0 => 0x2100,
			1 => 0x214F,
			2 => 67,
		),
		'Number Forms' => array(
			0 => 0x2150,
			1 => 0x218F,
			2 => 68,
		),
		'Arrows' => array(
			0 => 0x2190,
			1 => 0x21FF,
			2 => 69,
		),
		'Mathematical Operators' => array(
			0 => 0x2200,
			1 => 0x22FF,
			2 => 70,
		),
		'Miscellaneous Technical' => array(
			0 => 0x2300,
			1 => 0x23FF,
			2 => 71,
		),
		'Control Pictures' => array(
			0 => 0x2400,
			1 => 0x243F,
			2 => 72,
		),
		'Optical Character Recognition' => array(
			0 => 0x2440,
			1 => 0x245F,
			2 => 73,
		),
		'Enclosed Alphanumerics' => array(
			0 => 0x2460,
			1 => 0x24FF,
			2 => 74,
		),
		'Box Drawing' => array(
			0 => 0x2500,
			1 => 0x257F,
			2 => 75,
		),
		'Block Elements' => array(
			0 => 0x2580,
			1 => 0x259F,
			2 => 76,
		),
		'Geometric Shapes' => array(
			0 => 0x25A0,
			1 => 0x25FF,
			2 => 77,
		),
		'Miscellaneous Symbols' => array(
			0 => 0x2600,
			1 => 0x26FF,
			2 => 78,
		),
		'Dingbats' => array(
			0 => 0x2700,
			1 => 0x27BF,
			2 => 79,
		),
		'Miscellaneous Mathematical Symbols-A' => array(
			0 => 0x27C0,
			1 => 0x27EF,
			2 => 80,
		),
		'Supplemental Arrows-A' => array(
			0 => 0x27F0,
			1 => 0x27FF,
			2 => 81,
		),
		'Braille Patterns' => array(
			0 => 0x2800,
			1 => 0x28FF,
			2 => 82,
		),
		'Supplemental Arrows-B' => array(
			0 => 0x2900,
			1 => 0x297F,
			2 => 83,
		),
		'Miscellaneous Mathematical Symbols-B' => array(
			0 => 0x2980,
			1 => 0x29FF,
			2 => 84,
		),
		'Supplemental Mathematical Operators' => array(
			0 => 0x2A00,
			1 => 0x2AFF,
			2 => 85,
		),
		'Miscellaneous Symbols and Arrows' => array(
			0 => 0x2B00,
			1 => 0x2BFF,
			2 => 86,
		),
		'Glagolitic' => array(
			0 => 0x2C00,
			1 => 0x2C5F,
			2 => 87,
		),
		'Latin Extended-C' => array(
			0 => 0x2C60,
			1 => 0x2C7F,
			2 => 88,
		),
		'Coptic' => array(
			0 => 0x2C80,
			1 => 0x2CFF,
			2 => 89,
		),
		'Georgian Supplement' => array(
			0 => 0x2D00,
			1 => 0x2D2F,
			2 => 90,
		),
		'Tifinagh' => array(
			0 => 0x2D30,
			1 => 0x2D7F,
			2 => 91,
		),
		'Ethiopic Extended' => array(
			0 => 0x2D80,
			1 => 0x2DDF,
			2 => 92,
		),
		'Cyrillic Extended-A' => array(
			0 => 0x2DE0,
			1 => 0x2DFF,
			2 => 93,
		),
		'Supplemental Punctuation' => array(
			0 => 0x2E00,
			1 => 0x2E7F,
			2 => 94,
		),
		'CJK Radicals Supplement' => array(
			0 => 0x2E80,
			1 => 0x2EFF,
			2 => 95,
		),
		'Kangxi Radicals' => array(
			0 => 0x2F00,
			1 => 0x2FDF,
			2 => 96,
		),
		'Ideographic Description Characters' => array(
			0 => 0x2FF0,
			1 => 0x2FFF,
			2 => 97,
		),
		'CJK Symbols and Punctuation' => array(
			0 => 0x3000,
			1 => 0x303F,
			2 => 98,
		),
		'Hiragana' => array(
			0 => 0x3040,
			1 => 0x309F,
			2 => 99,
		),
		'Katakana' => array(
			0 => 0x30A0,
			1 => 0x30FF,
			2 => 100,
		),
		'Bopomofo' => array(
			0 => 0x3100,
			1 => 0x312F,
			2 => 101,
		),
		'Hangul Compatibility Jamo' => array(
			0 => 0x3130,
			1 => 0x318F,
			2 => 102,
		),
		'Kanbun' => array(
			0 => 0x3190,
			1 => 0x319F,
			2 => 103,
		),
		'Bopomofo Extended' => array(
			0 => 0x31A0,
			1 => 0x31BF,
			2 => 104,
		),
		'CJK Strokes' => array(
			0 => 0x31C0,
			1 => 0x31EF,
			2 => 105,
		),
		'Katakana Phonetic Extensions' => array(
			0 => 0x31F0,
			1 => 0x31FF,
			2 => 106,
		),
		'Enclosed CJK Letters and Months' => array(
			0 => 0x3200,
			1 => 0x32FF,
			2 => 107,
		),
		'CJK Compatibility' => array(
			0 => 0x3300,
			1 => 0x33FF,
			2 => 108,
		),
		'CJK Unified Ideographs Extension A' => array(
			0 => 0x3400,
			1 => 0x4DBF,
			2 => 109,
		),
		'Yijing Hexagram Symbols' => array(
			0 => 0x4DC0,
			1 => 0x4DFF,
			2 => 110,
		),
		'CJK Unified Ideographs' => array(
			0 => 0x4E00,
			1 => 0x9FFF,
			2 => 111,
		),
		'Yi Syllables' => array(
			0 => 0xA000,
			1 => 0xA48F,
			2 => 112,
		),
		'Yi Radicals' => array(
			0 => 0xA490,
			1 => 0xA4CF,
			2 => 113,
		),
		'Lisu' => array(
			0 => 0xA4D0,
			1 => 0xA4FF,
			2 => 114,
		),
		'Vai' => array(
			0 => 0xA500,
			1 => 0xA63F,
			2 => 115,
		),
		'Cyrillic Extended-B' => array(
			0 => 0xA640,
			1 => 0xA69F,
			2 => 116,
		),
		'Bamum' => array(
			0 => 0xA6A0,
			1 => 0xA6FF,
			2 => 117,
		),
		'Modifier Tone Letters' => array(
			0 => 0xA700,
			1 => 0xA71F,
			2 => 118,
		),
		'Latin Extended-D' => array(
			0 => 0xA720,
			1 => 0xA7FF,
			2 => 119,
		),
		'Syloti Nagri' => array(
			0 => 0xA800,
			1 => 0xA82F,
			2 => 120,
		),
		'Common Indic Number Forms' => array(
			0 => 0xA830,
			1 => 0xA83F,
			2 => 121,
		),
		'Phags-pa' => array(
			0 => 0xA840,
			1 => 0xA87F,
			2 => 122,
		),
		'Saurashtra' => array(
			0 => 0xA880,
			1 => 0xA8DF,
			2 => 123,
		),
		'Devanagari Extended' => array(
			0 => 0xA8E0,
			1 => 0xA8FF,
			2 => 124,
		),
		'Kayah Li' => array(
			0 => 0xA900,
			1 => 0xA92F,
			2 => 125,
		),
		'Rejang' => array(
			0 => 0xA930,
			1 => 0xA95F,
			2 => 126,
		),
		'Hangul Jamo Extended-A' => array(
			0 => 0xA960,
			1 => 0xA97F,
			2 => 127,
		),
		'Javanese' => array(
			0 => 0xA980,
			1 => 0xA9DF,
			2 => 128,
		),
		'Cham' => array(
			0 => 0xAA00,
			1 => 0xAA5F,
			2 => 129,
		),
		'Myanmar Extended-A' => array(
			0 => 0xAA60,
			1 => 0xAA7F,
			2 => 130,
		),
		'Tai Viet' => array(
			0 => 0xAA80,
			1 => 0xAADF,
			2 => 131,
		),
		'Meetei Mayek' => array(
			0 => 0xABC0,
			1 => 0xABFF,
			2 => 132,
		),
		'Hangul Syllables' => array(
			0 => 0xAC00,
			1 => 0xD7AF,
			2 => 133,
		),
		'Hangul Jamo Extended-B' => array(
			0 => 0xD7B0,
			1 => 0xD7FF,
			2 => 134,
		),
		'High Surrogates' => array(
			0 => 0xD800,
			1 => 0xDB7F,
			2 => 135,
		),
		'High Private Use Surrogates' => array(
			0 => 0xDB80,
			1 => 0xDBFF,
			2 => 136,
		),
		'Low Surrogates' => array(
			0 => 0xDC00,
			1 => 0xDFFF,
			2 => 137,
		),
		'Private Use Area' => array(
			0 => 0xE000,
			1 => 0xF8FF,
			2 => 138,
		),
		'CJK Compatibility Ideographs' => array(
			0 => 0xF900,
			1 => 0xFAFF,
			2 => 139,
		),
		'Alphabetic Presentation Forms' => array(
			0 => 0xFB00,
			1 => 0xFB4F,
			2 => 140,
		),
		'Arabic Presentation Forms-A' => array(
			0 => 0xFB50,
			1 => 0xFDFF,
			2 => 141,
		),
		'Variation Selectors' => array(
			0 => 0xFE00,
			1 => 0xFE0F,
			2 => 142,
		),
		'Vertical Forms' => array(
			0 => 0xFE10,
			1 => 0xFE1F,
			2 => 143,
		),
		'Combining Half Marks' => array(
			0 => 0xFE20,
			1 => 0xFE2F,
			2 => 144,
		),
		'CJK Compatibility Forms' => array(
			0 => 0xFE30,
			1 => 0xFE4F,
			2 => 145,
		),
		'Small Form Variants' => array(
			0 => 0xFE50,
			1 => 0xFE6F,
			2 => 146,
		),
		'Arabic Presentation Forms-B' => array(
			0 => 0xFE70,
			1 => 0xFEFF,
			2 => 147,
		),
		'Halfwidth and Fullwidth Forms' => array(
			0 => 0xFF00,
			1 => 0xFFEF,
			2 => 148,
		),
		'Specials' => array(
			0 => 0xFFF0,
			1 => 0xFFFF,
			2 => 149,
		),
		'Linear B Syllabary' => array(
			0 => 0x10000,
			1 => 0x1007F,
			2 => 150,
		),
		'Linear B Ideograms' => array(
			0 => 0x10080,
			1 => 0x100FF,
			2 => 151,
		),
		'Aegean Numbers' => array(
			0 => 0x10100,
			1 => 0x1013F,
			2 => 152,
		),
		'Ancient Greek Numbers' => array(
			0 => 0x10140,
			1 => 0x1018F,
			2 => 153,
		),
		'Ancient Symbols' => array(
			0 => 0x10190,
			1 => 0x101CF,
			2 => 154,
		),
		'Phaistos Disc' => array(
			0 => 0x101D0,
			1 => 0x101FF,
			2 => 155,
		),
		'Lycian' => array(
			0 => 0x10280,
			1 => 0x1029F,
			2 => 156,
		),
		'Carian' => array(
			0 => 0x102A0,
			1 => 0x102DF,
			2 => 157,
		),
		'Old Italic' => array(
			0 => 0x10300,
			1 => 0x1032F,
			2 => 158,
		),
		'Gothic' => array(
			0 => 0x10330,
			1 => 0x1034F,
			2 => 159,
		),
		'Ugaritic' => array(
			0 => 0x10380,
			1 => 0x1039F,
			2 => 160,
		),
		'Old Persian' => array(
			0 => 0x103A0,
			1 => 0x103DF,
			2 => 161,
		),
		'Deseret' => array(
			0 => 0x10400,
			1 => 0x1044F,
			2 => 162,
		),
		'Shavian' => array(
			0 => 0x10450,
			1 => 0x1047F,
			2 => 163,
		),
		'Osmanya' => array(
			0 => 0x10480,
			1 => 0x104AF,
			2 => 164,
		),
		'Cypriot Syllabary' => array(
			0 => 0x10800,
			1 => 0x1083F,
			2 => 165,
		),
		'Imperial Aramaic' => array(
			0 => 0x10840,
			1 => 0x1085F,
			2 => 166,
		),
		'Phoenician' => array(
			0 => 0x10900,
			1 => 0x1091F,
			2 => 167,
		),
		'Lydian' => array(
			0 => 0x10920,
			1 => 0x1093F,
			2 => 168,
		),
		'Kharoshthi' => array(
			0 => 0x10A00,
			1 => 0x10A5F,
			2 => 169,
		),
		'Old South Arabian' => array(
			0 => 0x10A60,
			1 => 0x10A7F,
			2 => 170,
		),
		'Avestan' => array(
			0 => 0x10B00,
			1 => 0x10B3F,
			2 => 171,
		),
		'Inscriptional Parthian' => array(
			0 => 0x10B40,
			1 => 0x10B5F,
			2 => 172,
		),
		'Inscriptional Pahlavi' => array(
			0 => 0x10B60,
			1 => 0x10B7F,
			2 => 173,
		),
		'Old Turkic' => array(
			0 => 0x10C00,
			1 => 0x10C4F,
			2 => 174,
		),
		'Rumi Numeral Symbols' => array(
			0 => 0x10E60,
			1 => 0x10E7F,
			2 => 175,
		),
		'Kaithi' => array(
			0 => 0x11080,
			1 => 0x110CF,
			2 => 176,
		),
		'Cuneiform' => array(
			0 => 0x12000,
			1 => 0x123FF,
			2 => 177,
		),
		'Cuneiform Numbers and Punctuation' => array(
			0 => 0x12400,
			1 => 0x1247F,
			2 => 178,
		),
		'Egyptian Hieroglyphs' => array(
			0 => 0x13000,
			1 => 0x1342F,
			2 => 179,
		),
		'Byzantine Musical Symbols' => array(
			0 => 0x1D000,
			1 => 0x1D0FF,
			2 => 180,
		),
		'Musical Symbols' => array(
			0 => 0x1D100,
			1 => 0x1D1FF,
			2 => 181,
		),
		'Ancient Greek Musical Notation' => array(
			0 => 0x1D200,
			1 => 0x1D24F,
			2 => 182,
		),
		'Tai Xuan Jing Symbols' => array(
			0 => 0x1D300,
			1 => 0x1D35F,
			2 => 183,
		),
		'Counting Rod Numerals' => array(
			0 => 0x1D360,
			1 => 0x1D37F,
			2 => 184,
		),
		'Mathematical Alphanumeric Symbols' => array(
			0 => 0x1D400,
			1 => 0x1D7FF,
			2 => 185,
		),
		'Mahjong Tiles' => array(
			0 => 0x1F000,
			1 => 0x1F02F,
			2 => 186,
		),
		'Domino Tiles' => array(
			0 => 0x1F030,
			1 => 0x1F09F,
			2 => 187,
		),
		'Enclosed Alphanumeric Supplement' => array(
			0 => 0x1F100,
			1 => 0x1F1FF,
			2 => 188,
		),
		'Enclosed Ideographic Supplement' => array(
			0 => 0x1F200,
			1 => 0x1F2FF,
			2 => 189,
		),
		'CJK Unified Ideographs Extension B' => array(
			0 => 0x20000,
			1 => 0x2A6DF,
			2 => 190,
		),
		'CJK Unified Ideographs Extension C' => array(
			0 => 0x2A700,
			1 => 0x2B73F,
			2 => 191,
		),
		'CJK Compatibility Ideographs Supplement' => array(
			0 => 0x2F800,
			1 => 0x2FA1F,
			2 => 192,
		),
		'Tags' => array(
			0 => 0xE0000,
			1 => 0xE007F,
			2 => 193,
		),
		'Variation Selectors Supplement' => array(
			0 => 0xE0100,
			1 => 0xE01EF,
			2 => 194,
		),
		'Supplementary Private Use Area-A' => array(
			0 => 0xF0000,
			1 => 0xFFFFF,
			2 => 195,
		),
		'Supplementary Private Use Area-B' => array(
			0 => 0x100000,
			1 => 0x10FFFF,
			2 => 196,
		),
	);

	#запрещаем создание экземпляра класса, вызов методов этого класса только статически!
	private function __construct() {}

	/**
	 * Remove combining diactrical marks, with possibility of the restore
	 * Удаляет диакритические знаки в тексте, с возможностью восстановления (опция)
	 *
	 * @param   string|null       $s
	 * @param   array|null        $additional_chars   for example: "\xc2\xad"  #soft hyphen = discretionary hyphen
	 * @param   bool              $is_can_restored
	 * @param   array|null        &$restore_table
	 * @return  string|bool|null  returns FALSE if error occured
	 */
	public static function diactrical_remove($s, $additional_chars = null, $is_can_restored = false, &$restore_table = null)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		if ($additional_chars)
		{
			foreach ($additional_chars as $k => &$v) $v = preg_quote($v, '/');
			$re = '/((?>' . self::$diactrical_re . '|' . implode('|', $additional_chars) . ')+)/sxSX';
		}
		else $re = '/((?>' . self::$diactrical_re . ')+)/sxSX';
		if (! $is_can_restored) return preg_replace($re, '', $s);

		$restore_table = array();
		$a = preg_split($re, $s, -1, PREG_SPLIT_DELIM_CAPTURE);
		$c = count($a);
		if ($c === 1) return $s;
		$pos = 0;
		$s2 = '';
		for ($i = 0; $i < $c - 1; $i += 2)
		{
			$s2 .= $a[$i];
			#запоминаем символьные (не байтовые!) позиции
			$pos += self::strlen($a[$i]);
			$restore_table['offsets'][$pos] = $a[$i + 1];
		}
		$restore_table['length'] = $pos + self::strlen(end($a));
		return $s2 . end($a);
	}

	/**
	 * Restore combining diactrical marks, removed by self::diactrical_remove()
	 * Восстанавливает диакритические знаки в тексте, при условии, что их символьные позиции и кол-во символов не изменились!
	 *
	 * @see     self::diactrical_remove()
	 * @param   string|null       $s
	 * @param   array             $restore_table
	 * @return  string|bool|null  returns FALSE if error occured (broken $restore_table)
	 */
	public static function diactrical_restore($s, array $restore_table)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		if (! $restore_table) return $s;
		if (! is_int(@$restore_table['length']) ||
			! is_array(@$restore_table['offsets']) ||
			$restore_table['length'] !== self::strlen($s)) return false;
		$a = array();
		$length = $offset = 0;
		$s2 = '';
		foreach ($restore_table['offsets'] as $pos => $diactricals)
		{
			$length = $pos - $offset;
			$s2 .= self::substr($s, $offset, $length) . $diactricals;
			$offset = $pos;
		}
		return $s2 . self::substr($s, $offset, strlen($s));
	}

	/**
	 * Функция для перекодировки данных произвольной структуры из какой-либо кодировки в кодировку UTF-8.
	 *
	 * @param   array|scalar|null  $data
	 * @param   string             $charset
	 * @return  array|scalar|null  returns FALSE if error occured
	 */
	public static function convert_from($data, $charset = 'cp1251')
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		return self::_convert($data, $charset, 'UTF-8');
	}

	/**
	 * Функция для перекодировки данных произвольной структуры из кодировки UTF-8 в другую кодировку.
	 *
	 * @param   array|scalar|null  $data
	 * @param   string             $charset
	 * @return  array|scalar|null  returns FALSE if error occured
	 */
	public static function convert_to($data, $charset = 'cp1251')
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		return self::_convert($data, 'UTF-8', $charset);
	}

	/**
	 * Функция для перекодировки данных произвольной структуры из/в кодировку UTF-8.
	 * Массивы обходятся рекурсивно, при этом перекодируются как ключи, так и значения элементов массива.
	 *
	 * @param   array|scalar|null  $data
	 * @param   string             $charset_from
	 * @param   string             $charset_to
	 * @return  array|scalar|null  returns FALSE if error occured
	 */
	private static function _convert($data, $charset_from, $charset_to)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;  #for recursive calls
		if ($charset_from === $charset_to) return $data;
		if (is_array($data))
		{
			$d = array();
			foreach ($data as $k => &$v)
			{
				$k = self::_convert($k, $charset_from, $charset_to);
				if ($k === false) return false;
				$d[$k] = self::_convert($v, $charset_from, $charset_to);
				if ($d[$k] === false && ! is_bool($v)) return false;
			}
			return $d;
		}
		if (is_string($data))
		{
			if ($charset_from === 'UTF-8' && ! self::is_utf8($data)) return $data;  #smart behaviour
			if ($charset_to === 'UTF-8' && self::is_utf8($data)) return $data;  #smart behaviour
			if (function_exists('iconv')) return iconv($charset_from, $charset_to . '//IGNORE//TRANSLIT', $data);
			if (function_exists('mb_convert_encoding')) return mb_convert_encoding($data, $charset_to, $charset_from);
			if ($charset_from === 'cp1251' || $charset_from === 'cp1259') return strtr($data, self::$cp1259_table);
			if ($charset_to   === 'cp1251' || $charset_to   === 'cp1259') return strtr($data, array_flip(self::$cp1259_table));
			if ($charset_from === 'UTF-16' || $charset_from === 'UCS-2')  return self::_convert_from_utf16($data);
			trigger_error('Convert "' . $charset_from . '" --> "' . $charset_to . '" is not supported native, "iconv" or "mbstring" extension required', E_USER_WARNING);
			return false;
		}
		return $data;
	}

	/**
	 * Convert UTF-16 / UCS-2 encoding string to UTF-8.
	 * Surrogates UTF-16 are supported!
	 *
	 * Преобразует строку из кодировки UTF-16 / UCS-2 в UTF-8.
	 * Суррогаты UTF-16 поддерживаются!
	 *
	 * @param    string        $s
	 * @param    string        $type      'BE' -- big endian byte order
	 *                                    'LE' -- little endian byte order
	 * @param    bool          $to_array  returns array chars instead whole string?
	 * @return   string|array|bool        UTF-8 string, array chars or FALSE if error occured
	 */
	private static function _convert_from_utf16($s, $type = 'BE', $to_array = false)
	{
		static $types = array(
			'BE' => 'n',  #unsigned short (always 16 bit, big endian byte order)
			'LE' => 'v',  #unsigned short (always 16 bit, little endian byte order)
		);
		if (! array_key_exists($type, $types))
		{
			trigger_error('Unexpected value in 2-nd parameter, "' . $type . '" given!', E_USER_WARNING);
			return false;
		}
		#the fastest way:
		if (function_exists('iconv') || function_exists('mb_convert_encoding'))
		{
			if (function_exists('iconv'))                   $s = iconv('UTF-16' . $type, 'UTF-8', $s);
			elseif (function_exists('mb_convert_encoding')) $s = mb_convert_encoding($s, 'UTF-8', 'UTF-16' . $type);
			if (! $to_array) return $s;
			return self::str_split($s);
		}

		/*
        http://en.wikipedia.org/wiki/UTF-16

        The improvement that UTF-16 made over UCS-2 is its ability to encode
        characters in planes 1-16, not just those in plane 0 (BMP).

        UTF-16 represents non-BMP characters (those from U+10000 through U+10FFFF)
        using a pair of 16-bit words, known as a surrogate pair.
        First 1000016 is subtracted from the code point to give a 20-bit value.
        This is then split into two separate 10-bit values each of which is represented
        as a surrogate with the most significant half placed in the first surrogate.
        To allow safe use of simple word-oriented string processing, separate ranges
        of values are used for the two surrogates: 0xD800-0xDBFF for the first, most
        significant surrogate and 0xDC00-0xDFFF for the second, least significant surrogate.

        For example, the character at code point U+10000 becomes the code unit sequence 0xD800 0xDC00,
        and the character at U+10FFFD, the upper limit of Unicode, becomes the sequence 0xDBFF 0xDFFD.
        Unicode and ISO/IEC 10646 do not, and will never, assign characters to any of the code points
        in the U+D800-U+DFFF range, so an individual code value from a surrogate pair does not ever
        represent a character.

        http://www.russellcottrell.com/greek/utilities/SurrogatePairCalculator.htm
        http://www.russellcottrell.com/greek/utilities/UnicodeRanges.htm

        Conversion of a Unicode scalar value S to a surrogate pair <H, L>:
          H = Math.floor((S - 0x10000) / 0x400) + 0xD800;
          L = ((S - 0x10000) % 0x400) + 0xDC00;
        The conversion of a surrogate pair <H, L> to a scalar value:
          N = ((H - 0xD800) * 0x400) + (L - 0xDC00) + 0x10000;
		*/
		$a = array();
		$hi = false;
		foreach (unpack($types[$type] . '*', $s) as $codepoint)
		{
			#surrogate process
			if ($hi !== false)
			{
				$lo = $codepoint;
				if ($lo < 0xDC00 || $lo > 0xDFFF) $a[] = "\xEF\xBF\xBD"; #U+FFFD REPLACEMENT CHARACTER (for broken char)
				else
				{
					$codepoint = (($hi - 0xD800) * 0x400) + ($lo - 0xDC00) + 0x10000;
					$a[] = self::chr($codepoint);
				}
				$hi = false;
			}
			elseif ($codepoint < 0xD800 || $codepoint > 0xDBFF) $a[] = self::chr($codepoint); #not surrogate
			else $hi = $codepoint; #surrogate was found
		}
		return $to_array ? $a : implode('', $a);
	}

	/**
	 * Strips out device control codes in the ASCII range.
	 *
	 * @param   string|null       string to clean
	 * @return  string|bool|null  returns FALSE if error occured
	 */
	public static function strict($s)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;
		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]+/sSX', '', $s);
	}

	/**
	 * Проверка данных на принадлежность классу символов ASCII
	 * Для значений null, integer, float, boolean возвращает TRUE.
	 *
	 * Массивы обходятся рекурсивно, если в хотябы одном элементе массива
	 * его значение не ASCII, возвращается FALSE.
	 *
	 * @param   array|scalar|null  $data
	 * @return  bool
	 */
	public static function is_ascii($data)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_array($data))
		{
			foreach ($data as $k => &$v)
			{
				if (! self::is_ascii($k) || ! self::is_ascii($v)) return false;
			}
			return true;
		}
		#if (is_string($data)) return preg_match('/^[\x00-\x7f]*$/sSX', $data);
		if (is_string($data)) return ltrim($data, "\x00..\x7f") === '';  #small speed improve
		if (is_scalar($data) || is_null($data)) return true;  #~ null, integer, float, boolean
		return false; #object or resource
	}

	/**
	 * Returns true if data is valid UTF-8 and false otherwise.
	 * Для значений null, integer, float, boolean возвращает TRUE.
	 *
	 * Массивы обходятся рекурсивно, если в хотябы одном элементе массива
	 * его значение не в кодировке UTF-8, возвращается FALSE.
	 *
	 * @link    http://www.w3.org/International/questions/qa-forms-utf-8.html
	 * @link    http://ru3.php.net/mb_detect_encoding
	 * @link    http://webtest.philigon.ru/articles/utf8/
	 * @link    http://unicode.coeurlumiere.com/
	 * @param   array|scalar|null  $data
	 * @param   bool               $is_strict  строгая проверка диапазона ASCII?
	 * @return  bool
	 */
	public static function is_utf8($data, $is_strict = true)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_array($data))
		{
			foreach ($data as $k => &$v)
			{
				if (! self::is_utf8($k, $is_strict) || ! self::is_utf8($v, $is_strict)) return false;
			}
			return true;
		}
		if (is_string($data))
		{
			if (! preg_match('~~suSX', $data)) return false;
			if (function_exists('preg_last_error') && preg_last_error() !== PREG_NO_ERROR) return false;
			#NOTE preg_match('~~suSX') much faster (up to 4 times), then mb_check_encoding($data, 'UTF-8')!
			#if (function_exists('mb_check_encoding') && ! mb_check_encoding($data, 'UTF-8')) return false; #DEPRECATED
			if ($is_strict && preg_match('/[^\x09\x0A\x0D\x20-\xBF\xC2-\xF7]/sSX', $data)) return false;
			return true;
		}
		if (is_scalar($data) || is_null($data)) return true;  #~ null, integer, float, boolean
		return false; #object or resource
	}

	/**
	 * Tries to detect if a string is in Unicode encoding
	 *
	 * @deprecated  Slowly, use self::is_utf8() instead
	 * @see     self::is_utf8()
	 * @param   string   $s          текст
	 * @param   bool     $is_strict  строгая проверка диапазона ASCII?
	 * @return  bool
	 */
	public static function check($s, $is_strict = true)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		for ($i = 0, $len = strlen($s); $i < $len; $i++)
		{
			$c = ord($s[$i]);
			if ($c < 0x80) #1 byte  0bbbbbbb
			{
				if ($is_strict === false || ($c > 0x1F && $c < 0x7F) || $c == 0x09 || $c == 0x0A || $c == 0x0D) continue;
			}
			if (($c & 0xE0) == 0xC0) $n = 1; #2 bytes 110bbbbb 10bbbbbb
			elseif (($c & 0xF0) == 0xE0) $n = 2; #3 bytes 1110bbbb 10bbbbbb 10bbbbbb
			elseif (($c & 0xF8) == 0xF0) $n = 3; #4 bytes 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
			elseif (($c & 0xFC) == 0xF8) $n = 4; #5 bytes 111110bb 10bbbbbb 10bbbbbb 10bbbbbb 10bbbbbb
			elseif (($c & 0xFE) == 0xFC) $n = 5; #6 bytes 1111110b 10bbbbbb 10bbbbbb 10bbbbbb 10bbbbbb 10bbbbbb
			else return false; #does not match any model
			#n bytes matching 10bbbbbb follow ?
			for ($j = 0; $j < $n; $j++)
			{
				$i++;
				if ($i == $len || ((ord($s[$i]) & 0xC0) != 0x80) ) return false;
			}
		}
		return true;
	}

	/**
	 * Check the data in UTF-8 charset on given ranges of the standard UNICODE.
	 * The suitable alternative to regular expressions.
	 *
	 * Для значений null, integer, float, boolean возвращает TRUE.
	 *
	 * Массивы обходятся рекурсивно, если в хотябы одном элементе массива
	 * его значение не прошло проверку, возвращается FALSE.
	 *
	 * Examples:
	 *   #A simple check the standard named ranges:
	 *   UTF8::blocks_check('поисковые системы Google и Yandex', array('Basic Latin', 'Cyrillic'));
	 *   #You can check the named, direct ranges or codepoints together:
	 *   UTF8::blocks_check('поисковые системы Google и Yandex', array(array(0x20, 0x7E),     #[\x20-\x7E]
	 *                                                                 array(0x0410, 0x044F), #[A-Яa-я]
	 *                                                                 0x0401, #russian yo (Ё)
	 *                                                                 0x0451, #russian ye (ё)
	 *                                                                 'Arrows',
	 *                                                                ));
	 *
	 * @link    http://www.unicode.org/charts/
	 * @param   array|scalar|null  $data
	 * @param   array|string       $blocks
	 * @return  bool               Возвращает TRUE, если все символы из текста принадлежат указанным диапазонам
	 *                             и FALSE в противном случае или для разбитого UTF-8.
	 */
	public static function blocks_check($data, $blocks)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;

		if (is_array($data))
		{
			foreach ($data as $k => &$v)
			{
				if (! self::blocks_check($k, $blocks) || ! self::blocks_check($v, $blocks)) return false;
			}
			return true;
		}

		if (is_string($data))
		{
			$chars = self::str_split($data);
			if ($chars === false) return false; #broken UTF-8
			unset($data); #memory free
			$skip = array(); #кэшируем уже проверенные символы
			foreach ($chars as $i => $char)
			{
				if (array_key_exists($char, $skip)) continue; #speed improve
				$codepoint = self::ord($char);
				if ($codepoint === false) return false; #broken UTF-8
				$is_valid = false;
				$blocks = (array)$blocks;
				foreach ($blocks as $j => $block)
				{
					if (is_string($block))
					{
						if (! array_key_exists($block, self::$unicode_blocks))
						{
							trigger_error('Unknown block "' . $block . '"!', E_USER_WARNING);
							return false;
						}
						list ($min, $max) = self::$unicode_blocks[$block];
					}
					elseif (is_array($block)) list ($min, $max) = $block;
					elseif (is_int($block)) $min = $max = $block;
					else trigger_error('A string/array/int type expected for block[' . $j . ']!', E_USER_ERROR);
					if ($codepoint >= $min && $codepoint <= $max)
					{
						$is_valid = true;
						break;
					}
				}#foreach
				if (! $is_valid) return false;
				$skip[$char] = true;
			}#foreach
			return true;
		}
		if (is_scalar($data) || is_null($data)) return true;  #~ null, integer, float, boolean
		return false; #object or resource
	}

	/**
	 * Перекодирует значения элементов массивов $_GET, $_POST, $_COOKIE, $_REQUEST, $_FILES из кодировки $charset в UTF-8, если необходимо.
	 * Побочным положительным эффектом является защита от XSS атаки с непечатаемыми символами на уязвимые PHP функции.
	 * Т.о. веб-формы можно посылать на сервер в 2-х кодировках: $charset и UTF-8.
	 * Параметры для тестирования: ?тест[тест]=тест (можно просто дописать в адресную строку браузера IE >= 5.x)
	 *
	 * Алгоритм работы:
	 * 1) Функция проверяет массивы $_GET, $_POST, $_COOKIE, $_REQUEST, $_FILES
	 *    на корректность значений элементов кодировке UTF-8.
	 * 2) Значения не в UTF-8 принимаются как $charset и конвертируется в UTF-8,
	 *    при этом байты от 0x00 до 0x7F (ASCII) сохраняются как есть.
	 * 3) Сконвертированные значения снова проверяются.
	 *    Если данные опять не в кодировке UTF-8, то они считаются разбитыми и функция возвращает FALSE.
	 *
	 * ЗАМЕЧАНИЕ
	 *   Функция должна вызываться после self::unescape_request()!
	 *
	 * @see     self::unescape_request()
	 * @param   bool    $is_hex2bin  Декодировать HEX-данные?
	 *                               Пример: 0xd09ec2a0d0bad0bed0bcd0bfd0b0d0bdd0b8d0b8 => О компании
	 *                               Параметры в URL адресах иногда бывает удобно кодировать не функцией rawurlencode(),
	 *                               а использовать следующий механизм (к тому же кодирующий данные более компактно):
	 *                               '0x' . bin2hex($string)
	 * @param   string  $charset
	 * @return  bool                 Возвращает TRUE, если все значения элементов массивов в кодировке UTF-8
	 *                               и FALSE + E_USER_WARNING в противном случае.
	 */
	public static function autoconvert_request($is_hex2bin = false, $charset = 'cp1251')
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		$is_converted = false;
		$is_broken = false;
		foreach (array('_GET', '_POST', '_COOKIE', '_FILES') as $k => $v)
		{
			if (! array_key_exists($v, $GLOBALS)) continue;
			#использовать array_walk_recursive() не предоставляется возможным,
			#т.к. его callback функция не поддерживает передачу ключа по ссылке
			$GLOBALS[$v] = self::_autoconvert_request_recursive($GLOBALS[$v], $is_converted, $is_broken, $is_hex2bin, $charset);
			if ($is_broken)
			{
				trigger_error('Array $' . $v . ' does not have keys/values in UTF-8 charset!', E_USER_WARNING);
				return false;
			}
		}
		if ($is_converted)
		{
			$_REQUEST =
				(isset($_COOKIE) ? $_COOKIE : array()) +
				(isset($_POST) ? $_POST : array()) +
				(isset($_GET) ? $_GET : array());
		}
		return true;
	}

	private static function _autoconvert_request_recursive(&$data, &$is_converted, &$is_broken, $is_hex2bin, $charset)
	{
		if ($is_broken) return $data;  #speed improve
		if (is_array($data))
		{
			$d = array();
			foreach ($data as $k => &$v)
			{
				$k = self::_autoconvert_request($k, $is_converted, $is_broken, $is_hex2bin, $charset);
				if ($is_broken) return $data;  #speed improve
				$d[$k] = self::_autoconvert_request_recursive($v, $is_converted, $is_broken, $is_hex2bin, $charset);
				if ($is_broken) return $data;  #speed improve
			}
			return $d;
		}
		return self::_autoconvert_request($data, $is_converted, $is_broken, $is_hex2bin, $charset);
	}

	private static function _autoconvert_request(&$s, &$is_converted, &$is_broken, $is_hex2bin, $charset)
	{
		#regexp speed improve by using strpos()
		if ($is_hex2bin && strpos($s, '0x') === 0 && preg_match('/^0x((?:[\da-fA-F]{2})+)$/sSX', $s, $m))
		{
			$s = pack('H' . strlen($m[1]), $m[1]); #hex2bin()
			$is_converted = true;
		}
		if (! self::is_utf8($s))
		{
			$s = self::convert_from($s, $charset);
			if ($s === false) $is_broken = true;
			elseif (! self::is_utf8($s))
			{
				trigger_error('String 0x ' . substr(bin2hex($s), 0, 100) . '... is not UTF-8!', E_USER_WARNING);
				$is_broken = true;
			}
			else $is_converted = true;
		}
		return $s;
	}

	/**
	 * Сравнение строк
	 *
	 * @param   string|null    $s1
	 * @param   string|null    $s2
	 * @param   string         $locale   For example, 'en_CA', 'ru_RU'
	 * @return  int|bool|null  Returns FALSE if error occured
	 *                         Returns < 0 if $s1 is less than $s2;
	 *                                 > 0 if $s1 is greater than $s2;
	 *                                 0 if they are equal.
	 */
	public static function strcmp($s1, $s2, $locale = '')
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s1) || is_null($s2)) return null;
		if (! function_exists('collator_create')) return strcmp($s1, $s2);
		# PHP 5 >= 5.3.0, PECL intl >= 1.0.0
		# If empty string ("") or "root" are passed, UCA rules will be used.
		$c = new Collator($locale);
		if (! $c)
		{
			# Returns an "empty" object on error. You can use intl_get_error_code() and/or intl_get_error_message() to know what happened.
			trigger_error(intl_get_error_message(), E_USER_WARNING);
			return false;
		}
		return $c->compare($s1, $s2);
	}

	/**
	 * Сравнение строк для N первых символов
	 *
	 * @param   string|null    $s1
	 * @param   string|null    $s2
	 * @param   int            $length
	 * @return  int|bool|null  Returns FALSE if error occured
	 *                         Returns < 0 if $s1 is less than $s2;
	 *                                 > 0 if $s1 is greater than $s2;
	 *                                 0 if they are equal.
	 */
	public static function strncmp($s1, $s2, $length)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s1) || is_null($s2)) return null;
		return self::strcmp(self::substr($s1, 0, $length), self::substr($s2, 0, $length));
	}

	/**
	 * Implementation strcasecmp() function for UTF-8 encoding string.
	 *
	 * @param   string|null    $s1
	 * @param   string|null    $s2
	 * @return  int|bool|null  Returns FALSE if error occured
	 *                         Returns < 0 if $s1 is less than $s2;
	 *                                 > 0 if $s1 is greater than $s2;
	 *                                 0 if they are equal.
	 */
	public static function strcasecmp($s1, $s2)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s1) || is_null($s2)) return null;
		return self::strcmp(self::lowercase($s1), self::lowercase($s2));
	}

	/**
	 * Converts a UTF-8 character to a UNICODE codepoint
	 *
	 * @param   string|null    $char  UTF-8 character
	 * @return  int|bool|null         Unicode codepoint
	 *                                Returns FALSE if $char broken (not UTF-8)
	 */
	public static function ord($char) # = UTF8::to_unicode() or unicode_from_utf8()
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($char)) return $char;

		static $cache = array();
		if (array_key_exists($char, $cache)) return $cache[$char]; #speed improve

		#TODO проверить скорость работы
		if (0)
		{
			$s = null;
			if (function_exists('iconv')) $s = @iconv('UTF-8', 'UCS-4BE', $char);
			elseif (function_exists('mb_convert_encoding')) $s = @mb_convert_encoding($char, 'UCS-4BE', 'UTF-8');
			if (is_string($s)) return reset(unpack('N', $s));
			if ($s !== null) return false;
		}

		switch (strlen($char))
		{
			case 1 : return $cache[$char] = ord($char);
			case 2 : return $cache[$char] = (ord($char{1}) & 63) |
											((ord($char{0}) & 31) << 6);
			case 3 : return $cache[$char] = (ord($char{2}) & 63) |
											((ord($char{1}) & 63) << 6) |
											((ord($char{0}) & 15) << 12);
			case 4 : return $cache[$char] = (ord($char{3}) & 63) |
											((ord($char{2}) & 63) << 6) |
											((ord($char{1}) & 63) << 12) |
											((ord($char{0}) & 7)  << 18);
			default :
				trigger_error('Character 0x ' . bin2hex($char) . ' is not UTF-8!', E_USER_WARNING);
				return false;
		}
	}

	/**
	 * Converts a UNICODE codepoint to a UTF-8 character
	 *
	 * @param   int|digit|null  $cp  Unicode codepoint
	 * @return  string|null          UTF-8 character
	 */
	public static function chr($cp) # = from_unicode() or unicode_to_utf8()
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($cp)) return $cp;

		static $cache = array();
		if (array_key_exists($cp, $cache)) return $cache[$cp]; #speed improve

		#TODO проверить скорость работы
		if (0)
		{
			$s = null;
			if (function_exists('iconv')) $s = @iconv('UCS-4BE', 'UTF-8', pack('N', $cp));
			elseif (function_exists('mb_convert_encoding')) $s = mb_convert_encoding(pack('N', $cp), 'UTF-8', 'UCS-4BE');
			if (is_string($s)) return $s;
			#U+FFFD REPLACEMENT CHARACTER
			if ($s !== null) return $cache[$cp] = "\xEF\xBF\xBD";
		}

		if ($cp <= 0x7f)     return $cache[$cp] = chr($cp);
		if ($cp <= 0x7ff)    return $cache[$cp] = chr(0xc0 | ($cp >> 6))  .
												  chr(0x80 | ($cp & 0x3f));
		if ($cp <= 0xffff)   return $cache[$cp] = chr(0xe0 | ($cp >> 12)) .
												  chr(0x80 | (($cp >> 6) & 0x3f)) .
												  chr(0x80 | ($cp & 0x3f));
		if ($cp <= 0x10ffff) return $cache[$cp] = chr(0xf0 | ($cp >> 18)) .
												  chr(0x80 | (($cp >> 12) & 0x3f)) .
												  chr(0x80 | (($cp >> 6) & 0x3f)) .
												  chr(0x80 | ($cp & 0x3f));
		#U+FFFD REPLACEMENT CHARACTER
		return $cache[$cp] = "\xEF\xBF\xBD";
	}

	/**
	 * Implementation chunk_split() function for UTF-8 encoding string.
	 *
	 * @param   string|null       $s
	 * @param   int|digit|null    $length
	 * @param   string|null       $glue
	 * @return  string|bool|null  returns FALSE if error occured
	 */
	public static function chunk_split($s, $length = null, $glue = null)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		$length = intval($length);
		$glue   = strval($glue);
		if ($length < 1) $length = 76;
		if ($glue === '') $glue = "\r\n";
		if (! is_array($a = self::str_split($s, $length))) return false;
		return implode($glue, $a);
	}

	/**
	 * Changes all keys in an array
	 *
	 * @param   array|null       $a
	 * @param   int              $mode  {CASE_LOWER|CASE_UPPER}
	 * @return  array|bool|null  returns FALSE if error occured
	 */
	public static function array_change_key_case($a, $mode)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (! is_array($a)) return $a;
		$a2 = array();
		foreach ($a as $k => $v)
		{
			if (is_string($k))
			{
				$k = self::convert_case($k, $mode);
				if ($k === false) return false;
			}
			$a2[$k] = $v;
		}
		return $a2;
	}

	/**
	 * Конвертирует регистр букв в данных в кодировке UTF-8.
	 * Массивы обходятся рекурсивно, при этом конвертируются только значения
	 * в элементах массива, а ключи остаются без изменений.
	 * Для конвертирования только ключей используйте метод self::array_change_key_case().
	 *
	 * @see     self::array_change_key_case()
	 * @link    http://www.unicode.org/charts/PDF/U0400.pdf
	 * @link    http://ru.wikipedia.org/wiki/ISO_639-1
	 * @param   array|scalar|null $data  Данные произвольной структуры
	 * @param   int               $mode  {CASE_LOWER|CASE_UPPER}
	 * @return  scalar|bool|null  returns FALSE if error occured
	 */
	public static function convert_case($data, $mode)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;

		if (is_array($data))
		{
			foreach ($data as $k => &$v) $v = self::convert_case($v, $mode);
			return $data;
		}
		if (! is_string($data)) return $data;
		if ($mode === CASE_UPPER)
		{
			if (! $data) return $data;
			if (self::is_ascii($data)) return strtoupper($data); #speed improve!
			if (function_exists('mb_strtoupper')) return mb_strtoupper($data, 'utf-8');
			return strtr($data, array_flip(self::$convert_case_table));
		}
		if ($mode === CASE_LOWER)
		{
			if (! $data) return $data;
			if (self::is_ascii($data)) return strtolower($data); #speed improve!
			if (function_exists('mb_strtolower')) return mb_strtolower($data, 'utf-8');
			return strtr($data, self::$convert_case_table);
		}
		trigger_error('Parameter 2 should be a constant of CASE_LOWER or CASE_UPPER!', E_USER_WARNING);
		return $data;
	}

	/**
	 * Конвертирует данные в нижний регистр.
	 *
	 * @param   array|scalar|null  $data
	 * @return  scalar|bool|null   Returns FALSE if error occured
	 */
	public static function lowercase($data)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		return self::convert_case($data, CASE_LOWER);
	}

	/**
	 * Конвертирует данные в верхний регистр.
	 *
	 * @param   array|scalar|null  $data
	 * @return  scalar|null        Returns FALSE if error occured
	 */
	public static function uppercase($data)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		return self::convert_case($data, CASE_UPPER);
	}


	/**
	 * Convert all HTML entities to native UTF-8 characters
	 * Функция декодирует гораздо больше именованных сущностей, чем стандартная html_entity_decode()
	 * Все dec и hex сущности так же переводятся в UTF-8.
	 *
	 * Example: '&quot;' or '&#34;' or '&#x22;' will be converted to '"'.
	 *
	 * @link    http://www.htmlhelp.com/reference/html40/entities/
	 * @link    http://www.alanwood.net/demos/ent4_frame.html (HTML 4.01 Character Entity References)
	 * @link    http://msdn.microsoft.com/workshop/author/dhtml/reference/charsets/charset1.asp?frame=true
	 * @link    http://msdn.microsoft.com/workshop/author/dhtml/reference/charsets/charset2.asp?frame=true
	 * @link    http://msdn.microsoft.com/workshop/author/dhtml/reference/charsets/charset3.asp?frame=true
	 *
	 * @param   scalar|null  $s
	 * @param   bool         $is_special_chars   Дополнительно обрабатывать специальные html сущности? (&lt; &gt; &amp; &quot;)
	 * @return  scalar|null  returns FALSE if error occured
	 */
	public static function html_entity_decode($s, $is_special_chars = false)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (! is_string($s)) return $s;

		#оптимизация скорости
		if (strlen($s) < 4  #по минимальной длине сущности - 4 байта: &#d; &xx;
			|| ($pos = strpos($s, '&') === false) || strpos($s, ';', $pos) === false) return $s;

		$table = self::$html_entity_table;
		if ($is_special_chars) $table += self::$html_special_chars_table;

		#заменяем именованные сущности
		#оптимизация скорости: заменяем только те сущности, которые используются в html коде!
		#эта часть кода работает быстрее, чем $s = strtr($s, $table);
		if (preg_match_all('/&[a-zA-Z]++\d*+;/sSX', $s, $m, null, $pos))
		{
			foreach (array_unique($m[0]) as $entity)
			{
				if (array_key_exists($entity, $table)) $s = str_replace($entity, $table[$entity], $s);
			}
		}

		#заменяем числовые dec и hex сущности:
		if (strpos($s, '&#') !== false)  #speed improve
		{
			$class = __CLASS__;
			$html_special_chars_table_flip = array_flip(self::$html_special_chars_table);
			$s = preg_replace_callback('/&#((x)[\da-fA-F]{1,6}+|\d{1,7}+);/sSX', 
										function (array $m) use ($class, $html_special_chars_table_flip, $is_special_chars)
										{
											$codepoint = isset($m[2]) && $m[2] === 'x' ? hexdec($m[1]) : $m[1];
											if (! $is_special_chars)
											{
												$char = pack('C', $codepoint);
												if (array_key_exists($char, $html_special_chars_table_flip)) return $html_special_chars_table_flip[$char];
											}
											return $class::chr($codepoint);
										}, $s);
		}
		return $s;
	}

	/**
	 * Convert special UTF-8 characters to HTML entities.
	 * Функция кодирует гораздо больше именованных сущностей, чем стандартная htmlentities()
	 *
	 * @link  http://www.htmlhelp.com/reference/html40/entities/
	 * @link  http://www.alanwood.net/demos/ent4_frame.html (HTML 4.01 Character Entity References)
	 * @link  http://msdn.microsoft.com/workshop/author/dhtml/reference/charsets/charset1.asp?frame=true
	 * @link  http://msdn.microsoft.com/workshop/author/dhtml/reference/charsets/charset2.asp?frame=true
	 * @link  http://msdn.microsoft.com/workshop/author/dhtml/reference/charsets/charset3.asp?frame=true
	 *
	 * @param   scalar|null  $s
	 * @param   bool         $is_special_chars_only          Обрабатывать только специальные html сущности? (&lt; &gt; &amp; &quot;)
	 * @return  scalar|null  Returns FALSE if error occured
	 */
	public static function html_entity_encode($s, $is_special_chars_only = false)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (! is_string($s)) return $s;

		#if ($is_special_chars_only) return strtr($s, array_flip(self::$html_special_chars_table));
		if ($is_special_chars_only) return htmlspecialchars($s);

		$table = array_flip(self::$html_entity_table);

		#заменяем UTF-8 символы на именованные сущности:
		#оптимизация скорости: заменяем только те символы, которые используются в html коде!
		if (preg_match_all('~(?>	[\xc2\xc3\xc5\xc6\xcb\xce\xcf][\x80-\xbf]  #2 bytes
								|	\xe2[\x80-\x99][\x82-\xac]                 #3 bytes
							  )
                            ~sxSX', $s, $m))
		{
			foreach (array_unique($m[0]) as $char)
			{
				if (array_key_exists($char, $table)) $s = str_replace($char, $table[$char], $s);
			}
		}

		return $s;
	}

	/**
	 * Call preg_match_all() and convert byte offsets into character offsets for PREG_OFFSET_CAPTURE flag.
	 * This is regardless of whether you use /u modifier.
	 *
	 * @link  http://bolknote.ru/2010/09/08/~2704
	 *
	 * @param   string           $pattern
	 * @param   string|null      $subject
	 * @param   array            $matches
	 * @param   int              $flags
	 * @param   int              $char_offset
	 * @return  array|bool|null  returns FALSE if error occured
	 */
	public static function preg_match_all($pattern, $subject, &$matches, $flags = PREG_PATTERN_ORDER, $char_offset = 0)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($subject)) return null;

		$byte_offset = ($char_offset > 0) ? strlen(self::substr($subject, 0, $char_offset)) : $char_offset;

		$return = preg_match_all($pattern, $subject, $matches, $flags, $byte_offset);
		if ($return === false) return false;

		if ($flags & PREG_OFFSET_CAPTURE)
		{
			foreach ($matches as &$match)
			{
				foreach ($match as &$a) $a[1] = self::strlen(substr($subject, 0, $a[1]));
			}
		}

		return $return;
	}

	#alias for self::str_limit()
	public static function truncate($s, $maxlength = null, $continue = "\xe2\x80\xa6", &$is_cutted = null, $tail_min_length = 20)
	{
		return self::str_limit($s, $maxlength, $continue, &$is_cutted, $tail_min_length);
	}

	/**
	 * Обрезает текст в кодировке UTF-8 до заданной длины,
	 * причём последнее слово показывается целиком, а не обрывается на середине.
	 * Html сущности корректно обрабатываются.
	 *
	 * @param   string|null     $s                Текст в кодировке UTF-8
	 * @param   int|null|digit  $maxlength        Ограничение длины текста
	 * @param   string          $continue         Завершающая строка, которая будет вставлена после текста, если он обрежется
	 * @param   bool|null       &$is_cutted       Текст был обрезан?
	 * @param   int|digit       $tail_min_length  Если длина "хвоста", оставшегося после обрезки текста, меньше $tail_min_length,
	 *                                            то текст возвращается без изменений
	 * @return  string|bool|null                  Returns FALSE if error occured
	 */
	public static function str_limit($s, $maxlength = null, $continue = "\xe2\x80\xa6", &$is_cutted = null, $tail_min_length = 20) #"\xe2\x80\xa6" = "&hellip;"
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		$is_cutted = false;
		if ($continue === null) $continue = "\xe2\x80\xa6";
		if (! $maxlength) $maxlength = 256;

		#оптимизация скорости:
		#{{{
		if (strlen($s) <= $maxlength) return $s;
		$s2 = str_replace("\r\n", '?', $s);
		$s2 = preg_replace('/&(?> [a-zA-Z][a-zA-Z\d]+
                                | \#(?> \d{1,4}
                                      | x[\da-fA-F]{2,4}
                                    )
                              );  # html сущности (&lt; &gt; &amp; &quot;)
                            /sxSX', '?', $s2);
		if (strlen($s2) <= $maxlength || self::strlen($s2) <= $maxlength) return $s;
		#}}}

		preg_match_all('/(?> \r\n   # переносы строк
                           | &(?> [a-zA-Z][a-zA-Z\d]+
                                | \#(?> \d{1,4}
                                      | x[\da-fA-F]{2,4}
                                    )
                              );  # html сущности (&lt; &gt; &amp; &quot;)
                           | .
                         )
                        /sxuSX', $s, $m);
		#d($m);
		if (count($m[0]) <= $maxlength) return $s;

		$left = implode('', array_slice($m[0], 0, $maxlength));
		#из диапазона ASCII исключаем буквы, цифры, открывающие парные символы [a-zA-Z\d\(\{\[] и некоторые др. символы
		#нельзя вырезать в конце строки символ ";", т.к. он используются в сущностях &xxx;
		$left2 = rtrim($left, "\x00..\x28\x2A..\x2F\x3A\x3C..\x3E\x40\x5B\x5C\x5E..\x60\x7B\x7C\x7E\x7F");
		if (strlen($left) !== strlen($left2)) $return = $left2 . $continue;
		else
		{
			#добавляем остаток к обрезанному слову
			$right = implode('', array_slice($m[0], $maxlength));
			preg_match('/^(?> [\d\)\]\}\-\.:]+  #английские буквы или цифры, закрывающие парные символы, дефис для составных слов, дата, время, IP-адреса, URL типа www.ya.ru:80!
                            | \p{L}+        #буквы
                            | \xe2\x80\x9d  #закрывающие кавычки
                            | \xe2\x80\x99  #закрывающие кавычки
							| \xe2\x80\x9c  #закрывающие кавычки
							| \xc2\xbb      #закрывающие кавычки
                          )+
                        /suxSX', $right, $m);
			#d($m);
			$right = isset($m[0]) ? rtrim($m[0], '.-') : '';
			$return = $left . $right;
			if (strlen($return) !== strlen($s)) $return .= $continue;
		}
		if (self::strlen($s) - self::strlen($return) < $tail_min_length) return $s;

		$is_cutted = true;
		return $return;
	}

	/**
	 * Implementation str_split() function for UTF-8 encoding string.
	 *
	 * @param   string|null      $s
	 * @param   int|null|digit   $length
	 * @return  array|bool|null  returns FALSE if error occured
	 */
	public static function str_split($s, $length = null)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		$length = ($length === null) ? 1 : intval($length);
		if ($length < 1) return false;
		#there are limits in regexp for {min,max}!
		if (preg_match_all('~.~suSX', $s, $m) === false) return false;
		if (function_exists('preg_last_error') && preg_last_error() !== PREG_NO_ERROR) return false;
		if ($length === 1) $a = $m[0];
		else
		{
			$a = array();
			for ($i = 0, $c = count($m[0]); $i < $c; $i += $length) $a[] = implode('', array_slice($m[0], $i, $length));
		}
		return $a;
	}

	/**
	 * Implementation strlen() function for UTF-8 encoding string.
	 *
	 * @param   string|null    $s
	 * @return  int|bool|null  returns FALSE if error occured
	 */
	public static function strlen($s)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;
		/*
          The fastest!
          utf8_decode() converts characters that are not in ISO-8859-1 to '?', which, for the purpose of counting, is quite alright.
          It's much faster than iconv_strlen()
          Note: this function does not count bad UTF-8 bytes in the string - these are simply ignored
		*/
		return strlen(utf8_decode($s));

		/*
        #DEPRECATED, speed less!
        if (function_exists('mb_strlen')) return mb_strlen($s, 'utf-8');
        if (function_exists('iconv_strlen')) return iconv_strlen($s, 'utf-8');

        #Do not count UTF-8 continuation bytes
        #return strlen(preg_replace('/[\x80-\xBF]/sSX', '', $s));

        #Тесты показали, что этот способ работает медленнее, чем хак через utf8_decode()
        preg_match_all('~.~suSX', $str, $m);
        return count($m[0]);

        #Тесты показали, что этот способ работает медленнее, чем через регулярное выражение!
        $n = 0;
        for ($i = 0, $len = strlen($s); $i < $len; $i++)
        {
            $c = ord(substr($s, $i, 1));
            if ($c < 0x80) $n++;                 #single-byte (0xxxxxx)
            elseif (($c & 0xC0) == 0xC0) $n++;   #multi-byte starting byte (11xxxxxx)
        }
        return $n;
		*/
	}

	/**
	 * Implementation strpos() function for UTF-8 encoding string
	 *
	 * @param   string|null    $s       The entire string
	 * @param   string|int     $needle  The searched substring
	 * @param   int|null       $offset  The optional offset parameter specifies the position from which the search should be performed
	 * @return  int|bool|null           Returns the numeric position of the first occurrence of needle in haystack.
	 *                                  If needle is not found, self::strpos() will return FALSE.
	 */
	public static function strpos($s, $needle, $offset = null)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		if ($offset === null || $offset < 0) $offset = 0;
		if (function_exists('iconv_strpos')) return iconv_strpos($s, $needle, $offset, 'utf-8');
		if (function_exists('mb_strpos')) return mb_strpos($s, $needle, $offset, 'utf-8');
		$byte_pos = $offset;
		do if (($byte_pos = strpos($s, $needle, $byte_pos)) === false) return false;
		while (($char_pos = self::strlen(substr($s, 0, $byte_pos++))) < $offset);
		return $char_pos;
	}

	/**
	 * Implementation strrev() function for UTF-8 encoding string
	 *
	 * @param   string|null       $s
	 * @return  string|bool|null  returns FALSE if error occured
	 */
	public static function strrev($s)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		if (0) #TODO протестировать скорость работы
		{
			$s = self::_convert($s, 'UTF-8', 'UTF-32');
			if (! is_string($s)) return false;
			$s = implode('', array_reverse(str_split($s, 4)));
			return self::_convert($s, 'UTF-32', 'UTF-8');
		}

		if (! is_array($a = self::str_split($s))) return false;
		return implode('', array_reverse($a));
	}

	/**
	 * Implementation substr() function for UTF-8 encoding string.
	 *
	 * @link     http://www.w3.org/International/questions/qa-forms-utf-8.html
	 * @param    string|null       $s
	 * @param    int|digit         $offset
	 * @param    int|null|digit    $length
	 * @param    bool              $in_cycle  speed improve for calling this method in cycles with the same $str and different $offset/$length!
	 * @return   string|bool|null             returns FALSE if error occured
	 */
	public static function substr($s, $offset, $length = null, $in_cycle = false)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		if (! $in_cycle)
		{
			if ($length === null) $length = self::strlen($s);
			#try to find standard functions, iconv_substr() faster then mb_substr()!
			if (function_exists('iconv_substr')) return iconv_substr($s, $offset, $length, 'utf-8');
			if (function_exists('mb_substr')) return mb_substr($s, $offset, $length, 'utf-8');
		}

		static $_s = null;
		static $_a = null;

		if ($_s !== $s) $_a = self::str_split($_s = $s);
		if (! is_array($_a)) return false;
		if ($length !== null) $a = array_slice($_a, $offset, $length);
		else                  $a = array_slice($_a, $offset);
		return implode('', $a);
	}

	/**
	 * Implementation substr_replace() function for UTF-8 encoding string.
	 *
	 * @param   string|null       $s
	 * @param   string|int        $replacement
	 * @param   int|digit         $start
	 * @param   int|null          $length
	 * @return  string|bool|null  returns FALSE if error occured
	 */
	public static function substr_replace($s, $replacement, $start, $length = null)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		if (! is_array($a = self::str_split($s))) return false;
		array_splice($a, $start, $length, $replacement);
		return implode('', $a);
	}

	/**
	 * Implementation ucfirst() function for UTF-8 encoding string.
	 * Преобразует первый символ строки в кодировке UTF-8 в верхний регистр.
	 *
	 * @param   string|null       $s
	 * @param   bool              $is_other_to_lowercase  остальные символы преобразуются в нижний регистр?
	 * @return  string|bool|null  returns FALSE if error occured
	 */
	public static function ucfirst($s, $is_other_to_lowercase = true)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		if ($s === '' || ! is_string($s)) return $s;
		if (! preg_match('/^(.)(.*)$/suSX', $s, $m)) return false;
		return self::uppercase($m[1]) . ($is_other_to_lowercase ? self::lowercase($m[2]) : $m[2]);
	}

	/**
	 * Implementation ucwords() function for UTF-8 encoding string.
	 * Преобразует в верхний регистр первый символ каждого слова в строке в кодировке UTF-8,
	 * остальные символы каждого слова преобразуются в нижний регистр.
	 *
	 * @param   string|null       $s
	 * @param   bool              $is_other_to_lowercase  остальные символы преобразуются в нижний регистр?
	 * @param   string            $spaces_re
	 * @return  string|bool|null  returns FALSE if error occured
	 */
	public static function ucwords($s, $is_other_to_lowercase = true, $spaces_re = '~([\pZ\s]+)~suSX') #\pXps is POSIX space: property Z or tab, NL, VT, FF, CR
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		$words = preg_split($spaces_re, $s, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		foreach ($words as $k => $word)
		{
			$words[$k] = self::ucfirst($word, $is_other_to_lowercase = true);
			if ($words[$k] === false) return false;
		}
		return implode('', $words);
	}

	/**
	 * Функция декодирует строку в формате %uXXXX или %u{XXXXXX} в строку формата UTF-8.
	 *
	 * Функция используется для декодирования данных типа "%u0442%u0435%u0441%u0442",
	 * закодированных устаревшей функцией javascript://encode().
	 * Рекомендуется использовать функцию javascript://encodeURIComponent().
	 *
	 * ЗАМЕЧАНИЕ
	 * Устаревший формат %uXXXX позволяет использовать юникод только из диапазона UCS-2, т.е. от U+0 до U+FFFF
	 *
	 * @param   scalar|array|null  $data
	 * @param   bool               $is_rawurlencode
	 * @return  scalar|array|null  returns FALSE if error occured
	 */
	public static function unescape($data, $is_rawurlencode = false)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_array($data))
		{
			$d = array();
			foreach ($data as $k => &$v)
			{
				$k = self::unescape($k, $is_rawurlencode);
				if ($k === false) return false;
				$d[$k] = self::unescape($v, $is_rawurlencode);
				if ($d[$k] === false && ! is_bool($v)) return false;
			}
			return $d;
		}
		if (is_string($data))
		{
			if (strpos($data, '%u') === false) return $data; #use strpos() for speed improving
			return preg_replace_callback('/%u(  [\da-fA-F]{4}+          #%uXXXX     only UCS-2
                                              | \{ [\da-fA-F]{1,6}+ \}  #%u{XXXXXX} extended form for all UNICODE charts
                                             )
                                          /sxSX', array('self', $is_rawurlencode ? '_unescape_rawurlencode' : '_unescape'), $data);
		}
		if (is_scalar($data) || is_null($data)) return $data;  #~ null, integer, float, boolean
		return false; #object or resource
	}

	private static function _unescape(array $m)
	{
		$codepoint = hexdec(trim($m[1], '{}'));
		return self::chr($codepoint);
	}

	private static function _unescape_rawurlencode(array $m)
	{
		return rawurlencode(self::_unescape($m));
	}

	/**
	 * 1) Корректирует глобальные массивы $_GET, $_POST, $_COOKIE, $_REQUEST
	 *    декодируя значения в юникоде "%uXXXX" и %u{XXXXXX}, закодированные, например, через устаревшую функцию javascript escape()
	 *    Cтандартный PHP 5.2.x этого делать не умеет.
	 * 2) Если в HTTP_COOKIE есть параметры с одинаковым именем, то берётся последнее значение, а не первое, как в QUERY_STRING.
	 * 3) Создаёт массив $_POST для нестандартных Content-Type, например, "Content-Type: application/octet-stream".
	 *    Стандартный PHP 5.2.x создаёт массив только для "Content-Type: application/x-www-form-urlencoded" и "Content-Type: multipart/form-data".
	 *
	 * Сессии, куки и независимая авторизация на поддоменах.
	 *
	 * ПРИМЕР 1
	 * У рабочего сайта http://domain.com появились поддомены.
	 * Для кроссдоменной авторизации через механизм сессий имя хоста для COOKIE было изменено с "domain.com" на ".domain.com"
	 * В результате авторизация не работает.
	 * Помогает очистка COOKIE, но их принудительная очистка на тысячах пользовательских компьютеров проблематична.
	 * Проблема в следующем: если в HTTP_COOKIE есть параметры с одинаковым именем, то берётся последнее значение,
	 * а не первое, как в QUERY_STRING.
	 * Более подробное описание:
	 * PHP не правильно (?) обрабатывает заголовок HTTP_COOKIE, если там встречаются параметры с одинаковым именем, но разными значениями.
	 * Пример запроса HTTP-заголовка клиентом: "Cookie: sid=chpgs2fiak-330mzqza; sid=cmz5tnp5zz-xlbbgqp"
	 * В этом случае сервер берёт первое значение, а не последнее.
	 * Хотя если в QUERY_STRING есть такая ситуация, всегда берётся последний параметр.
	 * В HTTP_COOKIE два параметра с одинаковым именем могут появиться, если отправить клиенту следующие HTTP-заголовки:
	 * "Set-Cookie: sid=chpgs2fiak-330mzqza; expires=Thu, 15 Oct 2009 14:23:42 GMT; path=/; domain=domain.com"  (только domain.com)
	 * "Set-Cookie: sid=cmz6uqorzv-1bn35110; expires=Thu, 15 Oct 2009 14:23:42 GMT; path=/; domain=.domain.com" (domain.com и все его поддомены)
	 * Решение: поменять имя сессии.
	 *
	 * ПРИМЕР 2
	 * Есть рабочие сайты: http://domain.com (основной), http://admin.domain.com (админка),
	 * http://sub1.domain.com (подпроект 1), http://sub2.domain.com, (подпроект 2).
	 * Так же имеется сервер разработки http://dev.domain.com, на котором м. б. свои поддомены.
	 * Требуется сделать независимую кросс-доменную авторизацию для http://*.domain.com и http://*.dev.domain.com.
	 * Для сохранения статуса авторизации будем использовать сессию, имя и значение которой пишется в COOKIE.
	 * Т. к. домены http://*.dev.domain.com имеют пересечение с доменами http://*.domain.com,
	 * для независимой авторизации	нужно использовать разные имена сессий.
	 * Пример HTTP заголовков ответа сервера:
	 * "Set-Cookie: sid=chpgs2fiak-330mzqza; expires=Thu, 15 Oct 2009 14:23:42 GMT; path=/; domain=.domain.com" (.domain.com и все его поддомены)
	 * "Set-Cookie: sid.dev=cmz6uqorzv-1bn35110; expires=Thu, 15 Oct 2009 14:23:42 GMT; path=/; domain=.dev.domain.com" (dev.domain.com и все его поддомены)
	 *
	 * @link    http://tools.ietf.org/html/rfc2965  RFC 2965 - HTTP State Management Mechanism
	 * @return  void
	 */
	public static function unescape_request()
	{
		$fixed = false;
        #ATTENTION! HTTP_RAW_POST_DATA is only accessible when Content-Type of POST request is NOT default "application/x-www-form-urlencoded"!
		$HTTP_RAW_POST_DATA = isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' ? (isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : @file_get_contents('php://input')) : null;
		if (ini_get('always_populate_raw_post_data')) $GLOBALS['HTTP_RAW_POST_DATA'] = $HTTP_RAW_POST_DATA;
		foreach (array( '_GET'    => isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null,
						'_POST'   => $HTTP_RAW_POST_DATA,
						'_COOKIE' => isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : null,
						) as $k => $v)
		{
			if (! is_string($v)) continue;
			if ($k === '_COOKIE')
			{
				$v = preg_replace('/; *+/sSX', '&', $v);
				unset($_COOKIE); #будем парсить HTTP_COOKIE сами, чтобы сделать обработку как у QUERY_STRING
			}
			if (strpos($v, '%u') !== false)
			{
				parse_str(self::unescape($v, $is_rawurlencode = true), $GLOBALS[$k]);
				$fixed = true;
				continue;
			}
			if (array_key_exists($k, $GLOBALS)) continue;
			parse_str($v, $GLOBALS[$k]);
			$fixed = true;
		}
		if ($fixed)
		{
			$_REQUEST =
				(isset($_COOKIE) ? $_COOKIE : array()) +
				(isset($_POST) ? $_POST : array()) +
				(isset($_GET) ? $_GET : array());
		}
	}

	/**
	 * Вычисляет высоту области редактирования текста (<textarea>) по значению и ширине.
	 *
	 * В большинстве случаев будет корректно работать для моноширинных шрифтов.
	 * Т.к. браузер переносит последнее слово, которое не умещается на строке,
	 * на следующую строку, высота м.б. меньше ожидаемой.
	 * Этот алгоритм явл. простым (и быстрым) и не отслеживает переносы слов.
	 *
	 * @param   string|null     $s         текст
	 * @param   int|digit       $cols      ширина области редактирования (колонок)
	 * @param   int|digit       $min_rows  минимальное кол-во строк
	 * @param   int|digit       $max_rows  максимальное кол-во строк
	 * @return  int|bool|null
	 */
	public static function textarea_rows($s, $cols, $min_rows = 3, $max_rows = 32)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		if (strlen($s) == 0) return $min_rows;  #speed improve
		$rows = 0;
		#utf8_decode() converts characters that are not in ISO-8859-1 to '?'
		foreach (preg_split('/\r\n|[\r\n]/sSX', utf8_decode($s)) as $line)
		{
			$rows += ceil((strlen($line) + 1) / $cols);
			if ($rows > $max_rows) return $max_rows;
		}
		return ($rows < $min_rows) ? $min_rows : $rows;
	}

	/**
	 * @param   string|null       $s
	 * @param   string|null       $charlist
	 * @return  string|bool|null
	 */
	public static function ltrim($s, $charlist = null)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;
		if ($charlist === null || self::is_ascii($charlist)) return ltrim($s);
		return preg_replace('~^[' . self::_preg_quote_class($charlist, '~') . ']+~uSX', '', $s);
	}

	/**
	 * @param   string|null       $s
	 * @param   string|null       $charlist
	 * @return  string|bool|null
	 */
	public static function rtrim($s, $charlist = null)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;
		if ($charlist === null || self::is_ascii($charlist)) return rtrim($s);
		return preg_replace('~[' . self::_preg_quote_class($charlist, '~') . ']+$~uSX', '', $s);
	}

	/**
	 * @param   scalar|null  $s
	 * @param   string|null  $charlist
	 * @return  scalar|null
	 */
	public static function trim($s, $charlist = null)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;
		if ($charlist === null || self::is_ascii($charlist)) return trim($s);
		$charlist_re = self::_preg_quote_class($charlist, '~');
		$s = preg_replace('~^[' . $charlist_re . ']+~uSX', '', $s);
		return preg_replace('~[' . $charlist_re . ']+$~uSX', '', $s);
	}

	private static function _preg_quote_class($charlist, $delimiter = null)
	{
		#return preg_quote($charlist, $delimiter); #DEPRECATED
		$quote_table = array(
			'\\' => '\\\\',
			'-'  => '\-',
			']'  => '\]',
		);
		if (is_string($delimiter)) $quote_table[$delimiter] = '\\' . $delimiter;
		return strtr($charlist, $quote_table);
	}

	/**
	 * @param   string|null       $s
	 * @param   int|digit         $length
	 * @param   string            $pad_str
	 * @param   int               $type     STR_PAD_LEFT, STR_PAD_RIGHT or STR_PAD_BOTH
	 * @return  string|bool|null
	 */
	public static function str_pad($s, $length, $pad_str = ' ', $type = STR_PAD_RIGHT)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;

		$input_len = self::strlen($s);
		if ($length <= $input_len) return $s;

		$pad_str_len = self::strlen($pad_str);
		$pad_len = $length - $input_len;

		if ($type == STR_PAD_RIGHT)
		{
			$repeat_num = ceil($pad_len / $pad_str_len);
			return self::substr($s . str_repeat($pad_str, $repeat_num), 0, $length);
		}

		if ($type == STR_PAD_LEFT)
		{
			$repeat_num = ceil($pad_len / $pad_str_len);
			return self::substr(str_repeat($pad_str, $repeat_num), 0, intval(floor($pad_len))) . $s;
		}

		if ($type == STR_PAD_BOTH)
		{
			$pad_len /= 2;
			$pad_amount_left  = intval(floor($pad_len));
			$pad_amount_right = intval(ceil($pad_len));
			$repeat_times_left  = ceil($pad_amount_left  / $pad_str_len);
			$repeat_times_right = ceil($pad_amount_right / $pad_str_len);

			$padding_left  = self::substr(str_repeat($pad_str, $repeat_times_left),  0, $pad_amount_left);
			$padding_right = self::substr(str_repeat($pad_str, $repeat_times_right), 0, $pad_amount_right);
			return $padding_left . $s . $padding_right;
		}

		trigger_error('Parameter 4 should be a constant of STR_PAD_RIGHT, STR_PAD_LEFT or STR_PAD_BOTH!', E_USER_WARNING);
		return false;
	}

	/**
	 * @param   string    $str
	 * @param   string    $mask
	 * @param   int|null  $start
	 * @param   int|null  $length
	 * @return  int|bool
	 */
	public static function strspn($str, $mask, $start = null, $length = null)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		#if (self::is_ascii($str) && self::is_ascii($mask)) return strspn($str, $mask, $start, $length);
		if ($start !== null || $length !== null) $str = self::substr($str, $start, $length);
		if (preg_match('~^[' . preg_quote($mask, '~') . ']+~uSX', $str, $m)) self::strlen($m[0]);
		return 0;
	}

	/**
	 * Перекодирует текстовые файлы в указанной папке в кодировку UTF-8
	 * При обработке пропускаются бинарные файлы, файлы в кодировке UTF-8 и файлы, которые перекодировать не удалось.
	 * Т. о. метод работает достаточно надёжно.
	 *
	 * @param   string       $dir             директория для сканирования
	 * @param   string|null  $files_re        регул. выражение для шаблона имён файлов,
	 *                                        например: '~\.(?:txt|sql|php|pl|py|sh|tpl|xml|xsl|html|xhtml|phtml|htm|js|json|css|conf|cfg|ini|htaccess)$~sSX'
	 * @param   bool         $is_recursive    обрабатывать вложенные папки и файлы?
	 * @param   string       $charset         исходная кодировка
	 * @param   string|null  $dirs_ignore_re  регул. выражение для исключения папок из обработки
	 *                                        например: '~^(?:cache|images?|photos?|fonts?|img|ico|\.svn|\.hg|\.cvs)$~siSX'
	 * @param   bool         $is_echo         печать имён обработанных файлов и статус обработки в выходной поток?
	 * @param   bool         $is_simulate     сымитировать работу без реальной перезаписи файлов?
	 * @return  int|bool                      возвращает кол-во перекодированных файлов
	 *                                        returns FALSE if error occured
	 */
	public static function convert_files_from(
		$dir,
		$files_re = null,
		$is_recursive = true,
		$charset = 'cp1251',
		$dirs_ignore_re = null,
		$is_echo = false,
		$is_simulate = false)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;

		$dh = opendir($dir);
		if (! is_resource($dh)) return false;
		$counter = 0;
		while (($name = readdir($dh)) !== false)
		{
			if ($name == '.' || $name == '..') continue;
			$file = $dir . '/' . $name;
			if (is_file($file))
			{
				if (is_string($files_re) && ! preg_match($files_re, $name)) continue;
				if ($is_echo) echo $file;
				$s = @file_get_contents($file);
				if (! is_string($s))
				{
					if ($is_echo) echo '  Error to reading' . PHP_EOL;
					return false;
				}
				if (self::is_utf8($s))
				{
					if ($is_echo) echo '  UTF-8' . PHP_EOL;
					continue;
				}
				$s = self::_convert($s, $charset, 'UTF-8');
				#игнорируем ошибки при попытке перекодировать бинарные файлы
				if (! is_string($s) || ! self::is_utf8($s))
				{
					if ($is_echo) echo '  Binary' . PHP_EOL;
					continue;
				}

				$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
				if ($ext === 'htm' || $ext === 'html' || $ext === 'xhtml' || $ext === 'phtml' || $ext === 'tpl')
				{
					$s = preg_replace('~(<meta .+? content="text/html; [\x00-\x20]+ charset=) #1
											[-a-zA-Z\d]+
											(" [^>]* >)  #2
										~sixSX', '$1utf-8$2', $s);
				}
				if ($ext === 'xml' || $ext === 'xsl' || $ext === 'tpl')
				{
					$s = preg_replace('~(<\?xml .+? encoding=") #1
											[-a-zA-Z\d]+
											(" .*? \?>)         #2
										~sixSX', '$1utf-8$2', $s);
				}

				if (! $is_simulate)
				{
					$bytes = @file_put_contents($file, $s);
					if ($bytes === false)
					{
						if ($is_echo) echo '  Error to writing' . PHP_EOL;
						return false;
					}
				}
				if ($is_echo) echo '  ' . $charset . ' -> UTF-8' . PHP_EOL;
				$counter++;
			}
			elseif ($is_recursive && is_dir($file))
			{
				if (! is_string($dirs_ignore_re) || ! preg_match($dirs_ignore_re, $name))
				{
					$c = self::convert_files_from($file, $files_re, $is_recursive, $charset, $dirs_ignore_re, $is_echo, $is_simulate);
					if ($c === false) return false;
					$counter += $c;
				}
			}
		}
		closedir($dh);
		return $counter;
	}

	/**
	 *
	 * @param   int|string  $low
	 * @param   int|string  $high
	 * @param   int         $step
	 * @return  array|bool         returns FALSE if error occured
	 */
	public static function range($low, $high, $step = 1)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_int($low) || is_int($high)) return range($low, $high, $step);  #speed improve
		$low_cp  = self::ord($low);
		$high_cp = self::ord($high);
		if ($low_cp === false || $high_cp === false) return false;
		$a = range($low_cp, $high_cp, $step);
		return array_map(array('self', 'chr'), $a);
	}

	/**
	 *
	 * @param   string|null       $str
	 * @param   string|array      $from
	 * @param   string|null       $to
	 * @return  string|bool|null         returns FALSE if error occured
	 */
	public static function strtr($s, $from, $to = null)
	{
		if (! pEngine_Text_Reflection::isValid()) return false;
		if (is_null($s)) return $s;
		if (is_array($from)) return strtr($s, $from); #speed improve
		$keys   = self::str_split($from);
		$values = self::str_split($to);
		if ($keys === false || $values === false) return false;
		$table = array_combine($keys, $values);
		if (! is_array($table)) return false;
		return strtr($s, $table);
	}

	public static function tests()
	{
		$a = array(
			'self::html_entity_decode("&quot;&amp;&lt;&gt;", true) === "\"&<>"',
			'self::html_entity_decode("&quot;&amp;&lt;&gt;", false) === "&quot;&amp;&lt;&gt;"',
			'self::html_entity_decode("&amp;amp;", true) === "&amp;"',
			'self::html_entity_decode("&amp;amp;", false) === "&amp;amp;"',
			'self::html_entity_decode("&#034;", true) === "\""',
			'self::html_entity_decode("&#034;", false) === "&quot;"',
			'self::html_entity_decode("&#039;", true) === "\'"',
			'self::html_entity_decode("&#039;", false) === "\'"',
			'self::html_entity_decode("&#x22;", true) === "\""',
			'self::html_entity_decode("&#x22;", false) === "&quot;"',

			'self::array_change_key_case(array("АБВГД" => "АБВГД"), CASE_LOWER) === array("абвгд" => "АБВГД")',
			'self::array_change_key_case(array("абвгд" => "абвгд"), CASE_UPPER) === array("АБВГД" => "абвгд")',

			'self::blocks_check("Яндекс", "Cyrillic") === true',
			'self::blocks_check("Google", "Basic Latin") === true',
			'self::blocks_check("Google & Яндекс", array("Basic Latin", "Cyrillic")) === true',
			'self::blocks_check("Ё-моё, Yandex!", array(array(0x20, 0x7E),    #[\x20-\x7E]
														array(0x0410, 0x044F), #[A-Яa-я]
														0x0401, #russian yo (Ё)
														0x0451, #russian ye (ё)
													)) === true',

			'self::chunk_split("абвг", 2) === "аб\r\nвг"',
			'self::chunk_split("абвг", 2, "|") === "аб|вг"',

			'self::lowercase("1234-ABCD-АБВГ") === "1234-abcd-абвг"',
			'self::lowercase(array("1234-ABCD-АБВГ" => "1234-ABCD-АБВГ")) === array("1234-ABCD-АБВГ" => "1234-abcd-абвг")',
			'self::uppercase("1234-abcd-абвг") === "1234-ABCD-АБВГ"',
			'self::uppercase(array("1234-abcd-абвг" => "1234-abcd-абвг")) === array("1234-abcd-абвг" => "1234-ABCD-АБВГ")',

			'self::convert_from(self::convert_to("123-ABC-abc-АБВ-абв", $charset = "cp1251"), $charset = "cp1251") === "123-ABC-abc-АБВ-абв"',

			'self::diactrical_remove("вдох\xc2\xadно\xc2\xadве\xcc\x81\xc2\xadние") === "вдох\xc2\xadно\xc2\xadве\xc2\xadние"',
			'self::diactrical_remove("вдох\xc2\xadно\xc2\xadве\xcc\x81\xc2\xadние", array("\xc2\xad")) === "вдохновение"',
			'self::diactrical_remove("вдох\xc2\xadно\xc2\xadве\xcc\x81\xc2\xadние", array("\xc2\xad"), true, $restore_table) === "вдохновение"',
			'self::diactrical_restore("вдохновение", $restore_table) === "вдох\xc2\xadно\xc2\xadве\xcc\x81\xc2\xadние"',

			'self::is_utf8(file_get_contents(' . var_export(__FILE__, true) . ', true)) === true',
			'self::is_utf8(file_get_contents(' . var_export(__FILE__, true) . ', false)) === true',

			#range() uses ord() and chr()
			'self::range("A", "D") === array("A", "B", "C", "D")',
			'self::range("а", "г") === array("а", "б", "в", "г")',
			'self::range(1, 3) === array(1, 2, 3)',
		);
		foreach ($a as $k => $v) if (! assert($v)) return false;
		return true;
	}

}
