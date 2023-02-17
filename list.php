<?php
require_once __DIR__ . '/png-info.php';
define(
    'IMAGE_DIR',
    __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR
);

if (!isset($_GET['img']))
    $_GET['img'] = '';

$ig = $_GET['img'];
if (!str_ends_with($ig, '/') && strlen($ig) > 2)
    $ig .= '/';

$gi = $_GET['img'];
if (DIRECTORY_SEPARATOR !== '/')
    $gi = str_replace('/', DIRECTORY_SEPARATOR, $_GET['img']);

$gi = IMAGE_DIR . $gi;

$p = realpath($gi);

if ($p === false || !str_starts_with($p, __DIR__) || !is_dir($p)) {
    http_response_code(400);
    die('No dir');
}

if (!str_ends_with($p, DIRECTORY_SEPARATOR))
    $p .= DIRECTORY_SEPARATOR;

function getImgDir($img)
{
    global $_SERVER;
    $uhost = 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '') . '://' .
        $_SERVER['HTTP_HOST'];
    $rurl = $uhost . $_SERVER['REQUEST_URI'];
    $url = parse_url($rurl);
    $paths = explode('/', $url['path']);
    $paths = array_slice($paths, 1, -1);
    $path = implode('/', $paths);
    if (!str_ends_with($path, '/') && strlen($path) > 1)
        $path .= '/';
    return $uhost . '/' . $path . 'img/' . $img;
}

$dirEntries = scandir($p);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body style="padding: 5px;">
    <div class="container">
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-6 g-4">
            <?php foreach ($dirEntries as $content) : ?>
                <?php if (str_starts_with($content, '.')) continue; ?>
                <div class="col">
                    <div class="card">
                        <?php if (is_file($p . $content) && str_ends_with($content, '.png')) : ?>
                            <img src="<?= htmlentities(getImgDir($ig . $content)) ?>" class="card-img-top" alt="thumbnail">
                            <div class="card-body">
                                <h5 class="card-title"><a href="info.php?img=<?= htmlentities($ig . $content) ?>"><?= htmlentities($content) ?></a></h5>
                            </div>
                        <?php elseif (is_dir($p . $content)) : ?>
                            <i class="card-img-top bi bi-folder"></i>
                            <div class="card-body">
                                <h5 class="card-title"><a href="?img=<?= htmlentities($ig . $content) ?>"><?= htmlentities($content) ?>/</a></h5>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>