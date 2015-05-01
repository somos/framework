<?php

namespace Somos;
use League\Tactician\CommandBus;

/**
 * The kernel for the Somos framework.
 *
 * Using the kernel you can start the framework, add a series of actions and handle commands that can make use of these
 * actions.
 *
 * Example of a simple command line application:
 *
 *     \Somos\Somos::start()
 *         ->add(
 *             \Somos\Action::get(
 *                 new \Somos\Console\Command('greet:world')
 *             )->respondWith(function () { echo 'Hello World!'; }
 *         )
 *         ->handle(new \Somos\Console\Run());
 *
 * Let's look at the example above line-by-line:
 *
 * Line 1. The framework is started (configuration and modules can be added as parameters)
 * Line 2. We call the add method on the kernel to give it an action that _may_ be executed later.
 * Line 3. In the call method an object of class `\Somos\Action` is created and we pass it an instance of
 *         `\Somos\Console\Command`.
 * Line 4. With command we provide the name with which we can invoke this command from the command line ('hello:world').
 * Line 5. We tell the Action that as soon as it is done it will echo 'Hello World!' on screen.
 * Line 7. The kernel is instructed to execute the Command `\Somos\Console\Run`; which will check if the command line
 *         has been called with the 'hello:world' command and if so, it will execute the Action that we have provided
 *         earlier and respond with the given message ('Hello world').
 *
 * Here is a more complicated example where we accept an option called 'name' with which we can show the name of the
 * person to greet on screen:
 *
 *     \Somos\Somos::start()
 *         ->add(
 *             \Somos\Action::get(
 *                 new \Somos\Console\Command('greet:person', ['name' => ['Name to display']]),
 *                 function ($name) { return ['name' => $name]; }
 *             )->respondWith(function ($name) { echo "Hello $name!"; })
 *         )
 *         ->handle(new \Somos\Console\Run());
 *
 * The most noteworthy change has been in line 4 and 5; on line 4 we have added a definition on the command which
 * options are supported and what description they have (in this case it is just the option 'name').
 * On line 5 we have added a callback
 */
final class Somos
{
    /**
     * The message bus that deals with the commands that need to be executed by the kernel.
     *
     * @var CommandBus
     */
    private $messagebus;

    /**
     * The available actions that have been configured for this instance.
     *
     * An action can be a command line action for running a command line application, a route (including action and
     * response) for a web execution or anything else that can be selected and ran using a Command.
     *
     * Each action consists of a matcher, which is used to select
     *
     * @var Actions
     */
    private $actions;

    /**
     * A list of supported scopes for handlers and how to determine if this is the scope.
     *
     * Handlers in the message bus can be executed conditionally depending if we are in the right scope. This means that
     * we can deal with, for example, CLI and Web handlers from the same kernel file.
     *
     * @var callable[]
     */
    private $scopes = [];

    /**
     * If no scope is specified when invoking a handler then this scope is used.
     *
     * @var string
     */
    private $defaultScope = 'web';

    /**
     * Starts the kernel of the Somos Framework.
     *
     * This factory method uses the KernelFactory class to create a new instance of the Somos Framework ready to be used
     * by adding Actions and handling Commands. See the class DocBlock for more information on how to use this class.
     *
     * @param mixed|mixed[] $configuration
     * @param mixed[] $modules
     *
     * @see self::add() to add actions to the returned Kernel instance.
     * @see self::handle() to execute a command
     *
     * @return static
     */
    public static function start($configuration = null, array $modules = [])
    {
        return KernelFactory::getInstance()->create($configuration, $modules);
    }

    /**
     * Injects the message bus and action listing into the Kernel.
     *
     * @param CommandBus $messagebus
     * @param Actions    $actions
     */
    public function __construct(CommandBus $messagebus, Actions $actions)
    {
        $this->messagebus = $messagebus;
        $this->actions    = $actions;

        // by default there are two scopes: cli for command line stuff and web for web-served content.
        $this->scopes['cli'] = function () {
            return php_sapi_name() === 'cli' || defined('STDIN');
        };
        $this->scopes['web'] = function () {
            return php_sapi_name() !== 'cli' && ! defined('STDIN');
        };
    }

    /**
     * Registers an action with the kernel.
     *
     * Actions are registered to the kernel so that {@see self::handle() Commands} can determine what business logic
     * to execute based on a given action.
     *
     * For example:
     *
     *     An action may be a single page request where the route is embedded in the Action. The Command that deals
     *     with HTTP requests and returning responses can iterate over the list of actions and if it encounters an
     *     action that matches the current URL it will execute its associated behaviour and hand the data from that
     *     action to a responder.
     *
     * @param Action $action
     *
     * @return $this
     */
    public function with(Action $action)
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * Registers another action with the kernel.
     *
     * @param Action $action
     *
     * @see self::with() for more information.
     *
     * @return $this
     */
    public function andWith(Action $action)
    {
        $this->with($action);

        return $this;
    }

    /**
     * Sets the default scope for handlers.
     *
     * @param string $scope
     *
     * @return $this
     */
    public function withDefaultScope($scope)
    {
        $this->defaultScope = $scope;

        return $this;
    }

    /**
     * Handles a Command that is passed to the Kernel.
     *
     * A Command is an indication to the command bus that a specific command needs to be executed. An example of this
     * is that via a command we can indicate that a HTTP request needs to be dealt with, or that authentication needs
     * to be applied.
     *
     * @param object $command
     * @param string $scope   A scope to which this handler belongs; may have multiple scopes separated by a comma. In
     *     case of multiple scopes all scopes must match for the handler to trigger.
     *
     * @return $this
     */
    public function handle($command, $scope = null)
    {
        if ($this->isInScope($scope)) {
            $this->messagebus->handle($command);
        }

        return $this;
    }

    /**
     * Adds additional scopes.
     *
     * Applications can add additional scopes so that they can decide to execute specific handlers according to the
     * determined scope. This can be useful if, for example, you want to execute behaviour on a specific server (master)
     * but not on the others. In this case you can add a new scope with a callable that detects if the app is installed
     * on a specific server. And after adding the scope you can invoke handlers with just that scope.
     *
     * @param $name
     * @param callable $matcher
     * @return $this
     */
    public function addScope($name, callable $matcher)
    {
        $this->scopes[$name] = $matcher;

        return $this;
    }

    /**
     * Checks if the current environment matches all provided scopes.
     *
     * Multiple scopes may be passed separated by a comma. When multiple scopes are provided they must all match.
     *
     * @param string $scope
     *
     * @return boolean
     */
    private function isInScope($scope)
    {
        if ($scope === null) {
            $scope = $this->defaultScope;
        }

        foreach (explode(',', $scope) as $specificScope) {
            if (! $this->scopes[$specificScope]()) {
                return false;
            }
        }

        return true;
    }
}
