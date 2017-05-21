<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app.php';

/** @var \Symfony\Component\HttpFoundation\Request $request */
$request = $app['request'];
/** @var \Symfony\Component\HttpFoundation\Response $response */
$response = $app['response'];

if ($request->isMethod(\Symfony\Component\HttpFoundation\Request::METHOD_POST)) {
    $publisher = \CartBooking\Application\ServiceLocator::getPioneerRepository()->findByPhone((int) $request->get('user'));
    if ($publisher !== null && password_verify($request->get('pass'), $publisher->getPassword())) {
        $expiry = time() + 60 * 60 * 24 * 30;
        setcookie("login", $publisher->getId(), $expiry);
        return \Symfony\Component\HttpFoundation\RedirectResponse::create('/')->send();
    }
    echo $app['twig']->render('login.twig', ['failedLogin' => true]);
    return;
}

echo $app['twig']->render('login.twig');
