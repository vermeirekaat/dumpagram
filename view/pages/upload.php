<div class="upload">
<form class="upload-form" method="post" action="index.php?page=upload" enctype="multipart/form-data">

    <input type="hidden" name="action" value="addImage"/>

    <div class="form-field">
        <label> Select an image </label>
            <input type="file" name="image" accept="image/ png, image/jpeg, image/gif"/>
            <span class="error"><?php if (!empty($errors['image'])) {echo $errors['image'];} ?></span>
    </div>

    <div class="form-field">
        <label> Title </label>
            <input type="text" name="title" placeholder="Place - Country"/>
            <span class="error"><?php if (!empty($errors['title'])) {echo $errors['title'];} ?></span>
    </div>

    <div class="form-field">
        <label> Description </label>
            <textarea name="description" cols="50" rows="5"><?php if (!empty($POST['description'])){echo $_POST['description'];} ?></textarea>
            <span class="error"><?php if (!empty($errors['description'])) {echo $errors['description'];} ?></span>
    </div>

    <div class="form-field">
        <input type="submit" name="button" value="Upload"/>
    </div>
</form>

<article class="logo">
    <div class="logo-animation">
        <img class="logo__text"src="assets/img/dumpagram.png" alt="dumpagram-text">
        <img class="logo__image" src="assets/img/illustration.png" alt="dumpagram-logo">
    </div>
    
</article>
</div>