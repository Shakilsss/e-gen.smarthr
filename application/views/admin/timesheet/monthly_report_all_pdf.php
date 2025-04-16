<?php
$first_date  = date('Y-m-01', strtotime($first_date));
$second_date = date('Y-m-t', strtotime($first_date));
$total_days = date('t', strtotime($first_date));
$row_count = 0;

// Fetch attendance data
$attendance_data = $this->db->select('employee_id, attendance_date, status')
	->where("attendance_date >=", $first_date)
	->where("attendance_date <=", $second_date)
	->get('xin_attendance_time')
	->result();
// Organize attendance data by employee_id and date
$attendance_by_employee = [];
foreach ($attendance_data as $data) {
	$attendance_by_employee[$data->employee_id][$data->attendance_date] = $data->status;
}
// Function to render table header
function renderTableHeader($total_days) {

	echo '<tr class="text-center"><th>SL.</th><th>ID</th><th>Name</th>';
	for ($i = 1; $i <= $total_days; $i++) {
		echo "<th>$i</th>";
	}
	echo '</tr>';
}

// Function to render attendance cell
function renderAttendanceCell($status, $current_date, $user_id, $db) {
	$bg_color = $status == 'Off Day' || $status == 'Holiday' ? 'red' : '';
	$text_color = $bg_color ? 'white' : '';
	switch ($status) {
		case 'Off Day':
			$display_status = 'W';
			break;
		case 'Present':
			$display_status = 'P';
			break;
		case 'Holiday':
			$display_status = 'H';
			break;
		case 'Leave':
			$leave_type = $db->select('leave_type')
				->where('from_date<=', $current_date)
				->where('to_date>=', $current_date)
				->where('employee_id', $user_id)
				->get('xin_leave_applications')
				->row("leave_type");
			$display_status = strtoupper($leave_type);
			break;
		default:
			$display_status = 'A';
			break;
	}
	echo "<td style='background:$bg_color; color:$text_color; font-weight:bold;'>$display_status</td>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Attendance Status Report (All) PDF</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		table th, table td { font-size: 0.9rem; }
		.header-row { background-color: #f8f9fa; }
		@media print { @page { size: A4 landscape; margin-top: 5px; } }
	</style>
</head>
<body class="container-fluid py-4">
	<table class="table table-bordered table-sm border-dark">
		<thead class="header-row">
			<?php renderTableHeader($total_days); ?>
		</thead>
		<tbody>
		<?php
		$j = 1;
		foreach ($xin_employees as $r) {
			if ($row_count > 0 && $row_count % 19 == 0) {
				echo '<div style="page-break-after: always;"></div>';
				renderTableHeader($total_days);
			}
			$row_count++;
			echo "<tr class='text-center'><td>$j</td><td>{$r->user_id}</td><td>{$r->first_name} {$r->last_name}</td>";
			for ($d = 1; $d <= $total_days; $d++) {
				$current_date = date('Y-m-d', strtotime("$first_date +".($d - 1)." days"));
				$status = $attendance_by_employee[$r->user_id][$current_date] ?? 'Absent';
				renderAttendanceCell($status, $current_date, $r->user_id, $this->db);
			}
			echo '</tr>';
			$j++;
		}
		?>
		</tbody>
	</table>
</body>
</html>

