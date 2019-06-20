#### OAuth1 PoC using symfony/http-client

Using public test server (kudos to the author!): http://term.ie/oauth/example/

##### Disclaimer

It's only proof of concept so *there is* some mess in code ;) At the very least you should extract methods from `src/OAuth1/OAuthRequest.php`, maybe even create some interfaces, it's yours to decide. And of course fix `dev` dependencies in `composer.json` ^^

##### Usage

Installing dependencies

`docker container run --rm -v $(pwd):/app composer:latest install`

Running command

`docker container run --rm -t -v $(pwd):/app -w /app php:7.3-cli-stretch bin/console app:example`

`-t` is just for `Symfony`'s `dump` colors ;)

Everything is in the command `src/Command/ExampleCommand.php`

Enjoy!
