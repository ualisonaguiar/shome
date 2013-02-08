<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     *
     * @var type
     */
    protected $nom_sistema = 'shome';

    /**
     * @see Zend_Application_Bootstrap_Bootstrap::run()
     * @return void
     */
    public function run()
    {
        return parent::run();
    }

    /**
     * Método de inicialização da sessão.
     *
     * @return void
     */
    protected function _initSession()
    {
        Zend_Session::start();
    }

    /**
     * Método de configuração do Autoloader.
     *
     * @return void
     */
    protected function _initAutoloader()
    {
        ini_set('upload_max_filesize', '10M');

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);
        //Autoloader

        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                'basePath' => APPLICATION_PATH,
                'namespace' => '',
                'resourceTypes' => array(
                    'default' => array(
                        'path' => 'modules/default/controllers/',
                        'namespace' => 'Default',
                    ),
                    'default_models' => array(
                        'path' => 'modules/default/models/',
                        'namespace' => 'PDefault_Models',
                    ),
                    'default_negocio' => array(
                        'path' => 'modules/default/negocio/',
                        'namespace' => 'NDefault',
                    ),
                    'default_forms' => array(
                        'path' => 'modules/default/views/forms/',
                        'namespace' => 'Form',
                    )
                )
            ));
    }

    /**
     * Método de definições de constantes do sistema.
     *
     * @return void
     */
    protected function _initConstants()
    {
        define('__APP__', $_SERVER['DOCUMENT_ROOT'] . '/' . $this->nom_sistema);
        define('END_WEB', 'http://' . $_SERVER['SERVER_NAME'] . '/' . $this->nom_sistema);
        define('EMAIL_ADMINISTRADOR', 'ualison.aguiar@gmail.com');

        define('RECPF', '/[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/');
        define('PATH_FILE', getcwd() . '/files');
    }

    /**
     * Método inicializar para configuração de servidor d email.
     *
     * @return void
     */
    protected function _initMail()
    {

    }

    /**
     * Método que registra os Helpers do Sistema.
     *
     * @return void
     */
    final protected function _initHelpers()
    {
        /** plugins globais * */
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/controllers/helpers');
    }

}

