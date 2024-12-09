<?php

return [
    'styles' => [
        'elegant' => [
            [
                'name' => 'email',
                'type' => 'solid',
                'class' => 'h-4 w-4 opacity-70',
                'html' => "
                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='currentColor' class='%class%'>
                        <path d='M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z' />
                        <path d='M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z' />
                    </svg>",
            ],
            [
                'name' => 'camera',
                'type' => 'solid',
                'class' => 'w-6 h-6 opacity-50 text-base-content',
                'html' => "
                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' class='%class%'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z'/>
                        <path d='M15 13a3 3 0 11-6 0 3 3 0 016 0z'/>
                    </svg>",
            ],
            [
                'name' => 'collection-name',
                'type' => 'solid',
                'class' => 'w-12 h-12 opacity-50 text-base-content',
                'html' => "
                     <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' class='%class%'>
                        <path d='M3 3h6v6H3V3zm0 9h6v6H3v-6zm9-9h6v6h-6V3zm0 9h6v6h-6v-6z'></path>
                    </svg>",
            ],
            [
                'name' => 'url',
                'type' => 'solid',
                'class' => 'w-12 h-12 opacity-50 text-base-content',
                'html' => "
                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' class='%class%'>
                        <path d='M10.59 13.41L9.17 12l2.83-2.83a3 3 0 014.24 4.24L14.83 15l1.42 1.41 2.83-2.83a5 5 0 00-7.07-7.07L7.76 10.24a5 5 0 007.07 7.07L13.41 15l-2.82 2.82a3 3 0 01-4.24-4.24l2.82-2.82z'/>
                    </svg>",
            ],
            [
                'name' => 'collection-number',
                'type' => 'material',
                'class' => 'w-6 h-6 opacity-50 material-symbols-outlined text-base-content',
                'html' => "<span class='%class%'>pin</span>",
            ],
            [
                'name' => 'collection-position',
                'type' => 'material-symbols-outlined',
                'class' => 'w-6 h-6 opacity-50 material-symbols-outlined text-base-content',
                'html' => "<span class='%class%'>stacks</span>",
            ],

            [
                'name' => 'egi-base-price',
                'type' => 'material',
                'class' => 'w-6 h-6 opacity-50 material-symbols-outlined text-base-content',
                'html' => "<span class='%class%'>payments</span>",
            ],
        ],

        // [
        //     'name' => 'description',
        //     'type' => 'solid',
        //     'class' => 'icon-class',
        //     'html' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="..."></path></svg>',
        // ],
        // [
        //     'name' => 'type',
        //     'type' => 'solid',
        //     'class' => 'icon-class',
        //     'html' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="..."></path></svg>',
        // ],
        // [
        //     'name' => 'path_image_banner',
        //     'type' => 'solid',
        //     'class' => 'icon-class',
        //     'html' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="..."></path></svg>',
        // ],
        // [
        //     'name' => 'path_image_card',
        //     'type' => 'solid',
        //     'class' => 'icon-class',
        //     'html' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="..."></path></svg>',
        // ],
        // [
        //     'name' => 'path_image_avatar',
        //     'type' => 'solid',
        //     'class' => 'icon-class',
        //     'html' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="..."></path></svg>',
        // ],
        // [
        //     'name' => 'url_collection_site',
        //     'type' => 'solid',
        //     'class' => 'icon-class',
        //     'html' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="..."></path></svg>',
        // ],
    ],
    'default' => 'elegant',
];
