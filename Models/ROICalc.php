<?php
class ROICalc extends model {
	function getAverageWage($employeeType, $state) 
	{ // $employeeType is 0 or 1; $state is its index alphabetically; 16 for the national average. Invalid input (-1) to get the default value
		if ($employeeType == 0) { // Registered Nurse
			$wages = array(19.03, 27.11, 28.83, 19.17, 29.70, 23.27, 29.64, 27.56, 22.90, 21.44,
				 26.96, 22.47, 25.96, 22.10, 21.62, 22.42, 21.84, 21.13, 24.08, 25.37, 29.11, 24.07,
				  23.82, 18.69, 21.97, 22.89, 23.40, 22.98, 27.23, 26.62, 28.00, 25.97, 25.25, 21.61,
				   23.53, 21.06, 18.91, 26.21, 24.42, 21.36, 19.71, 19.29, 24.25, 23.48, 27.34, 22.57,
				    27.95, 18.39, 22.73, 23.25, 16.47, 11.81);
			return $wages[$state];
		} else if ($employeeType == 1) { // LPN/LVN Licenses Practical Nurse
			$wages = array(29.51, 41.51, 37.59, 29.00, 54.63, 38.03, 43.15, 33.58, 45.87, 34.03, 35.63,
			 57.37, 33.60, 37.46, 33.82, 28.89, 29.50, 31.56, 35.14, 32.59, 40.32, 45.16, 34.10, 43.10,
			  29.00, 34.42, 33.38, 35.81, 31.71, 43.67, 38.00, 39.33, 37.24, 43.85, 30.57, 35.01, 32.81,
			   31.41, 44.65, 35.69, 37.32, 30.38, 27.80, 34.26, 39.12, 33.14, 30.31, 36.62, 41.95, 28.99,
			    36.46, 31.07, 27.96, 16.90);
			return $wages[$state];
		}
		return 23.4;
	}

	//function getROI($employeeType, $state, $patients, $eligiblePatients) { 
	// EmployeeType is 0 or 1; state is its index (16 for national avg), eligiblePatients = 0 if using estimate instead
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
}
?>