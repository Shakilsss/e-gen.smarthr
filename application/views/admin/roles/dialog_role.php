<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if(isset($_GET['jd']) && isset($_GET['role_id']) && $_GET['data']=='role'){
$role_resources_ids = explode(',',$role_resources); ?>

<div class="modal-header">
<?php echo form_button(array('aria-label' => 'Close', 'data-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>
<h4 class="modal-title" id="edit-modal-data"><?php echo $this->lang->line('xin_role_editrole');?></h4>
</div>
<?php $attributes = array('name' => 'edit_role', 'id' => 'edit_role', 'autocomplete' => 'off','class' => '"m-b-1');?>
<?php $hidden = array('_method' => 'EDIT', 'ext_name' => $role_name, '_token' => $role_id);?>
<?php echo form_open('admin/roles/update/'.$role_id, $attributes, $hidden);?>
	<div class="modal-body">
		<div class="row">
		<div class="col-md-4">
			<div class="row">
			<div class="col-md-12">
				<div class="form-group">
				<label for="role_name"><?php echo $this->lang->line('xin_role_name');?><i class="hrsale-asterisk">*</i></label>
				<input class="form-control" placeholder="<?php echo $this->lang->line('xin_role_name');?>" name="role_name" type="text" value="<?php echo $role_name;?>">
				</div>
			</div>
			</div>
			<div class="row">
				<input type="checkbox" name="role_resources[]" value="0" checked style="display:none;"/>
				<div class="col-md-12">
					<div class="form-group">
					<label for="role_access"><?php echo $this->lang->line('xin_role_access');?><i class="hrsale-asterisk">*</i></label>
					<select class="form-control custom-select" id="role_access_modal" name="role_access" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('xin_role_access');?>">
						<option value="">&nbsp;</option>
						<option value="1" <?php if($role_access==1):?> selected="selected" <?php endif;?>><?php echo $this->lang->line('xin_role_all_menu');?></option>
						<option value="2" <?php if($role_access==2):?> selected="selected" <?php endif;?>><?php echo $this->lang->line('xin_role_cmenu');?></option>
					</select>
					</div>
				</div>
			</div>
			<div class="row">
			<div class="col-md-12">
				<p><strong><?php echo $this->lang->line('xin_role_note_title');?></strong></p>
				<p><?php echo $this->lang->line('xin_role_note1');?></p>
				<p><?php echo $this->lang->line('xin_role_note2');?></p>
			</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="row">
			<div class="col-md-12">
				<div class="form-group">
				<label for="resources"><?php echo $this->lang->line('xin_role_resource');?></label>
				<div id="all_resources">
					<div class="demo-section k-content">
					<div>
						<div id="treeview_m1"></div>
					</div>
					</div>
				</div>
				</div>
			</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="row">
			<div class="col-md-12">
				<div class="form-group">
				<div id="all_resources">
					<div class="demo-section k-content">
					<div>
						<div id="treeview_m2"></div>
					</div>
					</div>
				</div>
				</div>
			</div>
			</div>
		</div>
		</div>
	</div>

	<div class="modal-footer">
		<?php echo form_button(array('data-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => '<i class="fa fa fa-check-square-o"></i> '.$this->lang->line('xin_close'))); ?> <?php echo form_button(array('name' => 'hrsale_form', 'type' => 'submit', 'class' => $this->Xin_model->form_button_class(), 'content' => '<i class="fa fa fa-check-square-o"></i> '.$this->lang->line('xin_update'))); ?>
	</div>
<?php echo form_close(); ?>

<script type="text/javascript">
 	$(document).ready(function(){
		$('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
		$('[data-plugin="select_hrm"]').select2({ width:'100%' });
		$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
			checkboxClass: 'icheckbox_minimal-blue',
			radioClass   : 'iradio_minimal-blue'
		});

		/* Edit data */
		$("#edit_role").submit(function(e){
			e.preventDefault();
			var obj = $(this), action = obj.attr('name');
			$('.save').prop('disabled', true);

			$.ajax({
				type: "POST",
				url: e.target.action,
				data: obj.serialize()+"&is_ajax=1&edit_type=role&form="+action,
				cache: false,
				success: function (JSON) {
					if (JSON.error != '') {
						toastr.error(JSON.error);
						$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
						$('.save').prop('disabled', false);
					} else {
						// On page load: datatable
						var xin_table = $('#xin_table').dataTable({
							"bDestroy": true,
							"ajax": {
								url : "<?php echo site_url("admin/roles/role_list") ?>",
								type : 'GET'
							},
							dom: 'lBfrtip',
							"buttons": ['csv', 'excel', 'pdf', 'print'], // colvis > if needed
							"fnDrawCallback": function(settings){
							$('[data-toggle="tooltip"]').tooltip();
							}
						});
						xin_table.api().ajax.reload(function(){
							toastr.success(JSON.result);
						}, true);
						$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
						$('.edit-modal-data').modal('toggle');
						$('.save').prop('disabled', false);
					}
				}
			});
		});
	});
</script>

<script>

jQuery("#treeview_m1").kendoTreeView({
	checkboxes: {
	checkChildren: true,
	template: "<label><input type='checkbox' #= item.check# class='#= item.class #' name='role_resources[]' value='#= item.value #'> #= item.text #</label>"
	},
	check: onCheck,
	dataSource: [

		{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('let_staff');?>",  add_info: "", check: "<?php if(isset($_GET['role_id'])) { if(in_array('103',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", value: "103",  items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('dashboard_employees');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "13", check: "<?php if(isset($_GET['role_id'])) { if(in_array('13',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "13", check: "<?php if(isset($_GET['role_id'])) { if(in_array('13',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "201", check: "<?php if(isset($_GET['role_id'])) { if(in_array('201',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

				{ id: "", class: "role-checkbox-modal", text: "Employee List",  add_info: "Employee List", value: "377", check: "<?php if(isset($_GET['role_id'])) { if(in_array('377',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_edit');?>", value: "202", check: "<?php if(isset($_GET['role_id'])) { if(in_array('202',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_delete');?>", value: "203", check: "<?php if(isset($_GET['role_id'])) { if(in_array('203',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_view_company_emp_title');?>",  add_info: "<?php echo $this->lang->line('xin_view_company_emp_title');?>", value: "372", check: "<?php if(isset($_GET['role_id'])) { if(in_array('372',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_view_location_emp_title');?>",  add_info: "<?php echo $this->lang->line('xin_view_location_emp_title');?>", value: "373", check: "<?php if(isset($_GET['role_id'])) { if(in_array('373',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
				]},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_hrsale_custom_fields');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "393",  items: [
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "393",check: "<?php if(isset($_GET['role_id'])) { if(in_array('393',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "394",check: "<?php if(isset($_GET['role_id'])) { if(in_array('394',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_edit');?>", value: "395",check: "<?php if(isset($_GET['role_id'])) { if(in_array('395',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_delete');?>", value: "396",check: "<?php if(isset($_GET['role_id'])) { if(in_array('396',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
				]},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_set_employees_salary');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "351", check: "<?php if(isset($_GET['role_id'])) { if(in_array('351',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_import_employees');?>",  add_info: "<?php echo $this->lang->line('xin_import_employees');?>", value: "92", check: "<?php if(isset($_GET['role_id'])) { if(in_array('92',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_employees_directory');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "88", check: "<?php if(isset($_GET['role_id'])) { if(in_array('88',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_employees_exit');?>",  add_info: "<?php echo $this->lang->line('xin_view_update');?>", value: "23", check: "<?php if(isset($_GET['role_id'])) { if(in_array('23',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "23", check: "<?php if(isset($_GET['role_id'])) { if(in_array('23',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "204", check: "<?php if(isset($_GET['role_id'])) { if(in_array('204',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_edit');?>", value: "205", check: "<?php if(isset($_GET['role_id'])) { if(in_array('205',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_delete');?>", value: "206", check: "<?php if(isset($_GET['role_id'])) { if(in_array('206',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo '<small>'.$this->lang->line('xin_role_view').' '.$this->lang->line('left_employees_exit').'</small>';?>",  add_info: "<?php echo $this->lang->line('xin_role_view');?>", value: "231", check: "<?php if(isset($_GET['role_id'])) { if(in_array('231',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
				]},
				{ id: "", class: "role-checkbox-modal", text: "Set Team Lead",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "400",check: "<?php if(isset($_GET['role_id'])) { if(in_array('400',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_employees_last_login');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "22", check: "<?php if(isset($_GET['role_id'])) { if(in_array('22',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "Employee List",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "377", check: "<?php if(isset($_GET['role_id'])) { if(in_array('377',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				{ id: "", class: "role-checkbox-modal", text: "Employee Issue",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "3770", check: "<?php if(isset($_GET['role_id'])) { if(in_array('3770',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
			]},

		// Organization
		{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_organization');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('2',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "", value:"2", items: [
			// sub 1
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_department');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "3", check: "<?php if(isset($_GET['role_id'])) { if(in_array('3',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "3", check: "<?php if(isset($_GET['role_id'])) { if(in_array('3',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "240", check: "<?php if(isset($_GET['role_id'])) { if(in_array('240',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "241", check: "<?php if(isset($_GET['role_id'])) { if(in_array('241',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "242", check: "<?php if(isset($_GET['role_id'])) { if(in_array('242',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_designation');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "4", check: "<?php if(isset($_GET['role_id'])) { if(in_array('4',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "4", check: "<?php if(isset($_GET['role_id'])) { if(in_array('4',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "243", check: "<?php if(isset($_GET['role_id'])) { if(in_array('243',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "244", check: "<?php if(isset($_GET['role_id'])) { if(in_array('244',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "245", check: "<?php if(isset($_GET['role_id'])) { if(in_array('245',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo '<small>'.$this->lang->line('xin_role_view').' '.$this->lang->line('left_designation').'</small>';?>",  add_info: "<?php echo $this->lang->line('xin_role_view');?>", value: "249",check: "<?php if(isset($_GET['role_id'])) { if(in_array('249',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_company');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "5", check: "<?php if(isset($_GET['role_id'])) { if(in_array('5',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "5", check: "<?php if(isset($_GET['role_id'])) { if(in_array('5',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "246", check: "<?php if(isset($_GET['role_id'])) { if(in_array('246',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "247", check: "<?php if(isset($_GET['role_id'])) { if(in_array('247',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "248", check: "<?php if(isset($_GET['role_id'])) { if(in_array('248',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_location');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "6", check: "<?php if(isset($_GET['role_id'])) { if(in_array('6',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "6", check: "<?php if(isset($_GET['role_id'])) { if(in_array('6',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "250", check: "<?php if(isset($_GET['role_id'])) { if(in_array('250',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "251", check: "<?php if(isset($_GET['role_id'])) { if(in_array('251',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "252", check: "<?php if(isset($_GET['role_id'])) { if(in_array('252',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_announcements');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "11", check: "<?php if(isset($_GET['role_id'])) { if(in_array('11',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "11", check: "<?php if(isset($_GET['role_id'])) { if(in_array('11',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "254", check: "<?php if(isset($_GET['role_id'])) { if(in_array('254',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "255", check: "<?php if(isset($_GET['role_id'])) { if(in_array('255',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "256", check: "<?php if(isset($_GET['role_id'])) { if(in_array('256',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo '<small>'.$this->lang->line('xin_role_view').' '.$this->lang->line('left_announcements').'</small>';?>",  add_info: "<?php echo $this->lang->line('xin_role_view');?>", value: "257", check: "<?php if(isset($_GET['role_id'])) { if(in_array('257',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_policies');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "9", check: "<?php if(isset($_GET['role_id'])) { if(in_array('9',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "9", check: "<?php if(isset($_GET['role_id'])) { if(in_array('9',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "258", check: "<?php if(isset($_GET['role_id'])) { if(in_array('258',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "259", check: "<?php if(isset($_GET['role_id'])) { if(in_array('259',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "260", check: "<?php if(isset($_GET['role_id'])) { if(in_array('260',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_org_chart_title');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "96", check: "<?php if(isset($_GET['role_id'])) { if(in_array('96',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
		]}, // sub 1 end
		// Organization

		// Timesheet HR
		{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_timesheet');?>",  add_info: "", check: "<?php if(isset($_GET['role_id'])) { if(in_array('27',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", value: "27",  items: [

			{ id: "", class: "role-checkbox", text: "Employee Attendance",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "389",check: "<?php if(isset($_GET['role_id'])) { if(in_array('389',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox", text: "Employee Movement",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "123",check: "<?php if(isset($_GET['role_id'])) { if(in_array('123',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox", text: "Employee Leave",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "124",check: "<?php if(isset($_GET['role_id'])) { if(in_array('124',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

			{ id: "", class: "role-checkbox", text: "Employee Holiday",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "130",check: "<?php if(isset($_GET['role_id'])) { if(in_array('130',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

			{ id: "", class: "role-checkbox", text: "attn file upload",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "1001",check: "<?php if(isset($_GET['role_id'])) { if(in_array('1001',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox", text: "Attendance process",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "1002",check: "<?php if(isset($_GET['role_id'])) { if(in_array('1002',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox", text: " Movement register",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "1003",check: "<?php if(isset($_GET['role_id'])) { if(in_array('1003',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_attendance');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "28", check: "<?php if(isset($_GET['role_id'])) { if(in_array('28',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "28", check: "<?php if(isset($_GET['role_id'])) { if(in_array('28',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo '<small>'.$this->lang->line('xin_role_view').' '.$this->lang->line('left_timesheet').'</small>';?>",  add_info: "<?php echo $this->lang->line('xin_role_view');?>", value: "397", check: "<?php if(isset($_GET['role_id'])) { if(in_array('397',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_month_timesheet_title');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "10", check: "<?php if(isset($_GET['role_id'])) { if(in_array('10',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "10", check: "<?php if(isset($_GET['role_id'])) { if(in_array('10',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo '<small>'.$this->lang->line('xin_role_view').' '.$this->lang->line('xin_month_timesheet_title').'</small>';?>",  add_info: "<?php echo $this->lang->line('xin_role_view');?>", value: "253",check: "<?php if(isset($_GET['role_id'])) { if(in_array('253',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			]},
			{ id: "", class: "role-checkbox", text: "<?php echo $this->lang->line('xin_attendance_timecalendar');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "261",check: "<?php if(isset($_GET['role_id'])) { if(in_array('261',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_date_wise_attendance');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "29", check: "<?php if(isset($_GET['role_id'])) { if(in_array('29',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "29", check: "<?php if(isset($_GET['role_id'])) { if(in_array('29',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo '<small>'.$this->lang->line('xin_role_view').' '.$this->lang->line('left_date_wise_attendance').'</small>';?>",  add_info: "<?php echo $this->lang->line('xin_role_view');?>", value: "381", check: "<?php if(isset($_GET['role_id'])) { if(in_array('381',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_update_attendance');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "30", check: "<?php if(isset($_GET['role_id'])) { if(in_array('30',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "30", check: "<?php if(isset($_GET['role_id'])) { if(in_array('30',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "277", check: "<?php if(isset($_GET['role_id'])) { if(in_array('277',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "278", check: "<?php if(isset($_GET['role_id'])) { if(in_array('278',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "279", check: "<?php if(isset($_GET['role_id'])) { if(in_array('279',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo '<small>'.$this->lang->line('xin_role_upd_company_attendance').'</small>';?>",  add_info: "<?php echo $this->lang->line('xin_role_view');?>", value: "310", check: "<?php if(isset($_GET['role_id'])) { if(in_array('310',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_overtime_request');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "401", check: "<?php if(isset($_GET['role_id'])) { if(in_array('401',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "401", check: "<?php if(isset($_GET['role_id'])) { if(in_array('401',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "402", check: "<?php if(isset($_GET['role_id'])) { if(in_array('402',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "403", check: "<?php if(isset($_GET['role_id'])) { if(in_array('403',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_import_attendance');?>",  add_info: "<?php echo $this->lang->line('xin_attendance_import');?>", value: "31", check: "<?php if(isset($_GET['role_id'])) { if(in_array('31',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_office_shifts');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "7", check: "<?php if(isset($_GET['role_id'])) { if(in_array('7',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "7", check: "<?php if(isset($_GET['role_id'])) { if(in_array('7',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "280", check: "<?php if(isset($_GET['role_id'])) { if(in_array('280',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "281", check: "<?php if(isset($_GET['role_id'])) { if(in_array('281',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "282", check: "<?php if(isset($_GET['role_id'])) { if(in_array('282',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_change_default');?>",  add_info: "<?php echo $this->lang->line('xin_role_change_default');?>", value: "2822", check: "<?php if(isset($_GET['role_id'])) { if(in_array('2822',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo '<small>'.$this->lang->line('xin_role_view').' '.$this->lang->line('left_office_shifts').'</small>';?>",  add_info: "<?php echo $this->lang->line('xin_role_view');?>", value: "311", check: "<?php if(isset($_GET['role_id'])) { if(in_array('311',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_holidays');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "8", check: "<?php if(isset($_GET['role_id'])) { if(in_array('8',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "8", check: "<?php if(isset($_GET['role_id'])) { if(in_array('8',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "283", check: "<?php if(isset($_GET['role_id'])) { if(in_array('283',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "284", check: "<?php if(isset($_GET['role_id'])) { if(in_array('284',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "285", check: "<?php if(isset($_GET['role_id'])) { if(in_array('285',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
			]},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_leaves');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "46", check: "<?php if(isset($_GET['role_id'])) { if(in_array('46',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "46", check: "<?php if(isset($_GET['role_id'])) { if(in_array('46',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "287", check: "<?php if(isset($_GET['role_id'])) { if(in_array('287',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "288", check: "<?php if(isset($_GET['role_id'])) { if(in_array('288',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "289", check: "<?php if(isset($_GET['role_id'])) { if(in_array('289',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo '<small>'.$this->lang->line('xin_role_view').' '.$this->lang->line('left_leaves').'</small>';?>",  add_info: "<?php echo $this->lang->line('xin_role_view');?>", value: "290", check: "<?php if(isset($_GET['role_id'])) { if(in_array('290',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo '<small>'.$this->lang->line('xin_role_1st_level_approval').'</small>';?>",  add_info: "<?php echo $this->lang->line('xin_role_view');?>", value: "286", check: "<?php if(isset($_GET['role_id'])) { if(in_array('286',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			{ id: "", class: "role-checkbox-modal", text: "<?php echo '<small>'.$this->lang->line('xin_role_2nd_level_approval').'</small>';?>",  add_info: "<?php echo $this->lang->line('xin_role_view');?>", value: "312", check: "<?php if(isset($_GET['role_id'])) { if(in_array('312',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
			]},
		]},
		// Timesheet HR

		// Store part //
		{ id: "", class: "role-checkbox-modal", text: "Store",  add_info: "", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1030',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", value: "1030",  items: [
			{ id: "", class: "role-checkbox-modal", text: "My Requisition",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1031", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1031',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

			{ id: "", class: "role-checkbox-modal", text: "Requisition",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "1070", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1070',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", items: [
					{ id: "", class: "role-checkbox-modal", text: "Create Requisition",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1071", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1071',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
					{ id: "", class: "role-checkbox-modal", text: "Requisition List",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1072", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1072',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
					{ id: "", class: "role-checkbox-modal", text: "Pending Requisition",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1073", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1073',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
					{ id: "", class: "role-checkbox-modal", text: "Approved Requisition",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1074", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1074',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
					{ id: "", class: "role-checkbox-modal", text: "Delivered Requisition",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1075", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1075',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
					{ id: "", class: "role-checkbox-modal", text: "Rejected Requisition",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1076", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1076',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			]},

			{ id: "", class: "role-checkbox-modal", text: "Purchase",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "1080", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1080',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", items: [
						{ id: "", class: "role-checkbox-modal", text: "Requisition",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1081", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1081',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
						{ id: "", class: "role-checkbox-modal", text: "Pending List",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1082", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1082',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
						{ id: "", class: "role-checkbox-modal", text: "Approved List",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1083", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1083',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
						{ id: "", class: "role-checkbox-modal", text: "Order Received List",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1084", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1084',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
						{ id: "", class: "role-checkbox-modal", text: "Rejected List",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1085", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1085',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
			]},


			{ id: "", class: "role-checkbox-modal", text: "Report",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1033", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1033',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

			{ id: "", class: "role-checkbox-modal", text: "Settings",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "1041", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1041',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", items: [
					{ id: "", class: "role-checkbox-modal", text: "Product",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1042", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1042',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
					{ id: "", class: "role-checkbox-modal", text: "Movement",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1048", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1048',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
					{ id: "", class: "role-checkbox-modal", text: "Low Product",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1047", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1047',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
					{ id: "", class: "role-checkbox-modal", text: "Supplier",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1046", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1046',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
					{ id: "", class: "role-checkbox-modal", text: "Unit",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1043", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1043',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
					{ id: "", class: "role-checkbox-modal", text: "Category",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1044", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1044',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

					{ id: "", class: "role-checkbox-modal", text: "Sub Category",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1045", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1045',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				]},

		]},
		// Store part //

		// Inventory / Accessories part //
		{ id: "", class: "role-checkbox-modal", text: "Inventory",  add_info: "", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1100',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", value: "1100",  items: [

			{ id: "", class: "role-checkbox-modal", text: "Item List",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1101", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1101',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

			{ id: "", class: "role-checkbox-modal", text: "Item Add",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1102", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1102',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

			{ id: "", class: "role-checkbox-modal", text: "Report",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1103", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1103',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

			{ id: "", class: "role-checkbox-modal", text: "Settings",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "1110", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1110',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", items: [
					{ id: "", class: "role-checkbox-modal", text: "Category",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1111", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1111',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

					{ id: "", class: "role-checkbox-modal", text: "Device Model",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1112", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1112',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

					{ id: "", class: "role-checkbox-modal", text: "Add Phone Number",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1113", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1113',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

					{ id: "", class: "role-checkbox-modal", text: "Add Desk",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "1114", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1114',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
				]},

		]},
		// Inventory / Accessories part /
	]
});

jQuery("#treeview_m2").kendoTreeView({
checkboxes: {
checkChildren: true,
template: "<label><input type='checkbox' #= item.check# class='#= item.class #' name='role_resources[]' value='#= item.value #'> #= item.text #</label>"
},
check: onCheck,
dataSource: [

{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_system');?>",  add_info: "", check: "<?php if(isset($_GET['role_id'])) { if(in_array('57',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",value: "57",  items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_settings');?>",  add_info: "<?php echo $this->lang->line('xin_view_update');?>", value: "60", check: "<?php if(isset($_GET['role_id'])) { if(in_array('60',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_constants');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "61", check: "<?php if(isset($_GET['role_id'])) { if(in_array('61',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox", text: "<?php echo $this->lang->line('xin_acc_payment_gateway');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "118", check: "<?php if(isset($_GET['role_id'])) { if(in_array('118',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_db_backup');?>",  add_info: "<?php echo $this->lang->line('xin_create_delete_download');?>", value: "62", check: "<?php if(isset($_GET['role_id'])) { if(in_array('62',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('left_email_templates');?>",  add_info: "<?php echo $this->lang->line('xin_update');?>", value: "63", check: "<?php if(isset($_GET['role_id'])) { if(in_array('63',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_setup_modules');?>",  add_info: "<?php echo $this->lang->line('xin_update');?>", value: "93", check: "<?php if(isset($_GET['role_id'])) { if(in_array('93',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
]},

{ id: "", class: "role-checkbox-modal",text: "<?php echo $this->lang->line('xin_acc_accounts');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('71',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "",value: "71",  items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_account_list');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('72',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "72",items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "72", check: "<?php if(isset($_GET['role_id'])) { if(in_array('72',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "352", check: "<?php if(isset($_GET['role_id'])) { if(in_array('352',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "353", check: "<?php if(isset($_GET['role_id'])) { if(in_array('353',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "354", check: "<?php if(isset($_GET['role_id'])) { if(in_array('354',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
	]},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_account_balances');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('73',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "73",},
	]},
	{ id: "", class: "role-checkbox-modal",text: "<?php echo $this->lang->line('xin_acc_transactions');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('74',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "",value: "74",  items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_deposit');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('75',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "75",items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "75", check: "<?php if(isset($_GET['role_id'])) { if(in_array('75',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "355", check: "<?php if(isset($_GET['role_id'])) { if(in_array('355',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "356", check: "<?php if(isset($_GET['role_id'])) { if(in_array('356',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "357", check: "<?php if(isset($_GET['role_id'])) { if(in_array('357',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
	]},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_expense');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('76',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "76",items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "76", check: "<?php if(isset($_GET['role_id'])) { if(in_array('76',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "358", check: "<?php if(isset($_GET['role_id'])) { if(in_array('358',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "359", check: "<?php if(isset($_GET['role_id'])) { if(in_array('359',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "360", check: "<?php if(isset($_GET['role_id'])) { if(in_array('360',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
	]},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_transfer');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('77',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "77",items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "77", check: "<?php if(isset($_GET['role_id'])) { if(in_array('77',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "361", check: "<?php if(isset($_GET['role_id'])) { if(in_array('361',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "362", check: "<?php if(isset($_GET['role_id'])) { if(in_array('362',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "363", check: "<?php if(isset($_GET['role_id'])) { if(in_array('363',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
	]},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_view_transactions');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('78',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "<?php echo $this->lang->line('xin_view');?>", value: "78",},
	]},

	{ id: "", class: "role-checkbox-modal",text: "<?php echo $this->lang->line('xin_acc_payees_payers');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('79',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "",value: "79",  items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_payees');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('80',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "80",items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "80", check: "<?php if(isset($_GET['role_id'])) { if(in_array('80',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "364", check: "<?php if(isset($_GET['role_id'])) { if(in_array('364',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "365", check: "<?php if(isset($_GET['role_id'])) { if(in_array('365',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "366", check: "<?php if(isset($_GET['role_id'])) { if(in_array('366',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
	]},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_payers');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('81',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "81",items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "81", check: "<?php if(isset($_GET['role_id'])) { if(in_array('81',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "367", check: "<?php if(isset($_GET['role_id'])) { if(in_array('367',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "368", check: "<?php if(isset($_GET['role_id'])) { if(in_array('368',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "369", check: "<?php if(isset($_GET['role_id'])) { if(in_array('369',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
	]},
	]},

	{ id: "", class: "role-checkbox-modal",text: "<?php echo $this->lang->line('xin_acc_reports');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('82',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "",value: "82",  items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_account_statement');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('83',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "<?php echo $this->lang->line('xin_view');?>", value: "83"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_expense_reports');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('84',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "<?php echo $this->lang->line('xin_view');?>", value: "84",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_income_reports');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('85',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "<?php echo $this->lang->line('xin_view');?>", value: "85",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_transfer_report');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('86',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "86",},
	]},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_quote_manager');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "87", check: "<?php if(isset($_GET['role_id'])) { if(in_array('87',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_project_clients');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "119", check: "<?php if(isset($_GET['role_id'])) { if(in_array('119',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "119", check: "<?php if(isset($_GET['role_id'])) { if(in_array('119',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "323", check: "<?php if(isset($_GET['role_id'])) { if(in_array('323',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "324", check: "<?php if(isset($_GET['role_id'])) { if(in_array('324',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "325", check: "<?php if(isset($_GET['role_id'])) { if(in_array('325',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_view');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "326", check: "<?php if(isset($_GET['role_id'])) { if(in_array('326',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	]},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_leads');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "410", check: "<?php if(isset($_GET['role_id'])) { if(in_array('410',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "411", check: "<?php if(isset($_GET['role_id'])) { if(in_array('411',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "412", check: "<?php if(isset($_GET['role_id'])) { if(in_array('412',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "413", check: "<?php if(isset($_GET['role_id'])) { if(in_array('413',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "414", check: "<?php if(isset($_GET['role_id'])) { if(in_array('414',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_view');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "420", check: "<?php if(isset($_GET['role_id'])) { if(in_array('420',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	]},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_estimates');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "415", check: "<?php if(isset($_GET['role_id'])) { if(in_array('415',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "416", check: "<?php if(isset($_GET['role_id'])) { if(in_array('416',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_create');?>",  add_info: "<?php echo $this->lang->line('xin_role_create');?>", value: "417", check: "<?php if(isset($_GET['role_id'])) { if(in_array('417',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "418", check: "<?php if(isset($_GET['role_id'])) { if(in_array('418',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "419", check: "<?php if(isset($_GET['role_id'])) { if(in_array('419',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",}
	]},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_invoices_title');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "121", check: "<?php if(isset($_GET['role_id'])) { if(in_array('121',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "121", check: "<?php if(isset($_GET['role_id'])) { if(in_array('121',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_create');?>",  add_info: "<?php echo $this->lang->line('xin_role_create');?>", value: "120", check: "<?php if(isset($_GET['role_id'])) { if(in_array('120',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_edit');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "328", check: "<?php if(isset($_GET['role_id'])) { if(in_array('328',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "329", check: "<?php if(isset($_GET['role_id'])) { if(in_array('329',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",}
	]},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_acc_invoice_payments');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "330", check: "<?php if(isset($_GET['role_id'])) { if(in_array('330',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	{ id: "", class: "role-checkbox-modal", text: "Get Payment",  add_info: "<?php echo $this->lang->line('xin_add_edit_view_delete_role_info');?>", value: "3320", check: "<?php if(isset($_GET['role_id'])) { if(in_array('3320',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",},
	]},

	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_lang_settings');?>",  add_info: "<?php echo $this->lang->line('xin_add_edit_delete_role_info');?>", value: "89", check: "<?php if(isset($_GET['role_id'])) { if(in_array('89',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>",items: [
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_enable');?>",  add_info: "<?php echo $this->lang->line('xin_role_enable');?>", value: "89", check: "<?php if(isset($_GET['role_id'])) { if(in_array('89',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_add');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "370", check: "<?php if(isset($_GET['role_id'])) { if(in_array('370',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_role_delete');?>",  add_info: "<?php echo $this->lang->line('xin_role_add');?>", value: "371", check: "<?php if(isset($_GET['role_id'])) { if(in_array('371',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"}
	]},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_notify_top');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "90", check: "<?php if(isset($_GET['role_id'])) { if(in_array('90',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('header_apply_jobs');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "91", check: "<?php if(isset($_GET['role_id'])) { if(in_array('91',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_theme_settings');?>",  add_info: "<?php echo $this->lang->line('xin_theme_settings');?>", value: "94", check: "<?php if(isset($_GET['role_id'])) { if(in_array('94',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_hr_calendar_title');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "95", check: "<?php if(isset($_GET['role_id'])) { if(in_array('95',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

	// hr report setting
	{ id: "", class: "role-checkbox-modal",text: "<?php echo $this->lang->line('xin_hr_report_title');?>", check: "<?php if(isset($_GET['role_id'])) { if(in_array('110',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>", add_info: "",value: "110", items: [

		{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_hr_reports_payslip');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "111", check: "<?php if(isset($_GET['role_id'])) { if(in_array('111',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

		{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_hr_reports_attendance_employee');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "112", check: "<?php if(isset($_GET['role_id'])) { if(in_array('112',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

		{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_hr_reports_training');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "113", check: "<?php if(isset($_GET['role_id'])) { if(in_array('113',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

		{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_hr_reports_projects');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "114", check: "<?php if(isset($_GET['role_id'])) { if(in_array('114',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

		{ id: "", class: "role-checkbox-modal", text: "Store Report",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "1141", check: "<?php if(isset($_GET['role_id'])) { if(in_array('1141',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

		{ id: "", class: "role-checkbox-modal", text: "Leave Report",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "115", check: "<?php if(isset($_GET['role_id'])) { if(in_array('115',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

		{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_hr_report_user_roles');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "116", check: "<?php if(isset($_GET['role_id'])) { if(in_array('116',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

		{ id: "", class: "role-checkbox-modal", text: "<?php echo $this->lang->line('xin_hr_report_employees');?>",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "117", check: "<?php if(isset($_GET['role_id'])) { if(in_array('117',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

		{ id: "", class: "role-checkbox-modal", text: "Lunch",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "409", check: "<?php if(isset($_GET['role_id'])) { if(in_array('409',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

		{ id: "", class: "role-checkbox-modal", text: "Accounts",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "419", check: "<?php if(isset($_GET['role_id'])) { if(in_array('419',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},

		{ id: "", class: "role-checkbox-modal", text: "Employee  Issue",  add_info: "<?php echo $this->lang->line('xin_view');?>", value: "420", check: "<?php if(isset($_GET['role_id'])) { if(in_array('420',$role_resources_ids)): echo 'checked'; else: echo ''; endif; }?>"},
	]},
]
});

	// show checked node IDs on datasource change
	function onCheck() {
		var checkedNodes = [],
		treeView = jQuery("#treeview").data("kendoTreeView"),
		message;
		//checkedNodeIds(treeView.dataSource.view(), checkedNodes);
		jQuery("#result").html(message);
	}
	$(document).ready(function(){
		$("#role_access_modal").change(function(){
			var sel_val = $(this).val();
			if(sel_val=='1') {
				$('.role-checkbox-modal').prop('checked', true);
			} else {
				$('.role-checkbox-modal').prop("checked", false);
			}
		});
	});
</script>
<?php }
?>
