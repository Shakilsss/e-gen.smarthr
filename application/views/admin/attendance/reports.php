<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://unpkg.com/lucide@latest"></script>

<style>
.container {
    background: #ffffff;
    border-radius: 12px;
    padding: 30px;
    max-width: 1100px;
    margin: auto;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
}

h2 {
    font-size: 26px;
    font-weight: bold;
    margin-bottom: 5px;
    color: #222;
}

.subtitle {
    color: #777;
    font-size: 15px;
    margin-bottom: 25px;
}

.card {
    background: #fafafa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 0px 6px 1px rgb(0 0 0 / 21%);
    transition: 0.3s ease;
}


.card:hover {
    box-shadow: 0 0px 10px 3px rgb(0 0 0 / 30%);
}

.card h5 {
    font-size: 18px;
    color: #333;
    margin-bottom: 15px;
    position: relative;
    padding-bottom: 10px;
    border-bottom: 3px solid #3819e7;
}

.buttons {
    display: flex;
    flex-direction: row;
    align-items: center;

    gap: 10px;
}

.buttons button {
    background-color: #3819e7;
    color: white;
    border: none;
    padding: 4px 10px;
    font-weight: 500;
    gap: 4px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    transition: 0.3s;
    flex-direction: row;
    align-content: center;
    align-items: center;
}

.buttons .excel {
    background-color: #1dbf73;
}

.buttons .view {
    background-color: #0074d9;
}

.buttons button:hover {
    opacity: 0.9;
}

.radio-tabs {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.radio-tabs input[type="radio"] {
    display: none;
}

.radio-tabs label {
    padding: 4px 10px;
    background: #fff;
    border: 2px solid #ccc;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

.radio-tabs input[type="radio"]:checked+label {
    border-color: #3819e7;
    color: #3819e7;
    font-weight: 600;
    background: #f0f0ff;
}

.filters {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.filter-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #444;
}

.filter-group input,
.filter-group select {
    width: 100%;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 8px;
    background: white;
    transition: 0.2s ease;
}

.filter-group input:focus,
.filter-group select:focus {
    border-color: #3819e7;
    outline: none;
}

.status-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.status-buttons input[type="radio"] {
    display: none;
}

.status-buttons label {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 10px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    cursor: pointer;
    border: 2px solid transparent;
    transition: 0.3s;
    font-size: 14px;
}

.status-buttons input[type="radio"]:checked+label {
    outline: 3px solid #333;
}

.status-buttons .all {
    background: #ff4136;
}

.status-buttons .late-in {
    background: #ff851b;
}

.status-buttons .early-leave {
    background: #aaa;
}

.status-buttons .late-times {
    background: #0074d9;
}

.status-buttons .lwp {
    background: #2ecc40;
}

.status-buttons .duty-hour {
    background: #39cccc;
}

.status-buttons .duty-hour-details {
    background: white;
    color: #0074d9;
    border: 2px solid #0074d9;
}

.status-buttons .duty-hour-details svg {
    stroke: #0074d9;
}

.status-buttons svg {
    width: 18px;
    height: 18px;
}

.col-md-12 {
    padding: 0;
    margin: 0;
}

.loader_report {
    display: none;
}
</style>
<div class="col-md-12">
    <div class="col-md-12" style="display: flex;align-items: center;">
        <div class="col-md-7">
            <h2>Attendance Report</h2>
            <div class="subtitle">Attendance status report depending on different type parameter</div>
        </div>
        <div class="col-md-5">
            <div class="loader_report" id="loading">
                <img src="<?php echo base_url()?>/skin/img/loader.gif" style="height: 40px;">
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-5">
            <div class='card'>
                <h5>Select Report download type</h5>
                <div class="buttons">
                    <button class="pdf report" data-d_type="pdf"> <i data-lucide="file-text"></i> PDF </button>
                    <button class="excel report" data-d_type="excel"> <i data-lucide="square-equal"></i> EXCEL </button>
                    <button class="view report" data-d_type="view"> <i data-lucide="eye"></i> View </button>
                </div>
            </div>
            <div class='card'>
                <h5>Select organization</h5>
                <div class="radio-tabs">
                    <input type="radio" name="orgTab" id="all" value="All" checked>
                    <label for="all">All</label>

                    <input type="radio" name="orgTab" id="egen" value="E-GEN">
                    <label for="egen">E-GEN</label>

                    <input type="radio" name="orgTab" id="ipag" value="IPAG">
                    <label for="ipag">IPAG</label>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class='card'>

                <h5>Filter's</h5>
                <div class="filters">
                    <div class="filter-group">
                        <label>Date Range</label>
                        <input type="text" id="dateRange" placeholder="Select date range">
                    </div>
                    <div class="filter-group">
                        <label>Employee</label>
                        <select name="employee" id="employee">
                            <option value="">Individual Employee</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class='card'>
                <h5>Report Type</h5>
                <div class="status-buttons">
                    <input type="radio" id="status-all" name="report_type" value="all">
                    <label for="status-all" class="all"><i data-lucide="list"></i> ALL</label>

                    <input type="radio" id="status-late-in" name="report_type" value="late-in">
                    <label for="status-late-in" class="late-in"><i data-lucide="clock"></i> LATE IN</label>

                    <input type="radio" id="status-early-leave" name="report_type" value="early-leave">
                    <label for="status-early-leave" class="early-leave"><i data-lucide="log-out"></i> EARLY
                        LEAVE</label>

                    <input type="radio" id="status-late-times" name="report_type" value="late-times">
                    <label for="status-late-times" class="late-times"><i data-lucide="timer"></i> Late In Times</label>

                    <input type="radio" id="status-lwp" name="report_type" value="lwp">
                    <label for="status-lwp" class="lwp"><i data-lucide="user-x"></i> LWP/ABSENT</label>

                    <input type="radio" id="status-duty-hour" name="report_type" value="duty-hour">
                    <label for="status-duty-hour" class="duty-hour"><i data-lucide="briefcase"></i> DUTY HOUR</label>

                    <input type="radio" id="status-duty-hour-details" name="report_type" value="duty-hour-details">
                    <label for="status-duty-hour-details" class="duty-hour-details"><i data-lucide="file-text"></i> DUTY
                        HOUR (DETAILS)</label>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Report Modal -->
<div id="reportModal" style="display:none;">
    <div id="modalBackdrop"
        style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:999;"></div>
    <div
        style="position:fixed;top:50%;left:50%;transform:translate(-50%, -50%);background:white;padding:20px;max-width:90%;max-height:90%;overflow:auto;z-index:1000;border-radius:12px;">
        <button onclick="closeModal()"
            style="float:right;background:red;color:white;border:none;padding:5px 10px;border-radius:5px;">X</button>
        <div id="modalContent"></div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
$()
// Init date picker
flatpickr("#dateRange", {
    mode: "range",
    dateFormat: "Y-m-d",
    defaultDate: [new Date().toISOString().split('T')[0], new Date().toISOString().split('T')[0]]
});

// Org radio tabs
document.querySelectorAll('input[name="orgTab"]').forEach(radio => {
    radio.addEventListener("change", () => {
        console.log("Selected Org:", radio.value);
    });
});

// Status radio
document.querySelectorAll('input[name="status"]').forEach(radio => {
    radio.addEventListener("change", () => {
        console.log("Selected Status:", radio.value);
    });
});

// Load Lucide icons
lucide.createIcons();
</script>

<!-- jsPDF for PDF generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- SheetJS (xlsx) for Excel export -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>


<script>
$(document).ready(function() {
    $('.report').click(function() {
        var doc_type = $(this).data('d_type');
        var report_type = $('input[name="report_type"]:checked').val();
        var org = $('input[name="orgTab"]:checked').val();
        var date_range = $('#dateRange').val();
        var employee = $('select[name="employee"]').val();

        if (!report_type) {
            alert('Please select a report type');
            return false;
        }
        if (!org) {
            alert('Please select an organization');
            return false;
        }

        $('#loading').show();

        $.ajax({
            url: '<?php echo base_url('admin/attendance/generate_report/'); ?>' + doc_type +
                '/' + report_type + '/' + org + '/' + date_range + '/' + employee,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#loading').hide();

                if (doc_type.toLowerCase() == 'pdf') {
                    const {
                        jsPDF
                    } = window.jspdf;
                    const doc = new jsPDF();

                    doc.text("Attendance Report", 10, 10);
                    doc.setFontSize(10);
                    doc.text(response.replace(/<[^>]+>/g, ''), 10, 20); // Remove HTML tags

                    doc.save('attendance_report.pdf');
                } else if (doc_type.toLowerCase() == 'excel') {
                    let worksheet = XLSX.utils.table_to_sheet($('<div>' + response +
                        '</div>').find('table')[0]);
                    let workbook = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Report");

                    XLSX.writeFile(workbook, 'attendance_report.xlsx');
                } else if (doc_type.toLowerCase() == 'view') {
                    $('#modalContent').html(response);
                    $('#reportModal').show();
                }
            }
        });
    });
});
</script>