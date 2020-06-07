<?php
$mt4_equity_plugin = new MT4_Equity_Plugin();

class MT4_Equity_Plugin {

  private $output;

  function __construct(){

    add_action( "template_redirect", array( &$this, 'mt4_equity_header' ) );

    add_action( "wp_enqueue_scripts", array( &$this, 'mt4_equity_enqueue_scripts' ) );

    add_shortcode( "mt4_equity", array( &$this, 'shortcode_mt4_equity' ) );
  }

  function mt4_equity_header() {
    global $post;

    $regex_pattern = get_shortcode_regex();

    preg_match_all('/'.$regex_pattern.'/s', $post->post_content, $regex_matches);

    if(count($regex_matches) < 4 ) {
      return;
    }

    $i = 0;
    foreach($regex_matches[2] as $m) {
      if ($m == 'mt4_equity') {
        $attribureStr = str_replace (" ", "&", trim ($regex_matches[3][$i]));
        $attribureStr = str_replace ('"', '', $attribureStr);

        $this->output = $this->format_equity_output($attribureStr);
      }

      $i++;
    }

  }

  function format_equity_output($attribures) {
    global $mt4accounts;

    $account_number = '';

    if (isset($attributes['account_number'])){
      $account_number = $attributes['account_number'];
    } else {
      $account_number = $_REQUEST['acc'];
    }

    if(empty($account_number)) {
      return false;
    }

   if(isset($params['template']))
      $template = $params['template']?$params['template']:'default';
    else
      $template = 'default';

    if (isset($attributes['caption'])){
      $caption = $attributes['caption'];
    } else
      $caption = '';

    ob_start();

    $items = $mt4accounts->get_equity();

    include_once( dirname( __FILE__ ) . "/templates/{$template}/equity.php");

    $data = ob_get_contents();
    ob_clean();

    return $data;

  }

  function mt4_equity_enqueue_scripts()  {

    wp_register_script( 'highcharts-js', "https://code.highcharts.com/highcharts.js", array('jquery'), null, false);
    wp_enqueue_script( 'highcharts-js' );
  }

  function shortcode_mt4_equity( $content ) {
   if(empty($this->output))
      $this->output = $this->format_equity_output($content);

    return $this->output;
  }

}