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
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		/* table tr th, table tr td {
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
		} */
		@media print {
			@page {
				size: A4;
				margin-bottom: 0px;
			}
		}
		table tr:last-child td:last-child {
			border: none !important;
		}
	</style>
</head>
<body class="container-fluid py-4">
	<div>
		<table class="table table-bordered table-sm border-dark">
			<tr class="text-center">
				<th>SL.</th>
				<th>ID</th>
				<th>Date</th>
				<th>Name</th>
				<th>Designation</th>
				<th>In Time</th>
				<th>Status</th>
			</tr>
			<tbody>
			<?php
			$j = 1;
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

				if ($row_count > 0 && $row_count % 15 == 0) {
					echo '<tr style="border:none;page-break-after: always;"></tr>'; 
					echo '<tr class="col-4"><td class="fw-bold" style="margin-top:-35px;position: absolute;">e.Gen <br>Consultants Ltd</td></tr>';
				}
				$row_count++;
			?>
			<tr class="text-center">
				<td><?= $j++ ?></td>
				<td><?= $r->user_id ?></td>
				<td><?= date('Y-m-d') ?></td>
				<td><?= $r->first_name . ' ' . $r->last_name ?></td>
				<td><?= $user_designation ?></td>
				<td>
					<?= isset($attendance_data) ? date('h:i:s a', strtotime($attendance_data->clock_in)) : '' ?><br>
					<?php 
					if (isset($attendance_data->clock_in)) {
						$first_time = new DateTime($attendance_data->clock_in);
						$second_time = new DateTime($late_start);
						$interval = $first_time->diff($second_time);
						if ($interval->h) echo $interval->h . ' hours ';
						if ($interval->i) echo $interval->i . ' minutes ';
						echo $interval->s . ' seconds late';
					} else {
						echo "N/A";
					}
					?>
				</td>
				<td>N/A</td>
			</tr>
			<?php if ($row_count % 17 == 0) { ?>
				<tr class="text-center" style="border:none !important">
					<td colspan="30" style="border:none !important;margin-bottom:15px !important">Page <?= @$k = 1 + $k ?></td>
				</tr>
			<?php } ?>
			<?php } ?>
			<?php if ($row_count == $total_rows) { ?>
				<tr class="col-4">
					<td class="fw-bold" style="margin-top:-35px;position: absolute;">e.Gen <br>Consultants Ltd</td>
				</tr>
			<?php } ?>
			<tr class="text-center" style="border:none !important">
				<td colspan="30" style="border:none !important;margin-bottom:15px !important">Page <?= @$k + 1 ?></td>
			</tr>
			</tbody>
		</table>
	</div>
</body>
</html>
