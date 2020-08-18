<?php
    $revenueTotal = number_format($_SESSION['roi']['annualRPMRevenue'] + $_SESSION['roi']['annualCCMRevenue'], 2);
    $expenseTotal = number_format($_SESSION['roi']['additionalStaffCosts'] + $_SESSION['roi']['costsBilled'], 2);
?>

<div class="row justify-content-center">
	<div class="col-lg-6">
		<div class="card">
			<div class="card-body">
                <h1>You Qualify!</h1>
                <p>Based on the information you provided, we estimate that you could earn <span style="font-size: 1.2em; font-weight: 600; color: green;">$<?=number_format($_SESSION['roi']['netAnnualProfit'], 2)?></span> annually.</p>
				                
                <h2>Breakdown</h2>
                <h4>Revenue</h4>
                <p><strong>Total Revenue: </strong>$<?=$revenueTotal?></p>
                <canvas id="revenue"></canvas>
                
                <h4>Expenses</h4>
                <p><strong>Total Expenses: </strong>$<?=$expenseTotal?></p>
                <canvas id="expenses"></canvas>
                
                <h4>Applicable CPT Codes</h4>
                <small>Values represent amount <strong>per eligible patient</strong>.</small>
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($_SESSION['roi']['costs'] as $key => $value) { ?>
                            <tr>
                                <td><strong><?=$key?></strong></td>
                                <td><small><?=$value['description']?></small></td>
                                <td style="color: green;">$<?=$value['amount']?></td>
                            </tr>                            
                        <?php } ?>
                    </tbody>
                </table>
                <br>
                <a class="btn btn-primary btn-lg" href="https://www.clouddx.com/">Learn More</a>
                <a class="btn btn-secondary btn-lg" href="<?=base_url?>web/downloads/RPM%20Calculator.xlsm">Download Spreadsheet</a>
                <br>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    var revenueChart = document.getElementById('revenue').getContext('2d');
    var chart = new Chart(revenueChart, {
        type: 'pie',
        data: {
            labels: ['Annual CCM Reimbursement', 'Annual RPM Reimbursement'],
            datasets: [{
                label: 'Revenue Breakdown',
                backgroundColor: ['rgb(255, 99, 132)', 'rgb(132, 99, 255)'],
                data: [<?=$_SESSION['roi']['annualCCMRevenue']?>, <?=$_SESSION['roi']['annualRPMRevenue']?>]
            }]
        },
        options: {}
    });
    
    var expenseChart = document.getElementById('expenses').getContext('2d');
    var chart = new Chart(expenseChart, {
        type: 'pie',
        data: {
            labels: ['Additional Staff Costs', 'Costs Billed'],
            datasets: [{
                label: 'Revenue Breakdown',
                backgroundColor: ['rgb(255, 99, 132)', 'rgb(132, 99, 255)'],
                data: [<?=$_SESSION['roi']['additionalStaffCosts']?>, <?=$_SESSION['roi']['costsBilled']?>]
            }]
        },
        options: {}
    });

</script>