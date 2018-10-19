<?php 
//************************************************************************************************* 
// Created by Philip DeGraaf - Last updated 04/14/2014 
//************************************************************************************************* 
// *** Define Field variables before running your first transaction *** 
//************************************************************************************************* 
$ssl_merchant_id = '8028811886';  // Change the '000666' to your Virtual Merchant ID(VID) 
$ssl_user_id = 'boostweb';  // Change the 'sandbox' to your User ID(UID) 
$ssl_pin = '384786';  // Change the '000666' to your Pin 
// Test mode is only to be used with Production 
$ssl_test_mode = 'true'; // when using a Sandbox Account make sure this is set to false 
// Product info 
$ssl_amount = '61.16'; // refer to the ELAVON Test Host Pre-programmed Responses.pdf 
$ssl_description = 'Test Transaction'; 
//  *** Credit Card Billing information *** 
$ssl_first_name ='test'; 
$ssl_last_name ='tester'; 
$ssl_company = 'Elavon';
$ssl_avs_address = '666 Chapman HWY'; 
$ssl_city = 'Knoxville'; 
$ssl_state = 'TN'; 
$ssl_avs_zip = '37920'; 
$ssl_country = 'USA';  // 3 digit Country Codes example: Canada = CAN 
$ssl_phone = '905-555-1234'; 
$ssl_email = 'elvis@meristone.com';  //  transaction receipt emails will be sent to this address 
// *** Credit Card information *** 
$ssl_card_number = '4111111111111111';  //  use only test card numbers with the sandbox environment 
$ssl_exp_date = '0118';  //  put in valid Expiry Month and Year must be 4 digits 
$ssl_cvv2cvc2 = '354';  //  3 or 4 digit number from the back of the Credit Card  
$ssl_cvv2cvc2_indicator = '1'; 
$ssl_transaction_type = 'ccsale';  // Transaction Types: ccsale, ccauthonly, ccforce, ccaddrecurring, ccassindtall, ccforce, ecspurchase, ccimport, ccrecimport, cccredit, ccavsonly, ccbalinquiry 
  
// Don't delete this information 
if ($ssl_merchant_id==="000666") { 
                echo '<html><head><title>Virtual Merchant Data Setup Error</title></head><body><p><b>...:: <em>Setup Script Error </em></b> : You need to customize this script before trying to run it. <b> ::...</p><p>...:: </b><em>Read the commented parts in the PHP file for more information.</em><b> ::...</b></p><p>You need change $ssl_merchant_id = "<b>000666</b>" to your Virtual Merchant ID to start using this script.</p><p>Make sure to refer to the Virtual Merchant Developers Guide when adding additional data fields.</p></body></html>'; 
} 
if ($ssl_user_id==="sandbox") { 
                echo '<html><head><title>Virtual Merchant Data Setup Error</title></head><body><p><b>...:: <em>Setup Script Error </em></b> : You need to customize this script before trying to run it. <b> ::...</p><p>...:: </b><em>Read the commented parts in the PHP file for more information.</em><b> ::...</b></p><p>You need change $ssl_user_id = "<b>sandbox</b>" to your Virtual Merchant ID to start using this script.</p><p>Make sure to refer to the Virtual Merchant Developers Guide when adding additional data fields.</p></body></html>'; 
} 
if ($ssl_pin==="000666") { 
                echo '<html><head><title>Virtual Merchant Data Setup Error</title></head><body><p><b>...:: <em>Setup Script Error </em></b> : You need to customize this script before trying to run it. <b> ::...</p><p>...:: </b><em>Read the commented parts in the PHP file for more information.</em><b> ::...</b></p><p>You need change $ssl_pin = "<b>000666</b>" to your Virtual Merchant ID to start using this script.</p><p>Make sure to refer to the Virtual Merchant Developers Guide when adding additional data fields.</p></body></html>'; 
} 
if ($ssl_card_number==="4715********0040") { 
                echo '<html><head><title>Virtual Merchant Data Setup Error</title></head><body><p><b>...:: <em>Setup Script Error </em></b> : You need to customize this script before trying to run it. <b> ::...</p><p>...:: </b><em>Read the commented parts in the PHP file for more information.</em><b> ::...</b></p><p>You need change $ssl_card_number = "<b>4715********0040</b>" needs to be changed to one of the test card numbers.</p><p>Make sure to refer to the Virtual Merchant Developers Guide when adding additional data fields.</p></body></html>'; 
  exit(); 
} 
   else { 
                
// The XML Request 
$xml = "<txn><ssl_merchant_id>" .$ssl_merchant_id. "</ssl_merchant_id> 
<ssl_pin>" .$ssl_pin. "</ssl_pin> 
<ssl_user_id>" .$ssl_user_id. "</ssl_user_id> 
<ssl_test_mode>" .$ssl_test_mode. "</ssl_test_mode> 
<ssl_transaction_type>" .$ssl_transaction_type. "</ssl_transaction_type> 
<ssl_card_number>" .$ssl_card_number. "</ssl_card_number> 
<ssl_exp_date>" .$ssl_exp_date. "</ssl_exp_date> 
<ssl_amount>" .$ssl_amount. "</ssl_amount> 
<ssl_description>" .$ssl_description. "</ssl_description> 
<products>" .$ssl_amount. "::1::001::" .$ssl_description. "::</products> 
<ssl_cvv2cvc2_indicator>" .$ssl_cvv2cvc2_indicator. "</ssl_cvv2cvc2_indicator> 
<ssl_cvv2cvc2>" .$ssl_cvv2cvc2. "</ssl_cvv2cvc2> 
<ssl_first_name>" .$ssl_first_name. "</ssl_first_name> 
<ssl_last_name>" .$ssl_last_name. "</ssl_last_name> 
<ssl_company>" .$ssl_company. "</ssl_company> 
<ssl_avs_address>" .$ssl_avs_address. "</ssl_avs_address> 
<ssl_city>" .$ssl_city. "</ssl_city> 
<ssl_state>" .$ssl_state. "</ssl_state> 
<ssl_country>" .$ssl_country. "</ssl_country> 
<ssl_avs_zip>" .$ssl_avs_zip. "</ssl_avs_zip> 
<ssl_phone>" .$ssl_phone. "</ssl_phone> 
<ssl_email>" .$ssl_email. "</ssl_email></txn>"; 
} 
  
//************************************************************************************************* 
//  POST URLs for Virtual Merchant 
//************************************************************************************************* 
//            $postURL = "https://www.myvirtualmerchant.com/VirtualMerchant/processxml.do";                   // Production URL 
//                $postURL = "https://demo.myvirtualmerchant.com/VirtualMerchantDemo/processxml.do";     // Sandbox URL 
            $postURL = "https://demo.myvirtualmerchant.com/VirtualMerchantDemo/test_tran.do";         // Info URL 
//************************************************************************************************* 
  
$postData = "xmldata=".URLEncode($xml);  //  must pass xmldata = URL encoding before the XML, very similar to InternetSecure's RequestMode=X&RequestData=URLEncode(XML) 
  
// Get the curl session object 
$session = curl_init(); 
  
// Pass the Content length and Type with the POSTed Data 
$header[] = "Content-Length: ".strlen($postData); 
$header[] = "Content-Type: application/x-www-form-urlencoded"; 
  
// Set the POST options. 
curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($session, CURLOPT_URL,$postURL); 
curl_setopt($session, CURLOPT_POST, true); 
curl_setopt($session, CURLOPT_POSTFIELDS, $postData); 
curl_setopt($session, CURLOPT_HEADER, true); 
curl_setopt($session, CURLOPT_HTTPHEADER, $header); 
curl_setopt($session, CURLOPT_RETURNTRANSFER, true); 
  
// Create the Session $response 
$response = curl_exec($session); 
// Close the session 
curl_close($session); 
  
// Print to screen transaction Data 
      echo "<pre><em><strong>Transaction Request XML:</strong></em><br \>"; 
      echo str_replace('>','&gt;',str_replace('<','&lt;',str_replace('><',"&gt;\n&lt;",$xml))).'</pre><br \>';  // use this line to display the XML 
  
      echo "<pre><em><strong>Transaction Request URL encoded:</strong></em><br \>"; 
      echo str_replace('>','&gt;',str_replace('<','&lt;',str_replace('><',"&gt;\n&lt;",$postData))).'</pre><br \>'; // URL Encoded XML 
  
      echo "<pre><em><strong>Transaction Response:</strong></em><br \>"; 
      echo str_replace('>','&gt;',str_replace('<','&lt;',str_replace('><',"&gt;\n&lt;",$response))).'</pre><br \>'; // XML response from Virtual Merchant 
      
