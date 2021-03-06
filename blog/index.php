<?php
require '../vendor/autoload.php'; // autoloader to avoid to "require" and "use" classes
use Ldrt\Blog\Post; // use <namespace\class> as <alias>

$pdo = new PDO('sqlite:../data.db', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
]);
$error = null;
try {
    if (isset($_POST['name'], $_POST['content'])) {
        $query = $pdo->prepare('INSERT INTO posts (name, content, created_at) VALUES (:name, :content, :creation)');
        $query->execute([
            'name' => $_POST['name'],
            'content' => $_POST['content'],
            'creation' => time()
        ]);
        header('Location: /blog/edit.php?id='. $pdo->lastInsertId());
        exit();
    }

    $query = $pdo->query('SELECT * FROM posts');
    /** @var Post[] */
    $posts = $query->fetchAll(PDO::FETCH_CLASS, Post::class);
    // PDO doesn't look up aliased class name nor resolve current namespace, so must specify it
} catch (PDOException $e) {
    $error = $e->getMessage();
}

require_once '../elements/header.php';
?>    

<?php if ($error) : ?>
    <div class="alert alert-danger">
        <?= $error ?>
    </div>
<?php else : ?>
    <?php foreach($posts as $post) : ?>
    <h2>
        <a href="/blog/edit.php?id=<?= $post->id ?>">
            <?= htmlentities($post->name) ?>
        </a>
    </h2>
    <p class="small text-muted">Created on <?= $post->created_at->format('d/m/Y H:i') ?></p>
    <p>
        <?= nl2br(htmlentities($post->getExcerpt())) ?>
    </p>
    <?php endforeach ?>

    <form action="" method="POST">
    <div class="form-group">
        <input type="text" class="form-control" name="name" placeholder="New article">
    </div>
    <div class="form-group">
        <textarea class="form-control" name="content"></textarea>
    </div>
    <button class="btn btn-primary">Send</button>
    </form>
<?php endif ?>

<?php
require_once '../elements/footer.php';
?>    