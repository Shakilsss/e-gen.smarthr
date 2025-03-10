<div class="box box-block bg-white">
    <div class="box-body">
        <div style="display: flex;flex-direction: row;align-items: center;justify-content: space-between;">

            <h2 style="margin: 0;padding: 0;">District</h2>
            <a href="<?php echo site_url('admin/address/add_district') ?>" class="btn btn-xs btn-primary"> <i
                    class="fa fa-plus"></i> Add</a>
        </div>
    </div>
</div>
<div class="box box-block bg-white">
    <div class="box-body ">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Sl. No.</th>
                    <th>District Name</th>
                    <th>Division</th>
                    <th>Actions</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($districts as $key => $district) { ?>
                <tr>
                    <td><?php echo $key+1 ?></td>
                    <td><?php echo $district->name_en ?></td>
                    <td><?php echo $district->division_name ?></td>
                    <td>
                        <div class="btn-group flex gap-px">
                            <a class="btn btn-xs btn-info" href="<?php echo base_url('admin/address/edit_district/'.$district->id) ?>"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                            &nbsp;
                            <a class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')" href="<?php echo base_url('admin/address/delete_district/'.$district->id) ?>"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>