<?php
session_start();

// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


// Redirect to login page if not logged in
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'salesperson') {
//    header("Location: login.php");
//    exit();
//}

// Fetch user ID from session
$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get general sale data
    $sales_order_no = $_POST['sales_order_no'];
    $sales_invoice_no = $_POST['sales_invoice_no'];
    $reference = $_POST['reference'];
    $date = $_POST['date'];
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $payment_method = $_POST['payment_method'];

    // Loop through each item entry and insert into database
    $item_ids = $_POST['item_id'];
    $item_descriptions = $_POST['item_description'];
    $gl_accounts = $_POST['gl_account'];
    $quantities = $_POST['quantity'];
    $unit_prices = $_POST['unit_price'];
    $job_ids = $_POST['job_id'];

    $total_sales_before_vat = 0;
    $total_sales_with_vat = 0;
    $vat_amount = 0;

    foreach ($item_ids as $index => $item_id) {
        $item_description = $item_descriptions[$index];
        $gl_account = $gl_accounts[$index];
        $quantity = $quantities[$index];
        $unit_price = $unit_prices[$index];
        $total_sales = $quantity * $unit_price;
        $job_id = isset($job_ids[$index]) ? $job_ids[$index] : '';  // Allow empty job ID

        // Calculate total sales before VAT
        $total_sales_before_vat += $total_sales;

        // Insert sale into database
        $insert_sale_query = "INSERT INTO sales (salesperson_id, sales_order_no, invoice_no, reference, date, customer_id, customer_name, payment_method, item_id, item_description, gl_account, quantity, unit_price, total_sales, job_id) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sale_query);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("issssssssdddsd", $user_id, $sales_order_no, $sales_invoice_no, $reference, $date, $customer_id, $customer_name, $payment_method, $item_id, $item_description, $gl_account, $quantity, $unit_price, $total_sales, $job_id);

        if ($stmt->execute()) {
            $success_message = "Sale added successfully!";
        } else {
            $error_message = "Error: " . $conn->error;
        }

        $stmt->close();
    }

    // Calculate VAT (15%)
    $vat_amount = $total_sales_before_vat * 0.15;
    $total_sales_with_vat = $total_sales_before_vat + $vat_amount;
}
?>


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .footer {
            margin-top: 20px;
            background-color: #343a40;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .form-label {
            font-weight: bold;
        }

        .table-excel {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
        }

        .table-excel th,
        .table-excel td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        .table-excel th {
            background-color: #f4f4f9;
            font-weight: bold;
        }

        .table-excel tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table-excel tr:hover {
            background-color: #f1f1f1;
        }

        .table-excel input[type="number"],
        .table-excel input[type="text"] {
            width: 80%;
            padding: 5px;
            font-size: 14px;
        }

        .btn-back {
            margin-top: 20px;
        }

        .row-input {
            margin-bottom: 15px;
        }

        .table-excel input {
            width: 100%;
        }

        .summary-section {
            background-color: #f8f9fa;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .summary-section h5 {
            font-weight: bold;
        }

        .summary-section p {
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <h1 class="text-center">Sales Invoice</h1>

        <!-- Display success or error message -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Sale Form -->
        <div class="card p-4">
            <form action="add_sale.php" method="POST" id="sales_form">
                <!-- Sales General Information in One Row -->
                <div class="row row-input">
                    <div class="col-md-2">
                        <label for="sales_order_no" class="form-label">Sales Order No.</label>
                        <input type="text" class="form-control" id="sales_order_no" name="sales_order_no">
                    </div>
                    <div class="col-md-2">
                        <label for="sales_invoice_no" class="form-label">Sales Invoice No.</label>
                        <input type="text" class="form-control" id="sales_invoice_no" name="sales_invoice_no" required>
                    </div>
                    <div class="col-md-2">
                        <label for="reference" class="form-label">Reference</label>
                        <input type="text" class="form-control" id="reference" name="reference" required>
                    </div>
              
                <div class="col-md-2">
                        <label for="date" class="form-label">Date</label>
                        <input type="text" class="form-control" id="date" name="date" required>
                    </div>
                </div>
                <!-- Customer Information in One Row -->
                <div class="row row-input">
                    <div class="col-md-2">
                        <label for="customer_id" class="form-label">Customer ID</label>
                        <input type="text" class="form-control" id="customer_id" name="customer_id" required>
                    </div>
                    <div class="col-md-4">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="col-md-2">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                </div>

                <!-- Item Details Table (Excel-style) -->



    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function calculateTotal(input) {
            const row = input.closest("tr");
            const quantity = parseFloat(row.querySelector('[name="quantity[]"]').value) || 0;
            const unitPrice = parseFloat(row.querySelector('[name="unit_price[]"]').value) || 0;
            const totalSales = quantity * unitPrice;
            row.querySelector('[name="total_sales[]"]').value = totalSales.toFixed(2);
            updateSalesSummary();
        }

        function addRow() {
            const table = document.getElementById("item_table").getElementsByTagName("tbody")[0];
            const newRow = table.insertRow();

            newRow.innerHTML = `
                <td><input type="text" class="form-control" name="item_id[]" required></td>
                <td><input type="text" class="form-control" name="item_description[]" required></td>
                <td><input type="text" class="form-control" name="gl_account[]" required></td>
                <td><input type="number" class="form-control" name="quantity[]" step="1" min="1" oninput="calculateTotal(this)" required></td>
                <td><input type="number" class="form-control" name="unit_price[]" step="0.01" min="0.01" oninput="calculateTotal(this)" required></td>
                <td><input type="number" class="form-control" name="total_sales[]" readonly></td>
                <td><input type="text" class="form-control" name="job_id[]"></td>
                <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remove</button></td>
            `;
        }

        function removeRow(button) {
            const row = button.closest("tr");
            row.remove();
            updateSalesSummary();
        }

        function updateSalesSummary() {
            let totalBeforeVAT = 0;
            document.querySelectorAll('[name="total_sales[]"]').forEach(input => {
                totalBeforeVAT += parseFloat(input.value) || 0;
            });

            const vatAmount = totalBeforeVAT * 0.15;
            const totalWithVAT = totalBeforeVAT + vatAmount;

            document.getElementById("total_sales_before_vat").textContent = totalBeforeVAT.toFixed(2);
            document.getElementById("vat_amount").textContent = vatAmount.toFixed(2);
            document.getElementById("sales_with_vat").textContent = totalWithVAT.toFixed(2);

            document.getElementById("amount_in_words").textContent = `Total in Words: ${toWords(totalWithVAT)} Birr only.`;
        }

        function toWords(amount) {
            const ones = [
                "", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine",
                "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen",
                "Seventeen", "Eighteen", "Nineteen",
            ];
            const tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
            const scales = ["", "Thousand", "Million", "Billion"];

            if (amount === 0) return "Zero";

            const words = [];
            const numStr = Math.floor(amount).toString();
            const numParts = numStr.match(/.{1,3}(?=(.{3})*$)/g).reverse();

            numParts.forEach((part, index) => {
                if (parseInt(part) === 0) return;

                let str = "";
                const hundreds = Math.floor(part / 100);
                const remainder = part % 100;
                if (hundreds > 0) str += ones[hundreds] + " Hundred ";
                if (remainder < 20) str += ones[remainder];
                else str += tens[Math.floor(remainder / 10)] + " " + ones[remainder % 10];

                words.push(str + (scales[index] ? " " + scales[index] : ""));
            });

            return words.reverse().join(" ").trim();
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Sales Invoice</h1>

        <table id="item_table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Description</th>
                    <th>GL Account</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Sales</th>
                    <th>Job ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be added dynamically -->
            </tbody>
        </table>

        <button type="button" class="btn btn-primary mb-4" onclick="addRow()">Add Item</button>

        <div>
            <p><strong>Total Before VAT:</strong> Birr<span id="total_sales_before_vat">0.00</span></p>
            <p><strong>VAT (15%):</strong> Birr<span id="vat_amount">0.00</span></p>
            <p><strong>Total Sales with VAT:</strong> Birr<span id="sales_with_vat">0.00</span></p>
            <p id="amount_in_words"><strong>Total in Words:</strong> Zero Birr only.</p>
        </div>
    </div>
    
    <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Save Sales</button>
                </div>
        
</body>
</html>
kkkkkkkkkk
<?php
// Include necessary libraries for PDF and Excel exports



$html = "<h1>Sales Report</h1><p>Details of sales go here...</p>";

// Company details
$companyName = "ABC Company";
$companyAddress = "Addis Ababa, Ethiopia";

// Sample invoice data
$invoiceItems = [
    ['description' => 'Compact Car', 'quantity' => 2, 'unit_price' => 15000.00],
    ['description' => 'Midsize Sedan', 'quantity' => 1, 'unit_price' => 25000.00],
    ['description' => 'Luxury Car', 'quantity' => 1, 'unit_price' => 40000.00],
];

// Calculate totals
$total = 0;
foreach ($invoiceItems as &$item) {
    $item['total_price'] = $item['quantity'] * $item['unit_price'];
    $total += $item['total_price'];
}
unset($item);

// Format numbers
function formatCurrency($amount) {
    return number_format($amount, 2, '.', ',');
}

// Generate HTML content
$htmlContent = "
<html>
<head>
    <title>Invoice</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .company-details { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class='company-details'>
        <h2>$companyName</h2>
        <p>$companyAddress</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>";

foreach ($invoiceItems as $item) {
    $htmlContent .= "
            <tr>
                <td>{$item['description']}</td>
                <td>{$item['quantity']}</td>
                <td>" . formatCurrency($item['unit_price']) . "</td>
                <td>" . formatCurrency($item['total_price']) . "</td>
            </tr>";
}

$htmlContent .= "
        </tbody>
        <tfoot>
            <tr>
                <th colspan='3'>Grand Total</th>
                <th>" . formatCurrency($total) . "</th>
            </tr>
        </tfoot>
    </table>
    <br>
    <button onclick='window.print()'>Print</button>
    <button onclick='exportToExcel()'>Export to Excel</button>
    <button onclick='exportToPDF()'>Export to PDF</button>

    <script>
        function exportToExcel() {
            let table = document.querySelector('table');
            let downloadLink = document.createElement('a');
            let dataType = 'application/vnd.ms-excel';
            let tableHTML = table.outerHTML.replace(/ /g, '%20');

            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
            downloadLink.download = 'invoice.xls';
            downloadLink.click();
        }

        function exportToPDF() {
            const doc = new jsPDF();
            doc.text('$companyName', 10, 10);
            doc.text('$companyAddress', 10, 20);
            doc.autoTable({
                html: 'table',
                startY: 30
            });
            doc.save('invoice.pdf');
        }
    </script>
</body>
</html>";

// Display the HTML content
echo $htmlContent;
?>