README
======

Implementation a REST API secured with OAuth2. This simple project provides APIs to manage projects with subtasks.

The API only returns JSON responses.

All API routes require authentication handled via OAuth2 with password grant type.

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
  ~~~bash
  https://github.com/FriendsOfSymfony/FOSOAuthServerBundle/blob/master/Resources/doc/index.md#creating-a-client
  ~~~


Usage
-----------
First we have to request an access token:

~~~bash
curl -i -X POST "http://localhost/symfony2-rest-api/web/app_dev.php/oauth/v2/token" --data "client_id=3_2dnysev5qmo0ws4go4g8k8kcg4cckg4og4c8kwosws0csk40ss&client_secret=213yuoi2jzpc8g40k84g0cowwss0skscw8w80ssko4cc48g4ck&grant_type=password&password=123456&username=raul"

{
  "access_token":"MDZiMjY1OTVlMWQ5ODFiOGM1MjE3N2MzNmNmODk5YzA0ZGY5NTdkZDE3MzM1NDczMGZhZmZiZTJlZTUwNTJmYg",
  "expires_in":3600,
  "token_type":"bearer",
  "scope":null,
  "refresh_token":"OTI1MDczMGJmZDg0NGY3ZDA1MTQzMGZlZGQyZmUyMjIxMWEzMzE5ZGUwNGE3MjY4N2RiMTAzOWJkOTY4ZjkyOQ"
}
~~~

Now we can make calls to any API endpoint by sending the access token as a Bearer:

**GET**
~~~bash
curl -i -X GET "http://localhost/symfony2-rest-api/web/app_dev.php/api/projects/1" -H "Authorization: Bearer MDZiMjY1OTVlMWQ5ODFiOGM1MjE3N2MzNmNmODk5YzA0ZGY5NTdkZDE3MzM1NDczMGZhZmZiZTJlZTUwNTJmYg"

HTTP/1.1 200 OK
{
  "id":1,
  "title":"My first project",
  "created_at":"2016-02-18T23:17:02+0100",
  "modified_at":"2016-02-18T23:17:02+0100"
}
~~~

**POST**
~~~bash
curl -i -X POST "http://localhost/symfony2-rest-api/web/app_dev.php/api/projects" -H "Authorization: Bearer MDZiMjY1OTVlMWQ5ODFiOGM1MjE3N2MzNmNmODk5YzA0ZGY5NTdkZDE3MzM1NDczMGZhZmZiZTJlZTUwNTJmYg" --data "title=New Project"

HTTP/1.1 201 Created
~~~

**PUT**
~~~bash
curl -i -X PUT "http://localhost/symfony2-rest-api/web/app_dev.php/api/projects/10" -H "Authorization: Bearer MDZiMjY1OTVlMWQ5ODFiOGM1MjE3N2MzNmNmODk5YzA0ZGY5NTdkZDE3MzM1NDczMGZhZmZiZTJlZTUwNTJmYg" --data "title=New Project updated"

HTTP/1.1 204 No Content
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