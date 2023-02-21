<?php
$halte = $_GET['halte'] ?? 'spihw2';
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

$bussen = array();
$TimingPointCode = null;
foreach ($json->$halte as $raw_halte) {
    $TimingPointCode = $raw_halte->Stop->TimingPointCode;
    foreach ($raw_halte->Passes as $Passes) {
        array_push($bussen, $Passes);
    }
}

$haltenaam = explode(", ", $json->$halte->$TimingPointCode->Stop->TimingPointName);

function dateCompare($date_1, $date_2)
{
    $date_1 = strtotime($date_1->ExpectedArrivalTime);
    $date_2 = strtotime($date_2->ExpectedArrivalTime);
    return $date_1 - $date_2;
}

function DepartureTime($bus)
{
    $Nowtime = strtotime(date('H:i:s'));
    $DepartureTime = strtotime($bus->TargetArrivalTime);
    $elapsed = $DepartureTime - $Nowtime;

    if($elapsed < 0) $elapsed = 0;

    $min = date("i", $elapsed);
    $how = date("H", $elapsed) -1;
    if($how > 0) $min = ($how * 60) + $min;
    if ($min == "00") {
        return "nu";
    }
    return $min . " min";
}

function minusTime($bus)
{
    $Target = strtotime($bus->TargetArrivalTime);
    $Expected = strtotime($bus->ExpectedArrivalTime);
    return $Expected - $Target;
}

usort($bussen, 'dateCompare');
?>

<div>
    <nav>
        <a href="/rotate.php" class="link">rotate</a>
        <p class="top">Info van <?= $haltenaam[1] ?? $haltenaam[0]?></p>
    </nav>
    <?php
    foreach ($bussen as $key => $bus) {
        $time = $bus->TargetArrivalTime;
        $delaytime = $bus->ExpectedArrivalTime;
        $start_datetime = new DateTime($time);
        $delay = $start_datetime->diff(new DateTime($delaytime));
    ?>
        <div class="block">
            <?php
            $time2 = date('H:i', strtotime($bus->TargetArrivalTime));
            $date = $bus->TargetArrivalTime;
            $newDate = date('H:i', strtotime($date . '-$time2'));
            if ($bus->TripStopStatus == "CANCEL") {
            ?>
                <div class="red">
                    <p class="arrival"><?= date('H:i', strtotime($bus->TargetArrivalTime)) ?></p>
                    <p class=min><b><?= DepartureTime($bus) ?></b></p>
                    <p class="name"><?= $bus->DestinationName50 ?? null ?></p>
                    <p class="canceled">Vervalen</p>
                    <p class="number"><?= $bus->LinePublicNumber ?? null ?></p>
                    <p class="company">
                        <?php
                        switch ($bus->OperatorCode) {
                            case 'EBS':
                                echo "EBS";
                                break;
                            case 'CXX':
                                echo "Connexxion";
                                break;

                            default:
                                echo $bus->OperatorCode;
                                break;
                        }
                        ?></p>
                </div>
            <?php
            } else {
            ?>
                <p class="arrival"><?= date('H:i', strtotime($bus->TargetArrivalTime)) ?></p>
                <?php
                if ($delay->i > 0) {
                ?>
                    <p class="delay">+<?= $delay->i ?></p>
                <?php
                }
                ?>
                <?php
                if (minusTime($bus) < 0) {
                ?>
                    <p class="delay"><?= minusTime($bus) ?></p>
                <?php
                }
                ?>
                <p class=min><b><?= DepartureTime($bus) ?></b></p>
                <p class="name"><?= $bus->DestinationName50 ?? null ?></p>
                <p class="number"><?= $bus->LinePublicNumber ?? null ?></p>
                <p class="company">
                    <?php
                    switch ($bus->OperatorCode) {
                        case 'EBS':
                            echo "EBS";
                            break;
                        case 'CXX':
                            echo "Connexxion";
                            break;

                        default:
                            echo $bus->OperatorCode;
                            break;
                    }
                    ?></p>
            <?php
            }
            ?>
        </div>
    <?php
    }
    ?>
</div>
<footer>
    Made by: quinten501 & FzzyLizzy
</footer>
</div>