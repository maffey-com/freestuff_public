<h1>Push accounts to mailchimp</h1>

<ul>
    <li><label>Records Pushed</label><?= $total_count ?></li>
    <li><label>Successfull</label><?= $success_count ?></li>
</ul>


<? if (sizeof($errors)) { ?>
    <h3>Errors</h3>
    <? foreach ($errors as $key => $value) { ?>
        <li><label style="color:red"><?= $key ?></label><?= $value ?> </li>
    <? }
} ?>
<br/><br/>
