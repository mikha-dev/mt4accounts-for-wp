<?php
$MT4_PortfolioAdvstat_Plugin = new MT4_PortfolioAdvstat_Plugin();

class MT4_PortfolioAdvstat_Plugin{

  private $output;

  function __construct(){

    add_action( "template_redirect", array( &$this, 'template_redirect' ) );

//    add_action( "wp_enqueue_scripts", array( &$this, 'mt4_equity_enqueue_scripts' ) );

    add_shortcode( "mt4_portfolio_advstat", array( &$this, 'format_shortcode' ) );
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
      if ($m == 'mt4_equity') {
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

    if (isset($attributes['caption'])){
      $caption = $attributes['caption'];
    } else
      $caption = '';

    ob_start();

    $account = mt4accounts_get_api()->get_portfolio_advstat($id);
    include_once( dirname( __FILE__ ) . "/templates/portfolio/adv_stat.php");

    $data = ob_get_contents();
    ob_clean();

    return $data;
  }

  function format_shortcode( $content ) {
    if(empty($this->output))
      $this->output = $this->format_output($content);

    return $this->output;
  }

}