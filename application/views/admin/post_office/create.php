<div class="box box-block bg-white">
    <div class="box-header with-border">
        <h3 class="box-title">Add post office</h3>
    </div>
    <div class="box-body">
        <form action="<?php echo site_url('admin/address/add_post_office') ?>" method="post">
            <div class="form-group">
                <label for="name_en">Select Division</label>
                <select name="div_id" class="form-control" id="div_id" required>
                    <option value="">Select</option>
                    <?php foreach ($divisions as $division) { ?>
                    <option value="<?php echo $division->id ?>"><?php echo $division->name_en ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="name_en">Select District</label>
                <select name="dis_id" id="dis_id" class="form-control" required>
                </select>
            </div>

            <div class="form-group">
                <label for="name_en">Select Upazila</label>
                <select name="up_id" id="up_id" class="form-control" required>
                </select>
            </div>

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
<script>
    $(document).ready(function() {
        $('#div_id').on('change', function() {
            var div_id = $(this).val();
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('api/Client_attendance/get_district') ?>',
                data: {
                    division_id: div_id
                },
                success: function(data) {
                    emp_districts=data.data.emp_districts
                    $('#dis_id').html('<option value="">Select</option>');
                    $.each(emp_districts, function(index, value) {
                        $('#dis_id').append('<option value="' + value.id + '">' + value.name_en + '</option>');
                    });
                }
            });
        });
        $('#dis_id').on('change', function() {
            var dis_id = $('#dis_id').val();
            var div_id = $('#div_id').val();

            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('api/Client_attendance/get_upazila') ?>',
                data: {
                    district_id: dis_id,
                    division_id:div_id 
                },
                success: function(data) {
                    emp_districts=data.data.emp_upazilas
                    $('#up_id').html('<option value="">Select</option>');
                    $.each(emp_districts, function(index, value) {
                        $('#up_id').append('<option value="' + value.id + '">' + value.name_en + '</option>');
                    });
                }
            });
        });
    })
</script>
