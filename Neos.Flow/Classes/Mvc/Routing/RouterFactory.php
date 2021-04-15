<?php
namespace Neos\Flow\Mvc\Routing;

/*
 * This file is part of the Neos.Flow package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\Mvc\Controller\AbstractController;
use Neos\Flow\Mvc\Controller\ControllerInterface;
use Neos\Flow\Mvc\Routing\Exception\InvalidAnnotatedMethodException;
use Neos\Flow\ObjectManagement\Exception\UnknownObjectException;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Reflection\ReflectionService;

/**
 * @Flow\Scope("singleton")
 */
class RouterFactory
{

    /**
     * @Flow\Inject
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function create(): Router
    {
        $annotatedRoutes = self::resolveRouteAnnotatedMethods($this->objectManager);
        $configuredRoutes = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_ROUTES);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return $this->objectManager->get(Router::class, array_merge($annotatedRoutes, $configuredRoutes));
    }

    /**
     * @param ObjectManagerInterface $objectManager
     * @Flow\CompileStatic
     * @return array
     * @throws InvalidAnnotatedMethodException if a none-Action method is annotated
     */
    protected static function resolveRouteAnnotatedMethods(ObjectManagerInterface $objectManager): array
    {
        $annotatedRoutes = [];
        $reflectionService = $objectManager->get(ReflectionService::class);
        $controllerClassNames = $reflectionService->getAllImplementationClassNamesForInterface(ControllerInterface::class);
        foreach ($controllerClassNames as $controllerClassName) {
            if ($reflectionService->isClassAbstract($controllerClassName)) {
                continue;
            }

            $methodNames = $reflectionService->getMethodsAnnotatedWith($controllerClassName, Flow\Route::class);
            foreach ($methodNames as $methodName) {
                if (preg_match('/Action$/', $methodName) === 0 || $reflectionService->isMethodPublic($controllerClassName, $methodName) === false) {
                    throw new InvalidAnnotatedMethodException(sprintf('The method %s->%s is annotated with @Flow\Route. The Route annotation is only meant for *Action methods in your controller', $controllerClassName, $methodName), 1618491181);
                }
                $controllerObjectName = $objectManager->getCaseSensitiveObjectName($controllerClassName);

                if ($controllerObjectName === null) {
                    throw new UnknownObjectException(sprintf('The object "%s" is not registered.', $controllerClassName), 1618384920);
                }
                $controllerPackageKey = $objectManager->getPackageKeyByObjectName($controllerObjectName);
                // FIXME see https://github.com/neos/flow-development-collection/pull/2421/files#r614044814
                $subject = substr($controllerObjectName, strlen($controllerPackageKey) + 1);
                preg_match(
                    '/
                        ^(
                            Controller
                        |
                            (?P<subpackageKey>.+)\\\\Controller
                        )
                        \\\\(?P<controllerName>[a-z\\\\]+)Controller
                        $
                    /ix',
                    $subject,
                    $matches
                );
                $controllerSubpackageKey = ($matches['subpackageKey'] !== '') ? $matches['subpackageKey'] : null;

                $annotation = $reflectionService->getMethodAnnotation($controllerClassName, $methodName, Flow\Route::class);
                if ($annotation === null) {
                    throw new InvalidAnnotatedMethodException(sprintf('Failed to parse @Flow\Route annotation for method %s->%s.', $controllerClassName, $methodName), 1618491174);
                }
                $routeConfiguration = [
                    'name' => $annotation->name ?? sprintf('Annotated Route (%s->%s)', $controllerClassName, $methodName),
                    'uriPattern' => $annotation->uriPattern,
                    'defaults' => [
                        '@package' => $controllerPackageKey,
                        '@subpackage' => $controllerSubpackageKey,
                        '@controller' => $matches['controllerName'],
                        '@action' => substr($methodName, 0, -6),
                        '@format' => $annotation->format
                    ],
                    'httpMethods' => $annotation->httpMethods,
                    'appendExceedingArguments' => $annotation->appendExceedingArguments
                ];
                $annotatedRoutes[] = $routeConfiguration;
            }
        }
        return $annotatedRoutes;
    }

}
