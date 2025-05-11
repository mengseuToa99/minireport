<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ពាក្យស្នើសុំបើកដំណើរការវិញ្ញាបនបត្រអាករលើតម្លៃបន្ថែម ក្រោយការព្យួរទុក</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, "Khmer OS", sans-serif;
        }
        body {
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .form-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-subtitle {
            font-size: 16px;
        }
        .form-section {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 16px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            display: flex;
        }
        .section-number {
            min-width: 25px;
            font-weight: bold;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
            align-items: center;
        }
        .form-group {
            margin-bottom: 15px;
            flex: 1 0 50%;
            min-width: 250px;
            padding-right: 15px;
        }
        .form-group.full-width {
            flex: 1 0 100%;
        }
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .radio-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .radio-label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        .checkbox-group {
            margin-bottom: 5px;
        }
        .declaration-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .declaration-text {
            font-style: italic;
            margin-bottom: 15px;
        }
        .signature-block {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .signature-group {
            width: 48%;
            margin-bottom: 15px;
            padding: 15px;
            border: 1px dashed #ccc;
            min-width: 250px;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #000;
        }
        .officer-section {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .officer-title {
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        @media (max-width: 768px) {
            .form-group {
                flex: 1 0 100%;
                padding-right: 0;
            }
            .signature-group {
                width: 100%;
            }
        }
        .form-code {
            text-align: right;
            font-style: italic;
            margin-bottom: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
@include("minireportb1::MiniReportB1.components.back_to_tax_dashboard_button")

    <div class="container">
        <div class="header">
            <div class="form-title">ពាក្យស្នើសុំ</div>
            <div class="form-subtitle">បើកដំណើរការវិញ្ញាបនបត្រអាករលើតម្លៃបន្ថែម ក្រោយការព្យួរទុក</div>
        </div>
        
        <div class="form-code">ទម្រង់៖ VAT-SUS-02</div>
        
        <div class="form-section">
            <div class="section-title">
                <span class="section-number">១.</span>
                <span>ព័ត៌មានអ្នកស្នើសុំ / Requester information</span>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">គោត្តនាម-នាម / Name of Requester</label>
                    <input type="text" class="form-control" id="requesterName">
                </div>
                <div class="form-group">
                    <label class="form-label">ជាអក្សរឡាតាំង / Name in Latin</label>
                    <input type="text" class="form-control" id="requesterNameLatin">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">លេខអត្តសញ្ញាណប័ណ្ណ / Identification Card Number</label>
                    <input type="text" class="form-control" id="idCardNumber">
                </div>
                <div class="form-group">
                    <label class="form-label">ភេទ / Gender:</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="gender" value="male"> ប្រុស / Male
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="gender" value="female"> ស្រី / Female
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ថ្ងៃខែឆ្នាំកំណើត / Date of Birth</label>
                    <input type="date" class="form-control" id="dateOfBirth">
                </div>
                <div class="form-group">
                    <label class="form-label">សញ្ជាតិ / Nationality</label>
                    <input type="text" class="form-control" id="nationality">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">លេខទូរស័ព្ទ / Phone number</label>
                    <input type="tel" class="form-control" id="phoneNumber">
                </div>
                <div class="form-group">
                    <label class="form-label">សារអេឡិចត្រូនិក / Email</label>
                    <input type="email" class="form-control" id="email">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ប្រភេទអ្នកស្នើសុំ / Type of Requester:</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="requesterType" value="taxpayer"> អ្នកជាប់ពន្ធ / Taxpayer
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="requesterType" value="representative"> អ្នកតំណាងសហគ្រាស / Enterprise Representative
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="requesterType" value="agency"> ភ្នាក់ងារសេវាកម្មពន្ធដារ / Tax Agency
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <div class="section-title">
                <span class="section-number">២.</span>
                <span>ព័ត៌មានសហគ្រាស / Company information</span>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ឈ្មោះសហគ្រាស / Name of company</label>
                    <input type="text" class="form-control" id="companyName">
                </div>
                <div class="form-group">
                    <label class="form-label">ឈ្មោះសហគ្រាសជាអក្សរឡាតាំង / Name of company in Latin</label>
                    <input type="text" class="form-control" id="companyNameLatin">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">លេខអត្តសញ្ញាណកម្មសារពើពន្ធ (TIN) / Tax Identification Number (TIN)</label>
                    <input type="text" class="form-control" id="tin">
                </div>
                <div class="form-group">
                    <label class="form-label">សកម្មភាពអាជីវកម្មចម្បង / Main Business Activity</label>
                    <input type="text" class="form-control" id="businessActivity">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <div class="section-title">
                <span class="section-number">៣.</span>
                <span>ព័ត៌មានចាំបាច់សម្រាប់ការស្នើសុំ / Required information for request</span>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ក. លេខលិខិតព្យួរទុក អតប ៖</label>
                    <input type="text" class="form-control" id="suspensionDocNumber">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <div class="section-title">
                <span class="section-number">៤.</span>
                <span>ឯកសារភ្ជាប់ / Required documents</span>
            </div>
            
            <div class="checkbox-group">
                <label class="radio-label">
                    <input type="checkbox" name="documents" value="suspensionLetter">
                    <span>លិខិតស្តីពីការព្យួរទុក VAT (ច្បាប់ចម្លង)</span>
                </label>
            </div>
            
            <div class="checkbox-group">
                <label class="radio-label">
                    <input type="checkbox" name="documents" value="taxDeclaration">
                    <span>លិខិតបញ្ជាក់ការដាក់លិខិតប្រកាសពន្ធប្រចាំខែ ឬប្រចាំឆ្នាំ ដែលមានបញ្ជាក់ពី ម៉ន្ត្រី ឬតាមអនឡាញ (ច្បាប់ចម្លង)</span>
                </label>
            </div>
            
            <div class="checkbox-group">
                <label class="radio-label">
                    <input type="checkbox" name="documents" value="paymentReceipt">
                    <span>បង្កាន់ដៃបញ្ជាក់ការបង់ប្រាក់ពន្ធលើ ខែដែលបានខកខាន (ច្បាប់ចម្លង)</span>
                </label>
            </div>
        </div>
        
        <div class="form-section">
            <div class="section-title">
                <span class="section-number">៥.</span>
                <span>សេចក្តីប្រកាស / Declaration</span>
            </div>
            
            <div class="declaration-text">
                ខ្ញុំបាទ/នាងខ្ញុំ សូមអះអាងថា ព័ត៌មានខាងលើ និងរាល់ឯកសារភ្ជាប់ពិតជាត្រឹមត្រូវពិតប្រាកដ។<br>
                ខ្ញុំបាទ/នាងខ្ញុំ សូមទទួលខុសត្រូវចំពោះមុខច្បាប់ ប្រសិនបើព័ត៌មានណាមួយមានការកែល្ងបន្លំ។
            </div>
            
            <div class="signature-block">
                <div class="signature-group">
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="form-label">កាលបរិច្ឆេទ / Date</label>
                            <input type="date" class="form-control" id="declarationDate">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="form-label">ឈ្មោះ / Name</label>
                            <input type="text" class="form-control" id="declarationName">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="form-label">តួនាទី / Position</label>
                            <input type="text" class="form-control" id="declarationPosition">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="form-label">លេខទូរស័ព្ទ / Telephone Nº</label>
                            <input type="tel" class="form-control" id="declarationPhone">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="form-label">ហត្ថលេខា និងត្រា / Signature and stamp</label>
                            <div class="signature-line"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="officer-section">
            <div class="officer-title">សម្រាប់ម៉ន្ត្រី / Officer Only</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">កាលបរិច្ឆេទ / Date</label>
                    <input type="date" class="form-control" id="officerDate" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">ឈ្មោះ / Name</label>
                    <input type="text" class="form-control" id="officerName" disabled>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">អត្តលេខ / ID Number</label>
                    <input type="text" class="form-control" id="officerId" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">ហត្ថលេខាម៉ន្ត្រី / Officer signature</label>
                    <div class="signature-line"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get current date
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`;
            
            // Set default date for declaration
            document.getElementById('declarationDate').value = formattedDate;
            
            // Form validation
            const form = document.querySelector('.container');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Basic validation
                const requesterName = document.getElementById('requesterName').value;
                const companyName = document.getElementById('companyName').value;
                const tin = document.getElementById('tin').value;
                
                if (!requesterName) {
                    alert('Please enter the requester name');
                    return;
                }
                
                if (!companyName) {
                    alert('Please enter the company name');
                    return;
                }
                
                if (!tin) {
                    alert('Please enter the Tax Identification Number (TIN)');
                    return;
                }
                
                // Submit logic would go here
                alert('Form submitted successfully!');
            });
            
            // Add print functionality
            const printButton = document.createElement('button');
            printButton.innerText = 'Print Form';
            printButton.style.cssText = 'margin: 20px auto; display: block; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;';
            printButton.addEventListener('click', function() {
                window.print();
            });
            document.body.appendChild(printButton);
        });
    </script>
</body>
</html>