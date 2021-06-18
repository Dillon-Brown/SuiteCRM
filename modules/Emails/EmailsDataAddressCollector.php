<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2021 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * Class EmailsDataAddressCollector
 * @author gyula
 */
class EmailsDataAddressCollector
{
    const ERR_INVALID_INBOUND_EMAIL_TYPE = 201;

    /**
     *
     * @var User
     */
    protected $currentUser;

    /**
     *
     * @var array
     */
    protected $sugarConfig;

    /**
     *
     * @var OutboundEmail
     */
    protected $oe;

    // ------------------ FROM DATA STRUCT -------------------

    /**
     *
     * @var string
     */
    protected $replyTo;

    /**
     *
     * @var string
     */
    protected $fromAddr;

    /**
     *
     * @var string
     */
    protected $fromName;

    /**
     *
     * @var string
     */
    protected $oeId;

    /**
     *
     * @var string
     */
    protected $oeName;

    // -------------------------------------------------------

    /**
     * EmailsDataAddressCollector constructor.
     * @param User $currentUser
     * @param array $sugarConfig
     */
    public function __construct(User $currentUser, $sugarConfig)
    {
        $this->currentUser = $currentUser;
        $this->sugarConfig = $sugarConfig;
    }

    /**
     * @param array|InboundEmail[] $ieAccounts
     * @param array $showFolders
     * @param bool $prependSignature
     * @param array $emailSignatures
     * @param array $defaultEmailSignature
     * @return array
     */
    public function collectDataAddressesFromIEAccounts(
        array $ieAccounts,
        array $showFolders,
        $prependSignature,
        array $emailSignatures,
        array $defaultEmailSignature
    ) {
        $dataAddresses = [];
        foreach ($ieAccounts as $inboundEmail) {
            $this->validateInboundEmail($inboundEmail);

            /** @noinspection PhpRedundantOptionalArgumentInspection */
            if (in_array($inboundEmail->id, $showFolders, false)) {
                $isGroupEmailAccount = $inboundEmail->isGroupEmailAccount();
                $isPersonalEmailAccount = $inboundEmail->isPersonalEmailAccount();
                $storedOptions = $inboundEmail->getStoredOptions();

                $this->retrieveFromDataStruct($storedOptions);
                $this->retrieveOutBoundEmail($this->oeId);

                $dataAddress = $this->getDataAddressFromIEAccounts(
                    $inboundEmail,
                    $storedOptions,
                    $prependSignature,
                    $isPersonalEmailAccount,
                    $isGroupEmailAccount,
                    $emailSignatures,
                    $defaultEmailSignature
                );

                $dataAddresses[] = $dataAddress;
            }
        }

        return $this->fillDataAddress($dataAddresses, $defaultEmailSignature, $prependSignature);
    }

    /**
     * @param InboundEmail|null $inboundEmail
     * @throws InvalidArgumentException
     */
    protected function validateInboundEmail($inboundEmail = null)
    {
        if (!$inboundEmail instanceof InboundEmail) {
            throw new InvalidArgumentException(
                'Inbound Email Account should be a valid Inbound Email. ' . gettype($inboundEmail) . ' given.',
                self::ERR_INVALID_INBOUND_EMAIL_TYPE
            );
        }
    }

    /**
     *
     * @param OutboundEmail|null $oe
     */
    protected function setOe($oe)
    {
        $this->oe = $oe;
    }

    /**
     * @param array $storedOptions
     */
    protected function retrieveFromDataStruct(array $storedOptions)
    {
        $this->replyTo = utf8_encode($storedOptions['reply_to_addr']);
        $this->fromName = utf8_encode($storedOptions['from_name']);
        $this->fromAddr = utf8_encode($storedOptions['from_addr']);
        $this->oeId = $storedOptions['outbound_email'];
    }

    /**
     *
     * @param InboundEmail $inboundEmail
     * @param array $storedOptions
     * @param string $prependSignature
     * @param bool $isPersonalEmailAccount
     * @param bool $isGroupEmailAccount
     * @param array $emailSignatures
     * @param array $defaultEmailSignature
     * @return array
     */
    protected function getDataAddressFromIEAccounts(
        InboundEmail $inboundEmail,
        array $storedOptions,
        $prependSignature,
        $isPersonalEmailAccount,
        $isGroupEmailAccount,
        array $emailSignatures,
        array $defaultEmailSignature
    ) {
        $dataAddress = $this->getDataAddressArrayFromIEAccounts(
            $inboundEmail,
            $storedOptions,
            $prependSignature,
            $isPersonalEmailAccount,
            $isGroupEmailAccount
        );

        $emailSignatureId = $this->getEmailSignatureId($emailSignatures, $inboundEmail);
        $signature = $this->currentUser->getSignature($emailSignatureId);

        if (!$signature) {
            if ($defaultEmailSignature['no_default_available'] === true) {
                $dataAddress['emailSignatures'] = $defaultEmailSignature;
            } else {
                $dataAddress['emailSignatures'] = [
                    'html' => $defaultEmailSignature['signature_html'],
                    'plain' => $defaultEmailSignature['signature'],
                ];
            }
        } else {
            $dataAddress['emailSignatures'] = [
                'html' => $signature['signature_html'],
                'plain' => $signature['signature'],
            ];
        }

        return $dataAddress;
    }


    /**
     *
     * @param InboundEmail $inboundEmail
     * @param array $storedOptions
     * @param string $prependSignature
     * @param bool $isPersonalEmailAccount
     * @param bool $isGroupEmailAccount
     * @return array
     */
    protected function getDataAddressArrayFromIEAccounts(
        InboundEmail $inboundEmail,
        array $storedOptions,
        $prependSignature,
        $isPersonalEmailAccount,
        $isGroupEmailAccount
    ) {
        return (new EmailsDataAddress())->getDataArray(
            $inboundEmail->module_name,
            $inboundEmail->id,
            $storedOptions['reply_to_addr'],
            $storedOptions['from_addr'],
            $storedOptions['from_name'],
            null,
            $prependSignature,
            $isPersonalEmailAccount,
            $isGroupEmailAccount,
            $this->getOeId(),
            $this->getOeName(),
            []
        );
    }

    /**
     *
     * @return string
     */
    protected function getOeId()
    {
        return $this->oeId;
    }

    /**
     *
     * @return string
     */
    protected function getOeName()
    {
        return $this->oeName;
    }

    /**
     * @param array $emailSignatures
     * @param InboundEmail $inboundEmail
     * @return string
     */
    protected function getEmailSignatureId(array $emailSignatures, InboundEmail $inboundEmail)
    {
        $emailSignatureID = $emailSignatures[$inboundEmail->id];
        if (!empty($emailSignatureID) && is_string($emailSignatureID)) {
            return $emailSignatureID;
        }

        return '';
    }

    /**
     * @param array $dataAddresses
     * @param array $defaultEmailSignature
     * @param string $prependSignature
     * @return array
     */
    protected function fillDataAddress(array $dataAddresses, array $defaultEmailSignature, $prependSignature)
    {
        $dataAddressesWithUserAddresses = $this->fillDataAddressFromUserAddresses(
            $dataAddresses,
            $defaultEmailSignature,
            $prependSignature
        );
        $dataAddressesWithUserAddressesAndSystem = $this->fillDataAddressWithSystemMailerSettings(
            $dataAddressesWithUserAddresses,
            $defaultEmailSignature
        );

        return $this->fillDataAddressFromPersonal($dataAddressesWithUserAddressesAndSystem);
    }

    /**
     * @param array $dataAddresses
     * @param array $defaultEmailSignature
     * @param string $prependSignature
     * @return array
     */
    protected function fillDataAddressFromUserAddresses(
        array $dataAddresses,
        array $defaultEmailSignature,
        $prependSignature
    ) {
        if (!empty($this->sugarConfig['email_allow_send_as_user'])) {
            $userAddressesArr = (new SugarEmailAddress())->getAddressesByGUID($this->currentUser->id, 'Users');
            $dataAddresses = $this->collectDataAddressesFromUserAddresses(
                $dataAddresses,
                $userAddressesArr,
                $defaultEmailSignature,
                $prependSignature
            );
        }

        return $dataAddresses;
    }

    /**
     * @param array $dataAddresses
     * @param array $userAddressesArr
     * @param array $defaultEmailSignature
     * @param string $prependSignature
     * @return array
     */
    protected function collectDataAddressesFromUserAddresses(
        array $dataAddresses,
        $userAddressesArr,
        array $defaultEmailSignature,
        $prependSignature
    ) {
        foreach ($userAddressesArr as $userAddress) {
            if (!empty($userAddress['reply_to_addr'])) {
                LoggerManager::getLogger()->error('EmailController::action_getFromFields() is Panicking: Reply-To address is not filled.');
            }
            $fromString = $this->getFromString($userAddress);

            $dataAddresses[] = $this->getCollectDataAddressArrayFromUserAddresses(
                $userAddress,
                $fromString,
                $prependSignature,
                $defaultEmailSignature
            );
        }

        return $dataAddresses;
    }

    /**
     *
     * @param array $userAddress
     * @return string
     */
    protected function getFromString($userAddress)
    {
        if (isset($userAddress['reply_to_addr']) && $userAddress['reply_to_addr'] === '1') {
            $fromString = $this->currentUser->full_name . ' &lt;' . $userAddress['email_address'] . '&gt;';
        } else {
            $fromString = $this->currentUser->full_name . ' &lt;' . $this->currentUser->email1 . '&gt;';
        }

        return $fromString;
    }

    /**
     * @param $email
     * @return string
     */
    protected function addCurrentUserToEmailString($email)
    {
        return $this->currentUser->full_name . ' &lt;' . $email . '&gt;';
    }

    /**
     * @param array $userAddress
     * @param string $fromString
     * @param string $prependSignature
     * @param array $defaultEmailSignature
     * @return array
     */
    protected function getCollectDataAddressArrayFromUserAddresses(
        $userAddress,
        $fromString,
        $prependSignature,
        array $defaultEmailSignature
    ) {
        return (new EmailsDataAddress())->getDataArray(
            'personal',
            $userAddress['email_address_id'],
            $this->currentUser->full_name . ' &lt;' . $userAddress['email_address'] . '&gt;',
            $fromString,
            $this->currentUser->full_name,
            null,
            $prependSignature,
            true,
            false,
            null,
            null,
            $defaultEmailSignature
        );
    }

    /**
     * @param array $dataAddresses
     * @param array $defaultEmailSignature
     * @return array
     */
    protected function fillDataAddressWithSystemMailerSettings(array $dataAddresses, array $defaultEmailSignature)
    {
        $this->setOe(new OutboundEmail());
        if ($this->getOe()->isAllowUserAccessToSystemDefaultOutbound()) {
            $system = $this->getOe()->getSystemMailerSettings();
            $dataAddresses[] = $this->getFillDataAddressArray(
                $system->id,
                $system->name,
                $system->smtp_from_name,
                $system->smtp_from_addr,
                $system->mail_smtpuser,
                $defaultEmailSignature
            );
        }

        return $dataAddresses;
    }

    /**
     * @param array $dataAddresses
     * @return array
     */
    protected function fillDataAddressFromPersonal(array $dataAddresses)
    {
        foreach ($dataAddresses as $address => $userAddress) {
            if ($userAddress['type'] !== 'system' && $userAddress['type'] !== 'personal') {
                $emailInfo = $userAddress['attributes'];
                $fromString = $this->addCurrentUserToEmailString($emailInfo['from']);
                $replyString = $this->addCurrentUserToEmailString($emailInfo['reply_to']);

                $dataAddresses[$address]['attributes'] = [
                    'from' => $fromString,
                    'name' => $userAddress['attributes']['name'],
                    'oe' => $userAddress['attributes']['oe'],
                    'reply_to' => $replyString
                ];
            }
        }

        return $dataAddresses;
    }

    /**
     *
     * @return OutboundEmail
     */
    protected function getOe()
    {
        return $this->oe;
    }


    /**
     *
     * @param string $id
     * @param string $name
     * @param string $fromName
     * @param string $fromAddr
     * @param string $mailUser
     * @param array $defaultEmailSignature
     * @return array
     */
    protected function getFillDataAddressArray(
        $id,
        $name,
        $fromName,
        $fromAddr,
        $mailUser,
        array $defaultEmailSignature
    ) {
        return (new EmailsDataAddress())->getDataArray(
            'system',
            $id,
            "$fromName &lt;$fromAddr&gt;",
            "$fromName &lt;$fromAddr&gt;",
            $fromName,
            false,
            false,
            true,
            $id,
            $name,
            $mailUser,
            $defaultEmailSignature
        );
    }

    /**
     * @param string $outboundEmailID
     */
    protected function retrieveOutboundEmail($outboundEmailID)
    {
        $this->oe = (new OutboundEmail())->retrieve($outboundEmailID);
        $this->oeId = $this->oe->id;
        $this->oeName = $this->oe->name;
    }
}
