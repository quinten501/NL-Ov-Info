<?php
$halte = $_GET['halte'] ?? 'spihw2';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus info | 30</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
</head>

<body>
    <div id="bustable"></div>
    <script>
        var timmer = 30;

        function reloadtable() {
            window.setTimeout(function() {
                reloadtable();
                document.title = "Bus info | " + timmer;
                if (timmer <= 0) {
                    timmer = 30;
                    xhr("<?= $halte ?>");
                }
                timmer--;
            }, 1000);
        }

        function xhr(stopareacode) {
            $.get("table.php", {
                    halte: stopareacode})
                .done(function(data) {
                    $('#bustable').html(data);
                });
        }
        xhr("<?= $halte ?>");
        reloadtable();
    </script>
</body>
</html>