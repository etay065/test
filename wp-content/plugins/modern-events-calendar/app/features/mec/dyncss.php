<?php
/** no direct access **/
defined('MECEXEC') or die();

// Get MEC Style Options
$styling = $this->main->get_styling();

// colorskin
$color = '';

function mec_dyn_hex2rgb( $cc ) {
	if ( $cc[0] == '#' ) {
			$cc = substr( $cc, 1 );
	}
	if ( strlen( $cc ) == 6 ) {
			list( $r, $g, $b ) = array( $cc[0] . $cc[1], $cc[2] . $cc[3], $cc[4] . $cc[5] );
	} elseif ( strlen( $cc ) == 3 ) {
			list( $r, $g, $b ) = array( $cc[0] . $cc[0], $cc[1] . $cc[1], $cc[2] . $cc[2] );
	} else {
			return false;
	}
	$r = hexdec( $r );
	$g = hexdec( $g );
	$b = hexdec( $b );
	return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}


if(isset($styling['color']) && $styling['color']) $color = $styling['color'];
elseif(isset($styling['mec_colorskin'])) $color = $styling['mec_colorskin'];

$rgb_color = '64,217,241';
if ( !empty($color)) $rgb_color = mec_dyn_hex2rgb($color);

// Typography
$mec_h_fontfamily_arr = $mec_p_fontfamily_arr = $fonts_url = $mec_container_normal_width = $mec_container_large_width = '';

if(isset($styling['mec_h_fontfamily']) && $styling['mec_h_fontfamily'])
{
	$mec_h_fontfamily_arr = $styling['mec_h_fontfamily'];
	$mec_h_fontfamily_arr = str_replace("[", "", $mec_h_fontfamily_arr);
	$mec_h_fontfamily_arr = str_replace("]", "", $mec_h_fontfamily_arr);
	$mec_h_fontfamily_arr = explode(",", $mec_h_fontfamily_arr);
}

if(isset($styling['mec_p_fontfamily']) && $styling['mec_p_fontfamily'])
{
	$mec_p_fontfamily_arr = $styling['mec_p_fontfamily'];
	$mec_p_fontfamily_arr = str_replace("[", "", $mec_p_fontfamily_arr);
	$mec_p_fontfamily_arr = str_replace("]", "", $mec_p_fontfamily_arr);
	$mec_p_fontfamily_arr = explode(",", $mec_p_fontfamily_arr);
}

if((is_array($mec_h_fontfamily_arr) && $mec_h_fontfamily_arr) || (is_array($mec_p_fontfamily_arr) && $mec_p_fontfamily_arr))
{
	//Google font
	$font_families  = array();
	$subsets    	= 'latin,latin-ext';
	$variant_h		= '';
	$variant_p		= '';
	$mec_h_fontfamily_array = '';
	if ( is_array($mec_h_fontfamily_arr) && $mec_h_fontfamily_arr ) :
		foreach($mec_h_fontfamily_arr as $key=>$mec_h_fontfamily_array) {
			if($key != '0') $variant_h .= $mec_h_fontfamily_array .', ';
		}
    endif;

	if ( is_array($mec_p_fontfamily_arr) && $mec_p_fontfamily_arr ) :
		foreach($mec_p_fontfamily_arr as $key=>$mec_p_fontfamily_array) {
			if($key != '0') $variant_p .= $mec_h_fontfamily_array .', ';
		}
	endif;

	$font_families[] = !empty($mec_h_fontfamily_arr[0]) ? $mec_h_fontfamily_arr[0] . ':' . $variant_h : '';
	$font_families[] = !empty($mec_p_fontfamily_arr[0]) ? $mec_p_fontfamily_arr[0] . ':' . $variant_p : '';
    
	if($font_families)
    {
		$fonts_url = add_query_arg(array(
            'family'=>urlencode(implode('|', $font_families)),
            'subset'=>urlencode($subsets),
		), 'https://fonts.googleapis.com/css');
    }
}

if(isset($styling['container_normal_width']) && $styling['container_normal_width'])
{
	$mec_container_normal_width = trim( $styling['container_normal_width'] );
	if( $mec_container_normal_width ) {
		if (is_numeric($mec_container_normal_width)) {
			$mec_container_normal_width .= 'px';
		}
	}
}

if(isset($styling['container_large_width']) && $styling['container_large_width'])
{
	$mec_container_large_width = trim( $styling['container_large_width'] );
	if( $mec_container_large_width ) {
		if (is_numeric($mec_container_large_width)) {
			$mec_container_large_width .= 'px';
		}
	}
}
$title_color = $title_color_hover = $content_color = '';
if(isset($styling['title_color']) && $styling['title_color'])
{
	$title_color = $styling['title_color'];
}

if(isset($styling['title_color_hover']) && $styling['title_color_hover'])
{
	$title_color_hover = $styling['title_color_hover'];
}

if(isset($styling['content_color']) && $styling['content_color'])
{
	$content_color = $styling['content_color'];
}

ob_start();

// render headings font familty
if($mec_h_fontfamily_arr): ?>
	/* == Custom Fonts For H Tag
		---------------- */
	.mec-hourly-schedule-speaker-name, .mec-hourly-schedule-speaker-job-title, .post-type-archive-mec-events h1, .tax-mec_category h1, .mec-wrap h1, .mec-wrap h2, .mec-wrap h3, .mec-wrap h4, .mec-wrap h5, .mec-wrap h6,.entry-content .mec-wrap h1, .entry-content .mec-wrap h2, .entry-content .mec-wrap h3,.entry-content  .mec-wrap h4, .entry-content .mec-wrap h5, .entry-content .mec-wrap h6
	{ font-family: '<?php echo $mec_h_fontfamily_arr[0]; ?>', Helvetica, Arial, sans-serif !important;}
<?php endif;

// render paragraph font familty
if($mec_p_fontfamily_arr): ?>
	/* == Custom Fonts For P Tag
		---------------- */
	.mec-single-event .mec-event-meta .mec-events-event-cost, .mec-next-occ-booking span, .mec-hourly-schedule-speaker-description, .mec-single-event .mec-speakers-details ul li .mec-speaker-job-title, .mec-single-event .mec-speakers-details ul li .mec-speaker-name, .mec-event-data-field-items, .mec-load-more-button, .mec-events-meta-group-tags a, .mec-events-button, .mec-single-event .mec-event-meta dt, .mec-wrap abbr, .mec-event-schedule-content dl dt, .mec-breadcrumbs a, .mec-breadcrumbs span .mec-event-content p, .mec-wrap p { font-family: '<?php echo $mec_p_fontfamily_arr[0]; ?>',sans-serif; font-weight:300 !important;}
<?php endif;

// render colorskin
if($color && $color != '#40d9f1'): ?>
	/* == TextColors
		---------------- */
	.mec-event-grid-minimal .mec-modal-booking-button:hover, .mec-timeline-event .mec-modal-booking-button, .mec-timetable-t2-col .mec-modal-booking-button:hover, .mec-event-container-classic .mec-modal-booking-button:hover, .mec-calendar-events-side .mec-modal-booking-button:hover, .mec-event-grid-yearly  .mec-modal-booking-button, .mec-events-agenda .mec-modal-booking-button, .mec-event-grid-simple .mec-modal-booking-button, .mec-event-list-minimal  .mec-modal-booking-button:hover, .mec-timeline-month-divider,  .mec-wrap.colorskin-custom .mec-totalcal-box .mec-totalcal-view span:hover,.mec-wrap.colorskin-custom .mec-calendar.mec-event-calendar-classic .mec-selected-day,.mec-wrap.colorskin-custom .mec-color, .mec-wrap.colorskin-custom .mec-event-sharing-wrap .mec-event-sharing > li:hover a, .mec-wrap.colorskin-custom .mec-color-hover:hover, .mec-wrap.colorskin-custom .mec-color-before *:before ,.mec-wrap.colorskin-custom .mec-widget .mec-event-grid-classic.owl-carousel .owl-nav i,.mec-wrap.colorskin-custom .mec-event-list-classic a.magicmore:hover,.mec-wrap.colorskin-custom .mec-event-grid-simple:hover .mec-event-title,.mec-wrap.colorskin-custom .mec-single-event .mec-event-meta dd.mec-events-event-categories:before,.mec-wrap.colorskin-custom .mec-single-event-date:before,.mec-wrap.colorskin-custom .mec-single-event-time:before,.mec-wrap.colorskin-custom .mec-events-meta-group.mec-events-meta-group-venue:before,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-side .mec-previous-month i,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-side .mec-next-month,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-side .mec-previous-month:hover,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-side .mec-next-month:hover,.mec-wrap.colorskin-custom .mec-calendar.mec-event-calendar-classic dt.mec-selected-day:hover,.mec-wrap.colorskin-custom .mec-infowindow-wp h5 a:hover, .colorskin-custom .mec-events-meta-group-countdown .mec-end-counts h3,.mec-calendar .mec-calendar-side .mec-next-month i,.mec-wrap .mec-totalcal-box i,.mec-calendar .mec-event-article .mec-event-title a:hover,.mec-attendees-list-details .mec-attendee-profile-link a:hover,.mec-wrap.colorskin-custom .mec-next-event-details li i, .mec-next-event-details i:before, .mec-marker-infowindow-wp .mec-marker-infowindow-count, .mec-next-event-details a,.mec-wrap.colorskin-custom .mec-events-masonry-cats a.mec-masonry-cat-selected,.lity .mec-color,.lity .mec-color-before :before,.lity .mec-color-hover:hover,.lity .mec-wrap .mec-color,.lity .mec-wrap .mec-color-before :before,.lity .mec-wrap .mec-color-hover:hover,.leaflet-popup-content .mec-color,.leaflet-popup-content .mec-color-before :before,.leaflet-popup-content .mec-color-hover:hover,.leaflet-popup-content .mec-wrap .mec-color,.leaflet-popup-content .mec-wrap .mec-color-before :before,.leaflet-popup-content .mec-wrap .mec-color-hover:hover, .mec-calendar.mec-calendar-daily .mec-calendar-d-table .mec-daily-view-day.mec-daily-view-day-active.mec-color, .mec-map-boxshow div .mec-map-view-event-detail.mec-event-detail i,.mec-map-boxshow div .mec-map-view-event-detail.mec-event-detail:hover,.mec-map-boxshow .mec-color,.mec-map-boxshow .mec-color-before :before,.mec-map-boxshow .mec-color-hover:hover,.mec-map-boxshow .mec-wrap .mec-color,.mec-map-boxshow .mec-wrap .mec-color-before :before,.mec-map-boxshow .mec-wrap .mec-color-hover:hover
	{color: <?php echo $color; ?>}

	/* == Backgrounds
		----------------- */
	.mec-skin-carousel-container .mec-event-footer-carousel-type3 .mec-modal-booking-button:hover, .mec-wrap .mec-map-lightbox-wp.mec-event-list-classic .mec-event-date,.mec-wrap.colorskin-custom .mec-event-sharing .mec-event-share:hover .event-sharing-icon,.mec-wrap.colorskin-custom .mec-event-grid-clean .mec-event-date,.mec-wrap.colorskin-custom .mec-event-list-modern .mec-event-sharing > li:hover a i,.mec-wrap.colorskin-custom .mec-event-list-modern .mec-event-sharing .mec-event-share:hover .mec-event-sharing-icon,.mec-wrap.colorskin-custom .mec-event-list-modern .mec-event-sharing li:hover a i,.mec-wrap.colorskin-custom .mec-calendar:not(.mec-event-calendar-classic) .mec-selected-day,.mec-wrap.colorskin-custom .mec-calendar .mec-selected-day:hover,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-row  dt.mec-has-event:hover,.mec-wrap.colorskin-custom .mec-calendar .mec-has-event:after, .mec-wrap.colorskin-custom .mec-bg-color, .mec-wrap.colorskin-custom .mec-bg-color-hover:hover, .colorskin-custom .mec-event-sharing-wrap:hover > li, .mec-wrap.colorskin-custom .mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected,.mec-wrap .flip-clock-wrapper ul li a div div.inn,.mec-wrap .mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected,.event-carousel-type1-head .mec-event-date-carousel,.mec-event-countdown-style3 .mec-event-date,#wrap .mec-wrap article.mec-event-countdown-style1,.mec-event-countdown-style1 .mec-event-countdown-part3 a.mec-event-button,.mec-wrap .mec-event-countdown-style2,.mec-map-get-direction-btn-cnt input[type="submit"],.mec-booking button,span.mec-marker-wrap,.mec-wrap.colorskin-custom .mec-timeline-events-container .mec-timeline-event-date:before
	{background-color: <?php echo $color; ?>;}

	

	/* == BorderColors
		------------------ */
	.mec-skin-carousel-container .mec-event-footer-carousel-type3 .mec-modal-booking-button:hover, .mec-timeline-month-divider, .mec-wrap.colorskin-custom .mec-single-event .mec-speakers-details ul li .mec-speaker-avatar a:hover img,.mec-wrap.colorskin-custom .mec-event-list-modern .mec-event-sharing > li:hover a i,.mec-wrap.colorskin-custom .mec-event-list-modern .mec-event-sharing .mec-event-share:hover .mec-event-sharing-icon,.mec-wrap.colorskin-custom .mec-event-list-standard .mec-month-divider span:before,.mec-wrap.colorskin-custom .mec-single-event .mec-social-single:before,.mec-wrap.colorskin-custom .mec-single-event .mec-frontbox-title:before,.mec-wrap.colorskin-custom .mec-calendar .mec-calendar-events-side .mec-table-side-day, .mec-wrap.colorskin-custom .mec-border-color, .mec-wrap.colorskin-custom .mec-border-color-hover:hover, .colorskin-custom .mec-single-event .mec-frontbox-title:before, .colorskin-custom .mec-single-event .mec-events-meta-group-booking form > h4:before, .mec-wrap.colorskin-custom .mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected,.mec-wrap .mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected,.event-carousel-type1-head .mec-event-date-carousel:after,.mec-wrap.colorskin-custom .mec-events-masonry-cats a.mec-masonry-cat-selected, .mec-marker-infowindow-wp .mec-marker-infowindow-count, .mec-wrap.colorskin-custom .mec-events-masonry-cats a:hover
	{border-color: <?php echo $color; ?>;}
	.mec-wrap.colorskin-custom .mec-event-countdown-style3 .mec-event-date:after,.mec-wrap.colorskin-custom .mec-month-divider span:before
	{border-bottom-color:<?php echo $color; ?>;}
	.mec-wrap.colorskin-custom  article.mec-event-countdown-style1 .mec-event-countdown-part2:after
	{border-color: transparent transparent transparent <?php echo $color; ?>;}

	/* == BoxShadow
		------------------ */
	.mec-wrap.colorskin-custom .mec-box-shadow-color { box-shadow: 0 4px 22px -7px <?php echo $color; ?>;}


	/* == Timeline View
		------------------ */
	.mec-timeline-event .mec-modal-booking-button, .mec-events-timeline-wrap:before, .mec-wrap.colorskin-custom .mec-timeline-event-local-time, .mec-wrap.colorskin-custom .mec-timeline-event-time ,.mec-wrap.colorskin-custom .mec-timeline-event-location { background: rgba(<?php echo $rgb_color['red']; ?>,<?php echo $rgb_color['green']; ?>,<?php echo $rgb_color['blue']; ?>,.11);}
	.mec-wrap.colorskin-custom .mec-timeline-events-container .mec-timeline-event-date:after { background: rgba(<?php echo $rgb_color['red']; ?>,<?php echo $rgb_color['green']; ?>,<?php echo $rgb_color['blue']; ?>,.3);}
<?php endif;

// Render Container Width
if($mec_container_normal_width): ?>
@media only screen and (min-width: 1281px) {
	.mec-container,
    body [id*="mec_skin_"].mec-fluent-wrap {
        width: <?php echo $mec_container_normal_width; ?> !important;
        max-width: <?php echo $mec_container_normal_width; ?> !important;
    }
}
<?php endif;


if($mec_container_large_width): ?>
@media only screen and (min-width: 1600px) {
	.mec-container,
    body [id*="mec_skin_"].mec-fluent-wrap {
        width: <?php echo $mec_container_large_width; ?> !important;
        max-width: <?php echo $mec_container_large_width; ?> !important;
    }
}
<?php endif;

if($title_color): ?>
.mec-wrap h1 a, .mec-wrap h2 a, .mec-wrap h3 a, .mec-wrap h4 a, .mec-wrap h5 a, .mec-wrap h6 a,.entry-content .mec-wrap h1 a, .entry-content .mec-wrap h2 a, .entry-content .mec-wrap h3 a,.entry-content  .mec-wrap h4 a, .entry-content .mec-wrap h5 a, .entry-content .mec-wrap h6 a {
	color: <?php echo $title_color; ?> !important;
}
<?php endif;

if($title_color_hover): ?>
.mec-wrap.colorskin-custom h1 a:hover, .mec-wrap.colorskin-custom h2 a:hover, .mec-wrap.colorskin-custom h3 a:hover, .mec-wrap.colorskin-custom h4 a:hover, .mec-wrap.colorskin-custom h5 a:hover, .mec-wrap.colorskin-custom h6 a:hover,.entry-content .mec-wrap.colorskin-custom h1 a:hover, .entry-content .mec-wrap.colorskin-custom h2 a:hover, .entry-content .mec-wrap.colorskin-custom h3 a:hover,.entry-content  .mec-wrap.colorskin-custom h4 a:hover, .entry-content .mec-wrap.colorskin-custom h5 a:hover, .entry-content .mec-wrap.colorskin-custom h6 a:hover {
	color: <?php echo $title_color_hover; ?> !important;
}
<?php endif;

if($content_color): ?>
.mec-wrap.colorskin-custom .mec-event-description {
	color: <?php echo $content_color; ?>;
}
<?php endif;

if (isset($styling['disable_fluent_height_limitation']) && $styling['disable_fluent_height_limitation']) {
	?>
	.mec-fluent-wrap.mec-skin-list-wrap .mec-calendar,
	.mec-fluent-wrap .mec-skin-weekly-view-events-container,
	.mec-fluent-wrap .mec-daily-view-events-left-side,
	.mec-fluent-wrap .mec-daily-view-events-right-side,
	.mec-fluent-wrap .mec-yearly-view-wrap .mec-yearly-calendar-sec,
	.mec-fluent-wrap .mec-yearly-view-wrap .mec-yearly-agenda-sec,
	.mec-fluent-wrap.mec-skin-grid-wrap .mec-calendar,
	.mec-fluent-wrap.mec-skin-tile-container .mec-calendar,
	.mec-fluent-wrap.mec-events-agenda-container .mec-events-agenda-wrap {
		max-height: unset !important;
	}
	<?php
}

/**
 * 
 * Fluent-view Layout Color Styles
 * 
 */
// Main Color
$fluent_main_color = '#ade7ff';
if (isset($styling['fluent_main_color']) && $styling['fluent_main_color']) {
	$fluent_main_color = $styling['fluent_main_color'];
	list($fluent_main_color_r, $fluent_main_color_g, $fluent_main_color_b) = sscanf($fluent_main_color, "#%02x%02x%02x");
	?>
	/* MAIN COLOR */
	.mec-more-events-icon, .mec-fluent-wrap .mec-daily-view-events-left-side .mec-daily-view-events-item>span.mec-time, .mec-fluent-wrap .mec-daily-view-events-left-side .mec-daily-view-events-item>span.mec-time-end, .mec-fluent-wrap .mec-calendar.mec-calendar-daily .mec-calendar-d-table.mec-date-labels-container span, .mec-fluent-wrap .mec-calendar .mec-week-events-container dl>span, .mec-fluent-current-time-text, .mec-fluent-wrap.mec-timetable-wrap .mec-cell .mec-time, .mec-fluent-wrap.mec-skin-masonry-container .mec-events-masonry-cats a:hover, .mec-fluent-wrap.mec-skin-masonry-container .mec-events-masonry-cats a.mec-masonry-cat-selected, .mec-fluent-wrap .mec-date-details i:before, .mec-fluent-wrap .mec-event-location i:before, .mec-fluent-wrap .mec-event-carousel-type2 .owl-next i, .mec-fluent-wrap .mec-event-carousel-type2 .owl-prev i, .mec-fluent-wrap .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-next i, .mec-fluent-wrap .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-prev i, .mec-fluent-wrap .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-next, .mec-fluent-wrap .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-prev, .mec-fluent-wrap .mec-date-wrap i, .mec-fluent-wrap .mec-calendar.mec-yearly-calendar .mec-calendar-table-head dl dt:first-letter, .mec-event-sharing-wrap .mec-event-sharing li:hover a, .mec-fluent-wrap .mec-agenda-event>i, .mec-fluent-wrap .mec-totalcal-box .nice-select:after, .mec-fluent-wrap .mec-totalcal-box .mec-totalcal-view span, .mec-fluent-wrap .mec-totalcal-box input, .mec-fluent-wrap .mec-totalcal-box select, .mec-fluent-wrap .mec-totalcal-box .nice-select, .mec-fluent-wrap .mec-totalcal-box .nice-select .list li, .mec-fluent-wrap .mec-text-input-search i, .mec-fluent-wrap .mec-event-location i, .mec-fluent-wrap .mec-event-article .mec-event-title a:hover, .mec-fluent-wrap .mec-date-details:before, .mec-fluent-wrap .mec-time-details:before, .mec-fluent-wrap .mec-venue-details:before, .mec-fluent-wrap .mec-price-details i:before, .mec-fluent-wrap .mec-available-tickets-details i:before, .mec-fluent-wrap .mec-booking-button {
		color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-totalcal-box input[type="search"]::-webkit-input-placeholder {
		color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-totalcal-box input[type="search"]::-moz-placeholder {
		color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-totalcal-box input[type="search"]:-ms-input-placeholder {
		color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-totalcal-box input[type="search"]:-moz-placeholder {
		color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl dt.mec-table-nullday {
		color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.3)'; ?> !important;
	}
	.mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl dt:hover {
		color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.8)'; ?> !important;
	}
	.mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type2 .mec-event-sharing-wrap:hover li a, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type1 .mec-booking-button, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type4 .mec-booking-button, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type3 .mec-booking-button {
		color: #fff !important;
	}
	.mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type4 .mec-booking-button:hover, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type3 .mec-booking-button:hover, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type1 .mec-booking-button:hover {
		color: <?php echo $fluent_main_color; ?> !important;
	}

	/* BORDER COLOR */
	/* list view */
	.mec-fluent-wrap.mec-skin-list-wrap .mec-event-article {
		border-top-color: <?php echo $fluent_main_color; ?> !important;
		border-left-color: <?php echo $fluent_main_color; ?> !important;
		border-bottom-color: <?php echo $fluent_main_color; ?> !important;
	}
	/* list view */
	.mec-fluent-wrap.mec-skin-grid-wrap .mec-event-article .mec-event-content {
		border-right-color: <?php echo $fluent_main_color; ?> !important;
		border-left-color: <?php echo $fluent_main_color; ?> !important;
		border-bottom-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap.mec-skin-grid-wrap .mec-event-article .mec-event-image {
		border-right-color: <?php echo $fluent_main_color; ?> !important;
		border-left-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top .mec-current-week, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic .mec-calendar-table-head, .mec-fluent-wrap .mec-yearly-view-wrap .mec-year-container, .mec-fluent-wrap.mec-events-agenda-container .mec-events-agenda-wrap, .mec-fluent-wrap .mec-totalcal-box .mec-totalcal-view span, .mec-fluent-wrap .mec-totalcal-box input, .mec-fluent-wrap .mec-totalcal-box select, .mec-fluent-wrap .mec-totalcal-box .nice-select, .mec-fluent-wrap .mec-load-more-button:hover, .mec-fluent-wrap .mec-booking-button, .mec-fluent-wrap .mec-skin-monthly-view-month-navigator-container, .mec-fluent-wrap .mec-calendar-a-month, .mec-fluent-wrap .mec-yearly-title-sec, .mec-fluent-wrap .mec-filter-content, .mec-fluent-wrap i.mec-filter-icon, .mec-fluent-wrap .mec-text-input-search input[type="search"], .mec-fluent-wrap .mec-event-sharing-wrap .mec-event-sharing, .mec-fluent-wrap .mec-load-month, .mec-fluent-wrap .mec-load-year{
		border-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-current-time-first, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top .mec-load-week, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl dt:first-of-type {
		border-left-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-current-time-last, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top .mec-current-week, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl dt:last-of-type {
		border-right-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-more-events, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl:last-of-type dt, .mec-fluent-wrap.mec-skin-full-calendar-container>.mec-totalcal-box .mec-totalcal-view .mec-fluent-more-views-content:before, .mec-fluent-wrap .mec-filter-content:before {
		border-bottom-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-event-sharing-wrap .mec-event-sharing:before {
		border-color: <?php echo $fluent_main_color; ?> transparent transparent transparent  !important;
	}
	.mec-fluent-wrap.mec-timetable-wrap .mec-cell, .mec-fluent-wrap .mec-event-meta {
		border-left-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap .mec-daily-view-events-left-side, .mec-fluent-wrap .mec-yearly-view-wrap .mec-yearly-calendar-sec {
		border-right-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap.mec-events-agenda-container .mec-agenda-events-wrap {
		border-left-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap dt .mec-more-events .simple-skin-ended:hover, .mec-fluent-wrap .mec-more-events .simple-skin-ended:hover, .mec-fluent-wrap.mec-skin-slider-container .mec-slider-t1 .mec-slider-t1-content, .mec-fluent-wrap.mec-events-agenda-container .mec-events-agenda {
		border-top-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
		border-bottom-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap .mec-calendar.mec-calendar-daily .mec-calendar-d-table, .mec-fluent-wrap.mec-timetable-wrap .mec-ttt2-title, .mec-fluent-wrap.mec-timetable-wrap .mec-cell, .mec-fluent-wrap.mec-skin-countdown-container .mec-date-wrap {
		border-bottom-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap .mec-skin-daily-view-events-container, .mec-fluent-wrap .mec-skin-weekly-view-events-container, .mec-fluent-wrap .mec-calendar .mec-week-events-container dt, .mec-fluent-wrap.mec-timetable-wrap .mec-timetable-t2-wrap, .mec-fluent-wrap .mec-event-countdown li, .mec-fluent-wrap .mec-event-countdown-style3 .mec-event-countdown li, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic dl dt, .mec-fluent-wrap .mec-yearly-view-wrap .mec-agenda-event, .mec-fluent-wrap .mec-yearly-view-wrap .mec-calendar.mec-yearly-calendar, .mec-fluent-wrap .mec-load-more-button, .mec-fluent-wrap .mec-totalcal-box .nice-select .list, .mec-fluent-wrap .mec-filter-content i {
		border-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.3)'; ?> !important;
	}
	.mec-fluent-wrap .mec-totalcal-box .nice-select:after {
		border-right-color: <?php echo $fluent_main_color; ?> !important;
		border-bottom-color: <?php echo $fluent_main_color; ?> !important;
	}

	/* BOXSHADOW */
	.mec-fluent-wrap .mec-totalcal-box .nice-select .list {
		box-shadow: 0 2px 5px rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.2)'; ?> !important;
	}
	.mec-fluent-wrap .mec-booking-button:hover, .mec-fluent-wrap .mec-load-more-button:hover, .mec-fluent-bg-wrap .mec-fluent-wrap article .mec-booking-button:hover {
		box-shadow: 0 4px 10px rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.2)'; ?> !important;
	}
	.mec-fluent-wrap.mec-skin-grid-wrap .mec-event-article {
		box-shadow: 0 4px 10px rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.7)'; ?> !important;
	}
	.mec-fluent-wrap .mec-skin-daily-view-events-container, .mec-fluent-wrap.mec-timetable-wrap .mec-timetable-t2-wrap, .mec-fluent-wrap .mec-calendar-side .mec-calendar-table, .mec-fluent-wrap .mec-yearly-view-wrap .mec-year-container, .mec-fluent-wrap.mec-events-agenda-container .mec-events-agenda-wrap {
		box-shadow: 0 5px 33px rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.3)'; ?> !important;
	}
	.mec-fluent-wrap .mec-yearly-view-wrap .mec-agenda-event {
		box-shadow: 0 1px 6px rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.05)'; ?> !important;
	}

	/* BACKGROUND */
	/* filter options */
	.nicescroll-cursors, .mec-fluent-wrap dt .mec-more-events .simple-skin-ended:hover, .mec-fluent-wrap .mec-more-events .simple-skin-ended:hover, .mec-fluent-wrap.mec-skin-countdown-container .mec-date-wrap, .mec-fluent-wrap .mec-yearly-view-wrap .mec-yearly-agenda-sec, .mec-fluent-wrap .mec-calendar-daily .mec-calendar-day-events, .mec-fluent-wrap .mec-totalcal-box .nice-select .list li:hover, .mec-fluent-wrap .mec-totalcal-box .nice-select .list li.focus {
		background-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.1)'; ?> !important;
	}
	.mec-fluent-wrap h5.mec-more-events-header, .mec-fluent-current-time {
		background-color: <?php echo $fluent_main_color; ?> !important;
	}
	.mec-fluent-wrap .mec-yearly-view-wrap .mec-agenda-events-wrap {
		background-color: transparent !important;
	}
	.mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type1 .mec-date-wrap i, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type4 .mec-date-wrap i, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type2 .mec-date-wrap i {
		background-color: #fff !important;
	}
	.mec-fluent-wrap.mec-skin-cover-container .mec-date-wrap i, .mec-fluent-wrap.mec-skin-carousel-container .mec-event-carousel-type2 .owl-next:hover, .mec-fluent-wrap.mec-skin-carousel-container .mec-event-carousel-type2 .owl-prev:hover, .mec-fluent-wrap.mec-skin-slider-container .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-next:hover, .mec-fluent-wrap.mec-skin-slider-container .mec-slider-t1-wrap .mec-owl-theme .owl-nav .owl-prev:hover, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top dt.active, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top .mec-current-week, .mec-fluent-wrap.mec-skin-full-calendar-container>.mec-totalcal-box .mec-totalcal-view span.mec-fluent-more-views-icon.active, .mec-fluent-wrap.mec-skin-full-calendar-container>.mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected, .mec-fluent-wrap i.mec-filter-icon.active, .mec-fluent-wrap .mec-filter-content i {
		background-color: rgba<?php echo '(' . $fluent_main_color_r . ', ' . $fluent_main_color_g . ', ' . $fluent_main_color_b . ', ' . '0.3)'; ?> !important;
	}
	<?php
}
// Bold Color - Second
$fluent_bold_color = '#00acf8';
if (isset($styling['fluent_bold_color']) && $styling['fluent_bold_color']) {
	$fluent_bold_color = $styling['fluent_bold_color'];
	?>
	/* MAIN BOLD COLOR - SECOND COLOR */
	.mec-fluent-wrap .mec-daily-view-events-left-side h5.mec-daily-today-title span:first-child, .mec-fluent-wrap.mec-skin-available-spot-container .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap.mec-skin-cover-container .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap.mec-skin-countdown-container .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap.mec-skin-carousel-container .event-carousel-type2-head .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap.mec-skin-slider-container .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap.mec-skin-masonry-container .mec-masonry .mec-date-wrap span.mec-event-day-num, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top dt.active, .mec-fluent-wrap .mec-calendar-weekly .mec-calendar-d-top .mec-current-week, .mec-fluent-wrap .mec-calendar.mec-event-calendar-classic .mec-calendar-table-head dt.active, .mec-fluent-wrap .mec-color, .mec-fluent-wrap a:hover, .mec-wrap .mec-color-hover:hover, .mec-fluent-wrap.mec-skin-full-calendar-container>.mec-totalcal-box .mec-totalcal-view span.mec-totalcalview-selected, .mec-fluent-wrap .mec-booking-button, .mec-fluent-wrap .mec-load-more-button, .mec-fluent-wrap .mec-load-month i, .mec-fluent-wrap .mec-load-year i, .mec-fluent-wrap i.mec-filter-icon, .mec-fluent-wrap .mec-filter-content i, .mec-fluent-wrap .mec-event-sharing-wrap>li:first-of-type i,	.mec-fluent-wrap .mec-available-tickets-details span.mec-available-tickets-number {
		color: <?php echo $fluent_bold_color; ?> !important;
	}
	.mec-fluent-wrap.mec-skin-cover-container .mec-event-sharing-wrap>li:first-of-type i, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type2 span.mec-event-day-num, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type2 .mec-event-sharing-wrap:hover li:first-child a {
		color: #fff !important;
	}

	/* BORDER BOLD COLOR - SECOND COLOR */
	.mec-fluent-wrap.mec-skin-carousel-container .mec-owl-theme .owl-dots .owl-dot.active span, .mec-fluent-wrap .mec-load-month, .mec-fluent-wrap .mec-load-year {
		border-color: <?php echo $fluent_bold_color; ?> !important;
	}
	.mec-fluent-wrap .mec-calendar .mec-daily-view-day.mec-has-event:after, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type4 .mec-booking-button, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type3 .mec-booking-button, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type1 .mec-booking-button, .mec-fluent-wrap .mec-event-cover-fluent-type2 .mec-event-sharing-wrap:hover>li:first-child, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type2 .mec-date-wrap, .mec-fluent-wrap.mec-skin-carousel-container .mec-owl-theme .owl-dots .owl-dot.active span {
		background-color: <?php echo $fluent_bold_color; ?> !important;
	}
	.mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type1 .mec-booking-button:hover, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type4 .mec-booking-button:hover, .mec-fluent-wrap.mec-skin-cover-container .mec-event-cover-fluent-type3 .mec-booking-button:hover {
		background-color: #fff !important;
	}
	<?php
}

// Background Hover Color
$fluent_bg_hover_color = '#ebf9ff';
if (isset($styling['fluent_bg_hover_color']) && $styling['fluent_bg_hover_color']) {
	$fluent_bg_hover_color = $styling['fluent_bg_hover_color'];
	?>
	/* BACKGROUND COLOR */
	.mec-fluent-wrap .mec-yearly-view-wrap .mec-calendar.mec-yearly-calendar .mec-has-event:after, .mec-fluent-wrap .mec-load-more-button:hover, .mec-fluent-wrap .mec-load-month:hover, .mec-fluent-wrap .mec-load-year:hover, .mec-fluent-wrap .mec-booking-button:hover {
		background-color: <?php echo $fluent_bg_hover_color; ?> !important;
	}
	<?php
}

// Background Color
$fluent_bg_color = '#ade7ff';
if (isset($styling['fluent_bg_color']) && $styling['fluent_bg_color']) {
	$fluent_bg_color = $styling['fluent_bg_color'];
	?>
	/* BACKGROUND COLOR */
	.mec-fluent-wrap {
		background-color: <?php echo $fluent_bg_color; ?> !important;
	}
	<?php
}

// Second Background Color
$fluent_second_bg_color = '#d6eef9';
if (isset($styling['fluent_second_bg_color']) && $styling['fluent_second_bg_color']) {
	$fluent_second_bg_color = $styling['fluent_second_bg_color'];
	?>
	/* BACKGROUND COLOR */
	.mec-fluent-wrap.mec-skin-masonry-container .mec-masonry .mec-date-wrap, .mec-fluent-wrap .mec-filter-content {
		background-color: <?php echo $fluent_second_bg_color; ?> !important;
	}

	.mec-fluent-wrap .mec-filter-content:after {
		border-bottom-color:<?php echo $fluent_second_bg_color; ?> !important;
	}
	<?php
}

// get render content
$out = '';
$out = ob_get_clean();

// minify css
$out = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $out);
$out = str_replace(array("\r\n", "\r", "\n", "\t", '    '), '', $out);

update_option('mec_gfont', $fonts_url);
update_option('mec_dyncss', $out);