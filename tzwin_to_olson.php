<?php

//This table maps the Olson Ids (used by Unix) to the more
//concise windows table.
//The source of this table is http://unicode.org/repos/cldr/trunk/docs/design/formatting/zone_log.html#windows_ids
//"S " and "D " have been removed. and Header "idx	OlsonID	WinID	Description" has been added
//The table is returned as a php array

function tzwin_to_olson(){
$tztablestr = "
idx	OlsonID	WinID	Description
1	Etc/GMT+12	Dateline	(GMT-12:00) International Date Line West
2	Pacific/Apia	Samoa	(GMT-11:00) Midway Island, Samoa
3	Pacific/Honolulu	Hawaiian	(GMT-10:00) Hawaii
4	America/Anchorage	Alaskan	(GMT-09:00) Alaska
5	America/Los_Angeles	Pacific	(GMT-08:00) Pacific Time (US & Canada); Tijuana
6	America/Phoenix	US Mountain	(GMT-07:00) Arizona
7	America/Denver	Mountain	(GMT-07:00) Mountain Time (US & Canada)
8	America/Chihuahua	Mexico Standard Time 2	(GMT-07:00) Chihuahua, La Paz, Mazatlan
9	America/Managua	Central America	(GMT-06:00) Central America
10	America/Regina	Canada Central	(GMT-06:00) Saskatchewan
11	America/Mexico_City	Mexico	(GMT-06:00) Guadalajara, Mexico City, Monterrey
12	America/Chicago	Central	(GMT-06:00) Central Time (US & Canada)
13	America/Indianapolis	US Eastern	(GMT-05:00) Indiana (East)
14	America/Bogota	SA Pacific	(GMT-05:00) Bogota, Lima, Quito
15	America/New_York	Eastern	(GMT-05:00) Eastern Time (US & Canada)
16	America/Caracas	SA Western	(GMT-04:00) Caracas, La Paz
17	America/Santiago	Pacific SA	(GMT-04:00) Santiago
18	America/Halifax	Atlantic	(GMT-04:00) Atlantic Time (Canada)
19	America/St_Johns	Newfoundland	(GMT-03:30) Newfoundland
20	America/Buenos_Aires	SA Eastern	(GMT-03:00) Buenos Aires, Georgetown
21	America/Godthab	Greenland	(GMT-03:00) Greenland
22	America/Sao_Paulo	E. South America	(GMT-03:00) Brasilia
23	America/Noronha	Mid-Atlantic	(GMT-02:00) Mid-Atlantic
24	Atlantic/Cape_Verde	Cape Verde	(GMT-01:00) Cape Verde Is.
25	Atlantic/Azores	Azores	(GMT-01:00) Azores
26	Africa/Casablanca	Greenwich	(GMT) Casablanca, Monrovia
27	Europe/London	GMT	(GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London
28	Africa/Lagos	W. Central Africa	(GMT+01:00) West Central Africa
29	Europe/Berlin	W. Europe	(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna
30	Europe/Paris	Romance	(GMT+01:00) Brussels, Copenhagen, Madrid, Paris
31	Europe/Sarajevo	Central European	(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb
32	Europe/Belgrade	Central Europe	(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague
33	Africa/Johannesburg	South Africa	(GMT+02:00) Harare, Pretoria
34	Asia/Jerusalem	Israel	(GMT+02:00) Jerusalem
35	Europe/Istanbul	GTB	(GMT+02:00) Athens, Istanbul, Minsk
36	Europe/Helsinki	FLE	(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius
37	Africa/Cairo	Egypt	(GMT+02:00) Cairo
38	Europe/Bucharest	E. Europe	(GMT+02:00) Bucharest
39	Africa/Nairobi	E. Africa	(GMT+03:00) Nairobi
40	Asia/Riyadh	Arab	(GMT+03:00) Kuwait, Riyadh
41	Europe/Moscow	Russian	(GMT+03:00) Moscow, St. Petersburg, Volgograd
42	Asia/Baghdad	Arabic	(GMT+03:00) Baghdad
43	Asia/Tehran	Iran	(GMT+03:30) Tehran
44	Asia/Muscat	Arabian	(GMT+04:00) Abu Dhabi, Muscat
45	Asia/Tbilisi	Caucasus	(GMT+04:00) Baku, Tbilisi, Yerevan
46	Asia/Kabul	Afghanistan	(GMT+04:30) Kabul
47	Asia/Karachi	West Asia	(GMT+05:00) Islamabad, Karachi, Tashkent
48	Asia/Yekaterinburg	Ekaterinburg	(GMT+05:00) Ekaterinburg
49	Asia/Calcutta	India	(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi
50	Asia/Katmandu	Nepal	(GMT+05:45) Kathmandu
51	Asia/Colombo	Sri Lanka	(GMT+06:00) Sri Jayawardenepura
52	Asia/Dhaka	Central Asia	(GMT+06:00) Astana, Dhaka
53	Asia/Novosibirsk	N. Central Asia	(GMT+06:00) Almaty, Novosibirsk
54	Asia/Rangoon	Myanmar	(GMT+06:30) Rangoon
55	Asia/Bangkok	SE Asia	(GMT+07:00) Bangkok, Hanoi, Jakarta
56	Asia/Krasnoyarsk	North Asia	(GMT+07:00) Krasnoyarsk
57	Australia/Perth	W. Australia	(GMT+08:00) Perth
58	Asia/Taipei	Taipei	(GMT+08:00) Taipei
59	Asia/Singapore	Singapore	(GMT+08:00) Kuala Lumpur, Singapore
60	Asia/Hong_Kong	China	(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi
61	Asia/Irkutsk	North Asia East	(GMT+08:00) Irkutsk, Ulaan Bataar
62	Asia/Tokyo	Tokyo	(GMT+09:00) Osaka, Sapporo, Tokyo
63	Asia/Seoul	Korea	(GMT+09:00) Seoul
64	Asia/Yakutsk	Yakutsk	(GMT+09:00) Yakutsk
65	Australia/Darwin	AUS Central	(GMT+09:30) Darwin
66	Australia/Adelaide	Cen. Australia	(GMT+09:30) Adelaide
67	Pacific/Guam	West Pacific	(GMT+10:00) Guam, Port Moresby
68	Australia/Brisbane	E. Australia	(GMT+10:00) Brisbane
69	Asia/Vladivostok	Vladivostok	(GMT+10:00) Vladivostok
70	Australia/Hobart	Tasmania	(GMT+10:00) Hobart
71	Australia/Sydney	AUS Eastern	(GMT+10:00) Canberra, Melbourne, Sydney
72	Asia/Magadan	Central Pacific	(GMT+11:00) Magadan, Solomon Is., New Caledonia
73	Pacific/Fiji	Fiji	(GMT+12:00) Fiji, Kamchatka, Marshall Is.
74	Pacific/Auckland	New Zealand	(GMT+12:00) Auckland, Wellington
75	Pacific/Tongatapu	Tonga	(GMT+13:00) Nuku'alofa";

$tztable = csv_to_array($tztablestr,"\t");

return $tztable;

}

//This function is for multiline php array read with headers. It came from
//the website:  http://www.php.net/manual/en/function.str-getcsv.php#95132
function csv_to_array($csv, $delimiter = ',', $enclosure = '"', $escape = '\\', $terminator = "\n") { 
    $r = array(); 
    $rows = explode($terminator,trim($csv)); 
    $names = array_shift($rows); 
    $names = str_getcsv($names,$delimiter,$enclosure,$escape); 
    $nc = count($names); 
    foreach ($rows as $row) { 
        if (trim($row)) { 
            $values = str_getcsv($row,$delimiter,$enclosure,$escape); 
            if (!$values) $values = array_fill(0,$nc,null); 
            $r[] = array_combine($names,$values); 
        } 
    } 
    return $r; 
} 

?>