<?php
    $progress = ceil((($_SESSION['q'] + 1) / $_SESSION['quizLength']) * 100);
?>
<div class="row justify-content-center">
	<div class="col-lg-5">
		<div class="card">
			<div class="card-body">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?=$progress?>%" aria-valuenow="<?=$progress?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <br>
                <?php if($_SESSION['errorMessage'] != '') { ?>
                <div class="alert alert-warning" role="alert">
                    <?=$_SESSION['errorMessage']?>
                </div>
                <?php } ?>              
                
				<form action="<?=base_url?>index.php/quiz/next/" method="POST" id="answer">
					<div class="form-group" id="question">
						<label><?= $_SESSION['question']['question_text'] ?></label>
						<?php 
						switch($_SESSION['question']['question_type']) {
							case "list":
								include(__DIR__.'/_list.php');
								break;
							case "option":
								include(__DIR__.'/_option.php');
								break;
							case "input":
								include(__DIR__.'/_input.php');
								break;
						}
						?>
						<button class='btn btn-primary btn-lg' type='submit'>Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>