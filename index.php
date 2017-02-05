<?php
include 'template.php';

$template = new template();
$template->SetHtml('view.html');
$template->SetMaster('master.html');
$template->SetView('sidebar.html');
$template->SetValue(array());
$template->Output(true, template::HTML_TRUE, template::MASTER_TRUE);