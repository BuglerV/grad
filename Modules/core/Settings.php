<?php

$form = new \App\Forms\Form('coreSettings');

$form->addHeader('Основные');
$form->add(new \App\Forms\TextType('siteName',\App\Settings::i()->siteName,[],[],['min_length'=>3,'max_length'=>50]));
$form->add(new \App\Forms\CkeditorType('window_links',\App\Settings::i()->window_links));

$form->addHeader('Чат');
$form->add(new \App\Forms\NumberType('chat_period_minutes',\App\Settings::i()->chat_period_minutes));
$form->add(new \App\Forms\NumberType('chat_message_count',\App\Settings::i()->chat_message_count));
$form->add(new \App\Forms\NumberType('chat_interval',\App\Settings::i()->chat_interval));

$form->addHeader('Радио');
$form->add(new \App\Forms\TextType('djName',\App\Settings::i()->djName));
$form->add(new \App\Forms\CheckboxType('radio_enabled',\App\Settings::i()->radio_enabled));
$form->add(new \App\Forms\TextType('radio_source',\App\Settings::i()->radio_source));

$form->addHeader('Дополнительно');
$form->add(new \App\Forms\CheckboxType('i18n_debug',\App\Settings::i()->i18n_debug));

if($form->isSubmitted() AND $form->isValid()){
    $form->save();
}