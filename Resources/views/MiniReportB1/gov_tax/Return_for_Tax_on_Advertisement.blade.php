@extends('layouts.app')

@section('title', __('Return for Tax on Advertisement'))

@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")
@include('minireportb1::MiniReportB1.components.reportheader', [
        'report_name' => 'ពន្ធលើផ្ទាំងផ្សាពវផ្សាយ | Return for Tax on Advertisement'
    ])

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card mb-4">
                <div class="card-body ad-tax-form">
                    <div class="ad-tax-header">
                        <div class="left-header">
                            <div class="khmer-text">ក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ</div>
                            <div class="english-text">MINISTRY OF ECONOMY AND FINANCE</div>
                            <div class="khmer-text mt-2">អគ្គនាយកដ្ឋានពន្ធដារ</div>
                            <div class="english-text">GENERAL DEPARTMENT OF TAXATION</div>
                        </div>
                        <div class="center-header">
                            <img src="{{ asset('modules/minireportb1/img/cambodia-emblem.png') }}" alt="Cambodia Emblem" class="cambodia-emblem">
                        </div>
                        <div class="right-header">
                            <div class="kingdom-header">
                                <div class="khmer-text">ព្រះរាជាណាចក្រកម្ពុជា</div>
                                <div class="english-text">KINGDOM OF CAMBODIA</div>
                                <div class="khmer-text mt-1">ជាតិ សាសនា ព្រះមហាក្សត្រ</div>
                                <div class="english-text">NATION RELIGION KING</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-title text-center my-4">
                        <h4 class="khmer-title">លិខិតប្រកាសពន្ធលើផ្ទាំងផ្សាពវផ្សាយ</h4>
                        <h5 class="english-title">RETURN FOR TAX ON ADVERTISEMENT</h5>
                    </div>

                    <div class="reset-btn-container text-center mb-3">
                        <button type="button" class="btn btn-danger" id="reset-form">
                            <i class="fas fa-sync-alt mr-1"></i> RESET FORM
                        </button>
                    </div>
                    
                    <!-- Section I: Enterprise Information -->
                    <div class="section-box mb-4">
                        <div class="section-header">
                            <span class="section-number">I</span>
                            <span class="section-title">ព័ត៌មានសហគ្រាស / ENTERPRISE INFORMATION</span>
                        </div>
                        <div class="section-content">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">លេខអត្តសញ្ញាណកម្មសារពាណិជ្ជកម្ម / TAX IDENTIFICATION NUMBER (TIN)</label>
                                    <div class="tin-input">
                                        <input type="text" class="form-control tin-part" maxlength="4">
                                        <span class="separator">-</span>
                                        <input type="text" class="form-control tin-part" maxlength="6">
                                        <span class="separator">-</span>
                                        <input type="text" class="form-control tin-part" maxlength="5">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ឈ្មោះអ្នករក្សាសហគ្រាស / NAME OF ENTERPRISE OWNER</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ឈ្មោះសហគ្រាស / NAME OF ENTERPRISE</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">សកម្មភាពអាជីវកម្ម / BUSINESS ACTIVITY</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">ឈ្មោះសហគ្រាសជាអក្សរឡាតាំង / NAME OF ENTERPRISE IN LATIN</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">អាសយដ្ឋាន ផ្ទះលេខ / ADDRESS NO.</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ផ្លូវ / STREET</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ភូមិ / VILLAGE</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">ឃុំ/សង្កាត់ / COMMUNE</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ក្រុង/ស្រុក/ខណ្ឌ / DISTRICT</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ខេត្ត/រាជធានី / PROVINCE/CITY</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">ទូរស័ព្ទចល័ត/ទូរស័ព្ទជារ៉ាក់ / MOBILE PHONE/TELEPHONE</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">សារអេឡិចត្រូនិក / E-MAIL</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section II: Total Tax Amount -->
                    <div class="section-box mb-4">
                        <div class="section-header">
                            <span class="section-number">II</span>
                            <span class="section-title">សរុបចំនួនពន្ធ / TOTAL TAX AMOUNT</span>
                        </div>
                        <div class="section-content">
                            <div class="table-responsive">
                                <table class="table table-bordered tax-table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th rowspan="2" class="text-center align-middle" style="width: 60px;">ល.រ.<br>NO.</th>
                                            <th rowspan="2" class="text-center align-middle">ប្រភេទផ្ទាំងផ្សាពវផ្សាយ<br>TYPE OF ADVERTISING BOARD</th>
                                            <th class="text-center align-middle" style="width: 200px;">ចំនួនពន្ធត្រូវបង់<br>TAX AMOUNT TO BE PAID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">១</td>
                                            <td>ស្លាកអាជីវកម្មដាក់<br>BUSINESS LABEL</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">៛</span>
                                                    </div>
                                                    <input type="text" class="form-control text-right tax-amount" data-row="1" value="0.00">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">២</td>
                                            <td>ផ្ទាំងអក្សរ ឬផ្ទាំងរូបភាពសម្រាប់គោលបំណងផ្សាពវជាតិជកម្ម<br>POSTERS OF PICTURE OR VIDEO FOR ADVERTISING PURPOSES</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">៛</span>
                                                    </div>
                                                    <input type="text" class="form-control text-right tax-amount" data-row="2" value="0.00">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">៣</td>
                                            <td>បណ្ណាប្រកាសធ្វើអំពីក្រដាសស្រាល<br>COMMERCIAL ADVERTISEMENTS MADE OF PLAIN PAPER</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">៛</span>
                                                    </div>
                                                    <input type="text" class="form-control text-right tax-amount" data-row="3" value="0.00">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">៤</td>
                                            <td>បណ្ណាប្រកាសធ្វើអំពីចំហស្រា កំណាត់សំពត់ ឬវត្ថុផ្សេងៗ<br>COMMERCIAL ADVERTISEMENTS MADE OF RUBBER, FABRIC STRIP OR OTHER MATERIALS</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">៛</span>
                                                    </div>
                                                    <input type="text" class="form-control text-right tax-amount" data-row="4" value="0.00">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">៥</td>
                                            <td>ពន្ធបន្ថែម និងការប្រាក់<br>ADDITIONAL TAX AND INTEREST</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">៛</span>
                                                    </div>
                                                    <input type="text" class="form-control text-right tax-amount" data-row="5" value="0.00">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-right">
                                                <span class="khmer-text">សូមភ្ជាប់បញ្ជីព័ត៌មានអំពីផ្ទាំងផ្សាពវផ្សាយ</span><br>
                                                <span class="english-text">PLEASE ATTACH LIST OF ADVERTISING BOARD INFORMATION</span>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">៛</span>
                                                    </div>
                                                    <input type="text" class="form-control text-right tax-amount" data-row="6" value="0.00">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="text-right font-weight-bold">
                                                <span class="khmer-text">សរុប</span><br>
                                                <span class="english-text">TOTAL</span>
                                            </td>
                                            <td class="font-weight-bold" id="total-tax-amount">
                                                <div class="p-2 text-right">៛ 0.00</div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section III: Declaration -->
                    <div class="section-box mb-4">
                        <div class="section-header">
                            <span class="section-number">III</span>
                            <span class="section-title">សច្ចាប័នប្រកាស / DECLARATION</span>
                        </div>
                        <div class="section-content">
                            <div class="declaration-box p-3 mb-4">
                                <p class="mb-2 khmer-text">
                                    យើងខ្ញុំបានពិនិត្យរក្សាទុកឆ្លងឆ្នាំ ឆ្នាំទាំងអស់ចៅក្នុងលិខិតប្រកាសចុះ និងរាងឧបសម្ព័ន្ធភ្ជាប់ជាមួយ។ យើងខ្ញុំមានឯកសារបញ្ជាក់ខ្លឹមសារលាស់ ត្រឹមត្រូវ ពេញលេញ ដើម្បីធានាបានថា ព័ត៌មានទាំងអស់ចៅក្នុងលិខិតប្រកាសចុះ ពិតជាត្រឹមត្រូវតាមពិតដូច្នេះ ហើយគ្មានប្រតិបត្តិការណាមួយមិនបានប្រកាសចៅោះចៅ។ យើងខ្ញុំសូមទទួលខុសត្រូវទាំងត្រឹមត្រួចំពោះមុខច្បាប់ទាំងឡាយជាធរមាន ប្រសិនបើព័ត៌មានណាមួយមានការក្លែងបន្លំ។
                                </p>
                                <p class="english-text">
                                    WE HAVE EXAMINED ALL ITEMS ON THIS RETURN AND THE ANNEXES ATTACHED HERE WITH. WE HAVE CLEAR, CORRECT AND FULL SUPPORTING DOCUMENTS TO ENSURE THAT ALL INFORMATION ON THIS RETURN IS TRUE AND ACCURATE AND THERE IS NO BUSINESS OPERATION UNDECLARED. WE ARE FULLY RESPONSIBLE DUE TO THE EXISTING LAWS FOR ANY FALSIFIED INFORMATION.
                                </p>
                            </div>
                            
                            <div class="row signature-section mt-5">
                                <div class="col-md-4">
                                    <div class="date-field mb-3">
                                        <label class="form-label">កាលបរិច្ឆេទ / DATE</label>
                                        <div class="date-inputs d-flex">
                                            <input type="text" class="form-control day-input mr-2" placeholder="DD" maxlength="2">
                                            <input type="text" class="form-control month-input mr-2" placeholder="MM" maxlength="2">
                                            <input type="text" class="form-control year-input" placeholder="YYYY" maxlength="4">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="signature-box text-center">
                                        <div class="signature-line mb-2"></div>
                                        <div class="signature-label">
                                            <span class="khmer-text">ហត្ថលេខា ឈ្មោះ និងត្រា</span><br>
                                            <span class="english-text">SIGNATURE, NAME AND SEAL</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="page-number text-right mb-3">
                        <span>ទំព័រ 1/2</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ad-tax-form {
        font-family: 'Khmer OS', 'Khmer OS System', 'Khmer OS Battambang', Arial, sans-serif;
        color: #000;
    }

    .ad-tax-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .left-header, .right-header {
        width: 30%;
    }

    .center-header {
        width: 40%;
        text-align: center;
    }

    .cambodia-emblem {
        width: 100px;
        height: auto;
    }

    .khmer-text {
        font-weight: 600;
    }

    .english-text {
        font-size: 0.9em;
        color: #444;
    }

    .kingdom-header {
        text-align: right;
    }

    .form-title {
        border-bottom: 1px solid #ddd;
        padding-bottom: 15px;
    }
    
    .section-box {
        border: 1px solid #000;
    }

    .section-header {
        background-color: #eee;
        padding: 8px 15px;
        border-bottom: 1px solid #000;
        font-weight: bold;
        display: flex;
        align-items: center;
    }

    .section-number {
        display: inline-block;
        width: 25px;
        height: 25px;
        line-height: 25px;
        text-align: center;
        border-radius: 50%;
        background-color: #fff;
        border: 1px solid #000;
        margin-right: 10px;
    }

    .section-content {
        padding: 15px;
    }
    
    .tin-input {
        display: flex;
        align-items: center;
    }
    
    .tin-input input {
        text-align: center;
    }
    
    .tin-input .separator {
        margin: 0 5px;
        font-weight: bold;
    }
    
    .tin-part:first-child {
        width: 80px;
    }
    
    .tin-part:nth-child(3) {
        width: 120px;
    }
    
    .tin-part:last-child {
        width: 100px;
    }
    
    .tax-table th, .tax-table td {
        vertical-align: middle;
    }
    
    .declaration-box {
        background-color: #f9f9f9;
        border: 1px dashed #999;
        border-radius: 4px;
    }
    
    .signature-line {
        height: 1px;
        background-color: #000;
        width: 80%;
        margin: 0 auto;
    }
    
    .day-input, .month-input {
        width: 60px;
    }
    
    .year-input {
        width: 80px;
    }
    
    @media print {
        .card {
            border: none;
        }
        .card-body {
            padding: 0;
        }
        .reset-btn-container {
            display: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate tax totals
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.tax-amount').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('total-tax-amount').innerHTML = '<div class="p-2 text-right">៛ ' + total.toFixed(2) + '</div>';
    }
    
    // Add event listeners to tax amount inputs
    document.querySelectorAll('.tax-amount').forEach(input => {
        input.addEventListener('input', function(e) {
            // Allow only numbers and decimal point
            this.value = this.value.replace(/[^0-9.]/g, '');
            calculateTotal();
        });
    });
    
    // Reset form button
    document.getElementById('reset-form').addEventListener('click', function() {
        if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
            document.querySelectorAll('.tax-amount').forEach(input => {
                input.value = '0.00';
            });
            calculateTotal();
            
            // Reset other form fields
            document.querySelectorAll('input:not(.tax-amount)').forEach(input => {
                input.value = '';
            });
        }
    });
    
    // Auto-tab for date inputs
    function setupAutoTab(selector) {
        const inputs = document.querySelectorAll(selector);
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length >= this.maxLength && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            
            // Allow only numbers
            input.addEventListener('keypress', function(e) {
                if (e.key < '0' || e.key > '9') {
                    e.preventDefault();
                }
            });
        });
    }
    
    setupAutoTab('.day-input, .month-input, .year-input');
    setupAutoTab('.tin-part');
});
</script>
@endsection