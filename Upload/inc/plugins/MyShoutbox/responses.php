<?php

function OkResponse(){
	http_response_code(200);
	exit;
}

function OkResponseWithObject($responseObject){
	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode($responseObject);
	exit;
}

function BadRequestResponse($message){
	http_response_code(400);
	header('Content-Type: application/json');
	$response = new StdClass();
	$response->message = $message;
	echo json_encode($response);
	exit;
}

function UnauthorisedResponse(){
	http_response_code(401);
	exit;
}

function NotFoundResponse(){
	http_response_code(404);
	exit;
}

function InternalServerErrorResponse(){
	http_response_code(500);
	exit;
}
?>