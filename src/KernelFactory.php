<?php

namespace Somos;

use DI\Container;
use DI\ContainerBuilder;
use DI\Definition\Source\ArrayDefinitionSource;
use DI\Definition\Source\DefinitionSource;

final class KernelFactory
{
    public static function getInstance()
    {
        return new static();
    }

    public function create($configuration, array $modules = [])
    {
        return $this->getKernelFromContainer(
            $this->createContainer($configuration, $modules)
        );
    }

    /**
     * @param $configuration
     * @param array $modules
     * @return Container
     */
    private function createContainer($configuration, array $modules)
    {
        $builder = new ContainerBuilder();
        $this->addModules($modules, $builder);
        $this->loadConfiguration($configuration, $builder);

        return $builder->build();
    }

    /**
     * @param $container
     * @return mixed
     */
    private function getKernelFromContainer(Container $container)
    {
        return $container->get(Somos::class);
    }

    /**
     * Loads the MessageBus module and any other module defined by the user.
     *
     * A module is a re-usable service definition that can be loaded so to register a series of services. Do note that
     * a library might not need a module service definition because all dependencies can be resolved using Auto-wiring.
     *
     * @param callable[]|array[]|DefinitionSource[]|string[] $modules
     * @param ContainerBuilder $builder
     *
     * @link http://php-di.org/doc/definition.html for more information on definitions and autowiring.
     *
     * @return void
     */
    private function addModules(array $modules, ContainerBuilder $builder)
    {
        $modules[] = new Module\MessageBus();

        foreach ($modules as $module) {
            $this->loadConfiguration($module, $builder);
        }
    }

    /**
     * Adds or replaces the service definitions in the given Container Builder with the provided services and
     * parameters.
     *
     * @param callable|array|DefinitionSource|string $configuration a list of services and parameters to load as an array or
     *    Definition Source, or a filename where to load a list of services from.
     * @param ContainerBuilder $builder
     *
     * @link http://php-di.org/ for more information on the format of the Service Definition.
     *
     * @return void
     */
    private function loadConfiguration($configuration, ContainerBuilder $builder)
    {
        if (is_callable($configuration)) {
            $configuration = $configuration();
        }

        if ($configuration === null) {
            return;
        }

        if (is_array($configuration)) {
            $configuration = new ArrayDefinitionSource($configuration);
        }

        $builder->addDefinitions($configuration);
    }
}
