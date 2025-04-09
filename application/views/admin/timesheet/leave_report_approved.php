<?php 
	$first_date  = date('Y-m-01', strtotime($first_date));
	$second_date = date('Y-m-d', strtotime($second_date));
	$total_days = date('t', strtotime($first_date));
	$row_count = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Leave Report (Approved)</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

	<style>
		/* .table-wrapper {
			overflow-x: auto;
		} */
		table tr th, table tr td {
			font-size: 13px;
		}
		.header-row {
			background-color: #f8f9fa;
		}
		th, td {
			font-size: 0.9rem;
		}
		.company-header {
			border-bottom: 2px solid #dee2e6;
		}
		.legend {
			font-size: 0.8rem;
		}
		@media print {

			@page {
				size: A4;
				/* margin-top: 5px; */
				margin-bottom: 0px;
			}

			.page-break {
				page-break-after: always;
				/* margin-bottom: 10px; */
			}
		}
		table tr:last-child td:last-child {
			border: none !important;
		}
		</style>
</head>
<body class="container-fluid py-4">
	<div id="">
		<div class="">
			<table class="table table-bordered table-sm border-dark">
				<!-- <thead class="header-row"> -->
					<tr class="text-center">
						<th>SL.</th>
						<th>NAME</th>
						<th>DASIGNATION</th>
						<th>LEAVE DAYS</th>
						<th>TOTAL</th>
					</tr>
				<!-- </thead> -->
				<tbody>
				<?php

				$j = 1;
				$row_count = 0;
				$total_rows = count($xin_employees);
				foreach ($xin_employees as $r) { 
					// dd($r);
					$emp_data   = $this->db->select('xin_employees.first_name, xin_employees.last_name,xin_designations.designation_name')
					->from('xin_employees')
					->join('xin_designations', 'xin_designations.designation_id = xin_employees.designation_id', 'left')
					->where('xin_employees.user_id', $r->employee_id)
					->get()->row();
					if ($row_count > 0 && $row_count % 17 == 0) {
						echo '<tr class="page-break" style="border:none;"></tr>';?> 
						<!-- Add company header on new page -->
						<div class="company-header mb-4 mt-1">
							<div class="row align-items-center">
								<div class="col-4">
									<h3 class="fw-bold" style="margin-top:-35px;position: absolute;">e.Gen <br>Consultants Ltd</h3>
								</div>
								<div class="col-4">
									<h4 class="fw-bold text-center">Leave Report <br><p class="text-center h5">(Approved)</p></h4>
								</div>
								<div class="col-4 text-end">
									<img src="logo.png" alt="e.Gen Logo" height="60" style="margin: 5px;">
								</div>
							</div>
						</div>

						<div class="mb-3">
							<strong>Reporting Date: <?php echo date('Y-m-d',strtotime($first_date)).' to '.date('Y-m-d',strtotime($second_date))?></strong><br>
							<strong>Report Generated Date:</strong> <?php echo date('d M Y').', '.date('h:i:s A')?>
						</div>

						<table class="table table-bordered table-sm border-dark">
							<thead class="header-row">
								<tr class="text-center">
									<th>SL.</th>
									<th>NAME</th>
									<th>DASIGNATION</th>
									<th>LEAVE DAYS</th>
									<th>TOTAL</th>
								</tr>
							</thead>
					<?php }
					$row_count++;
				?>

				<tr class="text-center">
					<td style="vertical-align: middle;"><?= $j++ ?></td>
					<td style="vertical-align: middle;"><?= $emp_data->first_name.' '.$emp_data->last_name?></td>
					<td style="vertical-align: middle;"><?= $emp_data->designation_name?></td>
					<td style="vertical-align: middle;"><?= date('d M Y',strtotime($r->from_date)).' to '.date('d M Y',strtotime($r->to_date))?></td>
					<td style="vertical-align: middle;"><?= $r->qty?></td>
				</tr>

				<?php if($row_count % 17 == 0){?>
					<tr class="text-center" style='border:none !important'>
						<td colspan="30" style='border:none !important;margin-bottom:15px !important'>Page <?php echo @$k=1+$k?></td>
					</tr>
				<?php }?>
					
				<?php 
				} 
				if ($row_count == $total_rows) { ?>
					<!-- Add company header and legend on the final page -->
					<div class="company-header mb-4 mt-4">
						<div class="row align-items-center">
							<div class="col-4">
								<h3 class="fw-bold" style="margin-top:-35px;position: absolute;">e.Gen <br>Consultants Ltd</h3>
							</div>
							<div class="col-4">
								<h4 class="fw-bold text-center">Leave Report <br><p class="text-center h5">(Approved)</p></h4>
							</div>
							<div class="col-4 text-end">
								<img src="logo.png" alt="e.Gen Logo" height="60" style="margin: 5px;">
							</div>
						</div>
					</div>

					<div class="mb-3">
						<strong>Reporting Date: <?php echo date('Y-m-d',strtotime($first_date)).' to '.date('Y-m-d',strtotime($second_date))?></strong><br>
						<strong>Report Generated Date:</strong> <?php echo date('d M Y').', '.date('h:i:s A')?>
					</div>
				<?php } ?>

				<tr class="text-center" style='border:none !important'>
					<td colspan="30" style='border:none !important;margin-bottom:15px !important'>Page <?php echo @$k+1?></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>
