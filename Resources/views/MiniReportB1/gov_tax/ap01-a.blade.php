<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AP01-A Form</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .section {
            border: 1px solid #000;
            margin-bottom: 20px;
            padding: 15px;
            position: relative;
        }
        
        .section-title {
            position: absolute;
            top: -10px;
            left: 10px;
            background-color: white;
            padding: 0 5px;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        
        .col {
            flex: 1;
            min-width: 200px;
            margin-right: 10px;
        }
        
        .field {
            margin-bottom: 15px;
        }
        
        .label {
            font-size: 12px;
            display: block;
            margin-bottom: 3px;
        }
        
        .khmer {
            font-family: 'Khmer OS', 'Khmer OS System', 'Khmer OS Battambang', sans-serif;
        }
        
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .form-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .official-use {
            background-color: #f0f0f0;
            padding: 10px;
            margin: 10px 0;
            border: 1px dashed #999;
        }
        
        .section-number {
            font-weight: bold;
            font-size: 14px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="section">
            <div class="section-title">
                <span class="section-number">២-</span>
                <span class="khmer">ព័ត៌មានសហគ្រាសដើម</span> / 
                <span>Principal Enterprise Information</span>
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">នាមករណ៍សហគ្រាស</span><br>
                    <span>Name of the Enterprise</span>
                </div>
                <input type="text" id="enterprise-name">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ចុះបញ្ជីដៅ នាយកដ្ឋានគ្របគ្រងអ្នកជាប់ពនធធំ សាខាពនធដ្ឋរ</span><br>
                    <span>Registered at Department of Large Taxpayers Tax branch</span>
                </div>
                <input type="text" id="registration-dept">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">សកមមភាពអាជីវកមម</span><br>
                    <span>Business Activities</span>
                </div>
                <input type="text" id="business-activities">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ដេខអ្តតសញ្ញាណកមមសារដពើពនធ</span> -<br>
                    <span>Tax Identification Number (TIN)</span>
                </div>
                <input type="text" id="tin">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ដេខកិចចសនារវាងភាន ក់ងារនិងសហគ្រាសដើម ចុះថ្ងៃទី</span><br>
                    <span>Principal Agent Contract Nº Dated</span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" id="contract-no" placeholder="Contract No.">
                    </div>
                    <div class="col">
                        <input type="date" id="contract-date" placeholder="Date">
                    </div>
                </div>
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">សារដអ្ឡិចគ្រតូនិកគ្របអ្ប់សំបគ្រត/គ្របអ្ប់សំបគ្រតដអ្ឡិកគ្រតូនិក</span><br>
                    <span>Email P.O. Box/Electronic mailbox</span>
                </div>
                <input type="text" id="email">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ទូរស័ពទចេ័ត ទូរស័ពទដេើត ទូរសារ</span><br>
                    <span>Mobile Phone Office Telephone Fax</span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" id="mobile" placeholder="Mobile">
                    </div>
                    <div class="col">
                        <input type="text" id="office-phone" placeholder="Office Phone">
                    </div>
                    <div class="col">
                        <input type="text" id="fax" placeholder="Fax">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="official-use">
            <div class="label">
                <span class="khmer">ព័ត៌មានអំពីសហគ្រាសដើម</span><br>
                <span>ADDITIONAL PRINCIPAL INFORMATION</span>
            </div>
            <div class="label">
                <span>សម្រាប់មន្ត្រីព្ធដារ /Tax official use only</span>
            </div>
            <div class="field">
                <div class="label">
                    <span class="khmer">ដេខអ្តតសញ្ញាណកមមសារដពើពនធ</span><br>
                    <span>Tax Identification Number (TIN)</span>
                </div>
                <input type="text" id="official-tin" disabled>
            </div>
            <div class="field">
                <div class="label">
                    <span class="khmer">ដ្មុះ សហគ្រាសជាភានក់ងារ</span><br>
                    <span>Name of Agent Enterprise</span>
                </div>
                <input type="text" id="agent-name" disabled>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">
                <span class="section-number">៣-</span>
                <span class="khmer">ព័ត៌មានសហគ្រាសដើម</span> / 
                <span>Principal Enterprise Information</span>
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">នាមករណ៍សហគ្រាស</span><br>
                    <span>Name of the Enterprise</span>
                </div>
                <input type="text" id="enterprise-name-3">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ចុះបញ្ជីដៅ នាយកដ្ឋានគ្របគ្រងអ្នកជាប់ពនធធំ សាខាពនធដ្ឋរ</span><br>
                    <span>Registered at Department of Large Taxpayers Tax branch</span>
                </div>
                <input type="text" id="registration-dept-3">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">សកមមភាពអាជីវកមម</span><br>
                    <span>Business Activities</span>
                </div>
                <input type="text" id="business-activities-3">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ដេខអ្តតសញ្ញាណកមមសារដពើពនធ</span> -<br>
                    <span>Tax Identification Number (TIN)</span>
                </div>
                <input type="text" id="tin-3">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ដេខកិចចសនារវាងភាន ក់ងារនិងសហគ្រាសដើម ចុះថ្ងៃទី</span><br>
                    <span>Principal Agent Contract Nº Dated</span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" id="contract-no-3" placeholder="Contract No.">
                    </div>
                    <div class="col">
                        <input type="date" id="contract-date-3" placeholder="Date">
                    </div>
                </div>
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">សារដអ្ឡិចគ្រតូនិកគ្របអ្ប់សំបគ្រត/គ្របអ្ប់សំបគ្រតដអ្ឡិកគ្រតូនិក</span><br>
                    <span>Email P.O. Box/Electronic mailbox</span>
                </div>
                <input type="text" id="email-3">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ទូរស័ពទចេ័ត ទូរស័ពទដេើត ទូរសារ</span><br>
                    <span>Mobile Phone Office Telephone Fax</span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" id="mobile-3" placeholder="Mobile">
                    </div>
                    <div class="col">
                        <input type="text" id="office-phone-3" placeholder="Office Phone">
                    </div>
                    <div class="col">
                        <input type="text" id="fax-3" placeholder="Fax">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">
                <span class="section-number">៤-</span>
                <span class="khmer">ព័ត៌មានសហគ្រាសដើម</span> / 
                <span>Principal Enterprise Information</span>
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">នាមករណ៍សហគ្រាស</span><br>
                    <span>Name of the Enterprise</span>
                </div>
                <input type="text" id="enterprise-name-4">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ចុះបញ្ជីដៅ នាយកដ្ឋានគ្របគ្រងអ្នកជាប់ពនធធំ សាខាពនធដ្ឋរ</span><br>
                    <span>Registered at Department of Large Taxpayers Tax branch</span>
                </div>
                <input type="text" id="registration-dept-4">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">សកមមភាពអាជីវកមម</span><br>
                    <span>Business Activities</span>
                </div>
                <input type="text" id="business-activities-4">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ដេខអ្តតសញ្ញាណកមមសារដពើពនធ</span> -<br>
                    <span>Tax Identification Number (TIN)</span>
                </div>
                <input type="text" id="tin-4">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ដេខកិចចសនារវាងភាន ក់ងារនិងសហគ្រាសដើម ចុះថ្ងៃទី</span><br>
                    <span>Principal Agent Contract Nº Dated</span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" id="contract-no-4" placeholder="Contract No.">
                    </div>
                    <div class="col">
                        <input type="date" id="contract-date-4" placeholder="Date">
                    </div>
                </div>
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">សារដអ្ឡិចគ្រតូនិកគ្របអ្ប់សំបគ្រត/គ្របអ្ប់សំបគ្រតដអ្ឡិកគ្រតូនិក</span><br>
                    <span>Email P.O. Box/Electronic mailbox</span>
                </div>
                <input type="text" id="email-4">
            </div>
            
            <div class="field">
                <div class="label">
                    <span class="khmer">ទូរស័ពទចេ័ត ទូរស័ពទដេើត ទូរសារ</span><br>
                    <span>Mobile Phone Office Telephone Fax</span>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="text" id="mobile-4" placeholder="Mobile">
                    </div>
                    <div class="col">
                        <input type="text" id="office-phone-4" placeholder="Office Phone">
                    </div>
                    <div class="col">
                        <input type="text" id="fax-4" placeholder="Fax">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-footer">
            <span>ទគ្រមង់ / Form AP01-A</span>
        </div>
    </div>

    <script>
        // Add any necessary JavaScript functionality
        document.addEventListener('DOMContentLoaded', function() {
            // You can add form validation or other functionality here
            
            // Example: Save form data to local storage
            const form = document.querySelector('.form-container');
            const inputs = form.querySelectorAll('input');
            
            // Load any saved data
            inputs.forEach(input => {
                const savedValue = localStorage.getItem(input.id);
                if (savedValue) input.value = savedValue;
                
                // Save data as user types
                input.addEventListener('input', function() {
                    localStorage.setItem(this.id, this.value);
                });
            });
        });
    </script>
</body>
</html>