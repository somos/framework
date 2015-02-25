# Getting Started

If you have read the chapter where we explain what Somos is then you will have seen that Somos differs from conventional
frameworks in a few key areas. If you have never worked with frameworks in PHP before then this should not be an issue
but if you are things may work a bit different than you have dealt with before. Please bear with us: it will be quite
convenient.

## Actions

After you have finished the [installation](installation.html) of the framework you should have a file named `somos.php`
in your current directory. This is the only file that Somos needs and you can rename it at will if you want to; the 
name is not required.

The `init` command of Somos in the installation chapter will populate the `somos.php` source file with everything the
framework needs to know in order to operate. The contents differ per generator but for the sake of simplicity I am going
to show the configuration for the *microframework* option. This is, almost, the simplest version and demonstrates what you
need to get up and running.

So without further ado, this is the somos.php file generated when using the *microframework* way:

```php
<?php
use Somos\Somos;
use Somos\Responder;
use Somos\Http;

Somos::start()
    // TODO: Add actions here, such as: `->with(Http\Action::matches('/')->responds(Responder\Twig::class, ['homepage.html.twig']))`
    ->handle(new Http\Go());
```

This is the most basic web application that you can find and it doesn't do anything yet but accept a request and 
return the default 'Not Found (404)' message.

In order to tell Somos which pages it can show you will have to tell it which actions it can expect and how to recognize
them. You can do this with the `with()` and `andWith()` methods and passing it an instance of an Action class.

> **Hint:** The `andWith()` method is an alias of the `with()` method and is used to have a natural flow when
> reading the list of actions. You can choose to just use the `with()` method instead.
 

## Going Full-Stack

## Examples

> **Important**: because we are still under development not all examples will work yet; I use the examples as a goal
> to work towards. This framework is intended to be as simple and out-of-your-way as is possible and using those
> examples I can challenge myself to work towards that goal.

In the [examples repository](http://github.com/somos/examples) you can find various ways to use Somos, ranging from a
Command Line application, a blog and a REST API. Anything is possible!
