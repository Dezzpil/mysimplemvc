<?
namespace msmvc\model;

/**
 * Translate int to string in current currency
 * @author Nikita Dezzpil Orlov
 */
class num
{
	private $figure;

	private $currency;

	const NAN = '';
	const RUR = 'rur';
	const USD = 'usd';
	const EUR = 'eur';
	const ITEM = 'item';
	
	private $curency_dictionary = array(
		self::NAN => array(array("", "", "", 0), array("", "", "", 1)),
		self::RUR => array(array("рубль ", "рубля ", "рублей ", 0), array("копейка ", "копейки ", "копеек ", 1)),
		self::USD => array(array("доллар ", "доллара ", "долларов ", 0), array("цент ", "цента ", "центов ", 0)),
		self::EUR => array(array("евро ", "евро ", "евро ", 0), array(" ", " ", " ")),
		self::ITEM => array(array("предмет ", "предмета ", "предметов ", 0), array("", "", "", 1))
	);

	private $short_numeration_sign = array(
		self::NAN => '',
		self::RUR => '.',
		self::USD => '.',
		self::EUR => '',
		self::ITEM => '.'
	);

    /**
     * Get int
     * @param int $figure
     */
	function __construct($figure)
	{
		$this->figure = $figure;
		$this->currency = self::NAN;
	}

	public function to_str($stripkop = TRUE)
	{
		$numeration = $this->currency;
		$cost = $this->figure;
		
		if ($cost == 0)
		{
			// нулевая стоимость
			$zero_cost = 'Ноль '.$this->curency_dictionary[$numeration][0][2];
			if ( ! $stripkop)
			{
				$zero_cost .= 'ноль '.$this->curency_dictionary[$numeration][1][2];
			}
			
			return $zero_cost;
		}
		
		$str[100] = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
		$str[11] = array('', 'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать', 'двадцать');
		$str[10] = array('', 'десять', 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
		$sex = array(
			array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
			array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять')
		);
		
		$forms = array(
			$this->curency_dictionary[$numeration][1],
			$this->curency_dictionary[$numeration][0],
			array('тысяча', 'тысячи', 'тысяч', 1), // 10^ 3
			array('миллион', 'миллиона', 'миллионов', 0), // 10^ 6
			array('миллиард', 'миллиарда', 'миллиардов', 0), // 10^ 9
			array('триллион', 'триллиона', 'триллионов', 0), // 10^12
		);
		
		$zero = '!!';
		$out = $tmp = array();

		$tmp = explode('.', str_replace(',', '.', $cost));
		$rub = number_format($tmp[0], 0, '', '-');
		if ($rub == 0) $out[] = $zero;

		$kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0, 2) : '00';
		$segments = explode('-', $rub);
		$offset = sizeof($segments);

		if ((int) $rub == 0)
		{
			$o[] = $zero;
			$o[] = $this->plural_form(0, $forms[1][0], $forms[1][1], $forms[1][2]);
		}
		else
		{
			foreach ($segments as $k => $lev)
			{
				$sexi = (int) $forms[$offset][3];
				$ri = (int) $lev;
				if ($ri == 0 && $offset > 1)
				{
					$offset--;
					continue;
				}

				$ri = str_pad($ri, 3, '0', STR_PAD_LEFT);
				
				$r1 = (int) substr($ri, 0, 1); 
				$r2 = (int) substr($ri, 1, 1); 
				$r3 = (int) substr($ri, 2, 1);
				$r22 = (int) $r2.$r3;

				if ($ri > 99)
					$o[] = $str[100][$r1];
				if ($r22 > 20)
				{
					$o[] = $str[10][$r2];
					$o[] = $sex[$sexi][$r3];
				}
				else
				{
					if ($r22 > 9)
						$o[] = $str[11][$r22 - 9];
					elseif ($r22 > 0)
						$o[] = $sex[$sexi][$r3];
				}

				$o[] = $this->plural_form($ri, $forms[$offset][0], $forms[$offset][1], $forms[$offset][2]);
				$offset--;
			}
		}

		if (!$stripkop)
		{
			$o[] = $kop;
			$o[] = $this->plural_form($kop, $forms[0][0], $forms[0][1], $forms[0][2]);
		}
		$o = preg_replace("/\s{2,}/", ' ', implode(' ', $o));
		$o{0} = mb_convert_case($o{0}, MB_CASE_UPPER, 'cp1251');
		return $o;
	}

	private function plural_form($n, $f1, $f2, $f5)
	{
		if (1 == $n % 10 && 11 != $n % 100)
		{
			return func_get_arg(1);
		}
		elseif ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 10 >= 20))
		{
			return func_get_arg(2);
		}
		else
		{
			return func_get_arg(3);
		}
	}

	function set_currency($numeration)
	{
		$this->currency = $numeration;
	}

	function get_short_sign()
	{
		return $this->short_numeration_sign[$this->currency];
	}
}
?>