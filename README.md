---
currentMenu: homepage
---

Somos
=====

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/somos/framework/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/somos/framework/?branch=master)
[![Build Status](https://travis-ci.org/somos/framework.svg)](https://travis-ci.org/somos/framework)

> **Important**: I have only started working on this framework recently; this means that some of the features mentioned
> in this README or other documents may not have been (fully) implemented yet. This will change over time and I will
> change this message accordingly.

Somos is a framework based on renewed insights and trends within the PHP Community. It intends to be as free of magic
as is possible but at the same time gets out of the way of your application.

Did you ever experience this?

* Ever felt that you need to write a ton of boilerplate code for your framework just to get your app running?
* Ever wondered why only 1 out of 10 folders in your project actually contains your code and the other ones are all for 
  your framework.
* You write workarounds to get your framework to do what you want to do?

With Somos I have started a journey to create a framework where all you do is write the code that you need to write for
your application and not to satisfy Somos.

The buzzwords (a.k.a. features)
-------------------------------

Somos is at heart a **micro-framework** that sports a **Message Bus** internally and that comes with a series of 
commands to transform it into a **full-stack** framework for your needs. It is flexible, and small, enough to be an 
excellent choice for command line tools but also for enterprise web applications that have to be able to scale.

By leveraging the **[Action-Domain-Responder](https://github.com/pmjones/adr)** design pattern as a first-class citizen 
Somos completely decouples the business logic from the presentation. Actions are blissfully unaware of how their data 
is returned, which in turn enables you to reuse that same action both from the **command line** option, in a 
**REST API** or normal **web application**.

Somos cares about **playing nice** with libraries that can be used. Somos leverages PHP-DI's **auto-wiring** 
capabilities to automatically inject dependencies that are needed to construct objects, meaning that _you_ don't have 
to write lengthy service definitions to add a single service.

In addition Somos leverages all the goodness brought to us by the **PHP-FIG** by adhering to **PSR-0, PSR-2, PSR-4, 
PSR-5 (in draft) and PSR-7 (in draft)**. This means that when you want to replace one component with another that you
only have to point Somos in the right direction and it integrates it as if it had always been there.

Installation
------------

> **Important**: because we are still under development not all examples will work yet; I use the examples as a goal
> to work towards. This framework is intended to be as simple and out-of-your-way as is possible and using those
> examples I can challenge myself to work towards that goal.

In the [examples repository](http://github.com/somos/examples) you can find various ways to use Somos, ranging from a
Command Line application, a blog and a REST API. Anything is possible!

The easiest way to use Somos is by installing it using composer and run the 'init' command to create a skeleton 
directory structure.

    composer require somos/framework
    ./vendor/bin/somos init
    
> By default Somos will create a skeleton for a web application with all bells and whistles (such as Doctrine for ORM).
> Somos is also capable of creating skeletons when you want to use it as a micro-framework or cli application; please
> run `./vendor/bin/somos list init` to see a list of possibilities.

Why another framework?
----------------------

In the last few years we have seen new ideas and concepts emerge in the PHP community; among those is the Action
Domain Responder pattern, Domain Driven Design, the creation of PSR-7 and auto-wiring dependency injection containers. 
Combine this with several years of experience with various frameworks and I wanted to at least try and see if I could 
use those concepts and my experience to create a unique framework experience.
