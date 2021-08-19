 <div class="return">
     <a class="return__link link" href="index.php">
         <-- Go back to overview</a> </p>
</div> 

<article class="detail">
    <?php if (empty($image)) : ?>
       <p class="session errors"> &#10005; This image doesn't exist &#10005;</p>
    <?php else : ?>

    <section class="detail__image">
        <h2 class="detail__title"><?php echo $image['title'] ?></h2>
            <img class="image__detail" src="<?php echo $image['path'] ?>" alt="<?php echo $image['title'] ?>">
    </section>

    <section class="vote">
        <?php foreach ($reactions as $reaction): ?>
        <ul class="vote__list">
            <li class="vote__item">
                <img class="reaction-image" src="<?php echo $reaction['icon'] ?>" alt="<?php echo $reaction['name']?>">
                <p class="vote-count"><?php echo $reaction['reaction_amount']?></p>
            </li>
        </ul>
        <?php endforeach; ?>
    </section>

    <section class="content">
        <h3 class="detail__subtitle">Description</h3>
            <p class="image__description"><?php echo $image['description'] ?></p>

        <h3 class="detail__subtitle">Comments</h3>
            <ul class="comments">
                <?php foreach ($imageComments as $imageComment) : ?>
                    <li class="comments__item"><?php echo $imageComment['comment'] ?></li>
                <?php endforeach; ?>
            </ul>

        <form class="comment-form" method="post" action="index.php?page=detail&id=<?php echo $image['id']; ?>">

            <input type="hidden" name="action" value="insertComment" />
                <div>
                    <label class="form__subtitle">Leave a comment </label>
                        <textarea class="form__comment" name="comment" rows="4" cols="80" required><?php if (!empty($_POST['comment'])) {echo $_POST['comment'];} ?></textarea>
                        <span class="error"><?php if (!empty($errors['comment'])) {echo $errors['comment'];} ?></span>
                </div>
                <div>
                    <input type="submit" name="button" value="Add comment" class="submit__button-comments">
                </div>
        </form>

        <form class="reaction-form" method="post" action="index.php?page=detail&id=<?php echo $image['id']; ?>">
        <input type="hidden" name="action" value="insertReaction">
        <label class="form__subtitle">What is your reaction?</label>
        <div class="reactions">
            <?php foreach ($reactions as $reaction): ?>
                <button class="button" type="submit" name="reaction_id" value="<?php echo $reaction['id']?>"><img class="reaction-image" src="<?php echo $reaction['icon'] ?>" alt="<?php echo $reaction['name'];?>"></button>
            <?php endforeach; ?>
        </div>
        </form>
    </section>

    <?php endif; ?>

</article>