<?php

/**
 * Sreedarsh Bsslider
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Bsslider
 */

class Sreedarsh_Bsslider_Model_System_Dropdown_Values
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '1',
                'label' => 'Yes',
            ),
            array(
                'value' => '0',
                'label' => 'No',
            ),
        );
    }
}