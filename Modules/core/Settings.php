<?php

$form = new \App\Forms\Form('coreSettings');

$form->add(new \App\Forms\TextType('siteName',\App\Settings::i()->siteName,[],[],['min_length'=>3,'max_length'=>50]));

if($form->isSubmitted() AND $form->isValid()){
    $form->save();
}