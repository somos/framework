# Working with Environments

Small-sized projects often do not have differences in the way the local development copy works versus a production 
environment. As soon as your project starts to grow in size you will notice that it becomes convenient to have a
master configuration and override specific parts for specific environments such as development, staging or production.

With Somos you are free to differentiate between environments as you see fit; Somos does not require you to do a 
specific mapping or change to make that happen.

So. How can you do this? 

> Disclaimer: the following method is only an example how you can set up environments in your application but really:
> how you get or combine arrays with settings is entirely up to you.

Let's assume that your current application looks like this:

```php
<?php
\Somos\Somos::start([
    'debug' => true,
    'twig.templates.path' => __DIR__ . '/views'
])
    ->with(\Somos\Http\Action::matches('/')->responds(\Somos\Responder\Twig::class, ['homepage.html.twig']))
    ->handle(new \Somos\Http\HandleRequest());
```

In this example you can see a single page application that shows the view `homepage.html.twig` as an opening page. The 
configuration, *which is the argument of the start method*, indicates that debugging is enabled and that the root path 
for the twig templates is a subfolder of the current folder called 'views'.

Now suppose that we want to override parts of our settings based on an environment variable called 'MYAPP_ENV' then we
can change the code to do the following:

```php
<?php
$configuration = [
    'debug' => true,
    'twig.templates.path' => __DIR__ . '/views'
];

if ($_ENV['MYAPP_ENV'] == 'prod') {
    $configuration = array_merge($configuration, [
        'debug' => false
    ]);
}

\Somos\Somos::start($configuration)
    ->with(\Somos\Http\Action::matches('/')->responds(\Somos\Responder\Twig::class, ['homepage.html.twig']))
    ->handle(new \Somos\Http\HandleRequest());
```

Of course, this is just a basic example. Once your application grows you can move the configuration arrays into separate
files, or even merge them using provisioning tools such as webistrano, phing or ansible.
