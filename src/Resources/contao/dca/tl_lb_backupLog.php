<?php





$GLOBALS['TL_DCA']['tl_lb_backupLog'] = array
(
    
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary'
            )
        )
    ),
    // Fields
    'fields' => array
    (
        
        'id' => array
        (
            'sql'       => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp' => array
        (
            'sql'       => "int(10) NOT NULL DEFAULT CURRENT_TIMESTAMP"
        ),
        'lastBackup' => array
        (
            'sql'       => "varchar(255) NOT NULL"
        ),

    )
    
    
    
);















    
