<?php
/**
 * Ajustamos los elementos del dom para que se vean correctamente en el mailing.
 * Todos los estilos deben ser en linea.
 */
error_log(print_r(Yii::$app->view->params,1));
if(isset($notification)) {
    $this->params['notification'] = $notification;
} else {
    $notification = Yii::$app->view->params['notification'];
}

// check que no se rompa la app si el contenido de la notificacion esta vacio 
//(ya habiendo apretado el boton de actualizar)
if (!isset($notification->content)){
    $notification->content = "!!cuerpo de notificacion vacio!!";
    Yii::warning("Actualizar notificacion - cuerpo vacio");
}

$content = $notification->content;

$dom = new DOMDocument();
$dom->loadHTML($content);

//Necesitamos evaluar los <a> y cambiar el estilo
$links = $dom->getElementsByTagName('a');
foreach ($links as $link) {
    $style = $link->getAttribute('style');
    $style .= 'text-decoration: none; margin-top: 5px; margin-bottom: 5px; display: inline-block; font-weight: 700;';

    //cambiamos el estilo
    $link->removeAttribute('style');
    $link->setAttribute("style", $style);
}

//All headers
foreach(['h1','h2','h3','h4','h5','h6'] as $h){
    $headers = $dom->getElementsByTagName($h);
    foreach($headers as $header){
        $style = $header->getAttribute('style');
        $style .= 'margin: 10px; line-height: 1.5em;';

        //cambiamos el estilo
        $header->removeAttribute('style');
        $header->setAttribute("style", $style);
    }
}

//Estilos de ul:
$lists = $dom->getElementsByTagName('ul');
foreach($lists as $list){
    $style = $list->getAttribute('style');
    $style .= 'line-height: 1.5em;';
    
    //cambiamos el estilo
    $list->removeAttribute('style');
    $list->setAttribute("style", $style);
}

//Estilos de parrafos:
$ps = $dom->getElementsByTagName('p');
foreach($ps as $p){
    $style = $p->getAttribute('style');
    $style .= 'margin: 10px; line-height: 1.5em;';
    
    //cambiamos el estilo
    $p->removeAttribute('style');
    $p->setAttribute("style", $style);
}

$html = $dom->saveHTML();

echo $html;