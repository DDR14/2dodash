<?php
session_start();
$servername = "localhost"; //live
 $username = "boostpr1_boostpr";
 $password = "Draper24@";
 $dbname = "boostpr1_boostpromotions";

 $link = mysql_connect($servername, $username, $password) or die ('oops something went wrong');
 mysql_select_db($dbname, $link) or die ('oops something went weong');
if (isset($_SESSION['ses_key'])=='' || isset($_SESSION['email'])=='') {
    $customers_id = '';
    $email = '';
    $quantity='0';
}else{ 
    $email= $_SESSION['email'];
    $customers_id = $_SESSION['customers_id'];
    $sql_basket = "SELECT SUM(a.customers_basket_quantity) AS quantity, 
                    SUM(IF(ISNULL(c.discount_price),b.products_price,c.discount_price) * a.customers_basket_quantity) AS ptotal
                    FROM zen_customers_basket a 
                    INNER JOIN zen_products b 
                    ON a.products_id = b.products_id 
                    LEFT JOIN (SELECT MIN(x.discount_price) AS discount_price, 
                    x.products_id FROM zen_products_discount_quantity x
                    INNER JOIN zen_customers_basket y 
                    ON x.products_id = y.products_id
                    WHERE y.customers_id =  '$customers_id'
                    AND y.customers_basket_quantity >= discount_qty 
                    GROUP BY x.products_id) c
                    ON c.products_id = a.products_id
                    WHERE a.customers_id =   '$customers_id'";
    $sql_basket=mysql_query($sql_basket);
    $row_basket = mysql_fetch_array($sql_basket);
    $quantity = $row_basket['quantity'];
    if($quantity==''){
      $quantity='0';
    }
}
$year=date('Y');
$date=date('Y-m-d');
function keepHistory()
{
    $history=$_SESSION["history"];
    $history[]=$_SERVER["PHP_SELF"];
    $_SESSION["history"]=$history;
}
//get customers detail
$query1=mysql_query("SELECT * FROM zen_customers WHERE customers_id='$customers_id'");
$row1=mysql_fetch_array($query1);
$customers_default_address_id=$row1['customers_default_address_id'];

//get customers default address
$query2=mysql_query("SELECT * FROM zen_address_book WHERE address_book_id='$customers_default_address_id'");
$row2=mysql_fetch_array($query2);
$customers_firstname=$row2['entry_firstname'];
$customers_lastname=$row2['entry_lastname'];
$customers_name=$customers_firstname.' '.$customers_lastname; 
$customers_company=$row2['entry_company'];
$customers_street_address=$row2['entry_street_address'];
$customers_suburb=$row2['entry_suburb'];
$customers_city=$row2['entry_city'];
$customers_postcode=$row2['entry_postcode'];
$entry_state=$row2['entry_state'];
$entry_zone_id=$row2['entry_zone_id'];

//get customers state
if($entry_state!=''){
    $customers_state=$entry_state;
}
else{
    $query3=mysql_query("SELECT * FROM zen_zones WHERE zone_id='$entry_zone_id'");
    $row3=mysql_fetch_array($query3);
    $customers_state=$row3['zone_name'];
}

//get customers country
$entry_country_id=$row2['entry_country_id'];
$query4=mysql_query("SELECT * FROM zen_countries WHERE countries_id='$entry_country_id'");
$row4=mysql_fetch_array($query4);
$customers_country=$row4['countries_name'];

$customers_telephone=$row1['customers_telephone'];
$customers_mobile=$row1['customers_mobile'];
$customers_email_address=$row1['customers_email_address'];
$customers_address_format_id='2';

//get delivery address
$shipping=$_SESSION['shipping'];
$query5=mysql_query("SELECT * FROM zen_address_book WHERE address_book_id='$shipping'");
$row5=mysql_fetch_array($query5);
$delivery_firstname=$row5['entry_firstname'];
$delivery_lastname=$row5['entry_lastname'];
$delivery_name=$delivery_firstname.' '.$delivery_lastname; 
$delivery_company=$row5['entry_company'];
$delivery_street_address=$row5['entry_street_address'];
$delivery_suburb=$row5['entry_suburb'];
$delivery_city=$row5['entry_city'];
$delivery_postcode=$row5['entry_postcode'];
$delivery_state=$row5['entry_state'];
$delivery_zone_id=$row5['entry_zone_id'];
//get delivery state
if($delivery_state!=''){
    $delivery_state=$delivery_state;
}
else{
    $query6=mysql_query("SELECT * FROM zen_zones WHERE zone_id='$delivery_zone_id'");
    $row6=mysql_fetch_array($query6);
    $delivery_state=$row6['zone_name'];
}
//get delivery country
$delivery_country_id=$row5['entry_country_id'];
$query7=mysql_query("SELECT * FROM zen_countries WHERE countries_id='$delivery_country_id'");
$row7=mysql_fetch_array($query7);
$delivery_country=$row7['countries_name'];
$delivery_address_format_id='2';
$delivery_details=$shipping_company.' '.$delivery_street_address.'<BR>'.$delivery_city.' '.$delivery_state.' '.$delivery_postcode;

//get billing Address
$billing=$_SESSION['billing'];
$query8=mysql_query("SELECT * FROM zen_address_book WHERE address_book_id='$billing'");
$row8=mysql_fetch_array($query8);
$billing_firstname=$row8['entry_firstname'];
$billing_lastname=$row8['entry_lastname'];
$billing_name=$billing_firstname.' '.$billing_lastname; 
$billing_company=$row8['entry_company'];
$billing_street_address=$row8['entry_street_address'];
$billing_suburb=$row8['entry_suburb'];
$billing_city=$row8['entry_city'];
$billing_postcode=$row8['entry_postcode'];
$billing_state=$row8['entry_state'];
$billing_zone_id=$row8['entry_zone_id'];

//get delivery state
if($billing_state!=''){
    $billing_state=$billing_state;
}
else{
    $query9=mysql_query("SELECT * FROM zen_zones WHERE zone_id='$billing_zone_id'");
    $row9=mysql_fetch_array($query9);
    $billing_state=$row9['zone_name'];
}
//get delivery country
$billing_country_id=$row8['entry_country_id'];
$query10=mysql_query("SELECT * FROM zen_countries WHERE countries_id='$billing_country_id'");
$row10=mysql_fetch_array($query10);
$billing_country=$row10['countries_name'];
$billing_address_format_id='2';
$billing_details=$billing_company.' '.$billing_street_address.'<BR>'.$billing_city.' '.$billing_state.' '.$billing_postcode;
//now get the checkout sessions
$payment_method=$_SESSION['payment_method'];
$payment_module_code=$_SESSION['payment_module_code'];
$shipping_method=$_SESSION['shipping_method'];
$shipping_module_code='table';
 if (isset($_SESSION["coupon"]) && !empty($_SESSION["coupon"])) {
    $coupon_code=$_SESSION['coupon'];
}else{
$coupon_code='';
}
$cc_type='';
$cc_owner='';
$cc_number='';
$cc_expires='';
$cc_cvv='';
$last_modified='';
$date_purchased=$date;
$orders_status=2;
$currency='USD';
$currency_value='1.000000';
$order_total=$_SESSION['order_total'];
$order_tax=$_SESSION['tax'];
$ip_address=$_SERVER['REMOTE_ADDR']; 
$ship_notes=$_SESSION['message'];


mysql_query("INSERT INTO `zen_orders`(`customers_id`, `customers_name`, `customers_company`, `customers_street_address`, `customers_suburb`, `customers_city`, `customers_postcode`, `customers_state`, `customers_country`, `customers_telephone`, `customers_mobile`, `customers_email_address`, `customers_address_format_id`, `delivery_name`, `delivery_company`, `delivery_street_address`, `delivery_suburb`, `delivery_city`, `delivery_postcode`, `delivery_state`, `delivery_country`, `delivery_address_format_id`, `billing_name`, `billing_company`, `billing_street_address`, `billing_suburb`, `billing_city`, `billing_postcode`, `billing_state`, `billing_country`, `billing_address_format_id`, `payment_method`, `payment_module_code`, `shipping_method`, `shipping_module_code`, `coupon_code`, `cc_type`, `cc_owner`, `cc_number`, `cc_expires`, `cc_cvv`, `last_modified`, `date_purchased`, `orders_status`, `follow_up`, `currency`, `currency_value`, `order_total`, `order_tax`, `paypal_ipn_id`, `ip_address`, `ship_notes`) VALUES ('$customers_id', '$customers_name', '$customers_company', '$customers_street_address', '$customers_suburb', '$customers_city', '$customers_postcode', '$customers_state', '$customers_country', '$customers_telephone', '$customers_mobile', '$customers_email_address', '$customers_address_format_id', '$delivery_name', '$delivery_company', '$delivery_street_address', '$delivery_suburb', '$delivery_city', '$delivery_postcode', '$delivery_state', '$delivery_country', '$delivery_address_format_id', '$billing_name', '$billing_company', '$billing_street_address', '$billing_suburb', '$billing_city', '$billing_postcode', '$billing_state', '$billing_country', '$billing_address_format_id', '$payment_method', '$payment_module_code', '$shipping_method', '$shipping_module_code', '$coupon_code', '$cc_type', '$cc_owner', '$cc_number', '$cc_expires', '$cc_cvv', '$last_modified', '$date_purchased', '$orders_status', '', '$currency', '$currency_value', '$order_total', '$order_tax', '', '$ip_address', '$ship_notes')");
$last_inserted_id=mysql_insert_id();
$products_details="";
$query11=mysql_query("SELECT * FROM zen_customers_basket WHERE customers_id='$customers_id'");
while($row11=mysql_fetch_array($query11)){
    $orders_id=$last_inserted_id;
    $products_id=$row11['products_id'];
    $query12=mysql_query("SELECT * FROM zen_products WHERE products_id='$products_id'");
    $row12=mysql_fetch_array($query12);
    $products_model=$row12['products_model'];
    $query13=mysql_query("SELECT * FROM zen_products_description WHERE products_id='$products_id'");
    $row13=mysql_fetch_array($query13);
    $products_name=$row13['products_name'];
    $products_tax=$order_tax;
    $products_quantity=$row11['customers_basket_quantity'];
    $products_price=$row11['final_price']/$products_quantity;
    $final_price=$row11['final_price']/$products_quantity;
    $products_details=$products_details.'</br>'.$products_quantity.' x '.$products_name.' ('.$products_model.') = '.$final_price;
        mysql_query("INSERT INTO `zen_orders_products`(`orders_id`, `products_id`, `products_model`, `products_name`, `products_price`, `final_price`, `products_tax`, `products_quantity`) VALUES ('$orders_id', '$products_id', '$products_model', '$products_name', '$products_price', '$final_price', '$products_tax', '$products_quantity')");
    $orders_products_id=mysql_insert_id();
    $date = str_replace( '-', '', $date );
    $model=$products_model;
    $title=$row11['title'];
    $footer=$row11['footer'];
    $background=$row11['background'];
    $customs=$row11['customs'];
    $website=$row11['website'];
    $upload=$row11['upload'];
        mysql_query("INSERT INTO `naz_custom_co`(`order_id`, `date`, `model`, `title`, `footer`, `background`, `upload`, `customs`, `website`, `orders_products_id`) VALUES ('$last_inserted_id', '$date', '$model', '$title', '$footer', '$background', '$upload', '$customs', '$website', '$orders_products_id')");
   
}
    $subtotal=$_SESSION['subtotal'];
    
    $tax=$_SESSION['tax'];
    $shippingprice=$_SESSION['shippingprice'];
    $taxtext='$'.number_format($tax, 2);
    $subtotaltext='$'.number_format($subtotal, 2);
    $order_totaltext='$'.number_format($order_total, 2);
    $shippingpricetext='$'.number_format($shippingprice, 2);
    $datetoday = date('Y-m-d H:i:s');
    $redeem_ip = $_SERVER['REMOTE_ADDR'];

    mysql_query("INSERT INTO `zen_orders_total`(`orders_id`, `title`, `text`, `value`, `class`, `sort_order`) VALUES ('$last_inserted_id', 'Sub-Total', '$subtotaltext', '$subtotal', 'ot_subtotal', '100')");


    if (isset($_SESSION["coupon"]) && !empty($_SESSION["coupon"])) {
    $_SESSION['coupon_amount'];
    $coupon_amount=$_SESSION['coupon_amount'];
    echo $coupon_amounttext='-$'.number_format($coupon_amount, 2);
    $coupon=$_SESSION['coupon'];
    $coupon_title='Discount Coupon: '.$coupon.':';
    $ref_no=$_SESSION['coupon'].'#'.$last_inserted_id;
     mysql_query("INSERT INTO `zen_orders_total`(`orders_id`, `title`, `text`, `value`, `class`, `sort_order`) VALUES ('$last_inserted_id', '$coupon_title', '$coupon_amounttext', '$coupon_amount', 'ot_coupon', '280')");
     //redeem track insert 
      mysql_query("INSERT INTO `zen_coupon_redeem_track`(`coupon_id`, `customer_id`, `redeem_date`, `redeem_ip`, `order_id`) VALUES ('$coupon',
         '$customers_id','$datetoday','$redeem_ip','$last_inserted_id' )");

      //give credits to referrer
      
      $new_code=$_SESSION['customers_id'].'REF';

      $query1=mysql_query("SELECT * FROM zen_coupon WHERE coupod_code='$new_code'");
      $row1=mysql_fetch_array($query1);
      if(mysql_num_rows($query1) == 0){
        //do nothing
      }else{
        // create duplicate coupon
        mysql_query("INSERT INTO `zen_coupons`(`coupon_type`, `coupon_code`, `coupon_amount`, `coupon_minimum_order`, `coupon_start_date`, `coupon_expire_date`, `uses_per_coupon`, `uses_per_user`, `restrict_to_products`, `restrict_to_categories`, `restrict_to_customers`, `coupon_active`, `date_created`, `date_modified`, `coupon_zone_restriction`) VALUES ('P', '$new_code', '5.0000', '0', '$datetoday', '2030-07-27 00:00:00', '1', '0', '', '', '','Y', '$datetoday', '$datetoday', '0' )");
        $coupon_id=mysql_insert_id();
        $referral_name=$customers_firstname. 'Referral Code';

        $referral_description='Refer friends using referral code and earn 5% total of referees order as credit for every order the customer you refer';
        mysql_query("INSERT INTO `zen_coupons_description`(`coupon_id`, `language_id`, `coupon_name`, `coupon_description`) VALUES ('$coupon_id', '1', '$referral_name', '$referral_description')");
      }
    
      // mysql_query("INSERT INTO zen_transactions (txn_type, payment_method, ref_no, amount, memo, txn_date, customers_id) "
      //                   . "SELECT 'Credit', 'Credit Memo', '$ref_no', '$new_credit','Referral Credit from Order $order_id', NOW(), '$referer' FROM zen_customers where customers_id ='" . $referer . "'");


    }
    mysql_query("INSERT INTO `zen_orders_total`(`orders_id`, `title`, `text`, `value`, `class`, `sort_order`) VALUES ('$last_inserted_id', '$shipping_method', '$shippingpricetext', '$shippingprice', 'ot_shipping', '200')");

    mysql_query("INSERT INTO `zen_orders_total`(`orders_id`, `title`, `text`, `value`, `class`, `sort_order`) VALUES ('$last_inserted_id', 'Total', '$order_totaltext', '$order_total', 'ot_total', '999')");

    if($subtotal<25){
        $loworder=10.00;
        mysql_query("INSERT INTO `zen_orders_total`(`orders_id`, `title`, `text`, `value`, `class`, `sort_order`) VALUES ('$last_inserted_id', 'Total', '$10.00', '$loworder', 'ot_loworderfee', '400')");
    }
    if($tax>0){
        mysql_query("INSERT INTO `zen_orders_total`(`orders_id`, `title`, `text`, `value`, `class`, `sort_order`) VALUES ('$last_inserted_id', 'Tax', '$taxtext', '$tax', 'ot_tax', '300')");
    }
    mysql_query("DELETE FROM zen_customers_basket WHERE customers_id='$customers_id'");

            $_SESSION['customers_id'] = $last_id;
            $_SESSION['customers_name'] = $f_name;
            $_SESSION['customers_lastname'] = $l_name;
            $_SESSION['email'] = $email;
            $message = file_get_contents('http://ctrtags.com/email/email_template_checkout.html');
            $str = str_replace(
    array(
      "INTRO_ORDER_NUMBER",
      "INTRO_DATE_ORDERED",
      "PRODUCT_DETAILS",
      "ORDER_SUB_TOTAL",
      "COUPON_DISCOUNT",
      "ORDER_TAX",
      "LOW_ORDER_CHARGE",
      "ORDER_TOTALS",
      "ORDER_COMMENTS",
      "ADDRESS_DELIVERY_DETAIL",
      "SHIPPING_METHOD_DETAIL",
      "ADDRESS_BILLING_DETAIL",
      "PAYMENT_METHOD_FOOTER"
    ),
    array(
       $last_inserted_id,
       date('Y-m-d'),
       $products_details,
       $subtotal,
       $coupon. ': -'.$coupon_amount,
       $tax,
       $loworder,
       $order_total,
       $ship_notes,
       $billing_details,
       $shipping_method,
       $billing_details,
       $payment_method
    ),
    file_get_contents('http://ctrtags.com/email/email_template_checkout.html')
);          
            $support = 'michael.r@meristone.com';
            $support2 = 'support@ctrtags.com';
            $subject = "Order Confirmation No. : ".$last_inserted_id;
            $message2 = $_SESSION['email'] . $_SESSION['customers_lastname'] . $_SESSION['customers_id'];
            $to = $email;
            $headers = "From: support@boostpromotions.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            mail($to, $subject, $str, $headers);
            mail($support, $subject, $str, $headers);
            mail($support2, $subject, $str, $headers);
// Destroy the session.
session_destroy();
header('Location:../checkout-success.php'."?ordernumber=".$last_inserted_id);
?>