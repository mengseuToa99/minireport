@extends('minireportb1::layouts.master2')

<link rel="stylesheet" href="{{ asset('modules/minireportb1/css/module.css') }}">

@section('content')
<div class="container-fluid py-4" style="color: azure; border: 1px solid black; width:600px; margin: 32px; justify-content:center; margin-left: 30%;margin-top: 10%; padding: 8px;" >
    <div class="card shadow-lg mt-4 border-0" style="max-width: 600px; margin-left: auto; margin-right: auto; border-radius: 20px;">
        <div class="card-header bg-gradient-primary text-white py-4" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
            <h5 class="mb-0 fw-bold text-center">Save View Configuration</h5>
        </div>
        <div class="card-body p-5">
            <form id="createFileForm">
                @csrf
                <div class="form-group mb-4">
                    <label for="fileName" class="form-label fw-semibold text-muted">File Name</label>
                    <input type="text" class="form-control form-control-lg rounded-pill" id="fileName" name="file_name" required placeholder="Enter file name">
                </div>
                <div class="form-group mb-4">
                    <label for="parentFolder" class="form-label fw-semibold text-muted">Select Folder</label>
                    <select class="form-control form-control-lg rounded-pill" id="parentFolder" name="parent_id" required>
                        <option value="">-- Select a Folder --</option>
                    </select>
                </div>
                <div class="form-group mb-4">
                    <label for="reportType" class="form-label fw-semibold text-muted">Select Report Type</label>
                    <select class="form-control form-control-lg rounded-pill" id="reportType" name="report_type" required>
                        <option value="">-- Select Report Type --</option>
                        <option value="payroll">Payroll</option>
                        <option value="saleReport">Sale</option>
                        <option value="purchaseReport">Purchase</option>
                        <option value="productReport">Product</option>
                        <option value="stockReport">Stock</option>
                        <option value="payroll1">Pay Components</option>
                        <option value="payroll2">Payroll Groups</option>
                        <option value="expenseReport">Expense</option>
                        <option value="followup">Follow Up</option>
                        <option value="employee">Employee</option>
                        <option value="customer">Customer</option>
                        <option value="supplier">Supplier</option>


                    </select>
                </div>
                <div class="text-end">
                    <button type="button" class="btn" onclick="navigateToReport()">Modify</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('#reportTable').DataTable({
        buttons: [{
            text: '<i class="fa fa-save" aria-hidden="true"></i> ' + (LANG.save || 'Save'),
            className: 'tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right tw-ml-4 tw-mb-8',
            action: function(e, dt, node, config) {
                $('html, body').animate({
                    scrollTop: $('#createFileForm').offset().top
                }, 500);
            }
        }]
    });

    loadFolders();

    function loadFolders() {
        $.ajax({
            url: '{{ route("minireport_getfolder") }}',
            method: 'GET',
            success: function(response) {
                console.log('Folders response:', response);
                if (response.success) {
                    var select = $('#parentFolder');
                    select.empty();
                    select.append('<option value="">-- Select a Folder --</option>');
                    response.data.forEach(function(folder) {
                        if (folder.type === 'report_section') {
                            select.append(new Option(folder.folder_name, folder.id));
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    }

    window.navigateToReport = function() {
        const fileName = $('#fileName').val();
        const folderId = $('#parentFolder').val();
        const folderName = $('#parentFolder option:selected').text();
        const reportType = $('#reportType').val();

        if (!fileName || !folderId || !reportType) {
            alert('Please fill in all required fields.');
            return;
        }

        const fileData = {
            fileName: fileName,
            folderId: folderId,
            folderName: folderName,
            reportType: reportType
        };
        localStorage.setItem('pendingFile', JSON.stringify(fileData));

        const reportRoutes = {
            'payroll': '{{ route('minireportb1.payroll') }}',
            'saleReport': '{{ route('minireportb1.saleReport') }}',
            'purchaseReport': '{{ route('minireportb1.purchaseReport') }}',
            'productReport': '{{ route('minireportb1.productReport') }}',
            'payroll1': '{{ route('minireportb1.payroll1') }}',
            'payroll2': '{{ route('minireportb1.payroll2') }}',
            'expenseReport': '{{ route('minireportb1.expenseReport') }}',
            'followup' : '{{ route('minireportb1.followupReport') }}',
            'employee' : '{{ route('minireportb1.employee') }}',
            'customer' : '{{ route('minireportb1.customer') }}',
            'supplier' : '{{ route('minireportb1.supplier') }}',


        };

        if (reportType && reportRoutes[reportType]) {
            window.location.href = reportRoutes[reportType];
        } else {
            alert('Invalid report type selected.');
        }
    };
});
</script>
@endsection