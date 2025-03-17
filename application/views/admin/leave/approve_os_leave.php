<?php $get_animate = $this->Xin_model->get_content_animate();?>
<?php $session = $this->session->userdata('username');?>

<div class="row">
    <div class="col-md-12">
        <?php if($this->session->flashdata('success')):?>
            <div class="alert alert-success">
                <?=$this->session->flashdata('success');;?>
            </div>
        <?php endif; ?>

        <div class="box mb-4 <?php echo $get_animate;?>">
            <div class="box-header with-border">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>Name</th>
                            <th>from date </th>
                            <th>to date</th>
                            <th>app. From date</th>
                            <th>app. To date</th>
                            <th>Status</th>
                            <th>Remark</th>
                            <th>Action</th>
                        </tr>

                        <?php foreach($results as $k => $res) { ?>
                        <tr>
                            <td><?php echo $k+1; ?></td>
                            <td><?php echo $res->first_name.' '.$res->last_name;?></td>
                            <td><?php echo date("d-m-Y", strtotime($res->from_date)) ;?></td>
                            <td><?php echo date("d-m-Y", strtotime($res->to_date)) ;?></td>
                            <?php if ($res->status == 3) { ?>
                            <td><?php echo date("d-m-Y", strtotime($res->ap_from_date)) ;?></td>
                            <td><?php echo date("d-m-Y", strtotime($res->ap_to_date)) ;?></td>
                            <?php } else { ?>
                            <td>--</td>
                            <td>--</td>
                            <?php } ?>
                            <?php if ($res->status == 1) {
                                $status = 'Draft';
                            } elseif ($res->status == 2) {
                                $status = 'On process';
                            } elseif ($res->status == 3) {
                                $status = 'Head Approved';
                            } else if ($res->status == 6) {
                                $status = 'Approved';
                            } else {
                                $status = 'Rejected';
                            } ?>

                            <td><?= $status; ?></td>
                            <td><?php echo $res->remark;?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">

                                        <?php if ($session['role_id'] == 1) { ?>
                                            <li><a href="<?php echo base_url();?>admin/leave/os_leave_del_rej/6/<?php echo $res->id;?>"><i class="fa fa-pencil-square-o"></i> Approve</a></li>
                                        <?php } else { ?>
                                            <li><a href="<?php echo base_url();?>admin/leave/os_leave_change/<?php echo $res->id;?>"><i class="fa fa-pencil-square-o"></i> Approve</a></li>
                                        <?php } ?>

                                        <li><a href="<?php echo base_url();?>admin/leave/os_leave_del_rej/4/<?php echo $res->id;?>"><i class="fa fa-pencil-square-o"></i> Reject</a></li>
                                        <li><a href="<?php echo base_url();?>admin/leave/os_leave_del_rej/5/<?php echo $res->id;?>"><i class="fa fa-pencil-square-o"></i> delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // get designations
    $('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
    $('[data-plugin="select_hrm"]').select2({
        width: '100%'
    });
});
</script>
