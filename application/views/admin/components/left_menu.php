  <style>
    /* Custom styles for pill-shaped badge */
    .badge-pill {
      border-radius: 10px; /* Adjust the value for pill shape */
    }
    .badge-danger {
      background-color: #e42520; /* Bootstrap's default danger color */
      color: white; /* Text color for visibility */
    }
  </style>

<?php
$session = $this->session->userdata('username');
$theme = $this->Xin_model->read_theme_info(1);
// set layout / fixed or static
if($theme[0]->right_side_icons=='true') {
	$icons_right = 'expanded menu-icon-right';
} else {
	$icons_right = '';
}
if($theme[0]->bordered_menu=='true') {
	$menu_bordered = 'menu-bordered';
} else {
	$menu_bordered = '';
}
$user_info = $this->Xin_model->read_user_info($session['user_id']);
if($user_info[0]->is_active!=1) {
	redirect('admin/');
}
$role_user = $this->Xin_model->read_user_role_info($user_info[0]->user_role_id);
if(!is_null($role_user)){
	$role_resources_ids = explode(',',$role_user[0]->role_resources);
} else {
	$role_resources_ids = explode(',',0);
}
?>
<?php $system = $this->Xin_model->read_setting_info(1);?>
<?php $arr_mod = $this->Xin_model->select_module_class($this->router->fetch_class(),$this->router->fetch_method()); ?>
<?php
if($theme[0]->sub_menu_icons != ''){
	$submenuicon = $theme[0]->sub_menu_icons;
} else {
	$submenuicon = 'fa-circle-o';
}
?>
<?php  if($user_info[0]->profile_picture!='' && $user_info[0]->profile_picture!='no file') {?>
<?php $cpimg = base_url().'uploads/profile/'.$user_info[0]->profile_picture;?>
<?php } else {?>
<?php  if($user_info[0]->gender=='Male') { ?>
<?php 	$de_file = base_url().'uploads/profile/default_male.jpg';?>
<?php } else { ?>
<?php 	$de_file = base_url().'uploads/profile/default_female.jpg';?>
<?php } ?>
<?php $cpimg = $de_file;?>
<?php  } ?>
<section class="sidebar">
  <!-- Sidebar user panel -->
  <div class="user-panel">
    <div class="image text-center"><img src="<?php echo $cpimg;?>" class="img-circle" alt="<?php echo $user_info[0]->first_name. ' '.$user_info[0]->last_name;?>"> </div>
    <div class="info">
      <p style="color:white; font-size: 16px;"><?php echo $user_info[0]->first_name. ' '.$user_info[0]->last_name;?></p>
      <a href="<?php echo site_url('admin/profile');?>"><i class="fa fa-user"></i></a>
      <?php if(in_array('60',$role_resources_ids)) { ?>
      <a href="<?php echo site_url('admin/settings');?>"><i class="fa fa-gear"></i></a>
      <?php } ?>
      <a href="<?php echo site_url('admin/logout');?>"><i class="fa fa-power-off"></i></a> </div>
  </div>
  <?php
  $idocuments_expired = 0; $iimg_documents = 0;
  $icompany_license = 0; $iwarranty_assets = 0;
  if($user_info[0]->user_role_id==1){
	  $idocuments_expired = $this->Xin_model->count_get_documents_expired_all();
	  $iimg_documents = $this->Xin_model->count_get_img_documents_expired_all();
	  $icompany_license = $this->Xin_model->iicount_company_license_expired_all();
	  $iwarranty_assets = $this->Xin_model->count_warranty_assets_expired_all();
  } else {
	  $idocuments_expired = $this->Xin_model->count_get_user_documents_expired_all($session['user_id']);
	  $iimg_documents = $this->Xin_model->count_get_user_img_documents_expired_all($session['user_id']);
	  $icompany_license = $this->Xin_model->count_get_company_license_expired($session['user_id']);
	  if(in_array('265',$role_resources_ids)) {
			$iwarranty_assets = $this->Xin_model->count_company_warranty_assets_expired_all($user_info[0]->company_id);
		} else {
			$iwarranty_assets = $this->Xin_model->count_user_warranty_assets_expired_all($session['user_id']);
		}
  }
  $exp_count = $idocuments_expired + $iimg_documents + $icompany_license + $iwarranty_assets;
  $exp_count = 0;

  ?>
  <!-- sidebar menu: : style can be found in sidebar.less -->
  <ul class="sidebar-menu" data-widget="tree">
    <li class="<?php if(!empty($arr_mod['active']))echo $arr_mod['active'];?>"> <a href="<?php echo site_url('admin/dashboard');?>"> <i class="fa fa-dashboard"></i> <span><?php echo $this->lang->line('dashboard_title');?></span> </a> </li>


    <?php if(in_array('13',$role_resources_ids) || in_array('88',$role_resources_ids) || in_array('92',$role_resources_ids) || in_array('22',$role_resources_ids) || in_array('23',$role_resources_ids) || in_array('393',$role_resources_ids) || in_array('400',$role_resources_ids) || $user_info[0]->user_role_id==1){?>
      <li class="<?php if(!empty($arr_mod['stff_open']))echo $arr_mod['stff_open'];?> treeview"> <a href="#"> <i class="fa fa-user"></i> <span><?php echo $this->lang->line('let_staff');?></span> <span class="pull-right-container"> <?php if($exp_count > 0):?><span class="label label-danger pull-right"><?php echo $exp_count;?></span><?php endif;?> <i class="fa fa-angle-left pull-right"></i> </span> </a>
        <ul class="treeview-menu">
          <?php if(in_array('13',$role_resources_ids)) { ?>
          <li class="<?php if(!empty($arr_mod['emp_active']))echo $arr_mod['emp_active'];?>"><a href="<?php echo site_url('admin/employees');?>"><i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('dashboard_employees');?></a></li>
          <?php } ?>

          <?php if(in_array('400',$role_resources_ids)) { ?>
          <li class="<?php if(!empty($arr_mod['team_leads_active']))echo $arr_mod['team_leads_active'];?>"><a href="<?php echo site_url('admin/employees/set_team_leads');?>"><i class="fa <?php echo $submenuicon;?>"></i> Set Team Lead<span class="label label-danger pull-right"></span></a></li>
          <?php } ?>
        </ul>
      </li>
    <?php } ?>

    <!-- employees -->
    <?php  if( in_array('389',$role_resources_ids) || in_array('123',$role_resources_ids) || in_array('124',$role_resources_ids) || in_array('130',$role_resources_ids) ) {?>
      <li class="<?php if(!empty($arr_mod['attnd_open']))echo $arr_mod['attnd_open'];?> treeview"> <a href="#"> <i class="fa fa-users"></i> <span> My </span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
        <ul class="treeview-menu">
          <?php if(in_array('389',$role_resources_ids)) { ?>
            <li class="sidenav-link <?php if(!empty($arr_mod['emp_atten_active']))echo $arr_mod['emp_atten_active'];?>"> <a href="<?php echo site_url('admin/attendance/employee_attendance');?>"><i class="fa <?php echo $submenuicon;?>"></i>Attendance</a> </li>
          <?php } ?>

          <?php if(in_array('123',$role_resources_ids)) { ?>
            <li class="sidenav-link <?php if(!empty($arr_mod['emp_move_active']))echo $arr_mod['emp_move_active'];?>"> <a href="<?php echo site_url("admin/attendance/employee_movement");?>"><i class="fa <?php echo $submenuicon;?>"></i>Movement</a></li>
          <?php } ?>

          <?php if(in_array('124',$role_resources_ids)) { ?>
            <li class="sidenav-link <?php if(!empty($arr_mod['emp_leave']))echo $arr_mod['emp_leave'];?>"> <a href="<?php echo site_url("admin/leave/emp_leave");?>"><i class="fa <?php echo $submenuicon;?>"></i>Leave</a></li>

            <li class="sidenav-link <?php if(!empty($arr_mod['emp_leaveo']))echo $arr_mod['emp_leaveo'];?>"> <a href="<?php echo site_url("admin/leave/emp_outstaton_leave");?>"><i class="fa <?php echo $submenuicon;?>"></i>Out Station Leave</a></li>
          <?php } ?>

          <li class="sidenav-link <?php if(!empty($arr_mod['os_leaveo']))echo $arr_mod['os_leaveo'];?>"> <a href="<?php echo site_url("admin/leave/approve_os_leave");?>"><i class="fa <?php echo $submenuicon;?>"></i>Approve Station Leave</a></li>

          <?php if(in_array('130',$role_resources_ids)) { ?>
            <li class="sidenav-link <?php if(!empty($arr_mod['emp_holyday']))echo $arr_mod['emp_holyday'];?>"> <a href="<?php echo site_url("admin/leave/emp_holyday");?>"><i class="fa <?php echo $submenuicon;?>"></i>Holiday</a></li>
          <?php } ?>
        </ul>
      </li>
    <?php } ?>
    <!-- employee -->

    <!-- Hr -->
    <?php  if(in_array('1002',$role_resources_ids) || in_array('1003',$role_resources_ids) || in_array('28',$role_resources_ids) || in_array('8',$role_resources_ids) || in_array('46',$role_resources_ids) ) {?>
      <li class="<?php if(!empty($arr_mod['attnd_open']))echo $arr_mod['attnd_open'];?> treeview"> <a href="#"> <i class="fa fa-users"></i> <span> HR </span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
        <ul class="treeview-menu">

          <?php if(in_array('1002',$role_resources_ids)) { ?>
            <li class="sidenav-link <?php if(!empty($arr_mod['attnp_active']))echo $arr_mod['attnp_active'];?>"> <a href="<?php echo site_url('admin/attendance/');?>"> <i class="fa <?php echo $submenuicon;?>"></i> attendance process</a> </li>
          <?php } ?>

          <?php if(in_array('1003',$role_resources_ids)) { ?>
            <li class="sidenav-link <?php if(!empty($arr_mod['move_active']))echo $arr_mod['move_active'];?>"> <a href="<?php echo site_url('admin/attendance/move_register');?>"> <i class="fa <?php echo $submenuicon;?>"></i> movement register</a> </li>
          <?php } ?>

          <?php if(in_array('28',$role_resources_ids)) { ?>
            <li class="sidenav-link <?php if(!empty($arr_mod['attnd_active']))echo $arr_mod['attnd_active'];?>"> <a href="<?php echo site_url('admin/timesheet/attendance');?>"> <i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('left_attendance');?> </a> </li>
          <?php } ?>

          <?php if(in_array('8',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['hol_active']))echo $arr_mod['hol_active'];?>"> <a href="<?php echo site_url('admin/timesheet/holidays');?>"> <i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('xin_manage_holidays');?> </a> </li>
          <?php } ?>

          <?php if(in_array('46',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['leave_active']))echo $arr_mod['leave_active'];?>"> <a href="<?php echo site_url('admin/timesheet/leave');?>"> <i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('xin_manage_leaves');?> </a> </li>
          <?php } ?>

        </ul>
      </li>
    <?php } ?>
    <!-- Hr -->

    <!-- Store -->
    <?php  if(in_array('1030',$role_resources_ids) || in_array('1031',$role_resources_ids) || in_array('1033',$role_resources_ids) || in_array('1070',$role_resources_ids) || in_array('1071',$role_resources_ids) || in_array('1072',$role_resources_ids) || in_array('1073',$role_resources_ids) || in_array('1074',$role_resources_ids) || in_array('1075',$role_resources_ids) || in_array('1076',$role_resources_ids) || in_array('1080',$role_resources_ids) || in_array('1081',$role_resources_ids) || in_array('1082',$role_resources_ids) || in_array('1083',$role_resources_ids) || in_array('1084',$role_resources_ids) || in_array('1085',$role_resources_ids) || in_array('1041',$role_resources_ids)) {?>
      <li class="<?php if(!empty($arr_mod['invtry_open']))echo $arr_mod['invtry_open'];?> treeview"> <a href="#"> <i class="fa fa-cart-arrow-down"></i> Store <span class="badge badge-pill " style="margin-top: 0px;margin-left: 56px; background:#0bbd22"><?php  $a = $this->db->select('COUNT(id) as id')->where_in('status', array(1, 2))->get('products_requisition_details')->row()->id ;  $b = $this->db->select('COUNT(id) as id')->where_in('status', array(1, 2))->get('products_purches_details')->row()->id ; $session = $this->session->userdata('username');  echo $session['role_id'] ==3 ? '' : ($session['role_id'] ==4 ? $a : ($a + $b))  ;?></span> <span class="pull-right-container">  <i class="fa fa-angle-left pull-right"></i> </span> </a>
        <ul class="treeview-menu">
          <?php if(in_array('1031',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['my_requi_active']))echo $arr_mod['my_requi_active'];?>"> <a href="<?php echo site_url('admin/inventory/index');?>"> <i class="fa < ?php echo $submenuicon;?>"></i> Requisition </a> </li>
          <?php } ?>

          <!-- requisition part -->
        <?php if(in_array('1070',$role_resources_ids) || in_array('1070',$role_resources_ids) || in_array('1071',$role_resources_ids) || in_array('1072',$role_resources_ids) || in_array('1073',$role_resources_ids) || in_array('1074',$role_resources_ids) || in_array('1075',$role_resources_ids) || in_array('1076',$role_resources_ids)) { ?>
          <li class="<?php if(!empty($arr_mod['requi_active']))echo $arr_mod['requi_active'];?> treeview"> <a href="#"><i class="fa fa-user-plus"></i> Requisition
          <span class="badge badge-pill badge-info" style="margin-top: 0px;margin-left: 30px;"><?php echo $a = $this->db->select('COUNT(id) as id')->where_in('status', array(1, 2))->get('products_requisition_details')->row()->id ; ?></span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
            <ul class="treeview-menu">
              <?php if(in_array('1072',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['iqlist_open']))echo $arr_mod['iqlist_open'];?>"> <a href="<?php echo site_url('admin/inventory/index');?>"> <i class="fa fa-list-ul"></i> Requisition List </a> </li>
              <?php } ?>

              <?php if(in_array('1071',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['iqcreate_open']))echo $arr_mod['iqcreate_open'];?>"> <a href="<?php echo site_url('admin/inventory/create');?>"> <i class="fa fa-plus-circle" style="color:green"></i> Create Requisition </a> </li>
              <?php } ?>

              <?php if(in_array('1033',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['iqpending_open']))echo $arr_mod['iqpending_open'];?>"> <a href="<?php echo site_url('admin/inventory/pending_list');?>"> <i class="fa fa-retweet" style="color:red"></i> Pending List <span class="badge badge-pill badge-danger" style="margin-top: 0px;margin-left: 30px;"><?php echo $a = $this->db->select('COUNT(id) as id')->where('status', 1)->get('products_requisition_details')->row()->id ; ?></span></a> </li>
              <?php } ?>

              <?php if(in_array('1074',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['iqapprove_open']))echo $arr_mod['iqapprove_open'];?>"> <a href="<?php echo site_url('admin/inventory/aproved_list');?>"> <i class="fa fa-check-circle" style="color:green"></i> Approved List <span class="badge badge-pill" style="margin-top: 0px;margin-left: 19px;background:green"><?php echo $a = $this->db->select('COUNT(id) as id')->where('status',2)->get('products_requisition_details')->row()->id ; ?></span></a> </li>
              <?php } ?>
              <?php if(in_array('1075',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['iqdelivery_open']))echo $arr_mod['iqdelivery_open'];?>"> <a href="<?php echo site_url('admin/inventory/delivery_list');?>"> <i class="fa fa-truck" style="color:#1e8c98"></i> Delivered List</a> </li>
              <?php } ?>
              <?php if(in_array('1076',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['reject_open']))echo $arr_mod['reject_open'];?>"> <a href="<?php echo site_url('admin/inventory/reject_list');?>"> <i class="fa fa-ban" style="color:red"></i> Rejected List</a> </li>
              <?php } ?>
            </ul>
          </li>
        <?php } ?>

          <!-- // purchase -->
          <?php if(in_array('1080',$role_resources_ids) || in_array('1081',$role_resources_ids) || in_array('1082',$role_resources_ids) || in_array('1083',$role_resources_ids) || in_array('1084',$role_resources_ids) || in_array('1085',$role_resources_ids)) { ?>

          <li class="<?php if(!empty($arr_mod['puiqu_active']))echo $arr_mod['puiqu_active'];?> treeview"> <a href="#"><i class="fa fa-shopping-bag" style="color:#1e8c98"></i> Purchase <span class="badge badge-pill badge-danger" style="margin-top: 0px;margin-left: 40px;"><?php $session = $this->session->userdata('username'); $a = $this->db->select('COUNT(id) as id')->where_in('status', array(1,2))->get('products_purches_details')->row()->id ; echo $session['role_id'] == 4 ? '' : $a;  ?></span><span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
            <ul class="treeview-menu">

              <?php if(in_array('1081',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['puscreate_open']))echo $arr_mod['puscreate_open'];?>"> <a href="<?php echo site_url('admin/inventory/purchase');?>"> <i class="fa fa-list"></i> Purchase List </a> </li>
              <?php } ?>

              <?php if(in_array('1082',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['puspending_open']))echo $arr_mod['puspending_open'];?>"> <a href="<?php echo site_url('admin/inventory/purchase_panding_list');?>"> <i class="fa fa-retweet" style="color:red"></i> Pending List <span class="badge badge-pill badge-danger" style="margin-top: 0px;margin-left: 30px;"><?php $c = $this->db->select('COUNT(id) as id')->where('status',1)->get('products_purches_details')->row()->id ; echo $session['role_id'] == 4 ? '' : $c;  ?></span></a> </li>
              <?php } ?>

              <?php if(in_array('1083',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['pusaproved_open']))echo $arr_mod['pusaproved_open'];?>"> <a href="<?php echo site_url('admin/inventory/purchase_aproved_list');?>"> <i class="fa fa-check-circle" style="color:green"></i> Approved List <span class="badge badge-pill" style="margin-top: 0px;margin-left: 19px;background:green"><?php  $b = $this->db->select('COUNT(id) as id')->where('status', 2)->get('products_purches_details')->row()->id ; echo $session['role_id'] == 4 ? '' : $b;  ?></span></a> </li>
              <?php } ?>

              <?php if(in_array('1084',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['pusreceived_open']))echo $arr_mod['pusreceived_open'];?>"> <a href="<?php echo site_url('admin/inventory/purchase_order_received_list');?>"> <i class="fa fa-handshake-o" style="color:green"></i> Order Received List</a> </li>
              <?php } ?>

              <?php if(in_array('1085',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['pusreject_open']))echo $arr_mod['pusreject_open'];?>"> <a href="<?php echo site_url('admin/inventory/purchase_reject_list');?>"> <i class="fa fa-ban" style="color:red"></i> Rejected List</a> </li>
              <?php } ?>
            </ul>
          </li>
          <?php } ?>

          <?php if(in_array('1042',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['product_open']))echo $arr_mod['product_open'];?>"> <a href="<?php echo site_url('admin/inventory/products');?>"> <i class="fa fa-product-hunt" style="color:green"></i> Product </a> </li>
          <?php } ?>


          <?php if(in_array('1048',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['move_open']))echo $arr_mod['move_open'];?>"> <a href="<?php echo site_url('admin/inventory/moves');?>"> <i class="fa fa-tablet" style="color:green"></i> Device Movements </a> </li>
          <?php } ?>

          <?php if(in_array('1047',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['low_product_list']))echo $arr_mod['low_product_list'];?>"> <a href="<?php echo site_url('admin/inventory/low_product_list');?>"> <i class="fa fa-sort-amount-asc" style="color:#e4d802"></i> Low Product List </a> </li>
          <?php } ?>

          <?php if(in_array('1033',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['inreport_active']))echo $arr_mod['inreport_active'];?>"> <a href="<?php echo site_url('admin/inventory/report');?>"> <i class="fa fa-line-chart" style="color:seagreen"></i> Report </a> </li>
          <?php } ?>

          <?php if(in_array('1041',$role_resources_ids)) { ?>
          <li class="<?php if(!empty($arr_mod['insetting_open']))echo $arr_mod['insetting_open'];?> treeview"> <a href="#"><i class="fa fa-circle-o"></i> Settings <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
            <ul class="treeview-menu">
              <?php if($user_info[0]->user_role_id != 3) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['insetting_open']))echo $arr_mod['insetting_open'];?>"> <a href="<?php echo site_url('admin/inventory/daily_pkg');?>"> <i class="fa <?php echo $submenuicon;?>"></i> Requisition Package </a> </li>
              <?php } ?>

              <?php if(in_array('1046',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['supplier_open']))echo $arr_mod['supplier_open'];?>"> <a href="<?php echo site_url('admin/inventory/supplier');?>"> <i class="fa <?php echo $submenuicon;?>"></i> Supplier </a> </li>
              <?php } ?>
              <?php if(in_array('1043',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['unit_open']))echo $arr_mod['unit_open'];?>"> <a href="<?php echo site_url('admin/inventory/unit');?>"> <i class="fa <?php echo $submenuicon;?>"></i> Unit </a> </li>
              <?php } ?>
              <?php if(in_array('1044',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['cat_open']))echo $arr_mod['cat_open'];?>"> <a href="<?php echo site_url('admin/inventory/category');?>"> <i class="fa <?php echo $submenuicon;?>"></i> Category </a> </li>
              <?php } ?>
              <?php if(in_array('1045',$role_resources_ids)) { ?>
              <li class="sidenav-link <?php if(!empty($arr_mod['subcat_open']))echo $arr_mod['subcat_open'];?>"> <a href="<?php echo site_url('admin/inventory/sub_category');?>"> <i class="fa <?php echo $submenuicon;?>"></i> Sub Category </a> </li>
              <?php } ?>
            </ul>
          </li>
          <?php } ?>

        </ul>
      </li>
    <?php } ?>
    <!-- Store -->

    <!-- hr reports -->
    <?php  if(in_array('111',$role_resources_ids) || in_array('112',$role_resources_ids) || in_array('113',$role_resources_ids) || in_array('114',$role_resources_ids) || in_array('115',$role_resources_ids) || in_array('116',$role_resources_ids) || in_array('117',$role_resources_ids)) {?>
      <li class="<?php if(!empty($arr_mod['reports_open']))echo $arr_mod['reports_open'];?> treeview"> <a href="#"> <i class="fa fa-bar-chart"></i> <span><?php echo $this->lang->line('xin_hr_report_title');?></span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
        <ul class="treeview-menu">

          <?php if(in_array('117',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['reports_employees_active']))echo $arr_mod['reports_employees_active'];?>"> <a href="<?php echo site_url('admin/reports/employees');?>"> <i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('xin_hr_report_employees');?> </a> </li>
          <?php } ?>

          <?php if(in_array('116',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['reports_roles_active']))echo $arr_mod['reports_roles_active'];?>"> <a href="<?php echo site_url('admin/reports/late_report');?>"> <i class="fa <?php echo $submenuicon;?>"></i>Employee Late Reort</a> </li>
          <?php } ?>

          <?php //if($system[0]->module_projects_tasks=='true'){?>
          <?php if(in_array('114',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['reports_store_active']))echo $arr_mod['reports_store_active'];?>"> <a href="<?php echo site_url('admin/reports/store_report');?>"> <i class="fa <?php echo $submenuicon;?>"></i>Store In Out Report</a> </li>
          <?php } ?>

          <?php if(in_array('1141',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['reports_projects_active']))echo $arr_mod['reports_projects_active'];?>"> <a href="<?php echo site_url('admin/reports/inventory');?>"> <i class="fa <?php echo $submenuicon;?>"></i>Inventory</a> </li>
          <?php } ?>
          <?php //} ?>

          <?php if(in_array('115',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['reports_leave_active']))echo $arr_mod['reports_leave_active'];?>"> <a href="<?php echo site_url('admin/reports/employee_leave_report');?>"> <i class="fa <?php echo $submenuicon;?>"></i>Leave</a> </li>
          <?php } ?>

          <?php if(in_array('409333333',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['reports_lunch_active']))echo $arr_mod['reports_lunch_active'];?>"> <a href="<?php echo site_url('admin/reports/lunch_report_all');?>"> <i class="fa <?php echo $submenuicon;?>"></i>Lunch</a> </li>
          <?php } ?>

          <?php if(in_array('419',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['reports_accounts_active']))echo $arr_mod['reports_accounts_active'];?>"> <a href="<?php echo site_url('admin/reports/accounts_report');?>"> <i class="fa <?php echo $submenuicon;?>"></i>Accounts</a> </li>
          <?php } ?>

           <?php if(in_array('420',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['reports_emp_issue_active']))echo $arr_mod['reports_emp_issue_active'];?>"> <a href="<?php echo site_url('admin/reports/issue_report');?>"> <i class="fa <?php echo $submenuicon;?>"></i>Employee Issue</a> </li>
          <?php } ?>
        </ul>
      </li>
    <?php } ?>

    <!-- setup -->
    <?php  if(in_array('2',$role_resources_ids) || in_array('3',$role_resources_ids) || in_array('5',$role_resources_ids) || in_array('6',$role_resources_ids) || in_array('4',$role_resources_ids) || in_array('11',$role_resources_ids) || in_array('9',$role_resources_ids) || in_array('96',$role_resources_ids)){?>
      <li class="<?php if(!empty($arr_mod['adm_open']))echo $arr_mod['adm_open'];?> treeview"> <a href="#"> <i class="fa fa-building"></i> <span>Setup</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
        <ul class="treeview-menu">
          <?php if(in_array('5',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['com_active']))echo $arr_mod['com_active'];?>"><a href="<?php echo site_url('admin/company')?>"><i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('left_company');?></a></li>
          <?php } ?>

          <?php if(in_array('3',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['dep_active']))echo $arr_mod['dep_active'];?>"><a href="<?php echo site_url('admin/department');?>"><i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('left_department');?></a></li>
          <?php } ?>

          <?php if(in_array('4',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['des_active']))echo $arr_mod['des_active'];?>"><a href="<?php echo site_url('admin/designation');?>"><i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('left_designation');?></a></li>
          <?php } ?>

          <?php if(in_array('96',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['sch_active']))echo $arr_mod['sch_active'];?>"><a href="<?php echo site_url('admin/schedules');?>"><i class="fa <?php echo $submenuicon;?>"></i>Schedule</a></li>

          <li class="sidenav-link <?php if(!empty($arr_mod['mch_active']))echo $arr_mod['mch_active'];?>"><a href="<?php echo site_url('admin/schedules/shift_manage');?>"><i class="fa <?php echo $submenuicon;?>"></i>Manage Shift</a></li>
          <?php } ?>

          <li class="sidenav-link <?php if(!empty($arr_mod['lts_active']))echo $arr_mod['lts_active'];?>"><a href="<?php echo site_url('admin/schedules/leave_type');?>"><i class="fa <?php echo $submenuicon;?>"></i>Leave Setup</a></li>

          <li class="sidenav-link <?php if(!empty($arr_mod['lss_active']))echo $arr_mod['lss_active'];?>"><a href="<?php echo site_url('admin/schedules/leave_setting');?>"><i class="fa <?php echo $submenuicon;?>"></i>Leave Setting</a></li>

          <?php if(in_array('11',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['ann_active']))echo $arr_mod['ann_active'];?>"><a href="<?php echo site_url('admin/announcement');?>"><i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('left_announcements');?></a></li>
          <?php } ?>
          <?php if(in_array('9',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['pol_active']))echo $arr_mod['pol_active'];?>"><a href="<?php echo site_url('admin/policy');?>"><i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('left_policies');?></a></li>
          <?php } ?>
        </ul>
      </li>
    <?php } ?>
    <!-- setup -->

    <!-- system -->
    <?php  if(in_array('57',$role_resources_ids) || in_array('60',$role_resources_ids) || in_array('61',$role_resources_ids) || in_array('61',$role_resources_ids) || in_array('62',$role_resources_ids) || in_array('63',$role_resources_ids) || in_array('89',$role_resources_ids) || in_array('93',$role_resources_ids)) {?>
      <li class="<?php if(!empty($arr_mod['system_open']))echo $arr_mod['system_open'];?> treeview"> <a href="#"> <i class="fa fa-cog"></i> <span><?php echo $this->lang->line('xin_system');?></span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span> </a>
        <ul class="treeview-menu">

          <?php if($user_info[0]->user_role_id==1 || $user_info[0]->user_role_id==2) { ?>
            <li class="<?php if(!empty($arr_mod['roles_active']))echo $arr_mod['roles_active'];?>"><a href="<?php echo site_url('admin/attendance/moveplace');?>"><i class="fa <?php echo $submenuicon;?>"></i>Move Location</a></li>
          <?php } ?>

          <?php if($user_info[0]->user_role_id==1) { ?>
            <li class="<?php if(!empty($arr_mod['roles_active']))echo $arr_mod['roles_active'];?>"><a href="<?php echo site_url('admin/roles');?>"><i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('xin_role_urole');?></a></li>
          <?php } ?>

          <?php if($system[0]->module_language=='true'){?>
          <?php if(in_array('89',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['languages_active']))echo $arr_mod['languages_active'];?>"> <a href="<?php echo site_url('admin/languages');?>"> <i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('xin_multi_language');?> </a> </li>
          <?php } ?>
          <?php } ?>
          <?php if(in_array('60',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['settings_active']))echo $arr_mod['settings_active'];?>"> <a href="<?php echo site_url('admin/settings');?>"> <i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('left_settings');?> </a> </li>
          <?php } ?>

          <?php if(in_array('62',$role_resources_ids)) { ?>
          <li class="sidenav-link <?php if(!empty($arr_mod['db_active']))echo $arr_mod['db_active'];?>"> <a href="<?php echo site_url('admin/settings/database_backup');?>"> <i class="fa <?php echo $submenuicon;?>"></i> <?php echo $this->lang->line('left_db_backup');?> </a> </li>
          <?php } ?>
        </ul>
      </li>
    <?php } ?>
    <li> &nbsp; </li>
  </ul>
</section>
