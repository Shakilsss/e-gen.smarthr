<div class="box box-block bg-white">
    <div class="box-header with-border">
        <h3 class="box-title">Add Division</h3>
    </div>
    <div class="box-body">
        <form action="<?php echo site_url('admin/address/add_division') ?>" method="post">
            <div class="form-group">
                <label for="name_en">Name (English)</label>
                <input type="text" class="form-control" name="name_en" placeholder="Name (English)" required>
            </div>
            <div class="form-group">
                <button class="btn btn-block btn-primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
