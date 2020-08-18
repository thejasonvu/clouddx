<!DOCTYPE html>
<html lang="en">
    <head>
        <title>RPM Calculator</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="<?=base_url?>Asset/css/style.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    </head>
    <body>
        <div class="bg"></div>
        <div class="container-fluid">
        <nav class="navbar col-md-8 offset-md-2">
            <h3 class="navbar-brand mb-0 h1 text-light">RPM Calculator</h3>
        </nav>
        <br>
            <?= $content_for_layout; ?>
        <div class="row">                
            <footer class="footer col-md-8 offset-md-2">
                <div style="color: #efefef;">© JAJACH 2019</div>
            </footer>
        </div>
        </div>
    </body>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</html>
