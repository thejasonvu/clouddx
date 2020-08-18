<?php

class Quiz extends Model {

    
        
        function getField($questionNumber)
        {
            $fields = array("email", "operation_type", "state", "region", "current_chronic_care", "frontline_chronic_care_staff", "staff_wage", "number_of_chronic_care_staff", "number_of_medicare_patients", "number_of_chronic_care_patients", "time_per_day_spent_on_patient", "emr_software");
            
            return $fields[$questionNumber];            
        }
    
        function createSubmission($email)
        {
            try 
            {
                $sql = ("INSERT INTO survey_submissions (email, submission_date) VALUES (?, ?)");

                $stmt = Database::getDao()->prepare($sql);

                $stmt->execute([$email, $this->getDateTime()]);
                
                return Database::getDao()->lastInsertId();
            }
            catch(PDOException $e)
            {
                return false;
            }	
        }
        
        function updateSubmission($field, $value, $id)
        {
            try 
            {
                $sql = ("UPDATE survey_submissions SET $field=? WHERE submission_id=?");
                $stmt = Database::getDao()->prepare($sql);

                $stmt->execute([$value, $id]);
                
                return true;
            }
            catch(PDOException $e)
            {
                return false;
            }	
        }
    
        function markComplete($id)
        {
            try 
            {
                $sql = ("UPDATE survey_submissions SET survey_complete=1 WHERE submission_id=?");
                $stmt = Database::getDao()->prepare($sql);

                $stmt->execute([$id]);
                
                return true;
            }
            catch(PDOException $e)
            {
                return false;
            }
        }
        
        function getSubmission($id)
        {
            try 
            {
                $sql = ("SELECT * FROM survey_submissions WHERE submission_id=?");
                $stmt = Database::getDao()->prepare($sql);

                $stmt->execute([$id]);

                return $stmt->fetch();
            }
            catch(PDOException $e)
            {
                return false;
            }	
        }
        
        function getQuestion($id)
        {
            try 
            {
                $sql = ("SELECT * FROM survey_questions WHERE question_id=?");
                $stmt = Database::getDao()->prepare($sql);

                $stmt->execute([$id]);

                return $stmt->fetch();
            }
            catch(PDOException $e)
            {
                return false;
            }	
        }
        
        // Question 2
        function getStates()
        {
            try
            {
                $sql = ("SELECT DISTINCT(state) FROM state_codes");
                $stmt = Database::getDao()->prepare($sql);

                $stmt->execute();

                $states = array();
                while ($nextRow = $stmt->fetch())
                {
                    $states[] = $nextRow['state'];
                }
                return $states;
            }
            catch(PDOException $e)
            {
                return false;
            }
        }

        // Question 6
        function getStaffWageByState($state, $staffType)
        {
            try 
            {
                $sql = ("SELECT * FROM staff_wages WHERE area_title=? AND occ_title=?");
                $stmt = Database::getDao()->prepare($sql);

                $stmt->execute([$state, $staffType]);

                $data = $stmt->fetch();
                return array($data['h_mean']);
            }
            catch(PDOException $e)
            {
                return false;
            }	
        }

        // Question 5
        function getStaffTitle($state)
        {
            try
            {
                $sql = ("SELECT * FROM staff_wages WHERE area_title=?");
                $stmt = Database::getDao()->prepare($sql);

                $stmt->execute([$state]);

                $titles = array();
                while ($nextRow = $stmt->fetch())
                {
                    $titles[] = $nextRow['occ_title'];
                }
                return $titles;
            }
            catch(PDOException $e)
            {
                return false;
            }
        }
        
        // Question 3
        function getRegionsByState($state)
        {
            try 
            {
                $sql = ("SELECT * FROM state_codes WHERE state=?");
                $stmt = Database::getDao()->prepare($sql);

                $stmt->execute([$state]);

                $regions = array();
                while ($nextRow = $stmt->fetch()) 
                {                
                    $regions[] = $nextRow['region_name'];
                }

                return $regions;
            }
            catch(PDOException $e)
            {
                return false;
            }
        }

        // Question 1 && Question 4        
        function getQuestionOptions($questionId)
        {
            try 
            {
                $sql = ("SELECT * FROM survey_question_options WHERE question_id=?");
                $stmt = Database::getDao()->prepare($sql);

                $stmt->execute([$questionId]);

                $questionOptions = array();
                while ($nextRow = $stmt->fetch()) 
                {                
                    $questionOptions[] = $nextRow['text'];
                }

                return $questionOptions;
            }
            catch(PDOException $e)
            {
                return false;
            }
        }
    
        function estimatePatientsWithChronicConditions($numberOfPatients)
        {
            return array(ceil($numberOfPatients * 0.4898));
        }
    
        function validateInput($answer, $question, &$errorMessage)
        {
            switch($question)
            {
                case 0:
                    if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) 
                    {
                        $errorMessage = "Invalid email format."; 
                    }
                    break;
                case 6:
                    if (!is_numeric($answer)) 
                    {
                        $errorMessage = "Invalid wage provided."; 
                    }
                    break;
                case 7:
                case 8:
                case 9:
                case 10:
                    if (!is_numeric($answer)) 
                    {
                        $errorMessage = "Invalid number provided."; 
                    }
                    break;                    
            }
            
            return $errorMessage == '';
        }
    
        function getROI($eligiblePatients, $avgWage) {
            // Default values, can be manually changed by the user?
            $monthlyHardwareReimbursement = 69.0;
            $monthlyRpmReimbursement = 54.0;
            $monthlyChronicCareReimbursement = 42.0;
            $initialPatientSetupReimbursement = 21.0;
            //$percentPatientsEligible = 0.293; // As per CMS.gov
            $percentPatientsRpm = 0.15;
            $percentPatientsRpmCcm = 0.05;

            //if ($eligiblePatients == 0) $eligiblePatients = $patients * $percentPatientsEligible;

            $rpmPatients = $eligiblePatients * $percentPatientsRpm;
            $ccmPatients = $eligiblePatients * $percentPatientsRpmCcm;
            $staffHoursPerMonth = ($rpmPatients + ($ccmPatients * 2)) / 3.0;

            $annualRpmReimbursement = ($monthlyHardwareReimbursement + $monthlyRpmReimbursement - 58.5) * 12; // Costs are already subtracted here, so this is the profit rather than net
            $annualCcmReimbursement = ($monthlyHardwareReimbursement + $monthlyRpmReimbursement + $monthlyChronicCareReimbursement - 6.5) * 12; // Costs are already subtracted here, so this is the profit rather than net

            $practiceGrossAnnualProfit = ($annualRpmReimbursement * $rpmPatients) + (($annualCcmReimbursement + $initialPatientSetupReimbursement) * $ccmPatients);

            //$avgWage = getAverageWage($employeeType, $state);

            $netProfit = $practiceGrossAnnualProfit - ($staffHoursPerMonth * $avgWage * 12);

                    $report = array(
                        "annualRpmReimbursement" => $annualRpmReimbursement,
                        "annualCcmReimbursement" => $annualCcmReimbursement,
                        "practiceGrossAnnualProfit" => $practiceGrossAnnualProfit,
                        "netProfit" => $netProfit
                    );

            return $report;
        }
    
        function calculateROI($answers)
        {
            $report = array();
            
            // Get costs based on answers given in survey
            $report['costs'] = $this->getAllCosts($answers);
            
            // Topline RPM value per patient per year
            $toplineRPM = $report['costs']['cpt99453']['amount'] + (12 * $report['costs']['cpt99454']['amount']) + (12 * $report['costs']['cpt99457']['amount']);
            
            // Calculate total annual RPM revenue based on number of patients in clinic
            // rpm * number of eligible patients
            $report['annualRPMRevenue'] = round($toplineRPM * $answers['9'], 2);
            
            // Calculate total annual CCM revenue
            $report['annualCCMRevenue'] = round(($answers['9'] * 0.2) * ($report['costs']['cpt99490']['amount'] * 12), 2);
            
            
            // Calculate additional staff costs
            $report['additionalStaffCosts'] = round($this->getStaffCosts($answers), 2);
            
            // CDX costs 52.45 PPPM
            $report['costsBilled'] = round($answers['9'] * 12 * 52.45, 2);
            
            $report['netAnnualProfit'] = round($report['annualRPMRevenue'] + $report['annualCCMRevenue'] - $report['additionalStaffCosts'] - $report['costsBilled'], 2);
            
            return $report;
        }
    
        function getStaffCosts($answers)
        {
            // yearly (2080 hours) + benefits (yearly * 11%)
            $staffSalary = ($answers['6'] * 2080) + ($answers['6'] * 2080 * 0.11);
            
            $currentStaffLevel = (($answers['7'] * $answers['10']) / 20) * 19.58;
            
            $timeSpent = 19.58 * 6.5 * 3;
            
            $effeciency = $timeSpent * 0.75;
            
            $staffRequired = ($answers['9'] - $currentStaffLevel) / $effeciency;
            
            return $staffRequired * $staffSalary;            
        }
    
        function getAllCosts($answers)
        {
            $costs = array();
            
            $costs['cpt99453'] = array();
            $costs['cpt99453']['amount'] = $this->lookupCost('99453', $answers['3'], $answers['2']);
            $costs['cpt99453']['description'] = "RPM Initial Patient Setup Average Reimbursement";
            
            $costs['cpt99454'] = array();
            $costs['cpt99454']['amount'] = $this->lookupCost('99454', $answers['3'], $answers['2']);
            $costs['cpt99454']['description'] = "RPM Monthly Hardware Supply Average Reimbursement";
            
            $costs['cpt99457'] = array();
            $costs['cpt99457']['amount'] = $this->lookupCost('99457', $answers['3'], $answers['2']);
            $costs['cpt99457']['description'] = "RPM Monthly Management Average Reimbursement (20 Mins)";
            
            $costs['cpt99490'] = array();
            $costs['cpt99490']['amount'] = $this->lookupCost('99490', $answers['3'], $answers['2']);
            $costs['cpt99490']['description'] = "CCM Monthly Chronic Care Management Services Avg Reimbursement (30 Mins)";
            
            $costs['cpt99491'] = array();
            $costs['cpt99491']['amount'] = $this->lookupCost('99491', $answers['3'], $answers['2']);
            $costs['cpt99491']['description'] = "CCM Monthly Chronic Care Management Services Average Reimbursement";
            
            $costs['cpt99487'] = array();
            $costs['cpt99487']['amount'] = $this->lookupCost('99487', $answers['3'], $answers['2']);
            $costs['cpt99487']['description'] = "Complex Chronic Care Management Services (60 Mins)";
            
            $costs['cpt99489'] = array();
            $costs['cpt99489']['amount'] = $this->lookupCost('99489', $answers['3'], $answers['2']);
            $costs['cpt99489']['description'] = "Complex CCM add-on for use with CPT 99487 (Each additional 30 Mins)";
            
            $costs['G0506'] = array();
            $costs['G0506']['amount'] = $this->lookupCost('G0506', $answers['3'], $answers['2']);
            $costs['G0506']['description'] = "Add-On to CCM Initiating Visit";
            
            return $costs;
        }
    
        function lookupCost($tableName, $region, $facilityType)
        {
            try 
            {
                $sql = ("SELECT * FROM `$tableName` WHERE `MAC LOCALITY`=?");
                $stmt = Database::getDao()->prepare($sql);

                $stmt->execute([$region]);
                
                $data = $stmt->fetch();
                
                $cost = $facilityType == 'Facility' ? $data['FACILITY PRICE'] : $data['NON-FACILITY PRICE'];
                
                return $cost;
            }
            catch(PDOException $e)
            {
                return false;
            }	
        }
        
        function getDateTime()
        {
            date_default_timezone_set("America/Toronto");
            $date = date("m/d/Y h:i A");
            $final = strtotime($date);
            
            return date("Y-m-d H:i:s", $final);
        }
}
?>