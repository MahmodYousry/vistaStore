<?php

    function lang($phrase) {

        static $lang = array(

            //Navbar Links

            'HOME_ADMIN'    => 'Home',
            'BRAND'         => 'Brand',
            'ITEMS'         => 'Items',
            'MEMBERS'       => 'Members',
            'COMMENTS'      => 'Comments',
            'STATISTICS'    => 'Statistics',
            'LOGS'          => 'Logs',
            'TYPE'          => 'Types',
            ''  => '',

        );

        return $lang[$phrase];

    }
