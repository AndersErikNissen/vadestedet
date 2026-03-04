<?php 

function sts_get_fields_definition() {
  $company = [
    [
      'group'     => 'company',
      'key'		    => 'name',
      'label'     => 'Navn', 
      'type' 	    => 'text',
      'translate' => false
    ],
    [
      'group'       => 'company',
      'key'		      => 'storefront_image',
      'label'       => 'Facadebillede', 
      'type' 	      => 'text',
      'placeholder' => 'Indsæt medie URL...',
      'translate'   => false
    ],
    [
      'group'       => 'company',
      'key'		      => 'reservation_url',
      'label'       => 'Reservations link', 
      'type' 	      => 'text',
      'placeholder' => 'Indsæt reservations URL...',
      'translate'   => false
    ],
    [
      'group'       => 'company',
      'key'		      => 'amenity',
      'label'       => 'Særlige faciliteter', 
      'type' 	      => 'text',
      'description' => 'Beskriv en særlig ting ved caféen (f.eks. "Brætspilsbibliotek" eller "Spilvejledning"). Dette hjælper Google med at vise din café til de rigtige kunder.'
    ],
    [
      'group'       => 'company',
      'key'		      => 'price_range',
      'label'       => 'Pris niveau', 
      'type' 	      => 'text',
      'placeholder' => 'Brug $, $$, eller $$$...',
      'translate'   => false
    ],
    [
      'group'     => 'company',
      'key'		    => 'telephone',
      'label'     => 'Telefonnummer', 
      'type' 	    => 'text',
      'translate' => false
    ],
    [
      'group'     => 'company',
      'key'		    => 'address',
      'label'     => 'Adresse', 
      'type' 	    => 'text',
      'translate' => false
    ],
    [
      'group'     => 'company',
      'key'		    => 'city',
      'label'     => 'Bynavn', 
      'type' 	    => 'text',
      'translate' => false
    ],
    [
      'group'     => 'company',
      'key'		    => 'postal_code',
      'label'     => 'Postkode', 
      'type' 	    => 'number',
      'translate' => false
    ],
    [
      'group'     => 'company',
      'key'		    => 'region',
      'label'     => 'Kommune', 
      'type' 	    => 'text',
      'translate' => false
    ],
    [
      'group'       => 'company',
      'key'		      => 'logo',
      'label'       => 'Logo', 
      'type' 	      => 'text',
      'placeholder' => 'Indsæt medie URL...',
      'translate'   => false
    ]
  ];

  $contact = [
    [
      'group' 			=> 'contact',
      'key'					=> 'phone',
      'label'				=> 'Telefon nummer', 
      'type'				=> 'text', 
      'description' => 'Vælg dit ønskede format, som f.eks. +45 9999 9999',
      'translate'   => false
    ],
    [
      'group' 			=> 'contact',
      'key'					=> 'email',
      'label' 			=> 'E-mail', 
      'type' 				=> 'email',
      'translate'   => false
    ]
  ];

  $header = [
    [
      'group' 			=> 'header',
      'key'					=> 'logo',
      'label'				=> 'Logo',
      'type'				=>'textarea',
      'placeholder' => 'Indsæt <svg> kode her...',
      'translate'   => false
    ]
  ];

  $footer = [
    [
      'group' 			=> 'footer',
      'key'					=> 'description',
      'label'				=> 'Beskrivelse',
      'type'				=> 'textarea',
      'placeholder' => 'Beskriv virksomheden...'
    ],
    [
      'group' 			=> 'footer',
      'key'					=> 'logo',
      'label'				=> 'Logo',
      'type'				=>'textarea',
      'placeholder' => 'Indsæt <svg> kode her...',
      'translate'   => false
    ]
  ];

  $ui = [
    [
      'group'  => 'ui',
      'key'		 => 'buttons',
      'label'	 => 'Knapper',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'back',
          'label' => 'Tilbage',
          'type'  => 'text',
        ],
        [
          'key'   => 'back_to_archive',
          'label' => 'Tilbage til',
          'type'  => 'text',
        ]
      ]
    ]
  ];

  $archive = [
    [
      'group'  => 'archive',
      'key'		 => 'event',
      'label'	 => 'Event',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'heading',
          'label' => 'Overskrift',
          'type'  => 'text',
        ],
        [
          'key'   => 'description',
          'label' => 'Beskrivelse',
          'type'  => 'textarea',
        ]
      ]
    ],
    [
      'group'  => 'archive',
      'key'		 => 'post',
      'label'	 => 'Indlæg',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'heading',
          'label' => 'Overskrift',
          'type'  => 'text',
        ],
        [
          'key'   => 'description',
          'label' => 'Beskrivelse',
          'type'  => 'textarea',
        ]
      ]
    ]
  ];

  $hours = [
    [
      'group'  => 'hours',
      'key'		 => 'monday',
      'label'	 => 'Mandag',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'open',
          'label' => 'Åbeningstidspunkt',
          'type'  => 'time',
        ],
        [
          'key'   => 'close',
          'label' => 'Lukketidspunkt',
          'type'  => 'time',
        ]
      ],
      'translate'   => false
    ],
    [
      'group'  => 'hours',
      'key'		 => 'tuesday',
      'label'	 => 'Tirsdag',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'open',
          'label' => 'Åbeningstidspunkt',
          'type'  => 'time',
        ],
        [
          'key'   => 'close',
          'label' => 'Lukketidspunkt',
          'type'  => 'time',
        ]
      ],
      'translate'   => false
    ],
    [
      'group'  => 'hours',
      'key'		 => 'wednesday',
      'label'	 => 'Onsdag',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'open',
          'label' => 'Åbeningstidspunkt',
          'type'  => 'time',
        ],
        [
          'key'   => 'close',
          'label' => 'Lukketidspunkt',
          'type'  => 'time',
        ]
      ],
      'translate'   => false
    ],
    [
      'group'  => 'hours',
      'key'		 => 'thursday',
      'label'	 => 'Torsdag',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'open',
          'label' => 'Åbeningstidspunkt',
          'type'  => 'time',
        ],
        [
          'key'   => 'close',
          'label' => 'Lukketidspunkt',
          'type'  => 'time',
        ]
      ],
      'translate'   => false
    ],
    [
      'group'  => 'hours',
      'key'		 => 'friday',
      'label'	 => 'Fredag',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'open',
          'label' => 'Åbeningstidspunkt',
          'type'  => 'time',
        ],
        [
          'key'   => 'close',
          'label' => 'Lukketidspunkt',
          'type'  => 'time',
        ]
      ],
      'translate'   => false
    ],
    [
      'group'  => 'hours',
      'key'		 => 'saturday',
      'label'	 => 'Lørdag',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'open',
          'label' => 'Åbeningstidspunkt',
          'type'  => 'time',
        ],
        [
          'key'   => 'close',
          'label' => 'Lukketidspunkt',
          'type'  => 'time',
        ]
      ],
      'translate'   => false
    ],
    [
      'group'  => 'hours',
      'key'		 => 'sunday',
      'label'	 => 'Søndag',
      'type'	 => 'group',
      'fields' => [
        [
          'key'   => 'open',
          'label' => 'Åbeningstidspunkt',
          'type'  => 'time',
        ],
        [
          'key'   => 'close',
          'label' => 'Lukketidspunkt',
          'type'  => 'time',
        ]
      ],
      'translate'   => false
    ],
    [
      'group'  => 'hours',
      'key'    => 'special_hours',
      'label'  => 'Specielle åbningstider',
      'type'   => 'repeater',
      'fields' => [
        [
          'key'   => 'description',
          'label' => 'Beskrivelse',
          'type'  => 'text'
        ],
        [
          'key'   => 'date',
          'label' => 'Dato',
          'type'  => 'date',
          'translate'   => false
        ],
        [
          'key'   => 'open',
          'label' => 'Åbeningstidspunkt',
          'type'  => 'time',
          'translate'   => false
        ],
        [
          'key'   => 'close',
          'label' => 'Lukketidspunkt',
          'type'  => 'time',
          'translate'   => false
        ]
      ],
    ]
  ];

  $inject = [
    [
      'group'       => 'inject',
      'key'         => 'head',
      'label'       => 'Head scripts',
      'type'        => 'textarea',
      'placeholder' => 'Indsæt <script> eller <meta> tags...',
      'translate'   => false,
    ],
    [
      'group'       => 'inject',
      'key'         => 'body',
      'label'       => 'Body scripts',
      'type'        => 'textarea',
      'placeholder' => 'Indsæt <script> tags...',
      'translate'   => false,
    ],
  ];

  return array_merge( $company, $contact, $hours, $header, $footer, $ui, $archive, $inject );
}