
<style>
        .header_employees_data {
            border-right: 1px solid #ccc;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 9px;
        }
        img { height: 31px; border-radius: 50%; }
    </style>

<div class="container-fluid employee-status mt-3">
    <div class="row mb-3" style="border-bottom: 1px solid;">
        <div class="col-md-12 d-flex">
            <div class="col-md-3 header_employees_data">
                <div class="total-employees">In office</div>
                <span id="in_count">0</span>
            </div>
            <div class="col-md-3 header_employees_data">
                <div class="total-employees">Out office</div>
                <span id="out_count">0</span>
            </div>
            <div class="col-md-3 header_employees_data">
                <div class="total-employees">On leave</div>
                <span id="leave_count">0</span>
            </div>
            <div class="col-md-3 header_employees_data">
                <div class="total-employees">Total employees</div>
                <span id="total_count" class="text-danger">0 In Total</span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- In Office -->
        <div class="col-md-4" style="height: 65vh; overflow-y: scroll;">
            <h5>In Office</h5>
            <table class="table table-striped">
                <thead><tr><td>Name</td><td>Image</td><td>Last Activity</td></tr></thead>
                <tbody id="in_office_table"></tbody>
            </table>
        </div>

        <!-- Out Office -->
        <div class="col-md-4" style="height: 65vh; overflow-y: scroll;">
            <h5 class="text-danger">Out Office</h5>
            <table class="table table-striped">
                <thead><tr><td>Name</td><td>Image</td><td>Last Activity</td></tr></thead>
                <tbody id="out_office_table"></tbody>
            </table>
        </div>

        <!-- Leave -->
        <div class="col-md-4" style="height: 65vh; overflow-y: scroll;">
            <h5 class="text-warning">Leave</h5>
            <table class="table table-striped">
                <thead><tr><td>Name</td><td>Image</td></tr></thead>
                <tbody id="leave_table"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function fetchEmployeeStatus() {
        $.get("<?= base_url('admin/dashboard/get_employee_status_data') ?>", function(data) {
            let res = JSON.parse(data);

            $('#in_count').text(res.in_office.length);
            $('#out_count').text(res.out_office.length);
            $('#leave_count').text(res.leave.length);
            $('#total_count').text(res.total + ' In Total');

            let in_html = '';
            res.in_office.forEach(emp => {
                let time = new Date(emp.date_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                in_html += `<tr>
                    <td><span class="badge badge-danger">${emp.full_name}</span></td>
                    <td><img src="<?= base_url('uploads/users/') ?>${emp.profile_picture}" /></td>
                    <td>${time}</td>
                </tr>`;
            });
            $('#in_office_table').html(in_html);

            let out_html = '';
            res.out_office.forEach(emp => {
                let time = new Date(emp.date_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                out_html += `<tr>
                    <td><span class="badge badge-danger">${emp.full_name}</span></td>
                    <td><img src="<?= base_url('uploads/users/') ?>${emp.profile_picture}" /></td>
                    <td>${time}</td>
                </tr>`;
            });
            $('#out_office_table').html(out_html);

            let leave_html = '';
            res.leave.forEach(emp => {
                leave_html += `<tr>
                    <td><span class="badge badge-danger">${emp.first_name} ${emp.last_name}</span></td>
                    <td><img src="<?= base_url('uploads/users/') ?>${emp.profile_picture}" /></td>
                </tr>`;
            });
            $('#leave_table').html(leave_html);
            setTimeout(() => {
                fetchEmployeeStatus();
            }, 3000);

        });
    }

    // setInterval(fetchEmployeeStatus, 3000);
    fetchEmployeeStatus();
</script>
