<br>
<select name="answer" class="form-control form-control-lg" required>
<?php 
foreach($_SESSION['questionAnswers'] as $option)
{ ?>
    <option class="form-control form-control-lg" value="<?=$option?>"><?=$option?></option>
<?php
}
?>
</select>
<br>