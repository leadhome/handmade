<?php

/**
 * Interface for observers
 * @package pEngine
 * @author Dmitriy Burlutskiy
 */
interface pEngine_Observer_Observer
{
    public function notify(Observer $objSource, $objArguments);
}
?>