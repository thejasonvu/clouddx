<?php    
    $inputType = $_SESSION['q'] >= 7 && $_SESSION['q'] <= 10 ? 'number' : 'text';
?>
<br>
<input type="<?=$inputType?>" class="form-control form-control-lg" id='questionInput' name="answer" required>
<br>