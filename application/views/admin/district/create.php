<div class="box box-block bg-white">
    <div class="box-header with-border">
        <h3 class="box-title">Add District</h3>
    </div>
    <div class="box-body">
        <form action="<?php echo site_url('admin/address/add_district') ?>" method="post">
            <div class="form-group">
                <label for="name_en">Name (English)</label>
                <input type="text" class="form-control" name="name_en" placeholder="Name (English)" required>
            </div>
            <div class="form-group">
                <label for="name_en">Select Division</label>
                <select name="div_id" class="form-control" required>
                    <option value="">Select</option>
                    <?php foreach ($divisions as $division) { ?>
                    <option value="<?php echo $division->id ?>"><?php echo $division->name_en ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <button class="btn btn-block btn-primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
