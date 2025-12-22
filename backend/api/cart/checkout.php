<?php
session_start();
require_once '../../lib/Database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../../vendor/autoload.php';


if (!isset($_SESSION['user']['UserID'], $_SESSION['user']['CartID'], $_SESSION['user']['Email'])) {
    die("Unauthorized access.");
}

$userID = $_SESSION['user']['UserID'];
$cartID = $_SESSION['user']['CartID'];
$userEmail = $_SESSION['user']['Email'];
$userName  = $_SESSION['user']['Username'];

$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("
    SELECT 
        b.Title,
        b.Price,
        c.Quantity
    FROM Cart_Items c
    JOIN Books b ON c.BookID = b.BookID
    WHERE c.CartID = ?
");
$stmt->bind_param("i", $cartID);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $rowTotal = $row['Price'] * $row['Quantity'];
    $total += $rowTotal;
    $items[] = $row;
}
$stmt->close();

$stmt = $db->prepare("CALL sp_PlaceOrder(?, ?)");
$stmt->bind_param("ii", $userID, $cartID);

if (!$stmt->execute()) {
    die("Order failed: " . $stmt->error);
}
$stmt->close();

$emailBody = "
<h2>Thank you for your purchase, {$userName}! 🎉</h2>
<p>Here is a summary of your order:</p>

<table border='1' cellpadding='8' cellspacing='0' width='100%'>
<tr>
    <th align='left'>Book</th>
    <th>Price</th>
    <th>Qty</th>
    <th>Total</th>
</tr>
";

foreach ($items as $item) {
    $lineTotal = $item['Price'] * $item['Quantity'];
    $emailBody .= "
    <tr>
        <td>{$item['Title']}</td>
        <td>$" . number_format($item['Price'], 2) . "</td>
        <td>{$item['Quantity']}</td>
        <td>$" . number_format($lineTotal, 2) . "</td>
    </tr>";
}

$emailBody .= "
<tr>
    <td colspan='3' align='right'><strong>Grand Total</strong></td>
    <td><strong>$" . number_format($total, 2) . "</strong></td>
</tr>
</table>

<p>📚 Enjoy your books!</p>
<p>— Yolo-Bard Team</p>
";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'hidebaba12@gmail.com';       // 🔴 CHANGE
    $mail->Password   = 'eauz ghgl phym ctqj';          // 🔴 CHANGE
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('hidebaba12@gmail.com', 'Yolo-Bard');
    $mail->addAddress($userEmail, $userName);

    $mail->isHTML(true);
    $mail->Subject = 'Your Yolo-Bard Order Confirmation';
    $mail->Body    = $emailBody;

    $mail->send();
} catch (Exception $e) {
    error_log("Email failed: {$mail->ErrorInfo}");
}


echo "<script>
    alert('Order placed successfully! Check your email 📧');
    window.location.href = '/yolobard/frontend/shopping-cart.php';
</script>";
exit;
