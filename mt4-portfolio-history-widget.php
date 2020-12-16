<?php
$MT4_PortfolioHistory_Plugin = new MT4_PortfolioHistory_Plugin();

class MT4_PortfolioHistory_Plugin{

  private $output;

  function __construct(){

    // load core lib at template_redirect because we need the post data!
    add_action( "template_redirect", array( &$this, '_header' ) );

    // load js and css files
    add_action( "wp_enqueue_scripts", array( &$this, 'wp_enqueue_scripts' ) );

    add_shortcode( "mt4_portfolio_history", array( &$this, 'shortcode_format' ) );
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
      if ($m == 'mt4_portfolio_history') {
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
    $items = mt4accounts_get_api()->get_portfolio_history($id);

    $data = array();
    foreach($items as $item) {
      $t = array();

      $t[] = '"'.$item->time_open.'"';
      $t[] = '"'.$item->time_close.'"';
      $t[] = '"'.$item->symbol.'"';
      $t[] = '"'.$item->type_str.'"';
      $t[] = '"'.$item->lots.'"';
      $t[] = '"'.$item->stoploss.'"';
      $t[] = '"'.$item->takeprofit.'"';
      $t[] = '"'.$item->price.'"';
      $t[] = '"'.$item->price_close.'"';
      $t[] = '"'.$item->pips.'"';
      $t[] = '"'.$item->pl.'"';

      $data[] = '['. implode(',', $t).']';
    }

    $items = '['. implode(',', $data). '];';
    include_once( dirname( __FILE__ ) . "/templates/{$template}/history.php");

    $data = ob_get_contents();
    ob_clean();

    return $data;
  }

  function wp_enqueue_scripts()  {


    wp_register_script( 'datatables-js', "https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js", array('jquery'), null, false);
    wp_enqueue_script( 'datatables-js' );

    wp_register_script( 'datatables-reor', "https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js", array('jquery'), null, false);
    wp_enqueue_script( 'datatables-reor' );

    wp_register_style( 'bs', "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" );
    wp_enqueue_style( 'bs' );

    wp_register_style( 'datatables-css', "https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" );
    wp_enqueue_style( 'datatables-css' );

    wp_register_style( 'datatables-bs', "https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css" );
    wp_enqueue_style( 'datatables-bs' );



    wp_register_style( 'datatables-resp', "https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js" );
    wp_enqueue_style( 'datatables-rest' );

  }

  function shortcode_format( $content ) {
   if(empty($this->output))
      $this->output = $this->format_output($content);

    return $this->output;
  }

}