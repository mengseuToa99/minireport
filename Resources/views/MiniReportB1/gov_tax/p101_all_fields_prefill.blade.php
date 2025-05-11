<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P101 Tax Form with All Fields Pre-filled</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .toolbar {
            background: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .toolbar button {
            padding: 8px 15px;
            margin: 0 5px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .toolbar button:hover {
            background: #0069d9;
        }
        #pdf-viewer {
            flex-grow: 1;
            width: 100%;
            height: calc(100% - 50px);
            overflow: auto;
            background-color: #525659;
            text-align: center;
        }
        .loading-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            color: white;
            font-size: 1.2rem;
        }
        #pdf-render-canvas {
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        .debug-panel {
            position: fixed;
            bottom: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: #fff;
            padding: 10px;
            max-width: 400px;
            max-height: 300px;
            overflow: auto;
            font-size: 12px;
            z-index: 1000;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="toolbar">
            <button id="downloadBtn" disabled>Download Pre-filled Form</button>
            <button id="backBtn">Back to Form Options</button>
            <button id="toggleDebugBtn">Toggle Debug Info</button>
        </div>
        <div id="pdf-viewer">
            <div class="loading-indicator" id="loading">
                <div>
                    <h3>Loading and pre-filling all form fields...</h3>
                    <p>All fields are pre-filled with test data. The form remains interactive for further editing.</p>
                </div>
            </div>
        </div>
        <div class="debug-panel" id="debugPanel">
            <h4>Debug Information</h4>
            <div id="debugOutput"></div>
        </div>
    </div>

    <!-- Load PDF.js and pdf-lib.js libraries -->
    <script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
    <script src="https://unpkg.com/downloadjs@1.4.7/download.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.7.107/pdf.min.js"></script>
    
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.7.107/pdf.worker.min.js';
        const DEBUG = true;
        let modifiedPdfBytes = null;
        function log(...args) {
            if (DEBUG) {
                console.log(...args);
                const debugOutput = document.getElementById('debugOutput');
                const message = args.map(arg => typeof arg === 'object' ? JSON.stringify(arg) : arg).join(' ');
                const logLine = document.createElement('div');
                logLine.textContent = message;
                debugOutput.appendChild(logLine);
                debugOutput.scrollTop = debugOutput.scrollHeight;
            }
        }
        
        async function fillForm() {
            try {
                // Get current date for date fields
                const now = new Date();
                const day = String(now.getDate()).padStart(2, '0');
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const year = now.getFullYear().toString();
                
                // Load the PDF
                const pdfUrl = "{{ asset('modules/minireportb1/pdf/1.pdf') }}";
                const existingPdfBytes = await fetch(pdfUrl).then(res => res.arrayBuffer());
                const pdfDoc = await PDFLib.PDFDocument.load(existingPdfBytes);
                const form = pdfDoc.getForm();
                const fields = form.getFields();
                
                log('📋 Available form fields:');
                fields.forEach(field => {
                    const type = field.constructor.name;
                    const name = field.getName();
                    let value = '';
                    
                    try {
                        if (type.includes('Text')) {
                            value = field.getText() || '(empty)';
                        } else if (type.includes('Button')) {
                            value = field.isChecked() ? 'Checked' : 'Unchecked';
                        }
                        log(`- ${name} (${type.charAt(0).toLowerCase()}): ${value}`);
                    } catch (e) {
                        log(`- ${name} (${type.charAt(0).toLowerCase()}): Error reading value`);
                    }
                });
                
                // Specific test data based on the actual field names
                let fieldsFilledCount = 0;
                
                // Fill the form with comprehensive test data based on the actual field names
                const fillTextField = (fieldName, value) => {
                    try {
                        const field = form.getTextField(fieldName);
                        field.setText(value);
                        log(`✅ Filled field '${fieldName}' with: ${value}`);
                        fieldsFilledCount++;
                        return true;
                    } catch (e) {
                        log(`❌ Error filling field '${fieldName}': ${e.message}`);
                        return false;
                    }
                };
                
                const checkCheckbox = (fieldName) => {
                    try {
                        const field = form.getCheckBox(fieldName);
                        field.check();
                        log(`✅ Checked checkbox '${fieldName}'`);
                        fieldsFilledCount++;
                        return true;
                    } catch (e) {
                        log(`❌ Error checking checkbox '${fieldName}': ${e.message}`);
                        return false;
                    }
                };
                
                // Fill Date Fields
                fillTextField('MM', month);
                fillTextField('YYYY', year);
                fillTextField('Text1', day);
                fillTextField('Text2', month);
                fillTextField('Text3', year);
                
                // Tax branch section
                fillTextField('TextNumber', 'P101-12345');
                fillTextField('Tax branch', 'Central Branch');
                
                // Company details section
                fillTextField('Text4', 'ABC Corporation');  // Company name
                fillTextField('Text5', 'Technology Services');  // Business type
                fillTextField('Text6', 'B1234-5678');  // Tax ID number
                fillTextField('Text7', '123 Business Street, Phnom Penh');  // Address
                
                // Fill every text field with appropriate test data
                for (let i = 8; i <= 242; i++) {
                    const fieldName = `Text${i}`;
                    let value;
                    
                    // Determine value based on field position/purpose
                    if (i >= 8 && i <= 20) {
                        // Financial figures
                        value = `${Math.floor(Math.random() * 10000) + 1000}`;
                    } else if (i >= 21 && i <= 30) {
                        // Percentages or small numbers
                        value = `${Math.floor(Math.random() * 100)}`;
                    } else if (i >= 31 && i <= 40) {
                        // Dates
                        value = `${day}/${month}/${year}`;
                    } else if (i >= 41 && i <= 50) {
                        // Dollar amounts
                        value = `$${Math.floor(Math.random() * 100000) + 10000}`;
                    } else if ((i - 1) % 6 === 0) {
                        // For fields like account codes
                        value = `AC${Math.floor(Math.random() * 1000) + 1000}`;
                    } else if ((i - 2) % 6 === 0) {
                        // For fields like item descriptions
                        value = `Item ${Math.floor(i/6)}`;
                    } else if ((i - 3) % 6 === 0 || (i - 6) % 6 === 0) {
                        // For amounts
                        value = `${Math.floor(Math.random() * 100000) + 5000}`;
                    } else if ((i - 4) % 6 === 0) {
                        // For percentages
                        value = `${Math.floor(Math.random() * 20) + 1}%`;
                    } else if ((i - 5) % 6 === 0) {
                        // For calculated values
                        value = `${Math.floor(Math.random() * 20000) + 1000}`;
                    } else {
                        // Generic value
                        value = `Value ${i}`;
                    }
                    
                    fillTextField(fieldName, value);
                }
                
                // Check every other checkbox for demonstration
                const checkboxNames = [
                    'Check Box1', 'Check Box3', 'Check Box5', 'Check Box7', 'Check Box9',
                    'Check Box11', 'Check Box13', 'Check Box15', 'Check Box17', 'Check Box19',
                    'Check Box21', 'Check Box23', 'Check Box25', 'Check Box27', 'Check Box29',
                    'Check Box31', 'Check Box33', 'Check Box35', 'Check Box37', 'Check Box39', 'Check Box41'
                ];
                
                checkboxNames.forEach(name => {
                    checkCheckbox(name);
                });
                
                log(`📊 Total fields filled: ${fieldsFilledCount}`);
                
                // Save the PDF without flattening to keep it interactive
                modifiedPdfBytes = await pdfDoc.save();
                
                // Display the filled PDF
                renderPDF(modifiedPdfBytes);
                
                // Enable the download button
                document.getElementById('downloadBtn').disabled = false;
                
            } catch (error) {
                console.error('Error processing PDF:', error);
                log('❌ Error details:', error);
                document.getElementById('loading').innerHTML = 
                    `Error processing PDF: ${error.message}<br><button onclick="location.reload()">Try Again</button>`;
            }
        }
        
        async function renderPDF(pdfBytes) {
            try {
                // Remove loading indicator
                document.getElementById('loading').style.display = 'none';
                
                // Convert bytes to blob for direct PDF viewing
                const blob = new Blob([pdfBytes], { type: 'application/pdf' });
                const url = URL.createObjectURL(blob);
                
                // Create iframe for better interactive PDF experience
                const iframe = document.createElement('iframe');
                iframe.src = url + '#toolbar=1&navpanes=1&scrollbar=1';
                iframe.width = '100%';
                iframe.height = '100%';
                iframe.style.border = 'none';
                iframe.setAttribute('frameborder', '0');
                
                // Set attributes to ensure form interaction works
                iframe.setAttribute('allowfullscreen', 'true');
                iframe.setAttribute('webkitallowfullscreen', 'true');
                iframe.setAttribute('mozallowfullscreen', 'true');
                
                // Add a message for users
                const note = document.createElement('div');
                note.innerHTML = '<p style="position: fixed; bottom: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 10px; border-radius: 5px; z-index: 1000;">All fields are pre-filled with test data. You can edit any field and download the form using the button above.</p>';
                document.body.appendChild(note);
                
                // Clear any existing content
                const container = document.getElementById('pdf-viewer');
                container.innerHTML = '';
                container.appendChild(iframe);
                
                log('✅ PDF rendered in iframe for better interactivity');
                
            } catch (error) {
                console.error('Error rendering PDF:', error);
                log('❌ Render error details:', error);
                document.getElementById('loading').innerHTML = 
                    `Error rendering PDF: ${error.message}<br><button onclick="location.reload()">Try Again</button>`;
                document.getElementById('loading').style.display = 'block';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            fillForm();
            document.getElementById('downloadBtn').addEventListener('click', function() {
                if (modifiedPdfBytes) {
                    const now = new Date();
                    const dateStr = now.toISOString().slice(0, 10);
                    download(modifiedPdfBytes, `p101_form_prefilled_${dateStr}.pdf`, 'application/pdf');
                }
            });
            document.getElementById('backBtn').addEventListener('click', function() {
                window.location.href = "{{ url('minireportb1/tax-gov-document') }}";
            });
            document.getElementById('toggleDebugBtn').addEventListener('click', function() {
                const debugPanel = document.getElementById('debugPanel');
                if (debugPanel.style.display === 'none' || !debugPanel.style.display) {
                    debugPanel.style.display = 'block';
                } else {
                    debugPanel.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 
