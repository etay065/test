<?php
// $Id$

/*
 * @file
 * The demonstration script for the PHP Jewish calendar class.
 */

error_reporting(E_ALL);

date_default_timezone_set('Asia/Jerusalem'); // We have to set something or else PHP will complain.

require_once dirname(__FILE__) .'/lib/NativeCalendar.php'; // Provides the calendar object. The 'engine.'
require_once dirname(__FILE__) .'/demo.inc'; // Utility functions

// $now contains today's date, and will be highlighted on the calendar printed.
// You may wish to add to it the user's timezone offset.
$now = getdate(time());

//
// Step 1:
//
// Load parameters from the URL.
//

// The year to show the callendar for. If it isn't provided in the URL, use current year.
$year  = get_param('year', $now['year']);
// The month to show the calendar for. If it isn't provided in the URL, use current month.
$month = get_param('month', $now['mon']);

// The language in which to show the calendar. Defaults to Hebrew if and only if the browser
// tells us the user reads Hebrew.
$language = get_param('language', strstr(@$_SERVER['HTTP_ACCEPT_LANGUAGE'], 'he') ? 'he' : 'en');

// The method used to calculate the holidays. Can be either 'israel' or 'diaspora'. Defaults
// to 'israel' if the language used is Hebrew.
$method = get_param('method', $language == 'he' ? 'israel' : 'diaspora');

// Show 'Erev Rosh HaShana', etc. Defaults to true.
$eves = get_param('eves', '1');

// Show Sefirat HaOmer (from Passover to Shavuot). Defaults to false.
$sefirat_omer = get_param('sefirat_omer', '0');

// Show 'Isru Khags'. Defaults to false because they have almost no halakhic meaning.
$isru = get_param('isru', '0');

//
// Step 2:
//
// Instantiate the calendar object.
//

$jcal = NativeCalendar::factory('Jewish');
$jcal->settings(array(
  'language' => ($language == 'he' ? CAL_LANG_NATIVE : CAL_LANG_FOREIGN),
  'method' => $method,
  'sefirat_omer' => $sefirat_omer,
  'eves' => $eves,
  'isru' => $isru,
));

if (get_param('feed', 0)) {
  header('Content-Type: text/calendar; charset=utf-8');
  header('Content-Disposition: attachment; filename="calendar.ics";');
  print get_ical_feed($jcal, $year, $month, 2 /* two years */);
  exit();
} else {
  header('Content-type: text/html; charset=utf-8');
}

//
// Step 3:
//
// Print the page.
//

/*$page_title = trans('Jewish Calendar', '?????? ??????');*/
$javascript = get_demo_javascript();
$direction = ($language == 'he' ? 'rtl' : 'ltr');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html dir="<?php echo $direction ?>" class="<?php echo $direction ?>">
<head>
   <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
   <meta name="robots" content="nofollow, noarchive" />
   <title><?php echo $page_title ?></title>
   <link href="demo-style/demo-core.css?1" rel="stylesheet" type="text/css" />
   <link href="demo-style/demo.css?1" rel="stylesheet" type="text/css" />
   <?php echo $javascript ?>
</head>
<body>

<?php
print "<table class='navigator-table' align='center'><tr>";
print "<td>";

print "<div class='title'>$page_title</div>";
print "<div class='navigator-today'>";
print_link(trans('Back to today', '????????'), create_url($now['year'], $now['mon']));
print "</div>";

print "</td>";
print "<td>";

print "<div class='navigator'>";
/*print_link(trans('Previous year', '???????? ????????????'), create_url($year - 1, $month), back_arrow());*/
print " ";
$year_options = array();
foreach (range($year - 70, $year + 70) as $y) {
    $year_options[$y] = $y;
}
/*print_select_element('year', $options);*/

/*print_link(trans('Next year', '???????? ????????'), create_url($year + 1, $month), forward_arrow());*/

print "</div>"; // <!-- .navigator -->

print "<div class='navigator'>";

print_link(trans('Previous month', '?????????? ??????????'), create_url($year, $month - 1), '', 'right');
print " ";

$options = array(1 =>
  trans('January',  '??????????'),
  trans('February', '????????????'),
  trans('March',    '??????'),
  trans('April',    '??????????'),
  trans('May',      '??????'),
  trans('June',     '????????'),
  trans('July',     '????????'),
  trans('August',   '????????????'),
  trans('September', '????????????'),
  trans('October',  '??????????????'),
  trans('November', '????????????'),
  trans('December', '??????????')
);
print($options[$GLOBALS['month']] . ' ' . $year_options[$GLOBALS['year']]);

print_link(trans('Next month', '?????????? ??????'), create_url($year, $month + 1), '', 'left');

print "</div>"; // <!-- .navigator -->

$start_date_str = $jcal->getMediumDate(array('year'=>$year, 'mon'=>$month, 'mday'=>1));
$end_date_str   = $jcal->getMediumDate(array('year'=>$year, 'mon'=>$month, 'mday'=>cal_days_in_month(CAL_GREGORIAN, $month, $year)));

print "<div class='calendar-range'>";
print "$start_date_str &#x2013; $end_date_str";
print "</div>";

print "</td>";
print "</table>";

print "<fieldset id='preferences'>\n";
print "<legend>". trans('Preferences', '????????????') ."</legend>\n";

print 'Language:<br />';
$options = array(
  'en' => 'English',
  'he' => 'Hebrew',
);
print_select_element('language', $options);
print '<br />';

print trans('Method:', '????????:') .'<br />';
$options = array(
  'diaspora' => trans('Diaspora', '????????'),
  'israel'   => trans('Land of Israel', '?????? ??????????'),
);
print_select_element('method', $options);
print '<br />';

print trans('Sefirat HaOmer:', '?????????? ??????????:') .'<br />';
$options = array(
  '0' => trans('No',  '????'),
  '1' => trans('Yes', '????'),
);
print_select_element('sefirat_omer', $options);
print '<br />';

print trans('Holiday Eves:', '???????? ????????:') .'<br />';
$options = array(
  '0' => trans('No',  '????'),
  '1' => trans('Yes', '????'),
);
print_select_element('eves', $options);
print '<br />';

print trans('Isru Khags:', '???????? ????:') .'<br />';
$options = array(
  '0' => trans('No',  '????'),
  '1' => trans('Yes', '????'),
);
print_select_element('isru', $options);
print '<br />';

if ($language == 'he' && $method == 'diaspora') {
?>
  <div id="method-warning">
  ????????????: ???????????? ???????? ???????? ?????????? ???????????? ???? ?????? ????????, ???????? ???????? ???????? ?????????? ???? ?????????? ?????????? ?????? ??????
  ???????????? ???????? ??????????. ?????? ??"?????? ??????????" ???? ???????????? ???????????? "????????" ?????? ?????? ????????.
  </div>
<?php
}
print "</fieldset>\n";

// Hurray! finally the heart of this script:
print $jcal->printCal($year, $month);


