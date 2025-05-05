  <?php
    $session = $this->session->userdata('username');
    $user = $this->Xin_model->read_employee_info($session['user_id']);
  ?>
  <?php $get_animate = $this->Xin_model->get_content_animate(); ?>

  <!-- breadcrumb -->
  <div class="col-md-8">
    <div class="box-widget widget-user-2">
      <div class="widget-user-header">
        <h4 class="widget-user-username welcome-hrsale-user">
          <?php echo $this->lang->line('xin_title_wcb'); ?>,
          <?php echo $user[0]->first_name . ' ' . $user[0]->last_name; ?>!
        </h4>
        <h5 class="widget-user-desc welcome-hrsale-user-text">
          <?php echo $this->lang->line('xin_title_today_is'); ?>
          <?php echo date('l, j F Y'); ?>
        </h5>
      </div>
    </div>
  </div>

  <!-- Filtering Section -->
  <div class="col-md-4" style="padding-right: 0px;">
    <?php $coms = $this->db->get('xin_companies')->result(); ?>
    <div class="col-md-8">
      <div class="form-group">
        <label> Organization </label>
        <select class="form-control" name="unit_id" id="unit_id" onchange="get_ajax_data()">
          <option value="">select one</option>
          <?php foreach ($coms as $key => $r) { ?>
            <option value="<?= $r->company_id ?>"><?= $r->name ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="col-md-4" style="padding: 0px;">
      <div class="form-group">
        <label> Date </label>
        <input onchange="get_ajax_data()" class="form-control date" id="date" value="<?php echo date('Y-m-d'); ?>" >
      </div>
    </div>
  </div>
  <div class="clearfix"></div>

  <style>
    .ctt {
      color: #262626 !important;
      font-size: 12px !important;;
    }
  </style>

  <!-- Card Section -->
  <?php $res = $this->Dashboard_model->count_attendance_status_wise(date('Y-m-d'), null); ?>
  <div class="row">

    <!-- total employees -->
    <div class="col-md-3">
      <div class="card p-3" onclick="get_ajax_data()">
        <div class="d-flex align-items-center">
          <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-success-4 mr-3"> <i class="fa fa-user"></i></span>
          <div>
            <h5 class="mb-1">
                <span class="ctt"> Employees ( Regular ) </span> <br>
                <span id="reg_emp"> <?= isset($res->counts) ? $res->counts : 0; ?> </span>
            </h5>
          </div>
        </div>
      </div>
    </div>

    <!-- In Office present employees -->
    <div class="col-md-3">
      <div class="card p-3" onclick="get_ajax_data('Present', 0)" style="cursor: pointer;">
        <div class="d-flex align-items-center">
          <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-warning-4 mr-3"><i class="fa fa-user"></i> </span>
          <div>
            <h5 class="mb-1">
                <span class="ctt"> In Office ( Present ) </span> <br>
                <span id="in_office"> <?= isset($res->office_in) ? $res->office_in : 0; ?> </span>
            </h5>
          </div>
        </div>
      </div>
    </div>

    <!-- Out Office present employees -->
    <div class="col-md-3">
      <div class="card p-3" onclick="get_ajax_data('Present', 1)" style="cursor: pointer;">
        <div class="d-flex align-items-center">
          <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-secondary mr-3">
            <i class="fa fa-user"></i>
          </span>
          <div>
            <h5 class="mb-1">
                <span class="ctt"> Out Office ( Present ) </span> <br>
                <span id="out_office"> <?= isset($res->office_out) ? $res->office_out : 0; ?> </span>
            </h5>
          </div>
        </div>
      </div>
    </div>

    <!-- absent employees -->
    <div class="col-md-3">
      <div class="card p-3" onclick="get_ajax_data('Absent')" style="cursor: pointer;">
        <div class="d-flex align-items-center">
          <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-danger-4 mr-3">
            <i class="fa fa-calendar"></i>
          </span>

          <div>
            <h5 class="mb-1">
                <span class="ctt"> On Absent </span> <br>
                <span id="absent"> <?= isset($res->absent) ? $res->absent : 0; ?> </span>
            </h5>
          </div>
        </div>
      </div>
    </div>

    <!-- On Leave employees -->
    <div class="col-md-3">
      <div class="card p-3" onclick="get_ajax_data('Leave')" style="cursor: pointer;">
        <div class="d-flex align-items-center">
          <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-warning-4 mr-3">
            <i class="fa fa-user"></i>
          </span>
          <div>
            <h5 class="mb-1">
              <span class="ctt"> On Leave </span> <br>
              <span id="leaves"> <?= isset($res->leaves) ? $res->leaves : 0; ?> </span>
            </h5>
          </div>
        </div>
      </div>
    </div>

    <!-- Out Station Leave -->
    <div class="col-md-3">
      <div class="card p-3" onclick="get_ajax_data('sLeave')" style="cursor: pointer;">
        <div class="d-flex align-items-center">
          <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-success-4 mr-3">
            <i class="fa fa-calendar"></i>
          </span>

          <div>
            <h5 class="mb-1">
                <span class="ctt"> Out Station Leave </span> <br>
                <span id="out_station"> <?= isset($res->out_station) ? $res->out_station : 0; ?> </span>
            </h5>
          </div>
        </div>
      </div>
    </div>

    <!-- Late Office In -->
    <div class="col-md-3">
      <div class="card p-3" onclick="get_ajax_data('late_status')" style="cursor: pointer;">
        <div class="d-flex align-items-center">
          <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-danger-4 mr-3"> <i class="fa fa-lock"></i> </span>
          <div>
            <h5 class="mb-1">
                <span class="ctt"> Late Office In </span> <br>
                <span id="late_office"> <?= isset($res->late_status) ? $res->late_status : 0; ?> </span>
            </h5>
          </div>
        </div>
      </div>
    </div>

    <!-- Early Office Leave -->
    <div class="col-md-3">
      <div class="card p-3" onclick="get_ajax_data('early_status')" style="cursor: pointer;">
        <div class="d-flex align-items-center">
          <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-secondary-4 mr-3"> <i class="fa fa-lock"></i> </span>
          <div>
            <h5 class="mb-1">
                <span class="ctt"> Early Leave </span> <br>
                <span id="early_leave"> <?= isset($res->early_status) ? $res->early_status : 0; ?> </span>
            </h5>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Report List Section -->
  <?php $logs = $this->Dashboard_model->get_attn_logs(date('Y-m-d'), null); ?>
  <div class="box" style="box-shadow: 0 0px 4px 1px rgba(0, 0, 0, 0.2) !important;">
    <div class="box-header with-border">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>Sl.</th>
            <th>Name</th>
            <th>Designation</th>
            <th>Organization</th>
            <th>Date</th>
            <th>In Time</th>
            <th>Out Time</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id='show_row'>
          <?php foreach($logs as $k => $log) { ?>
          <tr>
            <td> <?= $k+1 ?> </td>
            <td><?= $log->first_name . ' ' . $log->last_name ?></td>
            <td> <?= $log->designation_name ?> </td>
            <td> <?= $log->name ?> </td>
            <td> <?= date('Y-m-d', strtotime($log->attendance_date)) ?> </td>
            <td> <?= $log->clock_in ? date('H:i:s', strtotime($log->clock_in)) : '--' ?> </td>
            <td> <?= $log->clock_out ? date('H:i:s', strtotime($log->clock_out)) : '--' ?> </td>
            <td> <?= $log->status ?> </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function get_ajax_data(att_type = null, type = null) {
      var unit_id = $('#unit_id').val();
      var date = $('#date').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('admin/dashboard/get_ajax_data'); ?>",
        data: {unit_id: unit_id, date: date, att_type: att_type, type: type},
        dataType: "json",
        success: function(res) {
          $('#reg_emp').html(res.rc.counts !== null && res.rc.counts !== '' ? res.rc.counts : 0);
          $('#in_office').html(res.rc.office_in !== null && res.rc.office_in !== '' ? res.rc.office_in : 0);
          $('#out_office').html(res.rc.office_out !== null && res.rc.office_out !== '' ? res.rc.office_out : 0);
          $('#absent').html(res.rc.absent !== null && res.rc.absent !== '' ? res.rc.absent : 0);
          $('#leaves').html(res.rc.leaves !== null && res.rc.leaves !== '' ? res.rc.leaves : 0);
          $('#out_station').html(res.rc.sLeave !== null && res.rc.sLeave !== '' ? res.rc.sLeave : 0);
          $('#late_office').html(res.rc.late_status !== null && res.rc.late_status !== '' ? res.rc.late_status : 0);
          $('#early_leave').html(res.rc.early_status !== null && res.rc.early_status !== '' ? res.rc.early_status : 0);
          recs = '';
          $('#show_row').empty()
          if (res.results.length) {
            $.each(res.results, function(k, v) {
              recs += '<tr>';
              recs += '<td>' + (k+1) + '</td>';
              recs += '<td>' + (v.first_name !== null ? v.first_name : '') + ' ' + (v.last_name !== null ? v.last_name : '') + '</td>';
              recs += '<td>' + v.designation_name + '</td>';
              recs += '<td>' + v.name + '</td>';
              recs += '<td>' + v.attendance_date + '</td>';
              recs += '<td>' + (v.clock_in ? moment(v.clock_in).format('h:mm:ss') : '--') + '</td>';
              recs += '<td>' + (v.clock_out ? moment(v.clock_out).format('h:mm:ss') : '--') + '</td>';
              recs += '<td>' + v.status + '</td>';
              recs += '</tr>';
            })
            $('#show_row').html(recs);
          } else {
            $('#show_row').html("<tr><td stylw='text-align: center; display: flex; flex-direction: column;' colspan='8'>Record Not Found</td></tr>");
          }
        }
      });
    }

  </script>


