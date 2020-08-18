<?php    
    $inputType = $_SESSION['q'] == 9 ? 'number' : 'text';
?>
<br>
<input type="<?=$inputType?>" class="form-control form-control-lg" value="<?=$_SESSION['questionAnswers'][0]?>" name="answer" required />
<br>