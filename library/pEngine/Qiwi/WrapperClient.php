<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 10.05.11
 * Time: 13:12
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Qiwi_WrapperClient {
    /**
     * @var pEngine_Bootstrap_Qiwi
     */
    protected $resource = null;

    protected function __construct(){
        $this->resource = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('qiwi');
    }
    /**
     * @static
     * @return pEngine_Qiwi_WrapperClient
     */
    public static function factory(){
        return new self();
    }

    /**
     * Get Sender
     * @return IShopServerWSService
     */
    protected function getSender(){
        $options = $this->resource->getOptions();
        return new IShopServerWSService(
            $options['wsdl']['server']['path'],
            array(
                 'location'=> 'https://ishop.qiwi.ru/services/ishop',
                 'trace' => 1,
                 'exceptions'=>true,
                 //'local_cert'=>$options['crt']
                    ));
    }

     /**
     * Создание счет
     * Коды возврата:
     * 0 Успех
     * 13 Сервер занят, повторите запрос позже
     * 150 Ошибка авторизации (неверный логин/пароль)
     * 210 Счет не найден
     * 215 Счет с таким txn-id уже существует
     * 241 Сумма слишком мала
     * 242 Превышена максимальная сумма платежа – 15 000р.
     * 278 Превышение максимального интервала получения  списка счетов
     * 298 Агента не существует в системе
     * 300 Неизвестная ошибка
     * 330 Ошибка шифрования
     * 370 Превышено максимальное кол-во одновременно  выполняемых запросов
     * @param  $id string уникальный идентификатор счета (максимальная длина 30 байт);
     * @param  $user string идентификатор пользователя (номер телефона)
     * @param  $amount string сумма, на которую выставляется счет (разделитель «.»)
     * @param  $lifetime string время действия счета (в формате dd.MM.yyyy HH:mm:ss);
     * @param  $comment string – комментарий к счету, который увидит пользователь (максимальная длина 255 байт);
     * @param  $alarm int отправить оповещение пользователю (1 - уведомление SMS-сообщением, 2 - уведомление  звонком, 0 - не оповещать);
     * @param  $createUser bool – флаг для создания нового пользователя (если он не зарегистрирован в системе).
     * @return int
     */
    public function createBill($id,$user,$amount,$lifetime,$comment='',$alarm=0,$createUser = true){
        $options = $this->resource->getOptions();

        $params = new createBill();

        $params->login = $options['login'];
        $params->password = $options['password'];

        $params->txn = $id;
        $params->user = $user;
        $params->amount = $amount;
        $params->lifetime = $lifetime;
        $params->comment = $comment;
        $params->alarm = $alarm;
        $params->create = $createUser;
        $this->resource->setEvent('createBill',$params);
        $result = $this->getSender()->createBill($params);
        $this->resource->setEvent('createBillRequest',$result);

        return $result->createBillResult;
    }
    /**
     * Отмена счета
     * @param  $id string уникальный идентификатор счета (максимальная длина 30 байт).
     * @return int
     */
    public function cancelBill($id){
        $options = $this->resource->getOptions();

        $params = new cancelBill();
        $params->login = $options['login'];
        $params->password = $options['password'];

        $params->txn = $id;
        $this->resource->setEvent('cancelBill',$params);
        $result = $this->getSender()->cancelBill($params);
        $this->resource->setEvent('cancelBillRequest',$result);
        return $result->cancelBillResult;
    }
    /**
     * Проверка состояния счета
     * статус счетов
     * 50 Выставлен
     * 52 Проводится
     * 60 Оплачен
     * 150 Отменен (ошибка на терминале)
     * 151 Отменен (ошибка авторизации: недостаточно средств на балансе, отклонен абонентом при
     * оплате с лицевого счета оператора сотовой связи и т.п.).
     * 160 Отменен
     * 161 Отменен (Истекло время)
     * @param  $id string уникальный идентификатор счета (максимальная длина 30 байт).
     * @return checkBillResponse
     */
    public function checkBill($id){
        $options = $this->resource->getOptions();

        $params = new checkBill();
        $params->login = $options['login'];
        $params->password = $options['password'];

        $params->txn = $id;
        $this->resource->setEvent('checkBill',$params);
        $result = $this->getSender()->checkBill($params);
        $this->resource->setEvent('checkBillRequest',$result);
        return $result;
    }
}


class checkBill {
  public $login; // string
  public $password; // string
  public $txn; // string
}

class checkBillResponse {
  public $user; // string
  public $amount; // string
  public $date; // string
  public $lifetime; // string
  public $status; // int
}

class getBillList {
  public $login; // string
  public $password; // string
  public $dateFrom; // string
  public $dateTo; // string
  public $status; // int
}

class getBillListResponse {
  public $txns; // string
  public $count; // int
}

class cancelBill {
  public $login; // string
  public $password; // string
  public $txn; // string
}

class cancelBillResponse {
  public $cancelBillResult; // int
}

class createBill {
  public $login; // string
  public $password; // string
  public $user; // string
  public $amount; // string
  public $comment; // string
  public $txn; // string
  public $lifetime; // string
  public $alarm; // int
  public $create; // boolean
}

class createBillResponse {
  public $createBillResult; // int
}


/**
 * IShopServerWSService class
 *
 *
 *
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class IShopServerWSService extends SoapClient {

  const URI = 'http://server.ishop.mw.ru/';

  protected static $classmap = array(
                                    'checkBill' => 'checkBill',
                                    'checkBillResponse' => 'checkBillResponse',
                                    'getBillList' => 'getBillList',
                                    'getBillListResponse' => 'getBillListResponse',
                                    'cancelBill' => 'cancelBill',
                                    'cancelBillResponse' => 'cancelBillResponse',
                                    'createBill' => 'createBill',
                                    'createBillResponse' => 'createBillResponse',
                                   );

  public function IShopServerWSService($wsdl = "IShopServerWS.wsdl", $options = array()) {
    foreach(self::$classmap as $key => $value) {
      if(!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }
    parent::__construct($wsdl, $options);
  }
    
  /**
   *
   *
   * @param checkBill $parameters
   * @return checkBillResponse
   */
  public function checkBill(checkBill $parameters) {
    return $this->__soapCall('checkBill', array($parameters),       array(
            'uri' => self::URI,
            'soapaction' => ''
           )
      );
  }

  /**
   *
   *
   * @param getBillList $parameters
   * @return getBillListResponse
   */
  public function getBillList(getBillList $parameters) {
    return $this->__soapCall('getBillList', array($parameters),       array(
            'uri' => self::URI,
            'soapaction' => ''
           )
      );
  }

  /**
   *
   *
   * @param cancelBill $parameters
   * @return cancelBillResponse
   */
  public function cancelBill(cancelBill $parameters) {
    return $this->__soapCall('cancelBill', array($parameters),       array(
            'uri' => self::URI,
            'soapaction' => ''
           )
      );
  }

  /**
   *
   *
   * @param createBill $parameters
   * @return createBillResponse
   */
  public function createBill(createBill $parameters) {
    return $this->__soapCall('createBill', array($parameters),       array(
            'uri' => self::URI,
            'soapaction' => ''
           )
      );
  }

}
