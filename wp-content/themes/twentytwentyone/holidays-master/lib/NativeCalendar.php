<?php
// $Id$

/*
 * @file
 * PHP classes providing calendars.
 *
 * Copyright (C) Mooffie <mooffie@gmail.com>
 *
 * It is released to the public under the GNU General Public License (GPL).
 */

define('CAL_LANG_FOREIGN', 0);
define('CAL_LANG_NATIVE',  1);

/**
 * This is the base class of every native calendar.
 */
class NativeCalendar {

  static $timestamp_decoding_function = 'getdate';

  /**
   * When the calendar is asked to convert a unix timestamp to a native date,
   * it first needs to convert the timestamp --which is given in UTC-- to local
   * time. By default it uses getdate() for this conversion, but you may
   * register a function of your own. Your function must return an array
   * containing three keyed elements: 'year', 'mon', 'mday' (other keys are
   * ignored).
   */
  static function set_timestamp_decoding_function($function) {
    self::$timestamp_decoding_function = $function;
  }

  /**
   * You may use this function to instantiate calendar objects.
   *
   * Instead of <code>$cal = new JewishCalendar</code>, do
   * <code>$cal = NativeCalendar::factory('Jewish')</code>.
   *
   * @static
   * @param string $id
   * @return object
   */
  static function factory($id, $settings = NULL) {
    $filename = dirname(__FILE__) .'/'. $id .'Calendar.php';
    if (!file_exists($filename)) {
      return NULL;
    }
    require_once $filename;
    $classname = $id .'Calendar';
    $obj = new $classname;
    // We keep the ID around in case some 3rd party code may wish to use it:
    $obj->name = $id;
    if (isset($settings)) {
      $obj->settings($settings);
    }
    return $obj;
  }

  /**
   * Get installed calendars.
   *
   * @static
   * @return array
   */
  static function factory_list() {
    static $list = array();
    if ($list) {
      return $list;
    }
    $dir = opendir(dirname(__FILE__));
    while (($file = readdir($dir)) !== FALSE) {
      if (preg_match('/(.*)Calendar\.php$/', $file, $m) && ($file != 'NativeCalendar.php')) {
        $list[$m[1]] = $m[1];
      }
    }
    closedir($dir);
    // TODO: sort alphabetically?
    return $list;
  }

  function NativeCalendar() {
    $this->settings = array();
    // All talk defaults to English.
    $this->settings['language'] = CAL_LANG_FOREIGN;
    // We provide for a possible localization function, t().
    // If it's not already defined by the host system (e.g. your CMS), we 
    // implement a dummy one.
    if (!function_exists('t')) {
      function t($s) {
        return $s;
      }
    }
  }

  /**
   * Get the title of this calendar.
   */
  function title() {
    die('Error: pure virtual function NativeCalendar::title() called');
  }

  /**
   * The title for an iCal feed. Usually the same as title().
   */
  function ical_title() {
    return $this->title();
  }

  /**
   * Overridable; Return TRUE if the calendar's native language is right to left.
   */
  function is_rtl() {
    return FALSE;
  }

  /**
   * Overridable; Returns the native language of this calendar.
   */
  function native_language() {
    return array('en' => t('English'));
  }

  /**
   * Set one of more settings.
   *
   * Various calendars may have various settings. Instead of
   * defining a separate setXYZ() function for each setting, we elect for
   * a central settings() method.
   *
   * A setting which all calendars are required to support is the 'language' setting. 
   * If may either be CAL_LANG_NATIVE or CAL_LANG_FOREIGN and it determines
   * the language in which the calendar 'talks.'
   *
   * @param array $settings
   */
  function settings($settings) {
    $this->settings = array_merge($this->settings, $settings);
    return $this; // Allow for "fluid syntax".
  }
  
  /**
   * Clones the calendar object.
   *
   * (Note: "clone" is a reserved word in PHP, so we name it "copy" instead).
   *
   * This is merely "syntactic sugar". It allows you to write, e.g.,
   * <code>print some_factory('Jewish')->copy()->settings(...)->getMediumDate(time())</code>
   *
   * (In that specific example we use ->copy() in order to not affect the 
   * object that some_factory() may potentially cache.)
   */
  function copy() {
    $clone = version_compare(phpversion(), '5.0') < 0 ? $this : clone($this);
    return $clone;
  }

  /**
   * This method is the "official" way to read settings from outside.
   *
   * (Since PHP4 doesn't support 'private', we can't hide $this->settings,
   * nevertheless applications shouldn't access it directly.)
   */
  function settings_get($setting_name = NULL) {
    if (isset($setting_name)) {
      return $this->settings[$setting_name];
    } else {
      return $this->settings;
    }
  }

  /**
   * Returns a list of all the settings this calendar supports.
   */
  function settings_list() {
    return array_keys($this->settings);
  }

  /**
   * Get a form used to interactively edit the settings. Calendars should 
   * override this method.
   *
   * This is the only code in this library that's Drupal-dependant. We'd better
   * move it to the Drupal module itself.
   */
  function settings_form() {
    $form['language'] = array(
      '#type' => 'radios',
      '#title' => t('The language in which to print dates and holiday names'),
      '#options' => array(
        CAL_LANG_FOREIGN => t("The website's language"),
        CAL_LANG_NATIVE  => current($this->native_language()),
      ),
      '#default_value' => $this->settings['language'],
    );
    return $form;
  }

  /**
   * Save the form's values back into $this->settings.
   *
   * You need to override this method if your form has complicated fields that
   * need some interpretation before assigned to the settings.
   */
  function settings_form_save($form_values) {
    // We can't use array_intersect_key() because it ain't in PHP4.
    $settings = array();
    foreach ($this->settings_list() as $key) {
      $settings[$key] = $form_values[$key];
    }
    $this->settings($settings);
  }

  /**
   * Get the holidays falling on a certain date.
   *
   * If no holidays occur on this date, the array returned is empty. Else, the 
   * array contains one element for each holiday. Usually a maximun of one 
   * holiday occurs on a date, but since some religions may have two or more 
   * events occuring on the same date, you should loop over the array.
   *
   * Each holiday is represented thus:
   *
   * <pre>
   *
   * array(
   *
   *   'native'  => '...',          // The native name of the holiday
   *
   *   'foreign' => 'Rosh HaShana', // The foreign, usually English, name of the holiday
   *
   *   'name'    => 'Rosh HaShana', // The name you should pick for printing; it's
   *                                // either of the above, depending on
   *                                // the 'language' setting.
   *
   *   'class'  => 'taanit'         // A string that may be used in an HTML 'class'
   *                                // attibute (CSS). This string usually tells us
   *                                // something about the nature of this holiday.
   *
   *   'id'     =>  'roshHaShana1'  // The ID of this holiday. Each holiday has a
   *                                // unique ID string. You may use it in your CSS.
   * );
   *
   * </pre>
   *
   * @param  date
   * @return array Array of holidays. 
   */
  function getHolidays($date) {
    die('Error: pure virtual function NativeCalendar::getHoliday() called');
  }

  /**
   * Convert a date, given in various formats, to the native format.
   *
   * All the methods of a calendar object are using this function to convert the
   * $date parameter they receive to the native format. This is why you can feed them a
   * gregorian date, or a unix timestamp, and know nothing about the native date system.
   *
   * You seldom need to call this function directly.
   *
   * @param mixed $date
   * @return internal
   */
  function convertToNative($date) {
    die('Error: pure virtual function NativeCalendar::convertToNative() called');
  }

  /**
   * [This is a private function that's used by convertToNative().]
   *
   * Get a date and, if it's one of three known formats, returns an array of the
   * form getdate() returns (possibly with 'jdc' replacing the 'year'/'mon'/'mday').
   *
   * The date formats this function recognizes:
   *
   * - Unix timestamp.
   * - PHP5's date object.
   * - "ISO" string.
   *
   * The returned data is a local date (that is, timezone shifting is performed on
   * the input). In case of an ISO string, the input is taken to be local already
   * (In other words, feeding an ISO string to the calendar is just an alternative
   * to feeding it an explicit year/mon/mday array).
   *
   * @protected
   */
  function _canonizeInputDate($date) {
    if (is_numeric($date)) {
      // We've got a unix tiemstamp.
      //
      // Note: there's actually a unixtojd() PHP function, but we aren't using
      // it because we want to give the embedding application a chance to take
      // over the UTC-to-local conversion.
      $decoder = self::$timestamp_decoding_function;
      return $decoder($date);
    }
    elseif (is_object($date)) {
      // PHP5's date object.
      // should we do is_a($date, 'DateTime') instead? No, we want PHP to bork if
      // somebody feeds us a wrong object.
      $timestamp = date_format($date, 'U');
      return $this->_canonizeInputDate($timestamp);
    }
    elseif (is_string($date)) {
      if (preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d)(?: |T|$)/', $date, $Ymd)) {
        $parts = array(
          'jdc' => gregoriantojd($Ymd[2], $Ymd[3], $Ymd[1])
        );
        if (preg_match('/(\d\d):(\d\d)(?::(\d\d))?/', $date, $His)) {
          $parts += array(
            'hours' => $His[1],
            'minutes' => $His[2],
            'seconds' => empty($His[3]) ? 0 : $His[3]
          );
        }
        return $parts;
      }
    }
    else {
      // It's something else; return as-is.
      return $date;
    }
  }

  /**
   * A replacement for <code>getdate(time())</code> that takes your Content
   * Management System's timezone into account.
   */
  function get_todays_date_as_gregorian() {
    $decoder = self::$timestamp_decoding_function;
    return $decoder(time());
  }

  /**
   * A replacement for <code>unixtojd(time())</code> that takes your Content
   * Management System's timezone into account.
   */
  function get_todays_date_as_jdc() {
    $parts = $this->get_todays_date_as_gregorian();
    return gregoriantojd($parts['mon'], $parts['mday'], $parts['year']);
  }

  /*
   * Return the native representation of a number.
   *
   * Some calendars represent numbers using a counting system different than the common one. 
   * Whenever you need to print a number, use this function to get its 
   * representation.
   *
   * @param int $i
   * @return string
   */ 
  function getNumber($i) {
    die('Error: pure virtual function NativeCalendar::getNumber() called');
  }

  /**
   * Get the name of the n'th native month.
   *
   * The $year parameter is required because leap years may have different sets of
   * months.
   *
   * @param int $year
   * @param int $month
   */
  function getMonthName($year, $month) {
    die('Error: pure virtual function NativeCalendar::getMonthName() called');
  }
  
  /*
   * Return the native names of the days of the week.
   *
   * @return array An array with seven string elements.
   */
  function getDaysOfWeek() {
    die('Error: pure virtual function NativeCalendar::getDaysOfWeek() called');
  }

  /**
   * Get the native name of a date.
   *
   * Gregorian dates look like "25 Apr, 1984", but dates in other calendar 
   * systems may look quite differently.
   *
   * @param date
   * @return string
   */ 
  function getMediumDate($date) {
    die('Error: pure virtual function NativeCalendar::getMediumDate() called');
  }
  

  /**
   * Returns a nice HTML table showing one month in the calendar.
   *
   * This function is used for debugging. But you may use it for your 'end
   * product' if you're lazy.
   *
   * Note that the month shown is Gregorian (that is, January, February, ...),
   * therefore the parameters this function receives are the Gregorian year
   * and month.
   *
   * You should include the 'demo-core.css' style sheet in your page for a
   * pretty display.
   * 
   * @param int $year 
   * @param int $month
   */
  function printCal($year, $month)
  {
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $todays_jdc    = $this->get_todays_date_as_jdc();
    $prev_dow      = 100; // anything above 7

    if ($this->settings['language'] == CAL_LANG_FOREIGN) {
      $days_of_week = array(t('Sun'), t('Mon'), t('Tue'), t('Wed'), t('Thu'), t('Fri'), t('Sat'));
    } else {
      $days_of_week = $this->getDaysOfWeek();
    }

    $output  = "<table class='holidays-calendar'>";
    $output .= "<tr>";
    foreach ($days_of_week as $day) {
      $output .= "<th class='day-header'>$day</td>";
    }
    $output .= "</tr>";

    for ($day = 1; $day <= $days_in_month; $day++)
    {
      $jdc = gregoriantojd($month, $day, $year);
      $dow = jddayofweek($jdc, 0) + 1;

      if ($dow < $prev_dow) {
        // Starting a new week, so start a new row in table.
        if ($day != 1) {
          $output .= "</tr>";
          $output .= "<tr>";
        } else {
          $output .= "<tr>";
          for ($i = 1; $i < $dow; $i++) {
            $output .= "<td class='empty-day'></td>";
          }
        }
      }

      $j_date = $this->convertToNative(array('jdc' => $jdc));
      $holidays = $this->getHolidays($j_date);
      $holiday_names = '';
      $holiday_classes = array();

      if ($holidays) {
        foreach ($holidays as $hday) {
          $holiday_classes[$hday['id']] = 1;
          $holiday_classes[$hday['class']] = 1;
          $holiday_names .= "<div class='holiday-name'>$hday[name]</div>\n";
        }
      }
      if ($jdc == $todays_jdc) {
        $holiday_classes['today'] = 1;
      }
      $holiday_classes = implode(' ', array_keys($holiday_classes));

      $output .= "<td class='day $holiday_classes'>\n";
      $output .= "<span class='gregorian-number'>$day</span>\n";
      $output .= "<span class='native-number'>".$this->getNumber($j_date['mday']) . ' ??' . $this->getMonthName($j_date['year'], $j_date['mon']);
      if ($j_date['mday'] == 1)
        $output .= " <span class='month-name'>(".
              $this->getMonthName($j_date['year'], $j_date['mon']).")</span>";
      $output .= "</span>\n";
      $output .= $holiday_names;
      $output .= "</td>";

      $prev_dow = $dow;
    }
    for ($i = $dow + 1; $i <= 7; $i++) {
      $output .= "<td class='empty'></td>";
    }
    $output .= "</tr>";
    $output .= "</table>";

    return $output;
  }

}

