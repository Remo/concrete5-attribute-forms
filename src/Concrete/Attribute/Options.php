<?php

namespace Concrete\Package\AttributeForms\Attribute;

class Options
{

    /**
     *  Get Attribute type options by ID
     * @param int $atID
     * @return array
     */
    public static function getByAttributeTypeID($atID)
    {
        $at = \Concrete\Core\Attribute\Type::getByID($atID);
        if (is_object($at)) {
            return static::getByAttributeTypeHandle($at->getAttributeTypeHandle());
        }
        return array();
    }

    /**
     *  Get Attribute type options by Handle
     * @param string $atHandle
     * @return array
     */
    public static function getByAttributeTypeHandle($atHandle)
    {
        $options = static::get();
        if (isset($options[$atHandle])) {
            return $options[$atHandle];
        }
        return array();
    }

    /**
     * Get all available options grouped by atHandle
     * @return array
     */
    public static function get()
    {
        return array(
            'email' => array(
                'send_notification_from' => array(
                    'text' => t('Reply to this email address'),
                    'unique' => true
                ),
            ),
            'text' => array(
                'message_subject' => array(
                    'text' => t('Use as email subject'),
                    'unique' => false // Allow combined values
                )
            )
        );
    }
}