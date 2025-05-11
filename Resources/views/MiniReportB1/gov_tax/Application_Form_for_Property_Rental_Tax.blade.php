<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ទំរង់ FORM PRR 02 - Application Form for Property Rental Tax</title>
    <style>
        body {
            font-family: 'Khmer OS', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #000;
            font-size: 12px;
        }
        
        .form-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 15px;
            box-sizing: border-box;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 16px;
            margin: 5px 0;
        }
        
        .header .khmer {
            font-weight: bold;
        }
        
        .header .english {
            font-style: italic;
            font-size: 14px;
        }
        
        .section {
            margin-bottom: 15px;
            border: 1px solid #000;
            padding: 10px;
        }
        
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        
        .row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .col {
            flex: 1;
            padding: 0 5px;
        }
        
        .label {
            margin-bottom: 3px;
            font-weight: bold;
        }
        
        .label .khmer {
            display: block;
        }
        
        .label .english {
            display: block;
            font-style: italic;
            font-size: 10px;
        }
        
        .input-field {
            border-bottom: 1px solid #000;
            height: 20px;
            padding: 0 5px;
        }
        
        .date-input {
            display: flex;
            align-items: center;
        }
        
        .date-input .day, .date-input .month, .date-input .year {
            display: flex;
            align-items: center;
            margin-right: 10px;
        }
        
        .date-input .day input, .date-input .month input, .date-input .year input {
            width: 20px;
            border: none;
            border-bottom: 1px solid #000;
            text-align: center;
            margin: 0 3px;
        }
        
        .checkbox-group {
            display: flex;
            margin-top: 5px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }
        
        .checkbox-item input {
            margin-right: 5px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin-top: 30px;
        }
        
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 10px;
        }
        
        .page-number {
            text-align: right;
            font-size: 10px;
            margin-top: 10px;
        }
        
        .attachment-item {
            display: flex;
            margin-bottom: 5px;
        }
        
        .attachment-number {
            margin-right: 10px;
            font-weight: bold;
        }
        
        .address-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
        }
        
        .address-item {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
@include("minireportb1::MiniReportB1.components.back_to_tax_dashboard_button")

    <div class="form-container">
        <div class="header">
            <h1 class="khmer">ទំព័រ</h1>
            <h1 class="english">Page</h1>
            <h1 class="khmer">ពាក្យសុំសំរាប់ពីពន្ធលើរឿងឈ្នួលអចលនវត្ថុ</h1>
            <h1 class="english">Application Form for Property Rental Tax</h1>
        </div>
        
        <!-- Section I: Owner/Lessor and Lessee Information -->
        <div class="section">
            <div class="section-title">
                I. ព័ត៌មានកម្មសិទ្ធិករ ឬសិទ្ធិវន្ត និងអ្នកជួល / Owner's or Lessor's and Lessee's Information
            </div>
            
            <!-- Owner/Lessor Information -->
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">ឈ្មោះកម្មសិទ្ធិករ ឬ សិទ្ធិវន្ត</span>
                        <span class="english">Name of Owner's or Lessor</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">លេខទូរស័ព្ទ</span>
                        <span class="english">Telephone Number</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <!-- Lessee Information -->
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">ឈ្មោះអ្នកជួល</span>
                        <span class="english">Name of Lessee</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">លេខទូរស័ព្ទ</span>
                        <span class="english">Telephone Number</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
        </div>
        
        <!-- Section II: Property Rental Information -->
        <div class="section">
            <div class="section-title">
                II. ព័ត៌មានរបស់អចលនវត្ថុជួល / Property Rental Information
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">លេខសម្គាល់ការចុះបញ្ជីពន្ធលើរឿងឈ្នួលអចលនវត្ថុ</span>
                        <span class="english">Registration Number (RIN)</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">លេខសម្គាល់អចលនវត្ថុដែលជួល (បាកូដ)</span>
                        <span class="english">Barcode Number</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <!-- Property Address -->
            <div class="label">
                <span class="khmer">អាសយដ្ឋានអចលនវត្ថុដែលជួល ផ្ទះលេខ/អគារ</span>
                <span class="english">Rental Address House No./ Building</span>
            </div>
            
            <div class="address-grid">
                <div class="address-item">
                    <div class="label">
                        <span class="khmer">ជាន់ទី</span>
                        <span class="english">Floor</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="address-item">
                    <div class="label">
                        <span class="khmer">លេខបន្ទប់</span>
                        <span class="english">Room No.</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="address-item">
                    <div class="label">
                        <span class="khmer">ផ្លូវ</span>
                        <span class="english">Street</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="address-item">
                    <div class="label">
                        <span class="khmer">ភូមិ</span>
                        <span class="english">Village</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="address-item">
                    <div class="label">
                        <span class="khmer">ឃុំ/សង្កាត់</span>
                        <span class="english">Commune</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="address-item">
                    <div class="label">
                        <span class="khmer">ក្រុង/ស្រុក/ខណ្ឌ</span>
                        <span class="english">District</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="address-item">
                    <div class="label">
                        <span class="khmer">ខេត្ត/រាជធានី</span>
                        <span class="english">Province/City</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
        </div>
        
        <!-- Section III: Purpose and Date -->
        <div class="section">
            <div class="section-title">
                III. កម្មវត្ថុនៃការសុំ និង កាលបរិច្ឆេទ / Purpose and Date
            </div>
            
            <div class="label">
                <span class="khmer">មូលហេតុ (Reason):</span>
            </div>
            
            <div class="checkbox-group">
                <div class="checkbox-item">
                    <input type="checkbox" id="reason1" name="reason">
                    <label for="reason1">ផ្អាកបណ្តោះអាសន្ន (Suspension)</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="reason2" name="reason">
                    <label for="reason2">បញ្ឈប់ជាស្ថាពរ (Termination)</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="reason3" name="reason">
                    <label for="reason3">ផ្ទេរ (Transfer)</label>
                </div>
            </div>
            
            <!-- Dates -->
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">កាលបរិច្ឆេទបញ្ឈប់</span>
                        <span class="english">Termination Date</span>
                    </div>
                    <div class="date-input">
                        <span class="khmer">ថ្ងៃទី</span>
                        <span class="english">Day</span>
                        <div class="day">
                            <input type="text" maxlength="2"> D
                        </div>
                        <span class="khmer">ខែ</span>
                        <span class="english">Month</span>
                        <div class="month">
                            <input type="text" maxlength="2"> M
                        </div>
                        <span class="khmer">ឆ្នាំ</span>
                        <span class="english">Year</span>
                        <div class="year">
                            <input type="text" maxlength="4"> Y
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">កាលបរិច្ឆេទផ្ទេរតាមសិទ្ធិប្រកាស</span>
                        <span class="english">Transfer Date</span>
                    </div>
                    <div class="date-input">
                        <span class="khmer">ថ្ងៃទី</span>
                        <span class="english">Day</span>
                        <div class="day">
                            <input type="text" maxlength="2"> D
                        </div>
                        <span class="khmer">ខែ</span>
                        <span class="english">Month</span>
                        <div class="month">
                            <input type="text" maxlength="2"> M
                        </div>
                        <span class="khmer">ឆ្នាំ</span>
                        <span class="english">Year</span>
                        <div class="year">
                            <input type="text" maxlength="4"> Y
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">កាលបរិច្ឆេទផ្អាក</span>
                        <span class="english">Suspension Date</span>
                    </div>
                    <div class="date-input">
                        <span class="khmer">ថ្ងៃទី</span>
                        <span class="english">Day</span>
                        <div class="day">
                            <input type="text" maxlength="2"> D
                        </div>
                        <span class="khmer">ខែ</span>
                        <span class="english">Month</span>
                        <div class="month">
                            <input type="text" maxlength="2"> M
                        </div>
                        <span class="khmer">ឆ្នាំ</span>
                        <span class="english">Year</span>
                        <div class="year">
                            <input type="text" maxlength="4"> Y
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">ច្បាប់ថតចម្លង</span>
                        <span class="english">Certified Copy</span>
                    </div>
                    <div class="date-input">
                        <span class="khmer">ដល់</span>
                        <span class="english">Until</span>
                        <div class="day">
                            <input type="text" maxlength="2"> D
                        </div>
                        <div class="month">
                            <input type="text" maxlength="2"> M
                        </div>
                        <div class="year">
                            <input type="text" maxlength="4"> Y
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="label">
                <span class="khmer">សាខាពន្ធដារ</span>
                <span class="english">Tax branch</span>
            </div>
            <div class="input-field"></div>
        </div>
        
        <!-- Section IV: Attachment check-list -->
        <div class="section">
            <div class="section-title">
                IV. សំណុំឯកសារភ្ជាប់ / Attachment check-list
            </div>
            
            <div class="attachment-item">
                <div class="attachment-number">1.</div>
                <div>
                    <div class="label">
                        <span class="khmer">លិខិតបញ្ជាក់កិច្ចសន្យារវាងកម្មសិទ្ធិករ ឬ សិទ្ធិវន្ត និងអ្នកជួល (ប្រសិនបើមាន)</span>
                        <span class="english">Termination Lease Agreement Letter between Owner/Lessor and Lessee (If any)</span>
                    </div>
                    <div class="label">
                        <span class="khmer">ច្បាប់ថតចម្លង</span>
                        <span class="english">Certified Copy</span>
                    </div>
                </div>
            </div>
            
            <div class="attachment-item">
                <div class="attachment-number">2.</div>
                <div>
                    <div class="label">
                        <span class="khmer">កិច្ចសន្យាជួលអចលនវត្ថុ</span>
                        <span class="english">Lease Agreement</span>
                    </div>
                    <div class="label">
                        <span class="khmer">ច្បាប់ថតចម្លង</span>
                        <span class="english">Certified Copy</span>
                    </div>
                </div>
            </div>
            
            <div class="attachment-item">
                <div class="attachment-number">3.</div>
                <div>
                    <div class="label">
                        <span class="khmer">បង្កាន់ដៃរបស់ការបង់ប្រមូលពន្ធខែចុងក្រោយ</span>
                        <span class="english">Last Month Single Invoice of Rental Tax</span>
                    </div>
                    <div class="label">
                        <span class="khmer">ច្បាប់ថតចម្លង</span>
                        <span class="english">Certified Copy</span>
                    </div>
                </div>
            </div>
            
            <div class="attachment-item">
                <div class="attachment-number">4.</div>
                <div>
                    <div class="label">
                        <span class="khmer">រូបថតទីតាំង</span>
                        <span class="english">Location Photo</span>
                    </div>
                    <div class="label">
                        <span class="khmer">ច្បាប់ថតចម្លង</span>
                        <span class="english">Certified Copy</span>
                    </div>
                </div>
            </div>
            
            <div class="attachment-item">
                <div class="attachment-number">5.</div>
                <div>
                    <div class="label">
                        <span class="khmer">ករណីផ្ទេរតាមសិទ្ធិប្រកាសរតែវភ្ជាប់ប័ណ្ណពន្ធប៉ាតង់ ឬ លេខ VAT</span>
                        <span class="english">In case Transfer, please attached patent tax or VAT number</span>
                    </div>
                    <div class="label">
                        <span class="khmer">ច្បាប់ថតចម្លង</span>
                        <span class="english">Certified Copy</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Page 1 footer -->
        <div class="page-number">1/2</div>
        <div class="footer">ទំរង់ FORM PRR 02</div>
    </div>
    
    <!-- Page 2 -->
    <div class="form-container" style="margin-top: 20px;">
        <div class="section">
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">លេខបង្កាន់ដៃរបស់ការបង់ប្រមូលពន្ធ</span>
                        <span class="english">Single Invoice</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">យោបល់ផ្សេងៗ</span>
                        <span class="english">Comments</span>
                    </div>
                    <div class="input-field" style="height: 50px;"></div>
                </div>
                <div class="col">
                    <div class="signature-line"></div>
                    <div class="label">
                        <span class="khmer">ហត្ថលេខា</span>
                        <span class="english">Signature</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">យោបល់ផ្សេងៗ</span>
                        <span class="english">Comments</span>
                    </div>
                    <div class="input-field" style="height: 50px;"></div>
                </div>
                <div class="col">
                    <div class="signature-line"></div>
                    <div class="label">
                        <span class="khmer">ហត្ថលេខា</span>
                        <span class="english">Signature</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="signature-line"></div>
                    <div class="label">
                        <span class="khmer">ហត្ថលេខា</span>
                        <span class="english">Signature</span>
                    </div>
                </div>
                <div class="col">
                    <div class="signature-line"></div>
                    <div class="label">
                        <span class="khmer">ហត្ថលេខា</span>
                        <span class="english">Signature</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">យោបល់ផ្សេងៗ</span>
                        <span class="english">Comments</span>
                    </div>
                    <div class="input-field" style="height: 50px;"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">យោបល់ផ្សេងៗ</span>
                        <span class="english">Comments</span>
                    </div>
                    <div class="input-field" style="height: 50px;"></div>
                </div>
            </div>
        </div>
        
        <!-- Section V: Declaration -->
        <div class="section">
            <div class="section-title">
                V. សច្ចាប័ន្របកាស / Declaration
            </div>
            
            <p style="text-align: justify;">
                <span class="khmer">ខ្ញុំប្រធាន/នាងខ្ញុំសូមធ្វើធានាថា ព័ត៌មានទាំងអស់នៅលើលិខិតប្រកាសនេះពិតជាត្រឹមត្រូវ និងសូមទទួលខុសត្រូវទាំងស្រុងចំពោះមុខច្បាប់ទាំងឡាយជាធរមាន ប្រសិនបើព័ត៌មានណាមួយមានការក្លែងបន្លំ។</span>
                <span class="english">I declare that information provided in this application including all attachments are true and correct.</span>
            </p>
            
            <div class="signature-line" style="margin-top: 30px;"></div>
            <div class="label">
                <span class="khmer">ហត្ថលេខា</span>
                <span class="english">Signature</span>
            </div>
        </div>
        
        <!-- Tax official use only -->
        <div class="section">
            <div class="section-title">
                សម្រាប់មន្ត្រីពន្ធដារ / Tax official use only
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">កាលបរិច្ឆេទ</span>
                        <span class="english">Date</span>
                    </div>
                    <div class="date-input">
                        <div class="day">
                            <input type="text" maxlength="2"> D
                        </div>
                        <div class="month">
                            <input type="text" maxlength="2"> M
                        </div>
                        <div class="year">
                            <input type="text" maxlength="4"> Y
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="signature-line"></div>
                    <div class="label">
                        <span class="khmer">ហត្ថលេខា</span>
                        <span class="english">Signature</span>
                    </div>
                </div>
            </div>
            
            <div class="row" style="margin-top: 20px;">
                <div class="col">
                    <div class="label">
                        <span class="khmer">ឈ្មោះ</span>
                        <span class="english">Name</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">មន្ត្រីទទួលបន្ទុក</span>
                        <span class="english">Tax officer</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <div class="row" style="margin-top: 10px;">
                <div class="col">
                    <div class="label">
                        <span class="khmer">អនុប្រធានការិយាល័យ</span>
                        <span class="english">Deputy of Bureau</span>
                    </div>
                    <div class="signature-line"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">ប្រធានការិយាល័យ</span>
                        <span class="english">Head of Bureau</span>
                    </div>
                    <div class="signature-line"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">ប្រធានសាខាពន្ធដារ</span>
                        <span class="english">Head of Branch</span>
                    </div>
                    <div class="signature-line"></div>
                </div>
            </div>
        </div>
        
        <!-- Page 2 footer -->
        <div class="page-number">2/2</div>
    </div>

    <script>
        // JavaScript for form functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-tab between date fields
            const dateInputs = document.querySelectorAll('.date-input input');
            dateInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.length === this.maxLength) {
                        const next = this.parentElement.nextElementSibling;
                        if (next && next.querySelector('input')) {
                            next.querySelector('input').focus();
                        }
                    }
                });
                
                // Allow only numbers
                input.addEventListener('keypress', function(e) {
                    if (e.key < '0' || e.key > '9') {
                        e.preventDefault();
                    }
                });
            });
            
            // Make checkboxes mutually exclusive
            const checkboxes = document.querySelectorAll('input[name="reason"]');
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
</body>
</html>