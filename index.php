<?php

    /*

    UK Geocoding with PHP
    Simple demo which passes postcode to the API and returns latitude and longitude.

    Full geocoding API documentation:-
    https://developers.alliescomputing.com/postcoder-web-api/geocoding/position
    
    */

    if (array_key_exists("postcode", $_GET)) {

        var_dump(geocode_postcode($_GET['postcode']));
        
    } else {
        
        echo "<p>Pass a postcode using <code>?postcode=NR147PZ</code></p>";
        
    }

    function geocode_postcode($postcode = "") {
        
        // Replace with your API key, test key will always return latitude and longitude for "NR14 7PZ"
        $api_key = "PCW45-12345-12345-1234X";
        
        // Grab the input text and trim any whitespace
        $postcode = trim($postcode);
        
        // Create an empty output object
        $output = new StdClass();
        
        if ($postcode == "") {
            
            // Respond without calling API if no postcode supplied
            $output->message = "No postcode supplied";
            
        } else {
            
            // Create the URL to API including API key and encoded postcode
            $postcode_url = "https://ws.postcoder.com/pcw/" . $api_key . "/position/UK/" . urlencode($postcode); 
            
            // Use cURL to send the request and get the output
            $session = curl_init($postcode_url); 
            // Tell cURL to return the request data
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true); 
            // use application/json to specify json return values, the default is XML.
            $headers = array('Content-Type: application/json');
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            // Execute cURL on the session handle
            $response = curl_exec($session);
            
            // Capture any cURL errors
            if ($response === false) {
              $curl_error = curl_error($session);
            }
            
            // Capture the HTTP status code from the API
            $http_status_code = curl_getinfo($session, CURLINFO_HTTP_CODE);

            // Close the cURL session
            curl_close($session);
            
            if ($http_status_code != 200) {
                
                if ($curl_error) {
                    // Triggered if cURL failed for some reason
                    // Output the error captured by curl_error()
                    $output->error_message = "cURL error occurred - " . $curl_error;
                } else {
                    // Triggered if API does not return 200 HTTP code
                    // More info - https://developers.alliescomputing.com/postcoder-web-api/error-handling
                    // Here we will output a basic message with HTTP code
                    $output->error_message = "HTTP error occurred - " . $http_status_code;
                }
                
            } else {
                
                // Convert JSON into an object
                $result = json_decode($response);
                
                if(count($result) > 0) {
                    
                    $output->latitude = $result[0]->latitude;
                    $output->longitude = $result[0]->longitude;
                    
                } else {
                    
                    $output->message = "Postcode not found";
                    
                }
                
            }
            
        }
            
        return $output;
        
    }

?>
