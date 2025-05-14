@extends('layouts.app')

@section('title', __('minireportb1::minireportb1.p101_tax_form'))

@section('content')
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")
@include('minireportb1::MiniReportB1.components.reportheader', [
        'report_name' => 'ទ្រមង់បង់្របក់ពន� P 101'
    ])
<div class="container-fluid p101-form-container">
    <div class="row">
        <div class="col-md-12">
            <div class="p101-form-wrapper" id="p101-printable-area">
                <div class="p101-header">
                    <div class="logo-section">
                        <img src="{{ asset('modules/minireportb1/img/cambodia-emblem.png') }}" alt="Cambodia Emblem" class="cambodia-emblem">
                    </div>
                    <div class="header-text">
                        <div class="khmer-text">ក្រសួងសេដ្ឋកិច្ចនិងហិរញ្ញវត្ថុ</div>
                        <div class="english-text">MINISTRY OF ECONOMY AND FINANCE</div>
                        <div class="khmer-text mt-2">អគ្គនាយកដ្ឋានពន្ធដារ</div>
                        <div class="english-text">GENERAL DEPARTMENT OF TAXATION</div>
                    </div>
                    <div class="form-title-section">
                        <div class="form-title">
                            <div class="p101-title">ទិដ្ឋាការអាករលើប្រាក់ចំណូល P 101</div>
                            <div class="form-version">(កំណែទម្រង់ទី8 / Form Version: V8)</div>
                        </div>
                        <div class="form-fields">
                            <div class="month-field">
                                <label>ប្រចាំខែ/Month</label>
                                <input type="text" class="form-control" name="month" id="tax-month">
                            </div>
                            <div class="year-field">
                                <label>ឆ្នាំ/Year</label>
                                <input type="text" class="form-control" name="year" id="tax-year">
                            </div>
                            <div class="date-field">
                                <label>កាលបរិច្ឆេទ/Date</label>
                                <div class="date-inputs">
                                    <input type="text" class="form-control date-part" maxlength="2" name="day">
                                    <input type="text" class="form-control date-part" maxlength="2" name="month_date">
                                    <input type="text" class="form-control date-part" maxlength="4" name="year_date">
                                </div>
                            </div>
                            <div class="barcode-field">
                                <label>កូដបារលេខទម្រង់</label>
                                <div class="barcode">Click bar code here!</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="department-selection">
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="large_taxpayer" id="large_taxpayer">
                            <span>អគ្គនាយកដ្ឋានពន្ធដារអ្នកជាប់ពន្ធធំ</span>
                        </label>
                        <span class="dept-english">Department of Large Taxpayer</span>
                    </div>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="tax_branch" id="tax_branch">
                            <span>សាខាពន្ធដារ</span>
                        </label>
                        <span class="dept-english">Branch of Taxation</span>
                    </div>
                </div>
                
                <div class="company-info">
                    <div class="company-name">
                        <label>ឈ្មោះសហគ្រាស/Name of company</label>
                        <input type="text" class="form-control" name="company_name" id="company_name">
                    </div>
                    <div class="company-name-latin">
                        <label>ឈ្មោះសហគ្រាសជាអក្សរឡាតាំង/Name of company in Latin</label>
                        <input type="text" class="form-control" name="company_name_latin" id="company_name_latin">
                    </div>
                    <div class="tin-number">
                        <label>លេខអត្តសញ្ញាណកម្មអាករលើតម្លៃបន្ថែម/Tax Identification Number/TIN</label>
                        <div class="tin-inputs">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_1">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_2">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_3">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_4">
                            <span class="tin-separator">-</span>
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_5">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_6">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_7">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_8">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_9">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_10">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_11">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_12">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_13">
                            <input type="text" maxlength="1" class="form-control tin-part" name="tin_14">
                        </div>
                    </div>
                </div>
                
                <div class="tax-table">
                    <table>
                        <thead>
                            <tr>
                                <th rowspan="2" class="col-no">ល.រ<br/>N°</th>
                                <th rowspan="2" class="col-tax-type">ប្រភេទពន្ធ<br/>Taxes type</th>
                                <th rowspan="2" class="col-amount">ចំនួនអាករ<br/>Tax Amount</th>
                                <th rowspan="2" class="col-account">លេខគណនី<br/>Account No</th>
                                <th rowspan="2" class="col-additional">ប្រាក់បន្ថែម<br/>Additional Tax</th>
                                <th rowspan="2" class="col-interest">ការប្រាក់<br/>Interest</th>
                                <th rowspan="2" class="col-additional-acc">លេខគណនីបន្ថែម<br/>Additional Acc</th>
                                <th rowspan="2" class="col-total">សរុបចំនួន<br/>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 39; $i++)
                            <tr>
                                <td class="col-no">{{ $i }}</td>
                                <td class="col-tax-type tax-type-{{ $i }}">
                                    @if ($i == 1)
                                        ពន្ធប៉ តង់
                                    @elseif ($i == 2)
                                        ពន្ធប្រថាប់ត្រាលើលក្ខន្តិកៈក្រុមហ៊ុន
                                    @elseif ($i == 3)
                                        ពន្ធប្រថាប់ត្រាបិទក្រុមហ៊ុន
                                    @elseif ($i == 4)
                                        ពន្ធប្រថាប់ត្រាលើការរំលាយក្រុមហ៊ុនបញ្ចូលគ្នា
                                    @elseif ($i == 5)
                                        ពន្ធប្រថាប់ត្រាផ្ទេរភាគហ៊ុនអត្រា ០.១%
                                    @elseif ($i == 6)
                                        ពន្ធប្រថាប់ត្រាផ្ទេរភាគហ៊ុនអត្រា ៤%
                                    @elseif ($i == 7)
                                        ពន្ធប្រថាប់ត្រាលើលទ្ធកម្មសាធារណៈអត្រា០.១%
                                    @elseif ($i == 8)
                                        ប្រាក់ពិន័យ និងការប្រាក់
                                    @elseif ($i == 9)
                                        កម្រៃសេវាចុះបញ្ជី ឬធ្វើបច្ចុប្បន្នភាព
                                    @elseif ($i == 10)
                                        កម្រៃសេវាពន្ធប្រថាប់ត្រា
                                    @elseif ($i == 11)
                                        ពន្ធលើប្រាក់បៀវត្ស
                                    @elseif ($i == 12)
                                        ពន្ធលើអត្ថប្រយោជន៍បន្ថែម
                                    @elseif ($i == 13)
                                        ប្រាក់រំដោះពន្ធលើប្រាក់ចំណូល
                                    @elseif ($i == 14)
                                        ពន្ធលើប្រាក់ចំណូលប្រចាំឆ្នាំ
                                    @elseif ($i == 15)
                                        ពន្ធបង់មុនលើការបែងចែកភាគលាភ
                                    @elseif ($i == 16)
                                        ពន្ធអប្បបរមា
                                    @elseif ($i == 17)
                                        ពន្ធកាត់ទុកលើសេវាកម្ម ១៥%
                                    @elseif ($i == 18)
                                        ពន្ធកាត់ទុកលើសួយសារ ១៥%
                                    @elseif ($i == 19)
                                        ពន្ធកាត់ទុកលើការប្រាក់ ១៥%
                                    @elseif ($i == 20)
                                        ពន្ធកាត់ទុកលើការប្រាក់ ៦%
                                    @elseif ($i == 21)
                                        ពន្ធកាត់ទុកលើការប្រាក់ ៤%
                                    @elseif ($i == 22)
                                        ពន្ធកាត់ទុកលើអនិវាសនជន (ការប្រាក់)
                                    @elseif ($i == 23)
                                        ពន្ធកាត់ទុកលើអនិវាសនជន (សួយសារថ្លៃឈ្នួល)
                                    @elseif ($i == 24)
                                        ពន្ធកាត់ទុកលើអនិវាសនជន (សេវាគ្រប់គ្រងបច្ចេកទេស)
                                    @elseif ($i == 25)
                                        ពន្ធកាត់ទុកលើអនិវាសនជន (ភាគលាភ)
                                    @elseif ($i == 26)
                                        ពន្ធកាត់ទុកលើថ្លៃឈ្នួល (រូបវន្តបុគ្គល)
                                    @elseif ($i == 27)
                                        ពន្ធកាត់ទុកលើថ្លៃឈ្នួល (នីតិបុគ្គល)
                                    @elseif ($i == 28)
                                        អាករលើតម្លៃបន្ថែម
                                    @elseif ($i == 29)
                                        អាករពិសេសលើទំនិញមួយចំនួន
                                    @elseif ($i == 30)
                                        អាករពិសេសលើសេវាមួយចំនួន
                                    @elseif ($i == 31)
                                        ពន្ធបន្ថែមចំពោះការរាំងស្ទះ
                                    @elseif ($i == 32)
                                        ចំណូលពីការលក់ឯកសារបោះពុម្ព
                                    @elseif ($i == 33)
                                        អាករសម្រាប់បំភ្លឺសាធារណៈ
                                    @elseif ($i == 34)
                                        ពន្ធលើមធ្យោបាយដឹកជញ្ជូន
                                    @elseif ($i == 35)
                                        អាករលើការស្នាក់នៅ
                                    @elseif ($i == 36)
                                        ពន្ធសត្វឃាត
                                    @elseif ($i == 37)
                                        ពន្ធលើផ្ទាំងផ្សព្វផ្សាយ - ស្លាកអាជីវកម្មដ្ឋាន
                                    @elseif ($i == 38)
                                        ពន្ធលើផ្ទាំងផ្សព្វផ្សាយ - ផ្ទាំងផ្សាយពាណិជ្ជកម្ម
                                    @elseif ($i == 39)
                                        កម្រៃសេវាភ្នាក់ងារពន្ធដារ
                                    @endif
                                </td>
                                <td class="col-amount"><input type="text" class="form-control tax-amount" name="tax_amount_{{ $i }}" data-row="{{ $i }}"></td>
                                <td class="col-account"><input type="text" class="form-control account-no" name="account_no_{{ $i }}" value="{{ $i < 10 ? '101'.str_pad($i, 2, '0', STR_PAD_LEFT) : '' }}"></td>
                                <td class="col-additional"><input type="text" class="form-control additional-tax" name="additional_tax_{{ $i }}" data-row="{{ $i }}"></td>
                                <td class="col-interest"><input type="text" class="form-control interest" name="interest_{{ $i }}" data-row="{{ $i }}"></td>
                                <td class="col-additional-acc"><input type="text" class="form-control additional-acc" name="additional_acc_{{ $i }}" value="{{ $i < 10 ? '102'.str_pad($i, 2, '0', STR_PAD_LEFT) : '' }}"></td>
                                <td class="col-total total-amount-{{ $i }}">0 R</td>
                            </tr>
                            @endfor
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="signature-section">
                                    <div class="signature">
                                        <span>ហត្ថលេខា និងឈ្មោះ៖</span>
                                        <span class="english-label">Signature and name</span>
                                    </div>
                                    <div class="signature-box"></div>
                                </td>
                                <td class="total-label">
                                    <span>សរុប</span>
                                    <span class="english-label">Total</span>
                                </td>
                                <td class="grand-total" id="grand-total">0 R</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-danger" id="reset-form">Reset Your Form</button>
                    <button type="button" class="btn btn-primary" id="print-form">Print Form</button>
                    <button type="button" class="btn btn-success" id="save-form">Save Form</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .p101-form-container {
        font-family: 'Khmer OS', 'Khmer OS System', 'Khmer OS Battambang', Arial, sans-serif;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .p101-form-wrapper {
        border: 1px solid #000;
        padding: 15px;
        background-color: #fff;
    }
    
    .p101-header {
        display: flex;
        align-items: flex-start;
        padding-bottom: 15px;
    }
    
    .logo-section {
        width: 100px;
    }
    
    .cambodia-emblem {
        width: 80px;
        height: auto;
    }
    
    .header-text {
        flex: 1;
        text-align: center;
    }
    
    .khmer-text {
        font-weight: bold;
        font-size: 16px;
    }
    
    .english-text {
        font-size: 12px;
        color: #333;
    }
    
    .form-title-section {
        width: 250px;
        border: 1px solid #ccc;
        padding: 10px;
        background-color: #f5f5f5;
    }
    
    .p101-title {
        font-weight: bold;
        font-size: 16px;
        text-align: center;
    }
    
    .form-version {
        font-size: 12px;
        text-align: center;
    }
    
    .form-fields {
        margin-top: 10px;
    }
    
    .month-field, .year-field, .date-field, .barcode-field {
        margin-bottom: 5px;
    }
    
    .month-field label, .year-field label, .date-field label, .barcode-field label {
        display: block;
        font-size: 12px;
    }
    
    .date-inputs {
        display: flex;
    }
    
    .date-part {
        width: 40px;
        margin-right: 5px;
        text-align: center;
    }
    
    .barcode {
        border: 1px dashed #999;
        padding: 10px;
        text-align: center;
        font-size: 12px;
        color: #666;
    }
    
    .department-selection {
        display: flex;
        margin-bottom: 15px;
    }
    
    .checkbox-group {
        margin-right: 30px;
    }
    
    .dept-english {
        display: block;
        font-size: 10px;
        color: #666;
    }
    
    .company-info {
        margin-bottom: 15px;
    }
    
    .company-name, .company-name-latin, .tin-number {
        margin-bottom: 10px;
    }
    
    .company-name label, .company-name-latin label, .tin-number label {
        display: block;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .tin-inputs {
        display: flex;
        align-items: center;
    }
    
    .tin-part {
        width: 30px;
        margin-right: 2px;
        text-align: center;
    }
    
    .tin-separator {
        margin: 0 5px;
        font-weight: bold;
    }
    
    .tax-table {
        width: 100%;
        margin-bottom: 20px;
    }
    
    .tax-table table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .tax-table th, .tax-table td {
        border: 1px solid #000;
        padding: 5px;
        font-size: 12px;
        text-align: center;
    }
    
    .tax-table th {
        background-color: #e6e6e6;
    }
    
    .col-no {
        width: 40px;
    }
    
    .col-tax-type {
        width: 250px;
        text-align: left;
    }
    
    .col-amount, .col-account, .col-additional, .col-interest, .col-additional-acc {
        width: 100px;
    }
    
    .col-total {
        width: 120px;
    }
    
    .tax-table input[type="text"] {
        width: 100%;
        border: none;
        text-align: right;
        padding: 2px 5px;
    }
    
    .signature-section {
        text-align: left;
        vertical-align: top;
    }
    
    .signature {
        margin-bottom: 5px;
    }
    
    .english-label {
        display: block;
        font-size: 10px;
        color: #666;
    }
    
    .signature-box {
        width: 200px;
        height: 50px;
        border: 1px solid #ccc;
        margin-top: 5px;
    }
    
    .total-label {
        text-align: right;
        font-weight: bold;
    }
    
    .grand-total {
        font-weight: bold;
        text-align: right;
    }
    
    .form-actions {
        text-align: right;
        margin-top: 20px;
    }
    
    @media print {
        .form-actions {
            display: none;
        }
        
        body {
            padding: 0;
            margin: 0;
        }
        
        .p101-form-container {
            padding: 0;
        }
        
        .p101-form-wrapper {
            border: none;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set default month and year values
        const currentDate = new Date();
        document.getElementById('tax-month').value = currentDate.getMonth() + 1;
        document.getElementById('tax-year').value = currentDate.getFullYear();
        
        // Auto-tab for TIN and date fields
        setupAutoTab('.tin-part');
        setupAutoTab('.date-part');
        
        // Calculate totals when input values change
        const taxAmountInputs = document.querySelectorAll('.tax-amount, .additional-tax, .interest');
        taxAmountInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                // Allow only numbers and decimal point
                this.value = this.value.replace(/[^0-9.]/g, '');
                calculateRowTotal(this.getAttribute('data-row'));
                calculateGrandTotal();
            });
        });
        
        // Reset form button
        document.getElementById('reset-form').addEventListener('click', function() {
            if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
                resetForm();
            }
        });
        
        // Print form button
        document.getElementById('print-form').addEventListener('click', function() {
            window.print();
        });
        
        // Save form button
        document.getElementById('save-form').addEventListener('click', function() {
            saveForm();
        });
    });
    
    function setupAutoTab(selector) {
        const inputs = document.querySelectorAll(selector);
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length >= this.maxLength && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            
            input.addEventListener('keydown', function(e) {
                // Allow backspace to go to previous field
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    }
    
    function calculateRowTotal(rowIndex) {
        const taxAmount = parseFloat(document.querySelector(`[name="tax_amount_${rowIndex}"]`).value) || 0;
        const additionalTax = parseFloat(document.querySelector(`[name="additional_tax_${rowIndex}"]`).value) || 0;
        const interest = parseFloat(document.querySelector(`[name="interest_${rowIndex}"]`).value) || 0;
        
        const total = taxAmount + additionalTax + interest;
        document.querySelector(`.total-amount-${rowIndex}`).textContent = formatCurrency(total);
    }
    
    function calculateGrandTotal() {
        let grandTotal = 0;
        
        for (let i = 1; i <= 39; i++) {
            const taxAmount = parseFloat(document.querySelector(`[name="tax_amount_${i}"]`).value) || 0;
            const additionalTax = parseFloat(document.querySelector(`[name="additional_tax_${i}"]`).value) || 0;
            const interest = parseFloat(document.querySelector(`[name="interest_${i}"]`).value) || 0;
            
            grandTotal += taxAmount + additionalTax + interest;
        }
        
        document.getElementById('grand-total').textContent = formatCurrency(grandTotal);
    }
    
    function formatCurrency(amount) {
        return amount.toFixed(0) + ' R';
    }
    
    function resetForm() {
        // Reset all form inputs
        document.querySelectorAll('input[type="text"]').forEach(input => {
            // Preserve account numbers
            if (!input.classList.contains('account-no') && !input.classList.contains('additional-acc')) {
                input.value = '';
            }
        });
        
        // Reset checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Reset all totals
        document.querySelectorAll('[class^="total-amount-"]').forEach(total => {
            total.textContent = '0 R';
        });
        
        // Reset grand total
        document.getElementById('grand-total').textContent = '0 R';
        
        // Reset month and year to current
        const currentDate = new Date();
        document.getElementById('tax-month').value = currentDate.getMonth() + 1;
        document.getElementById('tax-year').value = currentDate.getFullYear();
    }
    
    function saveForm() {
        const formData = {
            month: document.getElementById('tax-month').value,
            year: document.getElementById('tax-year').value,
            company_name: document.getElementById('company_name').value,
            company_name_latin: document.getElementById('company_name_latin').value,
            large_taxpayer: document.getElementById('large_taxpayer').checked,
            tax_branch: document.getElementById('tax_branch').checked,
            tax_items: []
        };
        
        // Collect all tax items
        for (let i = 1; i <= 39; i++) {
            const taxAmount = parseFloat(document.querySelector(`[name="tax_amount_${i}"]`).value) || 0;
            const accountNo = document.querySelector(`[name="account_no_${i}"]`).value;
            const additionalTax = parseFloat(document.querySelector(`[name="additional_tax_${i}"]`).value) || 0;
            const interest = parseFloat(document.querySelector(`[name="interest_${i}"]`).value) || 0;
            const additionalAcc = document.querySelector(`[name="additional_acc_${i}"]`).value;
            
            if (taxAmount > 0 || additionalTax > 0 || interest > 0) {
                formData.tax_items.push({
                    tax_type: i,
                    tax_amount: taxAmount,
                    account_number: accountNo,
                    additional_tax: additionalTax,
                    interesprocess_p101_tax_formt: interest,
                    additional_account: additionalAcc,
                    total_amount: taxAmount + additionalTax + interest
                });
            }
        }
        
        // Get TIN number
        formData.tin = '';
        document.querySelectorAll('.tin-part').forEach(input => {
            formData.tin += input.value;
        });
        
        // Send data to server
        fetch('{{ route("tax_gov_document") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Form saved successfully!');
            } else {
                alert('Error saving form: ' + data.msg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the form.');
        });
    }
</script>
@endsection
