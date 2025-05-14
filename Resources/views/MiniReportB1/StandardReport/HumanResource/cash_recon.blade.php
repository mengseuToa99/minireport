<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Count Sheet</title>
    <style>
        body {
            align-items: center;
            width: 80%;
            font-family: Arial, sans-serif;
            margin: 0 auto; /* Centers horizontally */
            padding: 20px;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            margin-right: 20px;
        }
        .title {
            flex-grow: 1;
            text-align: center;
        }
        .khmer-title {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .english-title {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }
        .date-header {
            background-color: #a8c6e5;
            padding: 8px 0;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .account-name {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .account-name label {
            font-weight: bold;
            margin-right: 10px;
        }
        .account-name input {
            flex-grow: 1;
            border: none;
            border-bottom: 1px solid black;
            text-align: center;
            font-weight: bold;
            padding: 5px 0;
        }
        .balance-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .balance-label {
            font-weight: bold;
        }
        .balance-value {
            display: flex;
            align-items: center;
        }
        .balance-value .currency {
            font-weight: bold;
            margin-right: 10px;
        }
        .balance-value input {
            width: 150px;
            text-align: right;
            padding: 5px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .currency-section {
            margin-bottom: 20px;
        }
        .currency-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .currency-label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        td input {
            width: 95%;
            border: none;
            text-align: right;
            padding: 5px 0;
        }
        .justification {
            margin-top: 20px;
        }
        .justification-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .justification-box {
            border: 1px solid black;
            padding: 10px;
            margin-bottom: 20px;
            min-height: 100px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .signature-box {
            width: 48%;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 20px;
        }
        .signature-line {
            border-top: 1px solid black;
            margin: 40px 0 20px 0;
        }
        .signature-name, .signature-position {
            margin-bottom: 10px;
        }
        .signature-label {
            display: inline-block;
            width: 80px;
            font-weight: bold;
        }
        .red-text {
            color: red;
        }
        .total-row {
            font-weight: bold;
        }
    </style>
</head>
@include("minireportb1::MiniReportB1.components.back_to_dashboard_button")
<body>

    <div class="header">
        <img src="/api/placeholder/80/80" alt="Foxest Logo" class="logo">
        <div class="title">
            <div class="khmer-title red-text">លិខិតរាប់សាច់ប្រាក់</div>
            <h1 class="english-title">CASH COUNT SHEET</h1>
        </div>
    </div>

    <div class="date-header">
        AS AT <input type="text" value="31 December 2024" style="width: 200px; border: none; background: transparent; font-weight: bold; text-align: center;">
    </div>

    <div class="account-name">
        <label>Account Name:</label>
        <input type="text" value="Cash on hand">
    </div>

    <div class="balance-row">
        <div class="balance-label">Balance per Cash Book ( Accounting System )</div>
        <div class="balance-value">
            <div class="currency">USD</div>
            <input type="text" id="book-balance" value="38,729.75" disabled>
        </div>
    </div>

    <div class="section-title">PHYSICAL COUNT</div>

    <div class="currency-section">
        <div class="currency-header">
            <div class="currency-label red-text">USD Currency >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>></div>
            <div class="balance-value">
                <div class="currency">USD</div>
                <input type="text" id="usd-total" value="37,765.00" disabled>
            </div>
        </div>

        <table id="usd-table">
            <thead>
                <tr>
                    <th>Bank Note</th>
                    <th>Qty</th>
                    <th>USD Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><input type="number" class="usd-qty" data-denomination="1" value="10"></td>
                    <td><input type="text" class="usd-amount" value="10" disabled></td>
                </tr>
                <tr>
                    <td>5</td>
                    <td><input type="number" class="usd-qty" data-denomination="5" value="71"></td>
                    <td><input type="text" class="usd-amount" value="355" disabled></td>
                </tr>
                <tr>
                    <td>10</td>
                    <td><input type="number" class="usd-qty" data-denomination="10" value="130"></td>
                    <td><input type="text" class="usd-amount" value="1,300" disabled></td>
                </tr>
                <tr>
                    <td>20</td>
                    <td><input type="number" class="usd-qty" data-denomination="20" value="180"></td>
                    <td><input type="text" class="usd-amount" value="3,600" disabled></td>
                </tr>
                <tr>
                    <td>50</td>
                    <td><input type="number" class="usd-qty" data-denomination="50" value="210"></td>
                    <td><input type="text" class="usd-amount" value="10,500" disabled></td>
                </tr>
                <tr>
                    <td>100</td>
                    <td><input type="number" class="usd-qty" data-denomination="100" value="220"></td>
                    <td><input type="text" class="usd-amount" value="22,000" disabled></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="currency-section">
        <div class="currency-header">
            <div class="currency-label red-text">KHR Currency >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>></div>
            <div class="balance-value">
                <div class="currency">USD</div>
                <input type="text" id="khr-usd-total" value="964.75" disabled>
            </div>
        </div>

        <table id="khr-table">
            <thead>
                <tr>
                    <th>Bank Note</th>
                    <th>Qty</th>
                    <th>KHR Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>100</td>
                    <td><input type="number" class="khr-qty" data-denomination="100" value="3"></td>
                    <td><input type="text" class="khr-amount" value="300.00" disabled></td>
                </tr>
                <tr>
                    <td>200</td>
                    <td><input type="number" class="khr-qty" data-denomination="200" value="4"></td>
                    <td><input type="text" class="khr-amount" value="800.00" disabled></td>
                </tr>
                <tr>
                    <td>500</td>
                    <td><input type="number" class="khr-qty" data-denomination="500" value="8"></td>
                    <td><input type="text" class="khr-amount" value="4,000" disabled></td>
                </tr>
                <tr>
                    <td>1000</td>
                    <td><input type="number" class="khr-qty" data-denomination="1000" value="8"></td>
                    <td><input type="text" class="khr-amount" value="8,000" disabled></td>
                </tr>
                <tr>
                    <td>2000</td>
                    <td><input type="number" class="khr-qty" data-denomination="2000" value="60"></td>
                    <td><input type="text" class="khr-amount" value="120,000" disabled></td>
                </tr>
                <tr>
                    <td>5000</td>
                    <td><input type="number" class="khr-qty" data-denomination="5000" value="90"></td>
                    <td><input type="text" class="khr-amount" value="450,000" disabled></td>
                </tr>
                <tr>
                    <td>10000</td>
                    <td><input type="number" class="khr-qty" data-denomination="10000" value="40"></td>
                    <td><input type="text" class="khr-amount" value="400,000" disabled></td>
                </tr>
                <tr>
                    <td>20000</td>
                    <td><input type="number" class="khr-qty" data-denomination="20000" value="20"></td>
                    <td><input type="text" class="khr-amount" value="400,000" disabled></td>
                </tr>
                <tr>
                    <td>50000</td>
                    <td><input type="number" class="khr-qty" data-denomination="50000" value="50"></td>
                    <td><input type="text" class="khr-amount" value="2,500,000" disabled></td>
                </tr>
                <tr class="total-row">
                    <td colspan="2">Total</td>
                    <td><input type="text" id="khr-total" value="3,883,100" disabled></td>
                </tr>
                <tr>
                    <td colspan="2">Exchange Rate</td>
                    <td><input type="number" id="exchange-rate" value="4025" class="red-text"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="balance-row">
        <div class="balance-label">TOTAL PHYSICAL CASH COUNTED</div>
        <div class="balance-value">
            <div class="currency">USD</div>
            <input type="text" id="total-physical-cash" value="38,729.75" disabled>
        </div>
    </div>

    <div class="balance-row">
        <div class="balance-label red-text">DIFFERENT</div>
        <div class="balance-value">
            <input type="text" id="difference" value="(0.00)" disabled>
        </div>
    </div>

    <div class="justification">
        <div class="justification-title">Justificaiton</div>
        <div class="justification-box">
            Cash should be counted daily by supervisor or invited person to count againt GL. Accounting staff and cashier would be a separated person. In good practices, a susprised cash account should have been done once a month by non financial person or financial manager.
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">Accountable by:</div>
            <div class="signature-line"></div>
            <div class="signature-name">
                <span class="signature-label">Name:</span>
                <span class="red-text">ឈុន ស៊ីណាត</span>
            </div>
            <div class="signature-position">
                <span class="signature-label">Position:</span>
                <span class="red-text">ប្រធានហិរញ្ញិក</span>
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-title">Certified by:</div>
            <div class="signature-line"></div>
            <div class="signature-name">
                <span class="signature-label">Name:</span>
                <span class="red-text">ឈុន ស៊ីណាត</span>
            </div>
            <div class="signature-position">
                <span class="signature-label">Position:</span>
            </div>
        </div>
    </div>

    <script>
        // Format number with commas
        function formatNumber(num) {
            return new Intl.NumberFormat('en-US', { 
                minimumFractionDigits: num % 1 === 0 ? 0 : 2,
                maximumFractionDigits: 2
            }).format(num);
        }

        // Calculate USD totals
        function calculateUsdTotal() {
            let total = 0;
            document.querySelectorAll('.usd-qty').forEach(input => {
                const qty = parseInt(input.value) || 0;
                const denomination = parseInt(input.dataset.denomination);
                const amount = qty * denomination;
                total += amount;
                
                // Update the amount field
                const amountField = input.closest('tr').querySelector('.usd-amount');
                amountField.value = formatNumber(amount);
            });
            
            document.getElementById('usd-total').value = formatNumber(total);
            calculateTotalPhysicalCash();
        }

        // Calculate KHR totals
        function calculateKhrTotal() {
            let totalKHR = 0;
            document.querySelectorAll('.khr-qty').forEach(input => {
                const qty = parseInt(input.value) || 0;
                const denomination = parseInt(input.dataset.denomination);
                const amount = qty * denomination;
                totalKHR += amount;
                
                // Update the amount field
                const amountField = input.closest('tr').querySelector('.khr-amount');
                amountField.value = formatNumber(amount);
            });
            
            document.getElementById('khr-total').value = formatNumber(totalKHR);
            
            // Calculate USD equivalent
            const exchangeRate = parseFloat(document.getElementById('exchange-rate').value) || 4025;
            const khrInUsd = totalKHR / exchangeRate;
            document.getElementById('khr-usd-total').value = formatNumber(khrInUsd);
            
            calculateTotalPhysicalCash();
        }

        // Calculate total physical cash and difference
        function calculateTotalPhysicalCash() {
            const usdTotal = parseFloat(document.getElementById('usd-total').value.replace(/,/g, '')) || 0;
            const khrUsdTotal = parseFloat(document.getElementById('khr-usd-total').value.replace(/,/g, '')) || 0;
            const totalPhysicalCash = usdTotal + khrUsdTotal;
            
            document.getElementById('total-physical-cash').value = formatNumber(totalPhysicalCash);
            
            // Calculate difference from book balance
            const bookBalance = parseFloat(document.getElementById('book-balance').value.replace(/,/g, '')) || 0;
            const difference = totalPhysicalCash - bookBalance;
            
            if (difference === 0) {
                document.getElementById('difference').value = "(0.00)";
            } else {
                document.getElementById('difference').value = formatNumber(difference);
            }
        }

        // Add event listeners
        document.querySelectorAll('.usd-qty').forEach(input => {
            input.addEventListener('input', calculateUsdTotal);
        });

        document.querySelectorAll('.khr-qty').forEach(input => {
            input.addEventListener('input', calculateKhrTotal);
        });

        document.getElementById('exchange-rate').addEventListener('input', calculateKhrTotal);

        // Initialize calculations
        calculateUsdTotal();
        calculateKhrTotal();
    </script>
</body>
</html>