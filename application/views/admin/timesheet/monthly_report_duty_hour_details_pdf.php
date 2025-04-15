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
	<title>Attendance Status Report (Duty Hour Details)</title>
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
		}
	</style>
</head>
<body class="container-fluid py-4">
	<div id="">
		<div class="">
			<table class="table table-bordered table-sm border-dark">
				<!-- <thead class="header-row"> -->
					<tr class="text-center">
						<th style="vertical-align: middle;">SL.</th>
						<th style="vertical-align: middle;">ID</th>
						<th style="vertical-align: middle;">Name</th>
						<?php
							$day_of_month = date('t',strtotime($first_date)); 
							// Print the days column header only once
							for ($i = 1; $i <= $day_of_month; $i++) {
						?>
						<th style="vertical-align: middle;"><?php echo $i?></th>
						<?php } ?>
						<th style="vertical-align: middle;">Total Hours</th>

					</tr>
				<!-- </thead> -->
				<tbody>
				<?php
				$j = 1;
				$row_count = 0;
				$total_rows = count($xin_employees);
				foreach ($xin_employees as $r) { 

					if ($row_count > 0 && $row_count % 10 == 0) {
						echo '<tr class="page-break"></tr>';?> 
						<!-- Add company header on new page -->
						<div class="company-header mb-4">
							<div class="row align-items-center">
								<div class="col-4">
									<h3 class="fw-bold" style="margin-top:-35px;position: absolute;">e.Gen Consultants Ltd</h3>
								</div>
								<div class="col-4">
									<h4 class="fw-bold text-center">Attendance Report <br><p class="text-center h5">(Duty Hour Details)</p></h4>
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
									<th style="vertical-align: middle;">SL.</th>
									<th style="vertical-align: middle;">ID</th>
									<th style="vertical-align: middle;">Name</th>
									<?php
										// Print the days column header again on a new page
										for ($i = 1; $i <= $day_of_month; $i++) {
									?>
									<th style="vertical-align: middle;"><?php echo $i?></th>
									<?php } ?>
									<th style="vertical-align: middle;">Total Hours</th>
								</tr>
							</thead>
					<?php }
					$row_count++;
				?>
					<tr class="text-center">
						<td style="vertical-align: middle;"><?= $j++ ?></td>
						<td style="vertical-align: middle;"><?= $r->user_id ?></td>
						<td style="vertical-align: middle;"><?= $r->first_name . ' ' . $r->last_name ?></td>
						<?php
						$total_minutes = 0;
					for ($d = 1; $d <= $total_days; $d++) {
						$current_date = date('Y-m-d', strtotime("$first_date +".($d - 1)." days"));
						$attendance_data = $this->db->select('clock_in, clock_out, status')
							->where("attendance_date", $current_date)
							->where("employee_id", $r->user_id)
							->get('xin_attendance_time')
							->result();
						echo '<td style="vertical-align: middle;">';
						if (empty($attendance_data)) {
							echo '';
						} else {
							$day_minutes = 0;

							foreach ($attendance_data as $data) {
								if (!empty($data->clock_in) && !empty($data->clock_out)) {
									$clock_in_time = strtotime($data->clock_in);
									$clock_out_time = strtotime($data->clock_out);
									$time_difference = $clock_out_time - $clock_in_time;
									$hours = floor($time_difference / 3600);
									$minutes = floor(($time_difference % 3600) / 60);
									
									$day_minutes += ($hours * 60) + $minutes;
								}
							}

							if ($day_minutes > 0) {
								echo date('h:i a',strtotime($data->clock_in)).'<br>' . date('h:i a',strtotime($data->clock_out)). '<br>';
								$total_minutes += $day_minutes;
								printf("%02d:%02d", floor($day_minutes / 60), $day_minutes % 60);
							} else {
								if ($data->status == 'Off Day') {
									echo 'W';
								} elseif ($data->status == 'Present') {
									echo 'P';
								} elseif ($data->status == 'Holiday') {
									echo 'H';
								} elseif ($data->status == 'Leave') {
									$this->db->select('leave_type');
									$this->db->where('from_date<=', $current_date);
									$this->db->where('to_date>=', $current_date);
									$this->db->where('employee_id', $r->user_id);
									$leave_type = $this->db->get('xin_leave_applications')->row("leave_type");
									echo strtoupper($leave_type);
								} else {
									echo 'A';
								}
							}
						}

						echo '</td>';
					}
					$total_hours_final = floor($total_minutes / 60);
					$total_minutes_final = $total_minutes % 60;
					?>
					<td style="vertical-align: middle;"><strong><?= sprintf("%02d:%02d", $total_hours_final, $total_minutes_final) ?></strong></td>
					</tr>		
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
								<h4 class="fw-bold text-center">Attendance Report <br><p class="text-center h5">(Duty Hour Details)</p></h4>
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
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>
