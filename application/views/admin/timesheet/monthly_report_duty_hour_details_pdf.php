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
	<style>
		body {
			font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
			font-size: 10px;
			line-height: 1.2em;
		}
		table {
			width: 100%;
			border-collapse: collapse;
		}
		th, td {
			padding: 5px;
			border: 1px solid #ddd;
			text-align: left;
		}
		th {
			background-color: #f8f9fa;
		}
		@media print {
			@page {
				size: A4 landscape;
				margin: 0;
			}
			body {
				margin: 0;
				padding: 0;
			}
		}
	</style>
</head>
<body>
	<table>
		<tr>
			<th style="width: 20px;">SL.</th>
			<th style="width: 100px;">ID</th>
			<th style="width: 200px;">Name</th>
			<?php for ($i = 1; $i <= $total_days; $i++) { ?>
				<th style="width: 50px;"><?php echo $i?></th>
			<?php } ?>
			<th style="width: 100px; whitespace:nowrap">Total Hours</th>
		</tr>
		<tbody>
		<?php
		$j = 1;
		$row_count = 0;
		$total_rows = count($xin_employees);
		foreach ($xin_employees as $r) { 
			if ($row_count > 0 && $row_count % 10 == 0) {
				echo '<tr style="page-break-before: always;"></tr>';
			}
			$row_count++;
		?>
			<tr>
				<td style="text-align: center; vertical-align: middle;whitespace:nowrap"><?= $j++ ?></td>
				<td style="text-align: center; vertical-align: middle;"><?= $r->user_id ?></td>
				<td style="text-align: left; vertical-align: middle;"><?= $r->first_name . ' ' . $r->last_name ?></td>
				<?php
				$total_minutes = 0;
				for ($d = 1; $d <= $total_days; $d++) {
					$current_date = date('Y-m-d', strtotime("$first_date +".($d - 1)." days"));
					$attendance_data = $this->db->select('clock_in, clock_out, status')
						->where("attendance_date", $current_date)
						->where("employee_id", $r->user_id)
						->get('xin_attendance_time')
						->result();
					echo '<td style="text-align: center; vertical-align: middle;">';
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
				<td style="text-align: center; vertical-align: middle;"><strong><?= sprintf("%02d:%02d", $total_hours_final, $total_minutes_final) ?></strong></td>
			</tr>		
		<?php } ?>
		</tbody>
	</table>
</body>
</html>