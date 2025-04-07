<?php $session = $this->session->userdata('username');?>
<style>
    .back {
        float: right;
        margin-top: -40px;
        position: relative;
    }
</style>
<a class="back btn btn-primary btn-sm" href="<?= base_url('admin/attendance/index') ?>"> Back </a>

<div class="table-responsive" style="padding: 25px;box-shadow: 0px 0px 8px 1px #d0d0d0;border-radius: 7px;">
    <table id="table_data" class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Request Date</th>
                <th>In Time</th>
                <th>Out Time</th>
                <th>Request Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($alldata as $key => $request) {
                ?>
            <tr>
                <td><?= $key+1 ?></td>
                <td><?= $request->first_name.' '.$request->last_name ?></td>
                <td><?= $request->date ?></td>
                <td><?= $request->in_time == '00:00:00' ? '-' : $request->in_time ?></td>
                <td><?= $request->out_time == '00:00:00' ? '-' : $request->out_time ?></td>
                <td><?= $request->status == 1 ? 'Pending' : 'Approved' ?></td>
                <td class="status_action">
                    <?php if($request->status == 1 && $session['role_id'] == 1) { ?>
                        <a onclick="accept_request('<?= $request->id ?>',this)" class="btn btn-primary btn-sm">Approve</a>
                        <a onclick="reject_request('<?= $request->id ?>',this)" class="btn btn-danger btn-sm">Reject</a>
                    <?php } elseif($request->status == 2) {
                        echo '<span class="badge badge-success">Approved </span>';
                    } else {
                        echo '<span class="badge badge-danger">Rejected </span>';
                    }?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#table_data').DataTable();
    });

    function accept_request(id, el) {
        $.ajax({
            url: '<?= base_url("admin/attendance/accept_request") ?>',
            type: 'POST',
            data: {
                id: id
            },
            success: function(data) {
                $(el).closest('.status_action').html('<span class="badge badge-success">Approved </span>');
                alert('Request Accepted');
            }
        });
    }

    function reject_request(id, el) {
        $.ajax({
            url: '<?= base_url("admin/attendance/reject_request") ?>',
            type: 'POST',
            data: {
                id: id
            },
            success: function(data) {
                $(el).closest('.status_action').html('<span class="badge badge-danger">Rejected </span>');
                alert('Request Rejected');
            }
        });
    }
</script>
