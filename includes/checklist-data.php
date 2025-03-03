<?php
// Default categories and tasks for the checklist
global $ep_default_categories, $ep_default_tasks;

$ep_default_categories = array(
    'Algemeen',
    'Financieel',
    'Documenten'
);

$ep_default_tasks = array(
    'Algemeen' => array(
        array('name' => 'Koop vliegtickets', 'start' => '', 'end' => ''),
        array('name' => 'Leer basis van de taal', 'start' => '', 'end' => '')
    ),
    'Financieel' => array(
        array('name' => 'Open een bankrekening', 'start' => '', 'end' => ''),
        array('name' => 'Belastingen afhandelen', 'start' => '', 'end' => '')
    ),
    'Documenten' => array(
        array('name' => 'Vraag visum aan', 'start' => '', 'end' => ''),
        array('name' => 'Verzamel persoonlijke documenten', 'start' => '', 'end' => '')
    )
);
