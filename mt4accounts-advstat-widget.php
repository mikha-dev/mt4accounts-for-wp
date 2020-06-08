<?php
$MT4_Advstat_Plugin = new MT4_Advstat_Plugin();

class MT4_Advstat_Plugin{

  private $tag = 'mt4_advstat';
  private $output;

  function __construct(){

    add_action( "template_redirect", array( &$this, 'template_redirect' ) );
    //add_action( "wp_enqueue_scripts", array( &$this, 'wp_enqueue_scripts' ) );
    add_shortcode( $this->tag, array( &$this, 'format_shortcode' ) );
  }

  /**
   * This is the custom action, placed in header at your theme before any html-output!
   * To be continued: hooks and filters to perform different grids on different tables and datasources.
   */
  function template_redirect() {
    global $post;

    $regex_pattern = get_shortcode_regex();

    preg_match_all('/'.$regex_pattern.'/s', $post->post_content, $regex_matches);

    if(count($regex_matches) < 4) {
      return;
    }

    $i = 0;
    foreach($regex_matches[2] as $m) {
      if ($m == $this->tag) {
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

    ob_start();

    $account = mt4accounts_get_api()->get_advstat($account_number);
    include_once( dirname( __FILE__ ) . "/templates/{$template}/adv_stat.php");

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