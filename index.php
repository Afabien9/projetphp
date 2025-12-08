<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bataille Navale</title>

    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" 
          crossorigin="anonymous">
</head>

<body>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" 
            crossorigin="anonymous"></script>

    <!-- HEADER -->
    <header>
        <nav class="navbar bg-body-tertiary" data-bs-theme="dark">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Bataille Navale</span>
            </div>
        </nav>
    </header>

    <!-- MAIN -->
    <main class="container mt-4">

        <div class="row">

            <!-- ======================== -->
            <!--      GRILLE DE GAUCHE    -->
            <!-- ======================== -->
            <div class="col-5">

                <h1 id="message">Grille adverse</h1>

                <div class="container mt-3">

                    <?php
                    $lettres = range('A', 'J'); // lignes A à J
                    $chiffres = range(1, 10);   // colonnes 1 à 10
                    ?>

                    <div class="container">
                        <!-- Ligne des chiffres -->
                        <div class="row text-center">
                            <div class="col border square"></div> <!-- coin vide -->
                            <?php foreach ($chiffres as $chiffre): ?>
                                <div class="col border square"><?php echo $chiffre; ?></div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Lignes A à J -->
                        <?php foreach ($lettres as $lettre): ?>
                            <div class="row text-center">
                                <div class="col border square"><?php echo $lettre; ?></div>
                                <?php for ($i = 0; $i < 10; $i++): ?>
                                    <div class="col border square"></div>
                                <?php endfor; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ======================== -->
            <!--      GRILLE DE DROITE    -->
            <!-- ======================== -->
            <div class="col-5 offset-2">

                <h1 class="text-center">Votre grille</h1>

                <div class="container mt-3">

                    <?php
                    $lettres = range('A', 'J'); // lignes A à J
                    $chiffres = range(1, 10);   // colonnes 1 à 10
                    ?>

                    <div class="container">
                        <!-- Ligne des chiffres -->
                        <div class="row text-center">
                            <div class="col border square"></div> <!-- coin vide -->
                            <?php foreach ($chiffres as $chiffre): ?>
                                <div class="col border square"><?php echo $chiffre; ?></div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Lignes A à J -->
                        <?php foreach ($lettres as $lettre): ?>
                            <div class="row text-center">
                                <div class="col border square"><?php echo $lettre; ?></div>
                                <?php for ($i = 0; $i < 10; $i++): ?>
                                    <div class="col border square"></div>
                                <?php endfor; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer></footer>

</body>
</html>
