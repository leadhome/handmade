<?php

class pEngine_Tool_Manifest
    implements Zend_Tool_Framework_Manifest_ProviderManifestable
{
    public function  getProviders() {
        return array(
            new pEngine_Tool_pEngineProvider()
        );
    }
}