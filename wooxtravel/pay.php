<?php
require 'includes/header.php';
require 'config/config.php';

if(!isset($_SERVER['HTTP_REFERER'])) {
    header('location: http://localhost:8080');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Payment</title>
    <style>
        :root {
            --primary-color: #003087;
            --secondary-color: #009cde;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .payment-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .payment-header h2 {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .payment-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .payment-amount {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        #paypal-button-container {
            margin: 40px auto;
            max-width: 500px;
        }
        
        .payment-footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        
        .secure-payment {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            color: var(--success-color);
            font-weight: 500;
        }
        
        .secure-payment i {
            margin-right: 8px;
        }
        
        @media (max-width: 768px) {
            .payment-container {
                margin: 50px 20px;
                padding: 20px;
            }
            
            .payment-details {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h2>Complete Your Payment</h2>
            <p>You're almost done! Please review your order details below.</p>
        </div>
        
        <div class="payment-details">
            <div>
                <h4>Booking Summary</h4>
                <p>Booking for <?php echo isset($_SESSION['booking_details']['num_of_guests']) ? htmlspecialchars($_SESSION['booking_details']['num_of_guests']) : '1'; ?> guest(s)</p>
            </div>
            <div class="payment-amount">
                $<?php echo isset($_SESSION['payment']) ? htmlspecialchars($_SESSION['payment']) : '0.00'; ?>
            </div>
        </div>
        
        <div id="paypal-button-container"></div>
        
        <div class="secure-payment">
            <i class="fas fa-lock"></i>
            <span>Secure Payment Processed by PayPal</span>
        </div>
        
        <div class="payment-footer">
            <p>You'll be redirected to your account page after successful payment</p>
            <p>Need help? <a href="contact.php">Contact support</a></p>
        </div>
    </div>

    <!-- PayPal SDK -->
    <script src="https://www.paypal.com/sdk/js?client-id=AWjirPV1T_q8ei_-y9rPJ4QmKPna2EKFFulCJETTGddPVi6XLFl1xdBv81pzHPCHZZKZD0onPJegvTby&currency=USD"></script>
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <script>
        const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
        
        paypal.Buttons({
            style: {
                layout: 'vertical',
                color: 'blue',
                shape: 'rect',
                label: 'paypal',
                height: 50
            },
            createOrder: (data, actions) => {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: "<?php echo $_SESSION['payment']; ?>",
                            breakdown: {
                                item_total: {
                                    value: "<?php echo $_SESSION['payment']; ?>",
                                    currency_code: "USD"
                                }
                            }
                        },
                        items: [{
                            name: "Booking Payment",
                            description: "Payment for your booking",
                            quantity: "1",
                            unit_amount: {
                                value: "<?php echo $_SESSION['payment']; ?>",
                                currency_code: "USD"
                            }
                        }]
                    }]
                });
            },
            onApprove: (data, actions) => {
                return actions.order.capture().then(function(orderData) {
                    // Show loading state
                    document.querySelector('#paypal-button-container').innerHTML = '<div style="text-align:center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Processing your payment...</p></div>';
                    
                    // Redirect after 2 seconds to allow user to see the feedback
                    setTimeout(function() {
                        window.location.href = '/users/user.php?id=' + userId;
                    }, 2000);
                });
            },
            onError: (err) => {
                console.error('PayPal error:', err);
                alert('An error occurred with PayPal. Please try again or contact support.');
            }
        }).render('#paypal-button-container');
    </script>

<?php require 'includes/footer.php'; ?>