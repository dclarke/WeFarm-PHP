<h1>Buy <? echo $produce ?> from <? echo $farm ?> for <? echo $price ?></h1>
<br />
<p><a href="<? echo $return_uri ?>">← Back to <? echo $farm ?></a></p>
<br />

<div id="wepay-iframe-div">
       <script type="text/javascript" src="https://stage.wepay.com/min/js/iframe.wepay.js">
       </script>

       <script type="text/javascript">
           WePay.iframe_checkout("wepay-iframe-div", "<? echo $checkout_uri ?>");
       </script>
</div>
