<?php
	$session = $this->session->userdata('username');
	$user_info = $this->Xin_model->read_user_info($session['user_id']);
	$theme = $this->Xin_model->read_theme_info(1);
?>
<?php
	if(in_array($user_info[0]->user_role_id, array(1,2,4,5,6))):
		$this->load->view('admin/dashboard/administrator_dashboard');
	else:
		$this->load->view('admin/dashboard/employee_dashboard');
	endif;
?>











