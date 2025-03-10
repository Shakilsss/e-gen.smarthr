<div class="box box-block bg-white">
    <div class="box-header with-border">
        <h3 class="box-title">Edit post_office</h3>
    </div>
    <div class="box-body">
        <form action="<?php echo site_url('admin/address/edit_post_office').'/'.$post_office_id; ?>" method="post">
            
            <div class="form-group">
                <label for="name_en">Select Division</label>
                <select name="div_id" class="form-control" required>
                    <option value="">Select</option>
                    <?php foreach ($divisions as $division) { ?>
                    <option <?php if($post_office->div_id == $division->id) { echo 'selected'; } ?> value="<?php echo $division->id ?>"><?php echo $division->name_en ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="name_en">Select District</label>
                <select name="dis_id" class="form-control" required>
                    <option value="">Select</option>
                    <?php foreach ($districts as $district) { ?>
                    <option <?php if($post_office->dis_id == $district->id) { echo 'selected'; } ?> value="<?php echo $district->id ?>"><?php echo $district->name_en ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="name_en">Select Upazila</label>
                <select name="up_id" class="form-control" required>
                    <option value="">Select</option>
                    <?php foreach ($upazilas as $upazila) { ?>
                    <option <?php if($post_office->upa_id == $upazila->id) { echo 'selected'; } ?> value="<?php echo $upazila->id ?>"><?php echo $upazila->name_en ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="name_en">Name (English)</label>
                <input type="text" class="form-control" name="name_en" value="<?php echo $post_office->name_en; ?>" placeholder="Name (English)" required>
            </div>

            <div class="form-group">
                <button class="btn btn-block btn-primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
