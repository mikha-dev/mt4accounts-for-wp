<?php
$mt4_portfolio_equity_plugin = new MT4_PortfolioEquity_Plugin();

class MT4_PortfolioEquity_Plugin {

  private $output;

  function __construct(){

    add_action( "template_redirect", array( &$this, 'template_redirect' ) );

    add_action( "wp_enqueue_scripts", array( &$this, 'enqueue_scripts' ) );

    add_shortcode( "mt4_portfolio_equity", array( &$this, 'format_shortcode' ) );
  }

  function template_redirect() {
    global $post;

    $regex_pattern = get_shortcode_regex();

    preg_match_all('/'.$regex_pattern.'/s', $post->post_content, $regex_matches);

    if(count($regex_matches) < 4 ) {
      return;
    }

    $i = 0;
    foreach($regex_matches[2] as $m) {
      if ($m == 'mt4_portfolio_equity') {
        $attribureStr = str_replace (" ", "&", trim ($regex_matches[3][$i]));
        $attribureStr = str_replace ('"', '', $attribureStr);

        $this->output = $this->format_output($attribureStr);
      }

      $i++;
    }

  }

  function format_output($attributes) {

    $id = '';

    if (isset($attributes['id'])){
      $id = $attributes['id'];
    } else {
      if( isset($_REQUEST['id']))
        $id = $_REQUEST['id'];
    }

    if(empty($id)) {
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

    $items = mt4accounts_get_api()->get_portfolio_equity($id);

    include_once( dirname( __FILE__ ) . "/templates/{$template}/equity.php");

    $data = ob_get_contents();
    ob_clean();

    return $data;

  }


  function format_shortcode( $content ) {
   if(empty($this->output))
      $this->output = $this->format_output($content);

    return $this->output;
  }

  function enqueue_scripts()  {

    wp_register_script( 'highcharts-js', "https://code.highcharts.com/stock/highstock.js", array('jquery'), null, false);
    wp_enqueue_script( 'highcharts-js' );
  }

}