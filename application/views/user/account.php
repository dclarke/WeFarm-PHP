<h1 class="floatLeft"><? echo $name; ?>'s Farm</h1>

<? if (!$token) {
        $config = Kohana::$config->load('wepay');
?>

<a id="start_oauth2">Click here to create your WePay account</a>

<script src="<?echo $config->get('web_server'); ?>min/js/wepay.v2.js" type="text/javascript"></script>
<script type="text/javascript">

WePay.set_endpoint("stage");
WePay.endpoint = "<?echo $config->get('api_server'); ?>";
WePay.domain   = "<?echo $config->get('web_server'); ?>";
WePay.domainStatic = "<?echo $config->get('web_server'); ?>";

WePay.OAuth2.button_init(document.getElementById('start_oauth2'), {
        "client_id":"<?echo $config->get('client_id'); ?>",
        "scope":["manage_accounts","view_balance","collect_payments","view_user","send_money","preapprove_payments"],
        "redirect_uri":"<? echo $base; ?>" + "wepayapi",
        "user_country": "<? echo $country; ?>",
        "callback":function(data) {
            window.location="<?echo $base; ?>" + "wepayapi?code=" + data.code;
        } });
</script>
<p>
<?  
}
?>

<p> <? echo $wepay; ?></p>
<p>
<b>Name:</b>
<? echo $name; ?>
</p>
<p>
<b>Email:</b>
<? echo $email; ?>
</p>

<p>
<b>Farm:</b>
<? echo $farm; ?>
</p>

<p>
<b>Produce:</b>
<? echo $produce; ?>
</p>

<p>
<b>Produce price:</b>
<? echo '$'.$price; ?>
</p>

<b>Country:</b>
<? echo $country; ?>
</p>
<p><p>
<? if ($edit) {
    echo "<a id=\"edit\" href=" . URL::base() . 'user/edit>Edit</a><p>';
    echo "<a id=\"delete\" href=" . URL::base() . 'user/delete>Delete</a>';
}
?>
