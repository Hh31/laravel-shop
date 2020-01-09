<?php

return [
    'alipay' => [
        'app_id' => '2016102100732040',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxlCUHPRq3s7Z47sUxhyuIpM7tgx8IaAZlHFW+VSF5erk4l7OFyiZJQ/4PGCM32eDQ2LOWMGsvx4XTIiPNlUDJV8yKD6hBXspPjnn7XfSEOLHJvkZKRc0h8LMG9ysmDW6ILjaSzKKzNCBq2UuByU7u54osOobbO2gn/7IZBAw4Jh66mFWcX1aNYPfpIOKilkI6xo3mds290BPgdnk8Mw1j2/0JIp1LOWm2n3H9b3T0lwgjerMij8Xnv2B8LE1rRd2FM7995bC6rfEzm8SF8bgX/IMcJn2NcHgUsgT4gjwcBz0uWoo2tmZ+CsLXh/p+yBPYXvxdw1NbWBcXZEgIhuhpwIDAQAB',
        'private_key' => 'MIIEowIBAAKCAQEAq4gSfS0ZnNREfhjOg3JSyj0y5fMWveZRVRNQoX0eGdSUHdhjo+9nuZ59itUhXTfQrTS7MAfgg89yqmef4PvvJ/3imJZsEKxuR/eCJeMdm35bVvdS/0hkThE5rUAoqHbiXdGMsj2W8wqMlYWms29e3d11KFOsFiyEyrXnkmvRg9xRVHj6KmP936Styp8hYNsWV001ARCHZTafMoUq7Zg6wP3VQjd+0hLliBy1WbvGT45ESJhoZMIqKRXEISPCr/ZYDNzCOdzoTwOSJpBQcglPr8LaQED/0k1ZNtEorFmQQb/7nJumOxtvxuAHhqhPehFGV3pcrwLCYDmG4VoabPpJ9QIDAQABAoIBAHTfEKHjUbkuU8CKzGJ5SXlsOzq75znaLH5H2788DX9R/N1c637uBoFNQIKQdtwZxH/PFt9B7rJAbQM3+ZIEdc2MyZBnpRezNKyqMi34Udr5O+jEOPOg8l7Sk7a8qTcnUVLVDQUAu9G61VCnMI6/iiam1kV0xSKSkKPBTxfPiIBLQQ1HE0RZ8+SeMh7qWOpH+LcZb4Vfib0q1Isr0avG4IEsORV/bQegU+O99yqwqvqP2D+76hVbHUWD7ZeKVWRcFgzOkeP7XDI2NmPQwD9jW/zPofqlO/0t5kSB6ssTWfwJ/IyBEexeEGSxbHrHzLmzqxfJRXnJ9BFJ/i02V4ffk0ECgYEA/XavMDQzdLml1guviKZZF9XiIyZvLu3Hg8B0W7FXWhvKd3am9dnTjPf96Ih333KVzjCmT2VsOTevpEDnH69lmGJ5O42hOa/uzUlXPTKOkpGs6/XUw9/+QLCAWJUCYbSJh4Cg7dNoY3IjetuZhvSurzax7og15A0tioI2/cMz6CUCgYEArT9/Jv71wL0utzLLHLTiX4+MqYHLqqnkuqXfozx06NT28xS102hvto/7yd9jBeC+cv0Vz2Zr4M5xbfgwrwDa5fIysALQHGmldRxiOKTn6u4ClTPYOZIV1jgUrTpumHCPERE1hbL1NaC9whgAj/MevYUfG4OsilPQkLnPEPR4iZECgYEA2sauwHTOVVpJAlBj2xd6n1BGOQDbxy4GoHpl9R9FvsluIV+RDXRPXcZQq33fukc1dxwW8UEiAOFIAgbjeD30+2tEKVs+ZoStIxTC8FfLKEgFckZmuAuHcl7m6Y7011D1xLgKMD3iRLQoW8tg4VQh06rqOnHn0khtnrh2ruLm0uECgYBvvR1obFbauenQdK011aCPm8lQZouShkMk8uj7onmQ1hJ0k232bUxoBhOSj0aRuvN6vKr9eoSnmN7l24aiyL1mQ8DXbukE+kqh7u2WtR1zk5EM5ERYVVVKNh933tEH9sj/bAcGNXcb4JGjBtIOBfNUrvByUYnOKeLCBWV8/QBlUQKBgFB+Pp68p5NGtLTjVFUHUH4a+lK+DY3op3I2wnJs/6FxFVQf17XQL83Zpvi7ag4Y/xdpEyqGJXenw3f/caqjfe+pHdYgaVBn1IBPOk7CnKLWfsSNOqQSopd8PbeJkBZaTWEJB5G+loFbXbKP5I0CiK+U8E178fONElarLl/opbT0',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
