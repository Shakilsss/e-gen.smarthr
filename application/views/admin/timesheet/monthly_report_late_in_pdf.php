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
				margin-bottom: 0px;
			}

			.page-break {
				page-break-after: always;
				/* margin-bottom: 10px; */
			}
		}
	</style>
</head>
<body>

			<table class="table table-bordered table-sm" style="border: 1px solid #ddd;">
			
					<thead>
						<tr class="text-center">
							<td>SL.</td>
							<td>ID</td>
							<td>Date</td>
							<td>Name</td>
							<td>Designation</td>
							<td>Leave</td>
							<td>Time</td>
							<td>Status</td>
						</tr>
					</thead>
				<tbody>
				<?php

				$j = 1;
				$row_count = 0;
				$total_rows = count($xin_employees);
				foreach ($xin_employees as $r) { 
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

					$user_designation = $this->db->select('designation_name')
					->where('designation_id', $r->designation_id)
					->get('xin_designations')
					->row('designation_name');

					if ($row_count > 0 && $row_count % 12 == 0) {
						echo '<tr class="page-break" style="border:none;"></tr>';?> 

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
					<td style="vertical-align: middle;"><?php echo "N/A"?></td>
				</tr>

				<!-- < ?php if($row_count % 12 == 0){?>

				< ?php }?> -->
					
				<?php 
				} 
				// if ($row_count == $total_rows) { 
				
				?>

				<!-- < ?php } ?> -->


				</tbody>
			</table>

</body>
</html>

