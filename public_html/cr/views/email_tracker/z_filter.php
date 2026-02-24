<form method="post">
    To Address <input type="text" name="to_address" value="<?=$to_address?>" placeholder="To Address"><br/>
    Templates <select name="template">
        <? foreach ($templates as $template_row) {?>
            <?=option($template_row,$template_row,$template)?>
        <?}?>
    </select><br/>
    <input type="submit" value="Search"><br/>
</form>