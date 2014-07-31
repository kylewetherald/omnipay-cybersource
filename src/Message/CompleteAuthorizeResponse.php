<?php
namespace Omnipay\Cybersource\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * 
 * @author Rafael Diaz-Tushman
 *
 */
class CompleteAuthorizeResponse extends AbstractResponse
{

    public function isSuccessful()
    {
        return isset($this->data['decision']) && $this->data['decision'] === 'ACCEPT';
    }

    public function getTransactionReference()
    {
        return isset($this->data['transaction_id']) ? $this->data['transaction_id'] : null;
    }
    
    public function getMerchantTransactionReference()
    {
        return isset($this->data['req_reference_number']) ? $this->data['req_reference_number'] : null;
    }    

    public function getMessage()
    {
        return isset($this->data['message']) ? $this->data['message'] : null;
    }
    
    public function validateSignature( $secret_key ) 
    {
        $signed_field_names_string = isset($this->data['signed_field_names']) ? $this->data['signed_field_names'] : null;
        $signed_field_names = explode(",", $signed_field_names_string);
        
        $signed_data = array();
        foreach ($signed_field_names as $field) 
        {
            $signed_data[] = $field . "=" . $this->data[$field];
        }
        
        $our_signature = base64_encode(hash_hmac('sha256', implode(",", $signed_data), $secret_key, true));
        
        if ($our_signature != $this->data['signature']) 
        {
            return false;
        }
        
        return true;
    }
}
