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
	<title>Attendance Status Report (All)</title>
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
				size: A4 landscape;
				margin-top: 5px;
			}

			/* .page-break {
				page-break-before: always;
			} */
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
						<th>ID</th>
						<th>Name</th>
						<?php
							$day_of_month = date('t',strtotime($first_date)); 
							// Print the days column header only once
							for ($i = 1; $i <= $day_of_month; $i++) {
						?>
						<th><?php echo $i?></th>
						<?php } ?>
					</tr>
				<!-- </thead> -->
				<tbody>
				<?php

				// Fetch all attendance data in one query
				$attendance_data = $this->db->select('employee_id, attendance_date, e_status, status')
					->where("attendance_date >=", $first_date)
					->where("attendance_date <=", $second_date) // Avoid duplicate data
					->get('xin_attendance_time')
					->result();
				// dd($attendance_data);	

				// Organize attendance data by employee_id and date
				$attendance_by_employee = [];
				foreach ($attendance_data as $data) {
					$attendance_by_employee[$data->employee_id][$data->attendance_date] = $data->status;
				}

				$j = 1;
				$row_count = 0;
				$total_rows = count($xin_employees);
				foreach ($xin_employees as $r) { 
					if ($row_count > 0 && $row_count % 16 == 0) {
						echo '<tr class="page-break" style="border:none"></tr>';?> 
						<!-- Add company header on new page -->
						<div class="company-header mb-4">
							<div class="row align-items-center">
								<div class="col-4">
									<h3 class="fw-bold" style="margin-top:-35px;position: absolute;">e.Gen Consultants Ltd</h3>
								</div>
								<div class="col-4">
									<h4 class="fw-bold text-center">Attendance Report <br><p class="text-center h5">(All)</p></h4>
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

						<div class="legend mb-3">
							<span>*Legend:</span>
							<strong>CL - Casual Leave</strong>
							<strong>SL - Sick Leave</strong>
							<strong>Stl - Station Leave</strong>
							<strong>NL - Night Stay Leave</strong>
							<strong>H - Holiday</strong>
							<strong>A - Absent</strong>
							<strong>P - Present</strong>
							<strong>E - Early Leave</strong>
							<strong>L - Late in</strong>
							<strong>L/E - Early Leave and Late in</strong>
						</div>

						<table class="table table-bordered table-sm border-dark">
							<thead class="header-row">
								<tr class="text-center">
									<th>SL.</th>
									<th>ID</th>
									<th>Name</th>
									<?php
										// Print the days column header again on a new page
										for ($i = 1; $i <= $day_of_month; $i++) {
									?>
									<th><?php echo $i?></th>
									<?php } ?>
								</tr>
							</thead>
					<?php }
					$row_count++;
				?>

				<tr class="text-center">
					<td><?= $j++ ?></td>
					<td><?= $r->user_id ?></td>
					<td><?= $r->first_name . ' ' . $r->last_name ?></td>
					<?php
					for ($d = 1; $d <= $total_days; $d++) {
						$current_date = date('Y-m-d', strtotime("$first_date +".($d - 1)." days"));
						$status = $attendance_by_employee[$r->user_id][$current_date] ?? 'Absent'; // Default to Absent
						// dd($status);
						// Set background and text color
						$bg_color = $status == 'Off Day' ? 'red' : ($status == 'Holiday' ? 'red' : '');
						$text_color = $status == 'Off Day' || $status == 'Holiday' ? 'white' : '';

						echo '<td style="background:'.$bg_color.'; color:'.$text_color.';font-weight:bold;">';
						if ($status == 'Off Day') {
							echo 'W';
						} elseif ($status == 'Present') {
							echo 'P';
						} elseif ($status == 'Holiday') {
							echo 'H';
						} elseif ($status == 'Leave') {
							$this->db->select('leave_type');
							$this->db->where('from_date<=', $current_date);
							$this->db->where('to_date>=', $current_date);
							$this->db->where('employee_id', $r->user_id);
							$leave_type = $this->db->get('xin_leave_applications')->row("leave_type");
							echo strtoupper($leave_type);
						} else {
							echo 'A';
						}
						echo '</td>';
					}
					?>
				</tr>

				<?php if($row_count % 16 == 0){?>
					<tr class="text-center" style='border:none !important'>
						<td colspan="30" style='border:none !important'>Page <?php echo @$k=1+$k?></td>
					</tr>
				<?php }?>
					
				<?php 
				} 
				if ($row_count == $total_rows) { ?>
					<!-- Add company header and legend on the final page -->
					<div class="company-header mb-4">
						<div class="row align-items-center">
							<div class="col-4">
								<h3 class="fw-bold" style="margin-top:-35px;position: absolute;">e.Gen Consultants Ltd</h3>
							</div>
							<div class="col-4">
								<h4 class="fw-bold text-center">Attendance Report <br><p class="text-center h5">(All)</p></h4>
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

					<div class="legend mb-3">
						<span>*Legend:</span>
						<strong>CL - Casual Leave</strong>
						<strong>SL - Sick Leave</strong>
						<strong>Stl - Station Leave</strong>
						<strong>NL - Night Stay Leave</strong>
						<strong>H - Holiday</strong>
						<strong>A - Absent</strong>
						<strong>P - Present</strong>
						<strong>E - Early Leave</strong>
						<strong>L - Late in</strong>
						<strong>L/E - Early Leave and Late in</strong>
					</div>
				<?php } ?>

				<tr class="text-center" style='border:none !important'>
					<td colspan="30" style='border:none !important'>Page <?php echo @$k+1?></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>
