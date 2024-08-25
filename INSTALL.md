# To install just copy files and catalogs to your [ api/ ] catalog of the api-platform project 

### Running by local Symfony server to connection to other services

1. run symfony proxy https://symfony.com/doc/current/setup/symfony_server.html#setting-up-the-local-proxy
2. come up with new local domain (for example: user-api ])
3. create file with your local domain .symfony.local.yaml 
````
#.symfony.local.yaml
proxy:
    domains:
        - user-api
````
3. attach your domain 
````bash
symfony proxy:domain:attach user-api
````
4. add domain to trusted hosts in your .env
````
# API Platform distribution
TRUSTED_PROXIES=127.0.0.0/8,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16
TRUSTED_HOSTS=^(user-api.wip|ocalhost|php)$
###> symfony/framework-bundle ###
````
5. check out symfony proxy
````bash
symfony proxy:status
````
6. run symfony proxy if it has not run yet
````bash
symfony proxy:start
````
7. run symfony server
````bash
symfony serve -d
````
