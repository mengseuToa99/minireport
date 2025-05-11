<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Count Sheet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            border: 2px solid #000;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #aaa;
            background-color: #f8f8f8;
        }
        .logo {
            width: 60px;
            height: 60px;
            margin-right: 20px;
        }
        .title-container {
            flex-grow: 1;
            text-align: center;
        }
        .khmer-title {
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .english-title {
            font-size: 20px;
            font-weight: bold;
        }
        .subtitle {
            background-color: #b8cce4;
            padding: 5px;
            text-align: center;
            border-bottom: 1px solid #aaa;
        }
        .account-info {
            display: flex;
            padding: 10px;
            border-bottom: 1px solid #aaa;
        }
        .account-label {
            width: 200px;
            font-weight: bold;
        }
        .account-value {
            flex-grow: 1;
            text-align: center;
            text-decoration: underline;
        }
        .balance-row {
            display: flex;
            padding: 10px;
            border-bottom: 1px solid #aaa;
        }
        .balance-label {
            flex-grow: 1;
            font-weight: bold;
        }
        .currency-label {
            width: 80px;
            text-align: center;
            font-weight: bold;
        }
        .amount {
            width: 120px;
            text-align: right;
            font-weight: bold;
        }
        .red-text {
            color: red;
        }
        .section-label {
            font-weight: bold;
            padding: 10px;
            border-bottom: 1px solid #aaa;
        }
        .currency-header {
            display: flex;
            padding: 10px;
            font-weight: bold;
        }
        .currency-type {
            flex-grow: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        .justification {
            padding: 10px;
            border-bottom: 1px solid #aaa;
        }
        .justification-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .justification-box {
            border: 1px solid #000;
            height: 40px;
            margin-bottom: 5px;
        }
        .justification-note {
            font-style: italic;
        }
        .signature-section {
            display: flex;
            border-top: 1px solid #aaa;
        }
        .signature-box {
            flex: 1;
            padding: 10px;
            border-right: 1px solid #aaa;
        }
        .signature-box:last-child {
            border-right: none;
        }
        .signature-label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .signature-value {
            margin-bottom: 5px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin: 20px 0 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="/api/placeholder/60/60" alt="Logo" class="logo">
            <div class="title-container">
                <div class="khmer-title">ក្រុមហ៊ុនឌី ហ្វាក់សេស ភីអិលស៊ី</div>
                <div class="english-title">CASH COUNT SHEET</div>
            </div>
        </div>

        <!-- Subtitle -->
        <div class="subtitle">AS AT   31 December 2024</div>

        <!-- Account Info -->
        <div class="account-info">
            <div class="account-label">Account Name:</div>
            <div class="account-value">Cash on hand</div>
        </div>

        <!-- Balance Row -->
        <div class="balance-row">
            <div class="balance-label">Balance per Cash Book ( Accounting System )</div>
            <div class="currency-label">USD</div>
            <div class="amount red-text">38,729.75</div>
        </div>

        <!-- Physical Count -->
        <div class="section-label">PHYSICAL COUNT</div>

        <!-- USD Currency Section -->
        <div class="currency-header">
            <div class="currency-type">USD Currency >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>></div>
            <div class="currency-label">USD</div>
            <div class="amount">37,765.00</div>
        </div>

        <table>
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
                    <td>10</td>
                    <td>10</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>71</td>
                    <td>355</td>
                </tr>
                <tr>
                    <td>10</td>
                    <td>130</td>
                    <td>1,300</td>
                </tr>
                <tr>
                    <td>20</td>
                    <td>180</td>
                    <td>3,600</td>
                </tr>
                <tr>
                    <td>50</td>
                    <td>210</td>
                    <td>10,500</td>
                </tr>
                <tr>
                    <td>100</td>
                    <td>220</td>
                    <td>22,000</td>
                </tr>
                <tr>
                    <td colspan="3">-</td>
                </tr>
            </tbody>
        </table>

        <!-- KHR Currency Section -->
        <div class="currency-header">
            <div class="currency-type">KHR Currency >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>></div>
            <div class="currency-label">USD</div>
            <div class="amount">964.75</div>
        </div>

        <table>
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
                    <td>3</td>
                    <td>300.00</td>
                </tr>
                <tr>
                    <td>200</td>
                    <td>4</td>
                    <td>800.00</td>
                </tr>
                <tr>
                    <td>500</td>
                    <td>8</td>
                    <td>4,000</td>
                </tr>
                <tr>
                    <td>1000</td>
                    <td>8</td>
                    <td>8,000</td>
                </tr>
                <tr>
                    <td>2000</td>
                    <td>60</td>
                    <td>120,000</td>
                </tr>
                <tr>
                    <td>5000</td>
                    <td>90</td>
                    <td>450,000</td>
                </tr>
                <tr>
                    <td>10000</td>
                    <td>40</td>
                    <td>400,000</td>
                </tr>
                <tr>
                    <td>20000</td>
                    <td>20</td>
                    <td>400,000</td>
                </tr>
                <tr>
                    <td>50000</td>
                    <td>50</td>
                    <td>2,500,000</td>
                </tr>
                <tr>
                    <td colspan="2">Total</td>
                    <td>3,883,100</td>
                </tr>
                <tr>
                    <td colspan="2">Exchange Rate</td>
                    <td class="red-text">4026</td>
                </tr>
            </tbody>
        </table>

        <!-- Total Physical Cash -->
        <div class="balance-row">
            <div class="balance-label">TOTAL PHYSICAL CASH COUNTED</div>
            <div class="currency-label">USD</div>
            <div class="amount red-text">38,729.75</div>
        </div>

        <!-- Difference -->
        <div class="balance-row">
            <div class="balance-label">DIFFERENT</div>
            <div class="currency-label"></div>
            <div class="amount">(0.00)</div>
        </div>

        <!-- Justification -->
        <div class="justification">
            <div class="justification-label">Justificaiton</div>
            <div class="justification-box"></div>
            <div class="justification-note">Cash should be counted daily by supervisor or invited person to count againt GL . Accounting staff and cahsier would</div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-label">Accountable by:</div>
                <div class="signature-line"></div>
                <div class="signature-value">Name: ឃុន សុវិចិត្រ</div>
                <div class="signature-value">Position: មន្ត្រីគណនីយ</div>
                <div class="signature-value">Date: 31 December 2024</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Certified by:</div>
                <div class="signature-line"></div>
                <div class="signature-value">Name: ឃុន សុវិចិត្រ</div>
                <div class="signature-value">31 December 2024</div>
            </div>
        </div>
    </div>

    <script>
        // This script could be used to add any dynamic functionality
        // For example, you could calculate totals automatically
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Cash Count Sheet loaded');
            // Any initialization code would go here
        });
    </script>
</body>
</html>