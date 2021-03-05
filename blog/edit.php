<?php
$pdo = new PDO('sqlite:../data.db', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
]);
$error = null;
$success = null;
try {
    if (isset($_POST['name'], $_POST['content'])) {
        $query = $pdo->prepare('UPDATE posts SET name = :name, content = :content WHERE id = :id');
        $query->execute([
            'id' => $_GET['id'],
            'name' => $_POST['name'],
            'content' => $_POST['content']
        ]);
        $success = "Article updated";
    }

    // prepare then execute : avoiding SQL injections
    $query = $pdo->prepare('SELECT * FROM posts WHERE id = :id');
    $query->execute([
        'id' => $_GET['id']
    ]);
    $post = $query->fetch();
    /*
    $pdo->beginTransaction();
    queries ... 
    $pdo->commit(); // or rollback()
    */
} catch (PDOException $e) {
    $error = $e->getMessage();
}

require_once '../elements/header.php';
?>    

<p>
    <a href="/blog">Back to list</a>
</p>

<?php if ($success) : ?>
    <div class="alert alert-success">
        <?= $success ?>
    </div>
<?php endif ?>
<?php if ($error) : ?>
    <div class="alert alert-danger">
        <?= $error ?>
    </div>
<?php endif ?>
<form action="" method="POST">
<div class="form-group">
    <input type="text" class="form-control" name="name" value="<?= htmlentities($post->name) ?>">
</div>
<div class="form-group">
    <textarea class="form-control" name="content"><?= htmlentities($post->content) ?></textarea>
</div>
<button class="btn btn-primary">Save</button>
</form>

<?php
require_once '../elements/footer.php';
?>    