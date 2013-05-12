WScore.Web
==========

Web application front-end dispatcher. 

Idea on new structure
---------------------

The basic concept on request/response is,

```php
$response = $responder->request( $request, $post )->respond();
$response->render()->respond()->send();
```

where

$responder
: an object to return $response for a given request. 

$response
: a response object. 
: set to null if no response. 

$request
: a request object which contains request info.

$post
: post data



This section should be removed if this new idea is implemented, 
or rejected. 

###Overview

request and response. 

There is Http\Request and Http\Response, and WScore's 
Request and Response objects. 

module interface. 

$response = $module->request( $request, $post )->respond();
$response->render()->send();

###Request

baseURL
pathInfo
appUrl
appInfo
method
what

###Response

action
request
status
headers
data
content
renderer

