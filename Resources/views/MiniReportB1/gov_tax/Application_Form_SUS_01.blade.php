<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Suspension/Resumption Form</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #f9f9f9;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .form-section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .section-header {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .form-row {
            display: flex;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .form-group {
            margin-bottom: 15px;
            flex: 1;
            min-width: 200px;
            margin-right: 10px;
        }
        .form-group:last-child {
            margin-right: 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        input[type="text"], input[type="number"], input[type="date"], select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .checkbox-group input[type="checkbox"] {
            margin-right: 5px;
        }
        .radio-group {
            display: flex;
            margin-bottom: 10px;
        }
        .radio-option {
            margin-right: 15px;
            display: flex;
            align-items: center;
        }
        .radio-option input {
            margin-right: 5px;
        }
        .signature-block {
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .signature-field {
            width: 30%;
            min-width: 150px;
            margin-bottom: 10px;
        }
        .signature-field label {
            font-size: 12px;
            text-align: center;
            margin-top: 5px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 30px;
            margin-bottom: 5px;
        }
        .declaration-text {
            margin-bottom: 15px;
            font-style: italic;
        }
        .khmer-text {
            font-family: 'Khmer OS', 'Khmer OS System', Arial, sans-serif;
        }
        .required-docs {
            margin-top: 15px;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
        }
        .officer-section {
            background-color: #f5f5f5;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .form-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        @media print {
            body {
                background-color: white;
            }
            .form-container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
@include("minireportb1::MiniReportB1.components.back_to_tax_dashboard_button")

    <div class="form-container">
        <div class="form-title khmer-text">
            ពាក្យស្នើសុំ<br>
            ផ្អាកដំណើរការសកម្មភាពអាជីវកម្មជាបណ្តោះអាសន្ន ឬ បើកដំណើរការអាជីវកម្មឡើងវិញ
        </div>
        
        <div class="form-section">
            <div class="section-header">១. ព័ត៌មានសហគ្រាស / Company information</div>
            <div class="form-row">
                <div class="form-group">
                    <label>ឈ្មោះសហគ្រាស / Name of company</label>
                    <input type="text" name="company_name">
                </div>
                <div class="form-group">
                    <label>ឈ្មោះសហគ្រាសជាអក្សរឡាតាំង / Name of company in Latin</label>
                    <input type="text" name="company_name_latin">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>លេខអត្តសញ្ញាណកម្មសារពើពន្ធ (TIN) / Tax Identification Number</label>
                    <input type="text" name="tin_number">
                </div>
                <div class="form-group">
                    <label>សកម្មភាពអាជីវកម្មចម្បង / Main Business Activity</label>
                    <input type="text" name="main_business">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <div class="section-header">២. ព័ត៌មានចាំបាច់សម្រាប់ការស្នើសុំ / Required information for request</div>
            
            <div class="form-group">
                <label>ក. តើសំណើសុំនេះជាការស្នើសុំថ្មី ឬ ស្នើសុំបន្ត? / Is this a new request or extension?</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" name="request_type" id="new_request" value="new">
                        <label for="new_request">សំណើសុំថ្មី / New request</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" name="request_type" id="extension" value="extension">
                        <label for="extension">សំណើសុំបន្ត / Extension</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" name="request_type" id="reopening" value="reopening">
                        <label for="reopening">បើកដំណើរការអាជីវកម្មឡើងវិញ / Reopening</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>ខ. តើសហគ្រាសស្នើសុំផ្អាកសកម្មភាពសម្រាប់រយៈពេលប៉ុន្មានខែ? (មិនលើសពី៦ខែ) / Suspension period (not exceeding 6 months)</label>
                <div class="form-row">
                    <div class="form-group">
                        <input type="number" name="months" min="1" max="6">
                        <small>ខែ / Months</small>
                    </div>
                    <div class="form-group">
                        <label>គិតចាប់ពី / Starting from</label>
                        <input type="text" name="start_date" placeholder="MM/YYYY">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>គ. លេខបារកូដលិខិតអនុញ្ញាតផ្អាក / Barcode of suspension permit</label>
                <input type="text" name="barcode">
                <small>ករណីស្នើសុំបន្ត ឬបើកដំណើរការឡើងវិញ សូមបំពេញចំណុច "គ" តែម្តង។ / For extension or reopening requests, please fill out section "គ" only.</small>
            </div>
            
            <div class="form-group">
                <label>ឃ. តើសហគ្រាសស្នើសុំផ្អាកដំណើរការសកម្មភាពអាជីវកម្មនៅទីចាត់ការសហគ្រាស ឬសាខាណាមួយ? / Are you suspending activities at headquarters or branch?</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" name="location" id="headquarters" value="hq">
                        <label for="headquarters">ទីចាត់ការសហគ្រាស / Headquarters</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" name="location" id="branch" value="branch">
                        <label for="branch">សាខា / Branch</label>
                    </div>
                </div>
                <div id="branch_details" style="margin-top: 10px; display: none;">
                    <div class="form-row">
                        <div class="form-group">
                            <label>លេខ អតប សាខា / Branch VAT number</label>
                            <input type="text" name="branch_vat">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>អាសយដ្ឋានសាខា / Branch Address</label>
                        <div class="form-row">
                            <div class="form-group">
                                <label>ផ្ទះលេខ/អគារ / House No./Building</label>
                                <input type="text" name="branch_house_no">
                            </div>
                            <div class="form-group">
                                <label>ផ្លូវ / Street</label>
                                <input type="text" name="branch_street">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>ភូមិ / Village</label>
                                <input type="text" name="branch_village">
                            </div>
                            <div class="form-group">
                                <label>ឃុំ/សង្កាត់ / Commune</label>
                                <input type="text" name="branch_commune">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>ក្រុង/ស្រុក/ខណ្ឌ / District</label>
                                <input type="text" name="branch_district">
                            </div>
                            <div class="form-group">
                                <label>ខេត្ត/រាជធានី / Province/City</label>
                                <input type="text" name="branch_province">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>បញ្ជាក់ពីមូលហេតុនៃការផ្អាកសកម្មភាពអាជីវកម្ម / Reason for business suspension</label>
                <textarea name="reason" rows="3" style="width: 100%"></textarea>
            </div>
        </div>
        
        <div class="form-section">
            <div class="section-header">៣. ឯកសារភ្ជាប់ / Required documents</div>
            <div class="checkbox-group">
                <input type="checkbox" id="patent" name="documents[]" value="patent">
                <label for="patent">ប័ណ្ណបា៉តង់នៃឆ្នាំស្នើសុំ / Patent for the requested year (copy)</label>
            </div>
        </div>
        
        <div class="form-section">
            <div class="section-header">៤. សេចក្តីប្រកាស / Declaration</div>
            <div class="declaration-text khmer-text">
                ខ្ញុំបាទ/នាងខ្ញុំ សូមអះអាងថា ព័ត៌មានខាងលើ និងរាល់ឯកសារភ្ជាប់ពិតជាត្រឹមត្រូវពិតប្រាកដ។<br>
                ខ្ញុំបាទ/នាងខ្ញុំ សូមទទួលខុសត្រូវចំពោះមុខច្បាប់ ប្រសិនបើព័ត៌មានណាមួយមានការក្លែងបន្លំ។
            </div>
            
            <div class="signature-block">
                <div class="signature-row">
                    <div class="signature-field">
                        <div class="signature-line"></div>
                        <label>កាលបរិច្ឆេទ / Date<br>DD/MM/YYYY</label>
                    </div>
                    <div class="signature-field">
                        <div class="signature-line"></div>
                        <label>ឈ្មោះ / Name</label>
                    </div>
                    <div class="signature-field">
                        <div class="signature-line"></div>
                        <label>តួនាទី / Position</label>
                    </div>
                </div>
                <div class="signature-row">
                    <div class="signature-field">
                        <div class="signature-line"></div>
                        <label>ហត្ថលេខា និងត្រា / Signature and stamp</label>
                    </div>
                    <div class="signature-field">
                        <div class="signature-line"></div>
                        <label>លេខទូរស័ព្ទ / Telephone Nº</label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="officer-section">
            <div style="text-align: right; font-style: italic;">ស្រមាប់ម្រន្តី / Officer Only</div>
            <div class="signature-row">
                <div class="signature-field">
                    <div class="signature-line"></div>
                    <label>កាលបរិច្ឆេទ / Date<br>DD/MM/YYYY</label>
                </div>
                <div class="signature-field">
                    <div class="signature-line"></div>
                    <label>ឈ្មោះ / Name</label>
                </div>
                <div class="signature-field">
                    <div class="signature-line"></div>
                    <label>អត្តលេខ / ID Number</label>
                </div>
                <div class="signature-field">
                    <div class="signature-line"></div>
                    <label>ហត្ថលេខាម្រន្តី / Officer signature</label>
                </div>
            </div>
        </div>
        
        <div style="text-align: right; margin-top: 20px; font-size: 12px;">
            ទម្រង់ ៖ SUS-01
        </div>
    </div>

    <script>
        // Show/hide branch details based on selection
        document.querySelectorAll('input[name="location"]').forEach(input => {
            input.addEventListener('change', function() {
                const branchDetails = document.getElementById('branch_details');
                if (this.value === 'branch') {
                    branchDetails.style.display = 'block';
                } else {
                    branchDetails.style.display = 'none';
                }
            });
        });

        // Format date inputs
        document.querySelector('input[name="start_date"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 6) value = value.substring(0, 6);
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>