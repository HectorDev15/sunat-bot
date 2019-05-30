# Sunat BOT
[![Build Status](https://travis-ci.org/giansalex/sunat-bot.svg?branch=master)](https://travis-ci.org/giansalex/sunat-bot)  
Conexion a sunat utilizando la clave SOL.

### Caracteristicas
- Lista Comprobantes electrónicos emitidos por clave SOL.
- Busqueda de comprobante electrónico emitido desde SEE.
- Lista de recibo por honorarios emitidos.
- Descarga de XML emitidos por SEE-SOL, SEE-Sistema del Contribuyente

### Instalar

```bash
composer require giansalex/sunat-bot
```

### Ejemplo

```php
<?php

use Sunat\Bot\Bot;
use Sunat\Bot\Menu;
use Sunat\Bot\Model\ClaveSol;

$user = new ClaveSol();
$user->ruc = '';
$user->user = '';
$user->password = '';

$bot = new Bot($user);

// Descargar factura emitida por sistema del contribuyente

$bot->login();
$bot->navigate([Menu::CONSULTA_SEE_FE]);
$xml = $bot->getSeeXml($user->ruc, 'F001', '1');

file_put_contents('factura.xml', $xml);

```
