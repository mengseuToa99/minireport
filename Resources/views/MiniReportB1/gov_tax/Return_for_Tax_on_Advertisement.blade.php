<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ទំរង់ - Return for Tax on Advertisement</title>
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
            margin-bottom: 10px;
        }
        
        .header .khmer {
            font-weight: bold;
            font-size: 14px;
        }
        
        .header .english {
            font-style: italic;
            font-size: 12px;
        }
        
        .kingdom-header {
            text-align: center;
            margin-bottom: 10px;
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
        
        .tax-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .tax-table th, .tax-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        
        .tax-table th {
            background-color: #f0f0f0;
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
        
        .official-use {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .official-use-item {
            width: 30%;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }
        
        .checkbox-item input {
            margin-right: 5px;
        }
        
        .barcode-placeholder {
            height: 40px;
            border: 1px dashed #000;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
@include("minireportb1::MiniReportB1.components.back_to_tax_dashboard_button")

    <div class="form-container">
        <!-- Official Use Section -->
        <div class="official-use">
            <div class="official-use-item">
                <div class="label">
                    <span class="khmer">សម្រាប់មន្ត្រីពន្ធដារ</span>
                    <span class="english">FOR TAX OFFICIAL</span>
                </div>
                <div class="input-field"></div>
            </div>
            <div class="official-use-item">
                <div class="label">
                    <span class="khmer">កាលបរិច្ឆេទ</span>
                    <span class="english">DATE</span>
                </div>
                <div class="input-field"></div>
            </div>
            <div class="official-use-item">
                <div class="label">
                    <span class="khmer">អត្តសញ្ញាណមន្ត្រី</span>
                    <span class="english">ID NUMBER</span>
                </div>
                <div class="input-field"></div>
            </div>
        </div>
        
        <div class="official-use">
            <div class="official-use-item">
                <div class="signature-line"></div>
                <div class="label">
                    <span class="khmer">ហត្ថលេខា និងឈ្មោះ</span>
                    <span class="english">SIGNATURE AND NAME</span>
                </div>
            </div>
            <div class="official-use-item">
                <div class="signature-line"></div>
                <div class="label">
                    <span class="khmer">ចាត់ចែង</span>
                    <span class="english">FILLED IN</span>
                </div>
            </div>
            <div class="official-use-item">
                <div class="signature-line"></div>
                <div class="label">
                    <span class="khmer">អភិបាល/បញ្ញធិការ/កម្មសិទ្ធិករ សហគ្រាស</span>
                    <span class="english">DIRECTOR/MANAGER/OWNER OF ENTERPRISE</span>
                </div>
                <div class="signature-line"></div>
                <div class="label">
                    <span class="khmer">ហត្ថលេខា ឈ្មោះ និងត្រា</span>
                    <span class="english">SIGNATURE, NAME AND SEAL</span>
                </div>
            </div>
        </div>
        
        <div class="page-number">
            <span class="khmer">ទំព័រទី</span>
            <span class="english">PAGE 1</span>
        </div>
        
        <!-- Kingdom Header -->
        <div class="kingdom-header">
            <div class="khmer">ព្រះរាជាណាចក្រកម្ពុជា</div>
            <div class="english">KINGDOM OF CAMBODIA</div>
            <div class="khmer">ជាតិ សាសនា ព្រះមហាក្សត្រ</div>
            <div class="english">NATION RELIGION KING</div>
        </div>
        
        <!-- Ministry Header -->
        <div class="header">
            <div class="khmer">ក្រសួងសេដ្ឋកិច្ច និងហិរញ្ញវត្ថុ</div>
            <div class="english">MINISTRY OF ECONOMY AND FINANCE</div>
            <div class="khmer">អគ្គនាយកដ្ឋានពន្ធដារ</div>
            <div class="english">GENERAL DEPARTMENT OF TAXATION</div>
        </div>
        
        <!-- Form Title -->
        <div class="header">
            <div class="khmer">លិខិតប្រកាសពន្ធលើផ្ទាំងផ្សាពវផ្សាយ</div>
            <div class="english">RETURN FOR TAX ON ADVERTISEMENT</div>
        </div>
        
        <!-- Barcode Placeholder -->
        <div class="barcode-placeholder">
            <div class="khmer" style="text-align: center;">ស្លាកបិទបាកូដ</div>
            <div class="english" style="text-align: center;">BARCODE</div>
        </div>
        
        <!-- Section I: Enterprise Information -->
        <div class="section">
            <div class="section-title">
                I. ព័ត៌មានសហគ្រាស / ENTERPRISE INFORMATION
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">លេខអត្តសញ្ញាណកម្មសារពាណិជ្ជកម្ម</span>
                        <span class="english">TAX IDENTIFICATION NUMBER (TIN)</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">ឈ្មោះអ្នករក្សាសហគ្រាស</span>
                        <span class="english">NAME OF ENTERPRISE OWNER</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">ឈ្មោះសហគ្រាស</span>
                        <span class="english">NAME OF ENTERPRISE</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">សកម្មភាពអាជីវកម្ម</span>
                        <span class="english">BUSINESS ACTIVITY</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">ឈ្មោះសហគ្រាសជាអក្សរឡាតាំង</span>
                        <span class="english">NAME OF ENTERPRISE IN LATIN</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">អាសយដ្ឋាន ផ្ទះលេខ</span>
                        <span class="english">ADDRESS NO.</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">ផ្លូវ</span>
                        <span class="english">STREET</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">ភូមិ</span>
                        <span class="english">VILLAGE</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">ឃុំ/សង្កាត់</span>
                        <span class="english">COMMUNE</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">ក្រុង/ស្រុក/ខណ្ឌ</span>
                        <span class="english">DISTRICT</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">ខេត្ត/រាជធានី</span>
                        <span class="english">PROVINCE/CITY</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">ទូរស័ព្ទចល័ត/ទូរស័ព្ទជារ៉ាក់</span>
                        <span class="english">MOBILE PHONE/TELEPHONE</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">សារអេឡិចត្រូនិក</span>
                        <span class="english">E-MAIL</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
        </div>
        
        <!-- Section II: Total Tax Amount -->
        <div class="section">
            <div class="section-title">
                II. សរុបចំនួនពន្ធ / TOTAL TAX AMOUNT
            </div>
            
            <table class="tax-table">
                <thead>
                    <tr>
                        <th rowspan="2">ល.រ.<br>NO.</th>
                        <th rowspan="2">ប្រភេទផ្ទាំងផ្សាពវផ្សាយ<br>TYPE OF ADVERTISING BOARD</th>
                        <th colspan="1">ចំនួនពន្ធត្រូវបង់<br>TAX AMOUNT TO BE PAID</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>១</td>
                        <td>ស្លាកអាជីវកម្មដាក់<br>BUSINESS LABEL</td>
                        <td>៛ <input type="text" class="input-field" style="width: 80px;" value="0.00"></td>
                    </tr>
                    <tr>
                        <td>២</td>
                        <td>ផ្ទាំងអក្សរ ឬផ្ទាំងរូបភាពសម្រាប់គោលបំណងផ្សាពវជាតិជកម្ម<br>POSTERS OF PICTURE OR VIDEO FOR ADVERTISING PURPOSES</td>
                        <td>៛ <input type="text" class="input-field" style="width: 80px;" value="0.00"></td>
                    </tr>
                    <tr>
                        <td>៣</td>
                        <td>បណ្ណាប្រកាសធ្វើអំពីក្រដាសស្រាល<br>COMMERCIAL ADVERTISEMENTS MADE OF PLAIN PAPER</td>
                        <td>៛ <input type="text" class="input-field" style="width: 80px;" value="0.00"></td>
                    </tr>
                    <tr>
                        <td>៤</td>
                        <td>បណ្ណាប្រកាសធ្វើអំពីចំហស្រា កំណាត់សំពត់ ឬវត្ថុផ្សេងៗ<br>COMMERCIAL ADVERTISEMENTS MADE OF RUBBER, FABRIC STRIP OR OTHER MATERIALS</td>
                        <td>៛ <input type="text" class="input-field" style="width: 80px;" value="0.00"></td>
                    </tr>
                    <tr>
                        <td>៥</td>
                        <td>ពន្ធបន្ថែម និងការប្រាក់<br>ADDITIONAL TAX AND INTEREST</td>
                        <td>៛ <input type="text" class="input-field" style="width: 80px;" value="0.00"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right;">
                            <span class="khmer">សូមភ្ជាប់បញ្ជីព័ត៌មានអំពីផ្ទាំងផ្សាពវផ្សាយ</span><br>
                            <span class="english">PLEASE ATTACH LIST OF ADVERTISING BOARD INFORMATION</span>
                        </td>
                        <td>៛ <input type="text" class="input-field" style="width: 80px;" value="0.00"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right; font-weight: bold;">
                            <span class="khmer">សរុប</span><br>
                            <span class="english">TOTAL</span>
                        </td>
                        <td style="font-weight: bold;">៛ 0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Section III: Declaration -->
        <div class="section">
            <div class="section-title">
                III. សច្ចាប័នប្រកាស / DECLARATION
            </div>
            
            <p style="text-align: justify; margin-bottom: 20px;">
                <span class="khmer">យើងខ្ញុំបានពិនិត្យរក្សាទុកឆ្លងឆ្នាំ ឆ្នាំទាំងអស់ចៅក្នុងលិខិតប្រកាសចុះ និងរាងឧបសម្ព័ន្ធភ្ជាប់ជាមួយ។ យើងខ្ញុំមានឯកសារបញ្ជាក់ខ្លឹមសារលាស់ ត្រឹមត្រូវ ពេញលេញ ដើម្បីធានាបានថា ព័ត៌មានទាំងអស់ចៅក្នុងលិខិតប្រកាសចុះ ពិតជាត្រឹមត្រូវតាមពិតដូច្នេះ ហើយគ្មានប្រតិបត្តិការណាមួយមិនបានប្រកាសចៅោះចៅ។ យើងខ្ញុំសូមទទួលខុសត្រូវទាំងត្រឹមត្រួចំពោះមុខច្បាប់ទាំងឡាយជាធរមាន ប្រសិនបើព័ត៌មានណាមួយមានការក្លែងបន្លំ។</span><br>
                <span class="english">WE HAVE EXAMINED ALL ITEMS ON THIS RETURN AND THE ANNEXES ATTACHED HERE WITH. WE HAVE CLEAR, CORRECT AND FULL SUPPORTING DOCUMENTS TO ENSURE THAT ALL INFORMATION ON THIS RETURN IS TRUE AND ACCURATE AND THERE IS NO BUSINESS OPERATION UNDECLARED. WE ARE FULLY RESPONSIBLE DUE TO THE EXISTING LAWS FOR ANY FALSIFIED INFORMATION.</span>
            </p>
            
            <div style="text-align: center; margin-top: 30px;">
                <button style="padding: 5px 15px; background-color: #f0f0f0; border: 1px solid #000; cursor: pointer;">
                    <span class="khmer">សំអាតទម្រង់</span>
                    <span class="english">RESET FORM</span>
                </button>
            </div>
        </div>
        
        <div class="page-number">1/2</div>
    </div>
    
    <!-- Page 2 -->
    <div class="form-container" style="margin-top: 20px;">
        <!-- Official Use Section (Page 2) -->
        <div class="official-use">
            <div class="official-use-item">
                <div class="signature-line"></div>
                <div class="label">
                    <span class="khmer">ចាត់ចែង</span>
                    <span class="english">FILLED IN</span>
                </div>
            </div>
            <div class="official-use-item">
                <div class="signature-line"></div>
                <div class="label">
                    <span class="khmer">អភិបាល/បញ្ញធិការ/កម្មសិទ្ធិករ សហគ្រាស</span>
                    <span class="english">DIRECTOR/MANAGER/OWNER OF ENTERPRISE</span>
                </div>
                <div class="signature-line"></div>
                <div class="label">
                    <span class="khmer">ហត្ថលេខា ឈ្មោះ និងត្រា</span>
                    <span class="english">SIGNATURE, NAME AND SEAL</span>
                </div>
            </div>
        </div>
        
        <div class="page-number">
            <span class="khmer">ទំព័រទី</span>
            <span class="english">PAGE 2</span>
        </div>
        
        <!-- Section IV: Advertising Board Information -->
        <div class="section">
            <div class="section-title">
                IV. ព័ត៌មានអំពីផ្ទាំងផ្សាពវផ្សាយ / ADVERTISING BOARD INFORMATION
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">លេខអត្តសញ្ញាណកម្មសារពាណិជ្ជកម្ម</span>
                        <span class="english">TAX IDENTIFICATION NUMBER (TIN)</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">ឈ្មោះអ្នករក្សាសហគ្រាស</span>
                        <span class="english">NAME OF ENTERPRISE OWNER</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">ឈ្មោះសហគ្រាស</span>
                        <span class="english">NAME OF ENTERPRISE</span>
                    </div>
                    <div class="input-field"></div>
                </div>
                <div class="col">
                    <div class="label">
                        <span class="khmer">សកម្មភាពអាជីវកម្ម</span>
                        <span class="english">BUSINESS ACTIVITY</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="label">
                        <span class="khmer">ឈ្មោះសហគ្រាសជាអក្សរឡាតាំង</span>
                        <span class="english">NAME OF ENTERPRISE IN LATIN</span>
                    </div>
                    <div class="input-field"></div>
                </div>
            </div>
            
            <!-- Business Label Table -->
            <div style="margin-top: 15px;">
                <div class="label">
                    <span class="khmer">ស្លាកអាជីវកម្មដាក់</span>
                    <span class="english">BUSINESS LABEL</span>
                </div>
                
                <table class="tax-table">
                    <thead>
                        <tr>
                            <th rowspan="2">ក្រឡាផ្ទៃសរុប (ដម២)<br>TOTAL AREA (dm²)</th>
                            <th rowspan="2">តម្លៃ/ដម២<br>RATE/dm²</th>
                            <th rowspan="2">ចំនួនពន្ធ<br>TAX AMOUNT</th>
                            <th colspan="3">តួអក្សរបរទេស x រយៈពស់ (ដម)<br>FOREIGN LETTERS X HEIGHT (dm)</th>
                            <th rowspan="2">ចំនួនពន្ធត្រូវបង់<br>TAX AMOUNT TO BE PAID</th>
                        </tr>
                        <tr>
                            <th>តម្លៃ/ដម២<br>RATE/dm²</th>
                            <th>ចំនួនពន្ធ<br>TAX AMOUNT</th>
                        </tr>
                        <tr>
                            <th>A</th>
                            <th>B</th>
                            <th>C = A x B</th>
                            <th>D</th>
                            <th>E</th>
                            <th>F = D x E</th>
                            <th>G = C + F</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="input-field" style="width: 50px;"></td>
                            <td>100 ៛</td>
                            <td>៛ <input type="text" class="input-field" style="width: 50px;" value="0.00"></td>
                            <td><input type="text" class="input-field" style="width: 50px;"></td>
                            <td>200 ៛</td>
                            <td>៛ <input type="text" class="input-field" style="width: 50px;" value="0.00"></td>
                            <td>៛ <input type="text" class="input-field" style="width: 50px;" value="0.00"></td>
                        </tr>
                        <!-- Additional rows for other types -->
                        <!-- ... -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" style="text-align: right; font-weight: bold;">
                                <span class="khmer">សរុប</span><br>
                                <span class="english">TOTAL</span>
                            </td>
                            <td style="font-weight: bold;">៛ 0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Additional tables for other advertisement types -->
            <!-- ... -->
            
        </div>
        
        <div class="page-number">2/2</div>
    </div>

    <script>
        // JavaScript for form functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Calculate tax amounts when inputs change
            const calculateTax = function(input) {
                const row = input.closest('tr');
                const a = parseFloat(row.querySelector('td:nth-child(1) input').value) || 0;
                const b = parseFloat(row.querySelector('td:nth-child(2)').textContent) || 0;
                const c = a * b;
                row.querySelector('td:nth-child(3) input').value = c.toFixed(2);
                
                const d = parseFloat(row.querySelector('td:nth-child(4) input').value) || 0;
                const e = parseFloat(row.querySelector('td:nth-child(5)').textContent) || 0;
                const f = d * e;
                row.querySelector('td:nth-child(6) input').value = f.toFixed(2);
                
                const g = c + f;
                row.querySelector('td:nth-child(7) input').value = g.toFixed(2);
                
                // Update totals
                updateTotals();
            };
            
            // Update all totals
            const updateTotals = function() {
                // Update table totals
                document.querySelectorAll('.tax-table tbody').forEach(table => {
                    let total = 0;
                    table.querySelectorAll('tr').forEach(row => {
                        const amount = parseFloat(row.querySelector('td:last-child input').value) || 0;
                        total += amount;
                    });
                    table.closest('table').querySelector('tfoot td:last-child').textContent = '៛ ' + total.toFixed(2);
                });
                
                // Update section totals
                // ...
            };
            
            // Add event listeners to all inputs
            document.querySelectorAll('.tax-table input').forEach(input => {
                input.addEventListener('input', function() {
                    calculateTax(this);
                });
                
                // Allow only numbers
                input.addEventListener('keypress', function(e) {
                    if ((e.key < '0' || e.key > '9') && e.key !== '.') {
                        e.preventDefault();
                    }
                });
            });
            
            // Reset form button
            document.querySelector('button').addEventListener('click', function() {
                document.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });
                document.querySelectorAll('.tax-table tfoot td:last-child').forEach(td => {
                    td.textContent = '៛ 0.00';
                });
            });
        });
    </script>
</body>
</html>