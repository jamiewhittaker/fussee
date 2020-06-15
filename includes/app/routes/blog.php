<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



$app->get('/blog', function(Request $request, Response $response)
{
    $arr = [];
    if (isset($_SESSION['loggedIn'])) {
        $arr["firstName"] = $_SESSION["firstName"];
        $arr["loggedIn"] = true;
    } else {
        session_destroy();
    }

    $parsedown = new Parsedown();
    $exampleMarkdown =
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque vel luctus diam, nec volutpat magna. Morbi est justo, congue in dolor quis, tristique semper magna. Nunc condimentum sapien et urna maximus, ut lacinia arcu tempor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Donec fringilla purus id eros feugiat vehicula. Nulla imperdiet ex id felis ornare suscipit. Nulla condimentum augue ut auctor semper. Integer tincidunt mollis risus, vel pharetra elit. Nullam a porta neque. Praesent suscipit eu eros sed laoreet. Maecenas pretium imperdiet tortor vitae aliquet. Nam a elit ut sem fringilla condimentum. 

## Front-end Redesign
The decision has been made to completely remake the front end of the website using Bootstrap as a framework. This allows for much faster development and a much more responsive product.

## Routing changes
Much of the back-end logic involving the HTML outputted to the user such as errors and permissions were previously part of the routing. This is now handled by the TWIG templates themselves.";

    $arr["post"] = $parsedown->text($exampleMarkdown);
    return $this->view->render($response, 'blog.html.twig', $arr);

})->setName('/blog' );
