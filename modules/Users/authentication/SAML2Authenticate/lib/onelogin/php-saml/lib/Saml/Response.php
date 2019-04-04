<?php

class OneLogin_Saml_Response extends OneLogin_Saml2_Response
{
    /**
     * Constructor that process the SAML Response,
     * Internally initializes an SP SAML instance
     * and an OneLogin_Saml2_Response.
     *
     * @param array|object $oldSettings Settings
     * @param string $assertion SAML Response
     * @throws Exception
     */
    public function __construct($oldSettings, $assertion)
    {
        $auth = new OneLogin_Saml2_Auth($oldSettings);
        $settings = $auth->getSettings();
        parent::__construct($settings, $assertion);
    }

    /**
     * Retrieves an Array with the logged user data.
     *
     * @return array
     * @throws OneLogin_Saml2_ValidationError
     */
    public function get_saml_attributes()
    {
        return $this->getAttributes();
    }

    /**
     * Retrieves the nameId
     *
     * @return string
     * @throws OneLogin_Saml2_ValidationError
     */
    public function get_nameid()
    {
        return $this->getNameId();
    }
}
