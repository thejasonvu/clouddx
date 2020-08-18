<?php
class quizController extends Controller
{
    function index()
    {
        if(isset($_SESSION['id']))
        {
            unset($_SESSION['id']);
        }
        require_once(ROOT.'Models/Quiz.php');
        
        $quiz = new Quiz();
        
        $_SESSION['errorMessage'] = '';
        $_SESSION['q'] = 0;
        $_SESSION['question'] = $quiz->getQuestion($_SESSION['q']);
        $_SESSION['answers'] = array();
        $_SESSION['quizLength'] = 12;
        $this->render("index");
    }

    function next()
    {
        require_once(ROOT.'Models/Quiz.php');

        $quiz = new Quiz();
        $_SESSION['errorMessage'] = '';
        if(!$quiz->validateInput($_REQUEST['answer'], $_SESSION['q'], $_SESSION['errorMessage']))
        {
            $this->render("index");
        }
        else
        {
            $this->submit($_REQUEST['answer']);
        
            // create submission
            if(!isset($_SESSION['id']))
            {
                $_SESSION['id'] = $quiz->createSubmission($_REQUEST['answer']);
            }
            else // update submission
            {
                $quiz->updateSubmission($quiz->getField($_SESSION['q']), $_REQUEST['answer'], $_SESSION['id']);
            }

            $_SESSION['q']++;

            if($_SESSION['q'] < $_SESSION['quizLength'])
            {
                switch ($_SESSION['q']){
                    case 1:
                    case 4:
                        $_SESSION['questionAnswers'] = $quiz->getQuestionOptions($_SESSION['q']);
                        break;
                    case 2:
                        $_SESSION['questionAnswers'] = $quiz->getStates();
                        break;
                    case 3:
                        $_SESSION['questionAnswers'] = $quiz->getRegionsByState($_SESSION['answers']['2']);
                        break;
                    case 5:
                        $_SESSION['questionAnswers'] = $quiz->getStaffTitle($_SESSION['answers']['2']);
                        break;
                    case 6:
                        $_SESSION['questionAnswers'] = $quiz->getStaffWageByState($_SESSION['answers']['2'],$_SESSION['answers']['5']);
                        break;
                    case 9:
                        $_SESSION['questionAnswers'] = $quiz->estimatePatientsWithChronicConditions($_REQUEST['answer']);
                        break;
                }

                $_SESSION['question'] = $quiz->getQuestion($_SESSION['q']);
                $this->render("index");
            } else { 
                $quiz->markComplete($_SESSION['id']);
                //$_SESSION['roi'] = $quiz->getROI($_SESSION['answers']['9'], $_SESSION['answers']['6']);
                $_SESSION['roi'] = $quiz->calculateROI($_SESSION['answers']);
                $this->render("roi");
            }
        }
        
    }

    private function submit($answer)
    {
        $answer = $this->secure_input($answer);
        $_SESSION['answers'][] = $answer;
    }
}
?>