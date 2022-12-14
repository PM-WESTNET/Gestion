<?php

$accounts = [
    ["account_id" => 1,"name" => "ACTIVO","is_usable" => 0,"code" => "1","lft" => "1","rgt" => "154","parent_account_id" => null],
    ["account_id" => 2,"name" => "ACTIVO CORRIENTE","is_usable" => 0,"code" => "1.1","lft" => "2","rgt" => "131","parent_account_id" => 1],
    ["account_id" => 3,"name" => "CAJA Y BANCOS","is_usable" => 0,"code" => "1.1.1","lft" => "3","rgt" => "38","parent_account_id" => 2],
    ["account_id" => 4,"name" => "CAJA CHICA","is_usable" => 1,"code" => "1.1.1.1","lft" => "4","rgt" => "5","parent_account_id" => 3],
    ["account_id" => 5,"name" => "CAJA ECOPAGOS","is_usable" => 1,"code" => "1.1.1.2","lft" => "6","rgt" => "7","parent_account_id" => 3],
    ["account_id" => 6,"name" => "CAJA GODOY CRUZ","is_usable" => 1,"code" => "1.1.1.3","lft" => "8","rgt" => "9","parent_account_id" => 3],
    ["account_id" => 7,"name" => "BANCO 108","is_usable" => 1,"code" => "1.1.1.4","lft" => "10","rgt" => "11","parent_account_id" => 3],
    ["account_id" => 8,"name" => "BANCO 104","is_usable" => 1,"code" => "1.1.1.5","lft" => "12","rgt" => "13","parent_account_id" => 3],
    ["account_id" => 9,"name" => "BANCO CREDICOOP GABRIEL C\/C","is_usable" => 1,"code" => "1.1.1.6","lft" => "14","rgt" => "15","parent_account_id" => 3],
    ["account_id" => 10,"name" => "BANCO CREDICOOP MAURICIO C\/C","is_usable" => 1,"code" => "1.1.1.7","lft" => "16","rgt" => "17","parent_account_id" => 3],
    ["account_id" => 11,"name" => "BANCO MACRO C\/C","is_usable" => 1,"code" => "1.1.1.8","lft" => "18","rgt" => "19","parent_account_id" => 3],
    ["account_id" => 12,"name" => "BANCO FRANCES C\/C CHEQUES","is_usable" => 1,"code" => "1.1.1.9","lft" => "20","rgt" => "21","parent_account_id" => 3],
    ["account_id" => 13,"name" => "BANCO FRANCES C\/C EFECTIVO","is_usable" => 1,"code" => "1.1.1.10","lft" => "22","rgt" => "23","parent_account_id" => 3],
    ["account_id" => 14,"name" => "BANCO SUPERVIELLE C\/C","is_usable" => 1,"code" => "1.1.1.11","lft" => "24","rgt" => "25","parent_account_id" => 3],
    ["account_id" => 15,"name" => "BANCO STANDARD BANK C\/C","is_usable" => 1,"code" => "1.1.1.12","lft" => "26","rgt" => "27","parent_account_id" => 3],
    ["account_id" => 16,"name" => "ECOPAGOS","is_usable" => 0,"code" => "1.1.2","lft" => "39","rgt" => "78","parent_account_id" => 2],
    ["account_id" => 17,"name" => "LAVALLE","is_usable" => 1,"code" => "1.1.2.1","lft" => "40","rgt" => "41","parent_account_id" => 16],
    ["account_id" => 18,"name" => "COSTA","is_usable" => 1,"code" => "1.1.2.2","lft" => "42","rgt" => "43","parent_account_id" => 16],
    ["account_id" => 19,"name" => "PLANET EXPRESS","is_usable" => 1,"code" => "1.1.2.3","lft" => "44","rgt" => "45","parent_account_id" => 16],
    ["account_id" => 20,"name" => "PASEO LA BODEGA","is_usable" => 1,"code" => "1.1.2.4","lft" => "46","rgt" => "47","parent_account_id" => 16],
    ["account_id" => 21,"name" => "RAKU","is_usable" => 1,"code" => "1.1.2.5","lft" => "48","rgt" => "49","parent_account_id" => 16],
    ["account_id" => 22,"name" => "CORRALITOS- ONCE","is_usable" => 1,"code" => "1.1.2.6","lft" => "50","rgt" => "51","parent_account_id" => 16],
    ["account_id" => 23,"name" => "CORRALITOS \u2013 RUIZ","is_usable" => 1,"code" => "1.1.2.7","lft" => "52","rgt" => "53","parent_account_id" => 16],
    ["account_id" => 24,"name" => "RUTTINI","is_usable" => 1,"code" => "1.1.2.8","lft" => "54","rgt" => "55","parent_account_id" => 16],
    ["account_id" => 25,"name" => "COMODO","is_usable" => 1,"code" => "1.1.2.9","lft" => "56","rgt" => "57","parent_account_id" => 16],
    ["account_id" => 26,"name" => "PANQUEHUA","is_usable" => 1,"code" => "1.1.2.10","lft" => "58","rgt" => "59","parent_account_id" => 16],
    ["account_id" => 27,"name" => "WALMART","is_usable" => 1,"code" => "1.1.2.11","lft" => "60","rgt" => "61","parent_account_id" => 16],
    ["account_id" => 28,"name" => "PEDRIEL","is_usable" => 1,"code" => "1.1.2.12","lft" => "62","rgt" => "63","parent_account_id" => 16],
    ["account_id" => 29,"name" => "MINIMARKET MAURI","is_usable" => 1,"code" => "1.1.2.13","lft" => "64","rgt" => "65","parent_account_id" => 16],
    ["account_id" => 30,"name" => "COLONIA SEGOVIA- VARO","is_usable" => 1,"code" => "1.1.2.14","lft" => "66","rgt" => "67","parent_account_id" => 16],
    ["account_id" => 31,"name" => "CUENTAS POR COBRAR","is_usable" => 0,"code" => "1.1.3","lft" => "79","rgt" => "86","parent_account_id" => 2],
    ["account_id" => 32,"name" => "DEUDORES POR VENTAS","is_usable" => 1,"code" => "1.1.3.1","lft" => "80","rgt" => "81","parent_account_id" => 31],
    ["account_id" => 33,"name" => "DEUDORES VARIOS","is_usable" => 1,"code" => "1.1.3.2","lft" => "82","rgt" => "83","parent_account_id" => 31],
    ["account_id" => 34,"name" => "DEUDORES MOROSOS","is_usable" => 1,"code" => "1.1.3.3","lft" => "84","rgt" => "85","parent_account_id" => 31],
    ["account_id" => 35,"name" => "DOCUMENTOS A COBRAR","is_usable" => 1,"code" => "1.1.4","lft" => "87","rgt" => "88","parent_account_id" => 2],
    ["account_id" => 36,"name" => "PRESTAMOS OTORGADOS","is_usable" => 1,"code" => "1.1.5","lft" => "89","rgt" => "90","parent_account_id" => 2],
    ["account_id" => 37,"name" => "PREVISION PARA DEUDORES INCOBRABLES","is_usable" => 1,"code" => "1.1.6","lft" => "91","rgt" => "92","parent_account_id" => 2],
    ["account_id" => 38,"name" => "ANTICIPO IMP A LAS GANANCIAS","is_usable" => 1,"code" => "1.1.7","lft" => "93","rgt" => "94","parent_account_id" => 2],
    ["account_id" => 39,"name" => "ANTICIPO A PROVEEDORES","is_usable" => 1,"code" => "1.1.8","lft" => "95","rgt" => "96","parent_account_id" => 2],
    ["account_id" => 40,"name" => "IVA SALDO A FAVOR","is_usable" => 1,"code" => "1.1.9","lft" => "97","rgt" => "98","parent_account_id" => 2],
    ["account_id" => 41,"name" => "IVA CREDITO FISCAL","is_usable" => 1,"code" => "1.1.10","lft" => "99","rgt" => "100","parent_account_id" => 2],
    ["account_id" => 42,"name" => "CUENTA PARTICULAR GABRIEL LAS HERAS","is_usable" => 1,"code" => "1.1.11","lft" => "101","rgt" => "102","parent_account_id" => 2],
    ["account_id" => 43,"name" => "CUENTA PARTICULAR MAURICIO PUERTA","is_usable" => 1,"code" => "1.1.12","lft" => "103","rgt" => "104","parent_account_id" => 2],
    ["account_id" => 44,"name" => "RETENCIONES","is_usable" => 0,"code" => "1.1.13","lft" => "105","rgt" => "114","parent_account_id" => 2],
    ["account_id" => 45,"name" => "RETENCION IVA","is_usable" => 1,"code" => "1.1.13.1","lft" => "106","rgt" => "107","parent_account_id" => 44],
    ["account_id" => 46,"name" => "RETENCION GANANCIAS","is_usable" => 1,"code" => "1.1.13.2","lft" => "108","rgt" => "109","parent_account_id" => 44],
    ["account_id" => 47,"name" => "RETENCION IIBB","is_usable" => 1,"code" => "1.1.13.3","lft" => "110","rgt" => "111","parent_account_id" => 44],
    ["account_id" => 48,"name" => "RETENCION SUSSS","is_usable" => 1,"code" => "1.1.13.4","lft" => "112","rgt" => "113","parent_account_id" => 44],
    ["account_id" => 49,"name" => "ACTIVO NO CORRIENTE","is_usable" => 0,"code" => "1.2","lft" => "132","rgt" => "153","parent_account_id" => 1],
    ["account_id" => 50,"name" => "BIENES DE USO","is_usable" => 0,"code" => "1.2.1","lft" => "133","rgt" => "152","parent_account_id" => 49],
    ["account_id" => 51,"name" => "EQUIPOS DE INTERNET","is_usable" => 1,"code" => "1.2.1.1","lft" => "134","rgt" => "135","parent_account_id" => 50],
    ["account_id" => 52,"name" => "INMUEBLES","is_usable" => 1,"code" => "1.2.1.2","lft" => "136","rgt" => "137","parent_account_id" => 50],
    ["account_id" => 53,"name" => "AMORTIZACION ACUMULADA INMUEBLES","is_usable" => 1,"code" => "1.2.1.3","lft" => "138","rgt" => "139","parent_account_id" => 50],
    ["account_id" => 54,"name" => "RODADOS","is_usable" => 1,"code" => "1.2.1.4","lft" => "140","rgt" => "141","parent_account_id" => 50],
    ["account_id" => 55,"name" => "AMORTIZACION ACUMULADA RODADOS","is_usable" => 1,"code" => "1.2.1.5","lft" => "142","rgt" => "143","parent_account_id" => 50],
    ["account_id" => 56,"name" => "MUEBLES Y UTILES","is_usable" => 1,"code" => "1.2.1.6","lft" => "144","rgt" => "145","parent_account_id" => 50],
    ["account_id" => 57,"name" => "AMORTIZACION ACUMULADA MUEBLES Y UTILES","is_usable" => 1,"code" => "1.2.1.7","lft" => "146","rgt" => "147","parent_account_id" => 50],
    ["account_id" => 58,"name" => "EQUIPOS DE COMPUTACION","is_usable" => 1,"code" => "1.2.1.8","lft" => "148","rgt" => "149","parent_account_id" => 50],
    ["account_id" => 59,"name" => "INSTALACIONES","is_usable" => 1,"code" => "1.2.1.9","lft" => "150","rgt" => "151","parent_account_id" => 50],
    ["account_id" => 60,"name" => "PASIVO","is_usable" => 0,"code" => "2","lft" => "155","rgt" => "222","parent_account_id" => null],
    ["account_id" => 61,"name" => "PROVEEDORES","is_usable" => 0,"code" => "2.1","lft" => "156","rgt" => "189","parent_account_id" => 60],
    ["account_id" => 62,"name" => "SILICA NETWORKS","is_usable" => 1,"code" => "2.1.1","lft" => "157","rgt" => "158","parent_account_id" => 61],
    ["account_id" => 63,"name" => "TELEFONICA DE ARGENTINA","is_usable" => 1,"code" => "2.1.2","lft" => "159","rgt" => "160","parent_account_id" => 61],
    ["account_id" => 64,"name" => "INFOANDINA","is_usable" => 1,"code" => "2.1.3","lft" => "161","rgt" => "162","parent_account_id" => 61],
    ["account_id" => 65,"name" => "SOLUTION BOX","is_usable" => 1,"code" => "2.1.4","lft" => "163","rgt" => "164","parent_account_id" => 61],
    ["account_id" => 66,"name" => "LAUFQUEN","is_usable" => 1,"code" => "2.1.5","lft" => "165","rgt" => "166","parent_account_id" => 61],
    ["account_id" => 67,"name" => "MICROCOM","is_usable" => 1,"code" => "2.1.6","lft" => "167","rgt" => "168","parent_account_id" => 61],
    ["account_id" => 68,"name" => "ELECTRONICA MENDOZA","is_usable" => 1,"code" => "2.1.7","lft" => "169","rgt" => "170","parent_account_id" => 61],
    ["account_id" => 69,"name" => "FERGUSON JOSE CARLOS","is_usable" => 1,"code" => "2.1.8","lft" => "171","rgt" => "172","parent_account_id" => 61],
    ["account_id" => 70,"name" => "DOC A PAGAR","is_usable" => 1,"code" => "2.2","lft" => "190","rgt" => "191","parent_account_id" => 60],
    ["account_id" => 71,"name" => "ACREEDORES VARIOS","is_usable" => 1,"code" => "2.3","lft" => "192","rgt" => "193","parent_account_id" => 60],
    ["account_id" => 72,"name" => "ANTICIPOS DE CLIENTES","is_usable" => 1,"code" => "2.4","lft" => "194","rgt" => "195","parent_account_id" => 60],
    ["account_id" => 73,"name" => "IVA DEBITO FISCAL","is_usable" => 1,"code" => "2.5","lft" => "196","rgt" => "197","parent_account_id" => 60],
    ["account_id" => 74,"name" => "IVA A PAGAR","is_usable" => 1,"code" => "2.6","lft" => "198","rgt" => "199","parent_account_id" => 60],
    ["account_id" => 75,"name" => "SUSS A PAGAR","is_usable" => 1,"code" => "2.7","lft" => "200","rgt" => "201","parent_account_id" => 60],
    ["account_id" => 76,"name" => "IIBB A PAGAR","is_usable" => 1,"code" => "2.8","lft" => "202","rgt" => "203","parent_account_id" => 60],
    ["account_id" => 77,"name" => "HONORARIOS A PAGAR","is_usable" => 1,"code" => "2.9","lft" => "204","rgt" => "205","parent_account_id" => 60],
    ["account_id" => 78,"name" => "PATRIMONIO NETO","is_usable" => 0,"code" => "3","lft" => "223","rgt" => "232","parent_account_id" => null],
    ["account_id" => 79,"name" => "CAPITAL","is_usable" => 1,"code" => "3.1","lft" => "224","rgt" => "225","parent_account_id" => 78],
    ["account_id" => 80,"name" => "RESERVA LEGAL","is_usable" => 1,"code" => "3.2","lft" => "226","rgt" => "227","parent_account_id" => 78],
    ["account_id" => 81,"name" => "RESULTADO DEL EJERCICIO","is_usable" => 1,"code" => "3.3","lft" => "228","rgt" => "229","parent_account_id" => 78],
    ["account_id" => 82,"name" => "RESULTADOS NO ASIGNADOS","is_usable" => 1,"code" => "3.4","lft" => "230","rgt" => "231","parent_account_id" => 78],
    ["account_id" => 83,"name" => "INGRESOS","is_usable" => 0,"code" => "4","lft" => "233","rgt" => "250","parent_account_id" => null],
    ["account_id" => 84,"name" => "VENTAS","is_usable" => 0,"code" => "4.1","lft" => "234","rgt" => "243","parent_account_id" => 83],
    ["account_id" => 85,"name" => "ALQUILER MENSUAL EQUIPOS","is_usable" => 1,"code" => "4.1.1","lft" => "235","rgt" => "236","parent_account_id" => 84],
    ["account_id" => 86,"name" => "INSTALACION EQUIPOS","is_usable" => 1,"code" => "4.1.2","lft" => "237","rgt" => "238","parent_account_id" => 84],
    ["account_id" => 87,"name" => "PROVISION HADWARE","is_usable" => 1,"code" => "4.1.3","lft" => "239","rgt" => "240","parent_account_id" => 84],
    ["account_id" => 88,"name" => "PLAN DE PAGOS","is_usable" => 1,"code" => "4.1.4","lft" => "241","rgt" => "242","parent_account_id" => 84],
    ["account_id" => 89,"name" => "ALQUILERES GANADOS","is_usable" => 1,"code" => "4.2","lft" => "244","rgt" => "245","parent_account_id" => 83],
    ["account_id" => 90,"name" => "INTERESES GANADOS","is_usable" => 1,"code" => "4.3","lft" => "246","rgt" => "247","parent_account_id" => 83],
    ["account_id" => 91,"name" => "PRESTACION EXTERNA INTERNET","is_usable" => 1,"code" => "4.4","lft" => "248","rgt" => "249","parent_account_id" => 83],
    ["account_id" => 92,"name" => "EGRESOS","is_usable" => 0,"code" => "5","lft" => "251","rgt" => "454","parent_account_id" => null],
    ["account_id" => 93,"name" => "COSTO LABORAL","is_usable" => 0,"code" => "5.1","lft" => "252","rgt" => "269","parent_account_id" => 92],
    ["account_id" => 94,"name" => "SUELDOS Y JORNALES","is_usable" => 1,"code" => "5.1.1","lft" => "253","rgt" => "254","parent_account_id" => 93],
    ["account_id" => 95,"name" => "CONTRIBUCION OBRA SOCIAL","is_usable" => 1,"code" => "5.1.2","lft" => "255","rgt" => "256","parent_account_id" => 93],
    ["account_id" => 96,"name" => "CEC","is_usable" => 1,"code" => "5.1.3","lft" => "257","rgt" => "258","parent_account_id" => 93],
    ["account_id" => 97,"name" => "931","is_usable" => 1,"code" => "5.1.4","lft" => "259","rgt" => "260","parent_account_id" => 93],
    ["account_id" => 98,"name" => "FAECYS","is_usable" => 1,"code" => "5.1.5","lft" => "261","rgt" => "262","parent_account_id" => 93],
    ["account_id" => 99,"name" => "ALQUILERES","is_usable" => 0,"code" => "5.10.1","lft" => "329","rgt" => "350","parent_account_id" => 136],
    ["account_id" => 100,"name" => "OFICINA 108","is_usable" => 1,"code" => "5.10.1.1","lft" => "330","rgt" => "331","parent_account_id" => 99],
];

return $accounts;