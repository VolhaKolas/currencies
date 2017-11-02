<!DOCTYPE html>
<html>
<head>
    <title>Currencies</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="panel panel-default modal-dialog">
            <div class="panel-body">
                <?php if($message != ''): ?>
                <div class="wrapper">
                    <?= $message ?>
                </div>
                <?php endif;?>
                <div class="wrapper">
                    <form action="/currencies" enctype="multipart/form-data" method="POST">
                        <input type="date" name="chosenDate" class="btn btn-default btn-pressure btn-sensitive">
                        <input type="submit" class="btn btn-default btn-pressure btn-sensitive" value="Запросить курс">
                    </form>
                </div>
                <div class="wrapper">
                    <form action="/courses" enctype="multipart/form-data" method="GET">
                        <input type="date" name="chosenDate" class="btn btn-default btn-pressure btn-sensitive">
                        <input type="submit" class="btn btn-default btn-pressure btn-sensitive" value="Показать курс валют на дату">
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>