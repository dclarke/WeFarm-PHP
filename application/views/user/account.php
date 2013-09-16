<h1 class="floatLeft"><? echo $name; ?>'s Farm</h1>


<? if (!$token) {
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
<p><p>
<? if ($edit) {
    echo "<a id=\"edit\" href=" . URL::base() . 'user/edit>Edit</a><p>';
    echo "<a id=\"delete\" href=" . URL::base() . 'user/delete>Delete</a>';
}
?>
