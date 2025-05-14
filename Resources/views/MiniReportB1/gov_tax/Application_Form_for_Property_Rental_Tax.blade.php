@extends('layouts.app')

@section('title', __('Application Form for Property Rental Tax'))

@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")
@include('minireportb1::MiniReportB1.components.reportheader', [
        'report_name' => 'Application Form for Property Rental Tax'
    ])
<div class="container-fluid" style="align-items: center;">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-body prr-form">
                    <div class="prr-header">
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
                            <div class="form-number">
                                <div class="khmer-text">ទំរង់ FORM PRR 02</div>
                            </div>
                            <div class="form-number-box">
                                <div class="form-no">សំណុំបែបបទលេខ</div>
                                <div class="english-text">FORM NUMBER:</div>
                                <div class="form-input-box"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-title mt-4 mb-4 text-center">
                        <h4 class="khmer-title">ពាក្យសុំសំរាប់ពន្ធលើការជួលអចលនវត្ថុ</h4>
                        <h5 class="english-title">Application Form for Property Rental Tax</h5>
                    </div>

                    <div class="section-box mb-4">
                        <div class="section-header">
                            <span class="section-number">I</span>
                            <span class="section-title">ព័ត៌មានអ្នកដាក់ពាក្យ / Applicant Info</span>
                        </div>
                        <div class="section-content">
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <div class="form-check-item">
                                        <input type="checkbox" class="form-check-input" id="resident">
                                        <label class="form-check-label" for="resident">និវាសនជន (Resident)</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check-item">
                                        <input type="checkbox" class="form-check-input" id="non-resident">
                                        <label class="form-check-label" for="non-resident">អនិវាសនជន (Non-resident)</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check-item">
                                        <input type="checkbox" class="form-check-input" id="pe">
                                        <label class="form-check-label" for="pe">PE (Branch)</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ឈ្មោះម្ចាស់អចលនវត្ថុ / Owner Full Name</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ឈ្មោះជាភាសាអង់គ្លេស / Name in Latin and English</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">ប្រភេទអាជីវកម្ម / Business Type</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">លេខអត្តសញ្ញាណកម្មសារពើពន្ធ / TIN</label>
                                    <div class="tin-input">
                                        <input type="text" class="form-control">
                                        <span class="separator">-</span>
                                        <input type="text" class="form-control">
                                        <input type="text" class="form-control">
                                        <input type="text" class="form-control">
                                        <input type="text" class="form-control">
                                        <input type="text" class="form-control">
                                        <input type="text" class="form-control">
                                        <input type="text" class="form-control">
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-box mb-4">
                        <div class="section-header">
                            <span class="section-number">II</span>
                            <span class="section-title">ព័ត៌មានអចលនវត្ថុ និងការជួល / Property and Rental Information</span>
                        </div>
                        <div class="section-content">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ប្រភេទកម្មសិទ្ធិ / Property Type</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ផ្ទៃក្រឡានៃអចលនវត្ថុ / Property Area (m²)</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">អាសយដ្ឋានអចលនវត្ថុ / Property Address</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">ខេត្ត/ក្រុង / Province/City</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ស្រុក/ខណ្ឌ / District</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ឃុំ/សង្កាត់ / Commune</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-box mb-4">
                        <div class="section-header">
                            <span class="section-number">III</span>
                            <span class="section-title">ព័ត៌មានអំពីការជួល / Rental Details</span>
                        </div>
                        <div class="section-content">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">កាលបរិច្ឆេទចាប់ផ្តើម / Start Date</label>
                                    <div class="date-input-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control day-input" placeholder="ថ្ងៃ/D" maxlength="2">
                                            <input type="text" class="form-control month-input" placeholder="ខែ/M" maxlength="2">
                                            <input type="text" class="form-control year-input" placeholder="ឆ្នាំ/Y" maxlength="4">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">កាលបរិច្ឆេទបញ្ចប់ / End Date</label>
                                    <div class="date-input-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control day-input" placeholder="ថ្ងៃ/D" maxlength="2">
                                            <input type="text" class="form-control month-input" placeholder="ខែ/M" maxlength="2">
                                            <input type="text" class="form-control year-input" placeholder="ឆ្នាំ/Y" maxlength="4">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">រយៈពេល (ខែ) / Duration (Months)</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ប្រាក់ជួលប្រចាំខែ / Monthly Rental (USD)</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ប្រាក់ជួលប្រចាំខែ / Monthly Rental (KHR)</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-box mb-4">
                        <div class="section-header">
                            <span class="section-number">IV</span>
                            <span class="section-title">ឯកសារភ្ជាប់ / Attachment check-list</span>
                        </div>
                        <div class="section-content">
                            <ul class="attachment-list">
                                <li class="attachment-item">
                                    <span class="number">1</span>
                                    <div class="attachment-desc">
                                        <span class="khmer-text">កិច្ចសន្យាជួលចុះហត្ថលេខាអ្នកជួល និងម្ចាស់ដីរឺអាគារ</span>
                                        <span class="english-text">ច្បាប់ដើម</span>
                                    </div>
                                </li>
                                <li class="attachment-item">
                                    <span class="number">2</span>
                                    <div class="attachment-desc">
                                        <span class="khmer-text">អត្តសញ្ញាណប័ណ្ណ ឬលិខិតឆ្លងដែន</span>
                                        <span class="english-text">ច្បាប់ចម្លង</span>
                                    </div>
                                </li>
                                <li class="attachment-item">
                                    <span class="number">3</span>
                                    <div class="attachment-desc">
                                        <span class="khmer-text">លិខិតប្រគល់សិទ្ធិ ឬលិខិតគ្រប់គ្រងអចលនវត្ថុ</span>
                                        <span class="english-text">ច្បាប់ចម្លង</span>
                                    </div>
                                </li>
                                <li class="attachment-item">
                                    <span class="number">4</span>
                                    <div class="attachment-desc">
                                        <span class="khmer-text">បង្កាន់ដៃបង់ពន្ធប៉ាតង់ឆ្នាំចុងក្រោយ</span>
                                        <span class="english-text">ច្បាប់ចម្លង</span>
                                    </div>
                                </li>
                                <li class="attachment-item">
                                    <span class="number">5</span>
                                    <div class="attachment-desc">
                                        <span class="khmer-text">ប័ណ្ណប៉ាតង់ (ករណីអាជីវកម្ម)</span>
                                        <span class="english-text">ច្បាប់ចម្លង</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="page-number text-right mb-2">ទំព័រ 1/2</div>

                    <!-- Page 2 -->
                    <div class="page-break"></div>

                    <div class="section-box mb-4 mt-5">
                        <div class="section-header">
                            <span class="section-number">V</span>
                            <span class="section-title">សេចក្តីថ្លែងសច្ចាប័ន / Declaration</span>
                        </div>
                        <div class="section-content">
                            <p class="declaration-text">
                                ខ្ញុំសូមធានាអះអាងថាព័ត៌មានទាំងអស់ដែលបានផ្តល់ជូនក្នុងពាក្យសុំនេះ (រួមទាំងឯកសារភ្ជាប់) ពិតជាត្រឹមត្រូវ និងពិតប្រាកដ។
                                <br>
                                <span class="english-text">I declare that the information provided in this application including all attachments are true and correct.</span>
                            </p>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="date-stamp-box">
                                        <div class="date-label">កាលបរិច្ឆេទ / Date:</div>
                                        <div class="date-input-control">
                                            <input type="text" class="form-control day-input" placeholder="ថ្ងៃ/D" maxlength="2">
                                            <input type="text" class="form-control month-input" placeholder="ខែ/M" maxlength="2">
                                            <input type="text" class="form-control year-input" placeholder="ឆ្នាំ/Y" maxlength="4">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="signature-box text-center">
                                        <div class="signature-line"></div>
                                        <div class="signature-label">ហត្ថលេខានិងឈ្មោះ / Signature and name</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-box mb-4">
                        <div class="section-header official-use">
                            <span class="section-title">សម្រាប់មន្ត្រីពន្ធដារប៉ុណ្ណោះ / For tax official use only</span>
                        </div>
                        <div class="section-content">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ការិយាល័យសារពើពន្ធ / Tax Office</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">កាលបរិច្ឆេទ / Date</label>
                                    <div class="date-input-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control day-input" placeholder="ថ្ងៃ/D" maxlength="2">
                                            <input type="text" class="form-control month-input" placeholder="ខែ/M" maxlength="2">
                                            <input type="text" class="form-control year-input" placeholder="ឆ្នាំ/Y" maxlength="4">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="row signature-row">
                                        <div class="col-md-6">
                                            <div class="signature-box text-center">
                                                <div class="signature-label">អ្នកទទួលពាក្យសុំ / Tax officer</div>
                                                <div class="signature-line mt-4"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="signature-box text-center">
                                                <div class="signature-label">ប្រធាន / Director</div>
                                                <div class="signature-line mt-4"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="signature-box text-center">
                                        <div class="signature-label">ប្រធាននាយកដ្ឋាន / Head of Division</div>
                                        <div class="signature-line mt-4"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="signature-box text-center">
                                        <div class="signature-label">ប្រធានសាខា / Head of Branch</div>
                                        <div class="signature-line mt-4"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="page-number text-right">ទំព័រ 2/2</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .prr-form {
        font-family: 'Khmer OS', 'Khmer OS System', 'Khmer OS Battambang', Arial, sans-serif;
        color: #000;
    }

    .prr-header {
        display: flex;
        justify-content: space-between;
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

    .form-number {
        text-align: right;
        font-weight: bold;
    }

    .form-number-box {
        border: 1px solid #000;
        padding: 5px;
        margin-top: 10px;
        text-align: center;
    }

    .form-input-box {
        border: 1px dashed #999;
        height: 25px;
        margin-top: 5px;
    }

    .form-title {
        border-bottom: 1px solid #000;
        padding-bottom: 15px;
    }

    .khmer-title {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .english-title {
        color: #444;
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

    .form-check-item {
        display: flex;
        align-items: center;
    }

    .form-check-input {
        margin-right: 5px;
    }

    .tin-input {
        display: flex;
        align-items: center;
    }

    .tin-input input {
        width: 40px;
        text-align: center;
    }

    .tin-input .separator {
        margin: 0 5px;
        font-weight: bold;
    }

    .date-input-group .input-group {
        flex-wrap: nowrap;
    }

    .day-input, .month-input {
        width: 60px;
        text-align: center;
    }

    .year-input {
        width: 80px;
        text-align: center;
    }

    .attachment-list {
        list-style-type: none;
        padding-left: 0;
    }

    .attachment-item {
        display: flex;
        margin-bottom: 12px;
    }

    .attachment-item .number {
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

    .attachment-desc {
        display: flex;
        flex-direction: column;
    }

    .page-break {
        page-break-after: always;
        margin-bottom: 20px;
        border-top: 1px dashed #ccc;
        padding-top: 20px;
    }

    .declaration-text {
        text-align: justify;
        padding: 10px;
        border: 1px dashed #000;
        background-color: #f9f9f9;
    }

    .date-stamp-box {
        display: flex;
        align-items: center;
    }

    .date-label {
        margin-right: 10px;
        font-weight: bold;
    }

    .signature-box {
        padding: 10px;
    }

    .signature-line {
        height: 1px;
        background-color: #000;
        width: 80%;
        margin: 30px auto 10px;
    }

    .signature-label {
        font-size: 0.9em;
    }

    .official-use {
        background-color: #f2f2f2;
        color: #555;
    }

    .signature-row {
        margin-top: 20px;
    }

    .page-number {
        font-size: 0.8em;
        color: #555;
    }

    @media print {
        .card {
            border: none;
        }
        .card-body {
            padding: 0;
        }
        .page-break {
            page-break-after: always;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-tabbing between date fields
        const dateInputs = document.querySelectorAll('.day-input, .month-input, .year-input');
        
        dateInputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.length >= this.maxLength) {
                    // Find the next input in the same date group
                    const parent = this.closest('.date-input-group') || this.closest('.date-stamp-box');
                    if (parent) {
                        const inputs = Array.from(parent.querySelectorAll('input'));
                        const currentIndex = inputs.indexOf(this);
                        if (currentIndex < inputs.length - 1) {
                            inputs[currentIndex + 1].focus();
                        }
                    }
                }
            });
            
            // Numbers only validation
            input.addEventListener('keypress', function(e) {
                if (e.key < '0' || e.key > '9') {
                    e.preventDefault();
                }
            });
        });
        
        // Handle TIN input auto-tab
        const tinInputs = document.querySelectorAll('.tin-input input');
        
        tinInputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length >= this.maxLength && index < tinInputs.length - 1) {
                    tinInputs[index + 1].focus();
                }
            });
            
            // Numbers only validation
            input.addEventListener('keypress', function(e) {
                if (e.key < '0' || e.key > '9') {
                    e.preventDefault();
                }
            });
        });
        
        // Make resident/non-resident checkboxes mutually exclusive
        const checkboxes = document.querySelectorAll('#resident, #non-resident, #pe');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    checkboxes.forEach(cb => {
                        if (cb !== this) cb.checked = false;
                    });
                }
            });
        });
    });
</script>
@endsection
