<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sale Receipt #<?= $sale['sale_id'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .receipt {
            max-width: 80mm;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 10px;
        }
        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px;
        }
        .store-info {
            font-size: 10px;
            margin: 0 0 5px;
        }
        .receipt-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .items th, .items td {
            text-align: left;
            padding: 4px 0;
        }
        .items th {
            border-bottom: 1px solid #ddd;
        }
        .total-row td {
            border-top: 1px solid #ddd;
            font-weight: bold;
            padding-top: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            border-top: 1px dashed #ddd;
            padding-top: 10px;
        }
        .text-right {
            text-align: right;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .receipt {
                border: none;
                max-width: 100%;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1 class="store-name">Mabolo Veterinary Clinic</h1>
            <p class="store-info">Juan Luna Ave, Mabolo, Cebu City, Philippines</p>
            <p class="store-info">Phone: 233-20-39</p>
            <p class="store-info">VAT Reg TIN: 649-058-316-00000</p>
        </div>
        
        <div class="receipt-details">
            <div>
                <strong>Receipt #:</strong> <?= $sale['sale_id'] ?>
            </div>
            <div>
                <strong>Date:</strong> <?= date('M d, Y h:i A', strtotime($sale['sale_date'])) ?>
            </div>
        </div>
        
        <table class="items">
            <thead>
                <tr>
                    <th width="40%">Item</th>
                    <th width="15%">Qty</th>
                    <th width="20%" class="text-right">Price</th>
                    <th width="25%" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td class="text-right">₱<?= number_format($item['price'], 2) ?></td>
                    <td class="text-right">₱<?= number_format($item['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" class="text-right">Total:</td>
                    <td class="text-right">₱<?= number_format($sale['total_amount'], 2) ?></td>
                </tr>
            </tbody>
        </table>
        
        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>Come again!</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align:center; margin-top:20px;">
        <button onclick="window.print()">Print Receipt</button>
        <button onclick="window.close()">Close</button>
    </div>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html> 