
<style>
    body {
        background: linear-gradient(to right, #f8f9fc, #e6e9f0);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .summary-box {
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        padding: 30px 20px;
        text-align: center;
        transition: all 0.4s ease;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .summary-box:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .summary-count {
        font-size: 2.2rem;
        font-weight: 800;
        color: #3819e7;
        margin-top: 12px;
        transition: 0.3s ease-in-out;
    }

    .summary-box div:first-child {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
    }

    .card-status {
        height: 59vh;
        overflow-y: auto;
        border: none;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease-in-out;
    }

    .card-status:hover {
        transform: translateY(-3px);
    }

    .card-body {
        padding: 24px;
    }

    .card-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
    }

    .table {
        border-radius: 12px;
        overflow: hidden;
        font-size: 0.95rem;
    }

    .table thead {
        background: #e8e8f3;
        font-weight: 600;
    }

    .badge-custom {
        background-color: #3819e7;
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        box-shadow: 0 2px 6px rgba(56, 25, 231, 0.3);
    }

    img {
        height: 42px;
        width: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .text-success {
        color: #3bc87f !important;
    }

    .text-danger {
        color: #f04e4e !important;
    }

    .text-warning {
        color: #f6b73c !important;
    }

    /* Scrollbar Styling */
    .card-status::-webkit-scrollbar {
        width: 6px;
    }

    .card-status::-webkit-scrollbar-thumb {
        background: #bbb;
        border-radius: 10px;
    }

    .card-status::-webkit-scrollbar-track {
        background: transparent;
    }

    /* Fade animation for new data */
    /* tbody tr:hover {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(5px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    } */
</style>


<div class="container-fluid mt-4">
    <!-- Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="summary-box">
                <div>🟢 In Office</div>
                <div id="in_count" class="summary-count">0</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box">
                <div>🔴 Out Office</div>
                <div id="out_count" class="summary-count">0</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box">
                <div>🟡 On Leave</div>
                <div id="leave_count" class="summary-count">0</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box">
                <div>👥 Total Employees</div>
                <div id="total_count" class="summary-count text-danger">0</div>
            </div>
        </div>
    </div>

    <!-- Data Cards -->
    <div class="row g-3">
        <!-- In Office -->
        <div class="col-md-4">
            <div class="card card-status">
                <div class="card-body">
                    <div class="card-title text-success">🟢 In Office</div>
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody id="in_office_table"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Out Office -->
        <div class="col-md-4">
            <div class="card card-status">
                <div class="card-body">
                    <div class="card-title text-danger">🔴 Out Office</div>
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody id="out_office_table"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- On Leave -->
        <div class="col-md-4">
            <div class="card card-status">
                <div class="card-body">
                    <div class="card-title text-warning">🟡 On Leave</div>
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody id="leave_table"></tbody>
                    </table>
                </div>
            </div>
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
            $('#total_count').text(res.total);

            let in_html = '';
            res.in_office.forEach(emp => {
                let now = new Date();
                let emp_date = new Date(emp.date_time);
                let diff = Math.abs(now - emp_date);
                let hours = Math.floor(diff / (1000 * 60 * 60));
                let minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                let time = hours + 'h ' + minutes + 'm ago';
                in_html += `<tr>
                    <td><span class="badge badge-custom">${emp.full_name}</span></td>
                    <td><img src="<?= base_url('uploads/profile/') ?>${emp.profile_picture}" /></td>
                    <td>${time}</td>
                </tr>`;
            });
            $('#in_office_table').html(in_html);

            let out_html = '';
            res.out_office.forEach(emp => {
                let now = new Date();
                let emp_date = new Date(emp.date_time);
                let diff = Math.abs(now - emp_date);
                let hours = Math.floor(diff / (1000 * 60 * 60));
                let minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                let time = hours + 'h ' + minutes + 'm ago';
                out_html += `<tr>
                    <td><span class="badge badge-custom">${emp.full_name}</span></td>
                    <td><img src="<?= base_url('uploads/profile/') ?>${emp.profile_picture}" /></td>
                    <td>${time}</td>
                </tr>`;
            });
            $('#out_office_table').html(out_html);

            let leave_html = '';
            res.leave.forEach(emp => {
                leave_html += `<tr>
                    <td><span class="badge badge-custom">${emp.first_name} ${emp.last_name}</span></td>
                    <td><img src="<?= base_url('uploads/profile/') ?>${emp.profile_picture}" /></td>
                </tr>`;
            });
            $('#leave_table').html(leave_html);
            setTimeout(() => {
                fetchEmployeeStatus();
            }, 3000);
        });
    }

    fetchEmployeeStatus();
</script>
