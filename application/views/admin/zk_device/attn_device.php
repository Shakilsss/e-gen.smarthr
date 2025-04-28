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
            <div id="accordion">
                <div class="box-header  with-border">
                    <h3 class="box-title">Device Setup</h3>
                    <div class="box-tools pull-right">
                        <a class="text-dark collapsed" data-toggle="collapse" href="#add_form" aria-expanded="false">
                            <button type="button" class="btn btn-xs btn-primary">
                                <span class="ion ion-md-add"></span> Add
                            </button>
                        </a>
                    </div>
                </div>

                <div id="add_form" class="collapse add-form <?php echo $get_animate;?>" data-parent="#accordion" style="">
                    <div class="box-body">
                        <form action="<?= current_url(); ?>" method="get" enctype="multipart/form-data" name="add_schedule" id="xin-form" autocomplete="off">
                            <input type="hidden" name="_user" value="<?= $session['user_id']; ?>">

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Name <i class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <input required class="form-control" placeholder="Device name" name="name" >
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Location </label>
                                        <input class="form-control" placeholder="location" name="location" >
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Model </label>
                                        <input class="form-control" placeholder="model" name="model" >
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Device Ip  <i class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <input required class="form-control" placeholder="Device ip" name="ip" >
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Device Port <i class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <input required class="form-control" placeholder="Device port" name="port" >
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Type <i class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <select name="type" class="form-control">
                                            <option value="1">In</option>
                                            <option value="2">Out</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Status <i class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <select name="status" class="form-control">
                                            <option value="1">Active</option>
                                            <option value="2">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button name="hrsale_form" type="submit" class="btn btn-primary"><i class="fa fa fa-check-square-o"></i> Save</button>
                            </div>
                        </div>
                        <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="box mb-4 <?php echo $get_animate;?>">
            <div class="box-header with-border">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Model</th>
                            <th>Ip</th>
                            <th>Port</th>
                            <th>Type</th>
                            <th>status</th>
                            <th>Action</th>
                        </tr>

                        <?php foreach($results as $k => $res) { ?>
                        <tr>
                            <td><?php echo $k+1; ?></td>
                            <td><?php echo $res->name;?></td>
                            <td><?php echo $res->location;?></td>
                            <td><?php echo $res->model;?></td>
                            <td><?php echo $res->ip;?></td>
                            <td><?php echo $res->port;?></td>

                            <td><?= $res->type == 1 ? 'In' : 'Out'; ?></td>
                            <td><?= $res->status == 1 ? 'Active' : 'Inactive'; ?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a href="<?php echo base_url();?>admin/zk_device/attn_device_edit/<?php echo $res->id;?>"><i class="fa fa-pencil-square-o"></i> Edit</a></li>
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
