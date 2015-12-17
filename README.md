README
======

Implementation a REST API secured with OAuth2. This simple project provides APIs to manage tasks

The API only returns JSON responses

All API routes require authentication handled via OAuth2 with password grant type

Bundles used:
* [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle)
* [JMSSerializerBundle](https://github.com/schmittjoh/JMSSerializerBundle)
* [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)
* [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle)
* [FOSOAuthServerBundle](https://github.com/FriendsOfSymfony/FOSOAuthServerBundle)
* [NelmioCorsBundle](https://github.com/nelmio/NelmioCorsBundle)


Installation
------------
* Composer:
  ~~~bash
  composer install
  ~~~
    
* Configure database parameters: 
  ~~~bash
  app/config/parameters.ini
  ~~~

* Create database and tables:
  ~~~bash
  php app/console doctrine:database:create
  php app/console doctrine:schema:create
  ~~~

* Create a fos user:
  ~~~bash
  php app/console fos:user:create
  ~~~

* Create a client:
 See: https://github.com/FriendsOfSymfony/FOSOAuthServerBundle/blob/master/Resources/doc/index.md#creating-a-client


Usage
-----------
First we have to request an access token:

~~~bash
POST http://api.task/app.php/oauth/v2/token
Body params:
client_id=3_2dnysev5qmo0ws4go4g8k8kcg4cckg4og4c8kwosws0csk40ss
client_secret=213yuoi2jzpc8g40k84g0cowwss0skscw8w80ssko4cc48g4ck
grant_type=password
password=123456
username=raul

{
  "access_token": "MjgyODNkOGNiNDdkZGJjOTE5ZDNjZmNlODNlMzViNTA1MjkwNDM3NmFiMTQxYTEwNTljMDk2NWRmYzE3MDU1YQ",
  "expires_in": 3600,
  "token_type": "bearer",
  "scope": null,
  "refresh_token": "NWUxNjM3ODljZjNkOWQ0ZWYzY2JlYWYzOGZkMDU5MDEzMDk4OTcyNDZlNzk1ZGE0OTA1YTYyZTkwNzY3ZGFiMw"
}
~~~


Now we can make calls to any API endpoint by sending the access token as a Bearer:

~~~bash
GET http://localhost/task/web/app_dev.php/api/projects/1
Authorization: Bearer MjgyODNkOGNiNDdkZGJjOTE5ZDNjZmNlODNlMzViNTA1MjkwNDM3NmFiMTQxYTEwNTljMDk2NWRmYzE3MDU1YQ

{
  "id": 1,
  "title": "First project",
  "created_at": "2015-10-28T15:59:53+0100",
  "modified_at": "2015-10-28T16:40:29+0100"
}
~~~


At this point we can create a client (mobile app, AngularJs front-end..) that consumes our API


The following is a list of the generated routes:
~~~bash
GET      /api/projects/{id}
GET      /api/projects
POST     /api/projects
PUT      /api/projects/{id}
PATCH    /api/projects/{id}
DELETE   /api/projects/{id}
GET      /api/projects/{project}/tasks/{id}
GET      /api/projects/{project}/tasks
POST     /api/projects/{project}/tasks
PUT      /api/projects/{project}/tasks/{id}
PATCH    /api/projects/{project}/tasks/{id}
DELETE   /api/projects/{project}/tasks/{id}
~~~