<?php


namespace Excellence\Pagespeed\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const API_URL = 'https://gtmetrix.com/api/0.1';
    const XML_PAGESPEED_USERNAME = 'excellence_pagespeed/general/username';
    const XML_PAGESPEED_KEY = 'excellence_pagespeed/general/key';
    const PAGESPEED_ENABLE = 'excellence_pagespeed/general/enabled';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var Integer
     */
    protected $pageSpeedId;
    /**
     * @var Array
     */
    protected $result = [];
    /**
     * @var \Excellence\Pagespeed\Model\PagespeedFactory
     */
    protected $_pageSpeed;
    /**
     * @var String
     */
    protected $error;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Excellence\Pagespeed\Model\PagespeedFactory $pageSpeed
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Excellence\Pagespeed\Model\PagespeedFactory $pageSpeed,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

    ) {
        $this->messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        $this->_pageSpeed = $pageSpeed;
        $this->_date = $date;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context);
    }
    public function getConfigVal($val) {
        return $this->scopeConfig->getValue(
            $val,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }
    public function getSpeedData(){
        $pageSpeedInfo['gtmetrix_id'] = $this->apiUrl(array(
            'url' => $this->getBaseurl()
        ));
        $this->get_results();
     return $results = $this->results();
    }
    public function getCurrentTime(){
        return $this->_date->gmtDate("Y-m-d");
    }
    public function formatDate($date){
        return $this->_date->gmtDate("Y-m-d", $date);   
    }
    public function getTimeDiff($date){
        $currentDate = $this->getCurrentTime();
        if(($currentDate > $date) && $date){
            return true;
        }
        return false;
    }
    public function getBaseurl(){
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }
    // Fetch Last two Updated Data
    public function getLastTwoCollection(){
        $post = $this->_pageSpeed->create();
        $collections = $post->getCollection();
        $countData = count($collections);
        if($this->getConfigVal(self::PAGESPEED_ENABLE)){
            if($countData > 1){
                $collectionData = $collections->setOrder('id','DESC')->setPageSize(2)->getData();
                $countInfo = count($collectionData);
                $oldRecord= $collectionData[$countInfo - 1]['pagespeed_score'];
                $newRecord = $collectionData[$countInfo - 2]['pagespeed_score'];
                if($newRecord > $oldRecord){
                    return __('Page Speed Record Increased to %1%', $newRecord);
                }elseif($newRecord < $oldRecord){
                    return __('Page Speed Record Decrease to %1%', $newRecord);
                }else{
                    return false;
                }
            }elseif($countData == 1){
                $newCollection = $collections->getData();
                $newRecord = $newCollection[0]['pagespeed_score'];
                return __('New Page Speed Record is Set to %1%', $newRecord);
            }
        }
    }
    public function getSpeedInfo($limit){
        $post = $this->_pageSpeed->create();
        // print_r($post->getData()); die;
        $collections = $post->getCollection();
        if($collections->getData() && $this->getConfigVal(self::PAGESPEED_ENABLE)){
            $data = $collections->setOrder('id','DESC')->setPageSize($limit)->getData();
            return $data; 
        }
        return false;
    }
    public function getPageSpeedCollection(){
        $post = $this->_pageSpeed->create();
        return $post->getCollection()->getLastItem()->getData();
    }
    public function sendNotification($templateId){
        try {
            $storeName = $this->getStoreName();
            $storeEmail = $this->getStoreEmail();
            $email = $storeEmail;
            $from = array('email' => (string) $storeEmail, 'name' => (string) $storeName);
            $this->inlineTranslation->suspend();
            $toName = $storeName;
            $toMail = $storeEmail;
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'subject' => 'Welcome'
                ])
                ->setFrom($from)
                ->addTo($toMail, $toName)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();

            return $this;

        } catch (Exception $ex) {
            echo $ex->getMessage();
        }  
    }
    public function getStoreName()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * query()
     *
     * Makes curl connection to API
     *
     * $command string                          command to send
     * $req     string  GET|POST|DELETE         request to send API
     * $params  array                           POST data if request is POST
     *
     * returns raw http data (JSON object in most API cases) on success, false otherwise
     */
    protected function query( $command, $req = 'GET', $params = '' ) {
        $ch = curl_init();

        if ( substr( $command, 0, strlen( self::API_URL ) - 1 ) == self::API_URL ) {
            $URL = $command;
        } else {
            $URL = self::API_URL . '/' . $command;
        }

        curl_setopt( $ch, CURLOPT_URL, $URL );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
        curl_setopt( $ch, CURLOPT_USERPWD, $this->getConfigVal(self::XML_PAGESPEED_USERNAME) . ":" . $this->getConfigVal(self::XML_PAGESPEED_KEY) );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $req );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

        if ( $req == 'POST' )
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );

        $results = curl_exec( $ch );
        if ( $results === false )
            $this->messageManager->addError( __(curl_error( $ch )) );
        curl_close( $ch );

        return $results;
    }

    protected function checkid() {
        if ( empty( $this->pageSpeedId ) ) {
            $this->messageManager->addError( __('No pageSpeedId! Please start a new page speed test') );
            return false;
        }
        return true;
    }

    /**
     * test()
     *
     * Sends new test to GTmetrix API
     *
     * $data    array   array containing parameters to send API
     *
     * returns the pageSpeedId on success, false otherwise;
     */
    public function apiUrl( $data ) {

        if ( empty( $data ) ) {
            $this->messageManager->addError( __('Need to set a param to start a new speed!') );
            return false;
        }

        if ( !isset( $data['url'] ) OR empty( $data['url'] ) ) {
            $this->messageManager->addError( __('No URL found!') );
            return false;
        }

        // check URL
        if ( !preg_match( '@^https?://@', $data['url'] ) ) {
            $this->messageManager->addError( __('Bad URL.') );
            return false;
        }

        if ( !empty( $this->result ) )
            $this->result = array( );

        $data = http_build_query( $data );

        $result = $this->query( 'test', 'POST', $data );

        if ( $result != false ) {
            $result = json_decode( $result, true );
            if ( empty( $result['error'] ) ) {
                $this->pageSpeedId = $result['test_id'];

                if ( isset( $result['state'] ) AND !empty( $result['state'] ) )
                    $this->result = $result;

                return $this->pageSpeedId;
            } else {
                $this->messageManager->addError( __($result['error']) );
            }
        }

        return false;
    }

    /**
     * load()
     *
     * Query an existing test from GTmetrix API
     *
     * $pageSpeedId  string  The existing test's test ID
     *
     * pageSpeedId must be valid, or else all query methods will fail
     */
    public function load( $pageSpeedId ) {
        $this->pageSpeedId = $pageSpeedId;
        if ( !empty( $this->result ) )
            $this->result = array( );
    }

    /**
     * getpageSpeedId()
     *
     * Returns the pageSpeedId, false if pageSpeedId is not set
     */
    public function getpageSpeedId() {
        return ($this->pageSpeedId) ? $this->pageSpeedId : false;
    }

    /**
     * poll_state()
     *
     * polls the state of the test
     *
     * Precondition: member pageSpeedId is not empty
     *
     * The class will save a copy of the state object, 
     * which contains information such as the test results and resource urls (or nothing if an error occured)
     * so that additional queries to the API is not required.
     *
     * returns true on successful poll, or false on network error or no pageSpeedId
     */
    public function poll_state() {
        if ( !$this->checkid() )
            return false;

        if ( !empty( $this->result ) ) {
            if ( $this->result['state'] == "completed" )
                return true;
        }

        $command = "test/" . $this->pageSpeedId;

        $result = $this->query( $command );
        if ( $result != false ) {
            $result = json_decode( $result, true );

            if ( !empty( $result['error'] ) AND !isset( $result['state'] ) ) {
                $this->messageManager->addError( __($result['error']) );
                return false;
            }

            $this->result = $result;
            if ( $result['state'] == 'error' )
                $this->messageManager->addError( __($result['error']) );
            return true;
        }

        return false;
    }
    /**
     * state()
     *
     * Returns the state of the test (queued, started, completed, error)
     *
     * Precondition: member pageSpeedId is not empty
     *
     * returns the state of the test, or false on networking error
     */
    public function state() {
        if ( !$this->checkid() )
            return false;

        if ( empty( $this->result ) )
            return false;

        return $this->result['state'];
    }

    /**
     * completed()
     *
     * returns true if the test is complete, false otherwise
     */
    public function completed() {
        return ($this->state() == 'completed') ? true : false;
    }

    /*
     * get_results()
     *
     * locks and polls API until test results are received
     * waits for 6 seconds before first check, then polls every 2 seconds
     * at the 30 second mark it reduces frequency to 5 seconds
     */

    public function get_results() {
        sleep( 6 );
        $i = 1;
        while ( $this->poll_state() ) {
            if ( $this->state() == 'completed' OR $this->state() == 'error' )
                break;
            sleep( $i++ <= 13 ? 2 : 5  );
        }
    }

    /**
     * results()
     *
     * Get test results
     *
     * returns the test results, or false if the test hasn't completed yet
     */
    public function results() {
        if ( !$this->completed() )
            return false;

        return $this->result['results'];
    }

    /**
     * status()
     *
     * Get account status
     *
     * returns credits remaining, and timestamp of next refill
     */
    public function status() {
        $result = $this->query( 'status' );
        if ( $result != false ) {
            $result = json_decode( $result, true );
            if ( empty( $result['error'] ) ) {
                return $result;
            } else {
                $this->messageManager->addError( __($result['error']) );
            }
        }
        return false;
    }
}