<?php
//Desenvolvido por WHMCS.RED
//Versão 0.1
//Laravel DataBase
use WHMCS\Database\Capsule;
//Bloqueio de Acesso direto ao arquivo
if(!defined("WHMCS")){
	die("Acesso restrito!");
}
function invoice_red($vars){
	//Hash para captura do nome de e-mail definido abaixo
	$template_email = $vars['messagename'];
	//ID do e-mail
	$invoiceid = $vars['relid'];
	//montando array base
	$invoicered = array();
	//verifica o template de email se é os informados abaixo
	if($template_email=='Invoice Created' || $template_email=='Invoice Payment Confirmation'){
	    //Consulta o BD da invoice
	    foreach(Capsule::table('tblinvoices')->WHERE('id', $invoiceid)->get() as $bdinvoice){
	        $total_fatura = $bdinvoice->total;
    	}
    	//Verifica se o valor é negativo
    	if($total_fatura=='0.00'){
    	    //Bloqueia o envio do e-mail
    	    $invoicered['abortsend'] = true;
    	    //Deleta a fatura
    	    Capsule::table('tblinvoices')->WHERE('id', $invoiceid)->delete();
    	    //Cadastra o ActiveLog
    	    logActivity('[INVOICE RED] Fatura não criada por ser valor nulo - N°'.$id_invoice.'');
    	}
	    
	}
	return $invoicered;
}
//Adicionando Hook ao Pre Sender de Email
add_hook('EmailPreSend',1,'invoice_red');
//Tarefa Cron de limpeza
function cron_invoice_red($vars){
    //Cria o loop de verificação
    foreach(Capsule::table('tblinvoices')->WHERE('total', '0.00')->get() as $dadosinvoice){
        $id_invoice = $dadosinvoice->id;
        //Adicionado um LOG ao log Activity do WHMCS
        logActivity('[INVOICE RED - CRON] Fatura removida por ser valor nulo - N°'.$id_invoice.'');
        //Remove a fatura nula encontrada
        Capsule::table('tblinvoices')->WHERE('id', $id_invoice)->delete();
    }
}
//Adicionando ao CronJob
add_hook("DailyCronJob",1,"cron_invoice_red");
?>