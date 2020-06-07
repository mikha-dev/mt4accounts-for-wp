<?php
$MT4_Instrument_Plugin = new MT4_Instrument_Plugin();

class MT4_Instrument_Plugin{

  private $output;

  function __construct(){

    // load core lib at template_redirect because we need the post data!
    add_action( "template_redirect", array( &$this, 'mt4_instrument_header' ) );

    // load js and css files
    add_action( "wp_enqueue_scripts", array( &$this, 'wp_enqueue_scripts' ) );

    add_shortcode( "mt4_instruments", array( &$this, 'shortcode_mt4_instrument' ) );
  }

  function mt4_instrument_header() {
    global $post;

    $regex_pattern = get_shortcode_regex();

    preg_match_all('/'.$regex_pattern.'/s', $post->post_content, $regex_matches);

    if(count($regex_matches) < 4) {
      return;
    }

    $i = 0;
    foreach($regex_matches[2] as $m) {
      if ($m == 'mt4_instrument') {
        $attribute_str = str_replace (" ", "&", trim ($regex_matches[3][$i]));
        $attribute_str = str_replace ('"', '', $attribute_str);

        $defaults = array ();
        $attributes = wp_parse_args($attribute_str, $defaults);

        $this->output = $this->format_gain_output($attributes);
      }

      $i++;
    }

  }

  function format_gain_output($attributes) {
    global $mt4_accounts;


    if (isset($attributes['account_number'])){
      $account_number = $attributes['account_number'];
    } else {
      $account_number = $_REQUEST['acc'];
    }

    if(empty($account_number)) {
      return false;
    }

   if(isset($attributes['template']))
      $template = $attributes['template']?$attributes['template']:'default';
    else
      $template = 'default';

    if (isset($attributes['caption'])){
      $caption = $attributes['caption'];
    } else
      $caption = '';

    ob_start();
    $items = mt4accounts_get_api()->get_instruments($account_number);
    include_once( dirname( __FILE__ ) . "/templates/{$template}/instrument.php");

    $data = ob_get_contents();
    ob_clean();

    return $data;
  }

  function wp_enqueue_scripts()  {

    wp_register_script( 'highcharts-js', "https://code.highcharts.com/highcharts.js", array('jquery'), null, false);
    wp_enqueue_script( 'highcharts-js' );
  }

  function shortcode_mt4_instrument( $content ) {
   if(empty($this->output))
      $this->output = $this->format_gain_output($content);

    return $this->output;
  }

}