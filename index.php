<?php
/*
 * Plugin Name: Finding Leo
 */

function leo_scripts() {
    wp_register_style( 'leo-styles',  plugin_dir_url( __FILE__ ) . 'css.css' );
    wp_enqueue_style( 'leo-styles' );
    wp_register_script( 'leo-script',  plugin_dir_url( __FILE__ ) . 'js.js' );
    wp_enqueue_script( 'leo-script' );
};
add_action( 'wp_enqueue_scripts', 'leo_scripts' );



function do_findingleo( $atts ) {
	extract( shortcode_atts(
		array(
			'gpx' => '',
		), $atts )
	);
    $txt = '';
    $gpx_at = $_SERVER['DOCUMENT_ROOT'].substr($atts['gpx'], strpos($atts['gpx'],'/wp-content'));


    if(file_exists($gpx_at)):
        $gpx = simplexml_load_file($gpx_at);
        $leo = $gpx->trk->trkseg;

        $txt.= '<ol class="lingers">';
        $n = 0;
        $a_meter = 0.000009;
        $a_move = $a_meter*4;
        $sitting_still = 0;
        $sitting_still_is = 12 ; //units of 10 seconds
        $moving = 0;
        while ($leo->trkpt[$n]):
            if($leo->trkpt[($n+1)]):
                $the_now_lat = 0 + floatval(trim($leo->trkpt[$n]['lon']));
                $the_now_lon = 0 + floatval(trim($leo->trkpt[$n]['lat']));
                $the_next_lat = 0 + floatval(trim($leo->trkpt[$n+1]['lon']));
                $the_next_lon = 0 + floatval(trim($leo->trkpt[$n+1]['lat']));
                $the_gap_lon = number_format(floatval($the_now_lon - $the_next_lon),10);
                $the_gap_lat = number_format(floatval($the_now_lat - $the_next_lat),10);
                if(
                    ( ($the_gap_lon <  $a_move) && ($the_gap_lat < $a_move) )
                        &&
                    ( ($the_gap_lat > (0-$a_move)) && ($the_gap_lat > (0-$a_move)) )
                    ):
                        $the_now = new DateTime($leo->trkpt[$n]->time);
                        $sitting_still++;
                        $moving = 0;
                        if($sitting_still==$sitting_still_is):
    //                        echo '<li class="static" data-lat="'.$leo->trkpt[$n]['lat'].'" data-lon="'.$leo->trkpt[$n]['lon'].'">'.$the_now_lon.','.$the_now_lat.' Leo static at '.$leo->trkpt[$n]->time.'<br>'.$the_next_lon.','.$the_next_lat.'</li>';
                              $txt.= '<li class="static" data-lat="'.$leo->trkpt[$n]['lat'].'" data-lon="'.$leo->trkpt[$n]['lon'].'">'.$the_now->format('h:i').' Leo <b>Lingers</b> here</li>';
                        endif;
                    else:
                        $moving++;
                        if($moving>1):
                            $moving = 0;
                            $sitting_still = 0;
                        endif;
    //                    echo '<li class="moving" data-lat="'.$leo->trkpt[$n]['lat'].'" data-lon="'.$leo->trkpt[$n]['lon'].'">'.$the_now_lon.','.$the_now_lat.' Leo moves at '.$leo->trkpt[$n]->time.'</li>';
                endif;
            endif;
            $n++;
        endwhile;
        $txt.= '</ol>';

        $txt.= '<ol class="lingers">';
        $n = 0;
        while ($leo->trkpt[$n]):
            if($leo->trkpt[($n+1)]):
                $the_now_d = new DateTime($leo->trkpt[$n]->time);
                $the_next_d =  new DateTime($leo->trkpt[($n+1)]->time);
                $the_now = strtotime($leo->trkpt[$n]->time);
                $the_next = strtotime($leo->trkpt[($n+1)]->time);
                $the_sleep = $the_next - $the_now ;
                if($the_sleep>(($sitting_still_is*10)*2.5)):
                    $txt.= '<li data-lat="'.$leo->trkpt[$n]['lat'].'" data-lon="'.$leo->trkpt[$n]['lon'].'"><h2>'.$the_now_d->format('h:i').' Leo <b>under cover</b> for '.round(($the_sleep/60),0).' minutes.</li>';
                endif;
            endif;
            $n++;
        endwhile;

        $txt.= '</ol>';

	return $txt;

    else:
        exit('Failed to open xml.');
    endif;

}

add_shortcode( 'findingleo', 'do_findingleo' );


?>
