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
	<title>Attendance Status Report (Duty Hour)</title>
	<style>
		table {
			width: 100%;
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;
		}
		th, td {
			font-size: 12px;
			text-align: center;
			padding: 5px;
		}
		.header-row {
			background-color: #f0f0f0;
			font-weight: bold;
		}
		.company-header {
			text-align: center;
			margin-bottom: 10px;
		}
		.company-header h3, .company-header h4 {
			margin: 5px 0;
		}
	</style>
</head>
<body>

		<table class="company-header">
			<!-- <tr>
				<td>e.Gen Consultants Ltd</td>
				<td>Attendance Report <br><span>(Duty Hour)</span></td>
			</tr> -->
		<tr>
			<td colspan="<?= $total_days + 4 ?>">
				<strong>Reporting Date:</strong> <?= date('Y-m-d', strtotime($first_date)) . ' to ' . date('Y-m-d', strtotime($second_date)) ?><br>
				<strong>Report Generated Date:</strong> <?= date('d M Y') . ', ' . date('h:i:s A') ?>
			</td>
		</tr>
		<tr class="header-row">
			<th>SL.</th>
			<th>ID</th>
			<th>Name</th>
			<?php for ($i = 1; $i <= $total_days; $i++) { ?>
				<th><?= $i ?></th>
			<?php } ?>
			<th>Total Hours</th>
		</tr>
		<tbody>
		<?php
		$j = 1;
		$row_count = 0;
		$total_rows = count($xin_employees);
		foreach ($xin_employees as $r) { 
			$row_count++;
		?>
			<tr>
				<td><?= $j++ ?></td>
				<td><?= $r->user_id ?></td>
				<td><?= $r->first_name . ' ' . $r->last_name ?></td>
				<?php
				$total_minutes = 0;
				for ($d = 1; $d <= $total_days; $d++) {
					$current_date = date('Y-m-d', strtotime("$first_date +".($d - 1)." days"));
					$attendance_data = $this->db->select('clock_in, clock_out, status')
						->where("attendance_date", $current_date)
						->where("employee_id", $r->user_id)
						->get('xin_attendance_time')
						->result();
					echo '<td>';
					if (empty($attendance_data)) {
						echo '00:00';
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
							$total_minutes += $day_minutes;
							printf("%02d:%02d", floor($day_minutes / 60), $day_minutes % 60);
						} else {
							echo '00:00';
						}
					}

					echo '</td>';
				}
				$total_hours_final = floor($total_minutes / 60);
				$total_minutes_final = $total_minutes % 60;
				?>
				<td><strong><?= sprintf("%02d:%02d", $total_hours_final, $total_minutes_final) ?></strong></td>
			</tr>		
		<?php } ?>
		</tbody>
	</table>
</body>
</html>
