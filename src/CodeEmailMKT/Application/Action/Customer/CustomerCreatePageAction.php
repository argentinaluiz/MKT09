<?php

namespace CodeEmailMKT\Application\Action\Customer;

use CodeEmailMKT\Application\Form\CustomerForm;
use CodeEmailMKT\Domain\Service\FlashMessageInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use CodeEmailMKT\Domain\Persistence\CustomerRepositoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;


class CustomerCreatePageAction
{
    private $template;
    /**
     * @var CustomerRepositoryInterface
     */
    private $repository;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var CustomerForm
     */
    private $form;


    public function __construct(
        CustomerRepositoryInterface $repository,
        TemplateRendererInterface $template,
        RouterInterface $router,
        CustomerForm $form
    ){
        $this->template = $template;
        $this->repository = $repository;
        $this->router = $router;
        $this->form = $form;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {

        if($request->getMethod() == 'POST'){
            $flash = $request->getAttribute('flash');
            $dataRaw = $request->getParsedBody();
            $this->form->setData($dataRaw);
            if($this->form->isValid()){
                $entity = $this->form->getData();
                $this->repository->create($entity);
                $flash->setMessage(FlashMessageInterface::MESSAGE_SUCCESS,'Contato cadastrado com sucesso');
                $uri = $this->router->generateUri('customer.list');
                return new RedirectResponse($uri);
            }
        }
        return new HtmlResponse($this->template->render("app::customer/create",[
            'form' => $this->form
        ]));

    }
}
