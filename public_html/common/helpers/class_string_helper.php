<?
/*
 A bunch of static functions that take a string, do something to it and return it
*/

class StringHelper {
	public static function underscoreToSpace($word) {
		return ucfirst(str_ireplace('_', ' ', $word));	
	}
	
	public static function highlight($text, $keywords){
		$keywords = trim($keywords);
		if (empty($keywords)) {
			return $text;
		} else {
			$keywords = preg_replace("/[\+\-\/]/","",$keywords);
			$array = explode(" ",$keywords);
			foreach ($array as $word) {
				$text = preg_replace("[$word]","<span class=\"highlight\">$word</span>",$text);
			}
			return $text;
		}
	}
	
	/** 
	 * remove all characters other than 0-9a-Z and replace with '-'
	 * @param $input
	 */
	public static function cleanString($input) {
		$input = strtolower($input);
		$input = trim($input);
		$input = preg_replace('/[^a-zA-Z0-9\s]/', '', $input);
		return preg_replace('/(\s)+/', '-', $input);
	}
	
	/**
	 * shortens text to $no_of_chars, then puts three dots on the end
	 * @param string $text
	 * @param int $no_of_chars
	 * @return string
	 */
	public static function ellipsis($original_text, $no_of_chars) {
		$original_text = trim($original_text);
		$original_text = strip_tags($original_text);
		
		$text = $original_text . ' ';
		$text = substr($text, 0, $no_of_chars);
		$text = substr($text, 0, strrpos($text, ' '));
		
		return (strlen($original_text) > $no_of_chars) ? $text . '...' : $text;
	}
	
	public static function boolColour($value, $true, $false) {
		if ($value) {
			return "<span class='true'>" . $true ."</span>";
		} else {
			return "<span class='false'>" . $false . "</span>";
		}
	}
	
	
	public static function defaultIfEmpty($input, $default = '') {
		return (!empty($input) ? $input : $default);
	}
	
	/*
	Change underscore to space and captilizes first character	 
	*/
	public static function displayNicely($input) {
		$rtn = str_ireplace('_', ' ', $input);
		return ucfirst($rtn);
		
	}
	
	public static function randomString($length = 24) {
		$out = "";
		for ($i =0; $i < $length ; $i++) {
			$out.=chr(rand(65,90));
		}
		return $out;
	}

    public static function randomNumeric($length = 5) {
        $out = "";
        for ($i =0; $i < $length ; $i++) {
            $out.=chr(rand(48, 57));
        }
        return $out;
    }

	public static function moneyFormat($amount, $currency_symbol = '$') {
		return $currency_symbol . number_format($amount, 2);
	}
	
	public static function yesNo($value, $yes = 'Yes', $no = 'No') {
		$value = strtolower($value);
		return ($value == 'y') ? $yes : $no;
	}
	
	public static function addTextToLastParagraph($append_text, $paragraph_text, $extra_space = TRUE) {
		$append_text = ($extra_space) ? '  ' . $append_text : $append_text;
		 
		return preg_replace('{</p>$}', $append_text . "</p>", $paragraph_text);
	}

    /**
     * Remove all characters except letters, numbers, and spaces.
     *
     * @param string $string
     * @return string
     */
    public static function removeNonAlphaNumericSpaces($input) {
        return preg_replace("/[^a-z0-9 ]/i", "", $input);
    }

    /**
     * Remove all characters except letters and numbers.
     *
     * @param string $string
     * @return string
     */
    public static function removeNonAlphaNumeric($input) {
        return preg_replace("/[^a-z0-9]/i", "", $input);
    }

    /**
     * Remove all characters except numbers.
     *
     * @param string $string
     * @return string
     */
    public static function removeNonNumeric($input) {
        return preg_replace("/[^0-9]/", "", $input);
    }

    /**
     * Remove all characters except letters.
     *
     * @param string $string
     * @return string
     */
    public static function removeNonAlpha($input) {
        return preg_replace("/[^a-z]/i", "", $input);
    }

    /**
     * Transform two or more spaces into just one space.
     *
     * @param string $string
     * @return string
     */
    public static function removeExcessWhitespace($input) {
        return preg_replace('/  +/', ' ', $input);
    }

    /**
     * Format a string so it can be used for a URL slug
     *
     * @param string $string
     * @return string
     */
    public static function formatForUrl($input) {
        $input = trim(strtolower($input));

        $input = removeNonAlphNumericSpaces($input);
        $input = removeExcessWhitespace($input);

        return str_replace(" ", "-", $input);
    }

    /**
     * Format a slug into human readable string
     *
     * @param string $string
     * @return string
     */
    public static function formatFromUrl($input) {
        $input = trim(strtolower($input));

        return str_replace("-", " ", $input);
    }

	public static function singularOfPlural($list,$single,$plural) {
		if (is_array($list)) {
			$c = sizeof($list);
		} elseif (is_numeric($list)) {
			$c = $list;
		} else {
			$c = 0;
		}
		return $c == 1 ? '1 ' . $single : $c . ' ' .$plural;
	}

    /**
     * for obsucring passwords and credit card nos
     * eg 12** **** **** ***4
     * @param $foo
     * @param int $show_starting_chars
     * @param int $show_ending_chars
     */
	public static function mask($foo,$show_starting_chars =1,$show_ending_chars =1) {

	    $bar = substr($foo,$show_starting_chars,-$show_ending_chars);
	    $bar = preg_replace('/\S/','*',$bar);
        $bar = substr($foo,0,$show_starting_chars) . $bar . substr($foo,-$show_ending_chars);
        return $bar;


    }

    public static function base64_decode($foo) {
        return base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $foo));
    }
    
}

