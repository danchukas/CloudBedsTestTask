<?php

namespace App;

use App\Action\ActionInterface;
use App\Action\AddInterval;
use App\Action\DeleteInterval;
use App\Action\GetFullPriceList;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Application
{
    public function run(): void
    {
        $containerBuilder = $this->buildContainerBuilder();

        $routes = $this->createRoutes();
        $request = Request::createFromGlobals();
        $response = $this->getResponse($routes, $request, $containerBuilder);

        $response->prepare($request);
        $response->send();
    }

    private function buildContainerBuilder(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.yml');

        $containerBuilder->autowire(Database::class, Database::class)
            ->addArgument('cloudbeds_test_task_db')
            ->addArgument('tester')
            ->addArgument('tester')
            ->addArgument('cloudbeds_test_task');

        $containerBuilder->compile();

        return $containerBuilder;
    }

    private function createRoutes(): RouteCollection
    {
        $routes = new RouteCollection();
        $routes->add('addInterval', $this->createAddIntervalRoute());
        $routes->add('getFullPriceList', $this->createGetFullPriceListRoute());
        $routes->add('deleteIntervals', $this->createDeleteIntervalRoute());

        return $routes;
    }

    private function createAddIntervalRoute(): Route
    {
        $route = new Route('/addInterval', [
            '_controller' => AddInterval::class
        ]);
        $route->setMethods(Request::METHOD_POST);

        return $route;
    }

    private function createGetFullPriceListRoute(): Route
    {
        $route = new Route('/getFullPriceList', [
            '_controller' => GetFullPriceList::class
        ]);
        $route->setMethods(Request::METHOD_GET);

        return $route;
    }

    private function createDeleteIntervalRoute(): Route
    {
        $route = new Route('/deleteInterval', [
            '_controller' => DeleteInterval::class
        ]);
        $route->setMethods(Request::METHOD_POST);

        return $route;
    }

    private function getResponse(RouteCollection $routes, Request $request, ContainerBuilder $containerBuilder): Response
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        $action = $this->findAction($routes, $context, $containerBuilder);

        $response = $action->makeResponse($request);

        $response->headers->add(['Access-Control-Allow-Origin'=>'*']);

        return $response;
    }

    private function findAction(
        RouteCollection $routes,
        RequestContext $context,
        ContainerBuilder $containerBuilder
    ): ActionInterface {
        $matcher = new UrlMatcher($routes, $context);

        // Find the current route
        $parameters = $matcher->match($context->getPathInfo());

        /** @var ActionInterface $action */
        $action = $containerBuilder->get($parameters['_controller']);

        return $action;
    }
}