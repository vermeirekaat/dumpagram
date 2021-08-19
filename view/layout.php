<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/style.css">
  <title>Dumpagram</title>
</head>

<body>

  <header class="header">
    <h1 class="hidden">Dumpagram</h1>
    <div class="header__wrapper">
      <div class="header__link">
        <a class="header__url" href="index.php"><img class="header__logo" src="assets/img/logo.png" alt="dumpagram-logo"></a>
      </div>
      <div class="tagline">
        <p class="tagline-blue">Discover the world</p>
        <p class="tagline-green">Admire the grace of nature</p>
      </div>

      <a class="search__button link" href="index.php?page=search">
        <span>&#10031;</span> Explore new places <span>&#10031;</span></a>

    </div>

    <div>
      <a class="add__button link" href="index.php?page=upload"><span>&#10011;</span> Add a beautiful location <span>&#10011;</span></a>
    </div>
  </header>

  <main>
    <!-- boodschap (info of error) van de session zal hier getoond worden indien dit van toepassing is -->
    <?php if (!empty($_SESSION['info'])) : ?>
      <div class="session info"><?php echo $_SESSION['info']; ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])) : ?>
      <div class="session errors"><?php echo $_SESSION['error']; ?></div>
    <?php endif; ?>

    <?php echo $content; ?>
  </main>

  <footer class="footer">
    <p class="footer__text footer-green hidden">Dumpagram</p>
    <p class="footer__text footer-blue"><span>&copy;</span>Dumpagram</p>
  </footer>

  <script src="js/script.js"></script>
  <script src="js/validate.js"></script>
</body>

</html>