<?php

/**
 * Add async fonts
 */
function fjtg_fonts() {
/*/
?>
<script>
  (function(d) {
    var config = {
      kitId: 'vvl4ywy',
      scriptTimeout: 3000,
      async: true
    },
    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
  })(document);
</script>
<?php
/*/
}
add_action('wp_footer', 'fjtg_fonts', 110);


/**
 * Google Tag Manager
 */
if ( ! function_exists( 'fjtg_google_tag_manager' ) ) {
	function fjtg_google_tag_manager() {
/*/
		$site_url = get_site_url();
		if( rojak_str_contains( $site_url, '//www.holidayvilla.com' ) ) {
			echo <<<HTML
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-');</script>
<!-- End Google Tag Manager -->
HTML;
		}
/*/
	}
}
add_action('wp_footer', 'fjtg_google_tag_manager', 120);


/**
 * Add respond.js for IE
 */
if ( ! function_exists( 'fjtg_ie_support' ) ) {
	function fjtg_ie_support() {
		echo <<<HTML
<!--[if lte IE 8]>
	<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js" defer='defer'></script>
<![endif]-->
HTML;
	}
}
add_action('wp_footer', 'fjtg_ie_support', 130);