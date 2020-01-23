<?php

$form = new \App\Forms\Form('coreSettings');

$form->add(new \App\Forms\TextType('siteName',\App\Settings::i()->siteName,[],[],['min_length'=>3,'max_length'=>50]));
$form->add(new \App\Forms\TextType('djName',\App\Settings::i()->djName));

$form->add(new \App\Forms\CkeditorType('window_links',\App\Settings::i()->window_links));

$form->add(new \App\Forms\CheckboxType('radio_enabled',\App\Settings::i()->radio_enabled));
$form->add(new \App\Forms\CheckboxType('i18n_debug',\App\Settings::i()->i18n_debug));

if($form->isSubmitted() AND $form->isValid()){
    $form->save();
}