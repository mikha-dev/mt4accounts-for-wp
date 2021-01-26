<?php
$MT4_PortfolioInstrument_Plugin = new MT4_PortfolioInstrument_Plugin();

class MT4_PortfolioInstrument_Plugin {

  private $output;

  function __construct(){

    // load core lib at template_redirect because we need the post data!
    add_action( "template_redirect", array( &$this, '_header' ) );

    // load js and css files
    add_action( "wp_enqueue_scripts", array( &$this, '_enqueue_scripts' ) );

    add_shortcode( "mt4_portfolio_instruments", array( &$this, '_shortcode' ) );
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
      if ($m == 'mt4_portfolio_instrument') {
        $attribute_str = str_replace (" ", "&", trim ($regex_matches[3][$i]));
        $attribute_str = str_replace ('"', '', $attribute_str);

        $defaults = array ();
        $attributes = wp_parse_args($attribute_str, $defaults);

        $this->output = $this->_output($attributes);
      }

      $i++;
    }

  }

  function _output($attributes) {
    $id = '';
    if (isset($attributes['id'])){
      $id = $attributes['id'];
    } else {
      if(isset($_REQUEST['id']))
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
    $items = mt4accounts_get_api()->get_portfolio_instruments($id);
    include_once( dirname( __FILE__ ) . "/templates/portfolio/instrument.php");

    $data = ob_get_contents();
    ob_clean();

    return $data;
  }

  function _enqueue_scripts()  {

    wp_register_script( 'highcharts-js', "https://code.highcharts.com/highcharts.js", array('jquery'), null, false);
    wp_enqueue_script( 'highcharts-js' );
  }

  function _shortcode( $content ) {
   if(empty($this->output))
      $this->output = $this->_output($content);

    return $this->output;
  }

}