<?php 
	$first_date  = date('Y-m-01', strtotime($first_date));
	$second_date = date('Y-m-t', strtotime($first_date));
	$total_days = date('t', strtotime($first_date));
	$row_count = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Attendance Status Report (Late In)</title>
	<style>
		table {
			width: 100%;
			border-collapse: collapse;
			border: 1px solid #ddd;
		}
		th, td {
			padding: 5px;
			border: 1px solid #ddd;
		}
		th {
			background-color: #f8f9fa;
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
	</style>
</head>
<body class="container-fluid py-4">

			<table class="table table-bordered table-sm" style="border: 1px solid #ddd;">
				<!-- <thead class="header-row"> -->
					<tr class="text-center">
						<th>SL.</th>
						<th>ID</th>
						<th>Date</th>
						<th>Name</th>
						<th>Designation</th>
						<th>In Time</th>
						<th>Status</th>
					</tr>
				<!-- </thead> -->
				<tbody>
				<?php

				$j = 1;
				$row_count = 0;
				$total_rows = count($xin_employees);
				foreach ($xin_employees as $r) { 
					// $first_date = date('Y-m-01', strtotime($first_date));
					$late_start = $this->db->select('late_start')
					->get('emp_shift_schedule')
					->row('late_start');
					$attendance_data = $this->db->select('clock_in, clock_out, status')
					->where("attendance_date >=", $first_date)
					->where("attendance_date <=", $second_date) 
					->where('employee_id', $r->user_id)
					->where('TIME(clock_in) >=', date('h:i:01', strtotime($late_start)))
					->get('xin_attendance_time')
					->row();

					// dd($this->db->last_query());


					$user_designation = $this->db->select('designation_name')
					->where('designation_id', $r->designation_id)
					->get('xin_designations')
					->row('designation_name');
					// if(empty($attendance_data)){
					// 	continue;
					// }
					// dd($user_designation);
					if ($row_count > 0 && $row_count % 17 == 0) {
						echo '<tr class="page-break" style="border:none;"></tr>';?> 
						<!-- Add company header on new page -->
						<tr>
							<td colspan="3" class="company-header mb-4 mt-1">
								<table class="w-100">
									<tr class="row align-items-center">
										<td class="col-4">
											<h3 class="fw-bold" style="margin-top:-35px;position: absolute;">e.Gen <br>Consultants Ltd</h3>
										</td>
										<td class="col-4">
											<h4 class="fw-bold text-center">Attendance Report <br><p class="text-center h5">(Late In)</p></h4>
										</td>
										<td class="col-4 text-end">
											<img src="logo.png" alt="e.Gen Logo" height="60" style="margin: 5px;">
										</td>
									</tr>
								</table>
							</td>
						</tr>

						<tr class="mb-3">
							<td>Reporting Date: <?php echo date('Y-m-d',strtotime($first_date)).' to '.date('Y-m-d',strtotime($second_date))?></td><br>
							<td>Report Generated Date:</td> <?php echo date('d M Y').', '.date('h:i:s A')?>
						</tr>

						<table class="table table-bordered table-sm" style="border: 1px solid #ddd;">
							<thead class="header-row">
								<tr class="text-center">
									<th>SL.</th>
									<th>ID</th>
									<th>Date</th>
									<th>Name</th>
									<th>Designation</th>
									<th>Leave</th>
									<th>Time</th>
									<th>Status</th>
								</tr>
							</thead>
					<?php }
					$row_count++;
				?>

				<tr class="text-center">
					<td style="vertical-align: middle;"><?= $j++ ?></td>
					<td style="vertical-align: middle;"><?= $r->user_id ?></td>
					<td style="vertical-align: middle;"><?= date('Y-m-d')?></td>
					<td style="vertical-align: middle;"><?= $r->first_name . ' ' . $r->last_name ?></td>
					<td style="vertical-align: middle;"><?= $user_designation ?></td>
					<td style="vertical-align: middle;">
					<?= isset($attendance_data) ? date('h:i:s a',strtotime($attendance_data->clock_in)) : '' ?><br>
					<?php 
						// Assuming $attendance_data->clock_in and $attendance_data->clock_out are the time values
						if (isset($attendance_data->clock_in) && isset($attendance_data->clock_out)) {

							$first_time = new DateTime(date('h:i:s a', strtotime($attendance_data->clock_in)));
							$second_time = new DateTime(date('h:i:s a', strtotime($late_start)));
							$interval = $first_time->diff($second_time);
							$hours = $interval->h;
							$minutes = $interval->i;
							$seconds = $interval->s;
							if($hours != 0) {
								echo $hours . ' hours ';
							}
							if($minutes != 0) {
								echo $minutes . ' minutes ';
							}
							echo $seconds . ' seconds late';
						} else {
							echo "N/A";
						}
					?>
					</td>
					<!-- <td style="vertical-align: middle;">< ?= isset($attendance_data) ? $attendance_data->status : '' ?></td> -->
					<td style="vertical-align: middle;"><?php echo "N/A"?></td>
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
					<tr>
						<td colspan="30" style="text-align: center;">
							<h3 class="fw-bold">e.Gen <br>Consultants Ltd</h3>
						</td>
					</tr>
					<tr>
						<td colspan="30" style="text-align: center;">
							<h4 class="fw-bold">Attendance Report <br><p class="text-center h5">(Late In)</p></h4>
						</td>
					</tr>
					<tr>
						<td colspan="30" style="text-align: right;">
							<img src="logo.png" alt="e.Gen Logo" height="60" style="margin: 5px;">
						</td>
					</tr>

					<tr class="mb-3">
						<td>Reporting Date: <?php echo date('Y-m-d',strtotime($first_date)).' to '.date('Y-m-d',strtotime($second_date))?></td><br>
						<td>Report Generated Date:</td> <?php echo date('d M Y').', '.date('h:i:s A')?>
					</tr>
				<?php } ?>

				<tr class="text-center" style='border:none !important'>
					<td colspan="30" style='border:none !important;margin-bottom:15px !important'>Page <?php echo @$k+1?></td>
				</tr>
				</tbody>
			</table>

</body>
</html>

