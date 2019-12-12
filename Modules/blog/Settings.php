<?php

$form = new \App\Forms\Form('blogSettings');

$form->add(new \App\Forms\NumberType('blog_max_posts',\App\Settings::i()->blog_max_posts,[],[],['min'=>1,'max'=>100]));

if($form->isSubmitted() AND $form->isValid()){
    $form->save();
}