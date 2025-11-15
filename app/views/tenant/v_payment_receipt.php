<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - <?php echo $data['payment']->transaction_id; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .receipt-header {
            background: linear-gradient(135deg, #45a9ea 0%, #3b8fd9 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .receipt-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .receipt-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .receipt-body {
            padding: 40px;
        }

        .receipt-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
        }

        .info-block h3 {
            color: #45a9ea;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .info-row label {
            color: #666;
            font-weight: 500;
        }

        .info-row span {
            color: #333;
            font-weight: 600;
        }

        .payment-details {
            background: #f9fafb;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .payment-details h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .amount-row:last-child {
            border-bottom: none;
            padding-top: 15px;
            margin-top: 10px;
            border-top: 2px solid #45a9ea;
        }

        .amount-row.total {
            font-size: 20px;
            font-weight: bold;
            color: #45a9ea;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            background: #d1fae5;
            color: #065f46;
        }

        .transaction-id {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 30px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .receipt-footer {
            padding: 30px 40px;
            background: #f9fafb;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            color: #666;
        }

        .print-button {
            background: #45a9ea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin: 20px 0;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .print-button:hover {
            background: #3b8fd9;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                border-radius: 0;
            }

            .print-button {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .receipt-info {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .receipt-header {
                padding: 30px 20px;
            }

            .receipt-header h1 {
                font-size: 24px;
            }

            .receipt-body {
                padding: 20px;
            }

            .payment-details {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php if (isset($data['payment'])): ?>
        <?php
            $payment = $data['payment'];
            $baseRent = $payment->amount;
            $serviceFee = $baseRent * 0.10;
            $totalPaid = $baseRent * 1.10;
        ?>

        <div class="receipt-container">
            <div class="receipt-header">
                <h1><i class="fas fa-receipt"></i> Payment Receipt</h1>
                <p>Thank you for your payment</p>
            </div>

            <div class="receipt-body">
                <div class="transaction-id">
                    Transaction ID: <?php echo htmlspecialchars($payment->transaction_id); ?>
                </div>

                <div class="receipt-info">
                    <div class="info-block">
                        <h3>Payment Information</h3>
                        <div class="info-row">
                            <label>Payment Date:</label>
                            <span><?php echo date('F d, Y', strtotime($payment->payment_date)); ?></span>
                        </div>
                        <div class="info-row">
                            <label>Payment Method:</label>
                            <span><?php echo ucfirst(str_replace('_', ' ', $payment->payment_method)); ?></span>
                        </div>
                        <div class="info-row">
                            <label>Status:</label>
                            <span class="status-badge">Completed</span>
                        </div>
                    </div>

                    <div class="info-block">
                        <h3>Property Details</h3>
                        <div class="info-row">
                            <label>Property:</label>
                            <span><?php echo htmlspecialchars($payment->property_address); ?></span>
                        </div>
                        <div class="info-row">
                            <label>Landlord:</label>
                            <span><?php echo htmlspecialchars($payment->landlord_name); ?></span>
                        </div>
                        <div class="info-row">
                            <label>Tenant:</label>
                            <span><?php echo htmlspecialchars($payment->tenant_name); ?></span>
                        </div>
                    </div>
                </div>

                <div class="payment-details">
                    <h3>Payment Breakdown</h3>

                    <div class="amount-row">
                        <span>Monthly Rent</span>
                        <span>LKR <?php echo number_format($baseRent, 2); ?></span>
                    </div>

                    <div class="amount-row">
                        <span>Platform Service Fee (10%)</span>
                        <span>LKR <?php echo number_format($serviceFee, 2); ?></span>
                    </div>

                    <div class="amount-row total">
                        <span>Total Amount Paid</span>
                        <span>LKR <?php echo number_format($totalPaid, 2); ?></span>
                    </div>
                </div>

                <?php if (!empty($payment->notes)): ?>
                <div style="background: #f9fafb; padding: 20px; border-radius: 6px; border-left: 4px solid #45a9ea;">
                    <h4 style="margin-bottom: 10px; color: #333;">Notes:</h4>
                    <p style="color: #666; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($payment->notes)); ?></p>
                </div>
                <?php endif; ?>

                <div style="text-align: center;">
                    <button class="print-button" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                </div>
            </div>

            <div class="receipt-footer">
                <p><strong>Rentigo</strong> - Property Rental Management System</p>
                <p style="margin-top: 10px; font-size: 14px;">
                    This is a computer-generated receipt and does not require a signature.
                </p>
                <p style="margin-top: 5px; font-size: 12px;">
                    Generated on <?php echo date('F d, Y \a\t h:i A'); ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="receipt-container">
            <div class="receipt-body" style="text-align: center; padding: 60px 40px;">
                <i class="fas fa-exclamation-circle" style="font-size: 60px; color: #e74c3c; margin-bottom: 20px;"></i>
                <h2 style="color: #333; margin-bottom: 10px;">Receipt Not Found</h2>
                <p style="color: #666;">The payment receipt you're looking for could not be found.</p>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
