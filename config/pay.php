<?php

return [
    'alipay' => [
        'app_id'         => '2016091700529270',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqT/qM5/z34uKG166k2+urBNA1LJNjpb2fsBjHI+e6dSir6roIJjAMlBIt3/yWIktss1Sz3/8PAGTp9L/teehGKP+2l58ImBttTIAiGiI0BS1//t6EAyNZfYH280ZM6DEHcPLsQKp8ERz3GKfqKft6HvjYiwszRHvIQsFDSLMJqmXEtB99gLnAI2Ao57dsSqL1C+dNcHosP0xKtlIxvL7x0hUf08bu1y1w4fWtmwh65xUAHmZLfT05o0zjILwC22g1e/Vds+g7Y6zUf11Su23tDByQgfqg0xFHuhRAyaCvKeqO+w9MdYPfDbg37DpdGwnwOiVarS9Lc1SBpnitYGo4QIDAQAB',
        'private_key'    => 'MIIEowIBAAKCAQEA2PG22lzrhMXEdxetLER+m8vGBrJupnfAwd4is6LQv1V78Oi+OEeYkqbHVhrE6pujZUcmIfOHodWhfDF6qv91mWTxrQFXY5Hi35NyNFGBY85B3+m+IH1ZX6kjRIUuleEYUNNZazikSx1YjBWocnFybqRP5nmJ5CpQVFUyD972+uK4gw+e/EsV8ndPgMsCl64KNT62Or4wICPoPDOytWjgRPlgqlsc+K6X2adpYyZdcGg9ehWJ5K4iqHrT0cnBiPkm+bBZEAxPlHdzvUSL0gxAQOFvogXRXnmB9PB8k78FZ3+nmZqcHrFXiOBPyre8fbZkfvVG2vX/dslGcyApesFJEQIDAQABAoIBAQCh/nQkwCfZn/ehWhukp4zG9zVqgMihI2DbIu1Up+oqRUHkVVbdHZEvSLwmbpzF9uvez28JARkxXh1UaHoJv/HnVfokzpCbuC7p9ebcbFQq27RS5+5hwlf/V4QPQUfVAR0wFXF63/PGZyZcjDuZIV9qgOVs2yp9Jp+PKYLVWQ+hrFRZtp7lpQo0BF6W0m5nkXISVf0OVx7ZdLEDJQBeEsryCbC+uLSCxiE/iZurhzo+oZdtn+DsNnd8Zb1MGxqnFMxN/Kwy3Jm075GYEJZ0YecZkVwxV89RNR4nMdrNiziUDS0uhATFH8akkZI2L0w/AZKP01YInRqejuakJjw5MZ+FAoGBAOuxrz6Yxo1eZs5l+34IeZGAGMqZv2mqg/hVQ4V+rJfofab0qgyIalB0T+OR5t/6MYiyrNa+zv/HhlzK+qV4dOesf1Ya+kw/TKEzQ8bfO4ejduAfGHpuxr4YOelSMdOjlFwbNN4CstmULlQFuGG3TT7yFzXfF9DbaLONqs73HmRXAoGBAOuifpVmhqcnj5KWIZV81BfuZ0mjKPbITSE3tpwRb6KBpyqk8F8E9Jd3cqfsl5RoGHLRQfxmcZdHevi4Ppk7GKj6exb+y9apGG/pYf/MwaBnAlskrnem+hXpgjn5CqIRT54wvoUkAr2w8H4POHTPKIRr5/WVQGxOQC5beawv2pzXAoGAaMTXLXAj8ntgH6ddn6yMvYy/eG/XklTlzOG53gFtHymNkUV4wZFyEMljKbmVc0J3+lfSVLMEuNYsd9sSh7N/4+vdvpzHXlVU7uMm4aQhhi23jfDEpMfROHb0Zy7OT1GLhVXirj1s2yLvZRIV3/nnMG/UuXGt2H1vkUEMCGv46okCgYANbdJXA2PTLO8CxKmfUmDoCD88tB7Gib3TkdBHbrr1APyc8o98atThuP4A0fwFijUyffiLwO7iV0GL4Tw4EWUjZDsVoWnOjw6EekGKiYnTcWtx9FI5IXwJOaihUy8m82OdOVvyTI4Xb3kVRhrKlW/StC+hEGN+iKshuTbi+Es2OwKBgG0/e/gu+CKle9R47y91Qd89AoZttb8+JaC8u4UNWJBe051sw0rvKXUnK7I1ZsGlZuR+qcTZ6doQ51PO7UNjXfumZKn5dbyeTYPlPBUIV2uhQp4rlX3g3lkK+O26Iik7qjrl72Xq7cJtbkEgWVckj/nOwsXeBnNC8K9yACDp3qZf',
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
