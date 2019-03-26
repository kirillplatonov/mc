<?php
/**
	 * MobileCMS
	 *
	 * Open source content management system for mobile sites
	 *
	 * @author MobileCMS Team <support@mobilecms.pro>
	 * @copyright Copyright (c) 2011-2019, MobileCMS Team
	 * @link https://mobilecms.pro Official site
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 */

defined('IN_SYSTEM') or die('<b>403<br />Запрет доступа!</b>');

/**
 * Функции для работы с формами (часть кода взята из Smarty)
 */
class main_form {
	/**
	 * Checkboxes
	 */
	public static function checkboxes($params) {
		$name = 'checkbox';
		$values = null;
		$options = null;
		$selected = null;
		$separator = '';
		$labels = true;
		$output = null;

		$extra = '';

		foreach ($params as $_key => $_val) {
			switch ($_key) {
				case 'name':
				case 'separator':
					$$_key = $_val;
					 break;

				case 'labels':
					$$_key = (bool) $_val;
					break;

				case 'options':
					$$_key = (array) $_val;
					break;

				case 'values':
				case 'output':
					$$_key = array_values((array) $_val);
					break;

				case 'checked':
				case 'selected':
					$selected = array_map('strval', array_values((array) $_val));
					break;

				case 'checkboxes':
					die('html_checkboxes: the use of the "checkboxes" attribute is deprecated, use "options" instead');
					$options = (array) $_val;
					break;

				case 'assign':
					break;

				default:
					if (!is_array($_val)) {
						$extra .= ' '.$_key.'="'.$_val.'"';
					} else {
						die("html_checkboxes: extra attribute '$_key' cannot be an array");
					}
					break;
			}
		}

		if (!isset($options) && !isset($values))
			return ''; /* raise error here? */

		settype($selected, 'array');
		$_html_result = array();

		if (isset($options)) {
			foreach ($options as $_key=>$_val)
				$_html_result[] = self::checkboxes_output($name, $_key, $_val, $selected, $extra, $separator, $labels);
		} else {
			foreach ($values as $_i=>$_key) {
				$_val = isset($output[$_i]) ? $output[$_i] : '';
				$_html_result[] = self::checkboxes_output($name, $_key, $_val, $selected, $extra, $separator, $labels);
			}

		}

		if (!empty($params['assign'])) {
			$smarty->assign($params['assign'], $_html_result);
		}
		else {
			return implode("\n", $_html_result);
		}

	}

	/**
	 * Options
	 */
	public static function options($params) {
		$name = null;
		$values = null;
		$options = null;
		$selected = array();
		$output = null;
    
		$extra = '';

		foreach($params as $_key => $_val) {
			switch($_key) {
				case 'name':
					$$_key = (string)$_val;
					break;
	
				case 'options':
					$$_key = (array)$_val;
					break;
	
				case 'values':
				case 'output':
					$$_key = array_values((array)$_val);
					break;
	
				case 'selected':
					$$_key = array_map('strval', array_values((array)$_val));
					break;
	
				default:
					if(!is_array($_val)) {
						$extra .= ' '.$_key.'="'. $_val .'"';
					}
					else {
						die("html_options: extra attribute '$_key' cannot be an array");
					}
					break;
			}
		}

		if (!isset($options) && !isset($values))
			return ''; /* raise error here? */

		$_html_result = '';

		if (isset($options)) {
			foreach ($options as $_key=>$_val)
				$_html_result .= self::options_optoutput($_key, $_val, $selected);
		}
		else {
			foreach ($values as $_i=>$_key) {
				$_val = isset($output[$_i]) ? $output[$_i] : '';
				$_html_result .= self::options_optoutput($_key, $_val, $selected);
			}

		}

		if (!empty($name)) {
			$_html_result = '<select name="'.$name.'"'.$extra.'>'."\n".$_html_result.'</select>'."\n";
		}

		return $_html_result;
	}

	/**
	 * Select date
	 */
	public static function select_date($params) {
		global $q;
		/* Default values. */
		$prefix          = "date_";
		$start_year      = strftime("%Y");
		$end_year        = $start_year;
		$display_days    = true;
		$display_months  = true;
		$display_years   = true;
		$month_format    = "%B";
		/* Write months as numbers by default  GL */
		$month_value_format = "%m";
		$day_format      = "%02d";
		/* Write day values using this format MB */
		$day_value_format = "%d";
		$year_as_text    = false;
		/* Display years in reverse order? Ie. 2000,1999,.... */
		$reverse_years   = false;
		/* Should the select boxes be part of an array when returned from PHP?
	       e.g. setting it to "birthday", would create "birthday[Day]",
	       "birthday[Month]" & "birthday[Year]". Can be combined with prefix */
		$field_array     = null;
		/* <select size>'s of the different <select> tags.
	       If not set, uses default dropdown. */
		$day_size        = null;
		$month_size      = null;
		$year_size       = null;
		/* Unparsed attributes common to *ALL* the <select>/<input> tags.
	       An example might be in the template: all_extra ='class ="foo"'. */
		$all_extra       = null;
		/* Separate attributes for the tags. */
		$day_extra       = null;
		$month_extra     = null;
		$year_extra      = null;
		/* Order in which to display the fields.
	       "D" -> day, "M" -> month, "Y" -> year. */
		$field_order     = 'MDY';
		/* String printed between the different fields. */
		$field_separator = "\n";
		$time = time();
		$all_empty       = null;
		$day_empty       = null;
		$month_empty     = null;
		$year_empty      = null;
		$extra_attrs     = '';

		foreach ($params as $_key=>$_value) {
			switch ($_key) {
				case 'prefix':
				case 'time':
				case 'start_year':
				case 'end_year':
				case 'month_format':
				case 'day_format':
				case 'day_value_format':
				case 'field_array':
				case 'day_size':
				case 'month_size':
				case 'year_size':
				case 'all_extra':
				case 'day_extra':
				case 'month_extra':
				case 'year_extra':
				case 'field_order':
				case 'field_separator':
				case 'month_value_format':
				case 'month_empty':
				case 'day_empty':
				case 'year_empty':
					$$_key = (string)$_value;
					break;

				case 'all_empty':
					$$_key = (string)$_value;
					$day_empty = $month_empty = $year_empty = $all_empty;
					break;

				case 'display_days':
				case 'display_months':
				case 'display_years':
				case 'year_as_text':
				case 'reverse_years':
					$$_key = (bool)$_value;
					break;

				default:
					if(!is_array($_value)) {
						$extra_attrs .= ' '.$_key.'="'.$_value.'"';
					} else {
						$smarty->trigger_error("html_select_date: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
					}
					break;
			}
		}

		if (preg_match('!^-\d+$!', $time)) {
			// negative timestamp, use date()
			$time = date('Y-m-d', $time);
		}
		// If $time is not in format yyyy-mm-dd
		if (preg_match('/^(\d{0,4}-\d{0,2}-\d{0,2})/', $time, $found)) {
			$time = $found[1];
		} else {
			// use smarty_make_timestamp to get an unix timestamp and
			// strftime to make yyyy-mm-dd
			//echo $time;
			$time = strftime('%Y-%m-%d', $time);
		}
		// Now split this in pieces, which later can be used to set the select
		$time = explode("-", $time);

		// make syntax "+N" or "-N" work with start_year and end_year
		if (preg_match('!^(\+|\-)\s*(\d+)$!', $end_year, $match)) {
			if ($match[1] == '+') {
				$end_year = strftime('%Y') + $match[2];
			} else {
				$end_year = strftime('%Y') - $match[2];
			}
		}
		if (preg_match('!^(\+|\-)\s*(\d+)$!', $start_year, $match)) {
			if ($match[1] == '+') {
				$start_year = strftime('%Y') + $match[2];
			} else {
				$start_year = strftime('%Y') - $match[2];
			}
		}
		if (strlen($time[0]) > 0) {
			if ($start_year > $time[0] && !isset($params['start_year'])) {
				// force start year to include given date if not explicitly set
				$start_year = $time[0];
			}
			if($end_year < $time[0] && !isset($params['end_year'])) {
				// force end year to include given date if not explicitly set
				$end_year = $time[0];
			}
		}

		$field_order = strtoupper($field_order);

		$html_result = $month_result = $day_result = $year_result = "";

		$field_separator_count = -1;
		if ($display_months) {
			$field_separator_count++;
			$month_names = array();
			$month_values = array();
			$q[]="";
			$q[]="Января";
			$q[]="Февраля";
			$q[]="Марта";
			$q[]="Апреля";
			$q[]="Мая";
			$q[]="Июня";
			$q[]="Июля";
			$q[]="Августа";
			$q[]="Сентября";
			$q[]="Октября";
			$q[]="Ноября";
			$q[]="Декабря";

	        if(isset($month_empty)) {
	            $month_names[''] = $month_empty;
	            $month_values[''] = '';
	        }
	        for ($i = 1; $i <= 12; $i++) {
	            $month_names[$i] = $q[intval(date('n', mktime(0, 0, 0, $i, 1, 2000)))];
	            $month_values[$i] = strftime($month_value_format, mktime(0, 0, 0, $i, 1, 2000));
	        }

	        $month_result .= '<select name=';
	        if (null !== $field_array) {
	            $month_result .= '"'.$field_array.'['.$prefix.'month]"';
	        } else {
	            $month_result .= '"'.$prefix.'month"';
	        }
	        if (null !== $month_size) {
	            $month_result .= ' size="'.$month_size.'"';
	        }
	        if (null !== $month_extra) {
	            $month_result .= ' '.$month_extra;
	        }
	        if (null !== $all_extra) {
	            $month_result .= ' '.$all_extra;
	        }
	        $month_result .= $extra_attrs.'>'."\n";

	        $month_result .= self::options(array('output'     => $month_names,
	                                                            'values'     => $month_values,
	                                                            'selected'   => (int) $time[1] ? strftime($month_value_format, mktime(0, 0, 0, (int) $time[1], 1, 2000)) : '',
	                                                            'print_result' => false));
	        $month_result .= '</select>';
	    }

	    if ($display_days) {
	    	$field_separator_count++;
	        $days = array();
	        if (isset($day_empty)) {
	            $days[''] = $day_empty;
	            $day_values[''] = '';
	        }
	        for ($i = 1; $i <= 31; $i++) {
	            $days[] = sprintf($day_format, $i);
	            $day_values[] = sprintf($day_value_format, $i);
	        }

	        $day_result .= '<select name=';
	        if (null !== $field_array) {
	            $day_result .= '"'.$field_array.'['.$prefix.'day]"';
	        } else {
	            $day_result .= '"'.$prefix.'day"';
	        }
	        if (null !== $day_size) {
	            $day_result .= ' size="'.$day_size.'"';
	        }
	        if (null !== $all_extra) {
	            $day_result .= ' '.$all_extra;
	        }
	        if (null !== $day_extra) {
	            $day_result .= ' '.$day_extra;
	        }
	        $day_result .= $extra_attrs.'>'."\n";
	        $day_result .= self::options(array('output'     => $days,
	                                                          'values'     => $day_values,
	                                                          'selected'   => $time[2],
	                                                          'print_result' => false));
	        $day_result .= '</select>';
	    }

	    if ($display_years) {
	    	$field_separator_count++;
	        if (null !== $field_array) {
	            $year_name = $field_array.'['.$prefix.'year]';
	        } else {
	            $year_name = $prefix.'year';
	        }
	        if ($year_as_text) {
	            $year_result .= '<input type="text" name="'.$year_name.'" value="'.$time[0].'" size="4" maxlength="4"';
	            if (null !== $all_extra) {
	                $year_result .= ' '.$all_extra;
	            }
	            if (null !== $year_extra) {
	                $year_result .= ' '.$year_extra;
	            }
	            $year_result .= ' />';
	        } else {
	            $years = range((int) $start_year, (int) $end_year);
	            if ($reverse_years) {
	                rsort($years, SORT_NUMERIC);
	            } else {
	                sort($years, SORT_NUMERIC);
	            }
	            $yearvals = $years;
	            if (isset($year_empty)) {
	                array_unshift($years, $year_empty);
	                array_unshift($yearvals, '');
	            }
	            $year_result .= '<select name="'.$year_name.'"';
	            if (null !== $year_size) {
	                $year_result .= ' size="'.$year_size.'"';
	            }
	            if (null !== $all_extra) {
	                $year_result .= ' '.$all_extra;
	            }
	            if (null !== $year_extra) {
	                $year_result .= ' '.$year_extra;
	            }
	            $year_result .= $extra_attrs.'>'."\n";
	            $year_result .= self::options(array('output' => $years,
	                                                               'values' => $yearvals,
	                                                               'selected'   => $time[0],
	                                                               'print_result' => false));
	            $year_result .= '</select>';
	        }
	    }

	    // Loop thru the field_order field
	    for ($i = 0; $i <= 2; $i++) {
	        $c = substr($field_order, $i, 1);
	        switch ($c) {
	            case 'D':
	                $html_result .= $day_result;
	                break;

	            case 'M':
	                $html_result .= $month_result;
	                break;

	            case 'Y':
	                $html_result .= $year_result;
	                break;
	        }
	        // Add the field seperator
	        if ($i < $field_separator_count) {
	            $html_result .= $field_separator;
	        }
	    }

	    return $html_result;
	}

	/**
	* options_optoutput
	*/
	private static function options_optoutput($key, $value, $selected) {
	    if (!is_array($value)) {
	        $_html_result = '<option label="'.$value.'" value="'.
	            $key.'"';
	        if (in_array((string) $key, $selected))
	            $_html_result .= ' selected="selected"';
	        $_html_result .= '>'.$value.'</option>'."\n";
	    } else {
	        $_html_result = self::options_optgroup($key, $value, $selected);
	    }
	    return $_html_result;
	}

	/**
	* options_optgroup
	*/
	private static function options_optgroup($key, $values, $selected) {
	    $optgroup_html = '<optgroup label="'.$key.'">'."\n";
	    foreach ($values as $key => $value) {
	        $optgroup_html .= self::options_optoutput($key, $value, $selected);
	    }
	    $optgroup_html .= "</optgroup>\n";
	    return $optgroup_html;
	}

	/**
	* checkboxes_output
	* @param string $name
	* @param string $extra
	* @param string $separator
	* @param boolean $labels
	*/
	private static function checkboxes_output($name, $value, $output, $selected, $extra, $separator, $labels) {
	    $_output = '';
	    if ($labels) {
	    	$_output .= '<label>';
	    }
	    $_output .= '<input type="checkbox" name="'
	        . $name.'[]" value="'
	        . $value.'"';

	    if (in_array((string) $value, $selected)) {
	        $_output .= ' checked="checked"';
	    }
	    $_output .= $extra.' /> '.$output;
	    if ($labels) $_output .= '</label>';
	    $_output .= $separator;

	    return $_output;
	}
}
?>