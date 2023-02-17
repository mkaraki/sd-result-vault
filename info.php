<?php
require_once __DIR__ . '/png-info.php';
define(
    'IMAGE_DIR',
    __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR
);

if (!isset($_GET['img'])) {
    http_response_code(400);
    die('No image');
}

$gi = $_GET['img'];
if (DIRECTORY_SEPARATOR !== '/')
    $gi = str_replace('/', DIRECTORY_SEPARATOR, $_GET['img']);

$gi = IMAGE_DIR . $gi;

$p = realpath($gi);

if ($p === false || !str_starts_with($p, __DIR__)) {
    http_response_code(400);
    die('No image');
}

$info = parseDiffusionParameters($p);
if (isset($info['generation'])) {
    $generation_info = $info['generation'] ?? [];
    unset($info['generation']);
    $info = array_merge($info, $generation_info);
}

$uhost = 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '') . '://' .
    $_SERVER['HTTP_HOST'];
$rurl = $uhost . $_SERVER['REQUEST_URI'];
$url = parse_url($rurl);
$paths = explode('/', $url['path']);
$paths = array_slice($paths, 1, -1);
$path = implode('/', $paths);
if (!str_ends_with($path, '/') && strlen($path) > 1)
    $path .= '/';
$imgurl = $uhost . '/' . $path . 'img/' . $_GET['img'];


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image info</title>
    <meta property="og:url" content="<?= htmlentities($rurl) ?>">
    <meta property="twitter:url" content="<?= htmlentities($rurl) ?>">
    <meta property="og:type" content="article">
    <meta property="og:title" content="Image info">
    <meta name="twitter:title" content="Image info">
    <meta property="og:image" content="<?= htmlentities($imgurl) ?>">
    <meta name="twitter:image" content="<?= htmlentities($imgurl) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body style="padding: 5px;">
    <div class="container">
        <div class="row">
            <div class="col">
                <img src="<?= htmlentities($imgurl) ?>" alt="Image" class="img-fluid">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Parameter Name</th>
                            <th scope="col">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($info as $index => $param) : ?>
                            <tr>
                                <th scope="row"><?= htmlentities($index) ?></th>
                                <td><?= str_replace("\n", '<br />', htmlentities($param)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>