<article class="overview">
    <div class="subtitles">
        <h2><a class="subtitle-recent active link" href="index.php">Most Recent</a></h2>
        <h2><a class="subtitle-popular link" href="index.php?page=popular">Most Popular</a></h2>
    </div>

    <section class="gallery">
        <ul class="image-grid">
            <?php foreach ($imagesRecent as $imageRecent) : ?>
                <li class="image-grid__item">
                    <a class="link" href="index.php?page=detail&amp;id=<?php echo $imageRecent['id']; ?>">
                        <div class="image-placement">
                            <img class="image__overview" src="<?php echo $imageRecent['path']; ?>" alt="<?php echo $imageRecent['title']; ?>">
                        </div>
                        <p class="image__text"><span>&#9872;</span></p>
                        <p class="image__text"><?php echo $imageRecent['title']; ?> (<?php echo $imageRecent['amount'] ?>)</p>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>


</article>