<?php
        $halte =$_GET ['halte'] ?? 'spihw2';
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "http://kv78turbo.ovapi.nl/stopareacode/$halte",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        $json = json_decode($response);

        $code = $halte;
        $bussen = array();
        foreach ($json->$halte as $raw_halte) 
        {
            foreach ($raw_halte->Passes as $Passes) 
            {
                array_push($bussen,$Passes);
            }
        }
        


        function dateCompare($date_1,$date_2)
        {
            $date_1 = strtotime($date_1->ExpectedArrivalTime);
            $date_2 = strtotime($date_2->ExpectedArrivalTime);
            return $date_1 - $date_2;
        }

        usort($bussen, 'dateCompare');

        foreach ($bussen as $key => $bus) {
            foreach ($bus as $key => $rawbus) 
            {
                echo "$key : $rawbus";
                echo "<br>";
            }
            echo "<br><br>";
        }
