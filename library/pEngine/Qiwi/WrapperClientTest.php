<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yura
 * Date: 11.05.11
 * Time: 12:15
 * To change this template use File | Settings | File Templates.
 */
 
class pEngine_Qiwi_WrapperClientTest extends pEngine_Qiwi_WrapperClient{
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
    public function createBill($id,$user,$amount,$lifetime,$comment='',$alarm=0,$createUser = false){
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
        return 0;
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
        return 0;
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
        $return = new checkBillResponse();
        $return->status = 60;
    }
}