<?php

return array(

    // Turning debug on will cause detailed exceptions to be emitted.
    'debug'       => true,
    // If you want to use a proxy (e.g. 'localhost:8888')
    'proxy'       => false, #'localhost:8888',

    // Evernote
    'evernote.sandbox'  => true,

    // DT Tropo, placeholders for now ;)
    'tropo.endpoint'    => 'https://tropo.developergarden.com/api/',
    // Daans Token
#    'tropo.textToken'   => '5873626658576e52546a794d5849666b52444b626f484259536a646d5a51416b51474c42657869516f417a6d',
    // Armins Token - alt (probably limit reached?)
#    'tropo.textToken'   => '6f66794b46416e614373684b6f5647635858466e43475273706a5057456a664a426166697a41706241666d41',
    'tropo.textToken'   => '6146414a59674e6975666f616f6376564b4668686d584b4c416447597961645a644d6a454c697a4846774e54',

    // Yummly
    'yummly.endpoint'   => 'http://api.yummly.com/v1/api/',
    'yummly.id'         => 'bb590595',
    'yummly.key'        => '16661ed17dcef5c4392f7aa2e5318725',
    'yummly.maxResult'  => 5,

);
