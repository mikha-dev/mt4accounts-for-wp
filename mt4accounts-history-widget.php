<?php
$MT4_History_Plugin = new MT4_History_Plugin();

class MT4_History_Plugin{

  private $output;

  function __construct(){

    // load core lib at template_redirect because we need the post data!
    add_action( "template_redirect", array( &$this, '_header' ) );

    // load js and css files
    add_action( "wp_enqueue_scripts", array( &$this, 'wp_enqueue_scripts' ) );

    add_shortcode( "mt4_history", array( &$this, 'shortcode_format' ) );
  }

  function _header() {
    global $post;

    $regex_pattern = get_shortcode_regex();

    preg_match_all('/'.$regex_pattern.'/s', $post->post_content, $regex_matches);

    if(count($regex_matches) < 4) {
      return;
    }

    $i = 0;
    foreach($regex_matches[2] as $m) {
      if ($m == 'mt4_history') {
        $attribute_str = str_replace (" ", "&", trim ($regex_matches[3][$i]));
        $attribute_str = str_replace ('"', '', $attribute_str);

        $defaults = array ();
        $attributes = wp_parse_args($attribute_str, $defaults);

        $this->output = $this->format_output($attributes);
      }

      $i++;
    }

  }

  function format_output($attributes) {

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
//    $url_ajax = "/wp-content/plugins/mt4accounts-for-wp/mt4accounts-api.php?account=$account_number";
    $items = mt4accounts_get_api()->get_history($account_number);
    $items = json_encode($items);

    include_once( dirname( __FILE__ ) . "/templates/{$template}/history.php");

    $data = ob_get_contents();
    ob_clean();

    return $data;
  }

  function wp_enqueue_scripts()  {

    wp_register_script( 'datatables-js', "https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js", array('jquery'), null, false);
    wp_enqueue_script( 'datatables-js' );

    wp_register_style( 'datatables-css', "https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css", array('jquery'), null, false);
    wp_enqueue_style( 'datatables-css' );
  }

  function shortcode_format( $content ) {
   if(empty($this->output))
      $this->output = $this->format_output($content);

    return $this->output;
  }

}