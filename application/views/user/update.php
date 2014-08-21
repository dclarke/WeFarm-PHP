<? 
  $config = Kohana::$config->load('wepay');
?>

<div id="wepay-iframe-div">
       <script type="text/javascript" src="<?echo $config->get('web_server') ?>/min/js/iframe.wepay.js">
       </script>
	<iframe id="wefarm_account_update" height="300px" width="600px" src="<? echo $update_uri ?>"/>
</div>
~
~

