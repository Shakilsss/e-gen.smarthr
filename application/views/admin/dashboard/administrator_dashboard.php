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
            <select required class="form-control" name="unit_id" id="unit_id" >
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
            <input required class="form-control date" id="date" name="date" >
        </div>
    </div>
  </div>

  <div class="clearfix"></div>

  <!-- Card Section -->
  <div class="row" style="box-shadow: 0 0px 2px 1px rgba(0, 0, 0, 0.2) !important;">

    <!-- total employees -->
    <div class="col-md-4">
      <div class="card p-3">
        <a href="<?php echo site_url('admin/employees/increment_pro_list'); ?>">
          <div class="d-flex align-items-center">
            <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-success-4 mr-3"> <i class="fa fa-lock"></i> </span>
            <div>
              <h5 class="mb-1">
                <b>
                  <span style="color: #31ce36 !important;"> Total Employees ( Regular ) </span> <br>
                  <?php echo $this->Employees_model->get_total_employees(); ?>
                </b>
              </h5>
            </div>
          </div>
        </a>
      </div>
    </div>

    <!-- present employees -->
    <div class="col-md-4">
      <div class="card p-3">
        <a href="<?php echo site_url('admin/employees'); ?>">
          <div class="d-flex align-items-center">
            <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-secondary mr-3">
              <i class="fa fa-user"></i>
            </span>
            <div>
              <h5 class="mb-1">
                <b>
                  <small> In Office ( Present ) </small> <br>
                  <?php echo $this->Employees_model->get_total_employees(); ?>
                </b>
              </h5>
            </div>
          </div>
        </a>
      </div>
    </div>

    <!-- absent employees -->
    <div class="col-md-4">
      <div class="card p-3">
        <a href="<?php echo site_url('admin/timesheet/leave'); ?>">
          <div class="d-flex align-items-center">
            <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-danger-4 mr-3">
              <i class="fa fa-calendar"></i>
            </span>

            <div>
              <h5 class="mb-1">
                <b>
                  <small> Total Absent Employees </small> <br>
                  <?php echo $this->Employees_model->get_total_employees(); ?>
                </b>
              </h5>
            </div>
          </div>
        </a>
      </div>
    </div>

    <!-- Out of office employees -->
    <div class="col-md-4">
      <div class="card p-3">
        <a href="<?php echo site_url('admin/timesheet/leave'); ?>">
          <div class="d-flex align-items-center">
            <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-danger-4 mr-3">
              <i class="fa fa-calendar"></i>
            </span>

            <div>
              <h5 class="mb-1">
                <b>
                  <small> Out Off office ( Present ) </small> <br>
                  <?php echo $this->Employees_model->get_total_employees(); ?>
                </b>
              </h5>
            </div>
          </div>
        </a>
      </div>
    </div>

    <!-- Out Station Leave -->
    <div class="col-md-4">
      <div class="card p-3">
        <a href="<?php echo site_url('admin/employees/increment_pro_list'); ?>">
          <div class="d-flex align-items-center">
            <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-success-4 mr-3"> <i class="fa fa-lock"></i> </span>
            <div>
              <h5 class="mb-1">
                <b>
                  <span style="color: #31ce36 !important;"> Out Station Leave </span> <br>
                  <?php echo $this->Employees_model->get_total_employees(); ?>
                </b>
              </h5>
            </div>
          </div>
        </a>
      </div>
    </div>

    <!-- On Leave employees -->
    <div class="col-md-4">
      <div class="card p-3">
        <a href="<?php echo site_url('admin/employees'); ?>">
          <div class="d-flex align-items-center">
            <span class="stamp-hrsale-4 stamp-hrsale-md bg-hrsale-secondary mr-3">
              <i class="fa fa-user"></i>
            </span>
            <div>
              <h5 class="mb-1">
                <b>
                  <small> On Leave Employees </small> <br>
                  <?php echo $this->Employees_model->get_total_employees(); ?>
                </b>
              </h5>
            </div>
          </div>
        </a>
      </div>
    </div>

  </div>

  <!-- Report List Section -->
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
        <tr>
          <td>1</td>
          <td>John Doe</td>
          <td>Web Designer</td>
          <td>ogf</td>
          <td>2021-01-01</td>
          <td>10:00 AM</td>
          <td>6:00 PM</td>
          <td>Present</td>
        </tr>
        <tr>
          <td>1</td>
          <td>John Doe</td>
          <td>Web Designer</td>
          <td>organi</td>
          <td>2021-01-01</td>
          <td>10:00 AM</td>
          <td>6:00 PM</td>
          <td>Present</td>
        </tr>
        <tr>
          <td>1</td>
          <td>John Doe</td>
          <td>Web Designer</td>
          <td></td>
          <td>2021-01-01</td>
          <td>10:00 AM</td>
          <td>6:00 PM</td>
          <td>Present</td>
        </tr>
        <tr>
          <td>1</td>
          <td>John Doe</td>
          <td>Web Designer</td>
          <td></td>
          <td>2021-01-01</td>
          <td>10:00 AM</td>
          <td>6:00 PM</td>
          <td>Present</td>
        </tr>
      </table>
    </div>
  </div>


