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
            <div class="box-header  with-border">
                <h3 class="box-title">Device Setup</h3>
            </div>

            <div class="box-body">
                <form action="<?= current_url(); ?>" method="get" enctype="multipart/form-data" name="add_schedule" id="xin-form">
                    <input type="hidden" name="_user" value="<?= $session['user_id']; ?>">

                <div class="form-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label> Name <i class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                <input required class="form-control" value="<?=$info->name;?>" name="name" >
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label> Location </label>
                                <input class="form-control" value="<?=$info->location;?>" name="location" >
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label> Model </label>
                                <input class="form-control" value="<?=$info->model;?>" name="model" >
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label> Device Ip  <i class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                <input required class="form-control" value="<?=$info->ip;?>" name="ip" >
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label> Device Port <i class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                <input required class="form-control" value="<?=$info->port;?>" name="port" >
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label> Type <i class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                <select name="type" class="form-control">
                                    <option <?= $info->type == 1 ?> value="1">In</option>
                                    <option <?= $info->type == 2 ?> value="2">Out</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label> Status <i class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                <select name="status" class="form-control">
                                    <option <?= $info->status == 1 ?> value="1">Active</option>
                                    <option <?= $info->status == 2 ?> value="2">Inactive</option>
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

<script type="text/javascript">
$(document).ready(function() {
    // get designations
    $('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
    $('[data-plugin="select_hrm"]').select2({
        width: '100%'
    });
});
</script>
