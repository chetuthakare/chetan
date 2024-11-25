<?php

// Replace these variables with your actual Freshdesk API key and domain
$api_key = 'ugf61E54IyQ2iQBH4q2';
$domain = 'risecommerce';

// Ticket ID for which you want to retrieve communications
$ticket_id = 1236; // Replace with the actual ticket ID

// API endpoint for accessing ticket conversations
$conversations_endpoint = "https://{$domain}.freshdesk.com/api/v2/tickets/{$ticket_id}/conversations";

https://pushplinen.freshdesk.com/api/v2/tickets/1234/conversations";

// Initiate cURL session
$ch = curl_init($conversations_endpoint);

// Set cURL options
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($api_key . ':x')
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL session and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

// Parse the response (if needed)
$conversations_datas = json_decode($response, true);

// Output the ticket conversations
 //echo "<pre>";
 //var_dump($conversations_datas);
  //var_dump(array_key_last($conversations_datas));
?>
<table border="1">
    <tr>
        <th>Communication</th>
    </tr>
    <?php foreach ($conversations_datas as $conversations_data): ?>
    <tr>
        <td>

            <?php 
                echo "<pre>";
             //var_dump($conversations_data);
              // echo $conversations_data[array_key($conversations_data)]['body'] 
              //var_dump(array_keys($conversations_data));
          var_dump($conversations_data);
                //  var_dump(array_key_last($conversations_data)['body']);

               
              
              ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php 
//echo $conversations_data['body_text']  $conversations_data['to_emails'] ;?>