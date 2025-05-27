<?php

namespace MVC\core;

class App
{
    public static string $ROOT_DIR;
    protected Request $request;
    protected Response $response;
    protected Router $router;
    public function __construct(string $rootPath)
    {
        self::$ROOT_DIR = $rootPath;
        $this->request  = new Request();
        $this->response = new Response();
        $this->router   = new Router($this->request, $this->response);
    }
    public function run(): void
    {
        try {
            echo $this->router->dispatch();
        }catch (Log $e){
            $this->response->setStatusCode($e->getCode());
            if ($this->request->isApiRequest()) {
                $this->response->json(['error' => $e->getMessage()]);
            }else{
                echo View::renderView('error.index', [
                    'exception' => $e
                ]);
            }
        }
    }
}