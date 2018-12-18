<?php

namespace User\Controller;

use User\Form\LoginForm;
use User\Service\AuthenticationManager;
use Zend\Authentication\Result;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Uri\Uri;
use Zend\View\Model\ViewModel;


class AuthenticationController extends AbstractActionController
{
    private $entityManager;
    private $userManager;

    /**
     * @var AuthenticationManager
     */
    private $authenticationManager;

    public function __construct($entityManager, $userManager, $authManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->authenticationManager = $authManager;
    }

    public function loginAction()
    {
        $redirectUrl = (string) $this->params()->fromQuery('redirectUrl', '');

        if (\strlen($redirectUrl) > 2048) {
            throw new \Exception("Too long redirectUrl argument passed");
        }

        $form = new LoginForm();
        $form->get('redirect_url')->setValue($redirectUrl);
        $isLoginError = false;

        if ($this->getRequest()->isPost()){
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $result = $this->authenticationManager->login(
                    $data['email'],
                    $data['password'],
                    $data['remember_me']
                );

                // Check result.
                if ($result->getCode() == Result::SUCCESS) {

                    // Get redirect URL.
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');

                    if (!empty($redirectUrl)) {
                        // The below check is to prevent possible redirect attack
                        // (if someone tries to redirect user to another domain).
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost()!=null)
                            throw new \Exception('Incorrect redirect URL: ' . $redirectUrl);
                    }

                    // If redirect URL is provided, redirect the user to that URL;
                    // otherwise redirect to Home page.
                    if(empty($redirectUrl)) {
                        return $this->redirect()->toRoute('home');
                    } else {
                        $this->redirect()->toUrl($redirectUrl);
                    }
                } else {
                    $isLoginError = true;
                }


            } else {
                $isLoginError = true;
            }
        }

        return new ViewModel([
            'form' => $form,
            'isLoginError' => $isLoginError,
            'redirectUrl' => $redirectUrl
        ]);
    }


    public function logoutAction()
    {
        $this->authenticationManager->logout();

        return $this->redirect()->toRoute('login');

    }
}
