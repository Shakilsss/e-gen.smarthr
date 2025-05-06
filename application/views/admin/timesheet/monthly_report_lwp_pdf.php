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
	<title>Attendance Status Report (LWP/Absent)</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

	<style>
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
		table tr:last-child td:last-child {
			border: none !important;
		}
	</style>
</head>
<body>
	<table class="table table-bordered table-sm border-dark">
		<thead class="header-row">
			<tr class="text-center">
				<th>SL.</th>
				<th>ID</th>
				<th>Name</th>
				<?php
					$day_of_month = date('t',strtotime($first_date));
					for ($i = 1; $i <= $day_of_month; $i++) {
				?>
				<th><?php echo $i?></th>
				<?php } ?>
				<th>Total LWP</th>
			</tr>
		</thead>
		<tbody>
		<?php

		// Fetch all attendance data in one query
		$attendance_data = $this->db->select('employee_id, attendance_date, e_status, status')
			->where("attendance_date >=", $first_date)
			->where("attendance_date <=", $second_date) 
			->get('xin_attendance_time')
			->result();
		$attendance_by_employee = [];
		foreach ($attendance_data as $data) {
			$attendance_by_employee[$data->employee_id][$data->attendance_date] = $data->status;
		}

		$j = 1;
		$row_count = 0;
		$total_rows = count($xin_employees);
		foreach ($xin_employees as $r) {
			$count = 0;
			if ($row_count > 0 && $row_count % 19 == 0) {
				echo '<tr class="page-break" style="border:none"></tr>';?>
			

				<!-- <tr class="text-center">
					<th>SL.</th>
					<th>ID</th>
					<th>Name</th>
					< ?php
						// Print the days column header again on a new page
						for ($i = 1; $i <= $day_of_month; $i++) {
					?>
					<th>< ?php echo $i?></th>
					< ?php } ?>
					<th>Total LWP</th>
				</tr> -->
		<?php }
			$row_count++;
		?>

		<tr class="text-center">
			<td style="vertical-align: middle;"><?= $j++ ?></td>
			<td style="vertical-align: middle;"><?= $r->user_id ?></td>
			<td style="vertical-align: middle;"><?= $r->first_name . ' ' . $r->last_name ?></td>
			<?php
			for ($d = 1; $d <= $total_days; $d++) {
				$current_date = date('Y-m-d', strtotime("$first_date +".($d - 1)." days"));
				$status = isset($attendance_by_employee[$r->user_id][$current_date]) ? $attendance_by_employee[$r->user_id][$current_date] : 'Absent';
				$bg_color = $status == 'Off Day' ? 'red' : ($status == 'Holiday' ? 'red' : '');
				$text_color = $status == 'Off Day' || $status == 'Holiday' ? 'white' : '';

				echo '<td style="background:'.$bg_color.'; color:'.$text_color.';font-weight:bold; vertical-align: middle;">';
				if ($status == 'Off Day') {
					echo 'W';
				} elseif ($status == 'Present') {
					echo ''; 
				} elseif ($status == 'Holiday') {
					echo 'H';
				} elseif ($status == 'Leave') {
					$this->db->select('leave_type');
					$this->db->where('from_date<=', $current_date);
					$this->db->where('to_date>=', $current_date);
					$this->db->where('employee_id', $r->user_id);
					$leave_type = $this->db->get('xin_leave_applications')->row("leave_type");
					echo $leave_type == 'wp' ? 'A' : '';
					if($leave_type == 'wp'){
						@$count++;
					}
				} else {
					echo '';
				}
				echo '</td>';
			}
			?>

			<td style="vertical-align: middle;"><?= @$count ?></td>
		</tr>

		<?php if($row_count % 19 == 0){?>
		
		<?php }?>

		<?php
		}
		if ($row_count == $total_rows) { ?>

		<?php } ?>

		</tbody>
	</table>
</body>
</html>

