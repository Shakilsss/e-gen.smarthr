<?php 
	$first_date  = date('Y-m-01', strtotime($first_date));
	$second_date = date('Y-m-t', strtotime($first_date));
	$total_days = date('t', strtotime($first_date));
	$row_count = 0;
	$page_number = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Attendance Status Report (Late In)</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		@media print {
			@page {
				size: A4;
				margin-bottom: 0px;
			}
			.page-break {
				page-break-after: always;
			}
		}
		.company-header {
			font-weight: bold;
			margin-bottom: 10px;
		}
	</style>
</head>
<body class="container-fluid py-4">
<?php
$j = 1;
$total_rows = count($xin_employees);
foreach ($xin_employees as $index => $r) { 

	// Load shift data and attendance info
	$late_start = $this->db->select('late_start')
		->get('emp_shift_schedule')
		->row('late_start');
	$attendance_data = $this->db->select('clock_in, clock_out, status')
		->where("attendance_date >=", $first_date)
		->where("attendance_date <=", $second_date) 
		->where('employee_id', $r->user_id)
		->where('TIME(clock_in) >=', date('H:i:01', strtotime($late_start)))
		->get('xin_attendance_time')
		->row();
	$user_designation = $this->db->select('designation_name')
		->where('designation_id', $r->designation_id)
		->get('xin_designations')
		->row('designation_name');

	// Start a new page after every 15 records
	if ($row_count % 15 == 0) {
		if ($row_count > 0) {
			echo '</tbody></table>'; // Close previous table
			echo '<div class="text-center mb-3">Page ' . $page_number++ . '</div>';
			echo '<div class="page-break"></div>'; // Add page break
		}
?>
		<!-- Company Header and Table Start -->
		<div class="company-header mb-4 mt-1">
			<div class="row align-items-center">
				<div class="col-4">
					<h3 class="fw-bold" style="margin-top:-35px;position: absolute;">e.Gen <br>Consultants Ltd</h3>
				</div>
				<div class="col-4">
					<h4 class="fw-bold text-center">Attendance Report <br><p class="text-center h5">(Late In)</p></h4>
				</div>
				<div class="col-4 text-end">
					<img src="logo.png" alt="e.Gen Logo" height="60" style="margin: 5px;">
				</div>
			</div>
		</div>
		<hr>
		<div class="mb-3">
			<strong>Reporting Date: <?php echo date('Y-m-d',strtotime($first_date)).' to '.date('Y-m-d',strtotime($second_date))?></strong><br>
			<strong>Report Generated Date:</strong> <?php echo date('d M Y').', '.date('h:i:s A')?>
		</div>
		<table class="table table-bordered table-sm border-dark">
			<thead>
				<tr class="text-center">
					<th>SL.</th>
					<th>ID</th>
					<th>Date</th>
					<th>Name</th>
					<th>Designation</th>
					<th>In Time</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
<?php
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
			<?php 
			if (isset($attendance_data->clock_in)) {
				echo date('h:i:s a', strtotime($attendance_data->clock_in)) . '<br>';
				$first_time = new DateTime($attendance_data->clock_in);
				$second_time = new DateTime($late_start);
				$interval = $first_time->diff($second_time);
				if ($interval->invert == 0) { // check if it's truly late
					if ($interval->h) echo $interval->h . ' hours ';
					if ($interval->i) echo $interval->i . ' minutes ';
					echo $interval->s . ' seconds late';
				} else {
					echo "On time";
				}
			} else {
				echo "N/A";
			}
			?>
		</td>
		<td>N/A</td>
	</tr>
<?php } ?>

<?php if ($total_rows > 0): ?>
	</tbody>
	</table>
	<div class="text-center mb-3">Page <?= $page_number ?></div>
<?php endif; ?>
</body>
</html>
