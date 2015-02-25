# Installation

The easiest way to use Somos is by installing it using composer and run the 'init' command to create a skeleton 
directory structure.

    composer require somos/framework
    ./vendor/bin/somos init
    
> By default Somos will create a skeleton for a web application with all bells and whistles (such as Doctrine for ORM).
> Somos is also capable of creating skeletons when you want to use it as a micro-framework or cli application; please
> run `./vendor/bin/somos list init` to see a list of possibilities.
