<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus info | 0</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
</head>

<body>
    <div id="bustable"></div>
    <script>
        var timmer = 0;
        var index = -1;
        var haltes = [
            'spihw2',
            'spispo'
        ];

        function reloadtable() {
            window.setTimeout(function() {
                if (timmer <= 1) {
                    timmer = 15;
                    index = index + 1;
                    if (index >= haltes.length) {
                        index = 0;
                    }
                    console.log(index);
                    xhr(haltes[index]);
                }
                document.title = "Bus info | "+ (index+1) + "/"+ haltes.length +" | " + timmer;
                timmer--;
                reloadtable();
            }, 1000);
        }

        function xhr(stopareacode) {
            $.get("table.php", {
                    halte: stopareacode
                })
                .done(function(data) {
                    $('#bustable').html(data);
                });
        }
        reloadtable();
    </script>
</body>

</html>