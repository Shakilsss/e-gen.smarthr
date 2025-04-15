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
				size: A4;
				margin-bottom: 0px;
			}
			.page-break {
				page-break-after: always;
				margin-bottom: 10px;
			}
		}
		table tr:last-child td:last-child {
			border: none !important;
		}
	</style>
</head>
<body class="container-fluid py-4">
	<table class="table table-bordered table-sm border-dark">
		<tr class="text-center">
			<th>SL.</th>
			<th>ID</th>
			<th>Date</th>
			<th>Name</th>
			<th>Designation</th>
			<th>Leave Time</th>
			<th>Status</th>
		</tr>
		<tbody>
		<?php
		$j = 1;
		$row_count = 0;
		$total_rows = count($xin_employees);
		foreach ($xin_employees as $r) { 
			$first_date = date('Y-m-d', strtotime($first_date ));
			$second_date = date('Y-m-d', strtotime($first_date));
			$out_time = $this->db->select('out_time')
			->get('emp_shift_schedule')
			->row('out_time');
			$attendance_data = $this->db->select('clock_in, clock_out, status')
			->where("attendance_date >=", $first_date)
			->where("attendance_date <=", $second_date) 
			->where('employee_id', $r->user_id)
			->where('TIME(clock_out) <=', date('h:i:01', strtotime($out_time)))
			->where('status', 'Present')
			->get('xin_attendance_time')
			->row();
		
			$user_designation = $this->db->select('designation_name')
			->where('designation_id', $r->designation_id)
			->get('xin_designations')
			->row('designation_name');

			if ($row_count > 0 && $row_count % 17 == 0) {
				echo '<tr class="page-break" style="border:none;"></tr>'; ?>
				<tr>
					<td colspan="7" class="text-center">
						<table class="table table-bordered">
							<tr>
								<td colspan="3">
									<h3 class="fw-bold">e.Gen <br>Consultants Ltd</h3>
								</td>
								<td colspan="4" class="text-center">
									<h4 class="fw-bold">Attendance Report <br><span class="h5">(Early Leave)</span></h4>
								</td>
							</tr>
							<tr>
								<td colspan="7">
									<strong>Reporting Date:</strong> <?php echo date('Y-m-d',strtotime($first_date)).' to '.date('Y-m-d',strtotime($second_date))?><br>
									<strong>Report Generated Date:</strong> <?php echo date('d M Y').', '.date('h:i:s A')?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
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
			<?= isset($attendance_data) ? date('h:i:s a',strtotime($attendance_data->clock_out)) : '' ?><br>
			<?php 
				if (isset($attendance_data->clock_in) && isset($attendance_data->clock_out)) {
					$clock_in = new DateTime(date('h:i:s a', strtotime($attendance_data->clock_out)));
					$clock_out = new DateTime(date('h:i:s a', strtotime($out_time)));
					$interval = $clock_in->diff($clock_out);
					$hours = $interval->h;
					$minutes = $interval->i;
					$seconds = $interval->s;
					echo $hours . ' hours ' . $minutes . ' minutes ' . $seconds . ' seconds';
				} else {
					echo "--";
				}
			?>
			</td>
			<td><?php echo "N/A"?></td>
		</tr>

		<?php if($row_count % 17 == 0){?>
			<tr class="text-center" style='border:none !important'>
				<td colspan="7" style='border:none !important;margin-bottom:15px !important'>Page <?php echo @$k=1+$k?></td>
			</tr>
		<?php } ?>
			
		<?php 
		} 
		if ($row_count == $total_rows) { ?>
			<tr>
				<td colspan="7" class="text-center">
					<table class="table table-bordered">
						<tr>
							<td colspan="3">
								<h3 class="fw-bold">e.Gen <br>Consultants Ltd</h3>
							</td>
							<td colspan="4" class="text-center">
								<h4 class="fw-bold">Attendance Report <br><span class="h5">(Early Leave)</span></h4>
							</td>
						</tr>
						<tr>
							<td colspan="7">
								<strong>Reporting Date:</strong> <?php echo date('Y-m-d',strtotime($first_date)).' to '.date('Y-m-d',strtotime($second_date))?><br>
								<strong>Report Generated Date:</strong> <?php echo date('d M Y').', '.date('h:i:s A')?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		<?php } ?>

		<tr class="text-center" style='border:none !important'>
			<td colspan="7" style='border:none !important;margin-bottom:15px !important'>Page <?php echo @$k+1?></td>
		</tr>
		</tbody>
	</table>
</body>
</html>
