<form class="search-form" action="index.php">
    <label for="title">
        <span class="search__field">I'm looking for pictures in</span>
        <input class="search__input" id="title" type="text" name="title" value="<?php if (!empty($_GET['title'])) {echo $_GET['title'];} ?>">
    </label>
    <label for="description">
        <span class="search__field">The description contains the word</span>
        <input class="search__input" id="description" type="text" name="description" value="<?php if (!empty($_GET['description'])) {echo $_GET['description'];} ?>">
    </label>
    <input class="form-submit" type="submit" value="Search">
</form>




<article class="results">
    <h2 class="results__title">Magical places that were found</h2>
  <section class="gallery">
    <ul class="image-grid">
    <?php foreach ($results as $result) : ?>
      <li class="image-grid__item">
        <a class="link" href="index.php?page=detail&id=<?php echo $result['id']; ?>">
          <div class="image-placement">
            <img class="image__overview" src="<?php echo $result['path']; ?>" alt="<?php echo $result['title']; ?>">
          </div>
          <p class="image__text"><span>&#9872;</span></p>
          <p class="image__text"><?php echo $result['title'] ?></p>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
</article>